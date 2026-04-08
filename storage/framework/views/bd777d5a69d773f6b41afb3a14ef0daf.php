<div>
    <?php
        $isAdmin = auth()->user()->isAdmin();
    ?>

    
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
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
                        <a href="<?php echo e(route('biens.index')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Immobilisations</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo e($bien->NumOrdre); ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <?php echo e($bien->code_formate ?? 'NumOrdre: ' . $bien->NumOrdre); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->categorie): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?php echo e($bien->categorie->Categorie); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo e($bien->designation ? $bien->designation->designation : 'N/A'); ?>

                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a 
                    href="<?php echo e(route('biens.index')); ?>"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à la liste
                </a>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                    <a 
                        href="<?php echo e(route('biens.edit', $bien)); ?>"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <button 
                    id="btn-print-etiquette-<?php echo e($bien->NumOrdre); ?>"
                    data-bien-id="<?php echo e($bien->NumOrdre); ?>"
                    data-code-value="<?php echo e($bien->NumOrdre); ?>"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer étiquette
                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                    <button 
                        wire:click="supprimer"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible."
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        
        <div class="lg:col-span-3 space-y-6">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations générales</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            <?php echo e($bien->designation ? $bien->designation->designation : 'N/A'); ?>

                        </h3>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->categorie): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo e($bien->categorie->Categorie); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->etat): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <?php echo e($bien->etat->Etat); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->natureJuridique): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <?php echo e($bien->natureJuridique->NatJur); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500">Année d'acquisition</p>
                            <p class="text-sm font-medium text-gray-900">
                                <?php if($bien->DateAcquisition && $bien->DateAcquisition > 1970): ?>
                                    <?php echo e($bien->DateAcquisition); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->age && $this->age > 0): ?>
                                        <span class="text-gray-500">(<?php echo e($this->age); ?> an<?php echo e($this->age > 1 ? 's' : ''); ?>)</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">Non renseignée</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Numéro d'ordre</p>
                            <p class="text-2xl font-bold text-indigo-600">
                                <?php echo e($bien->NumOrdre); ?>

                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">Code d'immobilisation</p>
                        <div class="flex items-center gap-2">
                            <code class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono"><?php echo e($bien->code_formate ?? 'N/A'); ?></code>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->code_formate): ?>
                                <button 
                                    onclick="navigator.clipboard.writeText('<?php echo e($bien->code_formate); ?>'); alert('Code copié !');"
                                    class="p-2 text-gray-500 hover:text-gray-700 transition-colors"
                                    title="Copier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Emplacement</h2>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement): ?>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Emplacement</p>
                            <p class="text-lg font-medium text-gray-900">
                                <?php echo e($bien->emplacement->Emplacement); ?>

                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement->CodeEmplacement): ?>
                                <p class="text-sm text-gray-500 mt-1">Code: <?php echo e($bien->emplacement->CodeEmplacement); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement->localisation): ?>
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-1">Localisation</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo e($bien->emplacement->localisation->Localisation); ?>

                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement->localisation->CodeLocalisation): ?>
                                    <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($bien->emplacement->localisation->CodeLocalisation); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement->affectation): ?>
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-1">Affectation</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo e($bien->emplacement->affectation->Affectation); ?>

                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement->affectation->CodeAffectation): ?>
                                    <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($bien->emplacement->affectation->CodeAffectation); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 italic">Aucun emplacement assigné</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Observations</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->Observations): ?>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($bien->Observations); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-500 italic">Aucune observation</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Code-barres Code 128</h2>
                
                
                <div class="text-center mb-4">
                    <div 
                        id="barcode-container-<?php echo e($bien->NumOrdre); ?>"
                        class="w-full mx-auto cursor-pointer hover:opacity-80 transition-opacity bg-white p-2 rounded border border-gray-200"
                        onclick="document.getElementById('barcode-modal').classList.remove('hidden')"
                        title="Cliquez pour agrandir">
                        <svg id="barcode-svg-<?php echo e($bien->NumOrdre); ?>" width="100%" height="40" style="max-width: 100%; display: block;"></svg>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">Code 128 - 89mm × 36mm</p>
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-700 font-mono font-semibold"><?php echo e($bien->NumOrdre); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->code_formate): ?>
                            <p class="text-xs text-gray-600 font-mono"><?php echo e($bien->code_formate); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation): ?>
                            <p class="text-xs text-gray-700 font-medium"><?php echo e($bien->designation->designation); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <button 
                        id="btn-print-label-<?php echo e($bien->NumOrdre); ?>"
                        data-bien-id="<?php echo e($bien->NumOrdre); ?>"
                        data-code-value="<?php echo e($bien->NumOrdre); ?>"
                        class="w-full px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        Imprimer étiquette
                    </button>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations complémentaires</h2>
                
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->natureJuridique): ?>
                        <div>
                            <p class="text-sm text-gray-500">Nature Juridique</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($bien->natureJuridique->NatJur); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->natureJuridique->CodeNatJur): ?>
                                <p class="text-xs text-gray-500">Code: <?php echo e($bien->natureJuridique->CodeNatJur); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->sourceFinancement): ?>
                        <div>
                            <p class="text-sm text-gray-500">Source de Financement</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e($bien->sourceFinancement->SourceFin); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->sourceFinancement->CodeSourceFin): ?>
                                <p class="text-xs text-gray-500">Code: <?php echo e($bien->sourceFinancement->CodeSourceFin); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->age): ?>
                        <div>
                            <p class="text-sm text-gray-500">Âge</p>
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo e($this->age); ?> an<?php echo e($this->age > 1 ? 's' : ''); ?>

                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
                
                <div class="space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->emplacement): ?>
                        <a 
                            href="<?php echo e(route('biens.index', ['filterEmplacement' => $bien->idEmplacement])); ?>"
                            class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Voir toutes les immobilisations de cet emplacement
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->categorie): ?>
                        <a 
                            href="<?php echo e(route('biens.index', ['filterCategorie' => $bien->idCategorie])); ?>"
                            class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Voir toutes les immobilisations de cette catégorie
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation): ?>
                        <a 
                            href="<?php echo e(route('biens.index', ['filterDesignation' => $bien->idDesignation])); ?>"
                            class="block w-full text-left px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Voir toutes les immobilisations de cette désignation
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations détaillées</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation): ?>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Désignation</p>
                    <p class="text-sm font-medium text-gray-900"><?php echo e($bien->designation->designation); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation->CodeDesignation): ?>
                        <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($bien->designation->CodeDesignation); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation->categorie): ?>
                        <p class="text-xs text-gray-500 mt-1">Catégorie: <?php echo e($bien->designation->categorie->Categorie); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->categorie): ?>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Catégorie</p>
                    <p class="text-sm font-medium text-gray-900"><?php echo e($bien->categorie->Categorie); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->categorie->CodeCategorie): ?>
                        <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($bien->categorie->CodeCategorie); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->etat): ?>
                <div>
                    <p class="text-sm text-gray-500 mb-1">État</p>
                    <p class="text-sm font-medium text-gray-900"><?php echo e($bien->etat->Etat); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->etat->CodeEtat): ?>
                        <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($bien->etat->CodeEtat); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div id="barcode-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" onclick="this.classList.add('hidden')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('barcode-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-lg p-8 max-w-2xl" onclick="event.stopPropagation()">
                <button 
                    onclick="document.getElementById('barcode-modal').classList.add('hidden')"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="w-full flex items-center justify-center bg-white p-6 rounded-lg border border-gray-200">
                    <div id="barcode-modal-placeholder-<?php echo e($bien->NumOrdre); ?>" style="min-height: 120px; display: flex; align-items: center; justify-content: center; width: 100%; max-width: 600px;">
                        <svg id="barcode-svg-modal-<?php echo e($bien->NumOrdre); ?>" width="100%" height="140" style="max-width: 100%; display: block;"></svg>
                    </div>
                </div>
                <div class="text-center mt-4 space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->code_formate): ?>
                        <p class="text-xs font-mono text-gray-600"><?php echo e($bien->code_formate); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bien->designation): ?>
                        <p class="text-xs font-medium text-gray-700"><?php echo e($bien->designation->designation); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($bien) && $bien->NumOrdre): ?>
    
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    
    <script>
        console.log('🔍 Initialisation du script de code-barres...');
        
        // Variables globales pour le bien - Utiliser NumOrdre uniquement
        const BIEN_ID = <?php echo e($bien->NumOrdre); ?>;
        const CODE_VALUE = <?php echo e($bien->NumOrdre); ?>;
        // Définir sur window pour être accessible partout
        window.CODE_FORMATE = <?php echo json_encode($bien->code_formate ?? '', 15, 512) ?>;
        window.DESIGNATION = <?php echo json_encode($bien->designation->designation ?? '', 15, 512) ?>;
        console.log('Bien:', { id: BIEN_ID, code: CODE_VALUE, codeFormate: window.CODE_FORMATE, designation: window.DESIGNATION });
        // Fonction simplifiée pour générer le code-barres
        function generateBarcode(bienId, codeValue) {
            console.log('📊 generateBarcode appelé:', { bienId, codeValue });
            
            // Validation
            if (!codeValue || String(codeValue).trim() === '') {
                console.error('❌ Code vide');
                return false;
            }
            
            if (typeof JsBarcode === 'undefined') {
                console.error('❌ JsBarcode non chargé');
                return false;
            }
            
            const code = String(codeValue).trim();
            console.log('✅ Code à générer:', code);
            
            // Générer dans l'élément principal (version compacte)
            const svgMain = document.getElementById('barcode-svg-' + bienId);
            if (svgMain) {
                try {
                    JsBarcode(svgMain, code, {
                        format: "CODE128",
                        width: 1.5,
                        height: 35,
                        displayValue: false, // On affiche le texte séparément
                        background: "#ffffff",
                        lineColor: "#000000",
                        margin: 4
                    });
                    console.log('✅ Code-barres principal généré');
                } catch (e) {
                    console.error('❌ Erreur génération principale:', e);
                    return false;
                }
            } else {
                console.error('❌ Élément SVG principal non trouvé');
            }
            
            // Générer dans le modal (plus grand, format landscape)
            const svgModal = document.getElementById('barcode-svg-modal-' + bienId);
            if (svgModal) {
                try {
                    JsBarcode(svgModal, code, {
                        format: "CODE128",
                        width: 3,
                        height: 100,
                        displayValue: false, // Le texte sera affiché séparément en dessous
                        background: "#ffffff",
                        lineColor: "#000000",
                        margin: 15
                    });
                    console.log('✅ Code-barres modal généré');
                } catch (e) {
                    console.error('❌ Erreur génération modal:', e);
                }
            }
            
            return true;
        }
        
        // Initialiser les event listeners pour les boutons
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM chargé');
            
            // Attacher les événements pour les boutons d'impression
            const btnPrintLabel = document.getElementById('btn-print-label-' + BIEN_ID);
            if (btnPrintLabel) {
                console.log('✅ Bouton imprimer étiquette trouvé');
                btnPrintLabel.addEventListener('click', function() {
                    console.log('🖨️ Clic sur imprimer étiquette');
                    if (typeof window.imprimerEtiquette === 'function') {
                        window.imprimerEtiquette(BIEN_ID, CODE_VALUE);
                    }
                });
            } else {
                console.error('❌ Bouton imprimer étiquette non trouvé');
            }
            
            const btnPrintEtiquette = document.getElementById('btn-print-etiquette-' + BIEN_ID);
            if (btnPrintEtiquette) {
                console.log('✅ Bouton imprimer (haut) trouvé');
                btnPrintEtiquette.addEventListener('click', function() {
                    console.log('🖨️ Clic sur imprimer (haut)');
                    if (typeof window.imprimerEtiquette === 'function') {
                        window.imprimerEtiquette(BIEN_ID, CODE_VALUE);
                    }
                });
            }
        });
        
        // Attendre que JsBarcode soit chargé, puis générer
        window.addEventListener('load', function() {
            console.log('🚀 Fenêtre chargée');
            
            // Attendre un peu pour que tout soit prêt
            setTimeout(function() {
                if (typeof JsBarcode !== 'undefined') {
                    console.log('✅ JsBarcode chargé, génération du code-barres...');
                    generateBarcode(BIEN_ID, CODE_VALUE);
                } else {
                    console.error('❌ JsBarcode non chargé');
                }
            }, 300);
        });

        // Fonction pour imprimer l'étiquette avec le code-barres généré côté client
        window.imprimerEtiquette = async function(bienId, codeValue) {
            console.log('🖨️ Impression de l\'étiquette...', { bienId, codeValue });
            try {
                // Vérifier que jsbarcode et jsPDF sont chargés
                if (typeof JsBarcode === 'undefined') {
                    alert('Erreur: jsbarcode n\'est pas chargé. Veuillez recharger la page.');
                    return;
                }
                
                if (typeof window.jspdf === 'undefined') {
                    alert('Erreur: jsPDF n\'est pas chargé. Veuillez recharger la page.');
                    return;
                }

                const { jsPDF } = window.jspdf;

                // S'assurer que codeValue est une chaîne
                const codeStr = String(codeValue).trim();
                if (!codeStr) {
                    throw new Error('Code vide');
                }
                
                // Dimensions étiquettes Dymo Large Address Labels : 89mm × 36mm (Landscape)
                const labelWidthMm = 89; // Largeur de l'étiquette Dymo (Landscape)
                const labelHeightMm = 36; // Hauteur de l'étiquette Dymo (Landscape)
                
                // Créer un canvas pour générer le code-barres en PNG
                const tempCanvas = document.createElement('canvas');
                tempCanvas.style.position = 'absolute';
                tempCanvas.style.left = '-9999px';
                document.body.appendChild(tempCanvas);
                
                // Générer le code-barres directement sur le canvas (PNG)
                // Pour Code 128 sur étiquette Dymo 89mm de large (Landscape), paramètres optimaux
                JsBarcode(tempCanvas, codeStr, {
                    format: "CODE128",
                    width: 2, // Largeur normale pour 89mm
                    height: 50, // Hauteur optimale pour 36mm de haut
                    displayValue: false,
                    background: "#ffffff",
                    lineColor: "#000000",
                    margin: 0,
                    valid: function(valid) {
                        if (!valid) {
                            console.error('Code invalide pour Code 128:', codeStr);
                            throw new Error('Code invalide pour Code 128');
                        }
                    }
                });
                
                // Calculer les dimensions réelles du code-barres généré
                const barcodeAspectRatio = tempCanvas.width / tempCanvas.height;
                
                // Créer un canvas pour le PDF (dimensions de l'étiquette)
                const mmToPx = 3.779527559; // 1mm = 3.779527559 pixels à 96 DPI
                const pdfCanvas = document.createElement('canvas');
                pdfCanvas.width = labelWidthMm * mmToPx;
                pdfCanvas.height = labelHeightMm * mmToPx;
                const pdfCtx = pdfCanvas.getContext('2d');
                
                // Fond blanc
                pdfCtx.fillStyle = '#ffffff';
                pdfCtx.fillRect(0, 0, pdfCanvas.width, pdfCanvas.height);
                
                // Calculer les dimensions du code-barres
                const barcodeWidthMm = Math.min(labelWidthMm - 10, (tempCanvas.width / mmToPx)); // 5mm de marge de chaque côté
                const barcodeHeightMm = (tempCanvas.height / mmToPx);
                
                // Centrer le code-barres horizontalement et verticalement
                const barcodeX = (labelWidthMm - barcodeWidthMm) / 2;
                const barcodeY = (labelHeightMm - barcodeHeightMm - 6) / 2; // Centré verticalement avec espace pour le texte en bas
                
                // Convertir en pixels
                const barcodeWidthPx = barcodeWidthMm * mmToPx;
                const barcodeHeightPx = barcodeHeightMm * mmToPx;
                const barcodeXPx = barcodeX * mmToPx;
                const barcodeYPx = barcodeY * mmToPx;
                
                // Dessiner le code-barres sur le canvas PDF
                pdfCtx.drawImage(tempCanvas, barcodeXPx, barcodeYPx, barcodeWidthPx, barcodeHeightPx);
                
                // Nettoyer le canvas temporaire
                document.body.removeChild(tempCanvas);
                
                // Créer le PDF avec jsPDF (dimensions de l'étiquette en Landscape)
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [labelHeightMm, labelWidthMm] // [hauteur, largeur] pour landscape
                });
                
                // Ajouter le code-barres (image PNG du canvas)
                const imgData = pdfCanvas.toDataURL('image/png', 1.0);
                pdf.addImage(imgData, 'PNG', 0, 0, labelWidthMm, labelHeightMm);
                
                // Ajouter les textes en dessous du code-barres (centré)
                let currentY = labelHeightMm - 8; // Commencer plus haut pour avoir de la place
                
                // Code complet (code_formate) si disponible
                const codeFormate = window.CODE_FORMATE || '';
                if (codeFormate && codeFormate.trim() !== '') {
                    pdf.setFontSize(7);
                    pdf.setFont('courier', 'normal');
                    pdf.text(codeFormate, labelWidthMm / 2, currentY, { align: 'center' });
                    currentY += 5; // Espacement augmenté après le code
                }
                
                // Désignation si disponible
                const designation = window.DESIGNATION || '';
                if (designation && designation.trim() !== '') {
                    pdf.setFontSize(6);
                    pdf.setFont('helvetica', 'normal');
                    // Tronquer la désignation si trop longue
                    const maxWidth = labelWidthMm - 4;
                    const truncatedDesignation = pdf.splitTextToSize(designation, maxWidth);
                    truncatedDesignation.forEach((line, index) => {
                        if (currentY < labelHeightMm - 1) {
                            pdf.text(line, labelWidthMm / 2, currentY, { align: 'center' });
                            currentY += 2.5;
                        }
                    });
                }
                
                // Ouvrir le PDF dans une nouvelle fenêtre et lancer l'impression
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);
                const printWindow = window.open(pdfUrl, '_blank');
                
                if (printWindow) {
                    printWindow.onload = function() {
                        setTimeout(() => {
                            printWindow.print();
                            // Nettoyer l'URL après l'impression
                            setTimeout(() => {
                                URL.revokeObjectURL(pdfUrl);
                            }, 1000);
                        }, 250);
                    };
                } else {
                    // Si la fenêtre n'a pas pu s'ouvrir, télécharger le PDF
                    pdf.save('etiquette_' + codeValue.replace(/\//g, '_') + '.pdf');
                }
            } catch (error) {
                console.error('Erreur lors de la génération de l\'étiquette:', error);
                alert('Erreur lors de la génération de l\'étiquette: ' + error.message);
            }
        };
    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
</div><?php /**PATH C:\xampp\htdocs\gesimmos\resources\views\livewire\biens\detail-bien.blade.php ENDPATH**/ ?>