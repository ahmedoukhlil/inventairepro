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

        // [libelle_en_base => [stock_initial, stock_actuel]]
        $stocks = [
            // Matériel hôtelier
            'THE ACHORA'                                => [12, 7],
            'THE 20/20'                                 => [2,  1],
            'THE ASMA'                                  => [1,  1],
            'Sucre tropicana'                           => [30, 0],
            'SUCRE TROPICANNA'                          => [3,  3],
            'GOLD PETIT'                                => [3,  3],
            'GOLD GRAND'                                => [0,  0],
            'THE AZWAD'                                 => [154,139],
            'THE MALIKA'                                => [143,137],
            'CARTONS DE GABELETS À CAFÉ'                => [5000,4750],
            'CARTONS DE GOBELETS CAFÉ petit'            => [5000,5000],
            'MACHINE À CAFÉ DULCE GUSTO'                => [1,  1],
            'SAC SUCRE'                                 => [8,  6],

            // Matériel électrique
            'Remote control barrière'                   => [10, 6],
            'INGLEEC E14 40W'                           => [100,96],
            'LED G45'                                   => [30, 0],
            'Nettoyage mossant'                         => [24, 10],
            'RADIATEUR'                                 => [2,  0],
            'CASQUE TRADUCTION'                         => [2,  2],
            'CASQUE UTILISER SIMPLE'                    => [50, 0],
            'MEGA STAR'                                 => [4,  4],
            'GRAND JACK PETIT JACK'                     => [4,  4],
            'PETIT JACK'                                => [5,  5],
            'CABLE XLR RS'                              => [5,  5],
            'récepteur'                                 => [100,0],
            'CONTACT EMPOLE'                            => [10, 9],
            'BOITE ARCHIVE'                             => [29, 27],
            'contact américain'                         => [39, 12],
            'SAC POUBELLE 240L'                         => [15, 11],
            'GOULET'                                    => [5,  5],
            'VERRE AMPOLE E27'                          => [5,  5],
            'ROLE DE GNE'                               => [1,  1],
            'POLET 4516'                                => [1,  1],
            'AMPOLE 18W'                                => [6,  6],
            'AMPOLE 35W'                                => [200,200],
            'AMPOLE RECLET 220'                         => [16, 16],
            'CONTACTEUR 25 POUR UP'                     => [1,  1],
            'DOMINOH'                                   => [8,  8],
            'AMPOLE 10W'                                => [1,  1],
            'AMPOLE 4W'                                 => [27, 1],
            'AMPOLE E12 35W'                            => [12, 12],
            'AMPOLE 50W ANJELEC'                        => [6,  6],
            'POLET 36'                                  => [1,  1],
            'AMPOLE SOLEF'                              => [61, 61],
            'AMPOLE FONTEN'                             => [7,  7],
            'TRANFOL'                                   => [16, 16],
            'PRIS ITRANCH'                              => [6,  6],
            'PROJECTEUR 200W'                           => [6,  4],
            'ATACH'                                     => [12, 12],
            'CHEVIE'                                    => [200,200],
            'AMPOLE SPOTE 12W'                          => [7,  7],
            'D OUI'                                     => [2,  2],
            'CABLE 4*4'                                 => [2,  2],
            'BATTRIE AA'                                => [43, 12],
            'BATTRIE AAA'                               => [338,203],
            'BATTRIE 9V'                                => [35, 5],
            'Ampole led 480'                            => [27, 27],
            'Ampole 8W'                                 => [9,  9],
            'Ampole 9W'                                 => [75, 75],
            '117A COLEUR'                               => [5,  5],
            '217 A'                                     => [12, 12],
            'Adaptateur'                                => [4,  2],

            // Matériel informatique
            'TYPE C P10 HOTV 11N1'                      => [2,  2],
            'Mac Co'                                    => [1,  1],
            'type c to Mac'                             => [1,  1],
            'Clavier avec souris sans fille'            => [4,  3],
            'ordinateur portable acer'                  => [3,  7],
            'CANON COLEUR G 3020'                       => [2,  2],
            'CARTOUCHE 1106A'                           => [12, 12],
            'Crouche CE 2'                              => [27, 25],
            'Crouche CE 278A'                           => [1,  0],
            'HP 17A'                                    => [1,  0],
            'TK 1110'                                   => [13, 7],
            'TK 4105'                                   => [13, 13],
            'TK 5230C'                                  => [11, 11],
            'TK 5230M'                                  => [11, 11],
            'TK 5230K'                                  => [23, 22],
            'TK 1150'                                   => [16, 2],
            'TK160'                                     => [16, 16],
            'TK 6305Y'                                  => [1,  1],

            // Matériel de sonorisation et audiovisuel
            'RADIATEUR TRADUCTION'                      => [2,  2],
            'TP LINK'                                   => [1,  1],
            'PATCH RJ 45 100M'                          => [5,  5],
            'N0BWI'                                     => [1,  1],
            'SPOTLIGHT PRÉSENTATION'                    => [1,  1],
            'RAP 72 PRO WIFI ACCESS POINT'              => [1,  1],
            'HDMI 2.0 CABLE OPTICAL 80M'                => [4,  1],
            'HDMI 10M'                                  => [4,  3],
            'HDMI CABLE'                                => [3,  3],
            'RJ 45 CABLE 15M'                           => [5,  5],
            'RJ 45 CABLE 3M'                            => [5,  0],
            'HDMI EXTENDER'                             => [1,  1],
            'CASQUE SIMPLE'                             => [138,87],
            'CASQUE VIP'                                => [8,  8],
            'CASQUE VVIP'                               => [8,  8],

            // Matériels et produits de ménage
            'sacs poubelle 240L'                        => [40, 26],
            'Papier fresh'                              => [153,8],
            'vif liquide ménage'                        => [28, 8],
            'Ajax poudre'                               => [232,219],
            'Balais africains'                          => [45, 45],
            'Balais pour ménage'                        => [47, 45],
            'Désinfectant'                              => [156,147],
            'Désodorisant WC'                           => [84, 67],
            'Insecticide baygon'                        => [270,222],
            'Lessive'                                   => [371,348],
            'Original cotail'                           => [262,238],
            'Original coton mouchoir'                   => [101,0],
            'Papiers hygiéniques'                       => [105,0],
            'Piles + brosse'                            => [41, 41],
            'Savon en morceaux'                         => [527,41],
            'Savon en poudre omo'                       => [320,285],
            'Savon liquide lave vitres'                 => [114,0],
            'Savon liquide multi-usage'                 => [156,147],
            'Serpillère'                                => [14, 11],
            'Brouette'                                  => [11, 0],
            'Ciseaux de jar'                            => [15, 10],

            // Matériels de jardinage
            'Corbeille de poubelle 240L'                => [2,  2],
            'Engrais'                                   => [0,  0],
            'Torches'                                   => [5,  5],
            'Gant nettoyage'                            => [23, 20],
            'Gant pour engrais'                         => [5,  5],
            'Piquet'                                    => [3,  3],
            'Piquets'                                   => [15, 10],
            'Raccord d\'arrosage'                       => [31, 27],
            'Râteaux'                                   => [25, 25],
            'Poubelle 20L'                              => [55, 45],
            'Sécateur de jardin'                        => [16, 12],
            'Balais extérieur'                          => [9,  9],
            'Gant pour jardinage'                       => [5,  3],
            'Sécateur de jardin 240L'                   => [16, 10],

            // Fournitures de bureau
            'Agrafeuse'                                 => [8,  4],
            'Block notes'                               => [310,283],
            'Boite d\'archive'                          => [49, 6],
            'Classeur chrono'                           => [24, 4],
            'Classeur de registre de courrier'          => [21, 0],
            'Scotch'                                    => [13, 8],
            'Enveloppe A4'                              => [8,  6],
            'Enveloppe Rectangulaire'                   => [0,  0],
            'Marqueurs'                                 => [108,105],
            'Sachet clips 25mm'                         => [70, 59],
            'Sachet clips 50 MM'                        => [59, 59],
            'Papier de tableau'                         => [6,  6],
            'Papier note repositionnables (PF) Petit'   => [33, 33],
            'Papier note repositionnables (PF) Grand'   => [36, 0],
            'Paquettes de stylo'                        => [1110,1049],
            'Ramettes de papier'                        => [0,  0],
            'Rallonge 5M'                               => [33, 34],
            'Rames de papier'                           => [33, 0],
            'Surligneur'                                => [0,  0],
        ];

        $dateInit = '2026-04-09';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as $libelle => [$stockInitial, $stockActuel]) {

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

            $this->command->info("  [OK]   {$produit->libelle} → initial={$stockInitial}, actuel={$stockActuel}");
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
