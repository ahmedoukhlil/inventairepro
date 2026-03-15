<?php

namespace App\Http\Controllers;

use App\Exports\CorbeilleImmobilisationsExport;
use App\Models\Affectation;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\CorbeilleImmobilisation;
use App\Models\Designation;
use App\Models\Emplacement;
use App\Models\Etat;
use App\Models\Gesimmo;
use App\Models\LocalisationImmo;
use App\Models\NatureJuridique;
use App\Models\SourceFinancement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CorbeilleImmobilisationsController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'filter_designation' => 'nullable|integer',
            'filter_emplacement' => 'nullable|integer',
            'filter_categorie' => 'nullable|integer',
            'filter_etat' => 'nullable|integer',
            'filter_natjur' => 'nullable|integer',
            'filter_sf' => 'nullable|integer',
            'filter_date_acquisition' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
        ]);

        $query = CorbeilleImmobilisation::query()->orderByDesc('id');

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('designation_label', 'like', '%' . $search . '%')
                    ->orWhere('emplacement_label', 'like', '%' . $search . '%')
                    ->orWhere('original_num_ordre', 'like', '%' . $search . '%')
                    ->orWhere('idDesignation', 'like', '%' . $search . '%')
                    ->orWhere('idCategorie', 'like', '%' . $search . '%')
                    ->orWhere('idEtat', 'like', '%' . $search . '%')
                    ->orWhere('idEmplacement', 'like', '%' . $search . '%')
                    ->orWhere('idNatJur', 'like', '%' . $search . '%')
                    ->orWhere('idSF', 'like', '%' . $search . '%')
                    ->orWhere('Observations', 'like', '%' . $search . '%');
            });
        }

        if (!empty($validated['filter_designation'])) {
            $query->where('idDesignation', (int) $validated['filter_designation']);
        }

        if (!empty($validated['filter_emplacement'])) {
            $query->where('idEmplacement', (int) $validated['filter_emplacement']);
        }

        if (!empty($validated['filter_categorie'])) {
            $query->where('idCategorie', (int) $validated['filter_categorie']);
        }

        if (!empty($validated['filter_etat'])) {
            $query->where('idEtat', (int) $validated['filter_etat']);
        }

        if (!empty($validated['filter_natjur'])) {
            $query->where('idNatJur', (int) $validated['filter_natjur']);
        }

        if (!empty($validated['filter_sf'])) {
            $query->where('idSF', (int) $validated['filter_sf']);
        }

        if (!empty($validated['filter_date_acquisition'])) {
            $year = (int) $validated['filter_date_acquisition'];
            $query->whereYear('DateAcquisition', $year);
        }

        $rows = $query->paginate(25)->withQueryString();

        $designationMap = Designation::query()->pluck('designation', 'id');
        $designationCodeMap = Designation::query()->pluck('CodeDesignation', 'id');
        $categorieMap = Categorie::query()->pluck('Categorie', 'idCategorie');
        $categorieCodeMap = Categorie::query()->pluck('CodeCategorie', 'idCategorie');
        $etatMap = Etat::query()->pluck('Etat', 'idEtat');
        $natureJuridiqueCodeMap = NatureJuridique::query()->pluck('CodeNatJur', 'idNatJur');
        $sourceFinancementCodeMap = SourceFinancement::query()->pluck('CodeSourceFin', 'idSF');

        $designationOptionsRows = DB::table('corbeille_immobilisations')
            ->select('idDesignation', DB::raw('MAX(designation_label) as designation_label'))
            ->groupBy('idDesignation')
            ->orderBy('idDesignation')
            ->get();

        $designationOptions = $designationOptionsRows->map(function ($row) use ($designationMap) {
            $label = $designationMap[$row->idDesignation] ?? ($row->designation_label ?: ('Designation ' . $row->idDesignation));
            return [
                'id' => (int) $row->idDesignation,
                'label' => $label,
            ];
        })->values();

        $emplacementOptionsRows = DB::table('corbeille_immobilisations')
            ->select('idEmplacement', DB::raw('MAX(emplacement_label) as emplacement_label'))
            ->groupBy('idEmplacement')
            ->orderBy('idEmplacement')
            ->get();

        $emplacementMap = Emplacement::query()->pluck('Emplacement', 'idEmplacement');
        $emplacementOptions = $emplacementOptionsRows->map(function ($row) use ($emplacementMap) {
            $label = $emplacementMap[$row->idEmplacement] ?? ($row->emplacement_label ?: ('Emplacement ' . $row->idEmplacement));
            return [
                'id' => (int) $row->idEmplacement,
                'label' => $label,
            ];
        })->values();

        $categorieOptions = Categorie::query()
            ->select('idCategorie', 'Categorie')
            ->orderBy('Categorie')
            ->get()
            ->map(fn ($row) => ['id' => (int) $row->idCategorie, 'label' => $row->Categorie])
            ->values();

        $etatOptions = Etat::query()
            ->select('idEtat', 'Etat')
            ->orderBy('Etat')
            ->get()
            ->map(fn ($row) => ['id' => (int) $row->idEtat, 'label' => $row->Etat])
            ->values();

        $natJurOptions = NatureJuridique::query()
            ->select('idNatJur', 'NatJur')
            ->orderBy('NatJur')
            ->get()
            ->map(fn ($row) => ['id' => (int) $row->idNatJur, 'label' => $row->NatJur])
            ->values();

        $sourceFinOptions = SourceFinancement::query()
            ->select('idSF', 'SourceFin')
            ->orderBy('SourceFin')
            ->get()
            ->map(fn ($row) => ['id' => (int) $row->idSF, 'label' => $row->SourceFin])
            ->values();

        $emplacementIds = $rows->getCollection()->pluck('idEmplacement')->unique()->filter()->values();
        $emplacements = Emplacement::with(['localisation', 'affectation'])
            ->whereIn('idEmplacement', $emplacementIds)
            ->get()
            ->keyBy('idEmplacement');

        $rows->getCollection()->transform(function (CorbeilleImmobilisation $row) use (
            $designationMap,
            $designationCodeMap,
            $categorieMap,
            $categorieCodeMap,
            $etatMap,
            $natureJuridiqueCodeMap,
            $sourceFinancementCodeMap,
            $emplacements
        ) {
            $emplacement = $emplacements->get($row->idEmplacement);
            $annee = '';
            if (!empty($row->DateAcquisition)) {
                if ($row->DateAcquisition instanceof \DateTimeInterface) {
                    $annee = $row->DateAcquisition->format('Y');
                } elseif (is_numeric($row->DateAcquisition)) {
                    $anneeInt = (int) $row->DateAcquisition;
                    $annee = $anneeInt > 1970 ? (string) $anneeInt : '';
                } elseif (is_string($row->DateAcquisition)) {
                    $yearPart = (int) substr($row->DateAcquisition, 0, 4);
                    $annee = $yearPart > 1970 ? (string) $yearPart : '';
                }
            }

            $row->designation_display = $designationMap[$row->idDesignation] ?? ($row->designation_label ?: 'N/A');
            $row->categorie_display = $categorieMap[$row->idCategorie] ?? 'N/A';
            $row->etat_display = $etatMap[$row->idEtat] ?? 'N/A';
            $row->emplacement_display = $emplacement?->Emplacement ?? ($row->emplacement_label ?: 'N/A');
            $row->affectation_display = $emplacement?->affectation?->Affectation ?? $row->affectation_label;
            $row->localisation_display = $emplacement?->localisation?->Localisation ?? $row->localisation_label;
            $row->code_display = sprintf(
                '%s/%s/%s/%s/%s/%s',
                $natureJuridiqueCodeMap[$row->idNatJur] ?? '',
                $designationCodeMap[$row->idDesignation] ?? '',
                $categorieCodeMap[$row->idCategorie] ?? '',
                $annee,
                $sourceFinancementCodeMap[$row->idSF] ?? '',
                $row->original_num_ordre
            );

            return $row;
        });

        return view('corbeille.immobilisations.index', [
            'rows' => $rows,
            'search' => $validated['search'] ?? '',
            'filterDesignation' => $validated['filter_designation'] ?? '',
            'filterEmplacement' => $validated['filter_emplacement'] ?? '',
            'filterCategorie' => $validated['filter_categorie'] ?? '',
            'filterEtat' => $validated['filter_etat'] ?? '',
            'filterNatJur' => $validated['filter_natjur'] ?? '',
            'filterSF' => $validated['filter_sf'] ?? '',
            'filterDateAcquisition' => $validated['filter_date_acquisition'] ?? '',
            'designationOptions' => $designationOptions,
            'emplacementOptions' => $emplacementOptions,
            'categorieOptions' => $categorieOptions,
            'etatOptions' => $etatOptions,
            'natJurOptions' => $natJurOptions,
            'sourceFinOptions' => $sourceFinOptions,
        ]);
    }

    public function restore(int $corbeilleId): RedirectResponse
    {
        $item = CorbeilleImmobilisation::find($corbeilleId);

        if (!$item) {
            return back()->with('error', 'Element introuvable dans la corbeille.');
        }

        if (Gesimmo::where('NumOrdre', $item->original_num_ordre)->exists()) {
            return back()->with('error', 'Restauration impossible: NumOrdre deja utilise.');
        }

        try {
            DB::transaction(function () use ($item): void {
                $this->restoreItemFromTrash($item);
            });

            return back()->with('success', 'Immobilisation restauree avec succes.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', "Restauration impossible: {$e->getMessage()}");
        }
    }

    public function restoreByDesignation(int $designationId): RedirectResponse
    {
        $items = CorbeilleImmobilisation::query()
            ->where('idDesignation', $designationId)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Aucune immobilisation en corbeille pour cette designation.');
        }

        $restored = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($items as $item) {
            if (Gesimmo::where('NumOrdre', $item->original_num_ordre)->exists()) {
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($item): void {
                    $this->restoreItemFromTrash($item);
                });
                $restored++;
            } catch (\Throwable $e) {
                report($e);
                $failed++;
            }
        }

        if ($restored === 0) {
            return back()->with('error', "Aucune restauration effectuee (ignores: {$skipped}, echecs: {$failed}).");
        }

        return back()->with('success', "Restauration par designation terminee: {$restored} restauree(s), {$skipped} ignoree(s), {$failed} en echec.");
    }

    public function restoreByDesignationSelection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'designation_id' => 'required|integer',
        ]);

        return $this->restoreByDesignation((int) $validated['designation_id']);
    }

    public function restoreByEmplacement(int $emplacementId): RedirectResponse
    {
        $items = CorbeilleImmobilisation::query()
            ->where('idEmplacement', $emplacementId)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Aucune immobilisation en corbeille pour cet emplacement.');
        }

        $restored = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($items as $item) {
            if (Gesimmo::where('NumOrdre', $item->original_num_ordre)->exists()) {
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($item): void {
                    $this->restoreItemFromTrash($item);
                });
                $restored++;
            } catch (\Throwable $e) {
                report($e);
                $failed++;
            }
        }

        if ($restored === 0) {
            return back()->with('error', "Aucune restauration effectuee (ignores: {$skipped}, echecs: {$failed}).");
        }

        return back()->with('success', "Restauration par emplacement terminee: {$restored} restauree(s), {$skipped} ignoree(s), {$failed} en echec.");
    }

    public function restoreByEmplacementSelection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'emplacement_id' => 'required|integer',
        ]);

        return $this->restoreByEmplacement((int) $validated['emplacement_id']);
    }

    private function restoreItemFromTrash(CorbeilleImmobilisation $item): void
    {
        $dateAcquisitionYear = null;
        if (!empty($item->DateAcquisition)) {
            if ($item->DateAcquisition instanceof \DateTimeInterface) {
                $dateAcquisitionYear = (int) $item->DateAcquisition->format('Y');
            } elseif (is_numeric($item->DateAcquisition)) {
                $dateAcquisitionYear = (int) $item->DateAcquisition;
            } elseif (is_string($item->DateAcquisition)) {
                $dateAcquisitionYear = (int) substr($item->DateAcquisition, 0, 4);
            }
        }

        if ($dateAcquisitionYear !== null && ($dateAcquisitionYear < 1900 || $dateAcquisitionYear > 9999)) {
            $dateAcquisitionYear = null;
        }

        // Compatibilite: la colonne peut etre de type DATE ou entier selon l'environnement.
        // En envoyant YYYY-01-01, MySQL DATE conserve l'annee et un entier convertit en YYYY.
        $dateAcquisitionForInsert = $dateAcquisitionYear !== null
            ? sprintf('%04d-01-01', $dateAcquisitionYear)
            : null;

        if (!Emplacement::where('idEmplacement', $item->idEmplacement)->exists()) {
            $localisationId = $item->emplacement_id_localisation;

            if (empty($localisationId)) {
                $localisationId = LocalisationImmo::query()->value('idLocalisation');
            }

            if (empty($localisationId)) {
                $localisationId = DB::table('localisation')->insertGetId([
                    'Localisation' => $item->localisation_label ?: 'Localisation corbeille',
                    'CodeLocalisation' => null,
                ]);
            } elseif (!LocalisationImmo::where('idLocalisation', $localisationId)->exists()) {
                DB::table('localisation')->insert([
                    'idLocalisation' => $localisationId,
                    'Localisation' => $item->localisation_label ?: ('Localisation ' . $localisationId),
                    'CodeLocalisation' => null,
                ]);
            }

            $affectationId = $item->emplacement_id_affectation;

            if (empty($affectationId)) {
                $affectationId = Affectation::query()->value('idAffectation');
            }

            if (empty($affectationId)) {
                $affectationId = DB::table('affectation')->insertGetId([
                    'Affectation' => $item->affectation_label ?: 'Affectation corbeille',
                    'CodeAffectation' => null,
                    'idLocalisation' => $localisationId,
                ]);
            } elseif (!Affectation::where('idAffectation', $affectationId)->exists()) {
                DB::table('affectation')->insert([
                    'idAffectation' => $affectationId,
                    'Affectation' => $item->affectation_label ?: ('Affectation ' . $affectationId),
                    'CodeAffectation' => null,
                    'idLocalisation' => $localisationId,
                ]);
            }

            DB::table('emplacement')->insert([
                'idEmplacement' => $item->idEmplacement,
                'Emplacement' => $item->emplacement_label ?: ('Emplacement ' . $item->idEmplacement),
                'CodeEmplacement' => $item->emplacement_code,
                'idAffectation' => $affectationId,
                'idLocalisation' => $localisationId,
            ]);
        }

        $categorieId = $item->idCategorie;
        if (!Categorie::where('idCategorie', $categorieId)->exists()) {
            $categorieId = Categorie::query()->value('idCategorie');
        }

        $etatId = $item->idEtat;
        if (!Etat::where('idEtat', $etatId)->exists()) {
            $etatId = Etat::query()->value('idEtat');
        }

        $natJurId = $item->idNatJur;
        if (!NatureJuridique::where('idNatJur', $natJurId)->exists()) {
            $natJurId = NatureJuridique::query()->value('idNatJur');
        }

        $sourceFinId = $item->idSF;
        if (!SourceFinancement::where('idSF', $sourceFinId)->exists()) {
            $sourceFinId = SourceFinancement::query()->value('idSF');
        }

        if (empty($categorieId) || empty($etatId) || empty($natJurId) || empty($sourceFinId)) {
            throw new \RuntimeException('Impossible de restaurer: tables de reference manquantes (categorie/etat/nature juridique/source financement).');
        }

        // Re-creer la designation si elle a ete supprimee.
        if (!Designation::where('id', $item->idDesignation)->exists()) {
            DB::table('designation')->insert([
                'id' => $item->idDesignation,
                'designation' => $item->designation_label ?: ('Designation ' . $item->idDesignation),
                'CodeDesignation' => null,
                'idCat' => $categorieId,
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
            'DateAcquisition' => $dateAcquisitionForInsert,
            'Observations' => $item->Observations,
        ]);

        if (!empty($item->barcode)) {
            Code::create([
                'idGesimmo' => $item->original_num_ordre,
                'barcode' => $item->barcode,
            ]);
        }

        $item->delete();
    }

    public function forceDelete(int $corbeilleId): RedirectResponse
    {
        $item = CorbeilleImmobilisation::find($corbeilleId);

        if (!$item) {
            return back()->with('error', 'Element introuvable dans la corbeille.');
        }

        $item->delete();

        return back()->with('success', 'Suppression definitive effectuee.');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $filename = 'corbeille_immobilisations_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new CorbeilleImmobilisationsExport($validated['search'] ?? null),
            $filename
        );
    }
}
