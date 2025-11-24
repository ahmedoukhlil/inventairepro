<div>
    @php
        $isAdmin = auth()->user()->isAdmin();
        $natures = [
            'mobilier' => ['label' => 'Mobilier', 'color' => 'bg-blue-100 text-blue-800'],
            'informatique' => ['label' => 'Informatique', 'color' => 'bg-purple-100 text-purple-800'],
            'vehicule' => ['label' => 'Véhicule', 'color' => 'bg-yellow-100 text-yellow-800'],
            'materiel' => ['label' => 'Matériel', 'color' => 'bg-green-100 text-green-800'],
        ];
        $etats = [
            'neuf' => ['label' => 'Neuf', 'color' => 'bg-green-100 text-green-800'],
            'bon' => ['label' => 'Bon', 'color' => 'bg-green-100 text-green-800'],
            'moyen' => ['label' => 'Moyen', 'color' => 'bg-yellow-100 text-yellow-800'],
            'mauvais' => ['label' => 'Mauvais', 'color' => 'bg-red-100 text-red-800'],
            'reforme' => ['label' => 'Réformé', 'color' => 'bg-gray-100 text-gray-800'],
        ];
        $statutsScan = [
            'present' => ['label' => 'Présent', 'color' => 'bg-green-100 text-green-800', 'icon' => 'check-circle'],
            'deplace' => ['label' => 'Déplacé', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'arrow-right-circle'],
            'absent' => ['label' => 'Absent', 'color' => 'bg-red-100 text-red-800', 'icon' => 'x-circle'],
            'deteriore' => ['label' => 'Détérioré', 'color' => 'bg-orange-100 text-orange-800', 'icon' => 'exclamation-circle'],
        ];
    @endphp

    {{-- Header avec breadcrumb et actions --}}
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('biens.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Immobilisations</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $bien->code_inventaire }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    {{ $bien->code_inventaire }}
                    @if(isset($natures[$bien->nature]))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $natures[$bien->nature]['color'] }}">
                            {{ $natures[$bien->nature]['label'] }}
                        </span>
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500">{{ $bien->designation }}</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="{{ route('biens.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à la liste
                </a>

                @if($isAdmin)
                    <a 
                        href="{{ route('biens.edit', $bien) }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                @endif

                <button 
                    wire:click="telechargerEtiquette"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Télécharger étiquette
                </button>

                @if($isAdmin)
                    <button 
                        wire:click="supprimer"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible."
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Section principale (2 colonnes) --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Colonne gauche (60%) --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Card 1 : Informations générales --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations générales</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $bien->designation }}</h3>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if(isset($natures[$bien->nature]))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $natures[$bien->nature]['color'] }}">
                                {{ $natures[$bien->nature]['label'] }}
                            </span>
                        @endif
                        @if(isset($etats[$bien->etat]))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $etats[$bien->etat]['color'] }}">
                                {{ $etats[$bien->etat]['label'] }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500">Date d'acquisition</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $bien->date_acquisition->format('d/m/Y') }}
                                <span class="text-gray-500">(Il y a {{ $this->age }} an{{ $this->age > 1 ? 's' : '' }})</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Valeur d'acquisition</p>
                            <p class="text-2xl font-bold text-indigo-600">
                                {{ number_format($bien->valeur_acquisition, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">Code inventaire</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono">{{ $bien->code_inventaire }}</code>
                            <button 
                                onclick="navigator.clipboard.writeText('{{ $bien->code_inventaire }}'); alert('Code copié !');"
                                class="p-2 text-gray-500 hover:text-gray-700 transition-colors"
                                title="Copier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2 : Localisation --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Localisation</h2>
                
                @if($bien->localisation)
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Code et désignation</p>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $bien->localisation->code }} - {{ $bien->localisation->designation }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($bien->localisation->batiment)
                                <div>
                                    <p class="text-sm text-gray-500">Bâtiment</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $bien->localisation->batiment }}</p>
                                </div>
                            @endif
                            @if($bien->localisation->etage)
                                <div>
                                    <p class="text-sm text-gray-500">Étage</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $bien->localisation->etage }}</p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Service usager</p>
                            <p class="text-sm font-medium text-gray-900">{{ $bien->service_usager }}</p>
                        </div>

                        @if($bien->localisation->responsable)
                            <div>
                                <p class="text-sm text-gray-500">Responsable</p>
                                <p class="text-sm font-medium text-gray-900">{{ $bien->localisation->responsable }}</p>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-200">
                            <a 
                                href="{{ route('localisations.show', $bien->localisation) }}"
                                class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                Voir la fiche localisation
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Localisation non définie</p>
                @endif
            </div>

            {{-- Card 3 : Observation --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Observation</h2>
                @if($bien->observation)
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $bien->observation }}</p>
                @else
                    <p class="text-sm text-gray-500 italic">Aucune observation</p>
                @endif
            </div>

            {{-- Card 4 : Informations système --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations système</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Enregistré par</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $bien->user->name ?? 'N/A' }}
                            <span class="text-gray-500">le {{ $bien->created_at->format('d/m/Y à H:i') }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dernière modification</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bien->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bien->deleted_at ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $bien->deleted_at ? 'Réformé' : 'Actif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite (40%) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Card 1 : QR Code --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">QR Code</h2>
                
                @if($bien->qr_code_path && Storage::disk('public')->exists($bien->qr_code_path))
                    <div class="text-center mb-4">
                        @if(str_ends_with($bien->qr_code_path, '.svg'))
                            <div 
                                class="w-48 h-48 mx-auto cursor-pointer hover:opacity-80 transition-opacity flex items-center justify-center overflow-hidden"
                                onclick="document.getElementById('qr-modal').classList.remove('hidden')">
                                @php
                                    $svgContent = file_get_contents(storage_path('app/public/' . $bien->qr_code_path));
                                    // Remplacer les attributs width et height du SVG pour qu'il s'adapte au conteneur
                                    $svgContent = preg_replace('/width="[^"]*"/', 'width="100%"', $svgContent);
                                    $svgContent = preg_replace('/height="[^"]*"/', 'height="100%"', $svgContent);
                                    $svgContent = str_replace('<svg', '<svg style="width: 100%; height: 100%; object-fit: contain; max-width: 100%; max-height: 100%;"', $svgContent);
                                @endphp
                                {!! $svgContent !!}
                            </div>
                        @else
                            <img 
                                src="{{ asset('storage/' . $bien->qr_code_path) }}" 
                                alt="QR Code"
                                class="w-48 h-48 mx-auto cursor-pointer hover:opacity-80 transition-opacity object-contain"
                                onclick="document.getElementById('qr-modal').classList.remove('hidden')">
                        @endif
                        <p class="text-xs text-gray-500 mt-2">Scannez ce code lors des inventaires</p>
                    </div>

                    <div class="space-y-2">
                        <a 
                            href="{{ asset('storage/' . $bien->qr_code_path) }}"
                            download
                            class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Télécharger QR
                        </a>
                        <button 
                            wire:click="telechargerEtiquette"
                            class="w-full px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            Télécharger étiquette
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <p class="text-sm text-gray-500 mb-4">Aucun QR code généré</p>
                        <button 
                            wire:click="genererQRCode"
                            class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            Générer QR Code
                        </button>
                    </div>
                @endif
            </div>

            {{-- Card 2 : Statistiques --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistiques</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Nombre de fois scanné</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->nombreScans }}</p>
                    </div>
                    @if($this->dernierScan)
                        <div>
                            <p class="text-sm text-gray-500">Dernier inventaire</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $this->dernierScan->inventaire->annee ?? 'N/A' }}
                                <span class="text-gray-500">({{ $this->dernierScan->date_scan->format('d/m/Y') }})</span>
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Taux de présence</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $this->tauxPresence }}%</p>
                    </div>
                </div>
            </div>

            {{-- Card 3 : Actions rapides --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
                
                <div class="space-y-2">
                    @if($bien->localisation)
                        <a 
                            href="{{ route('biens.index', ['filterLocalisation' => $bien->localisation_id]) }}"
                            class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Voir toutes les immobilisations de cette localisation
                        </a>
                    @endif
                    <a 
                        href="{{ route('biens.index', ['filterService' => $bien->service_usager]) }}"
                        class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Voir toutes les immobilisations de ce service
                    </a>
                    <a 
                        href="{{ route('biens.index', ['filterNature' => $bien->nature]) }}"
                        class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Voir toutes les immobilisations de cette nature
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Section historique (pleine largeur) --}}
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div x-data="{ activeTab: 'historique' }">
            {{-- Onglets --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button 
                        @click="activeTab = 'historique'"
                        :class="activeTab === 'historique' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Historique inventaires
                    </button>
                    <button 
                        @click="activeTab = 'mouvements'"
                        :class="activeTab === 'mouvements' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Mouvements
                    </button>
                    <button 
                        @click="activeTab = 'documents'"
                        :class="activeTab === 'documents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Documents
                    </button>
                </nav>
            </div>

            {{-- Contenu onglet Historique --}}
            <div x-show="activeTab === 'historique'" x-transition>
                @if($this->historiqueScans->count() > 0)
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        <div class="space-y-6">
                            @foreach($this->historiqueScans as $scan)
                                <div class="relative flex items-start">
                                    <div class="absolute left-3 w-3 h-3 rounded-full border-2 border-white {{ isset($statutsScan[$scan->statut_scan]) ? $statutsScan[$scan->statut_scan]['color'] : 'bg-gray-200' }}" style="margin-top: 0.25rem;"></div>
                                    <div class="ml-8 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $scan->date_scan->format('d/m/Y à H:i') }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Inventaire {{ $scan->inventaire->annee ?? 'N/A' }}
                                                </p>
                                            </div>
                                            @if(isset($statutsScan[$scan->statut_scan]))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutsScan[$scan->statut_scan]['color'] }}">
                                                    {{ $statutsScan[$scan->statut_scan]['label'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            @if($scan->localisationReelle)
                                                <p>Localisation scannée : {{ $scan->localisationReelle->code }} - {{ $scan->localisationReelle->designation }}</p>
                                            @endif
                                            @if($scan->agent)
                                                <p>Agent : {{ $scan->agent->name }}</p>
                                            @endif
                                            @if($scan->commentaire)
                                                <p class="mt-1 italic">{{ $scan->commentaire }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm text-gray-500">Aucun scan d'inventaire enregistré</p>
                    </div>
                @endif
            </div>

            {{-- Contenu onglet Mouvements --}}
            <div x-show="activeTab === 'mouvements'" x-transition style="display: none;">
                @if($this->mouvements->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->mouvements as $mouvement)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mouvement['date']->format('d/m/Y à H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if($mouvement['localisation'])
                                                {{ $mouvement['localisation']->code }} - {{ $mouvement['localisation']->designation }}
                                            @else
                                                Localisation inconnue
                                            @endif
                                        </p>
                                        @if($mouvement['commentaire'])
                                            <p class="text-sm text-gray-500 italic mt-1">{{ $mouvement['commentaire'] }}</p>
                                        @endif
                                    </div>
                                    @if($mouvement['inventaire'])
                                        <span class="text-xs text-gray-500">Inventaire {{ $mouvement['inventaire']->annee }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <p class="text-sm text-gray-500">Aucun mouvement enregistré</p>
                    </div>
                @endif
            </div>

            {{-- Contenu onglet Documents --}}
            <div x-show="activeTab === 'documents'" x-transition style="display: none;">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-500 mb-2">Fonctionnalité à venir</p>
                    <p class="text-xs text-gray-400">Gestion des documents (factures, garanties, manuels, photos)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal QR Code agrandi --}}
    <div id="qr-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" onclick="this.classList.add('hidden')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('qr-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-lg p-8 max-w-md" onclick="event.stopPropagation()">
                <button 
                    onclick="document.getElementById('qr-modal').classList.add('hidden')"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                @if($bien->qr_code_path && Storage::disk('public')->exists($bien->qr_code_path))
                    @if(str_ends_with($bien->qr_code_path, '.svg'))
                        <div class="w-full flex items-center justify-center">
                            @php
                                $svgContent = file_get_contents(storage_path('app/public/' . $bien->qr_code_path));
                                $svgContent = str_replace('<svg', '<svg style="max-width: 100%; height: auto;"', $svgContent);
                            @endphp
                            {!! $svgContent !!}
                        </div>
                    @else
                        <img 
                            src="{{ asset('storage/' . $bien->qr_code_path) }}" 
                            alt="QR Code"
                            class="w-full h-auto">
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session()->has('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            x-transition
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>

