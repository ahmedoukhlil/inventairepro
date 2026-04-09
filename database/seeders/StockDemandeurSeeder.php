<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockDemandeurSeeder extends Seeder
{
    public function run(): void
    {
        $demandeurs = [
            ['nom' => 'Moulay Sbaai',                    'poste_service' => 'CADER'],
            ['nom' => 'Saleck Vall',                     'poste_service' => 'CADER'],
            ['nom' => 'Mohamed Lemine Mohamed Salem',    'poste_service' => 'Chef Section des Installations'],
            ['nom' => 'Kandioura Samba Sidibé',          'poste_service' => 'Chef Section Sécurité'],
            ['nom' => 'Roghaya Limam',                   'poste_service' => 'Chef Section Personnel'],
            ['nom' => 'Ahmed Mohmoud El Houssein',       'poste_service' => 'Chef Section R-F'],
            ['nom' => 'Fatimatou Mhd Vall M\'Saboue',   'poste_service' => 'Chef Section Info-Téleco'],
            ['nom' => 'El Moustapha Ahmed Hamed',        'poste_service' => 'Chef Section Magasins'],
            ['nom' => 'Hamdine Mamadou Diallo',          'poste_service' => 'Chef Section Audio Visuel'],
            ['nom' => 'Halima Mohamed Aloueimine',       'poste_service' => 'Chef Section Achats'],
            ['nom' => 'Sid\'Ahmed Mohamed Cheikh',       'poste_service' => 'Chef Section Traduction'],
            ['nom' => 'Fatimatou Bellah Mogueye',        'poste_service' => 'Chef Section Mobilier'],
            ['nom' => 'N\'Diaga Omar Séne',              'poste_service' => 'Chef Section Entretien Bâtiment'],
            ['nom' => 'Sidi Brahim',                     'poste_service' => 'Chef Section Logistique'],
            ['nom' => 'Fatimatou Deddahi Abdalahi',      'poste_service' => 'Chef Section Comptabilité'],
            ['nom' => 'Abdalahi Mohamed Lemine Bedy',    'poste_service' => 'Chef Section REM'],
            ['nom' => 'Cherif Ahmed Deh',                'poste_service' => 'Chef Section Trésorerie'],
            ['nom' => 'Fatimatou Soko',                  'poste_service' => 'Chef Section Hôtellerie'],
            ['nom' => 'Abdalahi Ahmed Seifer',           'poste_service' => 'Chef Service Communication MKT'],
            ['nom' => 'Moustapha Mohamed Ismail',        'poste_service' => 'DR-CIC Al Mourabitoune'],
            ['nom' => 'Yacoub Sleck El Ghali El Kharachi','poste_service' => 'Directeur Technique'],
            ['nom' => 'Fatimatou Cheyakh',               'poste_service' => 'Directrice Contrôle de Gestion'],
            ['nom' => 'El Moustapha Mohamed Taleb',      'poste_service' => 'Directeur DMG'],
            ['nom' => 'Habib Mohamed',                   'poste_service' => 'Directeur Commercial'],
            ['nom' => 'Deija Boulebatt',                 'poste_service' => 'DAF'],
            ['nom' => 'Said Beddy',                      'poste_service' => 'Président de la PMP'],
            ['nom' => 'Mohamed Salem Mohamed',           'poste_service' => 'Chef Service Maintenance'],
            ['nom' => 'Wagne Moctar Bocar',              'poste_service' => 'Chef Service Comptabilité'],
            ['nom' => 'Sarra Ahmed Abeidou',             'poste_service' => 'Chef Service RH'],
            ['nom' => 'Cheikh Brahim Sidine',            'poste_service' => 'Chef Service Gestion Immobilière'],
            ['nom' => 'El Hadj Chemse Dine',             'poste_service' => 'Chef Service Approvisionnement'],
            ['nom' => 'Mohamed Salem El Mamy',           'poste_service' => 'Chef Service Clientél'],
        ];

        $inserted = 0;
        $skipped  = 0;

        foreach ($demandeurs as $demandeur) {
            $exists = DB::table('stock_demandeurs')
                ->where('nom', $demandeur['nom'])
                ->exists();

            if ($exists) {
                $this->command->warn("  [SKIP] Déjà existant : {$demandeur['nom']}");
                $skipped++;
                continue;
            }

            DB::table('stock_demandeurs')->insert([
                'nom'           => $demandeur['nom'],
                'poste_service' => $demandeur['poste_service'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $this->command->info("  [OK]   {$demandeur['nom']} — {$demandeur['poste_service']}");
            $inserted++;
        }

        $this->command->newLine();
        $this->command->info("Résultat : {$inserted} inséré(s), {$skipped} ignoré(s).");
    }
}
