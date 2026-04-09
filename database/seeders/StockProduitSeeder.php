<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockProduitSeeder extends Seeder
{
    public function run(): void
    {
        // Résolution des catégories par libellé
        $cats = DB::table('stock_categories')->pluck('id', 'libelle');

        $c_hotel   = $cats['Matériel hôtelier']                       ?? null;
        $c_elec    = $cats['Matériel électrique']                      ?? null;
        $c_info    = $cats['Matériel informatique']                    ?? null;
        $c_sono    = $cats['Matériel de sonorisation et audiovisuel']  ?? null;
        $c_bureau  = $cats['Fournitures de bureau']                    ?? null;
        $c_menage  = $cats['Matériels et produits de ménage']          ?? null;
        $c_jardin  = $cats['Matériels de jardinage']                   ?? null;
        $c_plomb   = $cats['Matériel de plomberie']                    ?? null;

        // Récupérer le premier magasin disponible comme défaut
        $magasinDefault = DB::table('stock_magasins')->value('id');

        if (!$magasinDefault) {
            $this->command->error('Aucun magasin trouvé. Créez au moins un magasin avant de lancer ce seeder.');
            return;
        }

        $produits = [
            // Matériel hôtelier
            ['libelle' => 'THE ACHORA',                          'categorie_id' => $c_hotel,  'seuil_alerte' => 2],
            ['libelle' => 'THE 20/20',                           'categorie_id' => $c_hotel,  'seuil_alerte' => 2],
            ['libelle' => 'THE ASMA',                            'categorie_id' => $c_hotel,  'seuil_alerte' => 10],
            ['libelle' => 'Sucre tropicana',                     'categorie_id' => $c_hotel,  'seuil_alerte' => 5],
            ['libelle' => 'SUCRE TROPICANNA',                    'categorie_id' => $c_hotel,  'seuil_alerte' => 2],
            ['libelle' => 'GOLD PETIT',                          'categorie_id' => $c_hotel,  'seuil_alerte' => 2],
            ['libelle' => 'GOLD GRAND',                          'categorie_id' => $c_hotel,  'seuil_alerte' => 2],
            ['libelle' => 'THE AZWAD',                           'categorie_id' => $c_hotel,  'seuil_alerte' => 20],
            ['libelle' => 'THE MALIKA',                          'categorie_id' => $c_hotel,  'seuil_alerte' => 20],
            ['libelle' => 'CARTONS DE GABELETS À CAFÉ',          'categorie_id' => $c_hotel,  'seuil_alerte' => 1000],
            ['libelle' => 'CARTONS DE GOBELETS CAFÉ petit',      'categorie_id' => $c_hotel,  'seuil_alerte' => 1000],
            ['libelle' => 'MACHINE À CAFÉ DULCE GUSTO',          'categorie_id' => $c_hotel,  'seuil_alerte' => 1],
            ['libelle' => 'SAC SUCRE',                           'categorie_id' => $c_hotel,  'seuil_alerte' => 1],
            ['libelle' => 'Carton d\'eau',                       'categorie_id' => $c_hotel,  'seuil_alerte' => 10],

            // Matériel électrique
            ['libelle' => 'Remote control barrière',             'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'INGLEEC E14 40W',                     'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'LED G45',                             'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'RADIATEUR',                           'categorie_id' => $c_elec,   'seuil_alerte' => 0],
            ['libelle' => 'CASQUE TRADUCTION',                   'categorie_id' => $c_elec,   'seuil_alerte' => 0],
            ['libelle' => 'CASQUE UTILISER SIMPLE',              'categorie_id' => $c_elec,   'seuil_alerte' => 0],
            ['libelle' => 'MEGA STAR',                           'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'PETIT JACK',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'CABLE XLR RS',                        'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'CONTACT EMPOLE',                      'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'SAC POUBELLE 240L',                   'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'GOULET',                              'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'VERRE AMPOLE E27',                    'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'ROLE DE GNE',                         'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'POLET 4516',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE 18W',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE 35W',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE RECLET 220',                   'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'CONTACTEUR 25 POUR UP',               'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'DOMINOH',                             'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE 10W',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE 4W',                           'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE E12 35W',                      'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE 50W ANJELEC',                  'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'POLET 36',                            'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'AMPOLE SOLEF',                        'categorie_id' => $c_elec,   'seuil_alerte' => 20],
            ['libelle' => 'AMPOLE FONTEN',                       'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'TRANFOL',                             'categorie_id' => $c_elec,   'seuil_alerte' => 20],
            ['libelle' => 'PRIS ITRANCH',                        'categorie_id' => $c_elec,   'seuil_alerte' => 20],
            ['libelle' => 'PROJECTEUR 200W',                     'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'ATACH',                               'categorie_id' => $c_elec,   'seuil_alerte' => 100],
            ['libelle' => 'CHEVIE',                              'categorie_id' => $c_elec,   'seuil_alerte' => 100],
            ['libelle' => 'AMPOLE SPOTE 12W',                    'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'D OUI',                               'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'CABLE 4*4',                           'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'BATTRIE AA',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'BATTRIE AAA',                         'categorie_id' => $c_elec,   'seuil_alerte' => 80],
            ['libelle' => 'BATTRIE 9V',                          'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'Ampole led 480',                      'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'Ampole 8W',                           'categorie_id' => $c_elec,   'seuil_alerte' => 2],
            ['libelle' => 'Ampole 35w',                          'categorie_id' => $c_elec,   'seuil_alerte' => 10],
            ['libelle' => 'Ampole 9W',                           'categorie_id' => $c_elec,   'seuil_alerte' => 1],
            ['libelle' => '117A COLEUR',                         'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => '217 A',                               'categorie_id' => $c_elec,   'seuil_alerte' => 5],
            ['libelle' => 'Adaptateur',                          'categorie_id' => $c_elec,   'seuil_alerte' => 5],

            // Matériel informatique
            ['libelle' => 'TYPE C P10 HOTV 11N1',                'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'Mac Co',                              'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'type c to Mac',                       'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'Clavier avec souris sans fille',      'categorie_id' => $c_info,   'seuil_alerte' => 4],
            ['libelle' => 'ordinateur portable acer',            'categorie_id' => $c_info,   'seuil_alerte' => 1],
            ['libelle' => 'CANON COLEUR G 3020',                 'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'CARTOUCHE 1106A',                     'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'Crouche CE 2',                        'categorie_id' => $c_info,   'seuil_alerte' => 10],
            ['libelle' => 'Crouche CE 278A',                     'categorie_id' => $c_info,   'seuil_alerte' => 10],
            ['libelle' => 'D OUI',                               'categorie_id' => $c_info,   'seuil_alerte' => 10],
            ['libelle' => 'HP 17A',                              'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 1110',                             'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 4105',                             'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 5230C',                            'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 5230K',                            'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 5230M',                            'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 1150',                             'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK160',                               'categorie_id' => $c_info,   'seuil_alerte' => 5],
            ['libelle' => 'TK 6305Y',                            'categorie_id' => $c_info,   'seuil_alerte' => 5],

            // Matériel de sonorisation et audiovisuel
            ['libelle' => 'RADIATEUR TRADUCTION',                'categorie_id' => $c_sono,   'seuil_alerte' => 0],
            ['libelle' => 'GRAND JACK PETIT JACK',               'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'récepteur',                           'categorie_id' => $c_sono,   'seuil_alerte' => 20],
            ['libelle' => 'contact américain',                   'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'BOITE ARCHIVE',                       'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'sacs poubelle 12w',                   'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'TP LINK',                             'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'PATCH RJ 45 100M',                    'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'N0BWI',                               'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'SPOTLIGHT PRÉSENTATION',              'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'RAP 72 PRO WIFI ACCESS POINT',        'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'HDMI 2.0 CABLE OPTICAL 80M',         'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'HDMI 10M',                            'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'HDMI CABLE',                          'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'RJ 45 CABLE 15M',                     'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'RJ 45 CABLE 3M',                      'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'HDMI EXTENDER',                       'categorie_id' => $c_sono,   'seuil_alerte' => 5],
            ['libelle' => 'CASQUE SIMPLE',                       'categorie_id' => $c_sono,   'seuil_alerte' => 50],
            ['libelle' => 'CASQUE VIP',                          'categorie_id' => $c_sono,   'seuil_alerte' => 10],
            ['libelle' => 'CASQUE VVIP',                         'categorie_id' => $c_sono,   'seuil_alerte' => 10],

            // Matériels et produits de ménage
            ['libelle' => 'Nettoyage mossant',                   'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'PROMSE',                              'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'sacs poubelle 240L',                  'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Corbeille de poubelle 240L',          'categorie_id' => $c_menage, 'seuil_alerte' => 5],
            ['libelle' => 'Papier fresh',                        'categorie_id' => $c_menage, 'seuil_alerte' => 35],
            ['libelle' => 'vif liquide ménage',                  'categorie_id' => $c_menage, 'seuil_alerte' => 5],
            ['libelle' => 'Ajachs poudre',                       'categorie_id' => $c_menage, 'seuil_alerte' => 12],
            ['libelle' => 'Balais africains',                    'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Balais pour ménage',                  'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Désinfectant',                        'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Insecticide baygon',                  'categorie_id' => $c_menage, 'seuil_alerte' => 24],
            ['libelle' => 'Lessive',                             'categorie_id' => $c_menage, 'seuil_alerte' => 24],
            ['libelle' => 'Désodorisant WC',                     'categorie_id' => $c_menage, 'seuil_alerte' => 12],
            ['libelle' => 'Original cotail',                     'categorie_id' => $c_menage, 'seuil_alerte' => 30],
            ['libelle' => 'Original coton mouchoir',             'categorie_id' => $c_menage, 'seuil_alerte' => 20],
            ['libelle' => 'Papiers hygiéniques',                 'categorie_id' => $c_menage, 'seuil_alerte' => 20],
            ['libelle' => 'Piles + brosse',                      'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Savon en morceaux',                   'categorie_id' => $c_menage, 'seuil_alerte' => 20],
            ['libelle' => 'Savon en poudre omo',                 'categorie_id' => $c_menage, 'seuil_alerte' => 150],
            ['libelle' => 'Savon liquide lave vitres',           'categorie_id' => $c_menage, 'seuil_alerte' => 15],
            ['libelle' => 'Savon liquide multi-usage',           'categorie_id' => $c_menage, 'seuil_alerte' => 12],
            ['libelle' => 'Serpillère',                          'categorie_id' => $c_menage, 'seuil_alerte' => 10],
            ['libelle' => 'Brouette',                            'categorie_id' => $c_menage, 'seuil_alerte' => 1],
            ['libelle' => 'Ciseaux de jar',                      'categorie_id' => $c_menage, 'seuil_alerte' => 5],

            // Matériels de jardinage
            ['libelle' => 'Corbeille de poubelle 240L jardinage','categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Engrais',                             'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Torches',                             'categorie_id' => $c_jardin, 'seuil_alerte' => 3],
            ['libelle' => 'Gant nettoyage',                      'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Gant pour engrais',                   'categorie_id' => $c_jardin, 'seuil_alerte' => 2],
            ['libelle' => 'Piquet',                              'categorie_id' => $c_jardin, 'seuil_alerte' => 3],
            ['libelle' => 'Piquets',                             'categorie_id' => $c_jardin, 'seuil_alerte' => 3],
            ['libelle' => 'Raccord d\'arrosage',                 'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Râteaux',                             'categorie_id' => $c_jardin, 'seuil_alerte' => 3],
            ['libelle' => 'Poubelle 20L',                        'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Sécateur de jardin',                  'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Balais extérieur',                    'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Gant pour jardinage',                 'categorie_id' => $c_jardin, 'seuil_alerte' => 5],
            ['libelle' => 'Sécateur de jardin 240L',             'categorie_id' => $c_jardin, 'seuil_alerte' => 5],

            // Fournitures de bureau
            ['libelle' => 'Agrafeuse',                           'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Block notes',                         'categorie_id' => $c_bureau, 'seuil_alerte' => 30],
            ['libelle' => 'Boite d\'archive',                    'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Classeur chrono',                     'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Classeur de registre de courrier',    'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Scotch',                              'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Enveloppe A4',                        'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Enveloppe Rectangulaire',             'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Marqueurs',                           'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Sachet clips 25mm',                   'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Sachet clips 50 MM',                  'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Papier de tableau',                   'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Papier note repositionnables (PF) Petit', 'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Papier note repositionnables (PF) Grand', 'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Paquettes de stylo',                  'categorie_id' => $c_bureau, 'seuil_alerte' => 150],
            ['libelle' => 'Ramettes de papier',                  'categorie_id' => $c_bureau, 'seuil_alerte' => 100],
            ['libelle' => 'Rallonge 5M',                         'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
            ['libelle' => 'Rames de papier',                     'categorie_id' => $c_bureau, 'seuil_alerte' => 100],
            ['libelle' => 'Surligneur',                          'categorie_id' => $c_bureau, 'seuil_alerte' => 10],
        ];

        $inserted = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($produits as $produit) {
            if (is_null($produit['categorie_id'])) {
                $this->command->warn("  [SKIP] Catégorie introuvable pour : {$produit['libelle']}");
                $errors++;
                continue;
            }

            $exists = DB::table('stock_produits')
                ->where('libelle', $produit['libelle'])
                ->where('categorie_id', $produit['categorie_id'])
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà existant : {$produit['libelle']}");
                $skipped++;
                continue;
            }

            DB::table('stock_produits')->insert([
                'libelle'       => $produit['libelle'],
                'categorie_id'  => $produit['categorie_id'],
                'magasin_id'    => $magasinDefault,
                'stock_initial' => 0,
                'stock_actuel'  => 0,
                'seuil_alerte'  => $produit['seuil_alerte'],
                'descriptif'    => null,
                'stockage'      => null,
                'observations'  => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $this->command->info("  [OK]   {$produit['libelle']}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} inséré(s), {$skipped} ignoré(s), {$errors} erreur(s).");
        $this->command->newLine();
        $this->command->warn("N.B : Tous les produits ont été affectés au magasin ID={$magasinDefault}.");
        $this->command->warn("      Modifiez le magasin_id de chaque produit selon vos besoins.");
    }
}
