<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Inventaire {{ $inventaire->annee }}</title>
    <style>
        /* ============================================
           CONFIGURATION PAGE
           ============================================ */
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
            size: A4 portrait;
        }

        @page:first {
            margin-top: 3cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #1f2937;
        }

        /* ============================================
           HEADER & FOOTER
           ============================================ */
        .header {
            position: fixed;
            top: -1.5cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding: 5px 0;
            font-size: 8pt;
            color: #6b7280;
        }

        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding: 5px 0;
            font-size: 8pt;
            color: #6b7280;
        }

        .page-number:after {
            content: "Page " counter(page) " / " counter(pages);
        }

        /* ============================================
           COVER PAGE
           ============================================ */
        .cover-page {
            text-align: center;
            padding: 60px 30px;
            page-break-after: always;
        }

        .cover-title {
            font-size: 22pt;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .cover-subtitle {
            font-size: 14pt;
            color: #6b7280;
            margin-bottom: 25px;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 11pt;
            margin: 15px 0;
        }

        .status-conforme {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }

        .status-non-conforme {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
        }

        .cover-info {
            margin: 18px 0;
            font-size: 10pt;
            text-align: left;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
            padding: 15px 20px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .cover-info p {
            margin-bottom: 6px;
        }

        /* ============================================
           STATS CARDS - Synthèse
           ============================================ */
        .stats-row {
            display: table;
            width: 100%;
            margin: 18px 0;
            page-break-inside: avoid;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 14px 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .stat-card:first-child { border-left: none; }
        .stat-card:not(:last-child) { border-right: 1px solid #e5e7eb; }

        .stat-number {
            font-size: 18pt;
            font-weight: bold;
            display: block;
        }

        .stat-number.primary { color: #4F46E5; }
        .stat-number.success { color: #10B981; }
        .stat-number.warning { color: #F59E0B; }
        .stat-number.danger { color: #EF4444; }

        .stat-label {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ============================================
           SECTION TITLES
           ============================================ */
        h1 {
            font-size: 14pt;
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 6px;
            margin-bottom: 14px;
            margin-top: 22px;
            page-break-after: avoid;
        }

        h2 {
            font-size: 11pt;
            color: #374151;
            margin-top: 16px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }

        /* ============================================
           TABLE - Résultats par emplacement
           ============================================ */
        .table-emplacements {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 8pt;
            page-break-inside: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .table-emplacements thead {
            background: linear-gradient(180deg, #4F46E5 0%, #4338ca 100%);
            color: white;
        }

        .table-emplacements th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #4338ca;
        }

        .table-emplacements th.text-right { text-align: right; }
        .table-emplacements th.text-center { text-align: center; }

        .table-emplacements td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .table-emplacements tbody tr {
            page-break-inside: avoid;
        }

        .table-emplacements tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table-emplacements tbody tr:hover {
            background-color: #f3f4f6;
        }

        .table-emplacements .text-right { text-align: right; }
        .table-emplacements .text-center { text-align: center; }

        /* Écart badges */
        .ecart-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8pt;
        }

        .ecart-ok {
            background: #d1fae5;
            color: #065f46;
        }

        .ecart-manquant {
            background: #fee2e2;
            color: #991b1b;
        }

        .ecart-surplus {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Conformité badges */
        .conformite-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8pt;
        }

        .conformite-excellent {
            background: #d1fae5;
            color: #065f46;
        }

        .conformite-bon {
            background: #dbeafe;
            color: #1e40af;
        }

        .conformite-faible {
            background: #fef3c7;
            color: #92400e;
        }

        .conformite-insuffisant {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Bloc emplacement */
        .emplacement-block {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .emplacement-title {
            font-size: 11pt;
            color: #4F46E5;
            background: #eff6ff;
            padding: 8px 12px;
            margin: 16px 0 8px 0;
            border-left: 4px solid #4F46E5;
            page-break-after: avoid;
        }

        .emplacement-resume {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .emplacement-vide {
            font-size: 8pt;
            color: #9ca3af;
            font-style: italic;
            padding: 8px 0;
        }

        .table-detail {
            font-size: 7pt;
        }

        .table-detail th {
            padding: 6px 4px;
        }

        .table-detail td {
            padding: 5px 4px;
        }

        .etat-defectueux {
            color: #b45309;
            font-weight: bold;
        }

        .etat-ok {
            color: #065f46;
        }

        /* ============================================
           INFO BOXES
           ============================================ */
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #4F46E5;
            padding: 12px 14px;
            margin: 14px 0;
            page-break-inside: avoid;
            border-radius: 0 6px 6px 0;
        }

        .success-box {
            background: #ecfdf5;
            border-left: 4px solid #10B981;
            padding: 12px 14px;
            margin: 14px 0;
            page-break-inside: avoid;
            border-radius: 0 6px 6px 0;
        }

        .warning-box {
            background: #fffbeb;
            border-left: 4px solid #F59E0B;
            padding: 12px 14px;
            margin: 14px 0;
            page-break-inside: avoid;
            border-radius: 0 6px 6px 0;
        }

        .danger-box {
            background: #fef2f2;
            border-left: 4px solid #EF4444;
            padding: 12px 14px;
            margin: 14px 0;
            page-break-inside: avoid;
            border-radius: 0 6px 6px 0;
        }

        /* ============================================
           TABLE OF CONTENTS
           ============================================ */
        .toc {
            page-break-after: always;
        }

        .toc ul {
            list-style: none;
            margin-left: 0;
        }

        .toc li {
            margin-bottom: 8px;
            padding: 6px 0;
            padding-left: 20px;
            border-bottom: 1px dotted #e5e7eb;
        }

        /* ============================================
           UTILITIES
           ============================================ */
        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }
        .mt-15 { margin-top: 15px; }
        .mb-15 { margin-bottom: 15px; }
        ul { margin: 8px 0 8px 20px; }
        li { margin-bottom: 4px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <strong>Rapport d'inventaire {{ $inventaire->annee }}</strong> — Résultats détaillés par emplacement — Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="page-number"></div>
    </div>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <div class="cover-title">RAPPORT D'INVENTAIRE</div>
        <div class="cover-subtitle">Année {{ $inventaire->annee }} — Résultats détaillés</div>

        @php
            $tauxConformite = $statistiques['taux_conformite'] ?? 0;
            $statusClass = $tauxConformite >= 95 ? 'status-conforme' : 'status-non-conforme';
            $statusText = $tauxConformite >= 95 ? 'CONFORME' : 'À AMÉLIORER';
        @endphp

        <div class="status-badge {{ $statusClass }}">{{ $statusText }}</div>

        <div class="cover-info">
            <p><strong>Période :</strong> {{ $inventaire->date_debut->format('d/m/Y') }} — {{ $inventaire->date_fin ? $inventaire->date_fin->format('d/m/Y') : 'En cours' }}</p>
            <p><strong>Durée :</strong> {{ $statistiques['duree_jours'] ?? 0 }} jour(s)</p>
            <p><strong>Taux de conformité :</strong> {{ number_format($tauxConformite, 1) }}%</p>
            <p><strong>Taux de couverture :</strong> {{ $statistiques['taux_couverture'] ?? 0 }}%</p>
            <p><strong>Immobilisations attendues :</strong> {{ number_format($statistiques['total_biens_attendus'] ?? 0) }}</p>
            <p><strong>Immobilisations scannées :</strong> {{ number_format($statistiques['total_biens_scannes'] ?? 0) }}</p>
        </div>

        <div class="cover-info mt-15">
            <p><strong>Créé par :</strong> {{ $inventaire->creator->name ?? 'N/A' }}</p>
            @if($inventaire->closer)
                <p><strong>Clôturé par :</strong> {{ $inventaire->closer->name }}</p>
            @endif
        </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="toc">
        <h1>Table des matières</h1>
        <ul>
            <li>1. Synthèse générale</li>
            <li>2. Résultats par emplacement (détaillé)</li>
            <li>3. Immobilisations présentes</li>
            <li>4. Immobilisations déplacées</li>
            <li>5. Immobilisations absentes</li>
            <li>6. Anomalies et recommandations</li>
        </ul>
    </div>

    <!-- SECTION 1: SYNTHÈSE GÉNÉRALE -->
    <div class="page-break"></div>
    <h1>1. Synthèse générale</h1>

    <div class="stats-row no-break">
        <div class="stat-card">
            <span class="stat-number primary">{{ number_format($statistiques['total_biens_attendus'] ?? 0) }}</span>
            <span class="stat-label">Attendues</span>
        </div>
        <div class="stat-card">
            <span class="stat-number success">{{ number_format($statistiques['biens_presents'] ?? 0) }}</span>
            <span class="stat-label">Présentes</span>
        </div>
        <div class="stat-card">
            <span class="stat-number warning">{{ number_format($statistiques['biens_deplaces'] ?? 0) }}</span>
            <span class="stat-label">Déplacées</span>
        </div>
        <div class="stat-card">
            <span class="stat-number danger">{{ number_format($statistiques['biens_absents'] ?? 0) }}</span>
            <span class="stat-label">Absentes</span>
        </div>
    </div>

    <div class="info-box no-break">
        <p><strong>Taux de conformité global :</strong> {{ number_format($statistiques['taux_conformite'] ?? 0, 1) }}%</p>
        <p><strong>Taux de couverture :</strong> {{ $statistiques['taux_couverture'] ?? 0 }}% ({{ $statistiques['total_biens_scannes'] ?? 0 }}/{{ $statistiques['total_biens_attendus'] ?? 0 }} scannées)</p>
        <p><strong>Emplacements inventoriés :</strong> {{ count($statistiques['par_emplacement'] ?? []) }}</p>
    </div>

    <!-- SECTION 2: RÉSULTATS PAR EMPLACEMENT (tableaux détaillés) -->
    <div class="page-break"></div>
    <h1>2. Résultats par emplacement</h1>

    <p class="mb-15">Pour chaque emplacement, tableau des immobilisations avec colonnes Attendu, Trouvé effectivement, Conformité et État au moment de l'inventaire.</p>

    @php $detailParEmplacement = $detailParEmplacement ?? []; @endphp
    @if(count($detailParEmplacement) > 0)
        @foreach($detailParEmplacement as $emp)
        <div class="emplacement-block no-break">
            <h2 class="emplacement-title">{{ $emp['code'] }} — {{ $emp['localisation'] }}</h2>
            <p class="emplacement-resume">Résumé : {{ $emp['total_trouves'] }}/{{ $emp['total_attendus'] }} trouvées — Conformité {{ $emp['taux_conformite'] }}%</p>
            @if(count($emp['lignes'] ?? []) > 0)
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
                    @foreach($emp['lignes'] ?? [] as $ligne)
                    @php
                        $statut = $ligne['statut_scan'] ?? '';
                        $conformiteClass = $statut === 'present' ? 'conformite-excellent' : ($statut === 'absent' ? 'conformite-insuffisant' : ($statut === 'deplace' ? 'conformite-faible' : 'conformite-bon'));
                        $etatClass = ($ligne['etat'] ?? '') === 'Défectueuse' ? 'etat-defectueux' : 'etat-ok';
                    @endphp
                    <tr>
                        <td><strong>{{ $ligne['code'] ?? 'N/A' }}</strong></td>
                        <td>{{ Str::limit($ligne['designation'] ?? 'N/A', 35) }}</td>
                        <td class="text-center">{{ $ligne['attendu'] ?? 1 }}</td>
                        <td class="text-center">{{ $ligne['trouve'] ?? 0 }}</td>
                        <td class="text-center">
                            <span class="conformite-badge {{ $conformiteClass }}">{{ $ligne['conformite'] ?? '-' }}</span>
                        </td>
                        <td class="text-center {{ $etatClass }}">{{ $ligne['etat'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="emplacement-vide">Aucune immobilisation dans cet emplacement.</p>
            @endif
        </div>
        @endforeach

    <div class="info-box no-break mt-15">
        <p><strong>Légende :</strong></p>
        <p>• <strong>Attendu</strong> = 1 si l'immobilisation est attendue dans cet emplacement</p>
        <p>• <strong>Trouvé</strong> = 1 si trouvée effectivement sur place lors de l'inventaire, 0 sinon</p>
        <p>• <strong>Conformité</strong> = Présent (trouvé en place), Absent, Déplacé (trouvé ailleurs)</p>
        <p>• <strong>État</strong> = État physique constaté : Neuf, Bon état, Défectueuse</p>
    </div>
    @else
    <div class="warning-box no-break">
        <p>Aucune donnée par emplacement disponible. Les inventaires peuvent utiliser le mode par localisation.</p>
    </div>
    @endif

    <!-- SECTION 3: IMMOBILISATIONS PRÉSENTES -->
    @if(isset($biensPresents) && $biensPresents->count() > 0)
    <div class="page-break"></div>
    <h1>3. Immobilisations présentes ({{ $biensPresents->count() }})</h1>
    <table class="table-emplacements">
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensPresents->take(80) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ Str::limit($bien['designation'] ?? 'N/A', 45) }}</td>
                <td>{{ $bien['localisation'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensPresents->count() > 80)
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : {{ $biensPresents->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 4: IMMOBILISATIONS DÉPLACÉES -->
    @if(isset($biensDeplaces) && $biensDeplaces->count() > 0)
    <div class="page-break"></div>
    <h1>4. Immobilisations déplacées ({{ $biensDeplaces->count() }})</h1>
    <div class="warning-box no-break">
        <strong>Action requise :</strong> Ces immobilisations ont été trouvées dans une localisation différente de celle prévue.
    </div>
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
            @foreach($biensDeplaces->take(80) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ Str::limit($bien['designation'] ?? 'N/A', 40) }}</td>
                <td>{{ $bien['localisation_prevue'] ?? 'N/A' }}</td>
                <td>{{ $bien['localisation_reelle'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensDeplaces->count() > 80)
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : {{ $biensDeplaces->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 5: IMMOBILISATIONS ABSENTES -->
    @if(isset($biensAbsents) && $biensAbsents->count() > 0)
    <div class="page-break"></div>
    <h1>5. Immobilisations absentes ({{ $biensAbsents->count() }})</h1>
    <div class="danger-box no-break">
        <strong>Attention :</strong> {{ $biensAbsents->count() }} immobilisation(s) non trouvée(s) lors de l'inventaire.
    </div>
    <table class="table-emplacements">
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensAbsents->take(80) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ Str::limit($bien['designation'] ?? 'N/A', 45) }}</td>
                <td>{{ $bien['localisation'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensAbsents->count() > 80)
    <p class="text-center mt-15"><em>Liste tronquée à 80 entrées. Total : {{ $biensAbsents->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 6: ANOMALIES ET RECOMMANDATIONS -->
    @if(isset($anomalies) || isset($recommendations))
    <div class="page-break"></div>
    <h1>6. Anomalies et recommandations</h1>

    @php
        $totalAnomalies = count($anomalies['localisations_non_demarrees'] ?? []) +
                         count($anomalies['taux_absence_eleve'] ?? []) +
                         count($anomalies['biens_defectueux'] ?? []);
    @endphp

    @if($totalAnomalies > 0)
        @if(count($anomalies['localisations_non_demarrees'] ?? []) > 0)
        <h2>Emplacements non démarrés</h2>
        <div class="warning-box no-break">
            <ul>
                @foreach($anomalies['localisations_non_demarrees'] as $loc)
                <li>{{ $loc['code'] ?? 'N/A' }} — {{ $loc['designation'] ?? 'N/A' }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($anomalies['taux_absence_eleve'] ?? []) > 0)
        <h2>Taux d'absence élevé</h2>
        <div class="warning-box no-break">
            <ul>
                @foreach($anomalies['taux_absence_eleve'] as $a)
                <li>{{ $a['code'] ?? 'N/A' }} — {{ $a['taux_absence'] ?? 0 }}% absents ({{ $a['biens_absents'] ?? 0 }} immobilisations)</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($anomalies['biens_defectueux'] ?? []) > 0)
        <h2>Immobilisations signalées défectueuses</h2>
        <div class="danger-box no-break">
            <ul>
                @foreach($anomalies['biens_defectueux'] as $b)
                <li>{{ $b['code'] ?? 'N/A' }} — {{ $b['designation'] ?? 'N/A' }} ({{ $b['localisation'] ?? 'N/A' }})</li>
                @endforeach
            </ul>
        </div>
        @endif
    @else
    <div class="success-box no-break">
        <strong>✓ Aucune anomalie majeure détectée.</strong>
    </div>
    @endif

    @if(isset($recommendations) && (count($recommendations['corrections_immediates'] ?? []) > 0 || count($recommendations['ameliorations_organisationnelles'] ?? []) > 0))
    <h2>Recommandations</h2>
    @if(count($recommendations['corrections_immediates'] ?? []) > 0)
    <div class="danger-box no-break">
        <strong>Corrections immédiates :</strong>
        <ul>
            @foreach($recommendations['corrections_immediates'] as $r)
            <li>{{ $r }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if(count($recommendations['ameliorations_organisationnelles'] ?? []) > 0)
    <div class="info-box no-break">
        <strong>Améliorations :</strong>
        <ul>
            @foreach($recommendations['ameliorations_organisationnelles'] as $r)
            <li>{{ $r }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @endif
    @endif

</body>
</html>
