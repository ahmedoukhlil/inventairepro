<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Sortie Stock N° <?php echo e(str_pad($sortie->id, 4, '0', STR_PAD_LEFT)); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #e5e7eb;
        }

        /* ── PAGE A4 ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0 12mm 12mm 12mm;
            background: #fff;
        }

        /* ── ENTÊTE IMAGE ── */
        .header img {
            width: calc(100% + 24mm);
            margin-left: -12mm;
            height: auto;
            display: block;
        }

        /* ── TITRE ── */
        .bon-title {
            text-align: center;
            margin: 14px 0 12px;
        }

        .bon-title h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 2px solid #000;
            display: inline-block;
            padding: 5px 22px;
        }

        .bon-ref {
            margin-top: 5px;
            font-size: 10pt;
        }

        /* ── INFOS ── */
        .infos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }

        .infos-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .infos-table .lbl {
            font-weight: bold;
            white-space: nowrap;
            width: 1%;
        }

        .infos-table .val {
            min-width: 100px;
        }

        /* ── TABLEAU PRODUITS ── */
        .produits-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }

        .produits-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .produits-table td {
            border: 1px solid #000;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .produits-table tbody tr td { height: 24px; }

        .tc { text-align: center; }

        /* ── OBSERVATIONS ── */
        .obs-label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 3px;
        }

        .obs-box {
            border: 1px solid #000;
            padding: 6px 8px;
            min-height: 36px;
            font-size: 10pt;
            margin-bottom: 18px;
        }

        /* ── SIGNATURES ── */
        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 24px;
            gap: 20px;
        }

        .sig-block {
            flex: 1;
            text-align: center;
        }

        .sig-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            padding-bottom: 3px;
            margin-bottom: 48px;
            border-bottom: 1.5px solid #000;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin: 0 15px;
        }

        .sig-name {
            font-size: 9pt;
            margin-top: 3px;
        }

        /* ── PIED ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #aaa;
            padding: 5px 12mm;
            font-size: 7.5pt;
            color: #555;
            text-align: center;
            background: #fff;
        }

        /* ── IMPRESSION ── */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .page { margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>


<div class="no-print" style="background:#1e293b;padding:10px 20px;display:flex;align-items:center;gap:12px;">
    <button onclick="window.print()"
        style="background:#4f46e5;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">
        🖨️ Imprimer
    </button>
    <button onclick="window.close()"
        style="background:#475569;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;cursor:pointer;">
        ✕ Fermer
    </button>
    <span style="color:#94a3b8;font-size:12px;">Bon de Sortie Stock N° <?php echo e(str_pad($sortie->id, 4, '0', STR_PAD_LEFT)); ?></span>
</div>

<div class="page">

    
    <div class="header">
        <img src="<?php echo e(asset('images/enteteapcm.png')); ?>" alt="Entête AGPCM">
    </div>

    
    <div class="bon-title">
        <h1>Bon de Sortie Stock</h1>
        <div class="bon-ref">
            N° <strong><?php echo e(str_pad($sortie->id, 4, '0', STR_PAD_LEFT)); ?></strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Date : <strong><?php echo e($sortie->date_sortie->format('d/m/Y')); ?></strong>
        </div>
    </div>

    
    <table class="infos-table">
        <tr>
            <td class="lbl">Service émetteur :</td>
            <td class="val">Service Approvisionnements</td>
            <td class="lbl">Date de sortie :</td>
            <td class="val"><?php echo e($sortie->date_sortie->format('d/m/Y')); ?></td>
        </tr>
        <tr>
            <td class="lbl">Demandeur :</td>
            <td class="val"><?php echo e($sortie->demandeur->nom ?? '-'); ?></td>
            <td class="lbl">Service / Poste :</td>
            <td class="val"><?php echo e($sortie->demandeur->poste_service ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="lbl">Établi par :</td>
            <td class="val"><?php echo e($sortie->createur->users ?? 'Système'); ?></td>
            <td class="lbl">Date d'établissement :</td>
            <td class="val"><?php echo e($sortie->created_at->format('d/m/Y')); ?></td>
        </tr>
    </table>

    
    <table class="produits-table">
        <thead>
            <tr>
                <th style="width:5%">N°</th>
                <th style="width:32%">Désignation du produit</th>
                <th style="width:20%">Catégorie</th>
                <th style="width:10%">Unité</th>
                <th style="width:13%">Quantité sortie</th>
                <th style="width:20%">Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tc">1</td>
                <td><strong><?php echo e($sortie->produit->libelle ?? '-'); ?></strong></td>
                <td class="tc"><?php echo e($sortie->produit->categorie->libelle ?? '-'); ?></td>
                <td class="tc">–</td>
                <td class="tc"><strong><?php echo e($sortie->quantite); ?></strong></td>
                <td><?php echo e($sortie->observations ?? ''); ?></td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
            <tr>
                <td class="tc" style="color:#ccc;"><?php echo e($i + 2); ?></td>
                <td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    
    <div class="obs-label">Observations générales :</div>
    <div class="obs-box"><?php echo e($sortie->observations ?? ''); ?></div>

    
    <div class="signatures">
        <div class="sig-block">
            <div class="sig-title">Le Demandeur</div>
            <div class="sig-line"></div>
            <div class="sig-name"><?php echo e($sortie->demandeur->nom ?? ''); ?></div>
            <div class="sig-name" style="font-size:8.5pt;color:#666;"><?php echo e($sortie->demandeur->poste_service ?? ''); ?></div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Service Approvisionnements</div>
            <div class="sig-line"></div>
            <div class="sig-name"><?php echo e($sortie->createur->users ?? ''); ?></div>
        </div>
    </div>

    
    <div class="footer">
        AGENCE DE GESTION DES PALAIS DE CONGRÈS DE MAURITANIE &nbsp;|&nbsp;
        Bon de Sortie Stock N° <?php echo e(str_pad($sortie->id, 4, '0', STR_PAD_LEFT)); ?> &nbsp;|&nbsp;
        Imprimé le <?php echo e(now()->format('d/m/Y à H:i')); ?>

    </div>

</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\gesimmos\resources\views/stock/bon-sortie.blade.php ENDPATH**/ ?>