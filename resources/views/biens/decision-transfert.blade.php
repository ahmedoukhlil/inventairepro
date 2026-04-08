<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Décision de Transfert – {{ $groupeId }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page { size: A4 portrait; margin: 0; }

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
            padding: 0 10mm 22mm 10mm;
            background: #fff;
        }

        /* ENTÊTE */
        .header img {
            width: calc(100% + 20mm);
            margin-left: -10mm;
            height: auto;
            display: block;
        }

        /* OBJET */
        .objet {
            margin: 14px 0 10px;
            font-size: 10.5pt;
            line-height: 1.6;
        }

        .objet-label {
            font-weight: bold;
            text-decoration: underline;
        }

        /* CORPS */
        .corps {
            font-size: 10.5pt;
            line-height: 1.8;
            text-align: justify;
            margin-bottom: 14px;
        }

        .corps p { margin-bottom: 8px; }

        /* TABLEAU UNIQUE */
        .immo-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 14px;
            table-layout: fixed;
        }

        .immo-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            word-break: break-word;
        }

        .immo-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: middle;
            font-size: 8pt;
            word-break: break-word;
            line-height: 1.2;
        }

        .tc { text-align: center; }
        .num { font-weight: bold; }

        /* LIGNE SÉPARATRICE */
        .separateur {
            border: none;
            border-top: 1px solid #bbb;
            margin: 14px 0;
        }

        /* SIGNATURE */
        .signature-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 28px;
        }

        .sig-bloc {
            text-align: center;
            width: 230px;
        }

        .sig-ville-date {
            font-size: 10pt;
            margin-bottom: 6px;
        }

        .sig-fonction {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.3;
            margin-bottom: 52px;
        }

        .sig-ligne {
            border-top: 1.5px solid #000;
            margin: 0 20px;
        }

        .sig-mention {
            font-size: 8.5pt;
            color: #555;
            margin-top: 4px;
            font-style: italic;
        }

        /* PIED FIXE */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #bbb;
            padding: 4px 10mm;
            font-size: 7.5pt;
            color: #666;
            text-align: center;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .page { margin: 0; }
        }
    </style>
</head>
<body>

{{-- Barre impression --}}
<div class="no-print" style="background:#1e293b;padding:10px 20px;display:flex;align-items:center;gap:12px;">
    <button onclick="window.print()"
        style="background:#4f46e5;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">
        🖨️ Imprimer
    </button>
    <button onclick="window.close()"
        style="background:#475569;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;cursor:pointer;">
        ✕ Fermer
    </button>
    <span style="color:#94a3b8;font-size:12px;">Décision de transfert – {{ $groupeId }}</span>
</div>

<div class="page">

    {{-- ENTÊTE --}}
    <div class="header">
        <img src="{{ asset('images/enteteapcm.png') }}" alt="Entête AGPCM">
    </div>

    {{-- OBJET --}}
    <div class="objet">
        <span class="objet-label">Objet :</span>
        Décision de transfert d'emplacement d'immobilisation(s) —
        Réf. <strong>{{ $groupeId }}</strong>
    </div>

    <hr class="separateur">

    {{-- CORPS --}}
    <div class="corps">
        <p>
            Par la présente décision, il est procédé, à compter du
            <strong>{{ $premier->date_transfert->format('d/m/Y') }}</strong>,
            au transfert des immobilisations ci-après désignées vers leur nouvel emplacement
            conformément aux nécessités de service et aux instructions de la Direction Générale.
        </p>

        @if($premier->raison)
        <p>
            <strong>Motif :</strong> {{ $premier->raison }}
        </p>
        @endif

    </div>

    {{-- TABLEAU UNIQUE --}}
    <table class="immo-table">
        <colgroup>
            <col style="width:4%">   {{-- N° --}}
            <col style="width:7%">   {{-- Num Ordre --}}
            <col style="width:19%">  {{-- Désignation --}}
            <col style="width:10%">  {{-- Départ : Localisation --}}
            <col style="width:13%">  {{-- Départ : Affectation --}}
            <col style="width:12%">  {{-- Départ : Emplacement --}}
            <col style="width:10%">  {{-- Dest : Localisation --}}
            <col style="width:13%">  {{-- Dest : Affectation --}}
            <col style="width:12%">  {{-- Dest : Emplacement --}}
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align:middle;background:#f0f0f0;">N°</th>
                <th rowspan="2" style="vertical-align:middle;background:#f0f0f0;">N° Ord.</th>
                <th rowspan="2" style="vertical-align:middle;background:#f0f0f0;">Désignation</th>
                <th colspan="3" style="background:#fef3c7;">&#9658; Emplacement de départ</th>
                <th colspan="3" style="background:#d1fae5;">&#10004; Emplacement de destination</th>
            </tr>
            <tr>
                <th style="background:#fef3c7;">Localisation</th>
                <th style="background:#fef3c7;">Affectation</th>
                <th style="background:#fef3c7;">Emplacement</th>
                <th style="background:#d1fae5;">Localisation</th>
                <th style="background:#d1fae5;">Affectation</th>
                <th style="background:#d1fae5;">Emplacement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transferts as $i => $t)
            <tr>
                <td class="tc">{{ $i + 1 }}</td>
                <td class="tc num">{{ $t->NumOrdre }}</td>
                <td>{{ $t->immobilisation->designation->designation ?? '–' }}</td>
                <td class="tc">{{ $t->ancien_localisation_libelle !== 'N/A' ? $t->ancien_localisation_libelle : '–' }}</td>
                <td class="tc">{{ $t->ancien_affectation_libelle !== 'N/A' ? $t->ancien_affectation_libelle : '–' }}</td>
                <td class="tc">{{ $t->ancien_emplacement_libelle }}</td>
                <td class="tc"><strong>{{ $t->nouveau_localisation_libelle !== 'N/A' ? $t->nouveau_localisation_libelle : '–' }}</strong></td>
                <td class="tc"><strong>{{ $t->nouveau_affectation_libelle !== 'N/A' ? $t->nouveau_affectation_libelle : '–' }}</strong></td>
                <td class="tc"><strong>{{ $t->nouveau_emplacement_libelle }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- NOTE BAS --}}
    <div class="corps" style="margin-top:10px;">
        <p>
            La présente décision vaut mise à jour du registre des immobilisations.
            Les services concernés sont chargés, chacun en ce qui le concerne,
            de son exécution et de sa bonne application.
        </p>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature-wrapper">
        <div class="sig-bloc">
            <div class="sig-ville-date">Nouakchott, le {{ $premier->date_transfert->format('d/m/Y') }}</div>
            <div class="sig-fonction">Le Directeur<br>des Moyens Généraux</div>
            <div class="sig-ligne"></div>
            <div class="sig-mention">(Signature et cachet)</div>
        </div>
    </div>

    {{-- PIED --}}
    <div class="footer">
        AGENCE DE GESTION DES PALAIS DE CONGRÈS DE MAURITANIE
        &nbsp;|&nbsp; Décision de Transfert Réf. {{ $groupeId }}
        &nbsp;|&nbsp; Imprimé le {{ now()->format('d/m/Y à H:i') }}
    </div>

</div>
</body>
</html>
