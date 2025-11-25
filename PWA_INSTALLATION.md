# Guide d'installation PWA - Inventaire Pro

Ce guide explique comment installer l'application **Inventaire Pro** en tant qu'application Progressive Web App (PWA) sur différents appareils.

## 📱 Installation sur Mobile

### Android (Chrome)

1. **Ouvrir l'application** dans Chrome
2. **Menu** (trois points en haut à droite)
3. **"Ajouter à l'écran d'accueil"** ou **"Installer l'application"**
4. Confirmer l'installation
5. L'application apparaîtra sur l'écran d'accueil

### iOS (Safari)

1. **Ouvrir l'application** dans Safari
2. **Partager** (icône carrée avec flèche)
3. **"Sur l'écran d'accueil"**
4. Personnaliser le nom si nécessaire
5. **"Ajouter"**
6. L'application apparaîtra sur l'écran d'accueil

## 💻 Installation sur Desktop

### Chrome / Edge (Windows, macOS, Linux)

1. **Ouvrir l'application** dans Chrome ou Edge
2. **Icône d'installation** dans la barre d'adresse (ou menu → Installer)
3. Cliquer sur **"Installer"**
4. L'application s'ouvrira dans une fenêtre dédiée

### Firefox

Firefox ne supporte pas encore l'installation PWA de manière native. Vous pouvez :
- Utiliser Chrome ou Edge pour l'installation
- Ajouter un raccourci manuel sur le bureau

## 🔧 Prérequis techniques

### Pour le développeur

1. **Serveur HTTPS requis** : Les PWA nécessitent HTTPS (sauf localhost)
2. **Fichiers créés** :
   - `public/manifest.json` - Configuration PWA
   - `public/sw.js` - Service Worker pour le cache
   - Icônes dans `public/images/icons/`

3. **Icônes nécessaires** :
   - icon-72x72.png
   - icon-96x96.png
   - icon-128x128.png
   - icon-144x144.png
   - icon-152x152.png
   - icon-192x192.png
   - icon-384x384.png
   - icon-512x512.png

### Génération des icônes

Si vous n'avez pas encore les icônes, vous pouvez :

1. **Utiliser un générateur en ligne** :
   - [PWA Asset Generator](https://github.com/elegantapp/pwa-asset-generator)
   - [RealFaviconGenerator](https://realfavicongenerator.net/)

2. **Créer manuellement** :
   - Créer une image 512x512px avec votre logo
   - Redimensionner aux différentes tailles
   - Placer dans `public/images/icons/`

## ✅ Vérification de l'installation

### Vérifier que le Service Worker fonctionne

1. Ouvrir les **Outils de développement** (F12)
2. Aller dans l'onglet **Application** (Chrome) ou **Stockage** (Firefox)
3. Vérifier que le **Service Worker** est actif
4. Vérifier que le **Manifest** est chargé

### Tester l'installation

1. L'application doit être accessible hors ligne (après première visite)
2. L'icône doit apparaître sur l'écran d'accueil
3. L'application doit s'ouvrir en mode standalone (sans barre d'adresse)

## 🐛 Dépannage

### Le bouton d'installation n'apparaît pas

- Vérifier que vous êtes en HTTPS (ou localhost)
- Vérifier que le manifest.json est accessible
- Vérifier la console pour les erreurs

### Le Service Worker ne se charge pas

- Vérifier que `public/sw.js` existe
- Vérifier les permissions dans la console
- Vider le cache et recharger

### L'application ne fonctionne pas hors ligne

- Vérifier que les ressources sont mises en cache
- Vérifier la stratégie de cache dans `sw.js`
- Vérifier la console pour les erreurs de cache

## 📝 Notes importantes

- **HTTPS requis** : Les PWA nécessitent HTTPS en production (sauf localhost)
- **Réseau local** : Les PWA fonctionnent sur réseau local avec HTTPS (voir `PWA_RESEAU_LOCAL.md`)
- **localhost** : Fonctionne en HTTP (développement local uniquement)
- **Mise à jour** : Le Service Worker vérifie les mises à jour automatiquement
- **Cache** : Les données sont mises en cache pour un fonctionnement hors ligne
- **API** : Les requêtes API ne sont pas mises en cache (doivent être en ligne)

## 🌐 PWA sur Réseau Local

Pour utiliser la PWA sur un réseau local (LAN), consultez le guide détaillé : **`PWA_RESEAU_LOCAL.md`**

**Résumé rapide :**
- ✅ `localhost` : Fonctionne en HTTP
- ⚠️ IP locale (192.168.x.x) : Nécessite HTTPS
- 🔧 Solution : Utiliser mkcert pour créer un certificat local valide

## 🔄 Mise à jour de l'application

Lorsqu'une nouvelle version est déployée :

1. Le Service Worker détecte automatiquement la mise à jour
2. L'utilisateur sera notifié lors de la prochaine visite
3. L'application se mettra à jour en arrière-plan

Pour forcer une mise à jour immédiate, vider le cache dans les paramètres du navigateur.

