<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport d'Inventaire <?php echo e($inventaire->annee); ?></title>
<style>
/* ─── RESET ─────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ─── BASE ───────────────────────────────────────────────── */
html { font-size: 10.5pt; }
body {
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    color: #1c2333;
    background: #f0f2f5;
    line-height: 1.55;
}

/* ─── SCREEN WRAPPER ─────────────────────────────────────── */
.screen-shell {
    max-width: 900px;
    margin: 32px auto;
    background: #fff;
    box-shadow: 0 4px 40px rgba(0,0,0,.12);
    border-radius: 4px;
    overflow: hidden;
}

/* ─── ACTION BAR (screen only) ───────────────────────────── */
.action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 28px;
    background: #1c2333;
    gap: 12px;
}
.action-bar-title {
    color: #e2e8f0;
    font-size: 11pt;
    font-weight: 600;
    letter-spacing: .3px;
}
.action-bar-btns { display: flex; gap: 10px; }
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 6px;
    font-size: 10pt; font-weight: 600; cursor: pointer;
    text-decoration: none; border: none; line-height: 1;
}
.btn-ghost { background: rgba(255,255,255,.1); color: #e2e8f0; border: 1px solid rgba(255,255,255,.2); }
.btn-print { background: #3b5bdb; color: #fff; }
.btn:hover { opacity: .85; }

/* ─── PAGE (shared screen+print) ────────────────────────── */
.page { padding: 28mm 22mm 20mm; }

/* ─── COVER PAGE ─────────────────────────────────────────── */
.cover-page {
    min-height: 230mm;
    display: flex;
    flex-direction: column;
    padding: 0;
    position: relative;
}
.cover-header {
    background: #1c2333;
    padding: 28px 32px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cover-org-name {
    color: #fff;
    font-size: 13pt;
    font-weight: 700;
    letter-spacing: .5px;
}
.cover-org-sub {
    color: #94a3b8;
    font-size: 9pt;
    margin-top: 2px;
    letter-spacing: .3px;
}
.cover-logo-box {
    width: 52px; height: 52px;
    background: #3b5bdb;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.cover-logo-box svg { width: 28px; height: 28px; fill: #fff; }

.cover-band {
    height: 6px;
    background: linear-gradient(90deg, #3b5bdb 0%, #228be6 60%, #15aabf 100%);
}

.cover-body {
    flex: 1;
    padding: 40px 32px 32px;
    display: flex;
    flex-direction: column;
    gap: 28px;
}
.cover-type-label {
    font-size: 9pt;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #3b5bdb;
    border-bottom: 2px solid #3b5bdb;
    padding-bottom: 6px;
    display: inline-block;
}
.cover-main-title {
    font-size: 30pt;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin-top: 10px;
}
.cover-main-year {
    color: #3b5bdb;
}
.cover-main-sub {
    font-size: 13pt;
    color: #475569;
    margin-top: 6px;
}

.cover-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 999px;
    font-size: 9.5pt;
    font-weight: 700;
    margin-top: 4px;
}
.pill-termine { background: #fff3bf; color: #744210; border: 1px solid #f6c90e; }
.pill-cloture { background: #d3f9d8; color: #1a6b2e; border: 1px solid #69db7c; }

.cover-meta-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: #e2e8f0;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}
.cover-meta-cell {
    background: #fff;
    padding: 14px 18px;
}
.cover-meta-cell-label {
    font-size: 7.5pt;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #94a3b8;
    margin-bottom: 4px;
}
.cover-meta-cell-value {
    font-size: 10.5pt;
    font-weight: 700;
    color: #0f172a;
}

.cover-footer {
    padding: 16px 32px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 8.5pt;
    color: #94a3b8;
}

/* ─── SECTION PAGES ──────────────────────────────────────── */
.section-page { padding: 22mm 22mm 16mm; }

/* ─── RUNNING HEADER ─────────────────────────────────────── */
.running-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    border-bottom: 2px solid #1c2333;
    margin-bottom: 24px;
}
.running-header-left { font-size: 9pt; color: #64748b; }
.running-header-right {
    font-size: 9pt;
    font-weight: 700;
    color: #1c2333;
    letter-spacing: .3px;
}

/* ─── SECTION TITLE ──────────────────────────────────────── */
.sec-head {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 20px;
}
.sec-num {
    flex-shrink: 0;
    width: 34px; height: 34px;
    background: #1c2333;
    color: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11pt;
    font-weight: 800;
}
.sec-title-block {}
.sec-title {
    font-size: 14pt;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.sec-subtitle {
    font-size: 9pt;
    color: #64748b;
    margin-top: 2px;
}

/* ─── SYNTHESIS BOX ──────────────────────────────────────── */
.synth-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 5px solid #3b5bdb;
    border-radius: 0 8px 8px 0;
    padding: 18px 22px;
    font-size: 10pt;
    color: #334155;
    line-height: 1.7;
    margin-bottom: 24px;
}
.synth-box p + p { margin-top: 8px; }
.synth-box strong { color: #0f172a; }

/* ─── KPI ROW ────────────────────────────────────────────── */
.kpi-row {
    display: grid;
    gap: 10px;
    margin-bottom: 22px;
}
.kpi-row-4 { grid-template-columns: repeat(4,1fr); }
.kpi-row-5 { grid-template-columns: repeat(5,1fr); }
.kpi-row-3 { grid-template-columns: repeat(3,1fr); }
.kpi-row-2 { grid-template-columns: repeat(2,1fr); }

.kpi {
    border-radius: 8px;
    padding: 14px 12px 12px;
    text-align: center;
    border: 1px solid transparent;
}
.kpi-lbl { font-size: 7.5pt; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; opacity: .75; margin-bottom: 6px; }
.kpi-val { font-size: 22pt; font-weight: 800; line-height: 1; }
.kpi-sub { font-size: 8pt; opacity: .65; margin-top: 4px; }

.kpi-blue   { background:#eff6ff; border-color:#bfdbfe; }
.kpi-blue   .kpi-val { color:#1d4ed8; }
.kpi-indigo { background:#eef2ff; border-color:#c7d2fe; }
.kpi-indigo .kpi-val { color:#3730a3; }
.kpi-green  { background:#f0fdf4; border-color:#bbf7d0; }
.kpi-green  .kpi-val { color:#166534; }
.kpi-yellow { background:#fffbeb; border-color:#fde68a; }
.kpi-yellow .kpi-val { color:#92400e; }
.kpi-red    { background:#fef2f2; border-color:#fecaca; }
.kpi-red    .kpi-val { color:#991b1b; }
.kpi-orange { background:#fff7ed; border-color:#fed7aa; }
.kpi-orange .kpi-val { color:#9a3412; }
.kpi-slate  { background:#f8fafc; border-color:#e2e8f0; }
.kpi-slate  .kpi-val { color:#374151; }

/* ─── PROGRESS BAR ───────────────────────────────────────── */
.prog-block { margin-bottom: 20px; }
.prog-row { margin-bottom: 9px; }
.prog-lbl { display:flex; justify-content:space-between; font-size:9pt; color:#374151; margin-bottom:3px; }
.prog-lbl strong { color:#0f172a; }
.prog-bg { background:#e2e8f0; border-radius:999px; height:7px; overflow:hidden; }
.prog-fill { height:100%; border-radius:999px; }
.pfill-blue   { background:#3b5bdb; }
.pfill-green  { background:#22c55e; }
.pfill-yellow { background:#f59e0b; }
.pfill-red    { background:#ef4444; }

/* ─── TABLES ─────────────────────────────────────────────── */
.tbl-wrap { overflow-x:auto; margin-bottom:6px; border-radius:8px; border:1px solid #e2e8f0; }
table { width:100%; border-collapse:collapse; font-size:9pt; }
thead tr { background:#1c2333; }
thead th {
    color:#cbd5e1;
    font-weight:700;
    font-size:7.5pt;
    text-transform:uppercase;
    letter-spacing:.6px;
    padding:9px 11px;
    text-align:left;
    white-space:nowrap;
}
thead th.c { text-align:center; }
tbody td {
    padding:8px 11px;
    border-bottom:1px solid #f1f5f9;
    color:#1e293b;
    vertical-align:middle;
}
tbody td.c { text-align:center; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:nth-child(even) { background:#f8fafc; }
tfoot tr { background:#1c2333; }
tfoot td {
    padding:8px 11px;
    color:#e2e8f0;
    font-weight:700;
    font-size:9pt;
}
tfoot td.c { text-align:center; }

/* ─── TAG / BADGE ────────────────────────────────────────── */
.tag {
    display:inline-block;
    padding:2px 9px;
    border-radius:999px;
    font-size:7.5pt;
    font-weight:700;
    white-space:nowrap;
}
.tag-present  { background:#d3f9d8; color:#1a6b2e; }
.tag-deplace  { background:#fff3bf; color:#744210; }
.tag-absent   { background:#ffe3e3; color:#862e2e; }
.tag-deteriore{ background:#ffe8cc; color:#7c2d12; }
.tag-defect   { background:#fff3bf; color:#744210; }

/* ─── CONFORMITE BAR (inline) ───────────────────────────── */
.conf-bar { display:flex; align-items:center; gap:7px; justify-content:center; }
.conf-bar-track { width:52px; height:5px; background:#e2e8f0; border-radius:999px; overflow:hidden; }
.conf-bar-fill  { height:100%; border-radius:999px; }

/* ─── ALERT BANNER ───────────────────────────────────────── */
.alert {
    border-radius:8px;
    padding:12px 16px;
    margin-bottom:14px;
    font-size:9.5pt;
    border-left:4px solid transparent;
}
.alert-red    { background:#fff5f5; border-color:#f03e3e; color:#5c0b0b; }
.alert-yellow { background:#fffce1; border-color:#fab005; color:#5f3c04; }
.alert-orange { background:#fff4e6; border-color:#fd7e14; color:#6e2c00; }
.alert-green  { background:#ebfbee; border-color:#40c057; color:#163e20; }
.alert-title  { font-weight:700; margin-bottom:5px; }
.alert ul     { margin-left:18px; margin-top:4px; }
.alert li     { margin-bottom:2px; }

/* ─── ANALYSIS CARD ──────────────────────────────────────── */
.analysis-card {
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:8px;
    padding:18px 22px;
    font-size:9.5pt;
    color:#475569;
    line-height:1.65;
    margin-top:16px;
}
.analysis-card h4 {
    font-size:10.5pt;
    font-weight:700;
    color:#0f172a;
    margin-bottom:10px;
    display:flex;
    align-items:center;
    gap:7px;
}
.analysis-card p + p { margin-top:7px; }

/* ─── DIVIDER ────────────────────────────────────────────── */
.divider { border:none; border-top:1px solid #e2e8f0; margin:20px 0; }

/* ─── TWO-COL ────────────────────────────────────────────── */
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }

/* ─── PAGE BREAK ─────────────────────────────────────────── */
.pg-break { page-break-before:always; }
.no-break { page-break-inside:avoid; }

/* ─── RUNNING FOOTER ─────────────────────────────────────── */
.running-footer {
    margin-top:32px;
    padding-top:12px;
    border-top:1px solid #e2e8f0;
    display:flex;
    justify-content:space-between;
    font-size:7.5pt;
    color:#94a3b8;
}

/* ─── COLOR HELPERS ──────────────────────────────────────── */
.c-green  { color:#166534 !important; }
.c-red    { color:#991b1b !important; }
.c-yellow { color:#854d0e !important; }
.c-orange { color:#9a3412 !important; }
.c-slate  { color:#64748b !important; }
.fw7 { font-weight:700; }

/* ─── PRINT ──────────────────────────────────────────────── */
@media print {
    html { font-size:9.5pt; }
    body { background:#fff; }
    .action-bar { display:none !important; }
    .screen-shell { box-shadow:none; border-radius:0; margin:0; max-width:100%; }
    .section-page { padding:0; }
    .cover-page { min-height:auto; }
    .pg-break { page-break-before:always; }
    .no-break { page-break-inside:avoid; }
    thead { display:table-header-group; }
    tfoot { display:table-footer-group; }
    a { color:inherit !important; text-decoration:none !important; }
    .kpi-val { font-size:18pt; }
}

@page {
    size: A4;
    margin: 16mm 14mm 18mm;
    @bottom-center {
        content: "Rapport Inventaire <?php echo e($inventaire->annee); ?>  —  Page " counter(page) " / " counter(pages);
        font-size: 8pt;
        color: #94a3b8;
    }
}
</style>
</head>
<body>
<div class="screen-shell">


<div class="action-bar">
    <span class="action-bar-title">Rapport d'inventaire <?php echo e($inventaire->annee); ?></span>
    <div class="action-bar-btns">
        <a href="<?php echo e(route('inventaires.rapport', $inventaire)); ?>" class="btn btn-ghost">
            ← Retour
        </a>
        <button onclick="window.print()" class="btn btn-print">
            &#128438;&nbsp; Imprimer / PDF
        </button>
    </div>
</div>


<div class="cover-page">

    <div class="cover-header">
        <div>
            <div class="cover-org-name">GESIMMOS</div>
            <div class="cover-org-sub">Gestion des Immobilisations</div>
        </div>
        <div class="cover-logo-box">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2ZM9 17H7v-7h2Zm4 0h-2V7h2Zm4 0h-2v-4h2Z"/>
            </svg>
        </div>
    </div>

    <div class="cover-band"></div>

    <div class="cover-body">
        <div>
            <div class="cover-type-label">Rapport Officiel d'Inventaire Physique</div>
            <div class="cover-main-title">
                Inventaire <span class="cover-main-year"><?php echo e($inventaire->annee); ?></span>
            </div>
            <div class="cover-main-sub">Bilan complet des immobilisations</div>
            <div style="margin-top:10px;">
                <span class="cover-status-pill <?php echo e($inventaire->statut === 'cloture' ? 'pill-cloture' : 'pill-termine'); ?>">
                    <?php echo e($inventaire->statut === 'cloture' ? '✓ Clôturé' : '✓ Terminé'); ?>

                </span>
            </div>
        </div>

        <div class="cover-meta-grid">
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Date de début</div>
                <div class="cover-meta-cell-value"><?php echo e($inventaire->date_debut?->format('d/m/Y') ?? '—'); ?></div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Date de fin</div>
                <div class="cover-meta-cell-value"><?php echo e($inventaire->date_fin?->format('d/m/Y') ?? '—'); ?></div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Durée</div>
                <div class="cover-meta-cell-value"><?php echo e($statistiques['duree_jours']); ?> jour(s)</div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Responsable</div>
                <div class="cover-meta-cell-value"><?php echo e($inventaire->creator?->users ?? $inventaire->creator?->name ?? '—'); ?></div>
            </div>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Agents mobilisés</div>
                <div class="cover-meta-cell-value"><?php echo e($statistiques['nombre_agents']); ?></div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inventaire->statut === 'cloture'): ?>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Clôturé par</div>
                <div class="cover-meta-cell-value"><?php echo e($inventaire->closer?->users ?? $inventaire->closer?->name ?? '—'); ?></div>
            </div>
            <?php else: ?>
            <div class="cover-meta-cell">
                <div class="cover-meta-cell-label">Généré le</div>
                <div class="cover-meta-cell-value"><?php echo e(now()->format('d/m/Y')); ?></div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="cover-footer">
        <span>Document confidentiel — Usage interne uniquement</span>
        <span>Généré le <?php echo e(now()->format('d/m/Y à H:i')); ?> par <?php echo e(auth()->user()->users ?? auth()->user()->name ?? '—'); ?></span>
    </div>
</div>


<?php
    $taux      = $statistiques['taux_conformite'];
    $couverture = $statistiques['taux_couverture']
        ?? round(($statistiques['total_biens_scannes'] / max(1,$statistiques['total_biens_attendus'])) * 100, 1);
    if ($taux >= 95)      { $apprec = 'excellent';        $apprColor = '#166534'; }
    elseif ($taux >= 85)  { $apprec = 'satisfaisant';     $apprColor = '#1d4ed8'; }
    elseif ($taux >= 70)  { $apprec = 'moyen';            $apprColor = '#c2410c'; }
    else                  { $apprec = 'insuffisant';      $apprColor = '#991b1b'; }
?>

<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">1. Synthèse exécutive</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">1</div>
        <div class="sec-title-block">
            <div class="sec-title">Synthèse exécutive</div>
            <div class="sec-subtitle">Vue d'ensemble et appréciation générale de l'inventaire</div>
        </div>
    </div>

    <div class="synth-box">
        <p>
            L'inventaire physique <strong><?php echo e($inventaire->annee); ?></strong> a porté sur
            <strong><?php echo e(number_format($statistiques['total_biens_attendus'], 0, ',', '\u{202F}')); ?> immobilisation(s)</strong>
            réparties sur <strong><?php echo e($statistiques['total_localisations']); ?> localisation(s)</strong>,
            mené par <strong><?php echo e($statistiques['nombre_agents']); ?> agent(s)</strong>
            sur <strong><?php echo e($statistiques['duree_jours']); ?> jour(s)</strong>
            (du <?php echo e($inventaire->date_debut?->format('d/m/Y') ?? '—'); ?> au <?php echo e($inventaire->date_fin?->format('d/m/Y') ?? '—'); ?>).
        </p>
        <p>
            Le taux de couverture s'établit à <strong><?php echo e($couverture); ?>%</strong>
            (<?php echo e(number_format($statistiques['total_biens_scannes'], 0, ',', '\u{202F}')); ?> immobilisations vérifiées).
            Le taux de conformité atteint <strong style="color:<?php echo e($apprColor); ?>"><?php echo e($taux); ?>%</strong>,
            apprécié comme <strong style="color:<?php echo e($apprColor); ?>"><?php echo e($apprec); ?></strong>.
        </p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_absents'] > 0 || $statistiques['biens_deplaces'] > 0 || $statistiques['biens_defectueux'] > 0): ?>
        <p>
            Des anomalies ont été relevées :
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_absents'] > 0): ?><strong><?php echo e($statistiques['biens_absents']); ?> absence(s)</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_deplaces'] > 0): ?>, <strong><?php echo e($statistiques['biens_deplaces']); ?> déplacement(s)</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_defectueux'] > 0): ?>, <strong><?php echo e($statistiques['biens_defectueux']); ?> bien(s) défectueux</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>.
            Ces éléments nécessitent un suivi particulier.
        </p>
        <?php else: ?>
        <p><strong>Aucune anomalie majeure détectée.</strong> L'inventaire s'est déroulé dans de bonnes conditions.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="kpi-row kpi-row-4 no-break">
        <div class="kpi kpi-indigo">
            <div class="kpi-lbl">Taux de conformité</div>
            <div class="kpi-val"><?php echo e($taux); ?>%</div>
            <div class="kpi-sub">Biens présents / attendus</div>
        </div>
        <div class="kpi kpi-blue">
            <div class="kpi-lbl">Taux de couverture</div>
            <div class="kpi-val"><?php echo e($couverture); ?>%</div>
            <div class="kpi-sub"><?php echo e(number_format($statistiques['total_biens_scannes'],0,',','\u{202F}')); ?> / <?php echo e(number_format($statistiques['total_biens_attendus'],0,',','\u{202F}')); ?></div>
        </div>
        <div class="kpi <?php echo e(($statistiques['taux_absence'] ?? 0) > 10 ? 'kpi-red' : 'kpi-slate'); ?>">
            <div class="kpi-lbl">Taux d'absence</div>
            <div class="kpi-val"><?php echo e($statistiques['taux_absence'] ?? 0); ?>%</div>
            <div class="kpi-sub"><?php echo e($statistiques['biens_absents']); ?> absent(s)</div>
        </div>
        <div class="kpi kpi-slate">
            <div class="kpi-lbl">Durée</div>
            <div class="kpi-val"><?php echo e($statistiques['duree_jours']); ?>j</div>
            <div class="kpi-sub"><?php echo e($statistiques['nombre_agents']); ?> agent(s)</div>
        </div>
    </div>

    
    <div class="prog-block no-break">
        <?php $barres = [
            ['label'=>'Conformité (biens présents)',    'val'=>$taux,       'cl'=> $taux >= 85 ? 'pfill-green' : ($taux >= 70 ? 'pfill-yellow' : 'pfill-red')],
            ['label'=>'Couverture (biens scannés)',      'val'=>$couverture, 'cl'=>'pfill-blue'],
            ['label'=>'Progression localisations',      'val'=>$statistiques['progression_globale'] ?? 0, 'cl'=>'pfill-green'],
        ]; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $barres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="prog-row">
            <div class="prog-lbl">
                <span><?php echo e($b['label']); ?></span>
                <strong><?php echo e(round($b['val'],1)); ?>%</strong>
            </div>
            <div class="prog-bg">
                <div class="prog-fill <?php echo e($b['cl']); ?>" style="width:<?php echo e(min(100,$b['val'])); ?>%"></div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>


<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">2. Répartition des résultats</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">2</div>
        <div class="sec-title-block">
            <div class="sec-title">Répartition des résultats de vérification</div>
            <div class="sec-subtitle">Détail par statut de scan et état physique constaté</div>
        </div>
    </div>

    <div class="kpi-row kpi-row-5 no-break" style="margin-bottom:8px;">
        <?php $total = $statistiques['total_biens_scannes']; ?>
        <div class="kpi kpi-green">
            <div class="kpi-lbl">Présents</div>
            <div class="kpi-val"><?php echo e(number_format($statistiques['biens_presents'],0,',','\u{202F}')); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round($statistiques['biens_presents']/$total*100,1) : 0); ?>% des vérifiés</div>
        </div>
        <div class="kpi kpi-yellow">
            <div class="kpi-lbl">Déplacés</div>
            <div class="kpi-val"><?php echo e(number_format($statistiques['biens_deplaces'],0,',','\u{202F}')); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round($statistiques['biens_deplaces']/$total*100,1) : 0); ?>%</div>
        </div>
        <div class="kpi kpi-red">
            <div class="kpi-lbl">Absents</div>
            <div class="kpi-val"><?php echo e(number_format($statistiques['biens_absents'],0,',','\u{202F}')); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round($statistiques['biens_absents']/$total*100,1) : 0); ?>%</div>
        </div>
        <div class="kpi kpi-orange">
            <div class="kpi-lbl">Détériorés</div>
            <div class="kpi-val"><?php echo e(number_format($statistiques['biens_deteriores'],0,',','\u{202F}')); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round($statistiques['biens_deteriores']/$total*100,1) : 0); ?>%</div>
        </div>
        <div class="kpi kpi-yellow">
            <div class="kpi-lbl">Défectueux</div>
            <div class="kpi-val"><?php echo e(number_format($statistiques['biens_defectueux'],0,',','\u{202F}')); ?></div>
            <div class="kpi-sub">signalés via scan</div>
        </div>
    </div>

    <hr class="divider">

    <div style="margin-bottom:10px;font-size:9pt;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;">
        État physique constaté
    </div>
    <div class="kpi-row kpi-row-3 no-break">
        <div class="kpi kpi-green">
            <div class="kpi-lbl"><?php echo e($etatsConstate['neuf']['label'] ?? 'Neuf'); ?></div>
            <div class="kpi-val"><?php echo e($statistiques['biens_neufs'] ?? 0); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round(($statistiques['biens_neufs']??0)/$total*100,1) : 0); ?>% des vérifiés</div>
        </div>
        <div class="kpi kpi-blue">
            <div class="kpi-lbl"><?php echo e($etatsConstate['bon']['label'] ?? 'Bon état'); ?></div>
            <div class="kpi-val"><?php echo e($statistiques['biens_bon_etat'] ?? 0); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round(($statistiques['biens_bon_etat']??0)/$total*100,1) : 0); ?>%</div>
        </div>
        <div class="kpi kpi-yellow">
            <div class="kpi-lbl"><?php echo e($etatsConstate['mauvais']['label'] ?? 'Défectueux'); ?></div>
            <div class="kpi-val"><?php echo e($statistiques['biens_defectueux'] ?? 0); ?></div>
            <div class="kpi-sub"><?php echo e($total>0 ? round(($statistiques['biens_defectueux']??0)/$total*100,1) : 0); ?>%</div>
        </div>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>


<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">3. Résultats par localisation</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">3</div>
        <div class="sec-title-block">
            <div class="sec-title">Résultats par localisation</div>
            <div class="sec-subtitle">Récapitulatif des <?php echo e($parLocalisation->count()); ?> localisation(s) inventoriée(s)</div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Désignation</th>
                    <th>Agent</th>
                    <th class="c">Attendus</th>
                    <th class="c">Scannés</th>
                    <th class="c">Présents</th>
                    <th class="c">Déplacés</th>
                    <th class="c">Absents</th>
                    <th class="c">Détériorés</th>
                    <th class="c">Conformité</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $parLocalisation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="no-break">
                    <td><strong><?php echo e($loc['code']); ?></strong></td>
                    <td><?php echo e(Str::limit($loc['designation'], 32)); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($loc['agent']); ?></td>
                    <td class="c"><?php echo e($loc['attendus']); ?></td>
                    <td class="c"><?php echo e($loc['scannes']); ?></td>
                    <td class="c fw7 c-green"><?php echo e($loc['presents']); ?></td>
                    <td class="c c-yellow"><?php echo e($loc['deplaces']); ?></td>
                    <td class="c <?php echo e($loc['absents']>0 ? 'fw7 c-red' : 'c-slate'); ?>"><?php echo e($loc['absents']); ?></td>
                    <td class="c c-orange"><?php echo e($loc['deteriores']); ?></td>
                    <td class="c">
                        <div class="conf-bar">
                            <div class="conf-bar-track">
                                <div class="conf-bar-fill" style="width:<?php echo e(min(100,$loc['taux_conformite'])); ?>%;background:<?php echo e($loc['taux_conformite']>=90?'#22c55e':($loc['taux_conformite']>=70?'#f59e0b':'#ef4444')); ?>;"></div>
                            </div>
                            <span class="fw7" style="font-size:8.5pt;color:<?php echo e($loc['taux_conformite']>=90?'#166534':($loc['taux_conformite']>=70?'#854d0e':'#991b1b')); ?>"><?php echo e($loc['taux_conformite']); ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL GÉNÉRAL</td>
                    <td class="c"><?php echo e($parLocalisation->sum('attendus')); ?></td>
                    <td class="c"><?php echo e($parLocalisation->sum('scannes')); ?></td>
                    <td class="c"><?php echo e($parLocalisation->sum('presents')); ?></td>
                    <td class="c"><?php echo e($parLocalisation->sum('deplaces')); ?></td>
                    <td class="c"><?php echo e($parLocalisation->sum('absents')); ?></td>
                    <td class="c"><?php echo e($parLocalisation->sum('deteriores')); ?></td>
                    <td class="c"><?php echo e($statistiques['taux_conformite']); ?>%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php
        $locBasses = $parLocalisation->filter(fn($l) => $l['taux_conformite'] < 70 && $l['attendus'] > 0)->sortBy('taux_conformite');
        $locHautes = $parLocalisation->filter(fn($l) => $l['taux_conformite'] >= 95)->count();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locBasses->count() > 0): ?>
    <div class="alert alert-red no-break" style="margin-top:14px;">
        <div class="alert-title">Localisations à faible conformité (&lt; 70%)</div>
        <ul>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locBasses->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><strong><?php echo e($l['code']); ?></strong> — <?php echo e(Str::limit($l['designation'],40)); ?> : <?php echo e($l['taux_conformite']); ?>% (<?php echo e($l['absents']); ?> absent(s))</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locHautes > 0): ?>
    <div class="alert alert-green no-break" style="margin-top:8px;">
        <div class="alert-title"><?php echo e($locHautes); ?> localisation(s) avec une conformité excellente (≥ 95%)</div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($parCategorie->count() > 0): ?>
<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">4. Répartition par catégorie</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">4</div>
        <div class="sec-title-block">
            <div class="sec-title">Répartition par catégorie d'immobilisation</div>
            <div class="sec-subtitle"><?php echo e($parCategorie->count()); ?> catégorie(s) recensée(s)</div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th class="c">Total vérifié</th>
                    <th class="c">Présents</th>
                    <th class="c">Déplacés</th>
                    <th class="c">Absents</th>
                    <th class="c">Défectueux</th>
                    <th class="c">% Conformité</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $parCategorie; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $pct = $cat['total']>0 ? round($cat['presents']/$cat['total']*100,1) : 0; ?>
                <tr>
                    <td><strong><?php echo e($cat['categorie']); ?></strong></td>
                    <td class="c"><?php echo e($cat['total']); ?></td>
                    <td class="c fw7 c-green"><?php echo e($cat['presents']); ?></td>
                    <td class="c c-yellow"><?php echo e($cat['deplaces']); ?></td>
                    <td class="c <?php echo e($cat['absents']>0?'fw7 c-red':'c-slate'); ?>"><?php echo e($cat['absents']); ?></td>
                    <td class="c c-orange"><?php echo e($cat['defectueux']); ?></td>
                    <td class="c">
                        <div class="conf-bar">
                            <div class="conf-bar-track">
                                <div class="conf-bar-fill" style="width:<?php echo e(min(100,$pct)); ?>%;background:<?php echo e($pct>=85?'#22c55e':($pct>=70?'#f59e0b':'#ef4444')); ?>;"></div>
                            </div>
                            <span class="fw7" style="font-size:8.5pt;color:<?php echo e($pct>=85?'#166534':($pct>=70?'#854d0e':'#991b1b')); ?>"><?php echo e($pct); ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="c"><?php echo e($parCategorie->sum('total')); ?></td>
                    <td class="c"><?php echo e($parCategorie->sum('presents')); ?></td>
                    <td class="c"><?php echo e($parCategorie->sum('deplaces')); ?></td>
                    <td class="c"><?php echo e($parCategorie->sum('absents')); ?></td>
                    <td class="c"><?php echo e($parCategorie->sum('defectueux')); ?></td>
                    <td class="c"><?php echo e($statistiques['taux_conformite']); ?>%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($statistiques['par_agent'] ?? []) > 0): ?>
<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">5. Contribution par agent</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">5</div>
        <div class="sec-title-block">
            <div class="sec-title">Contribution par agent</div>
            <div class="sec-subtitle">Répartition de la charge de travail entre les <?php echo e($statistiques['nombre_agents']); ?> agent(s)</div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>Agent</th>
                    <th class="c">Localisations</th>
                    <th class="c">Biens scannés</th>
                    <th class="c">% du total</th>
                    <th class="c">Moy. / localisation</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statistiques['par_agent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $pct = $statistiques['total_biens_scannes'] > 0
                        ? round($agent['biens_scannes']/$statistiques['total_biens_scannes']*100,1)
                        : 0;
                    $moy = $agent['localisations'] > 0
                        ? round($agent['biens_scannes']/$agent['localisations'],1)
                        : 0;
                ?>
                <tr>
                    <td><strong><?php echo e($agent['agent_name']); ?></strong></td>
                    <td class="c"><?php echo e($agent['localisations']); ?></td>
                    <td class="c"><?php echo e(number_format($agent['biens_scannes'],0,',','\u{202F}')); ?></td>
                    <td class="c">
                        <div class="conf-bar">
                            <div class="conf-bar-track" style="width:70px;">
                                <div class="conf-bar-fill pfill-blue" style="width:<?php echo e($pct); ?>%;"></div>
                            </div>
                            <span class="fw7" style="font-size:8.5pt;"><?php echo e($pct); ?>%</span>
                        </div>
                    </td>
                    <td class="c c-slate"><?php echo e($moy); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensAbsents->count() > 0): ?>
<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">6. Immobilisations absentes</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">6</div>
        <div class="sec-title-block">
            <div class="sec-title">Immobilisations absentes</div>
            <div class="sec-subtitle"><?php echo e($biensAbsents->count()); ?> immobilisation(s) non retrouvée(s) lors de l'inventaire</div>
        </div>
    </div>

    <div class="alert alert-red no-break">
        <div class="alert-title">Action requise</div>
        Une enquête doit être diligentée pour chacune des immobilisations ci-dessous.
        Vérifier les registres de cession, transfert ou mise au rebut.
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th class="c">#</th>
                    <th>Code inventaire</th>
                    <th>Désignation</th>
                    <th>Catégorie</th>
                    <th>Localisation prévue</th>
                    <th>Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensAbsents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="c c-slate" style="font-size:8pt;"><?php echo e($i+1); ?></td>
                    <td><strong><?php echo e($scan->code_inventaire); ?></strong></td>
                    <td><?php echo e(Str::limit($scan->designation, 42)); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($scan->bien?->categorie?->Categorie ?? '—'); ?></td>
                    <td style="font-size:8.5pt;"><?php echo e($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($scan->agent?->users ?? $scan->agent?->name ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensDeplaces->count() > 0): ?>
<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">7. Immobilisations déplacées</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">7</div>
        <div class="sec-title-block">
            <div class="sec-title">Immobilisations déplacées</div>
            <div class="sec-subtitle"><?php echo e($biensDeplaces->count()); ?> immobilisation(s) trouvée(s) hors localisation habituelle</div>
        </div>
    </div>

    <div class="alert alert-yellow no-break">
        <div class="alert-title">Mise à jour requise</div>
        La localisation permanente de chaque bien ci-dessous doit être actualisée dans le système d'information.
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th class="c">#</th>
                    <th>Code inventaire</th>
                    <th>Désignation</th>
                    <th>Localisation prévue</th>
                    <th>Localisation réelle</th>
                    <th>Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensDeplaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="c c-slate" style="font-size:8pt;"><?php echo e($i+1); ?></td>
                    <td><strong><?php echo e($scan->code_inventaire); ?></strong></td>
                    <td><?php echo e(Str::limit($scan->designation, 38)); ?></td>
                    <td class="c-red" style="font-size:8.5pt;"><?php echo e($scan->bien?->emplacement?->localisation?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                    <td class="fw7 c-orange" style="font-size:8.5pt;"><?php echo e($scan->localisationReelle?->CodeLocalisation ?? $scan->localisationReelle?->Localisation ?? '—'); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($scan->agent?->users ?? $scan->agent?->name ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biensDefectueux->count() > 0): ?>
<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">8. Immobilisations défectueuses</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">8</div>
        <div class="sec-title-block">
            <div class="sec-title">Immobilisations signalées défectueuses</div>
            <div class="sec-subtitle"><?php echo e($biensDefectueux->count()); ?> immobilisation(s) en mauvais état constaté</div>
        </div>
    </div>

    <div class="alert alert-orange no-break">
        <div class="alert-title">Décision requise</div>
        Chaque bien ci-dessous doit faire l'objet d'une décision de réparation ou de mise au rebut.
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th class="c">#</th>
                    <th>Code inventaire</th>
                    <th>Désignation</th>
                    <th>Catégorie</th>
                    <th>Localisation</th>
                    <th>Commentaire</th>
                    <th>Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $biensDefectueux; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="c c-slate" style="font-size:8pt;"><?php echo e($i+1); ?></td>
                    <td><strong><?php echo e($scan->code_inventaire); ?></strong></td>
                    <td><?php echo e(Str::limit($scan->designation, 32)); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($scan->bien?->categorie?->Categorie ?? '—'); ?></td>
                    <td style="font-size:8.5pt;"><?php echo e($scan->localisationReelle?->CodeLocalisation ?? $scan->localisation_code ?? '—'); ?></td>
                    <td class="c-slate" style="font-size:8pt;font-style:italic;"><?php echo e(Str::limit($scan->commentaire ?? '', 35) ?: '—'); ?></td>
                    <td class="c-slate" style="font-size:8.5pt;"><?php echo e($scan->agent?->users ?? $scan->agent?->name ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="running-footer">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel</span>
        <span><?php echo e(now()->format('d/m/Y')); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="section-page pg-break">

    <div class="running-header">
        <span class="running-header-left">Rapport Inventaire <?php echo e($inventaire->annee); ?> — GESIMMOS</span>
        <span class="running-header-right">9. Anomalies et recommandations</span>
    </div>

    <div class="sec-head">
        <div class="sec-num">9</div>
        <div class="sec-title-block">
            <div class="sec-title">Anomalies détectées et recommandations</div>
            <div class="sec-subtitle">Synthèse des points d'attention et plan d'action suggéré</div>
        </div>
    </div>

    <?php
        $hasAnomalies = count($anomalies['localisations_non_demarrees'] ?? []) > 0
            || count($anomalies['localisations_bloquees'] ?? []) > 0
            || count($anomalies['taux_absence_eleve'] ?? []) > 0
            || count($anomalies['biens_deteriores'] ?? $anomalies['biens_defectueux'] ?? []) > 0;
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasAnomalies): ?>
    <div class="alert alert-green no-break">
        <div class="alert-title">Aucune anomalie détectée</div>
        L'inventaire s'est déroulé sans anomalie majeure. Toutes les procédures ont été respectées.
    </div>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['localisations_non_demarrees'] ?? []) > 0): ?>
        <div class="alert alert-yellow no-break">
            <div class="alert-title">Localisations non démarrées (<?php echo e(count($anomalies['localisations_non_demarrees'])); ?>)</div>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($anomalies['localisations_non_demarrees'], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><strong><?php echo e($a['code'] ?? ''); ?></strong> — <?php echo e($a['designation'] ?? ''); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($anomalies['taux_absence_eleve'] ?? []) > 0): ?>
        <div class="alert alert-orange no-break">
            <div class="alert-title">Localisations avec taux d'absence élevé (<?php echo e(count($anomalies['taux_absence_eleve'])); ?>)</div>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $anomalies['taux_absence_eleve']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><strong><?php echo e($a['code'] ?? ''); ?></strong> — <?php echo e($a['taux_absence'] ?? ''); ?>% d'absents (<?php echo e($a['biens_absents'] ?? 0); ?> immobilisations)</li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="analysis-card no-break">
        <h4>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#1c2333;border-radius:50%;color:#fff;font-size:9pt;font-weight:800;">✓</span>
            Recommandations
        </h4>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_absents'] > 0): ?>
        <p><strong>Biens absents :</strong> Diligenter une enquête pour les <?php echo e($statistiques['biens_absents']); ?> immobilisation(s) absente(s). Vérifier les registres de cession, de transfert et de mise au rebut. Procéder à une régularisation comptable si nécessaire.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_deplaces'] > 0): ?>
        <p><strong>Biens déplacés :</strong> Mettre à jour la localisation permanente des <?php echo e($statistiques['biens_deplaces']); ?> immobilisation(s) déplacée(s) dans le système d'information. Valider les transferts avec les responsables concernés.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['biens_defectueux'] > 0): ?>
        <p><strong>Biens défectueux :</strong> Soumettre les <?php echo e($statistiques['biens_defectueux']); ?> immobilisation(s) signalée(s) en mauvais état à l'examen d'une commission de réforme. Évaluer les coûts de réparation versus remplacement.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['taux_conformite'] < 85): ?>
        <p><strong>Taux de conformité :</strong> Le taux actuel de <?php echo e($statistiques['taux_conformite']); ?>% est inférieur au seuil recommandé de 85%. Un contrôle complémentaire ciblé sur les localisations à faible taux est conseillé.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statistiques['taux_conformite'] >= 95): ?>
        <p><strong>Bonne performance :</strong> Le taux de conformité de <?php echo e($statistiques['taux_conformite']); ?>% est excellent. Maintenir les bonnes pratiques de gestion et programmer le prochain inventaire selon le calendrier habituel.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$statistiques['biens_absents'] && !$statistiques['biens_deplaces'] && !$statistiques['biens_defectueux'] && $statistiques['taux_conformite'] >= 95): ?>
        <p>L'inventaire ne présente aucun point d'attention particulier. Toutes les immobilisations ont été vérifiées avec un taux de conformité excellent.</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div style="margin-top:40px;" class="no-break">
        <div style="font-size:9pt;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:16px;">
            Signatures et validation
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Responsable inventaire','Contrôleur interne','Direction']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="border-top:2px solid #1c2333;padding-top:10px;">
                <div style="font-size:8pt;font-weight:700;color:#1c2333;margin-bottom:36px;"><?php echo e($sig); ?></div>
                <div style="font-size:7.5pt;color:#94a3b8;">Nom et signature</div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="running-footer" style="margin-top:28px;">
        <span>GESIMMOS — Rapport Inventaire <?php echo e($inventaire->annee); ?></span>
        <span>Confidentiel — Usage interne</span>
        <span><?php echo e(now()->format('d/m/Y à H:i')); ?></span>
    </div>
</div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/inventaires/rapport-print.blade.php ENDPATH**/ ?>