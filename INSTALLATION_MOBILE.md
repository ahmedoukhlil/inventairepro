# Guide d'Installation PWA sur Mobile

Ce guide explique comment installer l'application **Inventaire Pro** en tant que Progressive Web App (PWA) sur iPhone (iOS) et Android.

## 📱 Prérequis

- ✅ L'application doit être accessible via HTTPS (ou localhost pour le développement)
- ✅ Le Service Worker doit être actif
- ✅ Le manifest.json doit être configuré correctement

## 🍎 Installation sur iPhone (iOS)

### Méthode 1 : Via Safari (Recommandé)

1. **Ouvrir Safari** (pas Chrome ou Firefox sur iOS)
   - Safari est le seul navigateur qui supporte l'installation PWA sur iOS

2. **Accéder à l'application**
   - Ouvrir Safari
   - Aller à l'URL de l'application (ex: `https://votre-domaine.com`)

3. **Afficher le menu de partage**
   - Appuyer sur le bouton **Partager** (icône carrée avec flèche vers le haut)
   - Situé en bas de l'écran sur iPhone, en haut sur iPad

4. **Ajouter à l'écran d'accueil**
   - Faire défiler vers le bas dans le menu de partage
   - Appuyer sur **"Sur l'écran d'accueil"** ou **"Ajouter à l'écran d'accueil"**

5. **Personnaliser le nom** (optionnel)
   - Modifier le nom si nécessaire
   - Appuyer sur **"Ajouter"** en haut à droite

6. **Lancer l'application**
   - L'icône apparaît sur l'écran d'accueil
   - Appuyer sur l'icône pour lancer l'application en mode standalone

### Méthode 2 : Via le menu Safari

1. Ouvrir Safari et accéder à l'application
2. Appuyer sur le bouton **Partage** (icône carrée avec flèche)
3. Faire défiler et sélectionner **"Sur l'écran d'accueil"**
4. Confirmer l'ajout

### ⚠️ Notes importantes pour iOS

- **Safari uniquement** : Chrome et Firefox sur iOS ne supportent pas l'installation PWA
- **iOS 11.3+** : Nécessite iOS 11.3 ou supérieur
- **Pas de bannière automatique** : iOS n'affiche pas de bannière d'installation automatique
- **Mode standalone** : L'application s'ouvre sans la barre d'adresse Safari

## 🤖 Installation sur Android

### Méthode 1 : Via Chrome (Recommandé)

1. **Ouvrir Chrome**
   - Utiliser Google Chrome (pas Firefox ou autres navigateurs)

2. **Accéder à l'application**
   - Aller à l'URL de l'application (ex: `https://votre-domaine.com`)

3. **Afficher le menu**
   - Appuyer sur le menu (3 points en haut à droite)

4. **Installer l'application**
   - Sélectionner **"Ajouter à l'écran d'accueil"** ou **"Installer l'application"**
   - Une bannière peut aussi apparaître automatiquement en bas de l'écran

5. **Confirmer l'installation**
   - Appuyer sur **"Installer"** dans la popup de confirmation
   - L'application sera installée sur l'écran d'accueil

### Méthode 2 : Via la bannière d'installation

1. Ouvrir Chrome et accéder à l'application
2. Une **bannière d'installation** peut apparaître automatiquement en bas de l'écran
3. Appuyer sur **"Installer"** ou **"Ajouter"**
4. Confirmer l'installation

### Méthode 3 : Via le menu Chrome

1. Ouvrir Chrome et accéder à l'application
2. Appuyer sur le menu (3 points)
3. Sélectionner **"Installer l'application"** ou **"Ajouter à l'écran d'accueil"**
4. Confirmer

### ⚠️ Notes importantes pour Android

- **Chrome recommandé** : Chrome supporte le mieux les PWA sur Android
- **Android 5.0+** : Nécessite Android 5.0 (Lollipop) ou supérieur
- **Bannière automatique** : Chrome affiche souvent une bannière d'installation automatique
- **Mode standalone** : L'application s'ouvre comme une application native

## 🔧 Dépannage

### Problème : L'option "Ajouter à l'écran d'accueil" n'apparaît pas

**Solutions :**
- ✅ Vérifier que vous utilisez Safari (iOS) ou Chrome (Android)
- ✅ Vérifier que l'application est accessible via HTTPS
- ✅ Vérifier que le manifest.json est correctement configuré
- ✅ Vider le cache du navigateur et recharger
- ✅ Vérifier que le Service Worker est actif (DevTools > Application > Service Workers)

### Problème : L'application ne s'installe pas

**Solutions :**
- ✅ Vérifier la connexion Internet
- ✅ Vérifier que le manifest.json est accessible (`/manifest.json`)
- ✅ Vérifier les erreurs dans la console (DevTools)
- ✅ Vérifier que les icônes sont accessibles

### Problème : L'application ne fonctionne pas hors ligne

**Solutions :**
- ✅ Vérifier que le Service Worker est enregistré
- ✅ Vérifier que les fichiers sont mis en cache
- ✅ Tester en mode avion après avoir visité l'application en ligne

## 📋 Checklist d'Installation

### Avant l'installation
- [ ] L'application est accessible via HTTPS
- [ ] Le manifest.json est accessible
- [ ] Les icônes sont présentes et accessibles
- [ ] Le Service Worker est actif

### Pendant l'installation
- [ ] Utiliser Safari (iOS) ou Chrome (Android)
- [ ] Suivre les étapes d'installation
- [ ] Confirmer l'installation

### Après l'installation
- [ ] L'icône apparaît sur l'écran d'accueil
- [ ] L'application s'ouvre en mode standalone
- [ ] L'application fonctionne hors ligne (après première visite)

## 🎯 Fonctionnalités PWA Disponibles

Une fois installée, l'application PWA offre :

- ✅ **Mode standalone** : S'ouvre comme une application native
- ✅ **Icône sur l'écran d'accueil** : Accès rapide
- ✅ **Fonctionnement hors ligne** : Utilisation sans connexion Internet
- ✅ **Notifications push** : (si configurées)
- ✅ **Mise à jour automatique** : Le Service Worker gère les mises à jour

## 📱 Instructions Visuelles

### iPhone (iOS)

```
1. Safari → URL de l'application
2. Bouton Partager (icône carrée avec flèche)
3. Faire défiler vers le bas
4. "Sur l'écran d'accueil"
5. "Ajouter"
```

### Android

```
1. Chrome → URL de l'application
2. Menu (3 points) → "Installer l'application"
   OU
   Bannière automatique → "Installer"
3. Confirmer
```

## 🔐 Sécurité

- ✅ L'application nécessite HTTPS en production
- ✅ Les données sont stockées localement sur l'appareil
- ✅ Le Service Worker gère le cache de manière sécurisée

## 📞 Support

Si vous rencontrez des problèmes :

1. Vérifier les prérequis (HTTPS, manifest, Service Worker)
2. Consulter les DevTools pour les erreurs
3. Vérifier la console du navigateur
4. Tester sur un autre appareil/navigateur

---

**Dernière mise à jour :** {{ date('d/m/Y') }}
**Version de l'application :** 1.0.0

