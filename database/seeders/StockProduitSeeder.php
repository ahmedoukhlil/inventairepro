<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockProduitSeeder extends Seeder
{
    public function run(): void
    {
        // Résolution des catégories par libellé exact en base
        $cats = DB::table('stock_categories')->pluck('id', 'libelle');

        $c_hotel  = $cats['Matériel hôtelier']                      ?? null;
        $c_elec   = $cats['Matériel électrique']                     ?? null;
        $c_info   = $cats['Matériel informatique']                   ?? null;
        $c_sono   = $cats['Matériel de sonorisation et audiovisuel'] ?? null;
        $c_bureau = $cats['Fournitures de bureau']                   ?? null;
        $c_menage = $cats['Matériels et produits de ménage']         ?? null;
        $c_jardin = $cats['Matériels de jardinage']                  ?? null;

        // Résolution des magasins par nom exact en base
        $mags = DB::table('stock_magasins')->pluck('id', 'magasin');

        $mCentral = $mags['Magasin central'] ?? null;
        $m217     = $mags['M217']            ?? null;
        $m231     = $mags['M231']            ?? null;
        $mExt     = $mags['M EXT']           ?? null;

        if (!$m217 || !$m231 || !$mExt) {
            $this->command->error('Magasins introuvables. Lancez d\'abord StockMagasinSeeder.');
            return;
        }

        // [libelle, categorie_id, magasin_id, seuil_alerte]
        $produits = [
            // Matériel hôtelier
            ['THE ACHORA',                              $c_hotel,  $m217, 2],
            ['THE 20/20',                               $c_hotel,  $m217, 2],
            ['THE ASMA',                                $c_hotel,  $m217, 1],
            ['sucre tropicana',                         $c_hotel,  $m217, 5],
            ['SUCRE TROPICANNA',                        $c_hotel,  $m217, 2],
            ['CAFE GOLD PETIT',                         $c_hotel,  $m217, 2],
            ['CAFE GOLD GRAND',                         $c_hotel,  $m217, 2],
            ['THE AZWAD',                               $c_hotel,  $m217, 20],
            ['THE MALIKA',                              $c_hotel,  $m217, 20],
            ['PAQUETS CAPSUL ESSPRESO',                 $c_hotel,  $m217, 50],
            ['CARTONS DE GABELETS A CAFE',              $c_hotel,  $m217, 1000],
            ['CARTONS DE GOBELETS CAFE petit',          $c_hotel,  $m217, 500],
            ['MACHINE CAFEE DULCE GUSTO',               $c_hotel,  $m217, 1],
            ['SAC SUCRE',                               $c_hotel,  $m217, 2],
            ['Carton d\'eau',                           $c_hotel,  $m217, 5],

            // Matériel électrique
            ['remote controle barriere',                $c_elec,   $m217, 2],
            ['INGELEC E14 40W',                         $c_elec,   $m217, 10],
            ['LED G45',                                 $c_elec,   $m217, 5],
            ['CONTACT EMPOLE',                          $c_elec,   $m217, 5],
            ['ampole 12w',                              $c_elec,   $m217, 5],
            ['GOULET',                                  $c_elec,   $mExt, 5],
            ['CADRE AMPOLE E27',                        $c_elec,   $m217, 5],
            ['ROLE DE GNE',                             $c_elec,   $m217, 2],
            ['POLET 4516',                              $c_elec,   $m217, 2],
            ['AMPOLE 18W',                              $c_elec,   $m217, 10],
            ['AMPOLE 35W',                              $c_elec,   $m217, 20],
            ['AMPOLE RECLET 220',                       $c_elec,   $m217, 5],
            ['RELE TERMIQUE',                           $c_elec,   $m217, 2],
            ['CONTACTEUR 25 POUR UP',                   $c_elec,   $m217, 2],
            ['DOMINOH',                                 $c_elec,   $m217, 5],
            ['AMPOLE 10W',                              $c_elec,   $m217, 5],
            ['AMPOLE 4W',                               $c_elec,   $m217, 5],
            ['AMPOLE E12 35 W',                         $c_elec,   $m217, 5],
            ['AMPOLE 50W ANJELEC',                      $c_elec,   $m217, 5],
            ['POLET 16',                                $c_elec,   $m217, 2],
            ['AMPOLE SOLEF',                            $c_elec,   $m217, 10],
            ['AMPOLE FONTEN',                           $c_elec,   $m217, 5],
            ['TRANFOU',                                 $c_elec,   $m217, 5],
            ['PRIS ITRANCH',                            $c_elec,   $m217, 5],
            ['PROJECTEUR 200 W',                        $c_elec,   $m217, 2],
            ['ATACH',                                   $c_elec,   $m217, 20],
            ['CHEVIE',                                  $c_elec,   $m217, 50],
            ['AMPOLE SPOTE 12W',                        $c_elec,   $m217, 5],
            ['D OUI',                                   $c_elec,   $m217, 2],
            ['CABLE 4*4',                               $c_elec,   $m217, 2],
            ['BATTRIE AA',                              $c_elec,   $m217, 10],
            ['BATTRIE AAA',                             $c_elec,   $m217, 50],
            ['BATTRIE 9V',                              $c_elec,   $m217, 5],
            ['polele',                                  $c_elec,   $m217, 5],
            ['Ampole led 480',                          $c_elec,   $m217, 5],
            ['Ampole 8W',                               $c_elec,   $m217, 5],
            ['Ampole 35w',                              $c_elec,   $m217, 10],
            ['Coffere mod',                             $c_elec,   $m217, 1],
            ['Ampole 9W',                               $c_elec,   $m217, 5],
            ['Ampole 40W',                              $c_elec,   $m217, 5],

            // Matériel informatique
            ['TYPE C TO HDTV 11IN 1',                   $c_info,   $m231, 1],
            ['type c to HDTV 8-1',                      $c_info,   $m231, 1],
            ['Clavier avec souris sans fille',          $c_info,   $m231, 2],
            ['ordinateur portable accer',               $c_info,   $m231, 1],
            ['117A COLEUR',                             $c_info,   $m231, 2],
            ['217 A',                                   $c_info,   $m231, 2],
            ['5230 COLEUR',                             $c_info,   $m231, 2],
            ['CANON COLEUR G 3020',                     $c_info,   $m231, 1],
            ['CARTOUCHE 1106A',                         $c_info,   $m231, 3],
            ['Crtouche CE 278A',                        $c_info,   $m231, 5],
            ['G3020 510',                               $c_info,   $m231, 2],
            ['HP 17A',                                  $c_info,   $m231, 2],
            ['TK 1110',                                 $c_info,   $m231, 2],
            ['TK 4105',                                 $c_info,   $m231, 2],
            ['TK 5230C',                                $c_info,   $m231, 2],
            ['TK 5230K',                                $c_info,   $m231, 2],
            ['TK 5230M',                                $c_info,   $m231, 2],
            ['TK1150',                                  $c_info,   $m231, 2],
            ['TK160',                                   $c_info,   $m231, 2],
            ['TK5230Y',                                 $c_info,   $m231, 2],

            // Matériel de sonorisation et audiovisuel
            ['RADIATEUR TRADUCTION',                    $c_sono,   $m217, 1],
            ['CASQUE TRADUCTION',                       $c_sono,   $m217, 5],
            ['CASQUE UTILISER SIMPLE',                  $c_sono,   $m217, 5],
            ['NOVA STAR',                               $c_sono,   $m217, 1],
            ['RADIATEUR',                               $c_sono,   $m217, 1],
            ['GRAND JACK PETIT JACK',                   $c_sono,   $m217, 2],
            ['PETIT JACK XLR',                          $c_sono,   $m217, 5],
            ['CABLE XLR RSA',                           $c_sono,   $m217, 5],
            ['recepteur',                               $c_sono,   $m217, 5],
            ['scotch american',                         $c_sono,   $m217, 2],
            ['micro conférance',                        $c_sono,   $m217, 2],
            ['TP LINK',                                 $c_sono,   $m231, 1],
            ['PATCH RJ 45 100M',                        $c_sono,   $m231, 2],
            ['NORWI',                                   $c_sono,   $m231, 1],
            ['SPOTLIGHT PRESANTATION',                  $c_sono,   $m231, 1],
            ['RAP 72 PRO WIFI ACCESS POINT',            $c_sono,   $m231, 1],
            ['HDMI 2.0 CABLE OPTICAL 80M',              $c_sono,   $m231, 1],
            ['HDMI 10M',                                $c_sono,   $m231, 2],
            ['HDMI CABLE 20M',                          $c_sono,   $m231, 1],
            ['RJ 45 CABLE 5M',                          $c_sono,   $m231, 2],
            ['RJ 45 CABLE 15M',                         $c_sono,   $m231, 2],
            ['HDMI EXTENDER',                           $c_sono,   $m231, 1],
            ['CASQUE SIMPLE',                           $c_sono,   $m217, 20],
            ['CASQUE VIP',                              $c_sono,   $m217, 5],
            ['CASQUE VVIP',                             $c_sono,   $m217, 5],

            // Matériels et produits de ménage
            ['Nettoyage mossant',                       $c_menage, $m217, 5],
            ['SAC POUBELLE 240L',                       $c_menage, $m217, 5],
            ['sacs poubelle 240L',                      $c_menage, $m217, 10],
            ['Papier fresh',                            $c_menage, $m217, 20],
            ['vif liquide menage',                      $c_menage, $m217, 5],
            ['Ajacks poudre',                           $c_menage, $m217, 20],
            ['Balais africain',                         $c_menage, $m217, 10],
            ['Balais pour ménage',                      $c_menage, $m217, 10],
            ['Désinfectant',                            $c_menage, $m217, 10],
            ['Désodirisans',                            $c_menage, $m217, 5],
            ['Désodorisant WC',                         $c_menage, $m217, 10],
            ['Insecticide baygon',                      $c_menage, $m217, 24],
            ['Javel',                                   $c_menage, $m217, 5],
            ['Original cotaill',                        $c_menage, $m217, 30],
            ['Papier mouchoir',                         $c_menage, $m217, 20],
            ['Papiers hygiéniques',                     $c_menage, $m217, 20],
            ['Pèles + brosses',                         $c_menage, $m217, 10],
            ['Savon en morceaux',                       $c_menage, $m217, 50],
            ['Savon en poudre omo',                     $c_menage, $m217, 100],
            ['savon liquide lave vitres',               $c_menage, $m217, 10],
            ['Savon liquide multi-usage',               $c_menage, $m217, 10],
            ['Serpillère',                              $c_menage, $m217, 5],

            // Matériels de jardinage
            ['Binette',                                 $c_jardin, $mExt, 2],
            ['Brouette',                                $c_jardin, $mExt, 1],
            ['Ciseaux de jardin',                       $c_jardin, $mExt, 3],
            ['Corbeille de boubelle 240 L',             $c_jardin, $mExt, 2],
            ['Corbeille de poubelle 20L',               $c_jardin, $mExt, 2],
            ['Engrais',                                 $c_jardin, $mExt, 2],
            ['Fourches',                                $c_jardin, $mExt, 2],
            ['Gant nettoyage',                          $c_jardin, $mExt, 5],
            ['Gant pour engrais',                       $c_jardin, $mExt, 2],
            ['Hache',                                   $c_jardin, $mExt, 1],
            ['Piquets',                                 $c_jardin, $mExt, 5],
            ['Raccord d\'arrosage',                     $c_jardin, $mExt, 5],
            ['Râteaux',                                 $c_jardin, $mExt, 3],
            ['Sacs poubelle 20L',                       $c_jardin, $m217, 5],
            ['Sécateur de jardin',                      $c_jardin, $mExt, 3],
            ['Balais exterieur',                        $c_jardin, $mExt, 3],

            // Fournitures de bureau
            ['CHEMISE',                                 $c_bureau, $m231, 5],
            ['BOITE ARCHIVE',                           $c_bureau, $m231, 10],
            ['Adaptateur',                              $c_bureau, $m231, 2],
            ['Agrafeuse',                               $c_bureau, $m231, 2],
            ['Block notes',                             $c_bureau, $m231, 30],
            ['Boite d\'archive',                        $c_bureau, $m231, 10],
            ['Classeur chrono',                         $c_bureau, $m231, 5],
            ['Classeur de registre de courier',         $c_bureau, $m231, 5],
            ['Correcteur',                              $c_bureau, $m231, 5],
            ['Enveloppe A4',                            $c_bureau, $m231, 10],
            ['Enveloppe Rectangulaire',                 $c_bureau, $m231, 5],
            ['Marqueurs',                               $c_bureau, $m231, 10],
            ['Papier clips 25mm',                       $c_bureau, $m231, 10],
            ['Papier clips 50 MM',                      $c_bureau, $m231, 10],
            ['Papier de tableau',                       $c_bureau, $m231, 5],
            ['Papier note repositionnables (PF) Petit', $c_bureau, $m231, 10],
            ['Papier note repositionnables grand',      $c_bureau, $m231, 10],
            ['Paquettes de stylo',                      $c_bureau, $m231, 100],
            ['Rallonge 20 M',                           $c_bureau, $m231, 2],
            ['Rallonge 5 M',                            $c_bureau, $m231, 2],
            ['Rames de papier',                         $c_bureau, $m231, 50],
            ['Staple Agrafeuse',                        $c_bureau, $m231, 5],
            ['Surligneur',                              $c_bureau, $m231, 5],
        ];

        $inserted = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($produits as [$libelle, $categorieId, $magasinId, $seuilAlerte]) {
            if (is_null($categorieId)) {
                $this->command->warn("  [ERR]  Catégorie introuvable pour : {$libelle}");
                $errors++;
                continue;
            }

            $exists = DB::table('stock_produits')
                ->where('libelle', $libelle)
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà existant : {$libelle}");
                $skipped++;
                continue;
            }

            DB::table('stock_produits')->insert([
                'libelle'       => $libelle,
                'categorie_id'  => $categorieId,
                'magasin_id'    => $magasinId,
                'stock_initial' => 0,
                'stock_actuel'  => 0,
                'seuil_alerte'  => $seuilAlerte,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $this->command->info("  [OK]   {$libelle}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} inséré(s), {$skipped} ignoré(s), {$errors} erreur(s).");
    }
}
