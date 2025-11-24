<?php

namespace App\Http\Controllers;

use App\Models\Localisation;
use App\Http\Requests\StoreLocalisationRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalisationController extends Controller
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
    public function store(StoreLocalisationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Localisation $localisation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Localisation $localisation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Localisation $localisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Localisation $localisation)
    {
        //
    }

    /**
     * Génère le QR code d'une localisation
     * 
     * @param Localisation $localisation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateQRCode(Localisation $localisation)
    {
        try {
            $path = $localisation->generateQRCode();
            
            return redirect()->back()->with('success', 'QR code généré avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du QR code: ' . $e->getMessage());
        }
    }

    /**
     * Télécharge l'étiquette PDF d'une localisation
     * 
     * Format : 10x8 cm (plus grande pour affichage sur porte/mur)
     * Contenu : QR code (6x6 cm), code, désignation, bâtiment, étage
     * 
     * @param Localisation $localisation
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function downloadEtiquette(Localisation $localisation)
    {
        try {
            // Vérifier que la localisation a un QR code, sinon le générer
            if (!$localisation->qr_code_path || !Storage::disk('public')->exists($localisation->qr_code_path)) {
                $localisation->generateQRCode();
                $localisation->refresh();
            }

            // Générer le PDF
            $pdf = Pdf::loadView('pdf.etiquette-localisation', [
                'localisation' => $localisation,
            ])->setPaper([0, 0, 283.46, 226.77], 'portrait'); // 10x8 cm en points

            $filename = 'etiquette_' . Str::slug($localisation->code) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération de l\'étiquette: ' . $e->getMessage());
        }
    }

    /**
     * Imprime en masse les étiquettes de plusieurs localisations
     * 
     * Disposition : 4 étiquettes par page A4 (2 colonnes x 2 lignes)
     * Marges : 10mm, Espacement : 5mm entre étiquettes
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function imprimerEtiquettes(Request $request)
    {
        try {
            $request->validate([
                'localisations' => 'required|array|min:1',
                'localisations.*' => 'exists:localisations,id',
            ]);

            $localisationIds = $request->input('localisations', []);
            
            // Récupérer les localisations
            $localisations = Localisation::whereIn('id', $localisationIds)->get();

            if ($localisations->isEmpty()) {
                return redirect()->back()->with('error', 'Aucune localisation sélectionnée.');
            }

            // Générer les QR codes manquants
            foreach ($localisations as $localisation) {
                if (!$localisation->qr_code_path || !Storage::disk('public')->exists($localisation->qr_code_path)) {
                    $localisation->generateQRCode();
                    $localisation->refresh();
                }
            }

            // Générer le PDF multi-pages
            $pdf = Pdf::loadView('pdf.etiquettes-localisations', [
                'localisations' => $localisations,
            ])->setPaper('a4', 'portrait');

            $filename = 'etiquettes_localisations_' . now()->format('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'impression des étiquettes: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les localisations en format Excel (CSV)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Request $request)
    {
        try {
            // Récupérer les IDs sélectionnés depuis la requête (si fournis)
            $ids = $request->input('ids');
            
            if ($ids) {
                // Convertir la chaîne d'IDs séparés par des virgules en tableau
                $idsArray = explode(',', $ids);
                $localisations = Localisation::whereIn('id', $idsArray)->get();
            } else {
                // Exporter toutes les localisations
                $localisations = Localisation::all();
            }

            if ($localisations->isEmpty()) {
                return redirect()->back()->with('warning', 'Aucune localisation à exporter.');
            }

            // Nom du fichier
            $filename = 'localisations_' . now()->format('Y-m-d_His') . '.csv';

            // En-têtes HTTP pour forcer le téléchargement
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            // Callback pour générer le CSV
            $callback = function() use ($localisations) {
                $file = fopen('php://output', 'w');
                
                // Ajouter le BOM UTF-8 pour Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // En-têtes de colonnes
                fputcsv($file, [
                    'Code',
                    'Désignation',
                    'Bâtiment',
                    'Étage',
                    'Service rattaché',
                    'Responsable',
                    'Actif',
                    'Nombre de biens',
                    'Date de création',
                ], ';');

                // Données
                foreach ($localisations as $localisation) {
                    fputcsv($file, [
                        $localisation->code,
                        $localisation->designation,
                        $localisation->batiment ?? '',
                        $localisation->etage ?? '',
                        $localisation->service_rattache ?? '',
                        $localisation->responsable ?? '',
                        $localisation->actif ? 'Oui' : 'Non',
                        $localisation->biens()->count(),
                        $localisation->created_at->format('d/m/Y H:i'),
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }
}
