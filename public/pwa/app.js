/**
 * Application JavaScript principale pour le scanner PWA
 * Gère l'authentification, le scan QR code, la communication API, 
 * le stockage local et la synchronisation
 */

// ============================================
// CONFIGURATION ET CONSTANTES
// ============================================

const CONFIG = {
    API_BASE_URL: window.location.origin + '/api/v1',
    STORAGE_KEY_TOKEN: 'inventaire_token',
    STORAGE_KEY_USER: 'inventaire_user',
    STORAGE_KEY_INVENTAIRE: 'inventaire_current',
    STORAGE_KEY_LOCATION: 'inventaire_active_location',
    DB_NAME: 'InventaireDB',
    DB_VERSION: 1
};

// ============================================
// STATE MANAGEMENT
// ============================================

const AppState = {
    token: null,
    user: null,
    inventaire: null,
    activeLocation: null, // InventaireLocalisation actuelle
    localisation: null, // Localisation entity
    biensAttendus: [],
    scansSession: [],
    isOnline: navigator.onLine,
    pendingScansCount: 0,
    html5QrCode: null
};

// ============================================
// INDEXEDDB MANAGEMENT
// ============================================

/**
 * Gestionnaire de base de données IndexedDB
 * Gère le stockage local pour le mode offline
 */
class DBManager {
    constructor() {
        this.db = null;
    }

    /**
     * Initialise la base de données IndexedDB
     * Crée les object stores si nécessaire
     */
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(CONFIG.DB_NAME, CONFIG.DB_VERSION);

            request.onerror = () => {
                console.error('[DB] Erreur ouverture IndexedDB:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                console.log('[DB] IndexedDB initialisé');
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Object store pour scans en attente
                if (!db.objectStoreNames.contains('pending-scans')) {
                    const store = db.createObjectStore('pending-scans', { 
                        keyPath: 'id', 
                        autoIncrement: true 
                    });
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                    store.createIndex('inventaire_id', 'inventaire_id', { unique: false });
                    console.log('[DB] Object store "pending-scans" créé');
                }

                // Object store pour cache des biens
                if (!db.objectStoreNames.contains('biens-cache')) {
                    const store = db.createObjectStore('biens-cache', { keyPath: 'id' });
                    store.createIndex('localisation_id', 'localisation_id', { unique: false });
                    store.createIndex('code_inventaire', 'code_inventaire', { unique: false });
                    console.log('[DB] Object store "biens-cache" créé');
                }

                // Object store pour historique scans
                if (!db.objectStoreNames.contains('scans-history')) {
                    const store = db.createObjectStore('scans-history', { 
                        keyPath: 'id', 
                        autoIncrement: true 
                    });
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                    store.createIndex('bien_id', 'bien_id', { unique: false });
                    console.log('[DB] Object store "scans-history" créé');
                }
            };
        });
    }

    /**
     * Ajoute un scan en attente de synchronisation
     * @param {Object} scanData - Données du scan à stocker
     * @returns {Promise<number>} ID du scan ajouté
     */
    async addPendingScan(scanData) {
        if (!this.db) {
            throw new Error('IndexedDB non initialisé');
        }

        const transaction = this.db.transaction(['pending-scans'], 'readwrite');
        const store = transaction.objectStore('pending-scans');
        
        const scan = {
            ...scanData,
            timestamp: Date.now(),
            token: AppState.token,
            inventaire_id: AppState.inventaire?.id
        };

        return new Promise((resolve, reject) => {
            const request = store.add(scan);
            request.onsuccess = () => {
                console.log('[DB] Scan en attente ajouté:', request.result);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('[DB] Erreur ajout scan:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Récupère tous les scans en attente
     * @returns {Promise<Array>} Liste des scans en attente
     */
    async getPendingScans() {
        if (!this.db) {
            return [];
        }

        const transaction = this.db.transaction(['pending-scans'], 'readonly');
        const store = transaction.objectStore('pending-scans');
        
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => resolve(request.result || []);
            request.onerror = () => {
                console.error('[DB] Erreur récupération scans:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Supprime un scan en attente après synchronisation réussie
     * @param {number} id - ID du scan à supprimer
     * @returns {Promise<void>}
     */
    async deletePendingScan(id) {
        if (!this.db) {
            return;
        }

        const transaction = this.db.transaction(['pending-scans'], 'readwrite');
        const store = transaction.objectStore('pending-scans');
        
        return new Promise((resolve, reject) => {
            const request = store.delete(id);
            request.onsuccess = () => {
                console.log('[DB] Scan supprimé:', id);
                resolve();
            };
            request.onerror = () => {
                console.error('[DB] Erreur suppression scan:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Met en cache les biens d'une localisation
     * @param {Array} biens - Liste des biens à mettre en cache
     * @returns {Promise<void>}
     */
    async cacheBiens(biens) {
        if (!this.db || !biens || biens.length === 0) {
            return;
        }

        const transaction = this.db.transaction(['biens-cache'], 'readwrite');
        const store = transaction.objectStore('biens-cache');
        
        try {
            for (const bien of biens) {
                await new Promise((resolve, reject) => {
                    const request = store.put(bien);
                    request.onsuccess = () => resolve();
                    request.onerror = () => reject(request.error);
                });
            }
            console.log(`[DB] ${biens.length} bien(s) mis en cache`);
        } catch (error) {
            console.error('[DB] Erreur cache biens:', error);
        }
    }

    /**
     * Récupère un bien depuis le cache
     * @param {number} bienId - ID du bien
     * @returns {Promise<Object|null>} Le bien ou null si non trouvé
     */
    async getCachedBien(bienId) {
        if (!this.db) {
            return null;
        }

        const transaction = this.db.transaction(['biens-cache'], 'readonly');
        const store = transaction.objectStore('biens-cache');
        
        return new Promise((resolve, reject) => {
            const request = store.get(bienId);
            request.onsuccess = () => resolve(request.result || null);
            request.onerror = () => {
                console.error('[DB] Erreur récupération bien:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Récupère un bien par son code inventaire depuis le cache
     * @param {string} codeInventaire - Code inventaire du bien
     * @returns {Promise<Object|null>} Le bien ou null si non trouvé
     */
    async getCachedBienByCode(codeInventaire) {
        if (!this.db) {
            return null;
        }

        const transaction = this.db.transaction(['biens-cache'], 'readonly');
        const store = transaction.objectStore('biens-cache');
        const index = store.index('code_inventaire');
        
        return new Promise((resolve, reject) => {
            const request = index.get(codeInventaire);
            request.onsuccess = () => resolve(request.result || null);
            request.onerror = () => {
                console.error('[DB] Erreur récupération bien par code:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Ajoute un scan à l'historique local
     * @param {Object} scanData - Données du scan
     * @returns {Promise<number>} ID du scan ajouté
     */
    async addToHistory(scanData) {
        if (!this.db) {
            return;
        }

        const transaction = this.db.transaction(['scans-history'], 'readwrite');
        const store = transaction.objectStore('scans-history');
        
        const scan = {
            ...scanData,
            timestamp: Date.now()
        };

        return new Promise((resolve, reject) => {
            const request = store.add(scan);
            request.onsuccess = () => {
                console.log('[DB] Scan ajouté à l\'historique:', request.result);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('[DB] Erreur ajout historique:', request.error);
                reject(request.error);
            };
        });
    }

    /**
     * Récupère l'historique des scans (les plus récents en premier)
     * @param {number} limit - Nombre maximum de scans à récupérer
     * @returns {Promise<Array>} Liste des scans
     */
    async getHistory(limit = 20) {
        if (!this.db) {
            return [];
        }

        const transaction = this.db.transaction(['scans-history'], 'readonly');
        const store = transaction.objectStore('scans-history');
        const index = store.index('timestamp');
        
        return new Promise((resolve, reject) => {
            const request = index.openCursor(null, 'prev');
            const results = [];
            
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor && results.length < limit) {
                    results.push(cursor.value);
                    cursor.continue();
                } else {
                    resolve(results);
                }
            };
            request.onerror = () => {
                console.error('[DB] Erreur récupération historique:', request.error);
                reject(request.error);
            };
        });
    }
}

// Instance globale du gestionnaire de base de données
const dbManager = new DBManager();

// ============================================
// API CALLS
// ============================================

/**
 * Classe pour gérer les appels API
 * Gère l'authentification, les erreurs et le mode offline
 */
class API {
    /**
     * Effectue une requête API avec gestion d'erreurs et authentification
     * @param {string} endpoint - Endpoint de l'API
     * @param {Object} options - Options de la requête (method, body, headers, etc.)
     * @returns {Promise<Object>} Réponse JSON de l'API
     */
    static async request(endpoint, options = {}) {
        const url = `${CONFIG.API_BASE_URL}${endpoint}`;
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        // Ajouter le token d'authentification si disponible
        if (AppState.token) {
            defaultOptions.headers['Authorization'] = `Bearer ${AppState.token}`;
        }

        // Ajouter le CSRF token si disponible
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        const finalOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...(options.headers || {})
            }
        };

        try {
            const response = await fetch(url, finalOptions);
            
            if (!response.ok) {
                // Gestion des erreurs HTTP
                if (response.status === 401) {
                    // Token invalide ou expiré, déconnecter l'utilisateur
                    console.warn('[API] Token invalide, déconnexion...');
                    AuthManager.logout();
                    throw new Error('Session expirée. Veuillez vous reconnecter.');
                }
                
                // Essayer de récupérer le message d'erreur depuis la réponse
                let errorMessage = `Erreur HTTP ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorData.error || errorMessage;
                } catch (e) {
                    // Si la réponse n'est pas du JSON, utiliser le status text
                    errorMessage = response.statusText || errorMessage;
                }
                
                throw new Error(errorMessage);
            }

            // Parser la réponse JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                // Si ce n'est pas du JSON, retourner le texte
                return await response.text();
            }
        } catch (error) {
            // Si offline et requête GET, ne pas lever d'erreur (utiliser le cache)
            if (!navigator.onLine && options.method !== 'POST' && options.method !== 'PUT' && options.method !== 'DELETE') {
                console.warn('[API] Mode offline, utilisation du cache');
                return null;
            }
            
            // Propager l'erreur pour gestion par l'appelant
            throw error;
        }
    }

    /**
     * Connexion de l'utilisateur
     * @param {string} email - Email de l'utilisateur
     * @param {string} password - Mot de passe
     * @returns {Promise<Object>} Réponse avec token et user
     */
    static async login(email, password) {
        return this.request('/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
    }

    /**
     * Déconnexion de l'utilisateur
     * @returns {Promise<void>}
     */
    static async logout() {
        try {
            await this.request('/logout', {
                method: 'POST'
            });
        } catch (error) {
            // Ignorer les erreurs de déconnexion (peut être offline)
            console.warn('[API] Erreur logout (ignorée):', error);
        }
    }

    /**
     * Récupère l'inventaire en cours de l'utilisateur
     * @returns {Promise<Object>} Données de l'inventaire
     */
    static async getCurrentInventaire() {
        return this.request('/inventaires/current');
    }

    /**
     * Récupère les localisations assignées à l'utilisateur pour un inventaire
     * @param {number} inventaireId - ID de l'inventaire
     * @returns {Promise<Array>} Liste des localisations
     */
    static async getMesLocalisations(inventaireId) {
        return this.request(`/inventaires/${inventaireId}/mes-localisations`);
    }

    /**
     * Récupère une localisation par son code
     * @param {string} code - Code de la localisation
     * @returns {Promise<Object>} Données de la localisation
     */
    static async getLocalisationByCode(code) {
        return this.request(`/localisations/by-code/${encodeURIComponent(code)}`);
    }

    /**
     * Récupère les biens d'une localisation
     * @param {number} localisationId - ID de la localisation
     * @returns {Promise<Array>} Liste des biens
     */
    static async getBiensLocalisation(localisationId) {
        return this.request(`/localisations/${localisationId}/biens`);
    }

    /**
     * Récupère les détails d'un bien
     * @param {number} bienId - ID du bien
     * @returns {Promise<Object>} Données du bien
     */
    static async getBien(bienId) {
        return this.request(`/biens/${bienId}`);
    }

    /**
     * Démarre le scan d'une localisation
     * @param {number} inventaireId - ID de l'inventaire
     * @param {number} localisationId - ID de la localisation
     * @returns {Promise<Object>} Données de l'inventaire localisation créée
     */
    static async demarrerLocalisation(inventaireId, localisationId) {
        return this.request(`/inventaires/${inventaireId}/demarrer-localisation`, {
            method: 'POST',
            body: JSON.stringify({ 
                localisation_id: localisationId,
                user_id: AppState.user.id
            })
        });
    }

    /**
     * Termine le scan d'une localisation
     * @param {number} inventaireId - ID de l'inventaire
     * @param {number} inventaireLocalisationId - ID de l'inventaire localisation
     * @returns {Promise<Object>} Réponse de confirmation
     */
    static async terminerLocalisation(inventaireId, inventaireLocalisationId) {
        return this.request(`/inventaires/${inventaireId}/terminer-localisation`, {
            method: 'POST',
            body: JSON.stringify({ 
                inventaire_localisation_id: inventaireLocalisationId
            })
        });
    }

    /**
     * Enregistre un scan de bien
     * @param {number} inventaireId - ID de l'inventaire
     * @param {Object} scanData - Données du scan
     * @returns {Promise<Object>} Réponse de confirmation
     */
    static async enregistrerScan(inventaireId, scanData) {
        return this.request(`/inventaires/${inventaireId}/scan`, {
            method: 'POST',
            body: JSON.stringify(scanData)
        });
    }

    /**
     * Récupère les statistiques d'un inventaire
     * @param {number} inventaireId - ID de l'inventaire
     * @returns {Promise<Object>} Statistiques de l'inventaire
     */
    static async getStats(inventaireId) {
        return this.request(`/inventaires/${inventaireId}/stats`);
    }
}

// ============================================
// AUTHENTICATION MANAGER
// ============================================

/**
 * Gestionnaire d'authentification
 * Gère la connexion, déconnexion et la persistance de session
 */
class AuthManager {
    /**
     * Initialise l'authentification en chargeant les données depuis localStorage
     * @returns {boolean} True si une session valide existe
     */
    static init() {
        // Charger token et user depuis localStorage
        const token = localStorage.getItem(CONFIG.STORAGE_KEY_TOKEN);
        const userJson = localStorage.getItem(CONFIG.STORAGE_KEY_USER);

        if (token && userJson) {
            try {
                AppState.token = token;
                AppState.user = JSON.parse(userJson);
                console.log('[Auth] Session restaurée pour:', AppState.user.email);
                return true;
            } catch (error) {
                console.error('[Auth] Erreur parsing user data:', error);
                this.logout();
                return false;
            }
        }

        return false;
    }

    /**
     * Connecte l'utilisateur
     * @param {string} email - Email de l'utilisateur
     * @param {string} password - Mot de passe
     * @returns {Promise<Object>} Résultat de la connexion {success: boolean, error?: string}
     */
    static async login(email, password) {
        try {
            const response = await API.login(email, password);
            
            if (response.token && response.user) {
                // Mettre à jour le state
                AppState.token = response.token;
                AppState.user = response.user;

                // Sauvegarder dans localStorage
                localStorage.setItem(CONFIG.STORAGE_KEY_TOKEN, response.token);
                localStorage.setItem(CONFIG.STORAGE_KEY_USER, JSON.stringify(response.user));

                console.log('[Auth] Connexion réussie:', response.user.email);
                return { success: true };
            } else {
                throw new Error('Réponse invalide du serveur');
            }
        } catch (error) {
            console.error('[Auth] Erreur login:', error);
            return { 
                success: false, 
                error: error.message || 'Erreur de connexion' 
            };
        }
    }

    /**
     * Déconnecte l'utilisateur et nettoie toutes les données
     */
    static logout() {
        console.log('[Auth] Déconnexion...');

        // Nettoyer le state
        AppState.token = null;
        AppState.user = null;
        AppState.inventaire = null;
        AppState.activeLocation = null;
        AppState.localisation = null;
        AppState.biensAttendus = [];
        AppState.scansSession = [];

        // Nettoyer localStorage
        localStorage.removeItem(CONFIG.STORAGE_KEY_TOKEN);
        localStorage.removeItem(CONFIG.STORAGE_KEY_USER);
        localStorage.removeItem(CONFIG.STORAGE_KEY_INVENTAIRE);
        localStorage.removeItem(CONFIG.STORAGE_KEY_LOCATION);

        // Appeler l'API de déconnexion (sans attendre)
        API.logout().catch(() => {
            // Ignorer les erreurs (peut être offline)
        });

        // Rediriger vers login
        showView('login');
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     * @returns {boolean} True si authentifié
     */
    static isAuthenticated() {
        return AppState.token !== null && AppState.user !== null;
    }
}

// ============================================
// SYNC MANAGER (pour mode offline)
// ============================================

/**
 * Gestionnaire de synchronisation
 * Synchronise les scans en attente lorsque la connexion revient
 */
class SyncManager {
    /**
     * Synchronise tous les scans en attente avec l'API
     * @returns {Promise<void>}
     */
    static async syncPendingScans() {
        if (!navigator.onLine) {
            console.log('[Sync] Offline, sync impossible');
            return;
        }

        if (!AuthManager.isAuthenticated()) {
            console.log('[Sync] Non authentifié, sync impossible');
            return;
        }

        try {
            const pendingScans = await dbManager.getPendingScans();
            
            if (pendingScans.length === 0) {
                console.log('[Sync] Aucun scan en attente');
                await this.updatePendingScansBadge();
                return;
            }

            console.log(`[Sync] Synchronisation de ${pendingScans.length} scan(s) en attente...`);

            let successCount = 0;
            let errorCount = 0;

            for (const scan of pendingScans) {
                try {
                    // Restaurer le token pour cette requête (au cas où il aurait changé)
                    const savedToken = AppState.token;
                    if (scan.token) {
                        AppState.token = scan.token;
                    }

                    await API.enregistrerScan(scan.inventaire_id, scan.data);
                    await dbManager.deletePendingScan(scan.id);
                    
                    // Restaurer le token actuel
                    AppState.token = savedToken;
                    
                    successCount++;
                    console.log('[Sync] Scan synchronisé:', scan.id);
  } catch (error) {
                    errorCount++;
                    console.error('[Sync] Erreur sync scan:', error);
                    // On continue avec les autres scans même en cas d'erreur
                }
            }

            await this.updatePendingScansBadge();

            if (successCount > 0) {
                showToast(`${successCount} scan(s) synchronisé(s) avec succès`, 'success');
            }
            if (errorCount > 0) {
                showToast(`${errorCount} scan(s) n'ont pas pu être synchronisé(s)`, 'warning');
            }
        } catch (error) {
            console.error('[Sync] Erreur synchronisation générale:', error);
            showToast('Erreur lors de la synchronisation', 'error');
        }
    }

    /**
     * Met à jour le badge de scans en attente dans l'interface
     * @returns {Promise<void>}
     */
    static async updatePendingScansBadge() {
        try {
            const pendingScans = await dbManager.getPendingScans();
            AppState.pendingScansCount = pendingScans.length;

            const badge = document.getElementById('pending-scans-badge');
            const statusText = document.getElementById('sync-status');

            if (badge && statusText) {
                if (pendingScans.length > 0) {
                    badge.textContent = pendingScans.length;
                    badge.classList.remove('hidden');
                    if (navigator.onLine) {
                        statusText.textContent = `${pendingScans.length} scan(s) en attente`;
                    } else {
                        statusText.textContent = `${pendingScans.length} scan(s) hors ligne`;
                    }
                } else {
                    badge.classList.add('hidden');
                    if (navigator.onLine) {
                        statusText.textContent = 'Synchronisé';
                    } else {
                        statusText.textContent = 'Mode hors ligne';
                    }
                }
            }

            // Mettre à jour l'indicateur de connexion dans le header
            updateOnlineStatusIndicator();
        } catch (error) {
            console.error('[Sync] Erreur mise à jour badge:', error);
        }
    }
}

// ============================================
// UTILITIES
// ============================================

/**
 * Affiche une vue spécifique en masquant les autres
 * @param {string} viewName - Nom de la vue à afficher (sans le préfixe "view-")
 */
function showView(viewName) {
    // Cacher toutes les vues
    document.querySelectorAll('[id^="view-"]').forEach(view => {
        view.classList.add('hidden');
    });

    // Afficher la vue demandée
    const view = document.getElementById(`view-${viewName}`);
    if (view) {
        view.classList.remove('hidden');
    } else {
        console.warn(`[UI] Vue "${viewName}" introuvable`);
    }
}

/**
 * Affiche une notification toast
 * @param {string} message - Message à afficher
 * @param {string} type - Type de toast (success, error, warning, info)
 */
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    
    if (!container) {
        console.warn('[UI] Container toast introuvable');
        return;
    }

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-orange-500',
        info: 'bg-blue-500'
    };

    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };

    const toast = document.createElement('div');
    toast.className = `${colors[type] || colors.info} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 translate-x-0 opacity-100`;
    toast.innerHTML = `
        <span class="text-xl">${icons[type] || icons.info}</span>
        <span class="flex-1">${message}</span>
        <button class="ml-4 hover:bg-white hover:bg-opacity-20 rounded p-1" aria-label="Fermer">✕</button>
    `;

    // Fermeture au clic sur le bouton
    toast.querySelector('button').addEventListener('click', () => {
        closeToast(toast);
    });

    container.appendChild(toast);

    // Auto-dismiss après 5 secondes
    setTimeout(() => {
        if (toast.parentElement) {
            closeToast(toast);
        }
    }, 5000);

    // Jouer son si disponible
    playSound(type);
}

/**
 * Ferme une notification toast avec animation
 * @param {HTMLElement} toast - Élément toast à fermer
 */
function closeToast(toast) {
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 300);
}

/**
 * Joue un son de feedback
 * @param {string} type - Type de son (success, error, warning, info)
 */
function playSound(type) {
    try {
        // Créer un contexte audio simple pour générer un beep
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        // Fréquences selon le type
        const frequencies = {
            success: 800,
            error: 400,
            warning: 600,
            info: 500
        };

        oscillator.frequency.value = frequencies[type] || 500;
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (error) {
        // Ignore les erreurs de son (peut ne pas être supporté)
        console.debug('[Sound] Audio non disponible:', error);
    }
}

/**
 * Formate une date en texte relatif (il y a X min, il y a Xh, etc.)
 * @param {Date|string|number} date - Date à formater
 * @returns {string} Date formatée
 */
function formatDate(date) {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'À l\'instant';
    if (minutes < 60) return `Il y a ${minutes} min`;
    if (hours < 24) return `Il y a ${hours}h`;
    if (days < 7) return `Il y a ${days}j`;
    
    return d.toLocaleDateString('fr-FR', { 
        day: '2-digit', 
        month: 'short', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// ============================================
// SCANNER MANAGER
// ============================================

/**
 * Gestionnaire du scanner QR code
 * Gère l'initialisation, le démarrage, l'arrêt et le traitement des scans
 */
class ScannerManager {
    constructor() {
        this.html5QrCode = null;
        this.isScanning = false;
        this.currentMode = 'localisation'; // 'localisation' ou 'bien'
    }

    /**
     * Initialise le scanner HTML5 QR Code
     * @returns {Promise<void>}
     */
    async init() {
        try {
            if (typeof Html5Qrcode === 'undefined') {
                throw new Error('Bibliothèque Html5Qrcode non chargée');
            }
            this.html5QrCode = new Html5Qrcode("qr-reader");
            console.log('[Scanner] Scanner initialisé');
        } catch (error) {
            console.error('[Scanner] Erreur initialisation:', error);
            throw error;
        }
    }

    /**
     * Démarre le scanner avec la caméra
     * @returns {Promise<void>}
     */
    async start() {
        if (this.isScanning) {
            console.log('[Scanner] Scanner déjà actif');
            return;
        }

        if (!this.html5QrCode) {
            await this.init();
        }

        try {
            // Configuration du scanner
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            // Démarrer le scanner avec la caméra arrière
            await this.html5QrCode.start(
                { facingMode: "environment" }, // Caméra arrière
                config,
                this.onScanSuccess.bind(this),
                this.onScanError.bind(this)
            );

            this.isScanning = true;
            this.updateScannerUI('scanning');
            
            console.log('[Scanner] Scanner démarré');
        } catch (error) {
            console.error('[Scanner] Erreur démarrage scanner:', error);
            showToast('Impossible d\'accéder à la caméra', 'error');
            this.updateScannerUI('error');
        }
    }

    /**
     * Arrête le scanner
     * @returns {Promise<void>}
     */
    async stop() {
        if (!this.isScanning || !this.html5QrCode) {
            return;
        }

        try {
            await this.html5QrCode.stop();
            this.isScanning = false;
            this.updateScannerUI('stopped');
            console.log('[Scanner] Scanner arrêté');
        } catch (error) {
            console.error('[Scanner] Erreur arrêt scanner:', error);
        }
    }

    /**
     * Callback appelé lors d'un scan réussi
     * @param {string} decodedText - Texte décodé du QR code
     * @param {Object} decodedResult - Résultat complet du décodage
     */
    async onScanSuccess(decodedText, decodedResult) {
        console.log('[Scanner] QR code scanné:', decodedText);

            // Vibration si disponible
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }

        // Son de feedback (optionnel)
        playSound('success');

        // Parser le QR code (format JSON attendu)
        let qrData;
        try {
            qrData = JSON.parse(decodedText);
        } catch (error) {
            // Si pas JSON, considérer comme code simple
            qrData = { type: 'unknown', code: decodedText };
        }

        // Arrêter temporairement le scanner
        await this.stop();

        // Traiter selon le type
        if (qrData.type === 'localisation' || this.currentMode === 'localisation') {
            await this.handleLocalisationScan(qrData);
        } else if (qrData.type === 'bien' || this.currentMode === 'bien') {
            await this.handleBienScan(qrData);
        } else {
            showToast('QR code non reconnu', 'error');
            setTimeout(() => this.start(), 2000);
        }
    }

    /**
     * Callback appelé lors d'une erreur de scan
     * @param {string} errorMessage - Message d'erreur
     */
    onScanError(errorMessage) {
        // Ignorer les erreurs de scan normales (pas de QR détecté)
        // Ces erreurs sont fréquentes et normales pendant le scan
        // console.log('[Scanner] Scan error:', errorMessage);
    }

    /**
     * Traite le scan d'une localisation
     * @param {Object} qrData - Données du QR code
     */
    async handleLocalisationScan(qrData) {
        try {
            showToast('Localisation détectée, vérification...', 'info');

            // Récupérer les infos de la localisation
            const localisation = await API.getLocalisationByCode(qrData.code);

            if (!localisation) {
                showToast('Localisation non trouvée', 'error');
                setTimeout(() => this.start(), 2000);
                return;
            }

            // Vérifier que cette localisation est assignée à l'agent
            const mesLocalisations = await API.getMesLocalisations(AppState.inventaire.id);
            const isAssigned = mesLocalisations.some(loc => loc.localisation_id === localisation.id);

            if (!isAssigned) {
                showToast('Cette localisation ne vous est pas assignée', 'error');
                setTimeout(() => this.start(), 2000);
                return;
            }

            // Démarrer le scan de cette localisation
            const inventaireLocalisation = await API.demarrerLocalisation(
                AppState.inventaire.id, 
                localisation.id
            );

            // Mettre à jour le state
            AppState.activeLocation = inventaireLocalisation;
            AppState.localisation = localisation;
            AppState.scansSession = []; // Réinitialiser les scans de la session

            // Charger les biens attendus
            const biens = await API.getBiensLocalisation(localisation.id);
            AppState.biensAttendus = biens;

            // Mettre en cache
            await dbManager.cacheBiens(biens);

            // Sauvegarder dans localStorage
            localStorage.setItem(CONFIG.STORAGE_KEY_LOCATION, JSON.stringify({
                activeLocation: inventaireLocalisation,
                localisation: localisation,
                biens: biens
            }));

            // Changer le mode en 'bien'
            this.currentMode = 'bien';

            // Mettre à jour l'UI
            updateActiveLocationUI();
            showToast(`Bureau activé : ${localisation.code}`, 'success');

            // Redémarrer le scanner pour les biens
            setTimeout(() => this.start(), 1500);

        } catch (error) {
            console.error('[Scanner] Erreur scan localisation:', error);
            showToast(error.message || 'Erreur lors du scan', 'error');
            setTimeout(() => this.start(), 2000);
        }
    }

    /**
     * Traite le scan d'un bien
     * @param {Object} qrData - Données du QR code
     */
    async handleBienScan(qrData) {
        try {
            // Vérifier qu'un bureau est actif
            if (!AppState.activeLocation) {
                showToast('Scannez d\'abord un bureau', 'warning');
                this.currentMode = 'localisation';
                setTimeout(() => this.start(), 2000);
                return;
            }

            showToast('Bien détecté, chargement...', 'info');

            // Récupérer les infos du bien
            let bien = await API.getBien(qrData.id);

            // Si offline, essayer depuis le cache
            if (!bien && !navigator.onLine) {
                bien = await dbManager.getCachedBien(qrData.id);
            }

            if (!bien) {
                showToast('Bien non trouvé', 'error');
                setTimeout(() => this.start(), 2000);
                return;
            }

            // Vérifier si déjà scanné dans cette session
            const dejaScan = AppState.scansSession.some(s => s.bien_id === bien.id);
            if (dejaScan) {
                showToast('Ce bien a déjà été scanné dans ce bureau', 'warning');
                playSound('warning');
                if (navigator.vibrate) {
                    navigator.vibrate([100, 50, 100, 50, 100]);
                }
                setTimeout(() => this.start(), 2000);
                return;
            }

            // Afficher la fiche du bien et les boutons d'action
            displayBienResult(bien);

        } catch (error) {
            console.error('[Scanner] Erreur scan bien:', error);
            showToast(error.message || 'Erreur lors du scan', 'error');
            setTimeout(() => this.start(), 2000);
        }
    }

    /**
     * Met à jour l'interface du scanner selon son état
     * @param {string} status - État du scanner ('scanning', 'stopped', 'error')
     */
    updateScannerUI(status) {
        const statusDiv = document.getElementById('scanner-status');
        const messageDiv = document.getElementById('scanner-message');

        if (!statusDiv || !messageDiv) {
            return;
        }

        switch (status) {
            case 'scanning':
                statusDiv.innerHTML = '<p class="text-green-600 font-semibold">📷 Caméra active</p>';
                messageDiv.textContent = this.currentMode === 'localisation' 
                    ? 'Pointez vers le QR code d\'un bureau' 
                    : 'Pointez vers le QR code d\'un bien';
                break;

            case 'stopped':
                statusDiv.innerHTML = `
                    <button id="start-scanner-btn" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        📷 Activer la caméra
                    </button>
                `;
                messageDiv.textContent = 'Prêt à scanner';
                // Re-attacher l'event listener
                const startBtn = document.getElementById('start-scanner-btn');
                if (startBtn) {
                    startBtn.addEventListener('click', () => this.start());
                }
                break;

            case 'error':
                statusDiv.innerHTML = `
                    <button id="start-scanner-btn" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                        🔄 Réessayer
                    </button>
                `;
                messageDiv.textContent = 'Erreur caméra';
                const retryBtn = document.getElementById('start-scanner-btn');
                if (retryBtn) {
                    retryBtn.addEventListener('click', () => this.start());
                }
                break;
        }
    }
}

// Instance globale du gestionnaire de scanner
const scannerManager = new ScannerManager();

// ============================================
// UI UPDATES
// ============================================

/**
 * Met à jour l'interface de la localisation active
 */
function updateActiveLocationUI() {
    const stateNoLocation = document.getElementById('state-no-location');
    const stateActiveLocation = document.getElementById('state-active-location');
    const progressBar = document.getElementById('progress-bar');

    if (!AppState.activeLocation || !AppState.localisation) {
        // Aucun bureau actif
        if (stateNoLocation) stateNoLocation.classList.remove('hidden');
        if (stateActiveLocation) stateActiveLocation.classList.add('hidden');
        if (progressBar) progressBar.classList.add('hidden');
        return;
    }

    // Bureau actif
    if (stateNoLocation) stateNoLocation.classList.add('hidden');
    if (stateActiveLocation) stateActiveLocation.classList.remove('hidden');
    if (progressBar) progressBar.classList.remove('hidden');

    // Infos localisation
    const locationName = document.getElementById('active-location-name');
    const locationDetails = document.getElementById('active-location-details');
    const locationProgress = document.getElementById('active-location-progress');
    const progressText = document.getElementById('progress-text');
    const currentLocationName = document.getElementById('current-location-name');
    const progressFill = document.getElementById('progress-fill');

    if (locationName) {
        locationName.textContent = `${AppState.localisation.code} - ${AppState.localisation.designation}`;
    }
    
    if (locationDetails) {
        locationDetails.textContent = 
            `${AppState.localisation.batiment || ''} ${AppState.localisation.etage ? 'Étage ' + AppState.localisation.etage : ''}`.trim();
    }

    // Progression
    const scanned = AppState.activeLocation.nombre_biens_scannes || 0;
    const total = AppState.activeLocation.nombre_biens_attendus || 0;
    const percentage = total > 0 ? Math.round((scanned / total) * 100) : 0;

    if (locationProgress) {
        locationProgress.textContent = `✓ ${scanned}/${total} biens scannés`;
    }
    if (progressText) {
        progressText.textContent = `${scanned}/${total}`;
    }
    if (currentLocationName) {
        currentLocationName.textContent = AppState.localisation.code;
    }
    if (progressFill) {
        progressFill.style.width = `${percentage}%`;
    }
}

/**
 * Affiche le résultat du scan d'un bien avec les actions possibles
 * @param {Object} bien - Données du bien scanné
 */
function displayBienResult(bien) {
    const resultDiv = document.getElementById('scan-result');
    
    if (!resultDiv) {
        console.warn('[UI] Élément scan-result introuvable');
        return;
    }

    // Vérifier si le bien est dans la bonne localisation
    const isCorrectLocation = bien.localisation_id === AppState.localisation.id;
    const statusClass = isCorrectLocation ? 'bg-green-50 border-green-200' : 'bg-orange-50 border-orange-200';
    const statusIcon = isCorrectLocation ? '✓' : '⚠️';
    const statusText = isCorrectLocation ? 'Localisation conforme' : 'Bien déplacé !';
    const statusColor = isCorrectLocation ? 'text-green-700' : 'text-orange-700';

    resultDiv.innerHTML = `
        <div class="space-y-4">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-2xl">${statusIcon}</span>
                        <span class="font-semibold ${statusColor}">${statusText}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">${bien.designation || 'N/A'}</h3>
                    <p class="text-gray-600 text-sm">${bien.code_inventaire || 'N/A'}</p>
                </div>
            </div>

            <!-- Infos bien -->
            <div class="${statusClass} border rounded-lg p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Nature</span>
                    <span class="font-medium">${bien.nature || 'N/A'}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Localisation prévue</span>
                    <span class="font-medium">${bien.localisation?.code || '-'}</span>
                </div>
                ${!isCorrectLocation ? `
                <div class="flex items-center justify-between border-t pt-2 mt-2">
                    <span class="text-sm text-gray-600">Localisation actuelle</span>
                    <span class="font-medium text-orange-700">${AppState.localisation.code}</span>
                </div>
                ` : ''}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Service</span>
                    <span class="font-medium">${bien.service_usager || '-'}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Valeur</span>
                    <span class="font-medium">${(bien.valeur_acquisition || 0).toLocaleString('fr-FR')} MRU</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <button onclick="enregistrerScan('${bien.id}', 'present')" 
                        class="action-button-large btn-success">
                    <span>✓</span>
                    <span>PRÉSENT</span>
                </button>

                ${!isCorrectLocation ? `
                <button onclick="enregistrerScan('${bien.id}', 'deplace')" 
                        class="action-button-large btn-warning">
                    <span>⚠️</span>
                    <span>CONFIRMER DÉPLACEMENT</span>
                </button>
                ` : `
                <button onclick="enregistrerScan('${bien.id}', 'deplace')" 
                        class="action-button-large btn-warning">
                    <span>⚠️</span>
                    <span>DÉPLACÉ</span>
                </button>
                `}

                <button onclick="enregistrerScan('${bien.id}', 'absent')" 
                        class="action-button-large btn-danger">
                    <span>✗</span>
                    <span>ABSENT</span>
                </button>

                <button onclick="annulerScan()" 
                        class="w-full bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                    Annuler
                </button>
            </div>
        </div>
    `;

    resultDiv.classList.remove('hidden');
}

/**
 * Enregistre un scan de bien avec le statut spécifié
 * @param {string|number} bienId - ID du bien
 * @param {string} statut - Statut du scan ('present', 'deplace', 'absent', 'deteriore')
 */
async function enregistrerScan(bienId, statut) {
    try {
        // Préparer les données du scan
        const scanData = {
            inventaire_id: AppState.inventaire.id,
            inventaire_localisation_id: AppState.activeLocation.id,
            bien_id: parseInt(bienId),
            statut_scan: statut,
            localisation_reelle_id: AppState.localisation.id,
            etat_constate: 'bon', // TODO: demander à l'utilisateur
            commentaire: null,
            photo_path: null,
            user_id: AppState.user.id
        };

        let scanResult;

        if (navigator.onLine) {
            try {
                // Mode online : envoyer directement à l'API
                scanResult = await API.enregistrerScan(AppState.inventaire.id, scanData);
                showToast('Scan enregistré', 'success');
                
                // Vibration de succès
                if (navigator.vibrate) {
                    navigator.vibrate(100);
                }
            } catch (error) {
                // Si erreur API, basculer en mode offline
                console.warn('[Scan] Erreur API, basculement en mode offline:', error);
                await dbManager.addPendingScan({
                    inventaire_id: AppState.inventaire.id,
                    data: scanData
                });
                showToast('Scan enregistré (hors ligne)', 'warning');
            }
        } else {
            // Mode offline : ajouter à la file d'attente
            await dbManager.addPendingScan({
                inventaire_id: AppState.inventaire.id,
                data: scanData
            });
            showToast('Scan enregistré (hors ligne)', 'warning');
            
            // Vibration différente pour offline
            if (navigator.vibrate) {
                navigator.vibrate([50, 50, 50]);
            }
        }

        // Mettre à jour le state local
        if (AppState.activeLocation) {
            AppState.activeLocation.nombre_biens_scannes = (AppState.activeLocation.nombre_biens_scannes || 0) + 1;
        }
        AppState.scansSession.push(scanData);

        // Ajouter à l'historique local
        const bien = await dbManager.getCachedBien(bienId);
        await dbManager.addToHistory({
            ...scanData,
            bien: bien
        });

        // Mettre à jour l'UI
        updateActiveLocationUI();
        updateHistoryUI();
        await SyncManager.updatePendingScansBadge();

        // Cacher le résultat et redémarrer le scanner
        const resultDiv = document.getElementById('scan-result');
        if (resultDiv) {
            resultDiv.classList.add('hidden');
        }
        setTimeout(() => scannerManager.start(), 500);

    } catch (error) {
        console.error('[Scan] Erreur enregistrement scan:', error);
        showToast(error.message || 'Erreur lors de l\'enregistrement', 'error');
    }
}

/**
 * Annule le scan en cours et redémarre le scanner
 */
function annulerScan() {
    const resultDiv = document.getElementById('scan-result');
    if (resultDiv) {
        resultDiv.classList.add('hidden');
    }
    scannerManager.start();
}

/**
 * Affiche la modal de confirmation pour terminer un bureau
 */
function showTerminerBureauModal() {
    if (!AppState.activeLocation || !AppState.localisation) {
        return;
    }

    // Calculer les statistiques
    const scanned = AppState.activeLocation.nombre_biens_scannes || 0;
    const total = AppState.activeLocation.nombre_biens_attendus || 0;
    const nonScannes = total - scanned;

    // Compter les statuts des scans
    const stats = {
        present: AppState.scansSession.filter(s => s.statut_scan === 'present').length,
        deplace: AppState.scansSession.filter(s => s.statut_scan === 'deplace').length,
        absent: AppState.scansSession.filter(s => s.statut_scan === 'absent').length
    };

    // Créer la modal
    const modalHTML = `
        <div id="modal-terminer-bureau" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-xl font-bold text-gray-800">Terminer le bureau</h3>
                    <button onclick="closeTerminerBureauModal()" class="modal-close" aria-label="Fermer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="text-gray-700 mb-2">
                            Vous êtes sur le point de terminer le scan de :
                        </p>
                        <p class="text-lg font-semibold text-indigo-600">
                            ${AppState.localisation.code} - ${AppState.localisation.designation}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Récapitulatif des scans</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">✓ Présents</span>
                                <span class="font-semibold text-green-600">${stats.present}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">⚠️ Déplacés</span>
                                <span class="font-semibold text-orange-600">${stats.deplace}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">✗ Absents</span>
                                <span class="font-semibold text-red-600">${stats.absent}</span>
                            </div>
                            <div class="border-t border-gray-300 pt-2 mt-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">Total scannés</span>
                                    <span class="font-bold text-gray-800">${scanned}/${total}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${nonScannes > 0 ? `
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-orange-800 mb-1">
                                    ${nonScannes} bien(s) non scanné(s)
                                </p>
                                <p class="text-sm text-orange-700">
                                    Certains biens attendus dans ce bureau n'ont pas été scannés.
                                </p>
                            </div>
                        </div>
                    </div>
                    ` : ''}

                    <div class="mb-4">
                        <label class="flex items-start space-x-2 cursor-pointer">
                            <input type="checkbox" id="mark-non-scanned-absent" class="mt-1">
                            <span class="text-sm text-gray-700">
                                Marquer les biens non scannés comme absents
                            </span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button onclick="closeTerminerBureauModal()" 
                            class="btn-secondary">
                        Annuler
                    </button>
                    <button onclick="confirmTerminerBureau()" 
                            class="btn-primary">
                        Terminer le bureau
                    </button>
                </div>
            </div>
        </div>
    `;

    // Ajouter la modal au body
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer.firstElementChild);

    // Animation d'entrée
    setTimeout(() => {
        const modal = document.getElementById('modal-terminer-bureau');
        if (modal) {
            modal.classList.add('modal-show');
        }
    }, 10);
}

/**
 * Ferme la modal de terminer bureau
 */
function closeTerminerBureauModal() {
    const modal = document.getElementById('modal-terminer-bureau');
    if (modal) {
        modal.classList.remove('modal-show');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

/**
 * Confirme et exécute la terminaison du bureau
 */
async function confirmTerminerBureau() {
    if (!AppState.activeLocation) {
        return;
    }

    const markNonScannedAbsent = document.getElementById('mark-non-scanned-absent')?.checked || false;
    const scanned = AppState.activeLocation.nombre_biens_scannes || 0;
    const total = AppState.activeLocation.nombre_biens_attendus || 0;
    const nonScannes = total - scanned;

    try {
        // Désactiver le bouton pendant le traitement
        const confirmBtn = document.querySelector('#modal-terminer-bureau .btn-primary');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Traitement...';
        }

        // Si demandé, marquer les non-scannés comme absents
        if (markNonScannedAbsent && nonScannes > 0) {
            const biensNonScannes = AppState.biensAttendus.filter(bien => {
                return !AppState.scansSession.some(scan => scan.bien_id === bien.id);
            });

            for (const bien of biensNonScannes) {
                const scanData = {
                    inventaire_id: AppState.inventaire.id,
                    inventaire_localisation_id: AppState.activeLocation.id,
                    bien_id: bien.id,
                    statut_scan: 'absent',
                    localisation_reelle_id: AppState.localisation.id,
                    etat_constate: 'bon',
                    commentaire: 'Marqué absent automatiquement lors de la fermeture du bureau',
                    user_id: AppState.user.id
                };

                if (navigator.onLine) {
                    try {
                        await API.enregistrerScan(AppState.inventaire.id, scanData);
                    } catch (error) {
                        console.error('[Scan] Erreur marquage absent:', error);
                        // Continuer même en cas d'erreur
                    }
                } else {
                    await dbManager.addPendingScan({
                        inventaire_id: AppState.inventaire.id,
                        data: scanData
                    });
                }
            }
        }

        // Terminer la localisation
        await API.terminerLocalisation(
            AppState.inventaire.id,
            AppState.activeLocation.id
        );

        // Fermer la modal
        closeTerminerBureauModal();

        // Vibration de succès
        if (navigator.vibrate) {
            navigator.vibrate([100, 50, 100]);
        }

        showToast(`✓ ${AppState.localisation.code} terminé ! Passez au bureau suivant`, 'success');

        // Réinitialiser le state
        AppState.activeLocation = null;
        AppState.localisation = null;
        AppState.biensAttendus = [];
        AppState.scansSession = [];
        localStorage.removeItem(CONFIG.STORAGE_KEY_LOCATION);

        // Changer le mode
        scannerManager.currentMode = 'localisation';

        // Mettre à jour l'UI
        updateActiveLocationUI();
        updateHistoryUI();
        await SyncManager.updatePendingScansBadge();

        // Redémarrer le scanner
        await scannerManager.stop();
        setTimeout(() => scannerManager.start(), 1000);

    } catch (error) {
        console.error('[Scan] Erreur terminer bureau:', error);
        showToast(error.message || 'Erreur lors de la fermeture', 'error');
        
        // Réactiver le bouton
        const confirmBtn = document.querySelector('#modal-terminer-bureau .btn-primary');
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Terminer le bureau';
        }
    }
}

/**
 * Termine le scan de la localisation active (fonction legacy pour compatibilité)
 */
async function terminerBureau() {
    showTerminerBureauModal();
}

/**
 * Met à jour l'indicateur de statut de connexion dans l'interface
 */
function updateOnlineStatusIndicator() {
    const header = document.querySelector('header');
    if (!header) return;

    // Supprimer l'ancien indicateur s'il existe
    const existingIndicator = document.getElementById('online-status-indicator');
    if (existingIndicator) {
        existingIndicator.remove();
    }

    // Créer le nouvel indicateur
    const indicator = document.createElement('div');
    indicator.id = 'online-status-indicator';
    indicator.className = `absolute top-2 right-2 w-3 h-3 rounded-full ${
        navigator.onLine ? 'bg-green-500' : 'bg-red-500'
    } ${navigator.onLine ? 'animate-pulse' : ''}`;
    indicator.title = navigator.onLine ? 'En ligne' : 'Hors ligne';
    indicator.setAttribute('aria-label', navigator.onLine ? 'En ligne' : 'Hors ligne');
    
    header.style.position = 'relative';
    header.appendChild(indicator);
}

/**
 * Met à jour l'interface de l'historique des scans
 */
async function updateHistoryUI() {
    const historyList = document.getElementById('history-list');
    const historyCount = document.getElementById('history-count');

    if (!historyList) {
        return;
    }

    try {
        const history = await dbManager.getHistory(20);
        
        if (historyCount) {
            historyCount.textContent = history.length;
        }

        if (history.length === 0) {
            historyList.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <p>Aucun scan effectué</p>
                </div>
            `;
            return;
        }

        const statusColors = {
            present: 'text-green-600 bg-green-50',
            deplace: 'text-orange-600 bg-orange-50',
            absent: 'text-red-600 bg-red-50',
            deteriore: 'text-purple-600 bg-purple-50'
        };

        const statusIcons = {
            present: '✓',
            deplace: '⚠️',
            absent: '✗',
            deteriore: '⚠'
        };

        const statusLabels = {
            present: 'Présent',
            deplace: 'Déplacé',
            absent: 'Absent',
            deteriore: 'Détérioré'
        };

        historyList.innerHTML = history.map(scan => {
            const statusClass = statusColors[scan.statut_scan] || statusColors.present;
            const statusIcon = statusIcons[scan.statut_scan] || statusIcons.present;
            const statusLabel = statusLabels[scan.statut_scan] || 'Inconnu';

            return `
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-semibold text-gray-800">${scan.bien?.code_inventaire || 'N/A'}</span>
                                <span class="px-2 py-1 rounded text-xs font-medium ${statusClass}">
                                    ${statusIcon} ${statusLabel}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">${scan.bien?.designation || 'Bien inconnu'}</p>
                            <p class="text-xs text-gray-500 mt-1">${formatDate(scan.timestamp)}</p>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('[UI] Erreur mise à jour historique:', error);
        historyList.innerHTML = `
            <div class="p-8 text-center text-red-500">
                <p>Erreur lors du chargement</p>
            </div>
        `;
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

/**
 * Attache tous les event listeners de l'application
 */
function attachEventListeners() {
    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('login-email')?.value;
            const password = document.getElementById('login-password')?.value;
            const errorDiv = document.getElementById('login-error');
            const loginBtn = document.getElementById('login-btn');
            const loginBtnText = document.getElementById('login-btn-text');
            const loginSpinner = document.getElementById('login-spinner');

            if (!email || !password) {
                if (errorDiv) {
                    errorDiv.textContent = 'Veuillez remplir tous les champs';
                    errorDiv.classList.remove('hidden');
                }
                return;
            }

            // Désactiver le bouton
            if (loginBtn) loginBtn.disabled = true;
            if (loginBtnText) loginBtnText.textContent = 'Connexion...';
            if (loginSpinner) loginSpinner.classList.remove('hidden');
            if (errorDiv) errorDiv.classList.add('hidden');

            const result = await AuthManager.login(email, password);

            if (result.success) {
                await loadInventaire();
                showView('scanner');
                await scannerManager.init();
            } else {
                if (errorDiv) {
                    errorDiv.textContent = result.error;
                    errorDiv.classList.remove('hidden');
                }
                if (loginBtn) loginBtn.disabled = false;
                if (loginBtnText) loginBtnText.textContent = 'Se connecter';
                if (loginSpinner) loginSpinner.classList.add('hidden');
            }
        });
    }

    // Menu toggle
    const menuBtn = document.getElementById('menu-btn');
    const closeMenuBtn = document.getElementById('close-menu');
    const menuOverlay = document.getElementById('menu-overlay');
    const menuDrawer = document.getElementById('menu-drawer');

    if (menuBtn && menuDrawer) {
        menuBtn.addEventListener('click', () => {
            menuDrawer.classList.remove('hidden');
        });
    }

    if (closeMenuBtn && menuDrawer) {
        closeMenuBtn.addEventListener('click', () => {
            menuDrawer.classList.add('hidden');
        });
    }

    if (menuOverlay && menuDrawer) {
        menuOverlay.addEventListener('click', () => {
            menuDrawer.classList.add('hidden');
        });
    }

    // Navigation
    const navMesLocalisations = document.getElementById('nav-mes-localisations');
    if (navMesLocalisations) {
        navMesLocalisations.addEventListener('click', (e) => {
            e.preventDefault();
            showView('mes-localisations');
            loadMesLocalisations();
            if (menuDrawer) menuDrawer.classList.add('hidden');
        });
    }

    const navHistorique = document.getElementById('nav-historique');
    if (navHistorique) {
        navHistorique.addEventListener('click', (e) => {
            e.preventDefault();
            const toggleHistory = document.getElementById('toggle-history');
            if (toggleHistory) toggleHistory.click();
            if (menuDrawer) menuDrawer.classList.add('hidden');
        });
    }

    const navLogout = document.getElementById('nav-logout');
    if (navLogout) {
        navLogout.addEventListener('click', async () => {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                try {
                    await API.logout();
                } catch (error) {
                    console.error('[Auth] Erreur logout API:', error);
                }
                AuthManager.logout();
            }
        });
    }

    // Scanner
    const startScannerBtn = document.getElementById('start-scanner-btn');
    if (startScannerBtn) {
        startScannerBtn.addEventListener('click', () => {
            scannerManager.start();
        });
    }

    // Terminer bureau
    const btnTerminerBureau = document.getElementById('btn-terminer-bureau');
    if (btnTerminerBureau) {
        btnTerminerBureau.addEventListener('click', () => {
            showTerminerBureauModal();
        });
    }

    // Toggle history
    const toggleHistory = document.getElementById('toggle-history');
    if (toggleHistory) {
        toggleHistory.addEventListener('click', () => {
            const content = document.getElementById('history-content');
            const chevron = document.getElementById('history-chevron');
            
            if (content && chevron) {
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                    updateHistoryUI();
                } else {
                    content.classList.add('hidden');
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        });
    }

    // Back to scanner
    const backToScanner = document.getElementById('back-to-scanner');
    if (backToScanner) {
        backToScanner.addEventListener('click', () => {
            showView('scanner');
        });
    }

    // Network status
    window.addEventListener('online', () => {
        AppState.isOnline = true;
        showToast('Connexion rétablie', 'success');
        updateOnlineStatusIndicator();
        SyncManager.syncPendingScans();
    });

    window.addEventListener('offline', () => {
        AppState.isOnline = false;
        showToast('Mode hors ligne activé', 'warning');
        updateOnlineStatusIndicator();
        SyncManager.updatePendingScansBadge();
    });
}

// ============================================
// DATA LOADING
// ============================================

/**
 * Charge l'inventaire en cours de l'utilisateur
 */
async function loadInventaire() {
    try {
        const inventaire = await API.getCurrentInventaire();
        
        if (!inventaire) {
            showToast('Aucun inventaire en cours', 'warning');
            return;
        }

        AppState.inventaire = inventaire;
        localStorage.setItem(CONFIG.STORAGE_KEY_INVENTAIRE, JSON.stringify(inventaire));

        // Charger la localisation active si elle existe
        const savedLocation = localStorage.getItem(CONFIG.STORAGE_KEY_LOCATION);
        if (savedLocation) {
            try {
                const locationData = JSON.parse(savedLocation);
                AppState.activeLocation = locationData.activeLocation;
                AppState.localisation = locationData.localisation;
                AppState.biensAttendus = locationData.biens || [];
                scannerManager.currentMode = 'bien';
                updateActiveLocationUI();
            } catch (error) {
                console.error('[App] Erreur chargement localisation:', error);
            }
        }

        // Mettre à jour l'UI
        const userName = document.getElementById('user-name');
        const menuUserName = document.getElementById('menu-user-name');
        const menuUserRole = document.getElementById('menu-user-role');

        if (userName && AppState.user) {
            userName.textContent = AppState.user.name || AppState.user.email;
        }
        if (menuUserName && AppState.user) {
            menuUserName.textContent = AppState.user.name || AppState.user.email;
        }
        if (menuUserRole && AppState.user) {
            menuUserRole.textContent = AppState.user.role === 'admin' ? 'Administrateur' : 'Agent';
        }

    } catch (error) {
        console.error('[App] Erreur chargement inventaire:', error);
        showToast('Erreur lors du chargement de l\'inventaire', 'error');
    }
}

/**
 * Charge et affiche les localisations assignées à l'utilisateur
 */
async function loadMesLocalisations() {
    const listDiv = document.getElementById('localisations-list');
    
    if (!listDiv) {
        return;
    }

    try {
        listDiv.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div></div>';

        const localisations = await API.getMesLocalisations(AppState.inventaire.id);

        if (localisations.length === 0) {
            listDiv.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p>Aucune localisation assignée</p>
                </div>
            `;
            return;
        }

        const statusColors = {
            en_attente: 'bg-gray-100 text-gray-700',
            en_cours: 'bg-blue-100 text-blue-700',
            termine: 'bg-green-100 text-green-700'
        };

        const statusLabels = {
            en_attente: 'En attente',
            en_cours: 'En cours',
            termine: 'Terminé'
        };

        listDiv.innerHTML = localisations.map(invLoc => {
            const percentage = invLoc.nombre_biens_attendus > 0 
                ? Math.round((invLoc.nombre_biens_scannes / invLoc.nombre_biens_attendus) * 100)
                : 0;

            return `
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-800">${invLoc.localisation?.code || 'N/A'}</h3>
                                <p class="text-gray-600">${invLoc.localisation?.designation || 'N/A'}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    ${invLoc.localisation?.batiment || ''} 
                                    ${invLoc.localisation?.etage ? 'Étage ' + invLoc.localisation.etage : ''}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColors[invLoc.statut] || statusColors.en_attente}">
                                ${statusLabels[invLoc.statut] || 'Inconnu'}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Progression</span>
                                <span class="font-semibold">${invLoc.nombre_biens_scannes || 0}/${invLoc.nombre_biens_attendus || 0} biens</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: ${percentage}%"></div>
                            </div>
                        </div>

                        ${invLoc.statut !== 'termine' ? `
                        <button onclick="activerLocalisation('${invLoc.localisation?.code || ''}')" 
                                class="mt-4 w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Scanner ce bureau
                        </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');

    } catch (error) {
        console.error('[App] Erreur chargement localisations:', error);
        listDiv.innerHTML = `
            <div class="text-center py-12 text-red-500">
                <p>Erreur lors du chargement</p>
            </div>
        `;
    }
}

/**
 * Active une localisation pour le scan
 * @param {string} code - Code de la localisation
 */
function activerLocalisation(code) {
    showView('scanner');
    showToast(`Scannez le QR code du bureau ${code}`, 'info');
    scannerManager.currentMode = 'localisation';
    if (!scannerManager.isScanning) {
        scannerManager.start();
    }
}

// Exposer les fonctions globalement pour les event handlers inline
window.enregistrerScan = enregistrerScan;
window.annulerScan = annulerScan;
window.activerLocalisation = activerLocalisation;
window.showTerminerBureauModal = showTerminerBureauModal;
window.closeTerminerBureauModal = closeTerminerBureauModal;
window.confirmTerminerBureau = confirmTerminerBureau;

// ============================================
// INITIALIZATION
// ============================================

/**
 * Initialise l'application complète
 */
async function init() {
    console.log('[App] Initialisation de l\'application...');

    try {
        // Initialiser IndexedDB
        await dbManager.init();
        console.log('[App] IndexedDB initialisé');

        // Vérifier l'authentification
        const isAuthenticated = AuthManager.init();

        // Attacher les event listeners
        attachEventListeners();

        if (isAuthenticated) {
            // Charger l'inventaire
            await loadInventaire();
            
            // Initialiser le scanner
            await scannerManager.init();
            
            // Afficher la vue scanner
            showView('scanner');

            // Mettre à jour le badge des scans en attente
            await SyncManager.updatePendingScansBadge();

            // Mettre à jour l'indicateur de connexion
            updateOnlineStatusIndicator();

            // Synchroniser si en ligne
            if (navigator.onLine) {
                SyncManager.syncPendingScans();
            }
        } else {
            // Afficher la vue login
            showView('login');
        }

        console.log('[App] Initialisation terminée');

    } catch (error) {
        console.error('[App] Erreur initialisation:', error);
        showToast('Erreur lors de l\'initialisation', 'error');
    }
}

// Démarrer l'app quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
