<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockEntreeInitialisationSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer l'ID du premier utilisateur admin
        $userId = DB::table('users')->value('idUser');
        if (!$userId) {
            $this->command->error('Aucun utilisateur trouvé.');
            return;
        }

        // Stocks initiaux extraits de la feuille Excel (libelle => stock_actuel)
        $stocks = [
            'THE ACHORA'                        => 7,
            'THE 20/20'                         => 1,
            'remote controlle barriere'         => 6,
            'TYPE C TO HDTV 11IN 1'             => 2,
            'type c to hdmi'                    => 1,
            'INGELEC E14 40W'                   => 96,
            'THE ASMA'                          => 1,
            'maye mossant'                      => 10,
            'LED G45'                           => 0,
            'sucre tropicana'                   => 1,
            'CHEMISE'                           => 1,
            'RADIATEUR TRADUCTION'              => 0,
            'CASQUE TRADUCTION'                 => 0,
            'CASQUE UTILISER SIMPLE'            => 0,
            'KRIPA STA'                         => 1,
            'RADIATEUR'                         => 0,
            'GRAND JACK PETIT JACK'             => 4,
            'PETIT JACK'                        => 5,
            'CABLE XLR RSA'                     => 5,
            'récepteur'                         => 0,
            'Contact EMPOLE'                    => 9,
            'BOITE ARCHIVE'                     => 27,
            'Mon conférence'                    => 2,
            'Ampole 12w'                        => 10,
            'SAC POUBELLE 240L'                 => 11,
            'GOULET'                            => 5,
            'CADRE AMPOLE E27'                  => 27,
            'ROLE DE GNE'                       => 0,
            'PALET 4516'                        => 1,
            'AMPOLE 18W'                        => 6,
            'AMPOLE 35W'                        => 200,
            'AMPOLE 16'                         => 16,
            'RELE TERMIQUE'                     => 1,
            'CONTACTEUR 25 POUR UP'             => 1,
            'DOMINOH'                           => 8,
            'AMPOLE 10W'                        => 1,
            'AMPOLE 4W'                         => 27,
            'AMPOLE E12 35 W'                   => 12,
            'AMPOLE 50W ANIELEC'                => 6,
            'PALET 16'                          => 1,
            'AMPOLE SOLEF'                      => 61,
            'AMPOLE FONTEN'                     => 7,
            'TRANFOU'                           => 16,
            'PRIS-ITRANCH'                      => 6,
            'PROJECTEUR 200 W'                  => 3,
            'ATACH'                             => 12,
            'CHEVIE'                            => 200,
            'AMPOLE SPOTE 12W'                  => 7,
            'D QUI'                             => 1,
            'CABLE 4*4'                         => 27,
            'Clavier avec souris sans fille'    => 3,
            'ordinateur portable accer'         => 7,
            'sacs poubelle 240L'                => 40,
            'BATTRIE AA'                        => 43,
            'BATTRIE AAA'                       => 338,
            'BATTRIE 9V'                        => 3,
            'TP LINK'                           => 1,
            'PATCH RJ 45 100M'                  => 5,
            'NBWI'                              => 1,
            'SPOTLIGHT PRÉSANTATION'            => 1,
            'RAP 72 PRO WIFI ACCESS POINT'      => 1,
            'HDMI 2.0 CABLE OPTICAL 80M'        => 1,
            'HDMI 10M'                          => 4,
            'HDMI CABLE 20M'                    => 1,
            'RJ 45 CABLE 15M'                   => 5,
            'HDMI EXTENDER'                     => 1,
            'CASQUE SIMPLE'                     => 138,
            'CASQUE VIP'                        => 8,
            'polele'                            => 26,
            'Ampole led 480'                    => 27,
            'AMPOLE 8W'                         => 9,
            'Ampole 35w'                        => 75,
            'Coffree mod'                       => 1,
            'SUCRE TROPICANNA'                  => 3,
            'CAFE GOLD PETIT'                   => 79,
            'CAFE GOLD GRAND'                   => 0,
            'THE AZWAD'                         => 154,
            'THE MALIKA'                        => 143,
            'BIQUETS CAPSUL ESPRESSO'           => 371,
            'CARTONS DE GABELETS A CAFE'        => 4750,
            'CARTONS DE GOBELETS CAFE petit'    => 0,
            'MACHINE CAFE DULCE GUSTO'          => 1,
            'SAC SUCRE'                         => 6,
            'CAFE SUCRE'                        => 150,
            'G03'                               => 5,
            'Ampole electrique'                 => 27,
            '117A COLEUR'                       => 5,
            'TRANFOU'                           => 12,
            'CANON COLEUR G 3020'               => 2,
            'CARTOUCHE 1106A'                   => 12,
            'Crouche CE 278A'                   => 25,
            'G3020 510'                         => 0,
            'TK 1110'                           => 5,
            'TK 4105'                           => 13,
            'TK S230C'                          => 13,
            'TK S230M'                          => 11,
            'TK S230Y'                          => 11,
            'TK S230BK'                         => 22,
            'TK160'                             => 16,
            'TK 6320Y'                          => 1,
            'Papier fresh'                      => 8,
            'vif liquide menage'                => 28,
            'Ajack poudhe'                      => 232,
            'Balais africain'                   => 45,
            'Balais pour ménage'                => 47,
            'Désinfectant'                      => 156,
            'Désodorisant WC'                   => 84,
            'Insecticide baygon'                => 270,
            'Original cotaill'                  => 262,
            'Papier mouchoir'                   => 101,
            'Piles + brosses'                   => 41,
            'Savon en morceaux'                 => 527,
            'Savon en poudre omé'               => 320,
            'Savon liquide lave vitres'         => 114,
            'Savon liquide multi-usage'         => 156,
            'Serpihere'                         => 14,
            'Torchons'                          => 11,
            'Ciseaux de jardin'                 => 9,
            'Corbeille de poubelle 240L'        => 2,
            'Corbeille de poubelle 20L'         => 6,
            'Engrais'                           => 0,
            'Fourches'                          => 5,
            'Gant nettoyage'                    => 23,
            'Gant pour engrais'                 => 5,
            'Houe'                              => 3,
            'Piquets'                           => 5,
            'Raccord d\'arrosage'               => 31,
            'Râteaux'                           => 25,
            'Sac poubelle 20L'                  => 12,
            'Sécateur de jardin'                => 16,
            'Balais extérieur'                  => 9,
            'Adaptateur'                        => 4,
            'Agrafeuse'                         => 8,
            'Block notes'                       => 310,
            'Boite d\'archive'                  => 49,
            'Classeur chrono'                   => 24,
            'Classeur de registre de courier'   => 21,
            'Enveloppe A5'                      => 23,
            'Enveloppe A4'                      => 8,
            'Enveloppe Rectangulaire'           => 0,
            'Marqueurs'                         => 108,
            'Papier clips 25mm'                 => 70,
            'Papier clips 50 MM'                => 59,
            'Papier de tableau'                 => 6,
            'Papier notes repositionnables (P)' => 33,
            'Papier notes repositionnables'     => 0,
            'Paquettes de stylo'                => 1049,
            'Ramettes de papier'                => 0,
            'Réf agrafeuse'                     => 34,
            'Ruligneau 5 M'                     => 0,
            'Surligneur'                        => 0,
        ];

        $dateInit = '2026-01-01';
        $inserted = 0;
        $skipped  = 0;
        $notFound = [];

        foreach ($stocks as $libelle => $quantite) {
            if ($quantite <= 0) {
                $skipped++;
                continue;
            }

            $produit = DB::table('stock_produits')->where('libelle', $libelle)->first();

            if (!$produit) {
                // Tentative de recherche approximative
                $produit = DB::table('stock_produits')
                    ->whereRaw('LOWER(libelle) LIKE ?', ['%' . strtolower($libelle) . '%'])
                    ->first();
            }

            if (!$produit) {
                $notFound[] = $libelle;
                continue;
            }

            // Vérifier si une entrée d'initialisation existe déjà
            $exists = DB::table('stock_entrees')
                ->where('produit_id', $produit->id)
                ->where('observations', 'Stock initial - initialisation système')
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà initialisé : {$produit->libelle}");
                $skipped++;
                continue;
            }

            // Créer l'entrée d'initialisation
            DB::table('stock_entrees')->insert([
                'date_entree'      => $dateInit,
                'reference_commande' => 'INIT-2026',
                'produit_id'       => $produit->id,
                'fournisseur_id'   => null,
                'quantite'         => $quantite,
                'observations'     => 'Stock initial - initialisation système',
                'created_by'       => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Mettre à jour stock_actuel et stock_initial du produit
            DB::table('stock_produits')->where('id', $produit->id)->update([
                'stock_actuel'  => $quantite,
                'stock_initial' => $quantite,
                'updated_at'    => now(),
            ]);

            $this->command->info("  [OK]   {$produit->libelle} → {$quantite}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} initialisé(s), {$skipped} ignoré(s) (stock = 0 ou déjà fait).");

        if (!empty($notFound)) {
            $this->command->newLine();
            $this->command->warn('Produits non trouvés en base (' . count($notFound) . ') :');
            foreach ($notFound as $libelle) {
                $this->command->warn("  - {$libelle}");
            }
        }
    }
}
