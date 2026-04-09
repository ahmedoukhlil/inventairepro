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

        // Résolution des magasins par nom
        $magasins = DB::table('stock_magasins')->pluck('id', 'magasin');

        $mCentral = $magasins['Magasin central'] ?? null;
        $m217     = $magasins['M217']            ?? null;
        $m231     = $magasins['M231']            ?? null;
        $mExt     = $magasins['M EXT']           ?? null;

        if (!$mCentral || !$m217 || !$m231 || !$mExt) {
            $this->command->error('Magasins introuvables. Lancez d\'abord StockMagasinSeeder.');
            return;
        }

        // [libelle => [stock_initial, stock_actuel, magasin_id]]
        $stocks = [
            // Matériel hôtelier
            'THE ACHORA'                                => [12,   7,    $m217],
            'THE 20/20'                                 => [2,    0,    $m217],
            'Remote control barrière'                   => [10,   6,    $m217],
            'TYPE C P10 HOTV 11N1'                      => [2,    1,    $m231],
            'type c to Mac'                             => [1,    1,    $m231],
            'INGLEEC E14 40W'                           => [100,  96,   $m217],
            'THE ASMA'                                  => [1,    1,    $m217],
            'Nettoyage mossant'                         => [24,   10,   $m217],
            'LED G45'                                   => [30,   0,    $m217],
            'Sucre tropicana'                           => [30,   0,    $m217],
            'Scotch'                                    => [43,   1,    $mCentral],
            'RADIATEUR TRADUCTION'                      => [2,    2,    $m217],
            'CASQUE TRADUCTION'                         => [2,    2,    $m217],
            'CASQUE UTILISER SIMPLE'                    => [50,   0,    $m217],
            'MEGA STAR'                                 => [4,    4,    $m217],
            'RADIATEUR'                                 => [2,    0,    $m217],
            'GRAND JACK PETIT JACK'                     => [4,    4,    $m217],
            'PETIT JACK'                                => [8,    5,    $m217],
            'CABLE XLR RS'                              => [5,    5,    $m217],
            'récepteur'                                 => [100,  0,    $m217],
            'CONTACT EMPOLE'                            => [10,   9,    $m217],
            'BOITE ARCHIVE'                             => [29,   27,   $m231],
            'contact américain'                         => [19,   10,   $m217],
            'SAC POUBELLE 240L'                         => [15,   11,   $m217],
            'GOULET'                                    => [5,    5,    $mExt],
            'VERRE AMPOLE E27'                          => [27,   27,   $m217],
            'ROLE DE GNE'                               => [1,    1,    $m217],
            'POLET 4516'                                => [1,    1,    $m217],
            'AMPOLE 18W'                                => [6,    6,    $m217],
            'AMPOLE 35W'                                => [200,  200,  $m217],
            'AMPOLE RECLET 220'                         => [16,   16,   $m217],
            'CONTACTEUR 25 POUR UP'                     => [1,    1,    $m217],
            'DOMINOH'                                   => [8,    8,    $m217],
            'AMPOLE 10W'                                => [1,    1,    $m217],
            'AMPOLE 4W'                                 => [27,   1,    $m217],
            'AMPOLE E12 35W'                            => [12,   12,   $m217],
            'AMPOLE 50W ANJELEC'                        => [6,    6,    $m217],
            'POLET 36'                                  => [1,    1,    $m217],
            'AMPOLE SOLEF'                              => [61,   61,   $m217],
            'AMPOLE FONTEN'                             => [7,    7,    $m217],
            'TRANFOL'                                   => [16,   7,    $m217],
            'PRIS ITRANCH'                              => [6,    6,    $m217],
            'PROJECTEUR 200W'                           => [6,    4,    $m217],
            'ATACH'                                     => [12,   12,   $m217],
            'CHEVIE'                                    => [200,  200,  $m217],
            'AMPOLE SPOTE 12W'                          => [7,    7,    $m217],
            'CABLE 4*4'                                 => [2,    2,    $m217],
            'Clavier avec souris sans fille'            => [4,    3,    $m231],
            'ordinateur portable acer'                  => [3,    3,    $m231],
            'sacs poubelle 240L'                        => [40,   26,   $m217],
            'BATTRIE AA'                                => [43,   12,   $m217],
            'BATTRIE AAA'                               => [338,  203,  $m217],
            'BATTRIE 9V'                                => [35,   35,   $m231],
            'TP LINK'                                   => [1,    1,    $m231],
            'PATCH RJ 45 100M'                          => [5,    5,    $m231],
            'RAP 72 PRO WIFI ACCESS POINT'              => [1,    1,    $m231],
            'SPOTLIGHT PRÉSENTATION'                    => [1,    1,    $m231],
            'N0BWI'                                     => [1,    1,    $m231],
            'HDMI 2.0 CABLE OPTICAL 80M'                => [4,    0,    $m231],
            'HDMI 10M'                                  => [4,    0,    $m231],
            'RJ 45 CABLE 15M'                           => [5,    0,    $m231],
            'HDMI EXTENDER'                             => [1,    0,    $m231],
            'CASQUE SIMPLE'                             => [138,  87,   $m217],
            'CASQUE VIP'                                => [8,    8,    $m217],
            'CASQUE VVIP'                               => [8,    8,    $m217],
            'Ampole led 480'                            => [26,   26,   $m217],
            'Ampole 8W'                                 => [9,    8,    $m217],
            'Ampole 9W'                                 => [75,   75,   $m217],
            'SUCRE TROPICANNA'                          => [3,    3,    $m217],
            'GOLD PETIT'                                => [3,    3,    $m217],
            'GOLD GRAND'                                => [0,    0,    $m217],
            'THE AZWAD'                                 => [154,  139,  $m217],
            'THE MALIKA'                                => [143,  137,  $m217],
            'Lessive'                                   => [371,  348,  $m217],
            'CARTONS DE GABELETS À CAFÉ'                => [5000, 4750, $m217],
            'CARTONS DE GOBELETS CAFÉ petit'            => [5000, 5000, $m217],
            'MACHINE À CAFÉ DULCE GUSTO'                => [1,    1,    $m217],
            'SAC SUCRE'                                 => [8,    6,    $m217],
            '117A COLEUR'                               => [5,    5,    $m231],
            '217 A'                                     => [12,   12,   $m231],
            'CANON COLEUR G 3020'                       => [2,    2,    $m231],
            'CARTOUCHE 1106A'                           => [12,   12,   $m231],
            'Crouche CE 278A'                           => [27,   25,   $m231],
            'Crouche CE 2'                              => [1,    0,    $m231],
            'HP 17A'                                    => [1,    0,    $m231],
            'TK 1110'                                   => [13,   7,    $m231],
            'TK 4105'                                   => [13,   13,   $m231],
            'TK 5230C'                                  => [11,   11,   $m231],
            'TK 5230M'                                  => [13,   13,   $m231],
            'TK 5230K'                                  => [23,   22,   $m231],
            'TK 1150'                                   => [16,   2,    $m231],
            'TK160'                                     => [16,   16,   $m231],
            'TK 6305Y'                                  => [1,    1,    $m231],
            'Papier fresh'                              => [153,  141,  $m217],
            'vif liquide ménage'                        => [28,   8,    $m217],
            'Ajax poudre'                               => [232,  219,  $m217],
            'Balais africains'                          => [45,   45,   $m217],
            'Balais pour ménage'                        => [47,   45,   $m217],
            'Désinfectant'                              => [156,  147,  $m217],
            'Désodorisant WC'                           => [84,   67,   $m217],
            'Insecticide baygon'                        => [270,  222,  $m217],
            'Original cotail'                           => [262,  238,  $m217],
            'Original coton mouchoir'                   => [101,  0,    $m217],
            'Papiers hygiéniques'                       => [105,  0,    $m217],
            'Piles + brosse'                            => [41,   41,   $m217],
            'Savon en morceaux'                         => [527,  41,   $m217],
            'Savon en poudre omo'                       => [320,  285,  $m217],
            'Savon liquide lave vitres'                 => [156,  147,  $m217],
            'Savon liquide multi-usage'                 => [156,  147,  $m217],
            'Serpillère'                                => [14,   11,   $m217],
            'Brouette'                                  => [11,   0,    $mExt],
            'Ciseaux de jar'                            => [9,    2,    $mExt],
            'Corbeille de poubelle 240L'                => [2,    2,    $mExt],
            'Engrais'                                   => [0,    0,    $mExt],
            'Torches'                                   => [0,    0,    $mExt],
            'Gant nettoyage'                            => [23,   20,   $mExt],
            'Gant pour engrais'                         => [5,    5,    $mExt],
            'Piquet'                                    => [3,    3,    $mExt],
            'Piquets'                                   => [15,   10,   $mExt],
            'Raccord d\'arrosage'                       => [31,   27,   $mExt],
            'Râteaux'                                   => [25,   25,   $mExt],
            'Poubelle 20L'                              => [30,   25,   $mExt],
            'Sécateur de jardin'                        => [16,   12,   $mExt],
            'Balais extérieur'                          => [9,    9,    $mExt],
            'Gant pour jardinage'                       => [5,    3,    $mExt],
            'Sécateur de jardin 240L'                   => [16,   10,   $mExt],
            'Adaptateur'                                => [4,    2,    $m217],
            'Agrafeuse'                                 => [8,    4,    $m231],
            'Block notes'                               => [310,  283,  $m231],
            'Boite d\'archive'                          => [49,   6,    $m231],
            'Classeur chrono'                           => [24,   4,    $m231],
            'Classeur de registre de courrier'          => [21,   0,    $m231],
            'Enveloppe A4'                              => [8,    6,    $m231],
            'Enveloppe Rectangulaire'                   => [0,    0,    $m231],
            'Marqueurs'                                 => [108,  105,  $m231],
            'Sachet clips 25mm'                         => [70,   59,   $m231],
            'Sachet clips 50 MM'                        => [59,   59,   $m231],
            'Papier de tableau'                         => [6,    5,    $m231],
            'Papier note repositionnables (PF) Petit'   => [33,   33,   $m231],
            'Papier note repositionnables (PF) Grand'   => [36,   56,   $m231],
            'Paquettes de stylo'                        => [1110, 1049, $m231],
            'Ramettes de papier'                        => [0,    0,    $m231],
            'Rallonge 5M'                               => [33,   3,    $m231],
            'Rames de papier'                           => [33,   0,    $m231],
            'Surligneur'                                => [0,    0,    $m231],
        ];

        $dateInit = '2026-04-09';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as $libelle => [$stockInitial, $stockActuel, $magasinId]) {

            $produit = DB::table('stock_produits')->where('libelle', $libelle)->first();

            if (!$produit) {
                $notFound[] = $libelle;
                continue;
            }

            // Vérifier si déjà initialisé
            $exists = DB::table('stock_entrees')
                ->where('produit_id', $produit->id)
                ->where('reference_commande', 'INIT-2026')
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà initialisé : {$produit->libelle}");
                $skipped++;
                continue;
            }

            // Mettre à jour stock_initial, stock_actuel et magasin_id
            DB::table('stock_produits')->where('id', $produit->id)->update([
                'stock_initial' => $stockInitial,
                'stock_actuel'  => $stockActuel,
                'magasin_id'    => $magasinId,
                'updated_at'    => now(),
            ]);

            // Créer une entrée uniquement si stock_actuel > 0
            if ($stockActuel > 0) {
                DB::table('stock_entrees')->insert([
                    'date_entree'        => $dateInit,
                    'reference_commande' => 'INIT-2026',
                    'produit_id'         => $produit->id,
                    'fournisseur_id'     => null,
                    'quantite'           => $stockActuel,
                    'observations'       => 'Stock initial - initialisation système',
                    'created_by'         => $userId,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            $this->command->info("  [OK]   {$produit->libelle} → init={$stockInitial}, actuel={$stockActuel}, magasin={$magasinId}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} initialisé(s), {$skipped} ignoré(s).");

        if (!empty($notFound)) {
            $this->command->newLine();
            $this->command->warn('Produits non trouvés (' . count($notFound) . ') :');
            foreach ($notFound as $l) {
                $this->command->warn("  - {$l}");
            }
        }
    }
}
