# GESIMMOS — Gestion des Immobilisations et des Stocks

Système de gestion intégré développé avec **Laravel 12 / Livewire 3**, couvrant trois domaines métier : les immobilisations, les inventaires et la gestion des stocks.

---

## Table des matières

1. [Aperçu général](#aperçu-général)
2. [Stack technique](#stack-technique)
3. [Module Immobilisations](#module-immobilisations)
4. [Module Inventaire](#module-inventaire)
5. [Module Stock](#module-stock)
6. [Gestion des accès](#gestion-des-accès)
7. [Export et impression](#export-et-impression)
8. [API mobile (PWA)](#api-mobile-pwa)
9. [Installation](#installation)

---

## Aperçu général

GESIMMOS permet à une organisation de :

- Tenir un registre complet de ses **immobilisations** (mobilier, équipements, matériel informatique, véhicules…)
- Conduire des **inventaires annuels** assistés par QR code / code-barres, avec des agents terrain et un suivi en temps réel
- Gérer les **entrées et sorties de stock** avec alertes de seuil et génération de bons

L'application est accessible via navigateur (interface web réactive) et via une **PWA mobile** dédiée aux agents d'inventaire.

---

## Stack technique

| Composant | Technologie |
|---|---|
| Framework backend | Laravel 12 (PHP 8.2+) |
| Composants réactifs | Livewire 3 |
| Base de données | MySQL |
| Frontend | Tailwind CSS 4, Alpine.js, Vite 7 |
| Authentification | Laravel Breeze (web) + Sanctum (API) |
| Export Excel | Maatwebsite Excel |
| Export PDF | DomPDF |
| QR code / Code-barres | Simple QRCode, PHP Barcode Generator |

---

## Module Immobilisations

### Hiérarchie des localisations

```
Localisation (bâtiment/site)
  └── Affectation (service/département)
        └── Emplacement (bureau, salle, local)
              └── Bien (immobilisation)
```

### Fonctionnalités

- **CRUD complet** des biens avec métadonnées (désignation, catégorie, état, nature juridique, source de financement, date d'acquisition)
- **Transfert d'actifs** entre emplacements avec historique traçable
- **Étiquettes QR / code-barres** : génération individuelle ou en masse (format A4, 21 étiquettes/page)
- **Corbeille** : suppression logique (soft delete) et restauration unitaire ou en lot (par désignation ou par emplacement)
- **Export Excel** de la liste des biens avec filtres appliqués
- **Collecte initiale** : saisie vocale autonome pour les organisations sans base existante

---

## Module Inventaire

### Cycle de vie d'un inventaire

```
Création → En préparation → En cours → Terminé → Clôturé
```

### Workflow

1. L'administrateur crée un inventaire annuel et affecte des localisations à des agents
2. L'agent scanne le QR code de la localisation via la PWA
3. Il scanne chaque bien et renseigne l'état observé
4. Le tableau de bord affiche en temps réel : biens présents, déplacés, absents
5. L'administrateur génère les rapports et clôture l'inventaire

### Rapports disponibles (Excel multi-feuilles / PDF)

| Feuille | Contenu |
|---|---|
| Synthèse | Statistiques globales |
| Par localisation | Liste des biens par lieu |
| Par état | Répartition selon la condition |
| Présents / Déplacés / Absents | Résultats de l'inventaire |
| Performance agents | Taux de scan par agent |
| Journal des mouvements | Historique complet des scans |

---

## Module Stock

### Entités

- **Produits** : libellé, stock initial, stock courant, seuil d'alerte, catégorie, magasin
- **Entrées** (E-001/2026) : réception avec fournisseur optionnel et référence commande
- **Sorties** (001/2026) : déstockage avec demandeur et génération du **bon de sortie**
- **Magasins**, **Catégories**, **Fournisseurs**, **Demandeurs** : référentiels configurables

### Fonctionnalités

- Tableau de bord stock avec indicateurs d'alerte (stock faible / critique)
- Mise à jour atomique du stock courant à chaque entrée/sortie (verrous transactionnels)
- Impression du bon de sortie (PDF)
- Export Excel du stock

---

## Gestion des accès

Système RBAC (rôles / permissions) avec les profils suivants :

| Rôle | Accès |
|---|---|
| `superuser` / `admin` | Accès complet à tous les modules |
| `admin_stock` | Gestion complète du module Stock |
| `agent` / `agent_inventory` | Lecture et scan (inventaire) |
| `agent_stock` | Opérations Stock (entrées/sorties) |

Les actions sensibles sont tracées dans un **journal d'audit** (utilisateur, modèle, action, diff, horodatage). Les champs sensibles sont chiffrés avec la clé applicative.

---

## Export et impression

| Format | Contenu exportable |
|---|---|
| Excel | Biens, inventaire (multi-feuilles), stock, collecte initiale, corbeille |
| PDF | Rapport d'inventaire, bon de sortie, décision de transfert |
| Étiquettes | QR code + code-barres par bien ou par emplacement |

---

## API mobile (PWA)

L'API REST (`/api/v1/`) permet aux agents d'utiliser une application mobile autonome :

- Authentification Sanctum (token)
- Consultation des localisations assignées
- Démarrage / clôture d'une session de scan
- Enregistrement d'un scan (bien + état + commentaire)
- Lookup d'un bien ou d'une localisation par QR code
- Soumission de lots de collecte initiale

Le throttling est configuré à 5 tentatives de connexion par minute.

---

## Installation

```bash
# Prérequis : PHP 8.2+, Composer, Node 20+, MySQL

git clone <repo>
cd gesimmos

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configurer .env (DB_DATABASE=bdimmos, DB_USERNAME=root, …)
php artisan migrate --seed

npm run build
php artisan serve
```

### Développement

```bash
composer dev   # Lance simultanément : serveur PHP, queue, logs, Vite (hot reload)
```

---

*GESIMMOS — Tous droits réservés*
