# Guide : Configuration HTTPS sur WAMP pour PWA

> 📘 **Pour la production sans nom de domaine, voir : `HTTPS_SANS_DOMAINE_PRODUCTION.md`**

Ce guide vous explique comment configurer HTTPS sur WAMP pour que votre PWA fonctionne sur un réseau local.

## 📋 Prérequis

- WAMP installé et fonctionnel
- Connaître votre IP locale (ex: 192.168.1.100)
- Droits administrateur

## 🔧 Étape 1 : Installer mkcert

### Option A : Via Chocolatey (Recommandé)

1. **Installer Chocolatey** (si pas déjà installé) :
   - Ouvrir PowerShell en **Administrateur**
   - Exécuter :
   ```powershell
   Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
   ```

2. **Installer mkcert** :
   ```powershell
   choco install mkcert
   ```

### Option B : Téléchargement manuel

1. **Télécharger mkcert** :
   - Aller sur : https://github.com/FiloSottile/mkcert/releases
   - Télécharger `mkcert-v1.4.4-windows-amd64.exe` (ou version la plus récente)
   - Renommer en `mkcert.exe`

2. **Placer dans le PATH** :
   - Créer un dossier `C:\tools\mkcert\`
   - Y placer `mkcert.exe`
   - Ajouter `C:\tools\mkcert\` au PATH système

## 🔐 Étape 2 : Créer le certificat SSL

1. **Ouvrir PowerShell ou CMD** en Administrateur

2. **Trouver votre IP locale** :
   ```powershell
   ipconfig
   ```
   Notez votre IPv4 (ex: 192.168.1.100)

3. **Installer l'autorité de certification locale** :
   ```powershell
   mkcert -install
   ```
   Cela ajoute mkcert comme autorité de certification de confiance.

4. **Créer le certificat** :
   ```powershell
   # Remplacer 192.168.1.100 par votre IP locale
   mkcert 192.168.1.100 localhost 127.0.0.1
   ```
   
   Cela crée deux fichiers :
   - `192.168.1.100+2.pem` (certificat)
   - `192.168.1.100+2-key.pem` (clé privée)

5. **Créer le dossier SSL dans Apache** :
   ```powershell
   # Créer le dossier (remplacer 2.4.xx par votre version Apache)
   mkdir C:\wamp64\bin\apache\apache2.4.xx\conf\ssl
   ```

6. **Déplacer les fichiers de certificat** :
   ```powershell
   # Déplacer les fichiers créés vers le dossier ssl
   move 192.168.1.100+2.pem C:\wamp64\bin\apache\apache2.4.xx\conf\ssl\
   move 192.168.1.100+2-key.pem C:\wamp64\bin\apache\apache2.4.xx\conf\ssl\
   ```

## ⚙️ Étape 3 : Activer SSL dans WAMP

1. **Activer le module SSL** :
   - Cliquer sur l'**icône WAMP** dans la barre des tâches
   - Aller dans **Apache** → **Modules Apache**
   - Cocher **`ssl_module`**
   - Redémarrer Apache (WAMP → Redémarrer tous les services)

2. **Vérifier que SSL est activé** :
   - Ouvrir `http://localhost`
   - Cliquer sur **"phpinfo()"**
   - Chercher "SSL" dans la page
   - Vous devriez voir "SSL Version" affiché

## 📝 Étape 4 : Configurer Apache pour HTTPS

1. **Trouver votre version Apache** :
   - Regarder dans `C:\wamp64\bin\apache\`
   - Notez le numéro de version (ex: `apache2.4.64`)

2. **Éditer le fichier de configuration SSL** :
   - Ouvrir : `C:\wamp64\bin\apache\apache2.4.xx\conf\extra\httpd-ssl.conf`
   - Utiliser un éditeur de texte (Notepad++, VS Code, etc.)

3. **Ajouter la configuration VirtualHost** à la fin du fichier :

   ```apache
   # Configuration HTTPS pour Inventaire Pro
   <VirtualHost *:443>
       ServerName 192.168.1.100
       ServerAlias localhost
       DocumentRoot "C:/wamp64/www/gesimmos/public"
       
       # Activer SSL
       SSLEngine on
       SSLCertificateFile "C:/wamp64/bin/apache/apache2.4.xx/conf/ssl/192.168.1.100+2.pem"
       SSLCertificateKeyFile "C:/wamp64/bin/apache/apache2.4.xx/conf/ssl/192.168.1.100+2-key.pem"
       
       # Configuration du répertoire
       <Directory "C:/wamp64/www/gesimmos/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       # Logs
       ErrorLog "C:/wamp64/logs/apache_error_ssl.log"
       CustomLog "C:/wamp64/logs/apache_access_ssl.log" common
   </VirtualHost>
   ```

   **⚠️ Important :** Remplacez :
   - `192.168.1.100` par votre IP locale
   - `apache2.4.xx` par votre version Apache exacte
   - `192.168.1.100+2.pem` et `192.168.1.100+2-key.pem` par les noms exacts de vos fichiers

4. **Vérifier que httpd-ssl.conf est inclus** :
   - Ouvrir : `C:\wamp64\bin\apache\apache2.4.xx\conf\httpd.conf`
   - Chercher la ligne :
   ```apache
   #Include conf/extra/httpd-ssl.conf
   ```
   - **Décommenter** (enlever le #) :
   ```apache
   Include conf/extra/httpd-ssl.conf
   ```

5. **Vérifier que le port 443 est écouté** :
   - Dans `httpd.conf`, chercher :
   ```apache
   Listen 80
   ```
   - Ajouter juste en dessous :
   ```apache
   Listen 443
   ```

## 🔄 Étape 5 : Redémarrer WAMP

1. **Arrêter tous les services** :
   - Cliquer sur l'icône WAMP
   - **Redémarrer tous les services**

2. **Vérifier qu'Apache démarre correctement** :
   - L'icône WAMP doit être **verte**
   - Si orange ou rouge, vérifier les logs d'erreur

## ✅ Étape 6 : Tester HTTPS

1. **Tester localement** :
   - Ouvrir : `https://localhost`
   - Vous verrez un avertissement de sécurité (normal avec certificat auto-signé)
   - Cliquer sur **"Avancé"** → **"Continuer vers localhost"**

2. **Tester depuis l'IP locale** :
   - Ouvrir : `https://192.168.1.100` (remplacer par votre IP)
   - Accepter l'avertissement de sécurité

3. **Vérifier le Service Worker** :
   - Ouvrir les DevTools (F12)
   - Onglet **Application** → **Service Workers**
   - Le Service Worker doit être **actif**

## 📱 Étape 7 : Configurer les autres appareils

Pour que les autres appareils du réseau acceptent le certificat :

### Sur Windows (autres PC du réseau)

1. **Exporter le certificat root** :
   ```powershell
   # Trouver le certificat root mkcert
   certutil -store -user ROOT
   ```
   
2. **Ou installer directement** :
   - Le certificat root est dans : `%LOCALAPPDATA%\mkcert\rootCA.pem`
   - Double-cliquer dessus
   - **Installer le certificat** → **Placer tous les certificats dans le magasin suivant** → **Autorités de certification racines de confiance**

### Sur Android

1. **Transférer le fichier rootCA.pem** sur le téléphone
2. **Paramètres** → **Sécurité** → **Chiffrement et identifiants**
3. **Installer depuis le stockage** → Sélectionner `rootCA.pem`
4. **Nommer** : "mkcert Root CA"
5. **Installer**

### Sur iOS

1. **Transférer rootCA.pem** sur l'iPhone (via email, AirDrop, etc.)
2. **Ouvrir le fichier** sur l'iPhone
3. **Paramètres** → **Général** → **À propos de** → **Certificats de confiance**
4. **Activer** le certificat mkcert

## 🎯 Configuration Laravel pour HTTPS

1. **Modifier `.env`** :
   ```env
   APP_URL=https://192.168.1.100
   ```

2. **Forcer HTTPS dans Laravel** (optionnel) :
   
   Créer ou modifier `app/Providers/AppServiceProvider.php` :
   
   ```php
   public function boot(): void
   {
       if (config('app.env') === 'production' || request()->secure()) {
           \URL::forceScheme('https');
       }
   }
   ```

## 🐛 Dépannage

### Erreur : "Port 443 already in use"

**Solution :**
```powershell
# Trouver le processus utilisant le port 443
netstat -ano | findstr :443

# Arrêter le processus (remplacer PID par le numéro trouvé)
taskkill /PID <PID> /F
```

### Erreur : "SSL certificate problem"

**Vérifier :**
- ✅ Les chemins des fichiers de certificat sont corrects
- ✅ Les fichiers existent bien dans le dossier ssl
- ✅ Les permissions sont correctes

### Apache ne démarre pas

**Vérifier les logs :**
- `C:\wamp64\logs\apache_error.log`
- Chercher les erreurs liées à SSL

**Vérifications communes :**
- ✅ Module ssl_module activé
- ✅ Port 443 libre
- ✅ Syntaxe correcte dans httpd-ssl.conf
- ✅ Chemins des fichiers corrects (utiliser `/` au lieu de `\`)

### Le Service Worker ne se charge pas

**Vérifier :**
- ✅ Vous accédez bien en HTTPS (pas HTTP)
- ✅ Le fichier `sw.js` est accessible : `https://192.168.1.100/sw.js`
- ✅ Pas d'erreurs dans la console du navigateur

## ✅ Checklist Finale

- [ ] mkcert installé
- [ ] Certificat créé pour votre IP locale
- [ ] Module SSL activé dans WAMP
- [ ] VirtualHost configuré dans httpd-ssl.conf
- [ ] Port 443 écouté
- [ ] WAMP redémarré
- [ ] HTTPS accessible : `https://192.168.1.100`
- [ ] Service Worker actif
- [ ] PWA installable

## 🎉 C'est terminé !

Votre application est maintenant accessible en HTTPS sur votre réseau local. Vous pouvez :
- ✅ Installer la PWA sur tous les appareils du réseau
- ✅ Utiliser le Service Worker
- ✅ Profiter de toutes les fonctionnalités PWA

**Accès :** `https://192.168.1.100` (remplacer par votre IP)

