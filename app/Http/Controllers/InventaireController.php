<?php

namespace App\Http\Controllers;

use App\Models\Etat;
use App\Models\Inventaire;
use App\Services\InventaireService;
use App\Services\RapportService;
use Illuminate\Http\Request;

class InventaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventaire $inventaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventaire $inventaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventaire $inventaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventaire $inventaire)
    {
        //
    }

    /**
     * Clôture un inventaire
     * 
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cloturer(Inventaire $inventaire)
    {
        if ($inventaire->statut === 'cloture') {
            return redirect()->back()->with('warning', 'Cet inventaire est déjà clôturé');
        }

        try {
            $inventaire->cloturer(auth()->id());
            
            return redirect()->route('inventaires.show', $inventaire)
                ->with('success', 'Inventaire clôturé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la clôture: ' . $e->getMessage());
        }
    }

    /**
     * Exporte un inventaire en format PDF
     * 
     * @param Inventaire $inventaire
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportPDF(Inventaire $inventaire)
    {
        try {
            $service = app(RapportService::class);
            return $service->streamRapportPDF($inventaire);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    private function buildEtatsConstate(): array
    {
        $codeMap = ['NF' => 'neuf', 'BE' => 'bon', 'DFCT' => 'mauvais'];
        $labelMap = [
            'neuf' => 'neuf', 'bon' => 'bon', 'bon etat' => 'bon', 'bon état' => 'bon',
            'moyen' => 'moyen', 'mauvais' => 'mauvais',
            'défectueux' => 'mauvais', 'defectueux' => 'mauvais',
            'défectueuse' => 'mauvais', 'defectueuse' => 'mauvais',
        ];
        $colors = [
            'neuf' => '#dcfce7',
            'bon'  => '#dbeafe',
            'moyen'=> '#fef9c3',
            'mauvais' => '#fef3c7',
        ];
        $textColors = [
            'neuf' => '#166534', 'bon' => '#1e40af',
            'moyen' => '#854d0e', 'mauvais' => '#92400e',
        ];

        $result = [];
        foreach (Etat::all() as $etat) {
            $constate = isset($codeMap[$etat->CodeEtat])
                ? $codeMap[$etat->CodeEtat]
                : ($labelMap[mb_strtolower(trim($etat->Etat))] ?? null);
            if ($constate) {
                $result[$constate] = [
                    'label' => $etat->Etat,
                    'bg'    => $colors[$constate] ?? '#f3f4f6',
                    'color' => $textColors[$constate] ?? '#374151',
                ];
            }
        }
        if (!isset($result['moyen'])) {
            $result['moyen'] = ['label' => 'Moyen', 'bg' => $colors['moyen'], 'color' => $textColors['moyen']];
        }
        $result['bon'] ??= ['label' => 'Bon état', 'bg' => $colors['bon'], 'color' => $textColors['bon']];
        return $result;
    }

    /**
     * Exporte un inventaire en format Excel
     */
    public function exportExcel(Inventaire $inventaire)
    {
        return redirect()->back()->with('info', 'Fonctionnalité en cours de développement');
    }

    /**
     * Vue d'impression — rapport complet et détaillé
     */
    public function imprimer(Inventaire $inventaire)
    {
        if (!in_array($inventaire->statut, ['termine', 'cloture'])) {
            abort(403, 'Le rapport n\'est disponible que pour les inventaires terminés ou clôturés.');
        }

        $inventaire->load([
            'creator',
            'closer',
            'inventaireLocalisations.localisation',
            'inventaireLocalisations.agent',
            'inventaireScans.bien.designation',
            'inventaireScans.bien.categorie',
            'inventaireScans.bien.emplacement.localisation',
            'inventaireScans.localisationReelle',
            'inventaireScans.agent',
        ]);

        $service = app(InventaireService::class);
        $statistiques = $service->calculerStatistiques($inventaire);
        $anomalies = $service->detecterAnomalies($inventaire);

        // Scans par statut
        $biensPresents  = $inventaire->inventaireScans->where('statut_scan', 'present');
        $biensDeplaces  = $inventaire->inventaireScans->where('statut_scan', 'deplace');
        $biensAbsents   = $inventaire->inventaireScans->where('statut_scan', 'absent');
        $biensDeteriores = $inventaire->inventaireScans->where('statut_scan', 'deteriore');
        $biensDefectueux = $inventaire->inventaireScans->where('etat_constate', 'mauvais');

        // Répartition par localisation (pour tableau récapitulatif)
        $parLocalisation = $inventaire->inventaireLocalisations->map(function ($invLoc) use ($inventaire) {
            $scansLoc = $inventaire->inventaireScans->where('inventaire_localisation_id', $invLoc->id);
            return [
                'code'            => $invLoc->localisation?->CodeLocalisation ?? 'N/A',
                'designation'     => $invLoc->localisation?->Localisation ?? 'N/A',
                'agent'           => $invLoc->agent?->users ?? $invLoc->agent?->name ?? '—',
                'attendus'        => $invLoc->nombre_biens_attendus,
                'scannes'         => $invLoc->nombre_biens_scannes,
                'presents'        => $scansLoc->where('statut_scan', 'present')->count(),
                'deplaces'        => $scansLoc->where('statut_scan', 'deplace')->count(),
                'absents'         => $scansLoc->where('statut_scan', 'absent')->count(),
                'deteriores'      => $scansLoc->where('statut_scan', 'deteriore')->count(),
                'taux_conformite' => $invLoc->nombre_biens_attendus > 0
                    ? round(($scansLoc->where('statut_scan', 'present')->count() / $invLoc->nombre_biens_attendus) * 100, 1)
                    : 0,
            ];
        });

        // Répartition par catégorie
        $parCategorie = $inventaire->inventaireScans
            ->groupBy(fn($s) => $s->bien?->categorie?->Categorie ?? 'Non renseigné')
            ->map(fn($group, $cat) => [
                'categorie' => $cat,
                'total'     => $group->count(),
                'presents'  => $group->where('statut_scan', 'present')->count(),
                'deplaces'  => $group->where('statut_scan', 'deplace')->count(),
                'absents'   => $group->where('statut_scan', 'absent')->count(),
                'defectueux'=> $group->where('etat_constate', 'mauvais')->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $etatsConstate = $this->buildEtatsConstate();

        return view('inventaires.rapport-print', compact(
            'inventaire',
            'statistiques',
            'anomalies',
            'biensPresents',
            'biensDeplaces',
            'biensAbsents',
            'biensDeteriores',
            'biensDefectueux',
            'parLocalisation',
            'parCategorie',
            'etatsConstate',
        ));
    }
}

