<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Inventaire <?php echo e($inventaire->annee); ?></title>
    <style>
        @page { margin: 1.5cm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.4; color: #333; }

        .header, .footer {
            position: fixed;
            left: 0; right: 0;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }
        .header { top: -1.2cm; border-bottom: 1px solid #ccc; padding: 4px 0; }
        .footer { bottom: -1.2cm; border-top: 1px solid #ccc; padding: 4px 0; }
        .page-number:after { content: "Page " counter(page) " / " counter(pages); }

        .cover-page { text-align: center; padding: 40px 20px; page-break-after: always; }
        .cover-title { font-size: 16pt; font-weight: bold; margin-bottom: 6px; }
        .cover-subtitle { font-size: 11pt; color: #666; margin-bottom: 16px; }
        .status-badge { display: inline-block; padding: 6px 16px; font-weight: bold; font-size: 9pt; margin: 10px 0; }
        .status-conforme { background: #22c55e; color: white; }
        .status-non-conforme { background: #ef4444; color: white; }
        .cover-info { margin: 12px 0; font-size: 9pt; text-align: left; max-width: 360px; margin-left: auto; margin-right: auto; padding: 10px 14px; background: #f5f5f5; border: 1px solid #ddd; }
        .cover-info p { margin-bottom: 4px; }

        .stats-row { display: table; width: 100%; margin: 12px 0; page-break-inside: avoid; }
        .stat-card { display: table-cell; width: 25%; padding: 8px 6px; text-align: center; border: 1px solid #ddd; background: #fafafa; }
        .stat-number { font-size: 14pt; font-weight: bold; display: block; }
        .stat-number.primary { color: #333; }
        .stat-number.success { color: #16a34a; }
        .stat-number.warning { color: #ca8a04; }
        .stat-number.danger { color: #dc2626; }
        .stat-label { font-size: 7pt; color: #666; margin-top: 2px; }

        h1 { font-size: 12pt; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 4px; margin: 16px 0 10px 0; page-break-after: avoid; }
        h2 { font-size: 10pt; margin: 12px 0 6px 0; page-break-after: avoid; }

        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8pt; }
        th, td { padding: 5px 6px; border: 1px solid #ccc; text-align: left; }
        thead th { background: #e5e5e5; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        .text-right, th.text-right, td.text-right { text-align: right; }
        .text-center, th.text-center, td.text-center { text-align: center; }

        .conformite-badge { padding: 2px 6px; font-size: 7pt; }
        .conformite-excellent { background: #dcfce7; color: #166534; }
        .conformite-bon { background: #dbeafe; color: #1e40af; }
        .conformite-faible { background: #fef9c3; color: #854d0e; }
        .conformite-insuffisant { background: #fee2e2; color: #991b1b; }

        .emplacement-block { margin-bottom: 16px; page-break-inside: avoid; }
        .emplacement-title { font-size: 10pt; font-weight: bold; padding: 6px 8px; margin: 12px 0 4px 0; background: #f0f0f0; border: 1px solid #ddd; }
        .emplacement-resume { font-size: 7pt; color: #666; margin-bottom: 6px; }
        .emplacement-vide { font-size: 8pt; color: #999; font-style: italic; padding: 6px 0; }
        .table-detail th, .table-detail td { padding: 4px 5px; font-size: 7pt; }
        .etat-defectueux { color: #b45309; }
        .etat-ok { color: #166534; }

        .info-box, .warning-box, .danger-box, .success-box {
            padding: 8px 10px; margin: 8px 0; page-break-inside: avoid;
            border: 1px solid #ddd; border-left-width: 3px;
        }
        .info-box { background: #f8fafc; border-left-color: #64748b; }
        .warning-box { background: #fffbeb; border-left-color: #f59e0b; }
        .danger-box { background: #fef2f2; border-left-color: #ef4444; }
        .success-box { background: #f0fdf4; border-left-color: #22c55e; }

        .toc { page-break-after: always; }
        .toc ul { list-style: none; margin-left: 0; }
        .toc li { margin-bottom: 4px; padding: 3px 0; border-bottom: 1px dotted #ccc; }

        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }
        .mt-15 { margin-top: 12px; }
        .mb-15 { margin-bottom: 12px; }
        ul { margin: 6px 0 6px 18px; }
        li { margin-bottom: 2px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        Rapport inventaire <?php echo e($inventaire->annee); ?> | <?php echo e(now()->format('d/m/Y H:i')); ?>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="page-number"></div>
    </div>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <div class="cover-title">Rapport d'inventaire <?php echo e($inventaire->annee); ?></div>
        <div class="cover-subtitle"><?php echo e($inventaire->date_debut->format('d/m/Y')); ?> — <?php echo e($inventaire->date_fin ? $inventaire->date_fin->format('d/m/Y') : 'En cours'); ?></div>

        <?php
            $tauxConformite = $statistiques['taux_conformite'] ?? 0;
            $statusClass = $tauxConformite >= 95 ? 'status-conforme' : 'status-non-conforme';
            $statusText = $tauxConformite >= 95 ? 'Conforme' : 'À améliorer';
        ?>

        <div class="status-badge <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></div>

        <div class="cover-info">
            <p><strong>Conformité :</strong> <?php echo e(number_format($tauxConformite, 1)); ?>% | <strong>Couverture :</strong> <?php echo e($statistiques['taux_couverture'] ?? 0); ?>%</p>
            <p><strong>Attendues :</strong> <?php echo e(number_format($statistiques['total_biens_attendus'] ?? 0)); ?> | <strong>Scannées :</strong> <?php echo e(number_format($statistiques['total_biens_scannes'] ?? 0)); ?></p>
            <p><strong>Créé par :</strong> <?php echo e($inventaire->creator->name ?? 'N/A'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inventaire->closer): ?> | <strong>Clôturé par :</strong> <?php echo e($inventaire->closer->name); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
        </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="toc">
        <h1>Sommaire</h1>
        <ul>
            <li>1. Synthèse</li>
            <li>2. Résultats par emplacement</li>
            <li>3. Présentes / Déplacées / Absentes</li>
            <li>4. Anomalies</li>
        </ul>
    </div>

    <!-- SECTION 1: SYNTHÈSE -->
    <div class="page-break"></div>
    <h1>1. Synthèse</h1>

    <div class="stats-row no-break">
        <div class="stat-card">
            <span class="stat-number primary"><?php echo e(number_format($statistiques['total_biens_attendus'] ?? 0)); ?></span>
            <span class="stat-label">Attendues</span>
        </div>
        <div class="stat-card">
            <span class="stat-number success"><?php echo e(number_format($statistiques['biens_presents'] ?? 0)); ?></span>
            <span class="stat-label">Présentes</span>
        </div>
        <div class="stat-card">
            <span class="stat-number warning"><?php echo e(number_format($statistiques['biens_deplaces'] ?? 0)); ?></span>
            <span class="stat-label">Déplacées</span>
        </div>
        <div class="stat-card">
            <span class="stat-number danger"><?php echo e(number_format($statistiques['biens_absents'] ?? 0)); ?></span>
            <span class="stat-label">Absentes</span>
        </div>
    </div>

    <p class="mb-15">Conformité : <?php echo e(number_format($statistiques['taux_conformite'] ?? 0, 1)); ?>% | Couverture : <?php echo e($statistiques['taux_couverture'] ?? 0); ?>% | <?php echo e(count($statistiques['par_emplacement'] ?? [])); ?> emplacements</p>

    <!-- SECTION 2: RÉSULTATS PAR EMPLACEMENT (tableaux détaillés) -->
    <div class="page-break"></div>
    <h1>2. Résultats par emplacement</h1>

    <p class="mb-15">Tableau des immobilisations par emplacement : Attendu, Trouvé, Conformité, État.</p>

    <?php $detailParEmplacement = $detailParEmplacement ?? []; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($detailParEmplacement) > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $detailParEmplacement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="emplacement-block no-break">
            <h2 class="emplacement-title"><?php echo e($emp['designation'] ?? $emp['code']); ?> — <?php echo e($emp['localisation']); ?></h2>
            <p class="emplacement-resume">Résumé : <?php echo e($emp['total_trouves']); ?>/<?php echo e($emp['total_attendus']); ?> trouvées — Conformité <?php echo e($emp['taux_conformite']); ?>%</p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($emp['lignes'] ?? []) > 0): ?>
            <table class="table-emplacements table-detail">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Désignation</th>
                        <th class="text-center">Attendu</th>
                        <th class="text-center">Trouvé</th>
                        <th class="text-center">Conformité</th>
                        <th class="text-center">État</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $emp['lignes'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ligne): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $statut = $ligne['statut_scan'] ?? '';
                        $conformiteClass = $statut === 'present' ? 'conformite-excellent' : ($statut === 'absent' ? 'conformite-insuffisant' : ($statut === 'deplace' ? 'conformite-faible' : 'conformite-bon'));
                        $etatClass = ($ligne['etat'] ?? '') === 'Défectueuse' ? 'etat-defectueux' : 'etat-ok';
                    ?>
                    <tr>
                        <td><strong><?php echo e($ligne['code'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo e(Str::limit($ligne['designation'] ?? 'N/A', 35)); ?></td>
                        <td class="text-center"><?php echo e($ligne['attendu'] ?? 1); ?></td>
                        <td class="text-center"><?php echo e($ligne['trouve'] ?? 0); ?></td>
                        <td class="text-center">
                            <span class="conformite-badge <?php echo e($conformiteClass); ?>"><?php echo e($ligne['conformite'] ?? '-'); ?></span>
                        </td>
                        <td class="text-center <?php echo e($etatClass); ?>"><?php echo e($ligne['etat'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="emplacement-vide">Aucune immobilisation dans cet emplacement.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <p class="mt-15" style="font-size:7pt;color:#666;">Légende : Attendu=1 attendu | Trouvé=1 si sur place | Conformité : Présent/Absent/Déplacé | État : Neuf/Bon/Défectueuse</p>
    <?php else: ?>
    <div class="warning-box no-break">
        <p>Aucune donnée par emplacement disponible. Les inventaires peuvent utiliser le mode par localisation.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- SECTION 3: PRÉSENTES / DÉPLACÉES / ABSENTES -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($biensPresents) && $biensPresents->count() > 0): ?>
    <div class="page-break"></div>
    <h1>3. Présentes (<?php echo e($biensPresents->count()); ?>)</h1>
    <table class="table-emplacements">
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensPresents->take(80); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($bien['code'] ?? 'N/A'); ?></td>
                <td><?php echo e(Str::limit($bien['designation'] ?? 'N/A', 45)); ?></td>
                <td><?php echo e($bien['localisation'] ?? 'N/A'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensPresents->count() > 80): ?>
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : <?php echo e($biensPresents->count()); ?></em></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- SECTION 4: DÉPLACÉES -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($biensDeplaces) && $biensDeplaces->count() > 0): ?>
    <div class="page-break"></div>
    <h1>4. Déplacées (<?php echo e($biensDeplaces->count()); ?>)</h1>
    <table class="table-emplacements">
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Loc. prévue</th>
                <th>Loc. réelle</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensDeplaces->take(80); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($bien['code'] ?? 'N/A'); ?></td>
                <td><?php echo e(Str::limit($bien['designation'] ?? 'N/A', 40)); ?></td>
                <td><?php echo e($bien['localisation_prevue'] ?? 'N/A'); ?></td>
                <td><?php echo e($bien['localisation_reelle'] ?? 'N/A'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensDeplaces->count() > 80): ?>
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : <?php echo e($biensDeplaces->count()); ?></em></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- SECTION 5: ABSENTES -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($biensAbsents) && $biensAbsents->count() > 0): ?>
    <div class="page-break"></div>
    <h1>5. Absentes (<?php echo e($biensAbsents->count()); ?>)</h1>
    <table class="table-emplacements">
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensAbsents->take(80); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($bien['code'] ?? 'N/A'); ?></td>
                <td><?php echo e(Str::limit($bien['designation'] ?? 'N/A', 45)); ?></td>
                <td><?php echo e($bien['localisation'] ?? 'N/A'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensAbsents->count() > 80): ?>
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : <?php echo e($biensAbsents->count()); ?></em></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- SECTION 6: ANOMALIES -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($anomalies) || isset($recommendations)): ?>
    <div class="page-break"></div>
    <h1>6. Anomalies</h1>

    <?php
        $totalAnomalies = count($anomalies['localisations_non_demarrees'] ?? []) +
                         count($anomalies['taux_absence_eleve'] ?? []) +
                         count($anomalies['biens_defectueux'] ?? []);
    ?>

    <?php if($totalAnomalies > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['localisations_non_demarrees'] ?? []) > 0): ?>
        <h2>Emplacements non démarrés</h2>
        <div class="warning-box no-break">
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['localisations_non_demarrees']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($loc['code'] ?? 'N/A'); ?> — <?php echo e($loc['designation'] ?? 'N/A'); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['taux_absence_eleve'] ?? []) > 0): ?>
        <h2>Taux d'absence élevé</h2>
        <div class="warning-box no-break">
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['taux_absence_eleve']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($a['code'] ?? 'N/A'); ?> — <?php echo e($a['taux_absence'] ?? 0); ?>% absents (<?php echo e($a['biens_absents'] ?? 0); ?> immobilisations)</li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['biens_defectueux'] ?? []) > 0): ?>
        <h2>Immobilisations signalées défectueuses</h2>
        <div class="danger-box no-break">
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['biens_defectueux']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($b['code'] ?? 'N/A'); ?> — <?php echo e($b['designation'] ?? 'N/A'); ?> (<?php echo e($b['localisation'] ?? 'N/A'); ?>)</li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
    <p>Aucune anomalie détectée.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($recommendations) && (count($recommendations['corrections_immediates'] ?? []) > 0 || count($recommendations['ameliorations_organisationnelles'] ?? []) > 0)): ?>
    <h2>Recommandations</h2>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($recommendations['corrections_immediates'] ?? []) > 0): ?>
    <div class="danger-box no-break">
        <strong>Corrections immédiates :</strong>
        <ul>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recommendations['corrections_immediates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($r); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($recommendations['ameliorations_organisationnelles'] ?? []) > 0): ?>
    <div class="info-box no-break">
        <strong>Améliorations :</strong>
        <ul>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recommendations['ameliorations_organisationnelles']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($r); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/pdf/rapport-inventaire.blade.php ENDPATH**/ ?>