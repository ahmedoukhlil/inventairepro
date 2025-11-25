# Guide : PWA sur Réseau Local

> 📘 **Pour un guide détaillé de configuration HTTPS sur WAMP, voir : `CONFIGURATION_HTTPS_WAMP.md`**

## ✅ Oui, les PWA fonctionnent sur un réseau local !

Les PWA peuvent fonctionner sur un réseau local, mais avec certaines conditions :

### 📋 Conditions pour les PWA

1. **localhost** : ✅ Fonctionne toujours (même en HTTP)
2. **127.0.0.1** : ✅ Fonctionne toujours (même en HTTP)
3. **IP locale (192.168.x.x)** : ⚠️ Nécessite HTTPS
4. **Nom de domaine local** : ⚠️ Nécessite HTTPS

## 🔧 Solutions pour Réseau Local

### Option 1 : Utiliser localhost (Développement)

Si vous accédez via `http://localhost:8000`, ça fonctionne directement !

**Avantages :**
- ✅ Pas de configuration nécessaire
- ✅ Service Worker fonctionne
- ✅ PWA installable

**Inconvénients :**
- ❌ Seulement sur la machine locale
- ❌ Pas accessible depuis d'autres appareils du réseau

### Option 2 : HTTPS avec Certificat Auto-signé (Recommandé pour LAN)

Pour accéder depuis d'autres appareils du réseau (192.168.x.x), vous devez configurer HTTPS.

#### Étape 1 : Installer mkcert (Génère des certificats valides localement)

**Windows :**
```powershell
# Installer via Chocolatey
choco install mkcert

# Ou télécharger depuis : https://github.com/FiloSottile/mkcert/releases
```

**Linux/Mac :**
```bash
# Installer via Homebrew (Mac)
brew install mkcert

# Ou via apt (Linux)
sudo apt install mkcert
```

#### Étape 2 : Créer un certificat local

```bash
# Créer l'autorité de certification locale
mkcert -install

# Créer un certificat pour votre IP locale (remplacez 192.168.1.100 par votre IP)
mkcert 192.168.1.100 localhost 127.0.0.1

# Cela crée deux fichiers :
# - 192.168.1.100+2.pem (certificat)
# - 192.168.1.100+2-key.pem (clé privée)
```

#### Étape 3 : Configurer WAMP avec HTTPS

1. **Copier les fichiers de certificat** dans un dossier sécurisé :
   ```
   C:\wamp64\bin\apache\apache2.4.x\conf\ssl\
   ```

2. **Activer le module SSL dans WAMP** :
   - Cliquer sur l'icône WAMP
   - Apache → Modules Apache → `ssl_module` (cocher)

3. **Configurer Apache SSL** :
   
   Éditer `C:\wamp64\bin\apache\apache2.4.x\conf\extra\httpd-ssl.conf` :
   
   ```apache
   <VirtualHost *:443>
       ServerName 192.168.1.100
       DocumentRoot "C:/wamp64/www/gesimmos/public"
       
       SSLEngine on
       SSLCertificateFile "C:/wamp64/bin/apache/apache2.4.x/conf/ssl/192.168.1.100+2.pem"
       SSLCertificateKeyFile "C:/wamp64/bin/apache/apache2.4.x/conf/ssl/192.168.1.100+2-key.pem"
       
       <Directory "C:/wamp64/www/gesimmos/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

4. **Redémarrer Apache**

5. **Accéder via HTTPS** :
   ```
   https://192.168.1.100
   ```

### Option 3 : Utiliser Laravel Valet (Mac/Linux)

Si vous êtes sur Mac ou Linux, Laravel Valet configure automatiquement HTTPS :

```bash
# Installer Valet
composer global require laravel/valet
valet install

# Configurer le projet
cd /path/to/gesimmos
valet link inventaire-pro

# Accéder via HTTPS automatique
https://inventaire-pro.test
```

### Option 4 : Utiliser ngrok (Tunnel HTTPS)

Pour tester rapidement sans configuration serveur :

```bash
# Installer ngrok
# Télécharger depuis : https://ngrok.com/download

# Créer un tunnel HTTPS
ngrok http 8000

# Vous obtiendrez une URL HTTPS publique :
# https://abc123.ngrok.io
```

**Avantages :**
- ✅ Configuration instantanée
- ✅ Accessible depuis n'importe où
- ✅ HTTPS automatique

**Inconvénients :**
- ❌ URL change à chaque démarrage (gratuit)
- ❌ Limite de bande passante (gratuit)

## 🔍 Vérifier que HTTPS fonctionne

1. **Ouvrir l'application** dans le navigateur
2. **Ouvrir la Console** (F12)
3. **Vérifier le Service Worker** :
   - Onglet "Application" → "Service Workers"
   - Doit être "actif et en cours d'exécution"

4. **Vérifier le Manifest** :
   - Onglet "Application" → "Manifest"
   - Doit afficher les informations de l'app

5. **Tester l'installation PWA** :
   - Le bouton d'installation doit apparaître
   - Ou menu → "Installer l'application"

## ⚠️ Accepter le Certificat Auto-signé

Quand vous accédez pour la première fois avec un certificat auto-signé :

1. Le navigateur affiche un **avertissement de sécurité**
2. Cliquer sur **"Avancé"** ou **"Advanced"**
3. Cliquer sur **"Continuer vers le site"** ou **"Proceed to site"**
4. Le certificat sera accepté pour ce site

## 🎯 Configuration Recommandée pour Réseau Local

### Scénario 1 : Développement seul
- ✅ Utiliser `http://localhost:8000`
- ✅ Pas de configuration nécessaire

### Scénario 2 : Test sur plusieurs appareils (même réseau)
- ✅ Configurer HTTPS avec mkcert
- ✅ Accéder via `https://192.168.x.x`
- ✅ Installer le certificat sur chaque appareil

### Scénario 3 : Production interne
- ✅ Utiliser un certificat valide (Let's Encrypt si possible)
- ✅ Ou configurer un reverse proxy (Nginx/Apache) avec HTTPS

## 📝 Notes Importantes

1. **Service Worker** : Nécessite HTTPS (sauf localhost)
2. **Manifest** : Fonctionne en HTTP sur localhost
3. **Installation PWA** : Nécessite HTTPS (sauf localhost)
4. **Cache** : Fonctionne même sans HTTPS sur localhost

## 🐛 Dépannage

### Le Service Worker ne se charge pas

**Vérifier :**
- ✅ Vous êtes en HTTPS (ou localhost)
- ✅ Le fichier `sw.js` est accessible
- ✅ Pas d'erreurs dans la console

**Solution :**
```javascript
// Vérifier dans la console
navigator.serviceWorker.getRegistrations().then(console.log);
```

### Le bouton d'installation n'apparaît pas

**Vérifier :**
- ✅ HTTPS activé (ou localhost)
- ✅ Manifest.json accessible
- ✅ Icônes présentes
- ✅ Service Worker actif

**Forcer l'affichage :**
- Ouvrir DevTools → Application → Manifest
- Cliquer sur "Add to Home Screen" (mobile)

### Erreur "Mixed Content"

**Problème :** Certaines ressources chargées en HTTP sur une page HTTPS

**Solution :** S'assurer que toutes les ressources utilisent HTTPS ou des chemins relatifs

## 🚀 Quick Start pour Réseau Local

1. **Installer mkcert** (voir Option 2)
2. **Créer le certificat** pour votre IP locale
3. **Configurer Apache** avec SSL
4. **Accéder via HTTPS** : `https://192.168.x.x`
5. **Accepter le certificat** dans le navigateur
6. **Installer l'application PWA** !

Votre PWA fonctionnera parfaitement sur votre réseau local ! 🎉

