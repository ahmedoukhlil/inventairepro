<?php

namespace App\Http\Controllers;

use App\Exports\CorbeilleImmobilisationsExport;
use App\Models\Code;
use App\Models\CorbeilleImmobilisation;
use App\Models\Designation;
use App\Models\Gesimmo;
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
        ]);

        $query = CorbeilleImmobilisation::query()->orderByDesc('id');

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('designation_label', 'like', '%' . $search . '%')
                    ->orWhere('original_num_ordre', 'like', '%' . $search . '%')
                    ->orWhere('idDesignation', 'like', '%' . $search . '%');
            });
        }

        return view('corbeille.immobilisations.index', [
            'rows' => $query->paginate(25)->withQueryString(),
            'search' => $validated['search'] ?? '',
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
                // Re-creer la designation si elle a ete supprimee.
                if (!Designation::where('id', $item->idDesignation)->exists()) {
                    DB::table('designation')->insert([
                        'id' => $item->idDesignation,
                        'designation' => $item->designation_label ?: ('Designation ' . $item->idDesignation),
                        'CodeDesignation' => null,
                        'idCat' => $item->idCategorie,
                    ]);
                }

                DB::table('gesimmo')->insert([
                    'NumOrdre' => $item->original_num_ordre,
                    'idDesignation' => $item->idDesignation,
                    'idCategorie' => $item->idCategorie,
                    'idEtat' => $item->idEtat,
                    'idEmplacement' => $item->idEmplacement,
                    'idNatJur' => $item->idNatJur,
                    'idSF' => $item->idSF,
                    'DateAcquisition' => $item->DateAcquisition,
                    'Observations' => $item->Observations,
                ]);

                if (!empty($item->barcode)) {
                    Code::create([
                        'idGesimmo' => $item->original_num_ordre,
                        'barcode' => $item->barcode,
                    ]);
                }

                $item->delete();
            });

            return back()->with('success', 'Immobilisation restauree avec succes.');
        } catch (\Throwable $e) {
            return back()->with('error', "Restauration impossible: {$e->getMessage()}");
        }
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
