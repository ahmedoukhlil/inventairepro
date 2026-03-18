<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $designationIds = [
            6121, 6170, 6173, 6174, 6175, 6176, 6177, 6178, 6179, 6180,
            6183, 6184, 6185, 6186, 6187, 6188, 6189, 6193, 6197, 6198,
            6200, 6201, 6204, 6205, 6206, 6224, 6225, 6226, 6227, 6228,
            6229, 6238, 6239, 6240, 6241, 6242, 6243, 6244, 6245, 6246,
            6254, 6273, 6274, 6282, 6283, 6284, 6285, 6286, 6287, 6288,
            6289, 6290, 6295, 6296, 6297, 6299, 6300, 6303, 6305, 6306,
            6307, 6308, 6310, 6312, 6321, 6323, 6324, 6343, 6372, 6376,
            6377, 6384, 6387, 6388, 6389, 6391, 6393, 6394, 6396, 6397,
            6398, 6399, 6449, 6450, 6451, 6452, 6480,
        ];

        $affectationHasLocalisation = Schema::hasColumn('affectation', 'idLocalisation');

        $normalizeYear = static function ($dateValue): ?int {
            if (empty($dateValue)) {
                return null;
            }

            if (is_numeric($dateValue)) {
                $year = (int) $dateValue;
                return ($year >= 1900 && $year <= 9999) ? $year : null;
            }

            if (is_string($dateValue)) {
                $year = (int) substr($dateValue, 0, 4);
                return ($year >= 1900 && $year <= 9999) ? $year : null;
            }

            return null;
        };

        DB::transaction(function () use ($designationIds, $affectationHasLocalisation, $normalizeYear): void {
            $defaultCategorieId = DB::table('categorie')->value('idCategorie');
            $defaultEtatId = DB::table('etat')->value('idEtat');
            $defaultNatJurId = DB::table('naturejurdique')->value('idNatJur');
            $defaultSourceFinId = DB::table('sourcefinancement')->value('idSF');
            $defaultLocalisationId = DB::table('localisation')->value('idLocalisation');
            $defaultAffectationId = DB::table('affectation')->value('idAffectation');

            if (empty($defaultCategorieId) || empty($defaultEtatId) || empty($defaultNatJurId) || empty($defaultSourceFinId)) {
                throw new \RuntimeException('Impossible de restaurer: tables de reference manquantes (categorie/etat/nature juridique/source financement).');
            }

            DB::table('corbeille_immobilisations')
                ->whereIn('idDesignation', $designationIds)
                ->where('deleted_reason', 'Suppression par designation (migration)')
                ->orderBy('id')
                ->chunkById(300, function ($items) use (
                    $defaultCategorieId,
                    $defaultEtatId,
                    $defaultNatJurId,
                    $defaultSourceFinId,
                    &$defaultLocalisationId,
                    &$defaultAffectationId,
                    $affectationHasLocalisation,
                    $normalizeYear
                ): void {
                    foreach ($items as $item) {
                        if (DB::table('gesimmo')->where('NumOrdre', $item->original_num_ordre)->exists()) {
                            continue;
                        }

                        $categorieId = DB::table('categorie')->where('idCategorie', $item->idCategorie)->exists()
                            ? $item->idCategorie
                            : $defaultCategorieId;

                        $etatId = DB::table('etat')->where('idEtat', $item->idEtat)->exists()
                            ? $item->idEtat
                            : $defaultEtatId;

                        $natJurId = DB::table('naturejurdique')->where('idNatJur', $item->idNatJur)->exists()
                            ? $item->idNatJur
                            : $defaultNatJurId;

                        $sourceFinId = DB::table('sourcefinancement')->where('idSF', $item->idSF)->exists()
                            ? $item->idSF
                            : $defaultSourceFinId;

                        if (!DB::table('designation')->where('id', $item->idDesignation)->exists()) {
                            DB::table('designation')->insert([
                                'id' => $item->idDesignation,
                                'designation' => $item->designation_label ?: ('Designation ' . $item->idDesignation),
                                'CodeDesignation' => null,
                                'idCat' => $categorieId,
                            ]);
                        }

                        if (!DB::table('emplacement')->where('idEmplacement', $item->idEmplacement)->exists()) {
                            $localisationId = $item->emplacement_id_localisation ?: $defaultLocalisationId;

                            if (empty($localisationId)) {
                                $localisationId = DB::table('localisation')->insertGetId([
                                    'Localisation' => $item->localisation_label ?: 'Localisation corbeille',
                                    'CodeLocalisation' => null,
                                ]);
                                $defaultLocalisationId = $localisationId;
                            } elseif (!DB::table('localisation')->where('idLocalisation', $localisationId)->exists()) {
                                DB::table('localisation')->insert([
                                    'idLocalisation' => $localisationId,
                                    'Localisation' => $item->localisation_label ?: ('Localisation ' . $localisationId),
                                    'CodeLocalisation' => null,
                                ]);
                            }

                            $affectationId = $item->emplacement_id_affectation ?: $defaultAffectationId;

                            if (empty($affectationId)) {
                                $affectationData = [
                                    'Affectation' => $item->affectation_label ?: 'Affectation corbeille',
                                    'CodeAffectation' => null,
                                ];
                                if ($affectationHasLocalisation) {
                                    $affectationData['idLocalisation'] = $localisationId;
                                }
                                $affectationId = DB::table('affectation')->insertGetId($affectationData);
                                $defaultAffectationId = $affectationId;
                            } elseif (!DB::table('affectation')->where('idAffectation', $affectationId)->exists()) {
                                $affectationData = [
                                    'idAffectation' => $affectationId,
                                    'Affectation' => $item->affectation_label ?: ('Affectation ' . $affectationId),
                                    'CodeAffectation' => null,
                                ];
                                if ($affectationHasLocalisation) {
                                    $affectationData['idLocalisation'] = $localisationId;
                                }
                                DB::table('affectation')->insert($affectationData);
                            }

                            DB::table('emplacement')->insert([
                                'idEmplacement' => $item->idEmplacement,
                                'Emplacement' => $item->emplacement_label ?: ('Emplacement ' . $item->idEmplacement),
                                'CodeEmplacement' => $item->emplacement_code,
                                'idAffectation' => $affectationId,
                                'idLocalisation' => $localisationId,
                            ]);
                        }

                        DB::table('gesimmo')->insert([
                            'NumOrdre' => $item->original_num_ordre,
                            'idDesignation' => $item->idDesignation,
                            'idCategorie' => $categorieId,
                            'idEtat' => $etatId,
                            'idEmplacement' => $item->idEmplacement,
                            'idNatJur' => $natJurId,
                            'idSF' => $sourceFinId,
                            'DateAcquisition' => $normalizeYear($item->DateAcquisition),
                            'Observations' => $item->Observations,
                        ]);

                        if (!empty($item->barcode) && !DB::table('codes')->where('idGesimmo', $item->original_num_ordre)->exists()) {
                            DB::table('codes')->insert([
                                'idGesimmo' => $item->original_num_ordre,
                                'barcode' => $item->barcode,
                            ]);
                        }

                        DB::table('corbeille_immobilisations')->where('id', $item->id)->delete();
                    }
                }, 'id');
        });
    }

    public function down(): void
    {
        // Migration de restauration:
        // rollback automatique non fiable, utiliser un backup si necessaire.
    }
};
