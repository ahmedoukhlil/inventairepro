# 📱 Application PWA Inventaire v2 - Workflow par Emplacement

## 🎯 Nouveau Workflow Simplifié

Au lieu de scanner chaque bien individuellement avec des confirmations, le nouveau workflow est basé sur les **emplacements** :

```
1. Générer QR codes des emplacements
   ↓
2. Scanner le QR code sur la porte d'un emplacement
   ↓
3. L'app affiche tous les biens affectés à cet emplacement
   ↓
4. Scanner les codes-barres 128 des étiquettes des biens
   ↓
5. Cliquer "Terminer" 
   ↓
6. L'app calcule automatiquement les écarts :
      - Biens scannés
      - Biens manquants
      - Taux de conformité
```

---

## 🚀 Installation et Configuration

### Étape 1 : Générer les QR codes des emplacements

1. Connectez-vous à l'application web
2. Accédez à : **`/qrcodes/emplacements`**
3. Filtrez par localisation ou affectation (optionnel)
4. Options disponibles :
   - **Imprimer sélection** : Sélectionnez les emplacements et imprimez
   - **Télécharger PDF** : Téléchargez un PDF avec tous les QR codes

### Étape 2 : Imprimer et coller les QR codes

1. Imprimez les QR codes générés
2. Découpez-les
3. Collez-les sur les portes des emplacements correspondants

**Format du QR code** : `EMP-{idEmplacement}`  
Exemple : `EMP-25` pour l'emplacement avec idEmplacement = 25

### Étape 3 : Accéder à la PWA v2

Ouvrez votre navigateur mobile et accédez à :
```
https://votre-domaine.com/pwa/index-v2.html
```

### Étape 4 : Installer l'application (optionnel)

Sur mobile :
1. Ouvrir dans le navigateur
2. Menu (⋮) → "Ajouter à l'écran d'accueil"
3. L'app s'ouvrira ensuite en mode standalone

---

## 📖 Guide d'utilisation

### Connexion

1. Entrez votre nom d'utilisateur et mot de passe
2. Cliquez sur "Se connecter"

### Scanner un emplacement

1. Cliquez sur "Activer la caméra"
2. Pointez vers le QR code sur la porte de l'emplacement
3. L'app charge automatiquement les biens de cet emplacement

### Scanner les biens

1. L'app affiche la liste de tous les biens attendus dans cet emplacement
2. Le scanner de codes-barres 128 se lance automatiquement
3. Scannez les étiquettes des biens un par un
4. Chaque bien scanné :
   - S'affiche en vert dans la liste
   - ✅ Apparaît comme "scanné"
   - La barre de progression avance

### Terminer le scan

1. Quand tous les biens sont scannés (ou que vous voulez arrêter)
2. Cliquez sur "Terminer"
3. L'app envoie les données et calcule les écarts

### Voir les résultats

L'app affiche :
- **Statistiques** :
  - Nombre de biens scannés
  - Nombre de biens manquants
  - Taux de conformité (%)
  
- **Biens Manquants** :
  - Liste détaillée des biens non scannés
  - Désignation, code inventaire, catégorie

---

## 🔧 Caractéristiques Techniques

### Technologies utilisées

| Techno | Usage |
|--------|-------|
| **QuaggaJS** | Scanner de codes-barres 128 |
| **getUserMedia API** | Accès à la caméra |
| **Service Worker** | Mode offline (si configuré) |
| **Tailwind CSS** | Interface utilisateur |
| **Laravel API** | Backend REST |

### Format des codes

1. **QR Code Emplacement** : `EMP-{idEmplacement}`  
   Exemple : `EMP-10`, `EMP-1348`

2. **Code-barres 128 Bien** : Contient le **NumOrdre**  
   Exemple : Si NumOrdre = 5001, le code-barres 128 = `5001`  
   ⚠️ **Important** : Le code-barres doit contenir uniquement le numéro d'ordre

### Endpoints API utilisés

```http
POST /api/v1/login
GET  /api/v1/emplacements/{id}/biens
POST /api/v1/emplacements/{id}/terminer
```

---

## 📊 Différences avec l'ancienne version

| Aspect | Ancienne Version | Nouvelle Version (v2) |
|--------|------------------|----------------------|
| **Point de départ** | Scanner un bureau (localisation) | Scanner un emplacement |
| **Scan des biens** | QR codes | Codes-barres 128 |
| **Validation** | Modal de confirmation par bien | Automatique + calcul à la fin |
| **Écarts** | Pas de calcul automatique | Calcul automatique des manquants |
| **Mode** | Inventaire général | Inventaire par emplacement |
| **Workflow** | 5-6 étapes par bien | 1 scan emplacement + scans biens |

---

## 🎨 Interface

### Page Login
- Formulaire simple
- Logo "Inventaire Pro v2"
- Indication "Scan par Emplacement"

### Page Scanner
- Instruction claire : "Scannez le QR code sur la porte"
- Zone caméra
- Bouton "Activer la caméra"

### Page Emplacement
- **Header** :
  - Nom de l'emplacement
  - Localisation et affectation
  - Bouton "Terminer"
  - Barre de progression (X/Y biens scannés)

- **Scanner** :
  - Zone caméra pour codes-barres 128
  - Détection automatique

- **Liste des biens** :
  - Tous les biens attendus
  - ✅ en vert quand scanné
  - ⚪ en gris quand non scanné

### Page Résultats
- **Statistiques** :
  - Carte verte : Nombre scannés
  - Carte rouge : Nombre manquants
  - Barre de conformité

- **Biens Manquants** :
  - Liste complète avec détails
  - Désignation, code, catégorie

- **Actions** :
  - "Nouveau Scan" : Retour au scanner

---

## ⚠️ Points d'attention

### 1. Codes-barres 128
- Les étiquettes doivent contenir le **NumOrdre** en Code 128
- Exemple : Pour le bien n°5001, le code-barres = `5001`
- La PWA cherche automatiquement par `NumOrdre` (clé primaire)

### 2. Permissions caméra
- L'app demande l'accès à la caméra
- Sur iOS : accepter dans les paramètres Safari
- Sur Android : accepter dans Chrome

### 3. Éclairage
- Scanner dans un environnement bien éclairé
- Tenir la caméra stable
- Distance : 10-20 cm du code-barres

### 4. Connexion internet
- Version actuelle nécessite une connexion
- Le mode offline peut être ajouté si nécessaire

---

## 🐛 Dépannage

### Le QR code ne se scanne pas
- Vérifiez le format : doit être `EMP-{id}`
- Assurez-vous que l'emplacement existe en base
- Nettoyez le QR code (pas de reflets)

### Le code-barres ne se détecte pas
- Augmentez la lumière
- Rapprochez/éloignez la caméra
- Vérifiez que c'est un code-barres 128
- Essayez de scanner lentement

### Erreur "Emplacement non trouvé"
- Vérifiez que l'idEmplacement existe en base
- Consultez la table `emplacement`

### Erreur "Bien non trouvé"
- Le code-barres n'existe pas dans `gesimmo.code_barre`
- Ou le bien est dans un autre emplacement

---

## 📞 Support

Pour toute question :
1. Consultez ce README
2. Vérifiez les logs dans la console navigateur (F12)
3. Contactez l'équipe de développement

---

## 📝 Notes de version

### v2.0.0 (2025-01-19)
- ✨ Nouveau workflow par emplacement
- 📸 Scanner codes-barres 128 avec QuaggaJS
- 📊 Calcul automatique des écarts
- 🎯 Interface simplifiée et intuitive
- ⚡ Performance améliorée

---

**Bon inventaire ! 🎉**
