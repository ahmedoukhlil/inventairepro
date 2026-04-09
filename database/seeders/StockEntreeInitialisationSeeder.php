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

        $mags = DB::table('stock_magasins')->pluck('id', 'magasin');
        $m217     = $mags['M217']            ?? null;
        $m231     = $mags['M231']            ?? null;
        $mExt     = $mags['M EXT']           ?? null;

        if (!$m217 || !$m231 || !$mExt) {
            $this->command->error('Magasins introuvables. Lancez d\'abord StockMagasinSeeder.');
            return;
        }

        // [libelle, stock_initial, stock_actuel, magasin_id]
        $stocks = [
            ['THE ACHORA',                          12,   7,    $m217],
            ['THE 20/20',                           12,   7,    $m217],
            ['remote controle barriere',            10,   6,    $m217],
            ['TYPE C TO HDTV 11IN 1',               2,    2,    $m231],
            ['type c to HDTV 8-1',                  2,    2,    $m231],
            ['INGELEC E14 40W',                     100,  96,   $m217],
            ['THE ASMA',                            4,    0,    $m217],
            ['Nettoyage mossant',                   24,   19,   $m217],
            ['LED G45',                             30,   0,    $m217],
            ['sucre tropicana',                     2,    1,    $m217],
            ['CHEMISE',                             43,   0,    $m231],
            ['RADIATEUR TRADUCTION',                2,    2,    $m217],
            ['CASQUE TRADUCTION',                   3,    3,    $m217],
            ['CASQUE UTILISER SIMPLE',              50,   0,    $m217],
            ['NOVA STAR',                           1,    1,    $m217],
            ['RADIATEUR',                           4,    4,    $m217],
            ['GRAND JACK PETIT JACK',               4,    4,    $m217],
            ['PETIT JACK XLR',                      8,    7,    $m217],
            ['CABLE XLR RSA',                       5,    5,    $m217],
            ['recepteur',                           100,  0,    $m217],
            ['scotch american',                     10,   6,    $m217],
            ['CONTACT EMPOLE',                      10,   9,    $m217],
            ['BOITE ARCHIVE',                       29,   27,   $m231],
            ['micro conférance',                    19,   11,   $m217],
            ['ampole 12w',                          12,   0,    $m217],
            ['SAC POUBELLE 240L',                   15,   11,   $m217],
            ['GOULET',                              5,    5,    $mExt],
            ['CADRE AMPOLE E27',                    27,   27,   $m217],
            ['ROLE DE GNE',                         1,    1,    $m217],
            ['POLET 4516',                          6,    6,    $m217],
            ['AMPOLE 18W',                          16,   16,   $m217],
            ['AMPOLE 35W',                          200,  200,  $m217],
            ['AMPOLE RECLET 220',                   16,   16,   $m217],
            ['RELE TERMIQUE',                       1,    1,    $m217],
            ['CONTACTEUR 25 POUR UP',               1,    1,    $m217],
            ['DOMINOH',                             8,    8,    $m217],
            ['AMPOLE 10W',                          1,    1,    $m217],
            ['AMPOLE 4W',                           27,   1,    $m217],
            ['AMPOLE E12 35 W',                     12,   12,   $m217],
            ['AMPOLE 50W ANJELEC',                  6,    6,    $m217],
            ['POLET 16',                            4,    4,    $m217],
            ['AMPOLE SOLEF',                        61,   61,   $m217],
            ['AMPOLE FONTEN',                       7,    7,    $m217],
            ['TRANFOU',                             16,   16,   $m217],
            ['PRIS ITRANCH',                        6,    6,    $m217],
            ['PROJECTEUR 200 W',                    6,    4,    $m217],
            ['ATACH',                               12,   12,   $m217],
            ['CHEVIE',                              200,  200,  $m217],
            ['AMPOLE SPOTE 12W',                    7,    7,    $m217],
            ['D OUI',                               35,   27,   $m217],
            ['CABLE 4*4',                           2,    2,    $m217],
            ['Clavier avec souris sans fille',      4,    3,    $m231],
            ['ordinateur portable accer',           3,    2,    $m231],
            ['sacs poubelle 240L',                  40,   37,   $m217],
            ['BATTRIE AA',                          43,   12,   $m217],
            ['BATTRIE AAA',                         338,  203,  $m217],
            ['BATTRIE 9V',                          35,   8,    $m217],
            ['TP LINK',                             1,    1,    $m231],
            ['PATCH RJ 45 100M',                    5,    5,    $m231],
            ['NORWI',                               1,    1,    $m231],
            ['SPOTLIGHT PRESANTATION',              1,    1,    $m231],
            ['RAP 72 PRO WIFI ACCESS POINT',        3,    3,    $m231],
            ['HDMI 2.0 CABLE OPTICAL 80M',          3,    3,    $m231],
            ['HDMI 10M',                            4,    0,    $m231],
            ['HDMI CABLE 20M',                      7,    4,    $m231],
            ['RJ 45 CABLE 5M',                      4,    1,    $m231],
            ['RJ 45 CABLE 15M',                     5,    0,    $m231],
            ['HDMI EXTENDER',                       8,    8,    $m231],
            ['CASQUE SIMPLE',                       138,  87,   $m217],
            ['CASQUE VIP',                          28,   28,   $m217],
            ['CASQUE VVIP',                         8,    8,    $m217],
            ['polele',                              26,   26,   $m217],
            ['Ampole led 480',                      27,   27,   $m217],
            ['Ampole 8W',                           9,    8,    $m217],
            ['Ampole 35w',                          75,   75,   $m217],
            ['Coffere mod',                         6,    6,    $m217],
            ['Ampole 9W',                           79,   47,   $m217],
            ['SUCRE TROPICANNA',                    3,    3,    $m217],
            ['CAFE GOLD PETIT',                     7,    1,    $m217],
            ['CAFE GOLD GRAND',                     0,    0,    $m217],
            ['THE AZWAD',                           154,  139,  $m217],
            ['THE MALIKA',                          143,  137,  $m217],
            ['PAQUETS CAPSUL ESSPRESO',             36,   7,    $m217],
            ['CARTONS DE GABELETS A CAFE',          5000, 4750, $m217],
            ['CARTONS DE GOBELETS CAFE petit',      5000, 5000, $m217],
            ['MACHINE CAFEE DULCE GUSTO',           2,    2,    $m217],
            ['SAC SUCRE',                           8,    6,    $m217],
            ['Carton d\'eau',                       203,  150,  $m217],
            ['Ampole 40W',                          45,   27,   $m217],
            ['117A COLEUR',                         5,    5,    $m231],
            ['217 A',                               12,   11,   $m231],
            ['5230 COLEUR',                         12,   12,   $m231],
            ['CANON COLEUR G 3020',                 2,    2,    $m231],
            ['CARTOUCHE 1106A',                     37,   34,   $m231],
            ['Crtouche CE 278A',                    27,   25,   $m231],
            ['G3020 510',                           3,    3,    $m231],
            ['HP 17A',                              3,    3,    $m231],
            ['TK 1110',                             7,    7,    $m231],
            ['TK 4105',                             13,   13,   $m231],
            ['TK 5230C',                            11,   11,   $m231],
            ['TK 5230K',                            11,   11,   $m231],
            ['TK 5230M',                            13,   13,   $m231],
            ['TK1150',                              23,   22,   $m231],
            ['TK160',                               2,    2,    $m231],
            ['TK5230Y',                             16,   16,   $m231],
            ['Papier fresh',                        153,  141,  $m217],
            ['vif liquide menage',                  28,   8,    $m217],
            ['Ajacks poudre',                       232,  219,  $m217],
            ['Balais africain',                     11,   10,   $m217],
            ['Balais pour ménage',                  47,   45,   $m217],
            ['Désinfectant',                        156,  147,  $m217],
            ['Désodirisans',                        6,    0,    $m217],
            ['Désodorisant WC',                     84,   67,   $m217],
            ['Insecticide baygon',                  270,  222,  $m217],
            ['Javel',                               371,  348,  $m217],
            ['Original cotaill',                    262,  238,  $m217],
            ['Papier mouchoir',                     375,  329,  $m217],
            ['Papiers hygiéniques',                 107,  100,  $m217],
            ['Pèles + brosses',                     41,   41,   $m217],
            ['Savon en morceaux',                   527,  516,  $m217],
            ['Savon en poudre omo',                 320,  285,  $m217],
            ['savon liquide lave vitres',           90,   81,   $m217],
            ['Savon liquide multi-usage',           156,  156,  $m217],
            ['Serpillère',                          14,   11,   $m217],
            ['Binette',                             11,   8,    $mExt],
            ['Brouette',                            2,    2,    $mExt],
            ['Ciseaux de jardin',                   9,    2,    $mExt],
            ['Corbeille de boubelle 240 L',         0,    0,    $mExt],
            ['Corbeille de poubelle 20L',           5,    5,    $mExt],
            ['Engrais',                             0,    0,    $mExt],
            ['Fourches',                            3,    3,    $mExt],
            ['Gant nettoyage',                      23,   20,   $mExt],
            ['Gant pour engrais',                   5,    3,    $mExt],
            ['Hache',                               20,   16,   $mExt],
            ['Piquets',                             11,   10,   $mExt],
            ['Raccord d\'arrosage',                 31,   27,   $mExt],
            ['Râteaux',                             25,   22,   $mExt],
            ['Sacs poubelle 20L',                   30,   29,   $m217],
            ['Sécateur de jardin',                  16,   12,   $mExt],
            ['Balais exterieur',                    55,   49,   $mExt],
            ['Adaptateur',                          13,   11,   $m231],
            ['Agrafeuse',                           8,    4,    $m231],
            ['Block notes',                         310,  283,  $m231],
            ['Boite d\'archive',                    66,   65,   $m231],
            ['Classeur chrono',                     24,   4,    $m231],
            ['Classeur de registre de courier',     21,   21,   $m231],
            ['Correcteur',                          29,   26,   $m231],
            ['Enveloppe A4',                        8,    6,    $m231],
            ['Enveloppe Rectangulaire',             0,    0,    $m231],
            ['Marqueurs',                           108,  105,  $m231],
            ['Papier clips 25mm',                   70,   69,   $m231],
            ['Papier clips 50 MM',                  60,   59,   $m231],
            ['Papier de tableau',                   5,    5,    $m231],
            ['Papier note repositionnables (PF) Petit', 36, 31, $m231],
            ['Papier note repositionnables grand',  59,   56,   $m231],
            ['Paquettes de stylo',                  1110, 1049, $m231],
            ['Rallonge 20 M',                       0,    0,    $m231],
            ['Rallonge 5 M',                        0,    0,    $m231],
            ['Rames de papier',                     33,   3,    $m231],
            ['Staple Agrafeuse',                    16,   14,   $m231],
            ['Surligneur',                          7,    3,    $m231],
        ];

        $dateInit = '2026-04-09';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as [$libelle, $stockInitial, $stockActuel, $magasinId]) {

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

            $this->command->info("  [OK]   {$produit->libelle} → init={$stockInitial}, actuel={$stockActuel}");
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
