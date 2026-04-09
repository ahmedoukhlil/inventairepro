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

        // [libelle, stock_initial, stock_actuel]
        $stocks = [
            ['THE ACHORA',                          12,   7],
            ['THE 20/20',                           12,   7],
            ['remote controle barriere',            10,   6],
            ['TYPE C TO HDTV 11IN 1',               2,    2],
            ['type c to HDTV 8-1',                  2,    2],
            ['INGELEC E14 40W',                     100,  96],
            ['THE ASMA',                            4,    0],
            ['Nettoyage mossant',                   24,   19],
            ['LED G45',                             30,   0],
            ['sucre tropicana',                     2,    1],
            ['CHEMISE',                             43,   0],
            ['RADIATEUR TRADUCTION',                2,    2],
            ['CASQUE TRADUCTION',                   3,    3],
            ['CASQUE UTILISER SIMPLE',              50,   0],
            ['NOVA STAR',                           1,    1],
            ['RADIATEUR',                           4,    4],
            ['GRAND JACK PETIT JACK',               4,    4],
            ['PETIT JACK XLR',                      8,    7],
            ['CABLE XLR RSA',                       5,    5],
            ['recepteur',                           100,  0],
            ['scotch american',                     10,   6],
            ['CONTACT EMPOLE',                      10,   9],
            ['BOITE ARCHIVE',                       29,   27],
            ['micro conférance',                    19,   11],
            ['ampole 12w',                          12,   0],
            ['SAC POUBELLE 240L',                   15,   11],
            ['GOULET',                              5,    5],
            ['CADRE AMPOLE E27',                    27,   27],
            ['ROLE DE GNE',                         1,    1],
            ['POLET 4516',                          6,    6],
            ['AMPOLE 18W',                          16,   16],
            ['AMPOLE 35W',                          200,  200],
            ['AMPOLE RECLET 220',                   16,   16],
            ['RELE TERMIQUE',                       1,    1],
            ['CONTACTEUR 25 POUR UP',               1,    1],
            ['DOMINOH',                             8,    8],
            ['AMPOLE 10W',                          1,    1],
            ['AMPOLE 4W',                           27,   1],
            ['AMPOLE E12 35 W',                     12,   12],
            ['AMPOLE 50W ANJELEC',                  6,    6],
            ['POLET 16',                            4,    4],
            ['AMPOLE SOLEF',                        61,   61],
            ['AMPOLE FONTEN',                       7,    7],
            ['TRANFOU',                             16,   16],
            ['PRIS ITRANCH',                        6,    6],
            ['PROJECTEUR 200 W',                    6,    4],
            ['ATACH',                               12,   12],
            ['CHEVIE',                              200,  200],
            ['AMPOLE SPOTE 12W',                    7,    7],
            ['D OUI',                               35,   27],
            ['CABLE 4*4',                           2,    2],
            ['Clavier avec souris sans fille',      4,    3],
            ['ordinateur portable accer',           3,    2],
            ['sacs poubelle 240L',                  40,   37],
            ['BATTRIE AA',                          43,   12],
            ['BATTRIE AAA',                         338,  203],
            ['BATTRIE 9V',                          35,   8],
            ['TP LINK',                             1,    1],
            ['PATCH RJ 45 100M',                    5,    5],
            ['NORWI',                               1,    1],
            ['SPOTLIGHT PRESANTATION',              1,    1],
            ['RAP 72 PRO WIFI ACCESS POINT',        3,    3],
            ['HDMI 2.0 CABLE OPTICAL 80M',          3,    3],
            ['HDMI 10M',                            4,    0],
            ['HDMI CABLE 20M',                      7,    4],
            ['RJ 45 CABLE 5M',                      4,    1],
            ['RJ 45 CABLE 15M',                     5,    0],
            ['HDMI EXTENDER',                       8,    8],
            ['CASQUE SIMPLE',                       138,  87],
            ['CASQUE VIP',                          28,   28],
            ['CASQUE VVIP',                         8,    8],
            ['polele',                              26,   26],
            ['Ampole led 480',                      27,   27],
            ['Ampole 8W',                           9,    8],
            ['Ampole 35w',                          75,   75],
            ['Coffere mod',                         6,    6],
            ['Ampole 9W',                           79,   47],
            ['SUCRE TROPICANNA',                    3,    3],
            ['CAFE GOLD PETIT',                     7,    1],
            ['CAFE GOLD GRAND',                     0,    0],
            ['THE AZWAD',                           154,  139],
            ['THE MALIKA',                          143,  137],
            ['PAQUETS CAPSUL ESSPRESO',             36,   7],
            ['CARTONS DE GABELETS A CAFE',          5000, 4750],
            ['CARTONS DE GOBELETS CAFE petit',      5000, 5000],
            ['MACHINE CAFEE DULCE GUSTO',           2,    2],
            ['SAC SUCRE',                           8,    6],
            ['Carton d\'eau',                       203,  150],
            ['Ampole 40W',                          45,   27],
            ['117A COLEUR',                         5,    5],
            ['217 A',                               12,   11],
            ['5230 COLEUR',                         12,   12],
            ['CANON COLEUR G 3020',                 2,    2],
            ['CARTOUCHE 1106A',                     37,   34],
            ['Crtouche CE 278A',                    27,   25],
            ['G3020 510',                           3,    3],
            ['HP 17A',                              3,    3],
            ['TK 1110',                             7,    7],
            ['TK 4105',                             13,   13],
            ['TK 5230C',                            11,   11],
            ['TK 5230K',                            11,   11],
            ['TK 5230M',                            13,   13],
            ['TK1150',                              23,   22],
            ['TK160',                               2,    2],
            ['TK5230Y',                             16,   16],
            ['Papier fresh',                        153,  141],
            ['vif liquide menage',                  28,   8],
            ['Ajacks poudre',                       232,  219],
            ['Balais africain',                     11,   10],
            ['Balais pour ménage',                  47,   45],
            ['Désinfectant',                        156,  147],
            ['Désodirisans',                        6,    0],
            ['Désodorisant WC',                     84,   67],
            ['Insecticide baygon',                  270,  222],
            ['Javel',                               371,  348],
            ['Original cotaill',                    262,  238],
            ['Papier mouchoir',                     375,  329],
            ['Papiers hygiéniques',                 107,  100],
            ['Pèles + brosses',                     41,   41],
            ['Savon en morceaux',                   527,  516],
            ['Savon en poudre omo',                 320,  285],
            ['savon liquide lave vitres',           90,   81],
            ['Savon liquide multi-usage',           156,  156],
            ['Serpillère',                          14,   11],
            ['Binette',                             11,   8],
            ['Brouette',                            2,    2],
            ['Ciseaux de jardin',                   9,    2],
            ['Corbeille de boubelle 240 L',         0,    0],
            ['Corbeille de poubelle 20L',           5,    5],
            ['Engrais',                             0,    0],
            ['Fourches',                            3,    3],
            ['Gant nettoyage',                      23,   20],
            ['Gant pour engrais',                   5,    3],
            ['Hache',                               20,   16],
            ['Piquets',                             11,   10],
            ['Raccord d\'arrosage',                 31,   27],
            ['Râteaux',                             25,   22],
            ['Sacs poubelle 20L',                   30,   29],
            ['Sécateur de jardin',                  16,   12],
            ['Balais exterieur',                    55,   49],
            ['Adaptateur',                          13,   11],
            ['Agrafeuse',                           8,    4],
            ['Block notes',                         310,  283],
            ['Boite d\'archive',                    66,   65],
            ['Classeur chrono',                     24,   4],
            ['Classeur de registre de courier',     21,   21],
            ['Correcteur',                          29,   26],
            ['Enveloppe A4',                        8,    6],
            ['Enveloppe Rectangulaire',             0,    0],
            ['Marqueurs',                           108,  105],
            ['Papier clips 25mm',                   70,   69],
            ['Papier clips 50 MM',                  60,   59],
            ['Papier de tableau',                   5,    5],
            ['Papier note repositionnables (PF) Petit', 36, 31],
            ['Papier note repositionnables grand',  59,   56],
            ['Paquettes de stylo',                  1110, 1049],
            ['Rallonge 20 M',                       0,    0],
            ['Rallonge 5 M',                        0,    0],
            ['Rames de papier',                     33,   3],
            ['Staple Agrafeuse',                    16,   14],
            ['Surligneur',                          7,    3],
        ];

        $dateInit = '2026-04-09';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as [$libelle, $stockInitial, $stockActuel]) {

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

            // Mettre à jour stock_initial et stock_actuel
            DB::table('stock_produits')->where('id', $produit->id)->update([
                'stock_initial' => $stockInitial,
                'stock_actuel'  => $stockActuel,
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
