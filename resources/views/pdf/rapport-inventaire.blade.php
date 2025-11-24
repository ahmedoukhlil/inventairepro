<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Inventaire {{ $inventaire->annee }}</title>
    <style>
        /* ============================================
           STYLES GÉNÉRAUX
           ============================================ */
        @page {
            margin: 2cm 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        /* ============================================
           HEADER & FOOTER
           ============================================ */
        header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            text-align: center;
            padding: 10px 0;
            border-bottom: 2px solid #4F46E5;
        }

        footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        /* ============================================
           COVER PAGE
           ============================================ */
        .cover-page {
            text-align: center;
            padding: 100px 50px;
            page-break-after: always;
        }

        .cover-title {
            font-size: 28pt;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 20px;
        }

        .cover-subtitle {
            font-size: 18pt;
            color: #666;
            margin-bottom: 40px;
        }

        .cover-info {
            margin: 20px 0;
            font-size: 12pt;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 30px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14pt;
            margin: 20px 0;
        }

        .status-conforme {
            background-color: #10B981;
            color: white;
        }

        .status-non-conforme {
            background-color: #EF4444;
            color: white;
        }

        /* ============================================
           TYPOGRAPHY
           ============================================ */
        h1 {
            font-size: 18pt;
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 8px;
            margin-bottom: 15px;
            page-break-after: avoid;
        }

        h2 {
            font-size: 14pt;
            color: #1F2937;
            margin-top: 20px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }

        h3 {
            font-size: 12pt;
            color: #374151;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        p {
            margin-bottom: 10px;
            text-align: justify;
        }

        /* ============================================
           TABLES
           ============================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
            page-break-inside: auto;
        }

        thead {
            background-color: #4F46E5;
            color: white;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 6px 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        tbody tr:hover {
            background-color: #F3F4F6;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* ============================================
           CARDS & BOXES
           ============================================ */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .stat-number {
            font-size: 24pt;
            font-weight: bold;
            color: #4F46E5;
            display: block;
        }

        .stat-label {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        .info-box {
            background: #F3F4F6;
            border-left: 4px solid #4F46E5;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .warning-box {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .danger-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .success-box {
            background: #D1FAE5;
            border-left: 4px solid #10B981;
            padding: 10px 15px;
            margin: 15px 0;
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
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

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        ul {
            margin: 10px 0 10px 20px;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

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
        </div>

        <div class="cover-info mt-20">
            <p><strong>Créé par :</strong> {{ $inventaire->creator->name ?? 'N/A' }}</p>
            @if($inventaire->closer)
                <p><strong>Clôturé par :</strong> {{ $inventaire->closer->name }}</p>
            @endif
            <p><strong>Date de génération :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
        </div>

        @if($inventaire->observation)
        <div class="info-box mt-20">
            <strong>Observation :</strong><br>
            {{ $inventaire->observation }}
        </div>
        @endif
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="page-break"></div>
    <h1>Table des matières</h1>
    <ul>
        <li>1. Synthèse générale</li>
        <li>2. Analyse par localisation</li>
        <li>3. Analyse par agent</li>
        <li>4. Immobilisations présentes conformes</li>
        <li>5. Immobilisations déplacées</li>
        <li>6. Immobilisations absentes</li>
        <li>7. Immobilisations non scannées</li>
        <li>8. Analyse des mouvements</li>
        <li>9. Anomalies détectées</li>
        <li>10. Recommandations</li>
    </ul>

    <!-- SECTION 1: SYNTHÈSE GÉNÉRALE -->
    <div class="page-break"></div>
    <h1>1. Synthèse générale</h1>

    <div class="stats-grid">
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

    <div class="info-box">
        <p><strong>Taux de conformité global :</strong> {{ number_format($statistiques['taux_conformite'], 2) }}%</p>
        <p><strong>Progression :</strong> {{ number_format($statistiques['progression_globale'], 2) }}% complété</p>
        <p><strong>Localisations scannées :</strong> {{ $statistiques['localisations_terminees'] }} / {{ $statistiques['total_localisations'] }}</p>
    </div>

    <h2>Répartition par nature de bien</h2>
    <table>
        <thead>
            <tr>
                <th>Nature</th>
                <th class="text-right">Total</th>
                <th class="text-right">Présents</th>
                <th class="text-right">Déplacés</th>
                <th class="text-right">Absents</th>
                <th class="text-right">Conformité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statistiques['par_nature'] ?? [] as $nature => $stats)
            <tr>
                <td><strong>{{ ucfirst($nature) }}</strong></td>
                <td class="text-right">{{ $stats['total'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['presents'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['deplaces'] ?? 0 }}</td>
                <td class="text-right">{{ $stats['absents'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($stats['conformite'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SECTION 2: ANALYSE PAR LOCALISATION -->
    <div class="page-break"></div>
    <h1>2. Analyse par localisation</h1>

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
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceLocalisations as $perf)
            <tr>
                <td><strong>{{ $perf['code'] ?? 'N/A' }}</strong></td>
                <td>{{ $perf['designation'] ?? 'N/A' }}</td>
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
                <td>
                    @if(($perf['statut'] ?? '') === 'termine')
                        <span class="badge badge-success">Terminé</span>
                    @elseif(($perf['statut'] ?? '') === 'en_cours')
                        <span class="badge badge-info">En cours</span>
                    @else
                        <span class="badge">En attente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($performanceLocalisations->where('taux_conformite', '<', 80)->count() > 0)
    <div class="warning-box mt-20">
        <strong>⚠️ Attention :</strong> {{ $performanceLocalisations->where('taux_conformite', '<', 80)->count() }} 
        localisation(s) présente(nt) un taux de conformité inférieur à 80%.
    </div>
    @endif

    <!-- SECTION 3: ANALYSE PAR AGENT -->
    <div class="page-break"></div>
    <h1>3. Performance par agent</h1>

    <table>
        <thead>
            <tr>
                <th>Agent</th>
                <th class="text-right">Localisations</th>
                <th class="text-right">Terminées</th>
                <th class="text-right">Immobilisations scannées</th>
                <th class="text-right">Durée totale</th>
                <th class="text-right">Moy./localisation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceAgents as $perf)
            <tr>
                <td><strong>{{ $perf['agent'] ?? 'N/A' }}</strong></td>
                <td class="text-right">{{ $perf['localisations'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['localisations_terminees'] ?? 0 }}</td>
                <td class="text-right">{{ $perf['biens_scannes'] ?? 0 }}</td>
                <td class="text-right">{{ number_format(($perf['duree_totale_minutes'] ?? 0) / 60, 1) }}h</td>
                <td class="text-right">{{ $perf['moyenne_par_localisation'] ?? 0 }} min</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SECTION 4: BIENS PRÉSENTS -->
    <div class="page-break"></div>
    <h1>4. Immobilisations présentes conformes ({{ $biensPresents->count() }})</h1>
    
    @if($biensPresents->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Nature</th>
                <th>Localisation</th>
                <th>Service</th>
                <th class="text-right">Valeur (MRU)</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensPresents->take(100) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ $bien['designation'] ?? 'N/A' }}</td>
                <td>{{ ucfirst($bien['nature'] ?? 'N/A') }}</td>
                <td>{{ $bien['localisation'] ?? 'N/A' }}</td>
                <td>{{ $bien['service'] ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }}</td>
                <td>{{ ucfirst($bien['etat'] ?? 'N/A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensPresents->count() > 100)
    <p class="text-center"><em>Liste tronquée à 100 entrées. Total : {{ $biensPresents->count() }}</em></p>
    @endif
    @else
    <p class="text-center"><em>Aucun bien présent conforme.</em></p>
    @endif

    <!-- SECTION 5: BIENS DÉPLACÉS -->
    <div class="page-break"></div>
    <h1>5. Immobilisations déplacées ({{ $biensDeplaces->count() }})</h1>

    @if($biensDeplaces->count() > 0)
    <div class="warning-box">
        <strong>⚠️ Action requise :</strong> Ces immobilisations ont été trouvées dans une localisation différente de celle prévue. 
        Vérifier les raisons et mettre à jour les données si nécessaire.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Loc. prévue</th>
                <th>Loc. réelle</th>
                <th class="text-right">Valeur</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensDeplaces as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ $bien['designation'] ?? 'N/A' }}</td>
                <td><span class="badge badge-danger">{{ $bien['localisation_prevue'] ?? 'N/A' }}</span></td>
                <td><span class="badge badge-warning">{{ $bien['localisation_reelle'] ?? 'N/A' }}</span></td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }}</td>
                <td style="font-size: 8pt;">{{ $bien['commentaire'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="success-box">
        ✓ Aucun bien déplacé. Excellente organisation !
    </div>
    @endif

    <!-- SECTION 6: BIENS ABSENTS -->
    <div class="page-break"></div>
    <h1>6. Immobilisations absentes ({{ $biensAbsents->count() }})</h1>

    @if($biensAbsents->count() > 0)
    @php
        $valeurTotaleAbsente = $biensAbsents->sum('valeur');
    @endphp

    <div class="danger-box">
        <strong>🚨 Attention critique :</strong> {{ $biensAbsents->count() }} immobilisation(s) non trouvée(s) lors de l'inventaire, 
        représentant une valeur totale de <strong>{{ number_format($valeurTotaleAbsente, 0, ',', ' ') }} MRU</strong>.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Localisation</th>
                <th>Service</th>
                <th class="text-right">Valeur (MRU)</th>
                <th>Acquisition</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensAbsents as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ $bien['designation'] ?? 'N/A' }}</td>
                <td>{{ $bien['localisation'] ?? 'N/A' }}</td>
                <td>{{ $bien['service'] ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }}</td>
                <td>{{ $bien['date_acquisition'] ? $bien['date_acquisition']->format('d/m/Y') : 'N/A' }}</td>
                <td style="font-size: 8pt;">{{ $bien['commentaire'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="success-box">
        ✓ Aucun bien absent. Parfait !
    </div>
    @endif

    <!-- SECTION 7: BIENS NON SCANNÉS -->
    <div class="page-break"></div>
    <h1>7. Immobilisations non scannées ({{ $biensNonScannes->count() }})</h1>

    @if($biensNonScannes->count() > 0)
    <div class="warning-box">
        <strong>⚠️ Information :</strong> Ces immobilisations étaient dans les localisations inventoriées mais n'ont pas été scannées.
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Nature</th>
                <th>Localisation</th>
                <th>Service</th>
                <th class="text-right">Valeur (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biensNonScannes->take(100) as $bien)
            <tr>
                <td>{{ $bien['code'] ?? 'N/A' }}</td>
                <td>{{ $bien['designation'] ?? 'N/A' }}</td>
                <td>{{ ucfirst($bien['nature'] ?? 'N/A') }}</td>
                <td>{{ $bien['localisation'] ?? 'N/A' }}</td>
                <td>{{ $bien['service'] ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($biensNonScannes->count() > 100)
    <p class="text-center"><em>Liste tronquée à 100 entrées. Total : {{ $biensNonScannes->count() }}</em></p>
    @endif
    @else
    <div class="success-box">
        ✓ Toutes les immobilisations attendues ont été scannées.
    </div>
    @endif

    <!-- SECTION 8: ANALYSE DES MOUVEMENTS -->
    <div class="page-break"></div>
    <h1>8. Analyse des mouvements</h1>

    @if($mouvements['total_deplaces'] > 0)
    <div class="info-box">
        <p><strong>Total d'immobilisations déplacées :</strong> {{ $mouvements['total_deplaces'] }}</p>
    </div>

    <h2>Flux de déplacement</h2>
    <table>
        <thead>
            <tr>
                <th>Origine</th>
                <th>Destination</th>
                <th class="text-right">Nombre d'immobilisations</th>
                <th class="text-right">Valeur totale (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mouvements['flux'] as $flux)
            <tr>
                <td><strong>{{ $flux['origine'] ?? 'N/A' }}</strong></td>
                <td><strong>{{ $flux['destination'] ?? 'N/A' }}</strong></td>
                <td class="text-right">{{ $flux['nombre_biens'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($flux['valeur_totale'] ?? 0, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="success-box">
        ✓ Aucun mouvement de bien détecté.
    </div>
    @endif

    <!-- SECTION 9: ANOMALIES DÉTECTÉES -->
    <div class="page-break"></div>
    <h1>9. Anomalies détectées</h1>

    @php
        $totalAnomalies = count($anomalies['localisations_non_demarrees'] ?? []) +
                         count($anomalies['localisations_bloquees'] ?? []) +
                         count($anomalies['biens_absents_valeur_haute'] ?? []) +
                         count($anomalies['localisations_non_assignees'] ?? []);
    @endphp

    @if($totalAnomalies > 0)
    @if(count($anomalies['localisations_non_demarrees'] ?? []) > 0)
    <h2>Localisations non démarrées</h2>
    <div class="warning-box">
        <ul>
            @foreach($anomalies['localisations_non_demarrees'] as $loc)
            <li>{{ $loc['code'] ?? 'N/A' }} - {{ $loc['designation'] ?? 'N/A' }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($anomalies['localisations_bloquees'] ?? []) > 0)
    <h2>Localisations bloquées</h2>
    <div class="warning-box">
        <p>Localisations sans scan depuis plus de 24 heures :</p>
        <ul>
            @foreach($anomalies['localisations_bloquees'] as $loc)
            <li>{{ $loc['code'] ?? 'N/A' }} - Dernier scan : {{ $loc['dernier_scan'] ?? 'N/A' }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($anomalies['biens_absents_valeur_haute'] ?? []) > 0)
    <h2>Immobilisations absentes de valeur élevée</h2>
    <div class="danger-box">
        <p>Immobilisations absentes avec une valeur supérieure à 100 000 MRU :</p>
        <ul>
            @foreach($anomalies['biens_absents_valeur_haute'] as $bien)
            <li>{{ $bien['code'] ?? 'N/A' }} - {{ $bien['designation'] ?? 'N/A' }} 
                ({{ number_format($bien['valeur'] ?? 0, 0, ',', ' ') }} MRU)</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($anomalies['localisations_non_assignees'] ?? []) > 0)
    <h2>Localisations non assignées</h2>
    <div class="info-box">
        <ul>
            @foreach($anomalies['localisations_non_assignees'] as $loc)
            <li>{{ $loc['code'] ?? 'N/A' }} - {{ $loc['designation'] ?? 'N/A' }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @else
    <div class="success-box">
        ✓ Aucune anomalie détectée. Excellent travail !
    </div>
    @endif

    <!-- SECTION 10: RECOMMANDATIONS -->
    <div class="page-break"></div>
    <h1>10. Recommandations</h1>

    @if(count($recommendations['corrections_immediates'] ?? []) > 0)
    <h2>Corrections immédiates nécessaires</h2>
    <div class="danger-box">
        <ul>
            @foreach($recommendations['corrections_immediates'] as $reco)
            <li>{{ $reco }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($recommendations['ameliorations_organisationnelles'] ?? []) > 0)
    <h2>Améliorations organisationnelles</h2>
    <div class="warning-box">
        <ul>
            @foreach($recommendations['ameliorations_organisationnelles'] as $reco)
            <li>{{ $reco }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(count($recommendations['prochain_inventaire'] ?? []) > 0)
    <h2>Planification du prochain inventaire</h2>
    <div class="info-box">
        <ul>
            @foreach($recommendations['prochain_inventaire'] as $reco)
            <li>{{ $reco }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- FOOTER -->
    <footer>
        <div class="page-number"></div>
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }} | Inventaire {{ $inventaire->annee }}</p>
    </footer>

</body>
</html>

