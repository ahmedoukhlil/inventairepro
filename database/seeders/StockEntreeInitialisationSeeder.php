<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockEntreeInitialisationSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->value('idUser');
        if (!$userId) {
            $this->command->error('Aucun utilisateur trouvé.');
            return;
        }

        // Libellés exacts tels qu'ils sont en base => stock actuel
        $stocks = [
            'THE ACHORA'                            => 7,
            'THE 20/20'                             => 1,
            'THE ASMA'                              => 1,
            'Sucre tropicana'                       => 1,
            'SUCRE TROPICANNA'                      => 3,
            'GOLD PETIT'                            => 79,
            'THE AZWAD'                             => 154,
            'THE MALIKA'                            => 143,
            'CARTONS DE GABELETS À CAFÉ'            => 4750,
            'SAC SUCRE'                             => 6,
            'Remote control barrière'               => 6,
            'INGLEEC E14 40W'                       => 96,
            'GRAND JACK PETIT JACK'                 => 4,
            'PETIT JACK'                            => 5,
            'CABLE XLR RS'                          => 5,
            'CONTACT EMPOLE'                        => 9,
            'BOITE ARCHIVE'                         => 27,
            'SAC POUBELLE 240L'                     => 11,
            'GOULET'                                => 5,
            'AMPOLE 18W'                            => 6,
            'AMPOLE 35W'                            => 200,
            'CONTACTEUR 25 POUR UP'                 => 1,
            'DOMINOH'                               => 8,
            'AMPOLE 10W'                            => 1,
            'AMPOLE 4W'                             => 27,
            'AMPOLE E12 35W'                        => 12,
            'AMPOLE 50W ANJELEC'                    => 6,
            'AMPOLE SOLEF'                          => 61,
            'AMPOLE FONTEN'                         => 7,
            'TRANFOL'                               => 16,
            'PRIS ITRANCH'                          => 6,
            'PROJECTEUR 200W'                       => 3,
            'ATACH'                                 => 12,
            'CHEVIE'                                => 200,
            'AMPOLE SPOTE 12W'                      => 7,
            'CABLE 4*4'                             => 27,
            'BATTRIE AA'                            => 43,
            'BATTRIE AAA'                           => 338,
            'BATTRIE 9V'                            => 3,
            'Ampole led 480'                        => 27,
            'Ampole 8W'                             => 9,
            'Ampole 9W'                             => 75,
            '117A COLEUR'                           => 5,
            'Adaptateur'                            => 4,
            'TYPE C P10 HOTV 11N1'                  => 2,
            'Clavier avec souris sans fille'        => 3,
            'ordinateur portable acer'              => 7,
            'CANON COLEUR G 3020'                   => 2,
            'CARTOUCHE 1106A'                       => 12,
            'Crouche CE 278A'                       => 25,
            'TK 1110'                               => 5,
            'TK 4105'                               => 13,
            'TK 5230C'                              => 13,
            'TK 5230M'                              => 11,
            'TK 5230K'                              => 11,
            'TK160'                                 => 16,
            'RADIATEUR TRADUCTION'                  => 0,
            'TP LINK'                               => 1,
            'PATCH RJ 45 100M'                      => 5,
            'N0BWI'                                 => 1,
            'SPOTLIGHT PRÉSENTATION'                => 1,
            'RAP 72 PRO WIFI ACCESS POINT'          => 1,
            'HDMI 2.0 CABLE OPTICAL 80M'            => 1,
            'HDMI 10M'                              => 4,
            'HDMI CABLE'                            => 1,
            'RJ 45 CABLE 15M'                       => 5,
            'HDMI EXTENDER'                         => 1,
            'CASQUE SIMPLE'                         => 138,
            'CASQUE VIP'                            => 8,
            'sacs poubelle 240L'                    => 40,
            'Papier fresh'                          => 8,
            'vif liquide ménage'                    => 28,
            'Ajax poudre'                           => 232,
            'Balais africains'                      => 45,
            'Balais pour ménage'                    => 47,
            'Désinfectant'                          => 156,
            'Désodorisant WC'                       => 84,
            'Insecticide baygon'                    => 270,
            'Original cotail'                       => 262,
            'Original coton mouchoir'               => 101,
            'Piles + brosse'                        => 41,
            'Savon en morceaux'                     => 527,
            'Savon en poudre omo'                   => 320,
            'Savon liquide lave vitres'             => 114,
            'Savon liquide multi-usage'             => 156,
            'Serpillère'                            => 14,
            'Corbeille de poubelle 240L'            => 2,
            'Gant nettoyage'                        => 23,
            'Gant pour engrais'                     => 5,
            'Piquets'                               => 5,
            'Raccord d\'arrosage'                   => 31,
            'Râteaux'                               => 25,
            'Sécateur de jardin'                    => 16,
            'Balais extérieur'                      => 9,
            'Agrafeuse'                             => 8,
            'Block notes'                           => 310,
            'Boite d\'archive'                      => 49,
            'Classeur chrono'                       => 24,
            'Classeur de registre de courrier'      => 21,
            'Enveloppe A4'                          => 8,
            'Marqueurs'                             => 108,
            'Sachet clips 25mm'                     => 70,
            'Sachet clips 50 MM'                    => 59,
            'Papier de tableau'                     => 6,
            'Papier note repositionnables (PF) Petit' => 33,
            'Paquettes de stylo'                    => 1049,
            'Rallonge 5M'                           => 0,
            'Rames de papier'                       => 0,
            'Surligneur'                            => 0,
        ];

        $dateInit = '2026-01-01';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as $libelle => $quantite) {
            if ($quantite < 0) {
                $skipped++;
                continue;
            }

            $produit = DB::table('stock_produits')->where('libelle', $libelle)->first();

            if (!$produit) {
                $notFound[] = $libelle;
                continue;
            }

            // Vérifier si déjà initialisé
            $exists = DB::table('stock_entrees')
                ->where('produit_id', $produit->id)
                ->where('observations', 'Stock initial - initialisation système')
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà initialisé : {$produit->libelle}");
                $skipped++;
                continue;
            }

            // Mettre à jour le stock_actuel du produit (stock_initial reste à 0)
            DB::table('stock_produits')->where('id', $produit->id)->update([
                'stock_actuel' => $quantite,
                'updated_at'   => now(),
            ]);

            // Créer une entrée uniquement si quantité > 0
            if ($quantite > 0) {
                DB::table('stock_entrees')->insert([
                    'date_entree'        => $dateInit,
                    'reference_commande' => 'INIT-2026',
                    'produit_id'         => $produit->id,
                    'fournisseur_id'     => null,
                    'quantite'           => $quantite,
                    'observations'       => 'Stock initial - initialisation système',
                    'created_by'         => $userId,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            $this->command->info("  [OK]   {$produit->libelle} → {$quantite}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} initialisé(s), {$skipped} ignoré(s) (stock = 0 ou déjà fait).");

        if (!empty($notFound)) {
            $this->command->newLine();
            $this->command->warn('Produits non trouvés en base (' . count($notFound) . ') :');
            foreach ($notFound as $l) {
                $this->command->warn("  - {$l}");
            }
        }
    }
}
