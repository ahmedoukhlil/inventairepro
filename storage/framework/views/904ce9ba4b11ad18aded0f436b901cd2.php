<div>
    <?php
        $isAdmin = auth()->user()->isAdmin();
        $stats = $this->statistiques;
        $statutsInventaire = [
            'en_preparation' => ['label' => 'En préparation', 'color' => 'bg-gray-100 text-gray-800'],
            'en_cours' => ['label' => 'En cours', 'color' => 'bg-blue-100 text-blue-800'],
            'termine' => ['label' => 'Terminé', 'color' => 'bg-orange-100 text-orange-800'],
            'cloture' => ['label' => 'Clôturé', 'color' => 'bg-green-100 text-green-800'],
        ];
        $statutsLoc = [
            'en_attente' => ['label' => 'En attente', 'color' => 'bg-gray-100 text-gray-800'],
            'en_cours' => ['label' => 'En cours', 'color' => 'bg-blue-100 text-blue-800'],
            'termine' => ['label' => 'Terminée', 'color' => 'bg-green-100 text-green-800'],
        ];
    ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($inventaire->statut, ['en_preparation', 'en_cours'])): ?>
        <div wire:poll.10s="refreshStatistiques" class="hidden"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                <li><a href="<?php echo e(route('dashboard')); ?>" class="text-gray-500 hover:text-indigo-600">Dashboard</a></li>
                <li class="flex items-center"><svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><a href="<?php echo e(route('inventaires.index')); ?>" class="text-gray-500 hover:text-indigo-600">Inventaires</a></li>
                <li class="flex items-center"><svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg><span class="text-gray-700 font-medium"><?php echo e($inventaire->annee); ?></span></li>
            </ol>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">Inventaire <?php echo e($inventaire->annee); ?></h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statutsInventaire[$inventaire->statut]['color'] ?? 'bg-gray-100 text-gray-800'); ?>">
                        <?php echo e($statutsInventaire[$inventaire->statut]['label'] ?? $inventaire->statut); ?>

                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo e($inventaire->date_debut?->format('d/m/Y')); ?> - <?php echo e($inventaire->date_fin?->format('d/m/Y') ?? 'En cours'); ?>

                </p>
            </div>
            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inventaire->statut === 'en_preparation'): ?>
                        <button wire:click="passerEnCours" wire:confirm="Voulez-vous démarrer cet inventaire ?" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Démarrer
                        </button>
                    <?php elseif($inventaire->statut === 'en_cours'): ?>
                        <button wire:click="terminerInventaire" wire:confirm="Voulez-vous terminer cet inventaire ?" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Terminer
                        </button>
                    <?php elseif($inventaire->statut === 'termine'): ?>
                        <button wire:click="cloturerInventaire" wire:confirm="Voulez-vous clôturer définitivement cet inventaire ? Cette action est irréversible." class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Clôturer
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($inventaire->statut, ['termine', 'cloture'])): ?>
                    <a href="<?php echo e(route('inventaires.rapport', $inventaire)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Voir rapport
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Progression</h3>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-4xl font-bold text-gray-900 mb-1"><?php echo e(round($stats['progression_globale'], 1)); ?>%</p>
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3">
                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-700" style="width: <?php echo e(min($stats['progression_globale'], 100)); ?>%"></div>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500"><?php echo e($stats['total_biens_scannes']); ?>/<?php echo e($stats['total_biens_attendus']); ?> scannés</span>
                <span class="text-gray-400"><?php echo e($stats['localisations_terminees']); ?>/<?php echo e($stats['total_localisations']); ?> localisations</span>
            </div>
        </div>

        
        <?php
            $confColor = $stats['taux_conformite'] >= 90 ? 'text-green-600' : ($stats['taux_conformite'] >= 70 ? 'text-amber-600' : 'text-red-600');
            $confBg = $stats['taux_conformite'] >= 90 ? 'bg-green-50' : ($stats['taux_conformite'] >= 70 ? 'bg-amber-50' : 'bg-red-50');
            $confIcon = $stats['taux_conformite'] >= 90 ? 'text-green-600' : ($stats['taux_conformite'] >= 70 ? 'text-amber-600' : 'text-red-600');
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Conformité</h3>
                <div class="h-10 w-10 rounded-lg <?php echo e($confBg); ?> flex items-center justify-center">
                    <svg class="w-5 h-5 <?php echo e($confIcon); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-4xl font-bold <?php echo e($confColor); ?> mb-1"><?php echo e(round($stats['taux_conformite'], 1)); ?>%</p>
            <p class="text-xs text-gray-400 mb-3"><?php echo e($stats['biens_presents']); ?> conformes sur <?php echo e($stats['total_biens_attendus']); ?> attendus</p>
            <div class="space-y-1.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Conformes</span>
                    <span class="font-medium text-gray-700"><?php echo e($stats['biens_presents']); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Déplacés</span>
                    <span class="font-medium text-gray-700"><?php echo e($stats['biens_deplaces']); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Absents</span>
                    <span class="font-medium text-gray-700"><?php echo e($stats['biens_absents']); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['biens_non_verifies'] > 0): ?>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-gray-300"></span> Non vérifiés</span>
                        <span class="font-medium text-gray-400"><?php echo e($stats['biens_non_verifies']); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php $totalAlertes = $this->totalAlertes; ?>
        <div class="bg-white rounded-xl shadow-sm border <?php echo e($totalAlertes > 0 ? 'border-red-200' : 'border-gray-200'); ?> p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Alertes</h3>
                <div class="h-10 w-10 rounded-lg <?php echo e($totalAlertes > 0 ? 'bg-red-50' : 'bg-gray-50'); ?> flex items-center justify-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalAlertes > 0): ?>
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <p class="text-4xl font-bold <?php echo e($totalAlertes > 0 ? 'text-red-600' : 'text-green-600'); ?> mb-3"><?php echo e($totalAlertes); ?></p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalAlertes > 0): ?>
                <p class="text-sm text-gray-500 mb-2">
                    <?php if($stats['biens_absents'] > 0): ?> <?php echo e($stats['biens_absents']); ?> absent(s) <?php endif; ?>
                    <?php if($stats['biens_absents'] > 0 && ($stats['biens_defectueux'] ?? 0) > 0): ?> &middot; <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stats['biens_defectueux'] ?? 0) > 0): ?> <?php echo e($stats['biens_defectueux']); ?> défectueux <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
                <a href="#alertes-detail" class="text-sm text-red-600 hover:text-red-800 font-medium inline-flex items-center gap-1">
                    Voir les détails
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            <?php else: ?>
                <p class="text-sm text-green-600">Aucune anomalie détectée</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Progression temporelle</h3>
                <p class="text-sm text-gray-500 mt-0.5">Cumul des scans vs objectif &middot; <?php echo e($stats['vitesse_moyenne']); ?> scans/jour en moyenne</p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['scans_aujourdhui'] > 0): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span></span>
                    <?php echo e($stats['scans_aujourdhui']); ?> scan(s) aujourd'hui
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="relative" style="height: 280px;">
            <canvas id="chart-progression-temporelle"></canvas>
        </div>
    </div>

    
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalAlertes > 0): ?>
        <?php $alertes = $this->alertes; ?>
        <div id="alertes-detail" x-data="{ expanded: true }" class="bg-white rounded-xl shadow-sm border border-red-200 mb-8 overflow-hidden">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-6 py-4 hover:bg-red-50/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-4.5 w-4.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-red-800"><?php echo e($totalAlertes); ?> alerte(s) requièrent votre attention</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['localisations_bloquees']) > 0): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><?php echo e(count($alertes['localisations_bloquees'])); ?> bloquée(s)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['biens_absents_valeur_haute']) > 0): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><?php echo e(count($alertes['biens_absents_valeur_haute'])); ?> absent(s)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['localisations_non_assignees']) > 0): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800"><?php echo e(count($alertes['localisations_non_assignees'])); ?> non assignée(s)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <svg class="w-5 h-5 text-red-400 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            <div x-show="expanded" x-collapse x-cloak>
                <div class="border-t border-red-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-red-50">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['localisations_bloquees']) > 0): ?>
                        <div class="bg-white p-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Localisations bloquées</h4>
                            <div class="space-y-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alertes['localisations_bloquees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-red-800"><?php echo e($alerte['code']); ?></span>
                                        <span class="text-xs text-red-600"><?php echo e($alerte['jours']); ?>j sans scan</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['biens_absents_valeur_haute']) > 0): ?>
                        <div class="bg-white p-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Immobilisations absentes</h4>
                            <div class="space-y-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($alertes['biens_absents_valeur_haute'], 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="text-xs font-mono text-red-700 bg-red-50 px-1.5 py-0.5 rounded"><?php echo e($alerte['code']); ?></span>
                                        <span class="text-gray-700 truncate"><?php echo e($alerte['designation']); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($alertes['biens_absents_valeur_haute']) > 5): ?>
                                    <p class="text-xs text-gray-400">+<?php echo e(count($alertes['biens_absents_valeur_haute']) - 5); ?> autre(s)</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['localisations_non_assignees']) > 0): ?>
                        <div class="bg-white p-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Non assignées</h4>
                            <div class="flex flex-wrap gap-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($alertes['localisations_non_assignees'], 0, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200"><?php echo e($alerte['code']); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($alertes['localisations_non_assignees']) > 8): ?>
                                    <span class="px-2 py-0.5 rounded text-xs text-gray-400">+<?php echo e(count($alertes['localisations_non_assignees']) - 8); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['biens_defectueux'] ?? []) > 0): ?>
                        <div class="bg-white p-4 sm:col-span-2 lg:col-span-3">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Défectueux (<?php echo e(count($alertes['biens_defectueux'])); ?>)</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($alertes['biens_defectueux'], 0, 9); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div x-data="{ showPhoto: false }" class="flex items-start gap-3 rounded-lg border border-orange-200 bg-orange-50/50 p-3">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($alerte['photo_url'])): ?>
                                            <button @click="showPhoto = true" class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 border-orange-300 hover:border-orange-500 transition-colors cursor-pointer relative group">
                                                <img src="<?php echo e($alerte['photo_url']); ?>" alt="Photo défaut" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                </div>
                                            </button>

                                            
                                            <div x-show="showPhoto" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showPhoto = false" @keydown.escape.window="showPhoto = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
                                                <div @click.stop class="relative max-w-3xl max-h-[85vh] bg-white rounded-xl shadow-2xl overflow-hidden">
                                                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-900"><?php echo e($alerte['code']); ?></p>
                                                            <p class="text-xs text-gray-500"><?php echo e($alerte['designation']); ?> &middot; <?php echo e($alerte['localisation']); ?></p>
                                                        </div>
                                                        <button @click="showPhoto = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-2 bg-gray-100">
                                                        <img src="<?php echo e($alerte['photo_url']); ?>" alt="Photo défaut <?php echo e($alerte['code']); ?>" class="max-h-[70vh] w-auto mx-auto rounded-lg">
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($alerte['commentaire'])): ?>
                                                        <div class="px-4 py-3 border-t border-gray-200">
                                                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Commentaire</p>
                                                            <p class="text-sm text-gray-700"><?php echo e($alerte['commentaire']); ?></p>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-mono font-semibold text-orange-800"><?php echo e($alerte['code']); ?></span>
                                            </div>
                                            <p class="text-xs text-gray-600 truncate mt-0.5" title="<?php echo e($alerte['designation']); ?>"><?php echo e($alerte['designation']); ?></p>
                                            <p class="text-xs text-gray-400 mt-0.5"><?php echo e($alerte['localisation']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['biens_defectueux']) > 9): ?>
                                <p class="text-xs text-gray-400 mt-2">+<?php echo e(count($alertes['biens_defectueux']) - 9); ?> autre(s) non affichés</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alertes['localisations_non_demarrees']) > 0): ?>
                        <div class="bg-white p-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Non démarrées</h4>
                            <div class="flex flex-wrap gap-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($alertes['localisations_non_demarrees'], 0, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600"><?php echo e($alerte['code']); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($alertes['localisations_non_demarrees']) > 8): ?>
                                    <span class="px-2 py-0.5 rounded text-xs text-gray-400">+<?php echo e(count($alertes['localisations_non_demarrees']) - 8); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <h2 class="text-base font-semibold text-gray-900">Localisations</h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full"><?php echo e($this->inventaireLocalisations->count()); ?></span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" wire:model.live.debounce.300ms="searchLoc" placeholder="Rechercher..." class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-48 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <select wire:model.live="filterStatutLoc" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminées</option>
                    </select>
                    <select wire:model.live="filterAgent" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Tous les agents</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($agent->idUser); ?>"><?php echo e($agent->users ?? $agent->name ?? 'Agent'); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        
        <div class="overflow-x-auto relative">
            <div wire:loading.flex wire:target="searchLoc, filterStatutLoc, filterAgent, sortBy, reassignerLocalisation" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 items-center justify-center">
                <div class="flex items-center gap-2 text-indigo-600 text-sm">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Chargement...
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('code')">
                            <div class="flex items-center gap-1">
                                Code
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortField === 'code'): ?>
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'); ?>"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Désignation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->inventaireLocalisations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invLoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $progression = $invLoc->nombre_biens_attendus > 0 
                                ? round(($invLoc->nombre_biens_scannes / $invLoc->nombre_biens_attendus) * 100, 1) 
                                : 0;
                            $progressionColor = $progression >= 100 ? 'bg-green-500' : ($progression >= 50 ? 'bg-indigo-500' : 'bg-indigo-300');
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-900"><?php echo e($invLoc->localisation->CodeLocalisation ?? 'N/A'); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 max-w-[250px] truncate block" title="<?php echo e($invLoc->localisation->Localisation ?? ''); ?>"><?php echo e($invLoc->localisation->Localisation ?? 'N/A'); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($statutsLoc[$invLoc->statut])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statutsLoc[$invLoc->statut]['color']); ?>">
                                        <?php echo e($statutsLoc[$invLoc->statut]['label']); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-gray-100 rounded-full h-2">
                                        <div class="<?php echo e($progressionColor); ?> h-2 rounded-full transition-all duration-500" style="width: <?php echo e(min($progression, 100)); ?>%"></div>
                                    </div>
                                    <span class="text-sm tabular-nums <?php echo e($progression >= 100 ? 'text-green-600 font-semibold' : 'text-gray-600'); ?>">
                                        <?php echo e($invLoc->nombre_biens_scannes); ?>/<?php echo e($invLoc->nombre_biens_attendus); ?>

                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invLoc->agent): ?>
                                        <div class="flex items-center gap-2">
                                            <div class="h-7 w-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-medium text-indigo-600"><?php echo e(mb_substr($invLoc->agent->users ?? $invLoc->agent->name ?? '?', 0, 1)); ?></span>
                                            </div>
                                            <span class="text-sm text-gray-700"><?php echo e($invLoc->agent->users ?? $invLoc->agent->name ?? 'Agent'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded">Non assigné</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin): ?>
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Réassigner">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                                <p class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase">Assigner à</p>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <button wire:click="reassignerLocalisation(<?php echo e($invLoc->id); ?>, <?php echo e($agent->idUser); ?>)" @click="open = false" class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 <?php echo e($invLoc->user_id == $agent->idUser ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700'); ?>">
                                                        <?php echo e($agent->users ?? $agent->name ?? 'Agent'); ?>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invLoc->user_id == $agent->idUser): ?> <svg class="w-3.5 h-3.5 inline ml-1 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </button>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invLoc->agent): ?>
                                                    <div class="border-t border-gray-100 mt-1"></div>
                                                    <button wire:click="reassignerLocalisation(<?php echo e($invLoc->id); ?>, '')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50">Retirer</button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="<?php echo e(route('localisations.show', $invLoc->localisation)); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Détails</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-sm text-gray-500">Aucune localisation trouvée</p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchLoc || $filterStatutLoc !== 'all' || $filterAgent !== 'all'): ?>
                                    <button wire:click="$set('searchLoc', ''); $set('filterStatutLoc', 'all'); $set('filterAgent', 'all')" class="mt-2 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        Réinitialiser les filtres
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->derniersScans->count() > 0): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Derniers scans</h3>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                    Temps réel
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->derniersScans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $scanColors = [
                            'present' => 'border-green-200 bg-green-50',
                            'deplace' => 'border-amber-200 bg-amber-50',
                            'absent' => 'border-red-200 bg-red-50',
                            'deteriore' => 'border-orange-200 bg-orange-50',
                        ];
                        $dotColors = [
                            'present' => 'bg-green-500',
                            'deplace' => 'bg-amber-500',
                            'absent' => 'bg-red-500',
                            'deteriore' => 'bg-orange-500',
                        ];
                    ?>
                    <div class="rounded-lg border <?php echo e($scanColors[$scan->statut_scan] ?? 'border-gray-200 bg-gray-50'); ?> p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full <?php echo e($dotColors[$scan->statut_scan] ?? 'bg-gray-400'); ?> flex-shrink-0"></span>
                            <span class="text-xs font-mono font-semibold text-gray-700 truncate"><?php echo e($scan->code_inventaire); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 truncate mb-1" title="<?php echo e($scan->designation); ?>"><?php echo e($scan->designation); ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span><?php echo e($scan->agent?->users ?? $scan->agent?->name ?? 'Système'); ?></span>
                            <span><?php echo e($scan->date_scan?->diffForHumans()); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            let chartTemp = null;
            
            function destroyCharts() {
                if (chartTemp) { chartTemp.destroy(); chartTemp = null; }
            }
            
            function initCharts() {
                destroyCharts();
                
                const ctxTemp = document.getElementById('chart-progression-temporelle');
                if (!ctxTemp) return;

                const scans = <?php echo json_encode($this->scansGraphData, 15, 512) ?>;
                const objectif = <?php echo e($stats['total_biens_attendus']); ?>;
                
                const scansParDate = {};
                scans.forEach(scan => {
                    const date = new Date(scan.date_scan).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                    scansParDate[date] = (scansParDate[date] || 0) + 1;
                });
                
                const dates = Object.keys(scansParDate).sort((a, b) => {
                    const [da, ma] = a.split('/');
                    const [db, mb] = b.split('/');
                    return (ma + da).localeCompare(mb + db);
                });
                const quotidien = dates.map(d => scansParDate[d]);
                const cumulatif = [];
                let cumul = 0;
                quotidien.forEach(qty => { cumul += qty; cumulatif.push(cumul); });

                chartTemp = new Chart(ctxTemp, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Cumul des scans',
                            data: cumulatif,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.08)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true,
                            pointRadius: dates.length > 20 ? 0 : 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#4f46e5',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }, {
                            label: 'Objectif (' + objectif.toLocaleString('fr-FR') + ')',
                            data: new Array(dates.length).fill(objectif),
                            borderColor: '#d1d5db',
                            borderWidth: 2,
                            borderDash: [8, 4],
                            fill: false,
                            pointRadius: 0,
                        }, {
                            label: 'Scans quotidiens',
                            data: quotidien,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 1.5,
                            tension: 0.3,
                            fill: false,
                            pointRadius: dates.length > 20 ? 0 : 2,
                            pointHoverRadius: 4,
                            yAxisID: 'y1',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 500 },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: { padding: 20, font: { size: 12 }, usePointStyle: true, pointStyle: 'circle' }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: (ctx) => {
                                        const v = ctx.parsed.y || 0;
                                        if (ctx.datasetIndex === 0) {
                                            const pct = objectif > 0 ? ((v / objectif) * 100).toFixed(1) : 0;
                                            return ctx.dataset.label + ': ' + v.toLocaleString('fr-FR') + ' (' + pct + '%)';
                                        }
                                        if (ctx.datasetIndex === 1) return ctx.dataset.label;
                                        return ctx.dataset.label + ': ' + v;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#9ca3af', maxRotation: 0, autoSkip: true, maxTicksLimit: 12 }
                            },
                            y: {
                                beginAtZero: true,
                                max: cumulatif.length > 0 ? Math.max(objectif, Math.max(...cumulatif)) * 1.1 : objectif * 1.1,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: { font: { size: 11 }, color: '#9ca3af', callback: v => v.toLocaleString('fr-FR') }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: { drawOnChartArea: false },
                                ticks: { font: { size: 11 }, color: '#10b981' }
                            }
                        }
                    }
                });
            }
            
            document.addEventListener('DOMContentLoaded', initCharts);
            document.addEventListener('livewire:navigated', initCharts);
            
            if (typeof Livewire !== 'undefined') {
                Livewire.on('statistiques-updated', () => setTimeout(initCharts, 200));
            } else {
                document.addEventListener('livewire:init', () => {
                    Livewire.on('statistiques-updated', () => setTimeout(initCharts, 200));
                });
            }
        })();
    </script>

    
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-3 items-end">
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/inventaires/dashboard-inventaire.blade.php ENDPATH**/ ?>