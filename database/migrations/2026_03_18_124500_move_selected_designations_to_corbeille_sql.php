<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

        $now = now();

        DB::table('gesimmo as g')
            ->select('g.NumOrdre')
            ->whereIn('g.idDesignation', $designationIds)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('corbeille_immobilisations as ci')
                    ->whereColumn('ci.original_num_ordre', 'g.NumOrdre');
            })
            ->orderBy('g.NumOrdre')
            ->chunkById(100, function ($chunk) use ($now): void {
                $numOrdresChunk = $chunk->pluck('NumOrdre')->values()->all();

                if (empty($numOrdresChunk)) {
                    return;
                }

                $this->runChunkWithRetry(function () use ($numOrdresChunk, $now): void {
                    $rows = DB::table('gesimmo as g')
                        ->leftJoinSub(
                            DB::table('codes')
                                ->select('idGesimmo', DB::raw('MAX(barcode) as barcode'))
                                ->groupBy('idGesimmo'),
                            'c',
                            fn ($join) => $join->on('c.idGesimmo', '=', 'g.NumOrdre')
                        )
                        ->leftJoin('designation as d', 'd.id', '=', 'g.idDesignation')
                        ->leftJoin('emplacement as e', 'e.idEmplacement', '=', 'g.idEmplacement')
                        ->leftJoin('affectation as a', 'a.idAffectation', '=', 'e.idAffectation')
                        ->leftJoin('localisation as l', 'l.idLocalisation', '=', 'e.idLocalisation')
                        ->whereIn('g.NumOrdre', $numOrdresChunk)
                        ->select([
                            'g.NumOrdre as original_num_ordre',
                            'g.idDesignation',
                            'g.idCategorie',
                            'g.idEtat',
                            'g.idEmplacement',
                            'g.idNatJur',
                            'g.idSF',
                            'g.DateAcquisition',
                            'g.Observations',
                            'c.barcode',
                            'e.Emplacement as emplacement_label',
                            'e.CodeEmplacement as emplacement_code',
                            'e.idAffectation as emplacement_id_affectation',
                            'e.idLocalisation as emplacement_id_localisation',
                            'a.Affectation as affectation_label',
                            'l.Localisation as localisation_label',
                            'd.designation as designation_label',
                        ])
                        ->get();

                    if ($rows->isEmpty()) {
                        return;
                    }

                    $toInsert = [];

                    foreach ($rows as $row) {
                        $dateAcquisition = null;
                        if (!empty($row->DateAcquisition)) {
                            $year = (int) $row->DateAcquisition;
                            if ($year >= 1900 && $year <= 9999) {
                                $dateAcquisition = sprintf('%04d-01-01', $year);
                            }
                        }

                        $toInsert[] = [
                            'original_num_ordre' => $row->original_num_ordre,
                            'idDesignation' => $row->idDesignation,
                            'idCategorie' => $row->idCategorie,
                            'idEtat' => $row->idEtat,
                            'idEmplacement' => $row->idEmplacement,
                            'idNatJur' => $row->idNatJur,
                            'idSF' => $row->idSF,
                            'DateAcquisition' => $dateAcquisition,
                            'Observations' => $row->Observations,
                            'barcode' => $row->barcode,
                            'emplacement_label' => $row->emplacement_label,
                            'emplacement_code' => $row->emplacement_code,
                            'emplacement_id_affectation' => $row->emplacement_id_affectation,
                            'emplacement_id_localisation' => $row->emplacement_id_localisation,
                            'affectation_label' => $row->affectation_label,
                            'localisation_label' => $row->localisation_label,
                            'designation_label' => $row->designation_label,
                            'deleted_reason' => 'Suppression par designation (SQL)',
                            'deleted_by_user_id' => null,
                            'deleted_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    DB::table('corbeille_immobilisations')->insert($toInsert);
                    DB::table('codes')->whereIn('idGesimmo', $numOrdresChunk)->delete();
                    DB::table('gesimmo')->whereIn('NumOrdre', $numOrdresChunk)->delete();
                });
            }, 'g.NumOrdre', 'NumOrdre');
    }

    public function down(): void
    {
        // Migration de donnees destructive:
        // rollback auto non fiable, restaurer depuis backup si necessaire.
    }

    private function runChunkWithRetry(callable $callback, int $maxAttempts = 5): void
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                DB::transaction($callback, 1);
                return;
            } catch (\Illuminate\Database\QueryException $e) {
                $mysqlErrorCode = (int) ($e->errorInfo[1] ?? 0);
                $isLockError = in_array($mysqlErrorCode, [1205, 1213], true);

                if (!$isLockError || $attempt >= $maxAttempts) {
                    throw $e;
                }

                usleep(300000 * $attempt);
            }
        }
    }
};
