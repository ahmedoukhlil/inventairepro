# Harmonisation avec la base de données bdimmos

Ce document décrit les changements effectués pour harmoniser l'application avec la structure de la base de données `bdimmos` selon le fichier `immos/immos.md`.

## ✅ Changements effectués

### 1. Configuration de la base de données

- **Fichier modifié**: `config/database.php`
- **Changements**:
  - Connexion par défaut changée de `sqlite` à `mysql`
  - Base de données par défaut changée de `laravel` à `bdimmos`

### 2. Modèles créés

Tous les modèles suivants ont été créés selon la structure de `immos.md`:

- ✅ `Categorie` - Table `categorie` (idCategorie, Categorie, CodeCategorie)
- ✅ `Designation` - Table `designation` (id, designation, CodeDesignation, idCat)
- ✅ `Etat` - Table `etat` (idEtat, Etat, CodeEtat)
- ✅ `Affectation` - Table `affectation` (idAffectation, Affectation, CodeAffectation)
- ✅ `LocalisationImmo` - Table `localisation` (idLocalisation, Localisation, CodeLocalisation)
- ✅ `Emplacement` - Table `emplacement` (idEmplacement, Emplacement, CodeEmplacement, idAffectation, idLocalisation)
- ✅ `NatureJuridique` - Table `naturejurdique` (idNatJur, NatJur, CodeNatJur)
- ✅ `SourceFinancement` - Table `sourcefinancement` (idSF, SourceFin, CodeSourceFin)
- ✅ `Gesimmo` - Table `gesimmo` (NumOrdre, idDesignation, idCategorie, idEtat, idEmplacement, idNatJur, idSF, DateAcquisition, Observations)
- ✅ `Code` - Table `codes` (id, idGesimmo, barcode)
- ✅ `Produit` - Table `produits` (idProduit, Produit, Unite)
- ✅ `Entree` - Table `entree` (idEntree, idProduit, idEmplacement, DateEntree, Quantite)
- ✅ `Sortie` - Table `sortie` (idSortie, idProduit, Quantite, DateSortie, SrvcDmndr, Observations)

### 3. Modèle User adapté

- **Fichier modifié**: `app/Models/User.php`
- **Changements**:
  - Clé primaire: `idUser` (au lieu de `id`)
  - Colonne nom d'utilisateur: `users` (au lieu de `email`)
  - Colonne mot de passe: `mdp` (au lieu de `password`)
  - Timestamps désactivés (`public $timestamps = false`)
  - Méthodes d'authentification adaptées pour utiliser `users` et `mdp`

### 4. Migrations créées

Toutes les migrations suivantes ont été créées dans `database/migrations/`:

1. `2026_01_15_214400_create_users_table_immos.php` - Table users
2. `2026_01_15_214401_create_categorie_table.php` - Table categorie
3. `2026_01_15_214402_create_designation_table.php` - Table designation
4. `2026_01_15_214403_create_etat_table.php` - Table etat
5. `2026_01_15_214404_create_affectation_table.php` - Table affectation
6. `2026_01_15_214405_create_localisation_table.php` - Table localisation
7. `2026_01_15_214406_create_emplacement_table.php` - Table emplacement
8. `2026_01_15_214407_create_naturejurdique_table.php` - Table naturejurdique
9. `2026_01_15_214408_create_sourcefinancement_table.php` - Table sourcefinancement
10. `2026_01_15_214409_create_gesimmo_table.php` - Table gesimmo
11. `2026_01_15_214410_create_codes_table.php` - Table codes
12. `2026_01_15_214411_create_produits_table.php` - Table produits
13. `2026_01_15_214412_create_entree_table.php` - Table entree
14. `2026_01_15_214413_create_sortie_table.php` - Table sortie

## ⚠️ Actions requises

### 1. Configuration de l'environnement

Assurez-vous que votre fichier `.env` contient les bonnes valeurs:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bdimmos
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Création de la base de données

Créez la base de données `bdimmos` dans MySQL:

```sql
CREATE DATABASE IF NOT EXISTS bdimmos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Exécution des migrations

Exécutez les migrations pour créer toutes les tables:

```bash
php artisan migrate
```

### 4. Adaptation des contrôleurs d'authentification

Les contrôleurs d'authentification actuels utilisent `email` pour l'authentification. Ils doivent être adaptés pour utiliser `users` (nom d'utilisateur) à la place.

**Fichiers à modifier**:
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Api/AuthController.php`

**Changements nécessaires**:
- Remplacer `email` par `users` dans les validations
- Remplacer `email` par `users` dans les requêtes de base de données
- Adapter les messages d'erreur

### 5. Adaptation des vues d'authentification

Les formulaires de connexion doivent utiliser un champ `users` (nom d'utilisateur) au lieu de `email`.

**Fichiers à modifier**:
- `resources/views/auth/login.blade.php`
- `public/pwa/app.js` (si applicable)

### 6. Migration des données existantes (si nécessaire)

Si vous avez des données existantes dans l'ancienne structure, vous devrez créer un script de migration pour:
- Convertir les emails en noms d'utilisateur
- Adapter les mots de passe si nécessaire
- Migrer les données des tables `biens` vers `gesimmo` (si applicable)

## 📋 Structure des relations

```
users (isolé)

gesimmo (table principale)
├── designation (idDesignation)
├── categorie (idCategorie)
├── etat (idEtat)
├── emplacement (idEmplacement)
│   ├── affectation (idAffectation)
│   └── localisation (idLocalisation)
├── naturejurdique (idNatJur)
├── sourcefinancement (idSF)
└── codes (NumOrdre → idGesimmo)

produits
├── entree (idProduit)
│   └── emplacement (idEmplacement)
└── sortie (idProduit)
```

## 🔍 Notes importantes

1. **Pas de timestamps**: Toutes les tables n'utilisent pas `created_at` et `updated_at`. Tous les modèles ont `public $timestamps = false;`

2. **Clés primaires personnalisées**: Plusieurs tables utilisent des noms de clés primaires non standards (`idUser`, `NumOrdre`, `idCategorie`, etc.). Tous les modèles spécifient `protected $primaryKey`.

3. **Noms de colonnes**: Certaines colonnes utilisent des noms en français ou des conventions non standards. Les modèles utilisent les noms exacts de la base de données.

4. **Codes générés**: Le code d'immobilisation est généré dynamiquement via un accessor dans le modèle `Gesimmo` au format: `CodeNatJur/CodeDesignation/CodeCategorie/Année/CodeSourceFin/NumOrdre`

5. **Stock disponible**: Le stock disponible des produits est calculé via un accessor dans le modèle `Produit` (somme des entrées - somme des sorties)

## 🚀 Prochaines étapes

1. Exécuter les migrations
2. Adapter les contrôleurs d'authentification
3. Adapter les vues d'authentification
4. Tester l'authentification avec la nouvelle structure
5. Migrer les données existantes si nécessaire
6. Adapter les contrôleurs et vues qui utilisent les anciens modèles (`Bien`, `Localisation`) vers les nouveaux modèles (`Gesimmo`, `LocalisationImmo`, etc.)
