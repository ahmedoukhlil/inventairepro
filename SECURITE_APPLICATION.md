# Guide de Sécurité - Inventaire Pro

Ce document décrit les mesures de sécurité implémentées dans l'application Inventaire Pro.

## 🔒 Mesures de Sécurité Implémentées

### 1. Protection contre les Injections SQL

#### ✅ Utilisation d'Eloquent ORM
- Toutes les requêtes utilisent Eloquent ORM qui échappe automatiquement les entrées
- Les requêtes préparées sont utilisées par défaut
- Aucune concaténation de chaînes SQL brute

**Exemple :**
```php
// ✅ SÉCURISÉ - Utilise les requêtes préparées
Bien::where('code_inventaire', $code)->first();

// ❌ NON SÉCURISÉ - Ne jamais faire ça
DB::raw("SELECT * FROM biens WHERE code_inventaire = '$code'");
```

#### ✅ Validation des Entrées
- Toutes les entrées utilisateur sont validées avant traitement
- Utilisation de FormRequest pour les validations complexes
- Validation stricte des types de données

### 2. Protection contre les Attaques XSS (Cross-Site Scripting)

#### ✅ Échappement Automatique dans Blade
- Blade échappe automatiquement toutes les variables avec `{{ }}`
- Utilisation de `{!! !!}` uniquement pour du contenu de confiance

**Exemple :**
```blade
{{-- ✅ SÉCURISÉ - Échappement automatique --}}
<div>{{ $bien->designation }}</div>

{{-- ⚠️ ATTENTION - Pas d'échappement, utiliser uniquement pour du HTML de confiance --}}
<div>{!! $bien->description_html !!}</div>
```

#### ✅ Validation et Sanitization
- Tous les champs texte sont validés avec des règles strictes
- Limitation de la longueur des champs
- Filtrage des caractères spéciaux si nécessaire

### 3. Protection CSRF (Cross-Site Request Forgery)

#### ✅ Tokens CSRF Automatiques
- Laravel génère automatiquement des tokens CSRF pour tous les formulaires
- Vérification automatique via le middleware `VerifyCsrfToken`
- Tokens régénérés après chaque action sensible

**Dans les formulaires :**
```blade
{{-- ✅ Token CSRF automatique --}}
@csrf
```

### 4. Authentification et Sessions

#### ✅ Déconnexion Automatique après 30 Minutes d'Inactivité
- Middleware `CheckSessionTimeout` vérifie l'activité utilisateur
- Session expirée après 30 minutes d'inactivité
- Régénération de session après connexion

**Configuration :**
- `config/session.php` : `lifetime => 30` (minutes)
- Middleware appliqué à toutes les routes authentifiées

#### ✅ Protection des Mots de Passe
- Hashage avec bcrypt (algorithme sécurisé)
- Minimum 8 caractères requis
- Comparaison sécurisée (timing-safe)

#### ✅ Rate Limiting sur le Login
- Limite : 5 tentatives de connexion par minute par IP
- Protection contre les attaques par force brute
- Messages d'erreur génériques (ne révèlent pas si l'email existe)

### 5. Validation des Formulaires

#### ✅ Règles de Validation Strictes

**Login :**
- Email : validation RFC + DNS, pattern regex, max 255 caractères
- Mot de passe : min 8 caractères, max 255 caractères

**Biens :**
- Désignation : required, string, max 255
- Nature : required, in:liste_valide
- Date : required, date, before_or_equal:today
- Valeur : required, numeric, min:0
- Localisation : required, exists:localisations,id

**Localisations :**
- Code : required, string, max 50, unique
- Désignation : required, string, max 255
- Étage : nullable, integer, min:-2, max:20

#### ✅ Messages d'Erreur Personnalisés
- Messages clairs et informatifs
- Pas de révélation d'informations sensibles

### 6. Sécurité des Cookies

#### ✅ Configuration Sécurisée
- `http_only => true` : Empêche l'accès JavaScript aux cookies
- `same_site => 'lax'` : Protection contre les attaques CSRF
- `secure => true` (en production avec HTTPS) : Transmission uniquement via HTTPS

### 7. Protection des Routes

#### ✅ Middlewares d'Authentification
- `auth` : Vérifie que l'utilisateur est connecté
- `session.timeout` : Vérifie l'expiration de session
- `admin` : Accès réservé aux administrateurs
- `inventory` : Accès pour admin et agents

### 8. Logging et Audit

#### ✅ Journalisation des Actions Sensibles
- Connexions réussies (IP, user agent, timestamp)
- Erreurs d'authentification
- Actions administratives (à implémenter si nécessaire)

## 🛡️ Bonnes Pratiques Appliquées

### ✅ Validation Côté Serveur
- Toujours valider côté serveur, même si validation côté client existe
- Ne jamais faire confiance aux données client

### ✅ Principe du Moindre Privilège
- Utilisateurs avec permissions minimales nécessaires
- Séparation des rôles (admin, agent)

### ✅ Régénération de Session
- Après connexion
- Après déconnexion
- Après actions sensibles

### ✅ Messages d'Erreur Génériques
- Ne pas révéler si un email existe dans la base
- Messages d'erreur informatifs mais non révélateurs

## 📋 Checklist de Sécurité

- [x] Protection contre les injections SQL (Eloquent ORM)
- [x] Protection contre les attaques XSS (échappement Blade)
- [x] Protection CSRF (tokens automatiques)
- [x] Déconnexion automatique après 30 min d'inactivité
- [x] Rate limiting sur le login (5 tentatives/min)
- [x] Validation stricte des formulaires
- [x] Hashage sécurisé des mots de passe (bcrypt)
- [x] Cookies sécurisés (http_only, same_site)
- [x] Middlewares d'authentification et autorisation
- [x] Logging des actions sensibles

## 🔐 Configuration Recommandée pour la Production

### Variables d'Environnement (.env)

```env
# Session
SESSION_LIFETIME=30
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaire_pro
DB_USERNAME=votre_user
DB_PASSWORD=votre_mot_de_passe_securise

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Sécurité Serveur

1. **HTTPS obligatoire** en production
2. **Firewall** configuré correctement
3. **Mises à jour** régulières du serveur
4. **Backups** réguliers de la base de données
5. **Monitoring** des logs d'erreur

## 🚨 En Cas de Vulnérabilité Détectée

1. **Ne pas divulguer** publiquement la vulnérabilité
2. **Corriger rapidement** le problème
3. **Tester** la correction
4. **Déployer** la correction en production
5. **Documenter** la vulnérabilité et sa correction

## 📚 Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [PHP Security Best Practices](https://www.php.net/manual/fr/security.php)

---

**Dernière mise à jour :** {{ date('d/m/Y') }}
**Version de l'application :** 1.0.0

