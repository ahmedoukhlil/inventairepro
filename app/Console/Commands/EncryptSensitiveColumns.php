<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptSensitiveColumns extends Command
{
    protected $signature = 'security:encrypt-columns {--dry-run : Afficher sans modifier}';

    protected $description = 'Chiffre les colonnes sensibles (commentaire, observation, observations) déjà en clair';

    private array $targets = [
        [
            'table'  => 'inventaire_scans',
            'column' => 'commentaire',
            'pk'     => 'id',
        ],
        [
            'table'  => 'inventaires',
            'column' => 'observation',
            'pk'     => 'id',
        ],
        [
            'table'  => 'stock_sorties',
            'column' => 'observations',
            'pk'     => 'id',
        ],
    ];

    public function handle(): int
    {
        foreach ($this->targets as $target) {
            $this->processTable($target['table'], $target['column'], $target['pk']);
        }

        return self::SUCCESS;
    }

    private function processTable(string $table, string $column, string $pk): void
    {
        // Convert empty strings to NULL first (encrypted cast fails on empty string)
        DB::table($table)->where($column, '')->update([$column => null]);

        $rows = DB::table($table)
            ->whereNotNull($column)
            ->get([$pk, $column]);

        $count = 0;

        foreach ($rows as $row) {
            $value = $row->{$column};

            // Si la valeur commence par 'eyJ' (base64 JSON), elle est déjà chiffrée par Laravel
            if (str_starts_with((string) $value, 'eyJ')) {
                continue;
            }

            $count++;

            if ($this->option('dry-run')) {
                $this->line("  [DRY-RUN] {$table}.{$column} id={$row->{$pk}} — en clair détecté");
            } else {
                DB::table($table)
                    ->where($pk, $row->{$pk})
                    ->update([$column => Crypt::encryptString($value)]);
            }
        }

        $action = $this->option('dry-run') ? 'détectés' : 'chiffrés';
        $this->info("{$table}.{$column} : {$count} enregistrement(s) {$action}.");
    }
}
