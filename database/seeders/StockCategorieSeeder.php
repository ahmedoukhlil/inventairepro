<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockCategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'libelle'      => 'Fournitures de bureau',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériel électrique',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériel hôtelier',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériel de sonorisation et audiovisuel',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériels de jardinage',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériels et produits de ménage',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériel de plomberie',
                'observations' => null,
            ],
            [
                'libelle'      => 'Matériel informatique',
                'observations' => null,
            ],
        ];

        $inserted = 0;
        $skipped  = 0;

        foreach ($categories as $categorie) {
            $exists = DB::table('stock_categories')
                ->where('libelle', $categorie['libelle'])
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà existante : {$categorie['libelle']}");
                $skipped++;
                continue;
            }

            DB::table('stock_categories')->insert([
                'libelle'      => $categorie['libelle'],
                'observations' => $categorie['observations'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $this->command->info("  [OK]   Insérée : {$categorie['libelle']}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} insérée(s), {$skipped} ignorée(s).");
    }
}
