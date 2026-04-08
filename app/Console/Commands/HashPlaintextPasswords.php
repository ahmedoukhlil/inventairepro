<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class HashPlaintextPasswords extends Command
{
    protected $signature = 'security:hash-passwords {--dry-run : Afficher sans modifier}';

    protected $description = 'Hasher les mots de passe en clair restants dans la base de données';

    public function handle(): int
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Les mots de passe bcrypt commencent par $2y$
            if (!str_starts_with((string) $user->mdp, '$2y$')) {
                if ($this->option('dry-run')) {
                    $this->line("  [DRY-RUN] Utilisateur \"{$user->users}\" (id={$user->idUser}) — mot de passe en clair détecté");
                } else {
                    $user->mdp = Hash::make($user->mdp);
                    $user->save();
                    $this->line("  ✓ Utilisateur \"{$user->users}\" (id={$user->idUser}) — mot de passe hashé");
                }
                $count++;
            }
        }

        if ($count === 0) {
            $this->info('Aucun mot de passe en clair trouvé. Tous les mots de passe sont déjà hashés.');
        } else {
            $action = $this->option('dry-run') ? 'détectés' : 'hashés';
            $this->info("{$count} mot(s) de passe {$action}.");
        }

        return self::SUCCESS;
    }
}
