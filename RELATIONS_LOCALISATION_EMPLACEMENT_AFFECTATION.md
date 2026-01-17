# Relations entre Localisation, Emplacement et Affectation

## Structure des Tables

### 1. Table `affectation`
- **Clé primaire**: `idAffectation` (INT, auto-increment)
- **Colonnes**:
  - `Affectation` (STRING) - Nom de l'affectation
  - `CodeAffectation` (STRING, nullable) - Code de l'affectation
- **Modèle**: `App\Models\Affectation`

### 2. Table `localisation`
- **Clé primaire**: `idLocalisation` (INT, auto-increment)
- **Colonnes**:
  - `Localisation` (STRING) - Nom de la localisation
  - `CodeLocalisation` (STRING, nullable) - Code de la localisation
- **Modèle**: `App\Models\LocalisationImmo`
- **Note**: Il existe aussi une table `localisations` (pluriel) qui est différente et utilisée pour les inventaires

### 3. Table `emplacement`
- **Clé primaire**: `idEmplacement` (INT, auto-increment)
- **Colonnes**:
  - `Emplacement` (STRING) - Nom de l'emplacement
  - `CodeEmplacement` (STRING, nullable) - Code de l'emplacement
  - `idAffectation` (INT) - **Clé étrangère** vers `affectation.idAffectation`
  - `idLocalisation` (INT) - **Clé étrangère** vers `localisation.idLocalisation`
- **Modèle**: `App\Models\Emplacement`

## Relations Eloquent

### Affectation → Emplacement
```php
// Affectation a plusieurs Emplacements
Affectation::emplacements() // HasMany
```

### LocalisationImmo → Emplacement
```php
// LocalisationImmo a plusieurs Emplacements
LocalisationImmo::emplacements() // HasMany
```

### Emplacement → Affectation
```php
// Emplacement appartient à une Affectation
Emplacement::affectation() // BelongsTo
```

### Emplacement → LocalisationImmo
```php
// Emplacement appartient à une LocalisationImmo
Emplacement::localisation() // BelongsTo
```

### Emplacement → Gesimmo (Immobilisations)
```php
// Emplacement a plusieurs Immobilisations
Emplacement::immobilisations() // HasMany
```

## Hiérarchie des Relations

```
Affectation (1)
    └── Emplacement (N)
            ├── LocalisationImmo (1)
            └── Gesimmo (N) - Immobilisations
```

**Schéma relationnel**:
- **1 Affectation** → **N Emplacements**
- **1 LocalisationImmo** → **N Emplacements**
- **1 Emplacement** → **N Gesimmo** (Immobilisations)

## Structure Hiérarchique Complète

```
LocalisationImmo (Niveau 1 - Localisation générale)
    └── Emplacement (Niveau 2 - Emplacement spécifique)
            ├── Affectation (Type d'affectation)
            └── Gesimmo (Immobilisations assignées)
```

## Exemple de Données

### Affectation
```
idAffectation: 1
Affectation: "Bureau"
CodeAffectation: "BUR"
```

### LocalisationImmo
```
idLocalisation: 1
Localisation: "Bâtiment A"
CodeLocalisation: "BAT-A"
```

### Emplacement
```
idEmplacement: 1
Emplacement: "Bureau 101"
CodeEmplacement: "BUR-101"
idAffectation: 1 (→ Bureau)
idLocalisation: 1 (→ Bâtiment A)
```

### Gesimmo (Immobilisation)
```
NumOrdre: 1
idEmplacement: 1 (→ Bureau 101)
...
```

## Utilisation dans le Code

### Récupérer tous les emplacements d'une localisation
```php
$localisation = LocalisationImmo::find(1);
$emplacements = $localisation->emplacements;
```

### Récupérer tous les emplacements d'une affectation
```php
$affectation = Affectation::find(1);
$emplacements = $affectation->emplacements;
```

### Récupérer la localisation et l'affectation d'un emplacement
```php
$emplacement = Emplacement::with(['localisation', 'affectation'])->find(1);
$localisation = $emplacement->localisation; // LocalisationImmo
$affectation = $emplacement->affectation; // Affectation
```

### Récupérer toutes les immobilisations d'un emplacement
```php
$emplacement = Emplacement::find(1);
$immobilisations = $emplacement->immobilisations; // Collection de Gesimmo
```

## Différence entre `localisation` et `localisations`

### Table `localisation` (singulier)
- Modèle: `LocalisationImmo`
- Utilisée pour la structure hiérarchique principale
- Relation avec `Emplacement`
- Colonnes: `idLocalisation`, `Localisation`, `CodeLocalisation`

### Table `localisations` (pluriel)
- Modèle: `Localisation`
- Utilisée pour les inventaires et la gestion moderne
- Relation avec `InventaireLocalisation`
- Colonnes: `id`, `code`, `designation`, `batiment`, `etage`, `service_rattache`, etc.
- A des timestamps (`created_at`, `updated_at`)

## Points Importants

1. **Emplacement est la table centrale** qui lie Affectation et LocalisationImmo
2. **Un Emplacement** appartient à **une Affectation** ET **une LocalisationImmo**
3. **Les Immobilisations (Gesimmo)** sont assignées à un **Emplacement**, pas directement à une Localisation ou Affectation
4. **Deux systèmes de localisation coexistent**:
   - `localisation` (ancien système) - utilisé avec Emplacement
   - `localisations` (nouveau système) - utilisé avec InventaireLocalisation
