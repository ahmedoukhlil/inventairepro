<?php

namespace App\Http\Controllers;

use App\Models\Gesimmo;
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
     * Génère le code-barres Code 128 d'une immobilisation
     * 
     * @param Gesimmo $bien
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateQRCode(Gesimmo $bien)
    {
        try {
            $barcode = $bien->generateBarcode();
            
            return redirect()->back()->with('success', 'Code-barres généré avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du code-barres: ' . $e->getMessage());
        }
    }


    /**
     * Génère l'étiquette PDF avec un code-barres SVG fourni côté client
     * 
     * @param Gesimmo $bien
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadEtiquetteWithBarcode(Gesimmo $bien, \Illuminate\Http\Request $request)
    {
        try {
            $request->validate([
                'svg' => 'required|string',
            ]);

            // Charger les relations nécessaires
            $bien->load(['emplacement.localisation', 'designation', 'categorie', 'etat', 'code']);

            // Générer le PDF avec le SVG fourni
            // Dimensions Code 128 standard : 50mm × 20mm (minimum 37.3mm × 12.7mm)
            // 50mm = 141.73 points, 20mm = 56.69 points (1 mm = 2.83465 points)
            $pdf = Pdf::loadView('pdf.etiquette-bien', [
                'bien' => $bien,
                'barcodeSvg' => $request->input('svg'), // SVG généré côté client
            ])->setPaper([0, 0, 141.73, 56.69], 'portrait')
              ->setOption('isHtml5ParserEnabled', true)
              ->setOption('isRemoteEnabled', true)
              ->setOption('isPhpEnabled', true)
              ->setOption('defaultFont', 'DejaVu Sans')
              ->setOption('enableFontSubsetting', true)
              ->setOption('isFontSubsettingEnabled', true);

            $filename = 'etiquette_' . \Illuminate\Support\Str::slug($bien->code_formate ?? $bien->NumOrdre) . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la génération de l\'étiquette: ' . $e->getMessage()
            ], 500);
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
    public function downloadEtiquette(Gesimmo $bien)
    {
        try {
            // Charger les relations nécessaires (sans 'code' car généré côté client)
            $bien->load(['emplacement.localisation', 'designation', 'categorie', 'etat']);

            // Générer le PDF avec les dimensions Code 128 standard
            // Dimensions minimales : 37.3mm × 12.7mm, on utilise 50mm × 20mm pour l'étiquette
            // 50mm = 141.73 points, 20mm = 56.69 points (1 mm = 2.83465 points)
            $pdf = Pdf::loadView('pdf.etiquette-bien', [
                'bien' => $bien,
            ])->setPaper([0, 0, 141.73, 56.69], 'portrait')
              ->setOption('isHtml5ParserEnabled', true)
              ->setOption('isRemoteEnabled', true)
              ->setOption('isPhpEnabled', true)
              ->setOption('defaultFont', 'DejaVu Sans')
              ->setOption('enableFontSubsetting', true)
              ->setOption('isFontSubsettingEnabled', true); // 8cm x 5cm en points

            $filename = 'etiquette_' . Str::slug($bien->code_formate ?? $bien->NumOrdre) . '.pdf';

            // Retourner le PDF en stream pour l'impression (au lieu de download)
            return $pdf->stream($filename);
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
                'biens.*' => 'exists:gesimmo,NumOrdre',
            ]);

            $bienIds = $request->input('biens', []);
            
            // Récupérer les immobilisations avec leurs relations (sans 'code' car généré côté client)
            $biens = Gesimmo::whereIn('NumOrdre', $bienIds)
                ->with(['emplacement.localisation', 'designation', 'categorie', 'etat'])
                ->get();

            if ($biens->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun bien sélectionné.');
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
     * Affiche la page pour imprimer les étiquettes groupées par emplacement
     * 
     * Disposition : 21 étiquettes par page A4 (3 colonnes x 7 lignes)
     * Format : Groupé par emplacement, génération côté client avec jsbarcode et pdf-lib
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function imprimerEtiquettesParEmplacement(Request $request)
    {
        try {
            $request->validate([
                'idEmplacement' => 'required|exists:emplacement,idEmplacement',
            ]);

            $idEmplacement = $request->input('idEmplacement');
            
            // Récupérer toutes les immobilisations de cet emplacement avec leurs relations
            $biens = Gesimmo::where('idEmplacement', $idEmplacement)
                ->with([
                    'emplacement.localisation',
                    'emplacement.affectation',
                    'designation',
                    'categorie',
                    'etat',
                    'natureJuridique',
                    'sourceFinancement'
                ])
                ->orderBy('NumOrdre')
                ->get();

            if ($biens->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun bien trouvé pour cet emplacement.');
            }

            // Récupérer les informations de l'emplacement
            $emplacement = \App\Models\Emplacement::with(['localisation', 'affectation'])
                ->find($idEmplacement);

            // Préparer les données pour le JavaScript
            $biensData = $biens->map(function($bien) {
                return [
                    'NumOrdre' => $bien->NumOrdre,
                    'code_formate' => $bien->code_formate ?? $bien->NumOrdre,
                    // Pour le code-barres, on utilise uniquement NumOrdre
                    'barcode_value' => (string)$bien->NumOrdre,
                ];
            })->toArray();

            return view('pdf.etiquettes-biens-par-emplacement-client', [
                'biens' => $biens,
                'biensData' => $biensData,
                'emplacement' => $emplacement,
            ]);
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
            
            // Récupérer toutes les immobilisations avec relations
            $query = Gesimmo::with([
                'designation.categorie',
                'categorie',
                'etat',
                'emplacement.localisation',
                'emplacement.affectation',
                'natureJuridique',
                'sourceFinancement',
            ]);
            
            if ($ids) {
                $idsArray = is_array($ids) ? $ids : explode(',', $ids);
                $query->whereIn('NumOrdre', $idsArray);
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
                    'NumOrdre',
                    'Code',
                    'Désignation',
                    'Catégorie',
                    'État',
                    'Emplacement',
                    'Localisation',
                    'Nature Juridique',
                    'Source Financement',
                    'Date Acquisition',
                    'Observations',
                ], ';');

                // Données
                foreach ($biens as $bien) {
                    fputcsv($file, [
                        $bien->NumOrdre,
                        $bien->code_formate ?? '',
                        $bien->designation ? $bien->designation->designation : 'N/A',
                        $bien->categorie ? $bien->categorie->Categorie : 'N/A',
                        $bien->etat ? $bien->etat->Etat : 'N/A',
                        $bien->emplacement ? $bien->emplacement->Emplacement : 'N/A',
                        $bien->emplacement && $bien->emplacement->localisation ? $bien->emplacement->localisation->Localisation : 'N/A',
                        $bien->natureJuridique ? $bien->natureJuridique->NatJur : 'N/A',
                        $bien->sourceFinancement ? $bien->sourceFinancement->SourceFin : 'N/A',
                        $bien->DateAcquisition ? $bien->DateAcquisition->format('d/m/Y') : '',
                        $bien->Observations ?? '',
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
            // Récupérer toutes les immobilisations avec relations
            $biens = Gesimmo::with([
                'designation.categorie',
                'categorie',
                'etat',
                'emplacement.localisation',
                'emplacement.affectation',
                'natureJuridique',
                'sourceFinancement',
            ])
                ->orderBy('NumOrdre')
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
