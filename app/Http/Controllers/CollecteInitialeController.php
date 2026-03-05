<?php

namespace App\Http\Controllers;

use App\Exports\CollecteInitialeExport;
use App\Models\CollecteBienInitiale;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CollecteInitialeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'emplacement' => 'nullable|string|max:255',
            'lot_uid' => 'nullable|string|max:255',
        ]);

        $query = CollecteBienInitiale::query()->latest('id');

        if (!empty($filters['emplacement'])) {
            $query->where('emplacement_label', 'like', '%' . $filters['emplacement'] . '%');
        }

        if (!empty($filters['lot_uid'])) {
            $query->where('lot_uid', $filters['lot_uid']);
        }

        $rows = $query->paginate(25)->withQueryString();

        return view('collecte-initiale.index', [
            'rows' => $rows,
            'filters' => $filters,
        ]);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            'emplacement' => 'nullable|string|max:255',
            'lot_uid' => 'nullable|string|max:255',
        ]);

        $filename = 'collecte_initiale_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new CollecteInitialeExport(
                $filters['emplacement'] ?? null,
                $filters['lot_uid'] ?? null
            ),
            $filename
        );
    }
}
