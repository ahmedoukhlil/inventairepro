<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $premiere = $sorties->first();
        $refBon   = $sorties->count() === 1
            ? 'N° ' . str_pad($premiere->id, 4, '0', STR_PAD_LEFT)
            : 'Groupe ' . strtoupper(substr($premiere->groupe_id ?? 'N/A', 0, 8));
    @endphp
    <title>Bon de Sortie Stock — {{ $refBon }}</title>
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

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0 12mm 12mm 12mm;
            background: #fff;
        }

        .header img {
            width: calc(100% + 24mm);
            margin-left: -12mm;
            height: auto;
            display: block;
        }

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
            background: #f3f4f6;
        }

        .produits-table td {
            border: 1px solid #000;
            padding: 7px 8px;
            vertical-align: middle;
        }

        .produits-table tbody tr td { height: 24px; }

        .tc { text-align: center; }

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

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .page { margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

{{-- Bouton impression --}}
<div class="no-print" style="background:#1e293b;padding:10px 20px;display:flex;align-items:center;gap:12px;">
    <button onclick="window.print()"
        style="background:#4f46e5;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">
        🖨️ Imprimer
    </button>
    <button onclick="history.back()"
        style="background:#475569;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;cursor:pointer;">
        ← Retour
    </button>
    <span style="color:#94a3b8;font-size:12px;">Bon de Sortie — {{ $refBon }}</span>
</div>

<div class="page">

    {{-- ── ENTÊTE ── --}}
    <div class="header">
        <img src="{{ asset('images/enteteapcm.png') }}" alt="Entête AGPCM">
    </div>

    {{-- ── TITRE ── --}}
    <div class="bon-title">
        <h1>Bon de Sortie Stock</h1>
        <div class="bon-ref">
            {{ $refBon }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Date : <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
        </div>
    </div>

    {{-- ── INFOS GÉNÉRALES ── --}}
    <table class="infos-table">
        <tr>
            <td class="lbl">Service émetteur :</td>
            <td class="val">Service Approvisionnements</td>
            <td class="lbl">Date de sortie :</td>
            <td class="val">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Demandeur :</td>
            <td class="val">{{ $demandeur->nom ?? '-' }}</td>
            <td class="lbl">Service / Poste :</td>
            <td class="val">{{ $demandeur->poste_service ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Établi par :</td>
            <td class="val">{{ $createur?->display_name ?? 'Système' }}</td>
            <td class="lbl">Date d'établissement :</td>
            <td class="val">{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- ── TABLEAU PRODUITS ── --}}
    <table class="produits-table">
        <thead>
            <tr>
                <th style="width:5%">N°</th>
                <th style="width:38%">Désignation du produit</th>
                <th style="width:22%">Catégorie</th>
                <th style="width:10%">Unité</th>
                <th style="width:13%">Qté sortie</th>
                <th style="width:12%">Magasin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sorties as $i => $s)
            <tr>
                <td class="tc">{{ $i + 1 }}</td>
                <td><strong>{{ $s->produit->libelle ?? '-' }}</strong></td>
                <td class="tc">{{ $s->produit->categorie->libelle ?? '-' }}</td>
                <td class="tc">–</td>
                <td class="tc"><strong>{{ $s->quantite }}</strong></td>
                <td class="tc">{{ $s->produit->magasin->magasin ?? '-' }}</td>
            </tr>
            @endforeach
            {{-- Lignes vides pour compléter jusqu'à 8 lignes minimum --}}
            @for($j = $sorties->count(); $j < 8; $j++)
            <tr>
                <td class="tc" style="color:#ddd;">{{ $j + 1 }}</td>
                <td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="font-weight:bold;text-align:right;border:1px solid #000;padding:6px 8px;">
                    Total articles : {{ $sorties->count() }}
                </td>
                <td class="tc" style="font-weight:bold;border:1px solid #000;">
                    {{ $sorties->sum('quantite') }}
                </td>
                <td style="border:1px solid #000;"></td>
            </tr>
        </tfoot>
    </table>

    {{-- ── OBSERVATIONS ── --}}
    @if($observations)
    <div class="obs-label">Observations :</div>
    <div class="obs-box">{{ $observations }}</div>
    @endif

    {{-- ── SIGNATURES ── --}}
    <div class="signatures">
        <div class="sig-block">
            <div class="sig-title">Le Demandeur</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $demandeur->nom ?? '' }}</div>
            <div class="sig-name" style="font-size:8.5pt;color:#666;">{{ $demandeur->poste_service ?? '' }}</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Service Approvisionnements</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $createur?->display_name ?? '' }}</div>
            @if($createur?->poste)
                <div class="sig-name" style="font-size:8.5pt;color:#666;">{{ $createur->poste }}</div>
            @endif
        </div>
    </div>

    {{-- ── PIED ── --}}
    <div class="footer">
        AGENCE DE GESTION DES PALAIS DE CONGRÈS DE MAURITANIE &nbsp;|&nbsp;
        Bon de Sortie {{ $refBon }} &nbsp;|&nbsp;
        Imprimé le {{ now()->format('d/m/Y à H:i') }}
    </div>

</div>

</body>
</html>