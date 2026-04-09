<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockMagasinSeeder extends Seeder
{
    public function run(): void
    {
        $magasins = [
            ['magasin' => 'Magasin Central', 'localisation' => 'CICMOD'],
            ['magasin' => 'M217',            'localisation' => 'CICMOD'],
            ['magasin' => 'M231',            'localisation' => 'CICMOD'],
            ['magasin' => 'M EXT',           'localisation' => 'CICMOD'],
        ];

        $inserted = 0;
        $skipped  = 0;

        foreach ($magasins as $magasin) {
            $exists = DB::table('stock_magasins')
                ->where('magasin', $magasin['magasin'])
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà existant : {$magasin['magasin']}");
                $skipped++;
                continue;
            }

            DB::table('stock_magasins')->insert([
                'magasin'      => $magasin['magasin'],
                'localisation' => $magasin['localisation'],
                'observations' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $this->command->info("  [OK]   {$magasin['magasin']} — {$magasin['localisation']}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} inséré(s), {$skipped} ignoré(s).");
    }
}
