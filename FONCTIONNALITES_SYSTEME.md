# 📋 Principales Fonctionnalités du Système GESIMMOS

## 🎯 Vue d'ensemble

GESIMMOS est un système complet de gestion d'inventaire avec scan QR code, conçu pour gérer efficacement les biens mobiliers d'une organisation. Le système comprend une application web principale et une Progressive Web App (PWA) mobile pour le scan sur le terrain.

---

## 🏗️ Architecture du Système

### **Modules Principaux**

1. **Gestion des Utilisateurs** 👥
2. **Gestion des Localisations** 📍
3. **Gestion des Biens** 🏢
4. **Gestion des Inventaires** 📊
5. **Application Mobile PWA** 📱
6. **Rapports et Exports** 📄

---

## 1️⃣ GESTION DES UTILISATEURS 👥

### Fonctionnalités

- **Authentification sécurisée** avec Laravel Breeze
- **Gestion des rôles** :
  - **Administrateur** : Accès complet au système
  - **Agent** : Accès aux inventaires et scans
- **Création et modification** des comptes utilisateurs
- **Session timeout** automatique pour la sécurité
- **Authentification API** via Laravel Sanctum pour la PWA


---

## 2️⃣ GESTION DES LOCALISATIONS 📍

### Fonctionnalités

- **Création et gestion** des localisations (bureaux, ateliers, salles, etc.)
- **Hiérarchie des localisations** :
  - Bâtiment
  - Étage
  - Service
  - Bureau/Salle
- **Génération de QR codes** pour chaque localisation
- **Impression d'étiquettes** en masse
- **Export Excel** des localisations
- **Affectation d'agents** aux localisations lors des inventaires

### Statuts des Localisations

- `active` : Localisation active
- `inactive` : Localisation désactivée



---

## 3️⃣ GESTION DES BIENS 🏢

### Fonctionnalités

- **Création et gestion** des biens mobiliers
- **Codes d'inventaire uniques** au format `INV-ANNEE-XXX`
- **Informations détaillées** :
  - Désignation
  - Date d'acquisition
  - Nature (mobilier, informatique, véhicule, etc.)
  - Service usager
  - Localisation
  - Valeur d'acquisition
  - État
  - Observations
- **Génération de QR codes** pour chaque bien
- **Impression d'étiquettes** individuelles ou en masse
- **Export Excel et PDF** des biens
- **Soft delete** pour conserver l'historique




## 4️⃣ GESTION DES INVENTAIRES 📊

### Cycle de Vie d'un Inventaire

1. **En préparation** (`en_preparation`) : Création et configuration
2. **En cours** (`en_cours`) : Inventaire actif, scans en cours
3. **Terminé** (`termine`) : Tous les scans effectués
4. **Clôturé** (`cloture`) : Inventaire finalisé et archivé

### Fonctionnalités Principales

#### A. Création et Démarrage

- **Création d'inventaire annuel** avec sélection des localisations
- **Assignation d'agents** aux localisations
- **Calcul automatique** du nombre de biens attendus par localisation
- **Démarrage de l'inventaire** (passage en statut `en_cours`)



#### B. Gestion des Scans

- **Enregistrement de scans** avec différents statuts :
  - `present` : Bien présent à sa localisation
  - `deplace` : Bien déplacé vers une autre localisation
  - `absent` : Bien absent
  - `deteriore` : Bien détérioré
- **Photos** associées aux scans
- **Commentaires** et observations
- **Suivi de l'agent** qui a effectué le scan
- **Horodatage** automatique



#### C. Statistiques et Suivi

- **Progression globale** de l'inventaire
- **Taux de conformité** (% de biens présents)
- **Répartition par statut** (présent, déplacé, absent, détérioré)
- **Progression par localisation**
- **Progression par service**
- **Durée de l'inventaire**


---

## 5️⃣ APPLICATION MOBILE PWA 📱

### Fonctionnalités

- **Progressive Web App** installable sur mobile
- **Scan QR code** en temps réel avec la caméra
- **Mode hors ligne** avec synchronisation automatique
- **Authentification** via API avec tokens Sanctum
- **Gestion des inventaires** :
  - Sélection de l'inventaire en cours
  - Scan de localisation pour démarrer
  - Scan des biens avec statuts
  - Prise de photos
  - Commentaires
- **Synchronisation** :
  - Stockage local (IndexedDB)
  - Synchronisation automatique en ligne
  - Synchronisation manuelle
  - Badge des scans en attente

### Architecture PWA

```1054:1481:public/pwa/app.js
class ScannerManager {
    constructor() {
        this.html5QrCode = null;
        // Gestion du scanner QR code
    }

    async handleLocalisationScan(qrData) {
        // Traitement du scan de localisation
        // Vérification de l'inventaire
        // Démarrrage du scan de la localisation
    }

    async handleBienScan(qrData) {
        // Traitement du scan de bien
        // Vérification du statut
        // Enregistrement du scan
    }
}
```

### API Endpoints


## 6️⃣ DASHBOARD ET STATISTIQUES 📊

### Vue d'ensemble

- **Statistiques globales** :
  - Total des biens
  - Total des localisations
  - Nombre de bâtiments
  - Valeur totale du parc
  - Biens créés cette année
- **Inventaire en cours** :
  - Progression globale
  - Taux de conformité
  - Répartition par statut
  - Progression par service
  - Localisations en cours
- **Dernières actions** :
  - Scans récents
  - Biens créés
  - Inventaires démarrés/clôturés


## 7️⃣ SÉCURITÉ ET PERMISSIONS 🔒

### Middleware de Sécurité

- **Authentification** : Toutes les routes nécessitent une authentification
- **Session timeout** : Déconnexion automatique après inactivité
- **Rôles et permissions** :
  - `admin` : Accès complet
  - `inventory` : Accès aux inventaires (admin + agent)
- **API Sanctum** : Authentification par tokens pour la PWA


## 8️⃣ EXPORTS ET RAPPORTS 📄

### Formats d'Export

- **PDF** : Rapports d'inventaire formatés
- **Excel** : Exports avec plusieurs feuilles :
  - Liste des biens
  - Liste des localisations
  - Statistiques
  - Scans par statut
- **Impression** : Rapports imprimables

### Services de Génération

- `RapportService` : Génération de rapports PDF
- `InventaireService` : Génération d'exports Excel
- Templates personnalisés pour chaque type de rapport

---

## 🔄 ÉVÉNEMENTS ET NOTIFICATIONS

### Événements Système

- `BienScanne` : Déclenché lors d'un scan
- `InventaireDemarre` : Déclenché au démarrage d'un inventaire
- `InventaireTermine` : Déclenché à la fin d'un inventaire
- `InventaireCloture` : Déclenché à la clôture d'un inventaire

---

## 📱 TECHNOLOGIES UTILISÉES

### Backend
- **Laravel 11** : Framework PHP
- **Livewire 3** : Composants réactifs
- **Laravel Sanctum** : Authentification API
- **DomPDF** : Génération de PDF
- **PhpSpreadsheet** : Génération d'Excel

### Frontend
- **Tailwind CSS** : Framework CSS
- **Alpine.js** : Interactivité JavaScript
- **HTML5 QR Code Scanner** : Scan QR code

### PWA
- **Service Worker** : Mode hors ligne
- **IndexedDB** : Stockage local
- **Manifest.json** : Installation PWA

---

## 🎯 WORKFLOW TYPIQUE D'UN INVENTAIRE

1. **Préparation** (Admin)
   - Création de l'inventaire annuel
   - Sélection des localisations à inventorier
   - Assignation des agents aux localisations
   - Démarrage de l'inventaire

2. **Scan sur le Terrain** (Agent)
   - Connexion à la PWA mobile
   - Sélection de l'inventaire en cours
   - Scan du QR code de la localisation
   - Scan des QR codes des biens
   - Enregistrement des statuts (présent, déplacé, absent, détérioré)
   - Prise de photos si nécessaire
   - Synchronisation automatique ou manuelle

3. **Suivi** (Admin/Agent)
   - Consultation du dashboard
   - Suivi de la progression
   - Visualisation des statistiques
   - Détection d'anomalies

4. **Clôture** (Admin)
   - Finalisation de tous les scans
   - Génération du rapport
   - Export PDF/Excel
   - Clôture de l'inventaire

---

## 📊 STATISTIQUES ET INDICATEURS

### Métriques Principales

- **Progression globale** : % de localisations terminées
- **Taux de conformité** : % de biens présents
- **Répartition par statut** : Présent, déplacé, absent, détérioré
- **Progression par service** : Suivi par service/département
- **Durée de l'inventaire** : Nombre de jours
- **Valeur totale** : Valeur du parc de biens

---

## 🎨 INTERFACE UTILISATEUR

- **Design moderne** avec Tailwind CSS
- **Interface responsive** pour mobile et desktop
- **Navigation intuitive** avec menus et breadcrumbs
- **Tableaux interactifs** avec filtres et recherche
- **Graphiques et visualisations** pour les statistiques
- **Notifications toast** pour les actions utilisateur

---

## 🔧 MAINTENANCE ET ADMINISTRATION

- **Logs système** : Traçabilité des actions
- **Gestion des erreurs** : Gestion centralisée des exceptions
- **Backup automatique** : Sauvegarde de la base de données
- **Migration de données** : Système de migrations Laravel
- **Seeders** : Données de test et initialisation

---

*Document généré automatiquement - Système GESIMMOS v1.0*

