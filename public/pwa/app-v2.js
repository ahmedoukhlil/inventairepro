/**
 * Application PWA Inventaire v2 - Workflow par Emplacement
 * Scan QR code emplacement → Scan codes-barres 128 → Calcul écarts
 */

console.log('[App v2] Démarrage...');

// ===========================================
// CONFIGURATION
// ===========================================

const CONFIG = {
    API_BASE_URL: window.location.origin + '/api/v1',
    STORAGE_KEY_TOKEN: 'inventaire_token_v2',
    STORAGE_KEY_USER: 'inventaire_user_v2',
};

// ===========================================
// HAPTIC FEEDBACK (Vibrations) - Mobile
// ===========================================

class HapticFeedback {
    static isSupported() {
        return 'vibrate' in navigator;
    }

    static light() {
        if (this.isSupported()) {
            navigator.vibrate(10);
        }
    }

    static medium() {
        if (this.isSupported()) {
            navigator.vibrate(20);
        }
    }

    static heavy() {
        if (this.isSupported()) {
            navigator.vibrate(50);
        }
    }

    static success() {
        if (this.isSupported()) {
            navigator.vibrate([20, 50, 20]);
        }
    }

    static error() {
        if (this.isSupported()) {
            navigator.vibrate([50, 100, 50, 100, 50]);
        }
    }

    static warning() {
        if (this.isSupported()) {
            navigator.vibrate([30, 50, 30]);
        }
    }
}

// ===========================================
// STATE
// ===========================================

const AppState = {
    token: null,
    user: null,
    currentEmplacement: null,
    biensAttendus: [],
    biensScannés: [],
    scannerActive: false,
    barcodeScanner: null,
};

// ===========================================
// API HELPER
// ===========================================

class API {
    static async request(endpoint, options = {}) {
        const url = `${CONFIG.API_BASE_URL}${endpoint}`;
        const method = options.method || 'GET';
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        if (AppState.token) {
            defaultOptions.headers['Authorization'] = `Bearer ${AppState.token}`;
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
                if (response.status === 401) {
                    AuthManager.logout();
                    throw new Error('Session expirée');
                }
                
                const errorText = await response.text();
                try {
                    const errorData = JSON.parse(errorText);
                    throw new Error(errorData.message || 'Erreur API');
                } catch {
                    throw new Error(`Erreur HTTP ${response.status}`);
                }
            }

            return await response.json();
        } catch (error) {
            console.error('[API] Erreur:', error);
            throw error;
        }
    }
}

// ===========================================
// AUTH MANAGER
// ===========================================

class AuthManager {
    static async login(username, password) {
        try {
            const response = await API.request('/login', {
                method: 'POST',
                body: JSON.stringify({ 
                    users: username, 
                    mdp: password 
                })
            });

            AppState.token = response.token;
            AppState.user = response.user;
            
            localStorage.setItem(CONFIG.STORAGE_KEY_TOKEN, response.token);
            localStorage.setItem(CONFIG.STORAGE_KEY_USER, JSON.stringify(response.user));

            HapticFeedback.success();
            UI.showView('scanner');
            UI.updateUserInfo();
            UI.showToast('✅ Connexion réussie', 'success');
        } catch (error) {
            HapticFeedback.error();
            throw error;
        }
    }

    static logout() {
        AppState.token = null;
        AppState.user = null;
        localStorage.removeItem(CONFIG.STORAGE_KEY_TOKEN);
        localStorage.removeItem(CONFIG.STORAGE_KEY_USER);
        
        HapticFeedback.medium();
        UI.showView('login');
        UI.showToast('👋 Déconnexion', 'info');
    }

    static checkAuth() {
        const token = localStorage.getItem(CONFIG.STORAGE_KEY_TOKEN);
        const userStr = localStorage.getItem(CONFIG.STORAGE_KEY_USER);

        if (token && userStr) {
            try {
                AppState.token = token;
                AppState.user = JSON.parse(userStr);
                UI.showView('scanner');
                UI.updateUserInfo();
                return true;
            } catch {
                this.logout();
            }
        }
        return false;
    }
}

// ===========================================
// SCANNER MANAGER - QR Code pour Emplacement
// ===========================================

class ScannerManager {
    static async startQRScanner() {
        const container = document.getElementById('scanner-container');
        container.innerHTML = `
            <video id="qr-video" class="w-full h-full object-cover" autoplay playsinline muted></video>
        `;

        try {
            // Configuration optimisée pour mobile Android et iOS
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment', // Caméra arrière
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 },
                    aspectRatio: { ideal: 16/9 },
                    frameRate: { ideal: 30, max: 60 }
                },
                audio: false
            });

            const video = document.getElementById('qr-video');
            video.srcObject = stream;

            AppState.scannerActive = true;
            HapticFeedback.light();
            this.detectQRCode(video);

        } catch (error) {
            console.error('[Scanner] Erreur caméra:', error);
            HapticFeedback.error();
            UI.showToast('❌ Impossible d\'accéder à la caméra', 'error');
        }
    }

    static detectQRCode(video) {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        const scanFrame = () => {
            if (!AppState.scannerActive) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);

                if (code && code.data) {
                    this.handleQRCodeDetected(code.data);
                    return;
                }
            }

            requestAnimationFrame(scanFrame);
        };

        scanFrame();
    }

    static async handleQRCodeDetected(data) {
        // Format attendu: EMP-{id}
        const match = data.match(/^EMP-(\d+)$/);
        
        if (!match) {
            HapticFeedback.warning();
            UI.showToast('⚠️ QR Code non reconnu', 'warning');
            return;
        }

        const idEmplacement = match[1];
        this.stopScanner();
        
        HapticFeedback.success();
        UI.showToast('🔍 Chargement de l\'emplacement...', 'info');

        try {
            const response = await API.request(`/emplacements/${idEmplacement}/biens`);
            
            AppState.currentEmplacement = response.emplacement;
            AppState.biensAttendus = response.biens;
            AppState.biensScannés = [];

            HapticFeedback.medium();
            UI.showEmplacementView();
            BarcodeScannerManager.startBarcodeScanner();

        } catch (error) {
            HapticFeedback.error();
            UI.showToast('❌ Erreur: ' + error.message, 'error');
        }
    }

    static stopScanner() {
        AppState.scannerActive = false;
        const video = document.getElementById('qr-video');
        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    }
}

// ===========================================
// BARCODE SCANNER - Code-barres 128 avec QuaggaJS
// ===========================================

class BarcodeScannerManager {
    static startBarcodeScanner() {
        const container = document.getElementById('barcode-scanner-container');
        
        // Configuration optimisée pour mobile Android et iOS

        Quagga.init({
            inputStream: {
                type: 'LiveStream',
                target: container,
                constraints: {
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 },
                    facingMode: 'environment', // Caméra arrière
                    aspectRatio: { ideal: 16/9 }
                },
                area: { // Zone de scan optimisée
                    top: "20%",
                    right: "10%",
                    left: "10%",
                    bottom: "20%"
                }
            },
            frequency: 10, // Réduire la fréquence pour économiser la batterie
            decoder: {
                readers: ['code_128_reader'], // Code-barres 128 uniquement
                multiple: false // Un seul code à la fois
            },
            locate: true, // Localiser le code-barres
            numOfWorkers: navigator.hardwareConcurrency || 2, // Optimiser selon le CPU
            locator: {
                patchSize: 'medium',
                halfSample: true // Performance mobile
            }
        }, (err) => {
            if (err) {
                console.error('[Barcode] Erreur init:', err);
                HapticFeedback.error();
                UI.showToast('❌ Erreur scanner code-barres', 'error');
                return;
            }

            Quagga.start();
            HapticFeedback.light();
            console.log('[Barcode] Scanner démarré');
        });

        Quagga.onDetected((result) => {
            if (result && result.codeResult && result.codeResult.code) {
                this.handleBarcodeDetected(result.codeResult.code);
            }
        });
    }

    static async handleBarcodeDetected(codeBarre) {
        console.log('[Barcode] Détecté:', codeBarre);

        // Le code-barres 128 contient le NumOrdre
        const numOrdre = parseInt(codeBarre, 10);

        // Vérifier si déjà scanné
        if (AppState.biensScannés.includes(numOrdre)) {
            HapticFeedback.warning();
            UI.showToast('⚠️ Déjà scanné', 'warning');
            return;
        }

        // Chercher dans les biens attendus par NumOrdre
        const bien = AppState.biensAttendus.find(b => b.num_ordre === numOrdre);

        if (bien) {
            AppState.biensScannés.push(numOrdre);
            HapticFeedback.success();
            UI.showToast(`✅ ${bien.designation}`, 'success');
            UI.updateBiensList();
            UI.updateProgress();
        } else {
            HapticFeedback.error();
            UI.showToast(`⚠️ Bien non attendu: ${codeBarre}`, 'warning');
        }
    }

    static stopBarcodeScanner() {
        if (Quagga) {
            Quagga.stop();
            console.log('[Barcode] Scanner arrêté');
        }
    }
}

// ===========================================
// UI MANAGER
// ===========================================

class UI {
    static showView(viewName) {
        document.querySelectorAll('[id^="view-"]').forEach(view => {
            view.classList.add('hidden');
        });

        const view = document.getElementById(`view-${viewName}`);
        if (view) {
            view.classList.remove('hidden');
        }

        // Cacher le header sur login
        const header = document.getElementById('app-header');
        if (viewName === 'login') {
            header.classList.add('hidden');
        } else {
            header.classList.remove('hidden');
        }
    }

    static updateUserInfo() {
        if (AppState.user) {
            document.getElementById('user-name').textContent = AppState.user.name || 'Agent';
            document.getElementById('menu-user-name').textContent = AppState.user.name || 'Agent';
        }
    }

    static showEmplacementView() {
        this.showView('emplacement-biens');

        const emp = AppState.currentEmplacement;
        document.getElementById('emplacement-nom').textContent = emp.nom;
        
        let details = emp.code;
        if (emp.localisation) details += ` • ${emp.localisation.nom}`;
        if (emp.affectation) details += ` • ${emp.affectation.nom}`;
        document.getElementById('emplacement-details').textContent = details;

        document.getElementById('biens-count').textContent = `${AppState.biensAttendus.length} bien(s)`;

        this.updateBiensList();
        this.updateProgress();
    }

    static updateBiensList() {
        const list = document.getElementById('biens-list');
        list.innerHTML = '';

        AppState.biensAttendus.forEach(bien => {
            const isScanned = AppState.biensScannés.includes(bien.num_ordre);
            
            const item = document.createElement('div');
            item.className = `p-3 ${isScanned ? 'bg-green-50' : 'bg-white'}`;
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 text-sm">${bien.designation}</p>
                        <p class="text-xs text-gray-600">N° ${bien.num_ordre} • ${bien.categorie}</p>
                    </div>
                    <div class="ml-3">
                        ${isScanned ? 
                            '<svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                            '<svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>'
                        }
                    </div>
                </div>
            `;
            list.appendChild(item);
        });
    }

    static updateProgress() {
        const total = AppState.biensAttendus.length;
        const scanned = AppState.biensScannés.length;
        const percent = total > 0 ? Math.round((scanned / total) * 100) : 0;

        document.getElementById('progress-text').textContent = `${scanned}/${total} biens scannés`;
        document.getElementById('progress-percent').textContent = `${percent}%`;
        document.getElementById('progress-bar').style.width = `${percent}%`;
    }

    static async showResultatsView() {
        BarcodeScannerManager.stopBarcodeScanner();

        UI.showToast('📊 Calcul des écarts...', 'info');

        try {
            // AppState.biensScannés contient déjà les NumOrdre
            const response = await API.request(
                `/emplacements/${AppState.currentEmplacement.id}/terminer`,
                {
                    method: 'POST',
                    body: JSON.stringify({
                        biens_scannes: AppState.biensScannés
                    })
                }
            );

            this.showView('resultats');
            this.displayResultats(response);

        } catch (error) {
            UI.showToast('❌ Erreur: ' + error.message, 'error');
        }
    }

    static displayResultats(data) {
        const stats = data.statistiques;

        document.getElementById('stat-scannes').textContent = stats.total_scanne;
        document.getElementById('stat-manquants').textContent = stats.total_manquant;
        
        document.getElementById('conformite-bar').style.width = `${stats.taux_conformite}%`;
        document.getElementById('conformite-text').textContent = `${stats.taux_conformite}%`;

        // Biens manquants
        if (data.biens_manquants.length > 0) {
            document.getElementById('section-manquants').classList.remove('hidden');
            const listManquants = document.getElementById('list-manquants');
            listManquants.innerHTML = '';

            data.biens_manquants.forEach(bien => {
                const item = document.createElement('div');
                item.className = 'bg-red-50 border-l-4 border-red-500 p-3 rounded';
                item.innerHTML = `
                    <p class="font-medium text-red-800">${bien.designation}</p>
                    <p class="text-sm text-red-600">N° ${bien.num_ordre} • ${bien.categorie}</p>
                `;
                listManquants.appendChild(item);
            });
        } else {
            document.getElementById('section-manquants').classList.add('hidden');
        }
    }

    static showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg`;
        toast.textContent = message;

        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// ===========================================
// EVENT LISTENERS
// ===========================================

// Login
document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('login-users').value;
    const password = document.getElementById('login-mdp').value;
    const btn = document.getElementById('login-btn');
    const btnText = document.getElementById('login-btn-text');
    const spinner = document.getElementById('login-spinner');
    const errorDiv = document.getElementById('login-error');

    btn.disabled = true;
    btnText.textContent = 'Connexion...';
    spinner.classList.remove('hidden');
    errorDiv.classList.add('hidden');

    try {
        await AuthManager.login(username, password);
    } catch (error) {
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.message || 'Erreur de connexion';
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Se connecter';
        spinner.classList.add('hidden');
    }
});

// Menu
document.getElementById('menu-btn').addEventListener('click', () => {
    document.getElementById('menu-drawer').classList.remove('hidden');
});

document.getElementById('close-menu').addEventListener('click', () => {
    document.getElementById('menu-drawer').classList.add('hidden');
});

document.getElementById('menu-overlay').addEventListener('click', () => {
    document.getElementById('menu-drawer').classList.add('hidden');
});

document.getElementById('nav-logout').addEventListener('click', () => {
    AuthManager.logout();
    document.getElementById('menu-drawer').classList.add('hidden');
});

// Scanner
document.getElementById('start-scanner-btn').addEventListener('click', () => {
    ScannerManager.startQRScanner();
    document.getElementById('start-scanner-btn').style.display = 'none';
});

// Terminer scan emplacement
document.getElementById('btn-terminer-emplacement').addEventListener('click', () => {
    if (confirm('Terminer le scan de cet emplacement ?')) {
        UI.showResultatsView();
    }
});

// Nouveau scan
document.getElementById('btn-nouveau-scan').addEventListener('click', () => {
    AppState.currentEmplacement = null;
    AppState.biensAttendus = [];
    AppState.biensScannés = [];
    UI.showView('scanner');
});

// ===========================================
// INIT
// ===========================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('[App v2] Init...');
    
    if (!AuthManager.checkAuth()) {
        UI.showView('login');
    }
});
