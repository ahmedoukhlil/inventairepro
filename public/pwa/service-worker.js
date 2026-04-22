/**
 * Service Worker pour l'application PWA de scan d'inventaire
 * Gère le cache, le mode offline et la synchronisation en arrière-plan
 */

const CACHE_NAME = 'inventaire-scanner-v6';
const ASSETS_TO_CACHE = [
  '/pwa/',
  '/pwa/index-v2.html',
  '/pwa/collecte-initiale.html',
  '/pwa/app-v2.js',
  '/pwa/collecte-initiale.js',
  '/pwa/styles.css',
  '/pwa/tailwind.css',
  '/pwa/manifest.json',
  '/images/icons/icon-144x144.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js'
];

/**
 * Installation : mise en cache des assets essentiels
 */
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installation...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(async (cache) => {
        console.log('[Service Worker] Mise en cache des assets');
        await Promise.all(
          ASSETS_TO_CACHE.map(async (url) => {
            try {
              const isCrossOrigin = url.startsWith('http');
              const request = new Request(url, isCrossOrigin ? { mode: 'no-cors' } : {});
              const response = await fetch(request);
              await cache.put(url, response);
            } catch (err) {
              console.warn('[Service Worker] Asset non mis en cache:', url, err);
            }
          })
        );
      })
      .then(() => self.skipWaiting())
  );
});

/**
 * Activation : nettoyage des anciens caches
 */
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activation...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('[Service Worker] Suppression ancien cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        // Prendre le contrôle de toutes les pages immédiatement
        return self.clients.claim();
      })
      .catch((error) => {
        console.error('[Service Worker] Erreur lors de l\'activation:', error);
      })
  );
});

/**
 * Fetch : stratégie de cache selon le type de ressource
 * - Cache First pour les assets statiques (rapide)
 * - Network First pour les appels API (données à jour)
 */
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Ignorer les requêtes non-GET
  if (request.method !== 'GET') {
    return;
  }

  // Stratégie Network First pour les appels API (GET uniquement, lectures idempotentes)
  if (url.pathname.startsWith('/api/')) {
    const cacheableApi = /\/api\/v1\/(emplacements\/[^/]+\/biens|localisations|biens|etats|inventaires\/current)/.test(url.pathname);

    event.respondWith(
      fetch(request)
        .then((response) => {
          if (cacheableApi && response && response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME)
              .then((cache) => cache.put(request, responseClone))
              .catch((error) => console.error('[Service Worker] Erreur cache API:', error));
          }
          return response;
        })
        .catch(() => {
          // En cas d'échec réseau, retourner depuis le cache si disponible
          return caches.match(request)
            .then((cachedResponse) => {
              if (cachedResponse) {
                console.log('[Service Worker] Réponse API depuis le cache (offline)');
                return cachedResponse;
              }
              // Retourner une réponse d'erreur si rien dans le cache
              return new Response(
                JSON.stringify({ error: 'Réseau indisponible et aucune donnée en cache' }),
                {
                  status: 503,
                  statusText: 'Service Unavailable',
                  headers: { 'Content-Type': 'application/json' }
                }
              );
            });
        })
    );
    return;
  }

  // Stratégie Cache First pour les assets statiques
  event.respondWith(
    caches.match(request)
      .then((cachedResponse) => {
        if (cachedResponse) {
          console.log('[Service Worker] Ressource depuis le cache:', request.url);
          return cachedResponse;
        }

        // Si pas dans le cache, récupérer depuis le réseau
        return fetch(request)
          .then((response) => {
            if (!response || (response.status !== 200 && response.type !== 'opaque')) {
              return response;
            }

            const responseClone = response.clone();
            
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(request, responseClone);
              })
              .catch((error) => {
                console.error('[Service Worker] Erreur mise en cache asset:', error);
              });

            return response;
          })
          .catch((error) => {
            console.error('[Service Worker] Erreur fetch:', error);
            // Retourner une réponse d'erreur si le fetch échoue
            return new Response('Ressource non disponible', {
              status: 404,
              statusText: 'Not Found'
            });
          });
      })
  );
});

/**
 * Gestion de la synchronisation en arrière-plan (Background Sync)
 * Permet de synchroniser les scans en attente lorsque la connexion revient
 */
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-scans') {
    console.log('[Service Worker] Synchronisation des scans...');
    event.waitUntil(syncScans());
  }
});

/**
 * Fonction de synchronisation des scans en attente
 * Récupère les scans stockés localement et les envoie à l'API
 */
async function syncScans() {
  try {
    // Récupérer les scans en attente depuis IndexedDB
    const db = await openDB();
    const transaction = db.transaction(['pending-scans'], 'readonly');
    const store = transaction.objectStore('pending-scans');
    const scans = await store.getAll();

    console.log(`[Service Worker] ${scans.length} scan(s) à synchroniser`);

    for (const scan of scans) {
      try {
        const endpoint = scan.emplacement_id
          ? `/api/v1/emplacements/${scan.emplacement_id}/scan`
          : `/api/v1/inventaires/${scan.inventaire_id}/scan`;

        const response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${scan.token}`
          },
          body: JSON.stringify(scan.data)
        });

        if (response.ok) {
          console.log('[Service Worker] Scan synchronisé avec succès:', scan.id);
          
          // Supprimer le scan de la file d'attente
          const deleteTransaction = db.transaction(['pending-scans'], 'readwrite');
          const deleteStore = deleteTransaction.objectStore('pending-scans');
          await deleteStore.delete(scan.id);
        } else {
          console.error('[Service Worker] Erreur synchronisation scan:', response.status);
        }
      } catch (error) {
        console.error('[Service Worker] Erreur sync scan:', error);
        // Continuer avec les autres scans même en cas d'erreur
      }
    }
  } catch (error) {
    console.error('[Service Worker] Erreur synchronisation générale:', error);
  }
}

/**
 * Fonction helper pour ouvrir IndexedDB
 * Crée la base de données et les object stores si nécessaire
 */
function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('InventaireDB', 1);

    request.onerror = () => {
      console.error('[Service Worker] Erreur ouverture IndexedDB:', request.error);
      reject(request.error);
    };

    request.onsuccess = () => {
      resolve(request.result);
    };

    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      
      // Créer l'object store pour les scans en attente
      if (!db.objectStoreNames.contains('pending-scans')) {
        const objectStore = db.createObjectStore('pending-scans', {
          keyPath: 'id',
          autoIncrement: true
        });
        
        // Créer des index pour faciliter les recherches
        objectStore.createIndex('inventaire_id', 'inventaire_id', { unique: false });
        objectStore.createIndex('timestamp', 'timestamp', { unique: false });
        
        console.log('[Service Worker] IndexedDB initialisé');
      }
    };
  });
}

/**
 * Gestion des messages depuis l'application principale
 * Permet la communication bidirectionnelle entre l'app et le service worker
 */
self.addEventListener('message', (event) => {
  console.log('[Service Worker] Message reçu:', event.data);

  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'SYNC_SCANS') {
    event.waitUntil(syncScans());
  }
});
