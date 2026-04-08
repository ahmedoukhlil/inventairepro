<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

/**
 * Trait Auditable
 *
 * Enregistre automatiquement les événements created / updated / deleted
 * dans la table audit_logs pour tout modèle Eloquent qui l'utilise.
 *
 * Usage : ajouter `use Auditable;` dans le modèle.
 *
 * Pour exclure des colonnes, surcharger la méthode dans le modèle :
 *   protected function auditExcludeFields(): array { return ['mdp', 'remember_token']; }
 */
trait Auditable
{
    /**
     * Retourne les colonnes à ne jamais enregistrer dans l'audit.
     * Les modèles peuvent surcharger cette méthode (pas de conflit de propriété).
     */
    protected function auditExcludeFields(): array
    {
        return [];
    }

    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->recordAudit('created', [], $model->getAuditableAttributes()));
        static::updated(fn ($model) => $model->recordAudit('updated', $model->getAuditOldValues(), $model->getAuditNewValues()));
        static::deleted(fn ($model) => $model->recordAudit('deleted', $model->getAuditableAttributes(), []));
    }

    private function recordAudit(string $event, array $oldValues, array $newValues): void
    {
        $user = auth()->user();

        AuditLog::create([
            'event'          => $event,
            'auditable_type' => static::class,
            'auditable_id'   => $this->getKey(),
            'user_id'        => $user?->idUser ?? $user?->id,
            'user_name'      => $user?->users ?? $user?->name,
            'ip_address'     => request()->ip(),
            'old_values'     => $oldValues ?: null,
            'new_values'     => $newValues ?: null,
        ]);
    }

    private function getAuditableAttributes(): array
    {
        return collect($this->getAttributes())
            ->except($this->getAuditExcluded())
            ->toArray();
    }

    private function getAuditOldValues(): array
    {
        return collect($this->getOriginal())
            ->only(array_keys($this->getDirty()))
            ->except($this->getAuditExcluded())
            ->toArray();
    }

    private function getAuditNewValues(): array
    {
        return collect($this->getDirty())
            ->except($this->getAuditExcluded())
            ->toArray();
    }

    private function getAuditExcluded(): array
    {
        return array_merge(
            ['created_at', 'updated_at'],
            $this->auditExcludeFields(),
            property_exists($this, 'hidden') ? $this->hidden : []
        );
    }
}
