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
            color: #000;
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
            color: #666;
        }

        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-top: 1px solid #ddd;
            padding: 5px 0;
            font-size: 8pt;
            color: #666;
        }

        .page-number {
            text-align: center;
        }

        .page-number:after {
            content: "Page " counter(page) " / " counter(pages);
        }

        /* ============================================
           COVER PAGE
           ============================================ */
        .cover-page {
            text-align: center;
            padding: 80px 30px;
            page-break-after: always;
        }

        .cover-title {
            font-size: 24pt;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 15px;
        }

        .cover-subtitle {
            font-size: 16pt;
            color: #666;
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 25px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12pt;
            margin: 15px 0;
        }

        .status-conforme {
            background-color: #10B981;
            color: white;
        }

        .status-non-conforme {
            background-color: #EF4444;
            color: white;
        }

        .cover-info {
            margin: 15px 0;
            font-size: 11pt;
            text-align: left;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ============================================
           TYPOGRAPHY
           ============================================ */
        h1 {
            font-size: 16pt;
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 5px;
            margin-bottom: 12px;
            margin-top: 20px;
            page-break-after: avoid;
        }

        h2 {
            font-size: 12pt;
            color: #1F2937;
            margin-top: 15px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }

        h3 {
            font-size: 10pt;
            color: #374151;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        p {
            margin-bottom: 8px;
        }

        /* ============================================
           TABLES
           ============================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 8pt;
            page-break-inside: auto;
        }

        thead {
            background-color: #4F46E5;
            color: white;
        }

        thead tr {
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #E5E7EB;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #E5E7EB;
            vertical-align: top;
        }

        tbody tr {
            page-break-inside: avoid;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        /* ============================================
           STATS GRID
           ============================================ */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .stat-number {
            font-size: 20pt;
            font-weight: bold;
            color: #4F46E5;
            display: block;
        }

        .stat-label {
            font-size: 8pt;
            color: #666;
            margin-top: 4px;
        }

        /* ============================================
           BOXES
           ============================================ */
        .info-box {
            background: #F3F4F6;
            border-left: 4px solid #4F46E5;
            padding: 8px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }

        .warning-box {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 8px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }

        .danger-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 8px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }

        .success-box {
            background: #D1FAE5;
            border-left: 4px solid #10B981;
            padding: 8px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .badge-info {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        /* ============================================
           UTILITIES
           ============================================ */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .mt-15 {
            margin-top: 15px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        ul {
            margin: 8px 0 8px 20px;
        }

        li {
            margin-bottom: 4px;
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
            margin-bottom: 6px;
            padding-left: 20px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <strong>Rapport Inventaire {{ $inventaire->annee }}</strong> | Généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="page-number"></div>
    </div>

    <!-- COVER PAGE -->
    <div class="cover-page">
        <div class="cover-title">
            RAPPORT D'INVENTAIRE
        </div>
        <div class="cover-subtitle">
            Année {{ $inventaire->annee }}
        </div>

        @php
            $tauxConformite = $statistiques['taux_conformite'];
            $statusClass = $tauxConformite >= 95 ? 'status-conforme' : 'status-non-conforme';
            $statusText = $tauxConformite >= 95 ? 'CONFORME' : 'NON CONFORME';
        @endphp

        <div class="status-badge {{ $statusClass }}">
            {{ $statusText }}
        </div>

        <div class="cover-info">
            <p><strong>Date de début :</strong> {{ $inventaire->date_debut->format('d/m/Y') }}</p>
            <p><strong>Date de fin :</strong> {{ $inventaire->date_fin ? $inventaire->date_fin->format('d/m/Y') : 'En cours' }}</p>
            <p><strong>Durée :</strong> {{ $statistiques['duree_jours'] }} jours</p>
            <p><strong>Taux de conformité :</strong> {{ number_format($tauxConformite, 2) }}%</p>
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
            <li>2. Analyse par localisation</li>
            <li>3. Performance par agent</li>
            <li>4. Immobilisations présentes</li>
            <li>5. Immobilisations déplacées</li>
            <li>6. Immobilisations absentes</li>
            <li>7. Immobilisations non scannées</li>
            <li>8. Anomalies détectées</li>
            <li>9. Recommandations</li>
        </ul>
    </div>

    <!-- SECTION 1: SYNTHÈSE GÉNÉRALE -->
    <div class="page-break"></div>
    <h1>1. Synthèse générale</h1>

    <div class="stats-grid no-break">
        <div class="stat-card">
            <span class="stat-number">{{ $statistiques['total_biens_attendus'] }}</span>
            <span class="stat-label">Immobilisations inventoriées</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #10B981;">{{ $statistiques['biens_presents'] }}</span>
            <span class="stat-label">Présents</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #F59E0B;">{{ $statistiques['biens_deplaces'] }}</span>
            <span class="stat-label">Déplacés</span>
        </div>
        <div class="stat-card">
            <span class="stat-number" style="color: #EF4444;">{{ $statistiques['biens_absents'] }}</span>
            <span class="stat-label">Absents</span>
        </div>
    </div>

    <div class="info-box no-break">
        <p><strong>Taux de conformité global :</strong> {{ number_format($statistiques['taux_conformite'], 2) }}%</p>
        <p><strong>Progression :</strong> {{ number_format($statistiques['progression_globale'], 2) }}% complété</p>
        <p><strong>Localisations scannées :</strong> {{ $statistiques['localisations_terminees'] ?? 0 }} / {{ $statistiques['total_localisations'] ?? 0 }}</p>
        <p><strong>Valeur totale scannée :</strong> {{ number_format($statistiques['valeur_totale_scannee'] ?? 0, 0, ',', ' ') }} MRU</p>
    </div>

    @if(isset($statistiques['par_nature']) && count($statistiques['par_nature']) > 0)
    <h2>Répartition par nature de bien</h2>
    <table class="no-break">
        <thead>
            <tr>
                <th>Nature</th>
                <th class="text-right">Total</th>
                <th class="text-right">Présents</th>
                <th class="text-right">Déplacés</th>
                <th class="text-right">Absents</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statistiques['par_nature'] as $nature => $stats)
            <tr>
                <td><strong>{{ ucfirst($nature) }}</strong></td>
                <td class="text-right">{{ $stats['total'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['presents'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['deplaces'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['absents'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- SECTION 2: ANALYSE PAR LOCALISATION -->
    <div class="page-break"></div>
    <h1>2. Analyse par localisation</h1>

    @if(isset($performanceLocalisations) && count($performanceLocalisations) > 0)
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th class="text-right">Attendus</th>
                <th class="text-right">Scannés</th>
                <th class="text-right">Présents</th>
                <th class="text-right">Déplacés</th>
                <th class="text-right">Absents</th>
                <th class="text-right">Conformité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceLocalisations as $perf)
            <tr>
                <td><strong>{{ $perf['code'] ?? 'N/A' }}</strong></td>
                <td>{{ Str::limit($perf['designation'] ?? 'N/A', 30) }}</td>
                <td class="text-right">{{ $perf['attendus'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['scannes'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['presents'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['deplaces'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['absents'] ?? 0 }}</td>
                <td class="text-right">
                    @if(($perf['taux_conformite'] ?? 0) >= 95)
                        <span class="badge badge-success">{{ number_format($perf['taux_conformite'], 1) }}%</span>
                    @elseif(($perf['taux_conformite'] ?? 0) >= 80)
                        <span class="badge badge-warning">{{ number_format($perf['taux_conformite'], 1) }}%</span>
                    @else
                        <span class="badge badge-danger">{{ number_format($perf['taux_conformite'], 1) }}%</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- SECTION 3: PERFORMANCE PAR AGENT -->
    @if(isset($performanceAgents) && count($performanceAgents) > 0)
    <div class="page-break"></div>
    <h1>3. Performance par agent</h1>

    <table>
        <thead>
            <tr>
                <th>Agent</th>
                <th class="text-right">Localisations</th>
                <th class="text-right">Immobilisations scannées</th>
                <th class="text-right">Durée totale</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceAgents as $perf)
            <tr>
                <td><strong>{{ $perf['agent'] ?? $perf['agent_name'] ?? 'N/A' }}</strong></td>
                <td class="text-right">{{ $perf['localisations'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['biens_scannes'] ?? 0 }}</td>
                <td class="text-right">{{ number_format(($perf['duree_totale_minutes'] ?? 0) / 60, 1) }}h</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- SECTION 4: BIENS PRÉSENTS -->
    @if(isset($biensPresents) && $biensPresents->count() > 0)
    <div class="page-break"></div>
    <h1>4. Immobilisations présentes ({{ $biensPresents->count() }})</h1>
    
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
                <th class="text-right">Valeur (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensPresents->take(100) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? ($bien->code_inventaire ?? 'N/A') }}</td>
                <td>{{ Str::limit($bien['designation'] ?? ($bien->designation ?? 'N/A'), 40) }}</td>
                <td>{{ $bien['localisation'] ?? ($bien->localisation->code ?? 'N/A') }}</td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? ($bien->valeur_acquisition ?? 0), 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensPresents->count() > 100)
    <p class="text-center"><em>Liste tronquée à 100 entrées. Total : {{ $biensPresents->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 5: BIENS DÉPLACÉS -->
    @if(isset($biensDeplaces) && $biensDeplaces->count() > 0)
    <div class="page-break"></div>
    <h1>5. Immobilisations déplacées ({{ $biensDeplaces->count() }})</h1>

    <div class="warning-box no-break">
        <strong>⚠️ Action requise :</strong> Ces immobilisations ont été trouvées dans une localisation différente de celle prévue.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Loc. prévue</th>
                <th>Loc. réelle</th>
                <th class="text-right">Valeur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensDeplaces as $bien)
            <tr>
                <td>{{ $bien['code'] ?? ($bien->code_inventaire ?? 'N/A') }}</td>
                <td>{{ Str::limit($bien['designation'] ?? ($bien->designation ?? 'N/A'), 40) }}</td>
                <td><span class="badge badge-danger">{{ $bien['localisation_prevue'] ?? ($bien->localisation->code ?? 'N/A') }}</span></td>
                <td><span class="badge badge-warning">{{ $bien['localisation_reelle'] ?? ($bien->localisationReelle->code ?? 'N/A') }}</span></td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? ($bien->valeur_acquisition ?? 0), 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- SECTION 6: BIENS ABSENTS -->
    @if(isset($biensAbsents) && $biensAbsents->count() > 0)
    <div class="page-break"></div>
    <h1>6. Immobilisations absentes ({{ $biensAbsents->count() }})</h1>

    @php
        $valeurTotaleAbsente = $biensAbsents->sum(function($b) { return $b['valeur'] ?? ($b->valeur_acquisition ?? 0); });
    @endphp

    <div class="danger-box no-break">
        <strong>🚨 Attention critique :</strong> {{ $biensAbsents->count() }} immobilisation(s) non trouvée(s), 
        représentant une valeur totale de <strong>{{ number_format($valeurTotaleAbsente, 0, ',', ' ') }} MRU</strong>.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
                <th class="text-right">Valeur (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensAbsents->take(100) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? ($bien->code_inventaire ?? 'N/A') }}</td>
                <td>{{ Str::limit($bien['designation'] ?? ($bien->designation ?? 'N/A'), 40) }}</td>
                <td>{{ $bien['localisation'] ?? ($bien->localisation->code ?? 'N/A') }}</td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? ($bien->valeur_acquisition ?? 0), 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensAbsents->count() > 100)
    <p class="text-center"><em>Liste tronquée à 100 entrées. Total : {{ $biensAbsents->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 7: BIENS NON SCANNÉS -->
    @if(isset($biensNonScannes) && $biensNonScannes->count() > 0)
    <div class="page-break"></div>
    <h1>7. Immobilisations non scannées ({{ $biensNonScannes->count() }})</h1>

    <div class="warning-box no-break">
        <strong>⚠️ Information :</strong> Ces immobilisations étaient dans les localisations inventoriées mais n'ont pas été scannées.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
                <th class="text-right">Valeur (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensNonScannes->take(100) as $bien)
            <tr>
                <td>{{ $bien->code_inventaire ?? 'N/A' }}</td>
                <td>{{ Str::limit($bien->designation ?? 'N/A', 40) }}</td>
                <td>{{ $bien->localisation->code ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($bien->valeur_acquisition ?? 0, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensNonScannes->count() > 100)
    <p class="text-center"><em>Liste tronquée à 100 entrées. Total : {{ $biensNonScannes->count() }}</em></p>
    @endif
    @endif

    <!-- SECTION 8: ANOMALIES -->
    @if(isset($anomalies))
    <div class="page-break"></div>
    <h1>8. Anomalies détectées</h1>

    @php
        $totalAnomalies = count($anomalies['localisations_non_demarrees'] ?? []) +
                         count($anomalies['localisations_bloquees'] ?? []) +
                         count($anomalies['biens_absents_valeur_haute'] ?? []);
    @endphp

    @if($totalAnomalies > 0)
        @if(count($anomalies['localisations_non_demarrees'] ?? []) > 0)
        <h2>Localisations non démarrées</h2>
        <div class="warning-box no-break">
            <ul>
                @foreach($anomalies['localisations_non_demarrees'] as $loc)
                <li>{{ $loc['code'] ?? 'N/A' }} - {{ $loc['designation'] ?? 'N/A' }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(count($anomalies['biens_absents_valeur_haute'] ?? []) > 0)
        <h2>Immobilisations absentes de valeur élevée</h2>
        <div class="danger-box no-break">
            <ul>
                @foreach($anomalies['biens_absents_valeur_haute'] as $bien)
                <li>{{ $bien['code'] ?? 'N/A' }} - {{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }} MRU</li>
                @endforeach
            </ul>
        </div>
        @endif
    @else
    <div class="success-box no-break">
        ✓ Aucune anomalie détectée. Excellent travail !
    </div>
    @endif
    @endif

    <!-- SECTION 9: RECOMMANDATIONS -->
    @if(isset($recommendations))
    <div class="page-break"></div>
    <h1>9. Recommandations</h1>

    @if(count($recommendations['corrections_immediates'] ?? []) > 0)
    <h2>Corrections immédiates nécessaires</h2>
    <div class="danger-box no-break">
        <ul>
            @foreach($recommendations['corrections_immediates'] as $reco)
            <li>{{ $reco }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($recommendations['ameliorations_organisationnelles'] ?? []) > 0)
    <h2>Améliorations organisationnelles</h2>
    <div class="warning-box no-break">
        <ul>
            @foreach($recommendations['ameliorations_organisationnelles'] as $reco)
            <li>{{ $reco }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @endif

</body>
</html>
