<div x-data="{ activeTab: <?php if ((object) ('activeTab') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('activeTab'->value()); ?>')<?php echo e('activeTab'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('activeTab'); ?>')<?php endif; ?>, sousOnglet: 'presents' }" class="space-y-6">
    <?php
        // $etatsConstate est injecté par le composant Livewire (depuis la table etat)
        $conformiteClass = function($taux) {
            if ($taux >= 95) return ['text' => 'text-green-700',  'bg' => 'bg-green-50',  'border' => 'border-green-200',  'bar' => 'bg-green-500',  'label' => 'Excellent'];
            if ($taux >= 85) return ['text' => 'text-indigo-700', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'bar' => 'bg-indigo-500', 'label' => 'Satisfaisant'];
            if ($taux >= 70) return ['text' => 'text-amber-700',  'bg' => 'bg-amber-50',  'border' => 'border-amber-200',  'bar' => 'bg-amber-500',  'label' => 'Moyen'];
            return             ['text' => 'text-red-700',    'bg' => 'bg-red-50',    'border' => 'border-red-200',    'bar' => 'bg-red-500',    'label' => 'Insuffisant'];
        };
        $taux        = $this->statistiques['taux_conformite'];
        $couverture  = $this->statistiques['taux_couverture'] ?? 0;
        $interp      = $conformiteClass($taux);

        $statutsInventaire = [
            'termine' => ['label' => 'Terminé',  'color' => 'bg-amber-100 text-amber-800 border border-amber-200'],
            'cloture' => ['label' => 'Clôturé',  'color' => 'bg-green-100 text-green-800 border border-green-200'],
        ];
        $statutsScan = [
            'present'   => ['label' => 'Présent',   'color' => 'bg-green-100  text-green-800'],
            'deplace'   => ['label' => 'Déplacé',   'color' => 'bg-yellow-100 text-yellow-800'],
            'absent'    => ['label' => 'Absent',    'color' => 'bg-red-100    text-red-800'],
            'deteriore' => ['label' => 'Détérioré', 'color' => 'bg-orange-100 text-orange-800'],
        ];
    ?>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        
        <div class="h-1 w-full bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-400"></div>

        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap mb-1">
                    <h1 class="text-xl font-bold text-gray-900">
                        Rapport — Inventaire <?php echo e($inventaire->annee); ?>

                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo e($statutsInventaire[$inventaire->statut]['color']); ?>">
                        <?php echo e($statutsInventaire[$inventaire->statut]['label']); ?>

                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    Du <?php echo e($inventaire->date_debut?->format('d/m/Y') ?? '—'); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inventaire->date_fin): ?> au <?php echo e($inventaire->date_fin->format('d/m/Y')); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    &bull; <?php echo e($this->statistiques['duree_jours']); ?> jour(s)
                    &bull; <?php echo e($this->statistiques['nombre_agents']); ?> agent(s)
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <button wire:click="exportPDF"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </button>
                <a href="<?php echo e(route('inventaires.imprimer', $inventaire)); ?>" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer
                </a>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        
        <div class="bg-white rounded-xl border <?php echo e($interp['border']); ?> shadow-sm p-5 <?php echo e($interp['bg']); ?>">
            <p class="text-xs font-semibold uppercase tracking-wide <?php echo e($interp['text']); ?> opacity-75 mb-1">Taux de conformité</p>
            <p class="text-3xl font-bold <?php echo e($interp['text']); ?>"><?php echo e($taux); ?>%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="<?php echo e($interp['bar']); ?> h-full rounded-full" style="width:<?php echo e(min(100,$taux)); ?>%"></div>
            </div>
            <p class="text-xs <?php echo e($interp['text']); ?> mt-1.5 font-medium"><?php echo e($interp['label']); ?></p>
        </div>

        
        <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-5 bg-indigo-50">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600 opacity-75 mb-1">Taux de couverture</p>
            <p class="text-3xl font-bold text-indigo-700"><?php echo e($couverture); ?>%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-indigo-500 h-full rounded-full" style="width:<?php echo e(min(100,$couverture)); ?>%"></div>
            </div>
            <p class="text-xs text-indigo-600 mt-1.5"><?php echo e($this->statistiques['total_biens_scannes']); ?> / <?php echo e($this->statistiques['total_biens_attendus']); ?> vérifiés</p>
        </div>

        
        <?php $tauxAbsence = $this->statistiques['taux_absence'] ?? 0; ?>
        <div class="bg-white rounded-xl border <?php echo e($tauxAbsence > 10 ? 'border-red-200 bg-red-50' : 'border-gray-200'); ?> shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide <?php echo e($tauxAbsence > 10 ? 'text-red-600' : 'text-gray-500'); ?> opacity-75 mb-1">Taux d'absence</p>
            <p class="text-3xl font-bold <?php echo e($tauxAbsence > 10 ? 'text-red-700' : 'text-gray-900'); ?>"><?php echo e($tauxAbsence); ?>%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="<?php echo e($tauxAbsence > 10 ? 'bg-red-500' : 'bg-gray-400'); ?> h-full rounded-full" style="width:<?php echo e(min(100,$tauxAbsence)); ?>%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5"><?php echo e($this->statistiques['biens_absents']); ?> absent(s)</p>
        </div>

        
        <?php $prog = $this->statistiques['progression_globale'] ?? 0; ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 opacity-75 mb-1">Progression</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($prog); ?>%</p>
            <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-gray-600 h-full rounded-full" style="width:<?php echo e(min(100,$prog)); ?>%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5"><?php echo e($this->statistiques['localisations_terminees']); ?>/<?php echo e($this->statistiques['total_localisations']); ?> loc.</p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <div class="w-1 h-4 rounded-full bg-indigo-600"></div>
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Résultats de vérification</h2>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['label'=>'Présents',   'val'=>$this->statistiques['biens_presents'],               'cls'=>'text-green-700  bg-green-50  border-green-200'],
                ['label'=>'Déplacés',   'val'=>$this->statistiques['biens_deplaces'],               'cls'=>'text-yellow-700 bg-yellow-50 border-yellow-200'],
                ['label'=>'Absents',    'val'=>$this->statistiques['biens_absents'],                'cls'=>'text-red-700    bg-red-50    border-red-200'],
                ['label'=>'Détériorés', 'val'=>$this->statistiques['biens_deteriores'],             'cls'=>'text-orange-700 bg-orange-50 border-orange-200'],
                ['label'=>'Défectueux', 'val'=>$this->statistiques['biens_defectueux'] ?? 0,       'cls'=>'text-amber-700  bg-amber-50  border-amber-200'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-lg border p-4 text-center <?php echo e($kpi['cls']); ?>">
                <p class="text-xs font-semibold uppercase tracking-wide opacity-70 mb-1"><?php echo e($kpi['label']); ?></p>
                <p class="text-2xl font-bold"><?php echo e($kpi['val']); ?></p>
                <p class="text-xs opacity-60 mt-0.5">
                    <?php echo e($this->statistiques['total_biens_scannes'] > 0 ? round($kpi['val'] / $this->statistiques['total_biens_scannes'] * 100, 1) : 0); ?>%
                </p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <div class="w-1 h-4 rounded-full bg-indigo-600"></div>
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">État physique constaté</h2>
        </div>
        <div class="p-5 grid grid-cols-3 gap-3">
            <?php
                $etatsPhysiques = [
                    ['key'=>'neuf',    'stat'=>'biens_neufs',      'cls'=>'text-green-700 bg-green-50 border-green-200'],
                    ['key'=>'bon',     'stat'=>'biens_bon_etat',   'cls'=>'text-blue-700  bg-blue-50  border-blue-200'],
                    ['key'=>'mauvais', 'stat'=>'biens_defectueux', 'cls'=>'text-amber-700 bg-amber-50 border-amber-200'],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $etatsPhysiques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $label = $etatsConstate[$ep['key']]['label'] ?? ucfirst($ep['key']);
                $val   = $this->statistiques[$ep['stat']] ?? 0;
                $pct   = $this->statistiques['total_biens_scannes'] > 0 ? round($val / $this->statistiques['total_biens_scannes'] * 100, 1) : 0;
            ?>
            <div class="rounded-lg border p-4 text-center <?php echo e($ep['cls']); ?>">
                <p class="text-xs font-semibold uppercase tracking-wide opacity-70 mb-1"><?php echo e($label); ?></p>
                <p class="text-2xl font-bold"><?php echo e($val); ?></p>
                <p class="text-xs opacity-60 mt-0.5"><?php echo e($pct); ?>%</p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex overflow-x-auto px-4" aria-label="Onglets">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    ['id'=>'resume',      'label'=>'Résumé'],
                    ['id'=>'emplacements','label'=>'Par localisation'],
                    ['id'=>'biens',       'label'=>'Immobilisations'],
                    ['id'=>'anomalies',   'label'=>'Anomalies'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button
                    @click="activeTab = '<?php echo e($tab['id']); ?>'; $wire.setActiveTab('<?php echo e($tab['id']); ?>')"
                    :class="activeTab === '<?php echo e($tab['id']); ?>'
                        ? 'border-indigo-600 text-indigo-600 bg-white'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-3.5 px-4 border-b-2 font-medium text-sm transition-colors -mb-px">
                    <?php echo e($tab['label']); ?>

                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </nav>
        </div>

        <div class="p-6">

            
            <div x-show="activeTab === 'resume'" x-transition class="space-y-6">

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->statistiques['par_agent'] ?? []) > 0): ?>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        Contribution par agent
                    </h3>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Localisations</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Biens scannés</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">% du total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->statistiques['par_agent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $pct = $this->statistiques['total_biens_scannes'] > 0 ? round($agent['biens_scannes']/$this->statistiques['total_biens_scannes']*100,1) : 0; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($agent['agent_name']); ?></td>
                                    <td class="px-4 py-3 text-center text-gray-600"><?php echo e($agent['localisations']); ?></td>
                                    <td class="px-4 py-3 text-center text-gray-600"><?php echo e(number_format($agent['biens_scannes'],0,',',' ')); ?></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 max-w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-indigo-500 rounded-full" style="width:<?php echo e($pct); ?>%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-indigo-700"><?php echo e($pct); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        Indicateurs secondaires
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                            ['label'=>"Taux d'anomalies", 'val'=>($this->statistiques['taux_anomalies']??0).'%', 'sub'=>'déplacés + absents', 'warn'=>($this->statistiques['taux_anomalies']??0)>15],
                            ['label'=>'Non scannés',       'val'=>$this->statistiques['biens_non_scannes']??0,    'sub'=>'manquants',          'warn'=>($this->statistiques['biens_non_scannes']??0)>0],
                            ['label'=>'Durée',             'val'=>($this->statistiques['duree_jours']??0).'j',    'sub'=>'inventaire',         'warn'=>false],
                            ['label'=>'Agents',            'val'=>$this->statistiques['nombre_agents']??0,        'sub'=>'ayant participé',    'warn'=>false],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ind): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border <?php echo e($ind['warn'] ? 'border-amber-200 bg-amber-50' : 'border-gray-200 bg-gray-50'); ?> p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1"><?php echo e($ind['label']); ?></p>
                            <p class="text-xl font-bold <?php echo e($ind['warn'] ? 'text-amber-700' : 'text-gray-900'); ?>"><?php echo e($ind['val']); ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?php echo e($ind['sub']); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div x-show="activeTab === 'emplacements'" x-transition style="display:none;" class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->detailParEmplacement) > 0): ?>
                <div>
                    <select wire:model.live="filterEmplacement"
                        class="block w-full md:w-72 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Toutes les localisations</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->detailParEmplacement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp['emplacement_id']); ?>"><?php echo e($emp['designation'] ?? $emp['code']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->detailParEmplacement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterEmplacement === 'all' || $filterEmplacement == $emp['emplacement_id']): ?>
                    <?php $tc = $emp['taux_conformite'] ?? 0; ?>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm"><?php echo e($emp['designation'] ?? $emp['code']); ?></p>
                                <p class="text-xs text-gray-500 mt-0.5"><?php echo e($emp['localisation'] ?? ''); ?> — <?php echo e($emp['total_trouves']); ?>/<?php echo e($emp['total_attendus']); ?> trouvés</p>
                            </div>
                            <span class="text-sm font-bold <?php echo e($tc >= 90 ? 'text-green-700' : ($tc >= 70 ? 'text-amber-700' : 'text-red-700')); ?>">
                                <?php echo e(round($tc,1)); ?>%
                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($emp['lignes'] ?? []) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Attendu</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Trouvé</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">État</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $emp['lignes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ligne): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $s = $ligne['statut_scan'] ?? '';
                                        $statutCls = $statutsScan[$s]['color'] ?? 'bg-gray-100 text-gray-700';
                                        $statutLbl = $statutsScan[$s]['label'] ?? $s;
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 font-medium text-gray-900"><?php echo e($ligne['code'] ?? '—'); ?></td>
                                        <td class="px-4 py-2.5 text-gray-700"><?php echo e(Str::limit($ligne['designation'] ?? '—', 45)); ?></td>
                                        <td class="px-4 py-2.5 text-center text-gray-600"><?php echo e($ligne['attendu'] ?? 1); ?></td>
                                        <td class="px-4 py-2.5 text-center text-gray-600"><?php echo e($ligne['trouve'] ?? 0); ?></td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($statutCls); ?>"><?php echo e($statutLbl); ?></span>
                                        </td>
                                        <td class="px-4 py-2.5 text-center text-gray-600 text-xs"><?php echo e($ligne['etat'] ?? '—'); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="px-4 py-5 text-sm text-gray-400 italic">Aucune immobilisation.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php else: ?>
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-4 text-sm text-amber-800">
                    Aucune donnée par localisation disponible.
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div x-show="activeTab === 'biens'" x-transition style="display:none;" class="space-y-4">

                
                <div class="flex gap-1 p-1 bg-gray-100 rounded-lg w-fit">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        ['id'=>'presents',   'label'=>'Présents',   'count'=>count($this->biensPresents)],
                        ['id'=>'deplaces',   'label'=>'Déplacés',   'count'=>count($this->biensDeplaces)],
                        ['id'=>'absents',    'label'=>'Absents',    'count'=>count($this->biensAbsents)],
                        ['id'=>'defectueux', 'label'=>'Défectueux', 'count'=>count($this->biensDefectueux)],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        @click="sousOnglet = '<?php echo e($st['id']); ?>'"
                        :class="sousOnglet === '<?php echo e($st['id']); ?>' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all whitespace-nowrap">
                        <?php echo e($st['label']); ?>

                        <span class="ml-1 text-xs opacity-60">(<?php echo e($st['count']); ?>)</span>
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div x-show="sousOnglet === 'presents'" x-transition style="display:none;">
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">État physique</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->biensPresents->take(50); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $eKey  = $scan->etat_constate ?? 'bon';
                                    $eCls  = $etatsConstate[$eKey]['color'] ?? 'bg-gray-100 text-gray-700';
                                    $eLbl  = $etatsConstate[$eKey]['label'] ?? $scan->etat_constate_label;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($scan->code_inventaire); ?></td>
                                    <td class="px-4 py-3 text-gray-700"><?php echo e(Str::limit($scan->designation, 50)); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($eCls); ?>"><?php echo e($eLbl); ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs"><?php echo e($scan->localisation_code ?? ($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? '—')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->biensPresents) > 50): ?>
                    <p class="text-xs text-gray-400 mt-2">50 premiers résultats sur <?php echo e(count($this->biensPresents)); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div x-show="sousOnglet === 'deplaces'" x-transition style="display:none;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->biensDeplaces) > 0): ?>
                    <div class="mb-3 rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800 font-medium">
                        Mettre à jour la localisation permanente de ces <?php echo e(count($this->biensDeplaces)); ?> immobilisation(s).
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation prévue</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation réelle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->biensDeplaces->take(50); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($scan->code_inventaire); ?></td>
                                    <td class="px-4 py-3 text-gray-700"><?php echo e(Str::limit($scan->designation, 45)); ?></td>
                                    <td class="px-4 py-3 text-xs text-red-600 font-medium"><?php echo e($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-xs text-amber-700 font-semibold"><?php echo e($scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien déplacé.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div x-show="sousOnglet === 'absents'" x-transition style="display:none;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->biensAbsents) > 0): ?>
                    <div class="mb-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 font-medium">
                        <?php echo e(count($this->biensAbsents)); ?> immobilisation(s) absente(s) — une enquête est nécessaire.
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Catégorie</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Agent</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->biensAbsents->take(50); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($scan->code_inventaire); ?></td>
                                    <td class="px-4 py-3 text-gray-700"><?php echo e(Str::limit($scan->designation, 45)); ?></td>
                                    <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($scan->bien?->categorie?->Categorie ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-xs text-gray-600"><?php echo e($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($scan->agent?->users ?? $scan->agent?->name ?? '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien absent.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div x-show="sousOnglet === 'defectueux'" x-transition style="display:none;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->biensDefectueux) > 0): ?>
                    <div class="mb-3 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 font-medium">
                        <?php echo e(count($this->biensDefectueux)); ?> immobilisation(s) signalée(s) en mauvais état — décision de réparation ou mise au rebut requise.
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">État</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Localisation</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Commentaire</th>
                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Photo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->biensDefectueux->take(50); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $eKey = $scan->etat_constate ?? 'mauvais';
                                    $eCls = $etatsConstate[$eKey]['color'] ?? 'bg-amber-100 text-amber-800';
                                    $eLbl = $etatsConstate[$eKey]['label'] ?? $scan->etat_constate_label;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($scan->code_inventaire); ?></td>
                                    <td class="px-4 py-3 text-gray-700"><?php echo e(Str::limit($scan->designation, 40)); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($eCls); ?>"><?php echo e($eLbl); ?></span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600"><?php echo e($scan->localisationReelle?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                                    <td class="px-4 py-3 text-xs text-gray-500 italic"><?php echo e(Str::limit($scan->commentaire ?? '', 35) ?: '—'); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($scan->photo_path && $scan->photo_url): ?>
                                        <div x-data="{ open: false }" @keydown.escape.window="open = false" class="inline">
                                            <button @click="open = true" type="button">
                                                <img src="<?php echo e($scan->photo_url); ?>" alt="" class="w-10 h-10 object-cover rounded border border-gray-200 hover:border-indigo-400 transition cursor-pointer"
                                                    onerror="this.style.display='none'">
                                            </button>
                                            <div x-show="open" x-cloak @click.self="open = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100">
                                                <div class="relative">
                                                    <img src="<?php echo e($scan->photo_url); ?>" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-xl">
                                                    <button @click="open = false" class="absolute -top-10 right-0 text-white p-2 hover:bg-white/10 rounded-full transition">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-gray-300">—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(count($this->biensDefectueux) > 50): ?>
                    <p class="text-xs text-gray-400 mt-2">50 premiers résultats sur <?php echo e(count($this->biensDefectueux)); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                    <p class="text-sm text-gray-400 italic py-4">Aucun bien défectueux signalé.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div x-show="activeTab === 'anomalies'" x-transition style="display:none;" class="space-y-4">
                <?php $anomalies = $this->anomalies; ?>

                <?php if(count($anomalies['localisations_non_demarrees'] ?? []) > 0 || count($anomalies['taux_absence_eleve'] ?? []) > 0 || count($anomalies['biens_defectueux'] ?? []) > 0): ?>

                    <?php if(count($anomalies['localisations_non_demarrees'] ?? []) > 0): ?>
                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                        <h4 class="font-semibold text-yellow-800 text-sm mb-2">
                            Localisations non démarrées (<?php echo e(count($anomalies['localisations_non_demarrees'])); ?>)
                        </h4>
                        <ul class="space-y-1 text-sm text-yellow-700 list-disc list-inside">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['localisations_non_demarrees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><strong><?php echo e($a['code']); ?></strong> — <?php echo e($a['designation']); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['taux_absence_eleve'] ?? []) > 0): ?>
                    <div class="rounded-lg bg-orange-50 border border-orange-200 p-4">
                        <h4 class="font-semibold text-orange-800 text-sm mb-2">
                            Taux d'absence élevé (<?php echo e(count($anomalies['taux_absence_eleve'])); ?>)
                        </h4>
                        <ul class="space-y-1 text-sm text-orange-700 list-disc list-inside">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['taux_absence_eleve']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><strong><?php echo e($a['code']); ?></strong> — <?php echo e($a['taux_absence']); ?>% absents (<?php echo e($a['biens_absents']); ?> immobilisations)</li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['biens_defectueux'] ?? []) > 0): ?>
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                        <h4 class="font-semibold text-amber-800 text-sm mb-2">
                            Immobilisations défectueuses (<?php echo e(count($anomalies['biens_defectueux'])); ?>)
                        </h4>
                        <ul class="space-y-1 text-sm text-amber-700 list-disc list-inside">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['biens_defectueux']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><strong><?php echo e($a['code']); ?></strong> — <?php echo e($a['designation']); ?> (<?php echo e($a['localisation']); ?>)</li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php else: ?>
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-900">Aucune anomalie détectée</p>
                    <p class="text-sm text-gray-500 mt-1">L'inventaire s'est déroulé sans anomalie majeure.</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>

    
    <div class="text-center text-xs text-gray-400 py-2">
        Rapport généré le <?php echo e(now()->format('d/m/Y à H:i')); ?> par <?php echo e(auth()->user()->users ?? auth()->user()->name ?? '—'); ?>

    </div>

</div>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/livewire/inventaires/rapport-inventaire.blade.php ENDPATH**/ ?>