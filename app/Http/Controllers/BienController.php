<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Http\Requests\StoreBienRequest;
use App\Http\Requests\UpdateBienRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BienController extends Controller
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
    public function store(StoreBienRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bien $bien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bien $bien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBienRequest $request, Bien $bien)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bien $bien)
    {
        //
    }

    /**
     * Génère le QR code d'un bien
     * 
     * @param Bien $bien
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateQRCode(Bien $bien)
    {
        try {
            $path = $bien->generateQRCode();
            
            return redirect()->back()->with('success', 'QR code généré avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du QR code: ' . $e->getMessage());
        }
    }

    /**
     * Télécharge l'étiquette PDF d'un bien
     * 
     * Format : 8x5 cm
     * Contenu : QR code, code inventaire, désignation, localisation, date acquisition
     * 
     * @param Bien $bien
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadEtiquette(Bien $bien)
    {
        try {
            // Vérifier que le bien a un QR code, sinon le générer
            if (!$bien->qr_code_path || !Storage::disk('public')->exists($bien->qr_code_path)) {
                try {
                    $bien->generateQRCode();
                    $bien->refresh();
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Impossible de générer le QR code: ' . $e->getMessage());
                }
            }

            // Charger les relations nécessaires
            $bien->load('localisation');

            // Générer le PDF avec les nouvelles dimensions 70x37mm
            // 70mm = 198.43 points, 37mm = 104.88 points (1 mm = 2.83465 points)
            $pdf = Pdf::loadView('pdf.etiquette-bien', [
                'bien' => $bien,
            ])->setPaper([0, 0, 198.43, 104.88], 'portrait'); // 70x37mm en points

            $filename = 'etiquette_' . Str::slug($bien->code_inventaire) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de l\'étiquette: ' . $e->getMessage());
        }
    }

    /**
     * Imprime en masse les étiquettes de plusieurs biens
     * 
     * Disposition : 12 étiquettes par page A4 (3 colonnes x 4 lignes)
     * Marges : 10mm, Espacement : 5mm entre étiquettes
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function imprimerEtiquettes(Request $request)
    {
        try {
            $request->validate([
                'biens' => 'required|array|min:1',
                'biens.*' => 'exists:biens,id',
            ]);

            $bienIds = $request->input('biens', []);
            
            // Récupérer les biens avec leurs relations
            $biens = Bien::whereIn('id', $bienIds)
                ->with('localisation')
                ->get();

            if ($biens->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun bien sélectionné.');
            }

            // Générer les QR codes manquants
            foreach ($biens as $bien) {
                if (!$bien->qr_code_path || !Storage::disk('public')->exists($bien->qr_code_path)) {
                    try {
                        $bien->generateQRCode();
                        $bien->refresh();
                    } catch (\Exception $e) {
                        // Logger l'erreur mais continuer avec les autres biens
                        \Illuminate\Support\Facades\Log::warning("Impossible de générer le QR code pour le bien {$bien->code_inventaire}: " . $e->getMessage());
                    }
                }
            }

            // Générer le PDF multi-pages
            $pdf = Pdf::loadView('pdf.etiquettes-biens', [
                'biens' => $biens,
            ])->setPaper('a4', 'portrait');

            $filename = 'etiquettes_' . now()->format('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'impression des étiquettes: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les biens en format Excel (CSV)
     * 
     * Colonnes : Code, Désignation, Nature, Localisation, Service, Valeur, État, Date acquisition
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            // Récupérer les IDs sélectionnés si fournis
            $ids = $request->input('ids');
            
            // Récupérer tous les biens avec relations
            $query = Bien::with(['localisation', 'user']);
            
            if ($ids) {
                $idsArray = is_array($ids) ? $ids : explode(',', $ids);
                $query->whereIn('id', $idsArray);
            }
            
            $biens = $query->get();

            $filename = 'biens_' . now()->format('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Ajouter BOM pour Excel UTF-8
            $callback = function() use ($biens) {
                $file = fopen('php://output', 'w');
                
                // Ajouter BOM UTF-8 pour Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // En-têtes
                fputcsv($file, [
                    'Code Inventaire',
                    'Désignation',
                    'Nature',
                    'Localisation',
                    'Service Usager',
                    'Valeur Acquisition (MRU)',
                    'État',
                    'Date Acquisition',
                    'Date Création',
                ], ';');

                // Données
                foreach ($biens as $bien) {
                    fputcsv($file, [
                        $bien->code_inventaire,
                        $bien->designation,
                        $bien->nature,
                        $bien->localisation ? $bien->localisation->code . ' - ' . $bien->localisation->designation : 'N/A',
                        $bien->service_usager,
                        number_format($bien->valeur_acquisition, 2, ',', ' '),
                        $bien->etat,
                        $bien->date_acquisition->format('d/m/Y'),
                        $bien->created_at->format('d/m/Y H:i'),
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les biens en format PDF
     * 
     * Génère une liste complète formatée avec tableau, en-tête et pied de page
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportPDF(Request $request)
    {
        try {
            // Récupérer tous les biens avec relations
            $biens = Bien::with(['localisation', 'user'])
                ->orderBy('code_inventaire')
                ->get();

            if ($biens->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucun bien à exporter.');
            }

            // Générer le PDF
            $pdf = Pdf::loadView('pdf.liste-biens', [
                'biens' => $biens,
                'date' => now(),
            ])->setPaper('a4', 'landscape');

            $filename = 'liste_biens_' . now()->format('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }
}
