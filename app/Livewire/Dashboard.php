<?php

namespace App\Livewire;

use App\Models\Bien;
use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Models\InventaireScan;
use App\Models\Localisation;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $totalBiens = 0;
    public $totalLocalisations = 0;
    public $inventaireEnCours = null;
    public $valeurTotale = 0;
    public $dernieresActions = [];
    public $statistiquesInventaire = [];
    public $localisationsInventaire = [];
    public $repartitionStatuts = [];
    public $progressionParService = [];
    public $biensCetteAnnee = 0;
    public $nombreBatiments = 0;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function refresh()
    {
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        try {
            // Total immobilisations (gesimmo)
            $this->totalBiens = \App\Models\Gesimmo::count();
            
            // Biens créés cette année (basé sur l'année d'acquisition > année en cours - 1)
            $this->biensCetteAnnee = \App\Models\Gesimmo::where('DateAcquisition', '>=', now()->year)->count();
            
            // Total localisations actives
            $this->totalLocalisations = \App\Models\Localisation::count();
            
            // Nombre de bâtiments uniques (comptage des localisations distinctes)
            $this->nombreBatiments = \App\Models\Localisation::distinct('CodeLocalisation')->count();
            
            // Valeur totale du parc (on ne dispose pas de cette info dans gesimmo, donc on laisse à 0)
            $this->valeurTotale = 0;
            
            // Inventaire en cours
            $this->inventaireEnCours = Inventaire::where(function($query) {
                $query->where('statut', 'en_cours')
                      ->orWhere('statut', 'en_preparation');
            })
            ->orderBy('annee', 'desc')
            ->first();
            
            // Charger les statistiques de l'inventaire en cours
            if ($this->inventaireEnCours) {
                $this->loadInventaireStats();
            }
            
            // Dernières actions
            $this->loadRecentActions();
        } catch (\Exception $e) {
            // En cas d'erreur, initialiser avec des valeurs par défaut
            \Log::error('Erreur lors du chargement des statistiques du dashboard: ' . $e->getMessage());
            $this->totalBiens = 0;
            $this->totalLocalisations = 0;
            $this->valeurTotale = 0;
            $this->biensCetteAnnee = 0;
            $this->nombreBatiments = 0;
        }
    }

    private function loadInventaireStats()
    {
        if (!$this->inventaireEnCours) {
            return;
        }

        // Localisations de l'inventaire
        $this->localisationsInventaire = InventaireLocalisation::where('inventaire_id', $this->inventaireEnCours->id)
            ->with(['localisation', 'agent'])
            ->orderBy('date_debut_scan', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'localisation' => $item->localisation->full_name ?? 'N/A',
                    'biens_attendus' => $item->nombre_biens_attendus,
                    'biens_scannes' => $item->nombre_biens_scannes,
                    'progression' => $item->progression,
                    'statut' => $item->statut,
                    'agent' => $item->agent->name ?? 'Non assigné',
                ];
            })
            ->toArray();

        // Répartition des statuts de scans
        $scans = InventaireScan::where('inventaire_id', $this->inventaireEnCours->id)->get();
        $this->repartitionStatuts = [
            'present' => $scans->where('statut_scan', 'present')->count(),
            'deplace' => $scans->where('statut_scan', 'deplace')->count(),
            'absent' => $scans->where('statut_scan', 'absent')->count(),
            'deteriore' => $scans->where('statut_scan', 'deteriore')->count(),
        ];

        // Progression par service
        $localisations = InventaireLocalisation::where('inventaire_id', $this->inventaireEnCours->id)
            ->with('localisation')
            ->get();
        
        $services = [];
        foreach ($localisations as $invLoc) {
            $service = $invLoc->localisation->service_rattache ?? 'Autre';
            if (!isset($services[$service])) {
                $services[$service] = ['total' => 0, 'termine' => 0];
            }
            $services[$service]['total']++;
            if ($invLoc->statut === 'termine') {
                $services[$service]['termine']++;
            }
        }

        $this->progressionParService = collect($services)->map(function ($data, $service) {
            return [
                'service' => $service,
                'progression' => $data['total'] > 0 ? round(($data['termine'] / $data['total']) * 100, 1) : 0,
            ];
        })->values()->toArray();

        // Statistiques globales
        try {
        $this->statistiquesInventaire = $this->inventaireEnCours->getStatistiques();
        } catch (\Exception $e) {
            $this->statistiquesInventaire = [
                'progression' => 0,
                'taux_conformite' => 0,
                'duree' => 0,
                'total_localisations' => 0,
                'localisations_terminees' => 0,
                'total_scans' => 0,
                'scans_presents' => 0,
                'scans_deplaces' => 0,
                'scans_absents' => 0,
                'scans_deteriores' => 0,
            ];
        }
    }

    private function loadRecentActions()
    {
        $actions = collect();

        try {
            // Scans récents (7 derniers jours)
            $scansRecents = InventaireScan::with(['bien', 'localisationReelle', 'agent'])
                ->where('date_scan', '>=', now()->subDays(7))
                ->orderBy('date_scan', 'desc')
                ->limit(5)
                ->get();

            foreach ($scansRecents as $scan) {
                if ($scan->bien && $scan->agent) {
                    $agentName = $scan->agent->name ?? 'Utilisateur';
                    $bienDesignation = $scan->bien->designation->designation ?? 'Immobilisation';
                    $localisationNom = $scan->localisationReelle->Localisation ?? 'N/A';
                    
                    $actions->push([
                        'type' => 'scan',
                        'icon' => '📋',
                        'message' => "{$agentName} a scanné {$bienDesignation} dans {$localisationNom}",
                        'time' => $scan->date_scan,
                    ]);
                }
            }

            // Inventaires démarrés/clôturés récemment
            $inventairesRecents = Inventaire::with('creator')
                ->where(function ($query) {
                    $query->where('date_debut', '>=', now()->subDays(7))
                          ->orWhere('date_fin', '>=', now()->subDays(7));
                })
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($inventairesRecents as $inventaire) {
                if ($inventaire->creator) {
                    $creatorName = $inventaire->creator->name ?? 'Utilisateur';
                    
                    if ($inventaire->statut === 'en_cours' && $inventaire->date_debut) {
                        $actions->push([
                            'type' => 'inventaire_started',
                            'icon' => '🚀',
                            'message' => "{$creatorName} a démarré l'inventaire {$inventaire->annee}",
                            'time' => $inventaire->date_debut,
                        ]);
                    } elseif ($inventaire->statut === 'cloture' && $inventaire->date_fin) {
                        $actions->push([
                            'type' => 'inventaire_closed',
                            'icon' => '✅',
                            'message' => "L'inventaire {$inventaire->annee} a été clôturé",
                            'time' => $inventaire->date_fin,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des actions récentes: ' . $e->getMessage());
        }

        // Trier par date et prendre les 10 plus récents
        $this->dernieresActions = $actions->sortByDesc('time')
            ->take(10)
            ->values()
            ->map(function ($action) {
                $action['time_ago'] = Carbon::parse($action['time'])->diffForHumans();
                return $action;
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
