/**
 * Application PWA Inventaire v2 - Workflow par Emplacement
 * Scan QR code emplacement -> scan QR biens OU saisie OU liste a cocher -> ecarts
 */

console.log('[App v2] Demarrage...');

// ===========================================
// CONFIGURATION
// ===========================================

const CONFIG = {
    API_BASE_URL: window.location.origin + '/api/v1',
    STORAGE_KEY_TOKEN: 'inventaire_token_v2',
    STORAGE_KEY_USER: 'inventaire_user_v2',
};

// ===========================================
// HAPTIC FEEDBACK
// ===========================================

class HapticFeedback {
    static isSupported() { return 'vibrate' in navigator; }
    static light() { if (this.isSupported()) navigator.vibrate(10); }
    static medium() { if (this.isSupported()) navigator.vibrate(20); }
    static heavy() { if (this.isSupported()) navigator.vibrate(50); }
    static success() { if (this.isSupported()) navigator.vibrate([20, 50, 20]); }
    static error() { if (this.isSupported()) navigator.vibrate([50, 100, 50, 100, 50]); }
    static warning() { if (this.isSupported()) navigator.vibrate([30, 50, 30]); }
}

// ===========================================
// STATE
// ===========================================

const AppState = {
    token: null,
    user: null,
    currentEmplacement: null,
    biensAttendus: [],
    biensScannes: [], // [{ num_ordre, etat_id, photo? }]
    etats: [],
    empScannerActive: false,
    bienScannerActive: false,
    modalBienEnCours: null,
};

// ===========================================
// API
// ===========================================

class API {
    static async request(endpoint, options = {}) {
        const url = `${CONFIG.API_BASE_URL}${endpoint}`;
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (AppState.token) {
            defaultHeaders['Authorization'] = `Bearer ${AppState.token}`;
        }

        const finalOptions = {
            ...options,
            headers: { ...defaultHeaders, ...(options.headers || {}) }
        };

        const response = await fetch(url, finalOptions);

        if (!response.ok) {
            if (response.status === 401) {
                AuthManager.logout();
                throw new Error('Session expiree');
            }
            const errorText = await response.text();
            let errorData = null;
            try { errorData = JSON.parse(errorText); } catch {}

            if (response.status === 422 && errorData) {
                // Erreur de validation Laravel : extraire le premier message utile
                if (errorData.errors && typeof errorData.errors === 'object') {
                    const firstField = Object.keys(errorData.errors)[0];
                    const firstMsg = Array.isArray(errorData.errors[firstField])
                        ? errorData.errors[firstField][0]
                        : errorData.errors[firstField];
                    throw new Error(firstMsg || errorData.message || 'Identifiants incorrects');
                }
                throw new Error(errorData.message || 'Identifiants incorrects');
            }
            if (errorData && errorData.message) {
                throw new Error(errorData.message);
            }
            throw new Error(`Erreur HTTP ${response.status}`);
        }

        return await response.json();
    }
}

// ===========================================
// AUTH
// ===========================================

class AuthManager {
    static async login(username, password) {
        try {
            const response = await API.request('/login', {
                method: 'POST',
                body: JSON.stringify({ users: username, mdp: password })
            });

            AppState.token = response.token;
            AppState.user = response.user;
            localStorage.setItem(CONFIG.STORAGE_KEY_TOKEN, response.token);
            localStorage.setItem(CONFIG.STORAGE_KEY_USER, JSON.stringify(response.user));

            await UI.loadEtats();

            HapticFeedback.success();
            UI.showView('scanner');
            UI.updateUserInfo();
            UI.showToast('Connexion reussie', 'success');
            EmplacementTabs.activate('scan');
        } catch (error) {
            HapticFeedback.error();
            throw error;
        }
    }

    static logout() {
        EmplacementScanner.stop();
        BienScanner.stop();
        AppState.token = null;
        AppState.user = null;
        localStorage.removeItem(CONFIG.STORAGE_KEY_TOKEN);
        localStorage.removeItem(CONFIG.STORAGE_KEY_USER);

        HapticFeedback.medium();
        UI.showView('login');
    }

    static checkAuth() {
        const token = localStorage.getItem(CONFIG.STORAGE_KEY_TOKEN);
        const userStr = localStorage.getItem(CONFIG.STORAGE_KEY_USER);
        if (token && userStr) {
            try {
                AppState.token = token;
                AppState.user = JSON.parse(userStr);
                UI.loadEtats().then(() => {});
                UI.showView('scanner');
                UI.updateUserInfo();
                EmplacementTabs.activate('scan');
                return true;
            } catch {
                this.logout();
            }
        }
        return false;
    }
}

// ===========================================
// GENERIC QR SCANNER (via jsQR)
// ===========================================

class QRScanner {
    constructor(containerId, onDetected, activeFlagKey) {
        this.containerId = containerId;
        this.onDetected = onDetected;
        this.activeFlagKey = activeFlagKey;
        this.stream = null;
        this.video = null;
        this.rafId = null;
    }

    async start() {
        if (typeof jsQR === 'undefined') {
            UI.showToast('Bibliotheque QR non chargee. Rechargez la page.', 'error');
            return;
        }

        const container = document.getElementById(this.containerId);
        if (!container) return;

        // Créer la vidéo sans écraser l'overlay (si présent)
        const overlay = container.querySelector('.scan-overlay');
        container.querySelectorAll('video').forEach(v => v.remove());

        this.video = document.createElement('video');
        this.video.setAttribute('autoplay', '');
        this.video.setAttribute('playsinline', '');
        this.video.setAttribute('muted', '');
        this.video.className = 'w-full h-full object-cover';
        container.insertBefore(this.video, overlay || null);

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 }
                },
                audio: false
            });
            this.video.srcObject = this.stream;

            this.video.addEventListener('loadedmetadata', () => {
                AppState[this.activeFlagKey] = true;
                HapticFeedback.light();
                this.detect();
            });
        } catch (error) {
            console.error('[QRScanner] Erreur camera:', error);
            HapticFeedback.error();
            let msg = 'Impossible d acceder a la camera';
            if (error.name === 'NotAllowedError') msg = 'Permission camera refusee.';
            else if (error.name === 'NotFoundError') msg = 'Aucune camera trouvee';
            else if (error.name === 'NotReadableError') msg = 'Camera deja utilisee';
            UI.showToast(msg, 'error');
        }
    }

    detect() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });

        const loop = () => {
            if (!AppState[this.activeFlagKey] || !this.video) return;

            if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                canvas.width = this.video.videoWidth;
                canvas.height = this.video.videoHeight;
                ctx.drawImage(this.video, 0, 0, canvas.width, canvas.height);
                try {
                    const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
                    if (code && code.data) {
                        this.onDetected(code.data);
                        return;
                    }
                } catch (err) {
                    console.error('[QRScanner] Detect error:', err);
                }
            }
            this.rafId = requestAnimationFrame(loop);
        };

        loop();
    }

    stop() {
        AppState[this.activeFlagKey] = false;
        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
            this.rafId = null;
        }
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        if (this.video) {
            this.video.srcObject = null;
            this.video.remove();
            this.video = null;
        }
    }
}

// ===========================================
// EMPLACEMENT SCANNER
// ===========================================

const EmplacementScanner = {
    _instance: null,
    start() {
        if (this._instance) this._instance.stop();
        this._instance = new QRScanner('scanner-container', (data) => this.handle(data), 'empScannerActive');
        this._instance.start();
    },
    stop() {
        if (this._instance) {
            this._instance.stop();
            this._instance = null;
        }
    },
    async handle(data) {
        const match = data.match(/^EMP-(\d+)$/i);
        if (!match) {
            HapticFeedback.warning();
            UI.showToast(`QR non reconnu : ${data}. Format attendu : EMP-{id}`, 'warning');
            return;
        }
        this.stop();
        await loadEmplacement(parseInt(match[1], 10));
    }
};

// ===========================================
// BIEN SCANNER (QR)
// ===========================================

const BienScanner = {
    _instance: null,
    start() {
        if (this._instance) this._instance.stop();
        this._instance = new QRScanner('bien-scanner-container', (data) => this.handle(data), 'bienScannerActive');
        this._instance.start();
    },
    stop() {
        if (this._instance) {
            this._instance.stop();
            this._instance = null;
        }
    },
    async handle(data) {
        // Accepte num d'ordre pur ou format BIEN-{n}
        let numOrdre = null;
        const match = data.match(/^(?:BIEN-)?(\d+)$/i);
        if (match) numOrdre = parseInt(match[1], 10);

        if (!numOrdre) {
            HapticFeedback.warning();
            UI.showToast(`QR non reconnu : ${data}`, 'warning');
            return;
        }
        submitBien(numOrdre);
    }
};

// ===========================================
// CHARGEMENT D'UN EMPLACEMENT
// ===========================================

async function loadEmplacement(idEmplacement) {
    HapticFeedback.success();
    UI.showToast('Chargement de l\'emplacement...', 'info');

    try {
        const response = await API.request(`/emplacements/${idEmplacement}/biens`);
        AppState.currentEmplacement = response.emplacement;
        AppState.biensAttendus = response.biens || [];
        AppState.biensScannes = [];

        HapticFeedback.medium();
        UI.showEmplacementView();
        BienTabs.activate('scan');
    } catch (error) {
        HapticFeedback.error();
        UI.showToast('Erreur : ' + error.message, 'error');
        EmplacementTabs.activate('scan');
    }
}

// ===========================================
// AJOUT D'UN BIEN (scan ou saisie)
// ===========================================

function submitBien(numOrdre) {
    if (AppState.biensScannes.some(b => b.num_ordre === numOrdre)) {
        HapticFeedback.warning();
        UI.showToast('Deja enregistre', 'warning');
        return;
    }

    const bien = AppState.biensAttendus.find(b => b.num_ordre === numOrdre);
    if (!bien) {
        HapticFeedback.error();
        UI.showToast(`Bien non attendu : ${numOrdre}`, 'warning');
        return;
    }

    HapticFeedback.light();
    UI.showModalEtatBien(bien);
}

// ===========================================
// GESTIONNAIRES D'ONGLETS
// ===========================================

const EmplacementTabs = {
    activate(tab) {
        document.querySelectorAll('[data-emp-tab]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.empTab === tab);
        });
        document.querySelectorAll('[data-emp-panel]').forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.empPanel !== tab);
        });

        if (tab === 'scan') {
            EmplacementScanner.start();
        } else {
            EmplacementScanner.stop();
            if (tab === 'manual') {
                setTimeout(() => document.getElementById('manual-emp-input')?.focus(), 100);
            }
        }
    }
};

const BienTabs = {
    activate(tab) {
        document.querySelectorAll('[data-bien-tab]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.bienTab === tab);
        });
        document.querySelectorAll('[data-bien-panel]').forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.bienPanel !== tab);
        });

        if (tab === 'scan') {
            BienScanner.start();
        } else {
            BienScanner.stop();
            if (tab === 'manual') {
                setTimeout(() => document.getElementById('manual-num-ordre-input')?.focus(), 100);
            } else if (tab === 'list') {
                UI.renderCheckList();
            }
        }
    }
};

// ===========================================
// UI
// ===========================================

class UI {
    static async loadEtats() {
        if (!AppState.token) return;
        try {
            const response = await API.request('/etats');
            AppState.etats = response.etats || [];
        } catch (error) {
            console.warn('[UI] Etats non charges:', error);
            AppState.etats = [];
        }
    }

    static showView(viewName) {
        document.querySelectorAll('[id^="view-"]').forEach(v => v.classList.add('hidden'));
        const view = document.getElementById(`view-${viewName}`);
        if (view) view.classList.remove('hidden');

        const header = document.getElementById('app-header');
        if (viewName === 'login') header.classList.add('hidden');
        else header.classList.remove('hidden');

        // Stopper les scanners si on quitte la vue
        if (viewName !== 'scanner') EmplacementScanner.stop();
        if (viewName !== 'emplacement-biens') BienScanner.stop();
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

        let details = emp.code || '';
        if (emp.localisation) details += (details ? ' - ' : '') + emp.localisation.nom;
        if (emp.affectation) details += (details ? ' - ' : '') + emp.affectation.nom;
        document.getElementById('emplacement-details').textContent = details || '-';

        document.getElementById('biens-count').textContent = `${AppState.biensAttendus.length} bien(s)`;

        this.updateBiensList();
        this.updateProgress();
    }

    static updateBiensList() {
        const list = document.getElementById('biens-list');
        list.innerHTML = '';

        AppState.biensAttendus.forEach(bien => {
            const scanData = AppState.biensScannes.find(b => b.num_ordre === bien.num_ordre);
            const isScanned = !!scanData;
            const etatObj = scanData && scanData.etat_id ? AppState.etats.find(e => e.id === scanData.etat_id) : null;
            const isDefectueux = etatObj && etatObj.require_photo;
            const etatLabel = etatObj ? etatObj.label : '';

            const item = document.createElement('div');
            item.className = `p-3 ${isScanned ? (isDefectueux ? 'bg-amber-50' : 'bg-green-50') : 'bg-white'}`;
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 text-sm">${bien.designation}</p>
                        <p class="text-xs text-gray-600">N° ${bien.num_ordre} - ${bien.categorie || ''}${isScanned ? ' - ' + etatLabel : ''}</p>
                    </div>
                    <div class="ml-3">
                        ${isScanned
                            ? '<svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                            : '<svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>'
                        }
                    </div>
                </div>
            `;
            list.appendChild(item);
        });
    }

    static renderCheckList() {
        const container = document.getElementById('list-biens-checkboxes');
        const countEl = document.getElementById('list-biens-count');
        container.innerHTML = '';

        if (!AppState.biensAttendus.length) {
            container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Aucun bien attendu</div>';
            countEl.textContent = '0 bien(s)';
            return;
        }

        countEl.textContent = `${AppState.biensAttendus.length} bien(s)`;

        AppState.biensAttendus.forEach(bien => {
            const isChecked = AppState.biensScannes.some(b => b.num_ordre === bien.num_ordre);
            const row = document.createElement('label');
            row.className = `flex items-center gap-3 p-3 cursor-pointer touch-feedback ${isChecked ? 'bg-green-50' : 'hover:bg-gray-50'}`;
            row.innerHTML = `
                <input type="checkbox" data-num-ordre="${bien.num_ordre}" ${isChecked ? 'checked' : ''}
                       class="checkbox-bien w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm truncate">${bien.designation}</p>
                    <p class="text-xs text-gray-600">N° ${bien.num_ordre} - ${bien.categorie || ''}</p>
                </div>
            `;
            container.appendChild(row);
        });
    }

    static updateProgress() {
        const total = AppState.biensAttendus.length;
        const done = AppState.biensScannes.length;
        const percent = total > 0 ? Math.round((done / total) * 100) : 0;
        document.getElementById('progress-text').textContent = `${done}/${total} biens coches`;
        document.getElementById('progress-percent').textContent = `${percent}%`;
        document.getElementById('progress-bar').style.width = `${percent}%`;
    }

    static async showResultatsView() {
        EmplacementScanner.stop();
        BienScanner.stop();

        UI.showToast('Calcul des ecarts...', 'info');

        try {
            const payload = AppState.biensScannes.map(b => ({
                num_ordre: b.num_ordre,
                etat_id: b.etat_id || null,
                photo: b.photo || null
            }));

            const response = await API.request(
                `/emplacements/${AppState.currentEmplacement.id}/terminer`,
                { method: 'POST', body: JSON.stringify({ biens_scannes: payload }) }
            );

            this.showView('resultats');
            this.displayResultats(response);
        } catch (error) {
            UI.showToast('Erreur : ' + error.message, 'error');
        }
    }

    static showModalEtatBien(bien) {
        AppState.modalBienEnCours = bien;
        document.getElementById('modal-etat-designation').textContent = `${bien.designation} (N° ${bien.num_ordre})`;

        const container = document.getElementById('modal-etat-buttons');
        container.innerHTML = '';
        const etats = AppState.etats.length ? AppState.etats : [{ id: null, label: 'Bon', require_photo: false }];
        etats.forEach(etat => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'modal-etat-btn touch-target py-3 px-4 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-700 hover:border-indigo-500 hover:bg-indigo-50 transition';
            btn.dataset.etatId = etat.id ?? '';
            btn.dataset.requirePhoto = etat.require_photo ? '1' : '0';
            btn.textContent = etat.label;
            container.appendChild(btn);
        });

        document.getElementById('modal-etat-photo-section').classList.add('hidden');
        document.getElementById('modal-etat-photo-input').value = '';
        document.getElementById('modal-etat-photo-preview').classList.add('hidden');
        document.getElementById('modal-etat-confirmer').disabled = true;

        document.getElementById('modal-etat-bien').classList.remove('hidden');
    }

    static hideModalEtatBien() {
        AppState.modalBienEnCours = null;
        document.getElementById('modal-etat-bien').classList.add('hidden');
    }

    static confirmModalEtatBien(etatId, photoBase64) {
        if (!AppState.modalBienEnCours) return;
        const bien = AppState.modalBienEnCours;
        AppState.biensScannes.push({
            num_ordre: bien.num_ordre,
            etat_id: etatId ? parseInt(etatId, 10) : null,
            photo: photoBase64 || null
        });

        HapticFeedback.success();
        UI.showToast(bien.designation, 'success');
        UI.updateBiensList();
        UI.updateProgress();

        // Si on etait dans la liste a cocher, rafraichir aussi la case
        const activeTab = document.querySelector('[data-bien-tab].active')?.dataset.bienTab;
        if (activeTab === 'list') UI.renderCheckList();

        UI.hideModalEtatBien();
    }

    static removeBien(numOrdre) {
        AppState.biensScannes = AppState.biensScannes.filter(b => b.num_ordre !== numOrdre);
        UI.updateBiensList();
        UI.updateProgress();
    }

    static displayResultats(data) {
        const stats = data.statistiques;
        document.getElementById('stat-scannes').textContent = stats.total_scanne;
        document.getElementById('stat-manquants').textContent = stats.total_manquant;
        document.getElementById('conformite-bar').style.width = `${stats.taux_conformite}%`;
        document.getElementById('conformite-text').textContent = `${stats.taux_conformite}%`;

        if (data.biens_manquants && data.biens_manquants.length > 0) {
            document.getElementById('section-manquants').classList.remove('hidden');
            const list = document.getElementById('list-manquants');
            list.innerHTML = '';
            data.biens_manquants.forEach(bien => {
                const item = document.createElement('div');
                item.className = 'bg-red-50 border-l-4 border-red-500 p-3 rounded';
                item.innerHTML = `
                    <p class="font-medium text-red-800">${bien.designation}</p>
                    <p class="text-sm text-red-600">N° ${bien.num_ordre} - ${bien.categorie || ''}</p>
                `;
                list.appendChild(item);
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
        toast.className = `${colors[type] || colors.info} text-white px-4 py-3 rounded-lg shadow-lg`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
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

// Onglets emplacement
document.querySelectorAll('[data-emp-tab]').forEach(btn => {
    btn.addEventListener('click', () => EmplacementTabs.activate(btn.dataset.empTab));
});

// Saisie manuelle emplacement
document.getElementById('manual-emp-submit').addEventListener('click', () => {
    const input = document.getElementById('manual-emp-input');
    const raw = input.value.trim();
    if (!raw) {
        UI.showToast('Saisissez un code ou un ID', 'warning');
        return;
    }
    const match = raw.match(/^(?:EMP-)?(\d+)$/i);
    if (!match) {
        UI.showToast('Format invalide (ex: EMP-12 ou 12)', 'warning');
        return;
    }
    input.value = '';
    loadEmplacement(parseInt(match[1], 10));
});
document.getElementById('manual-emp-input').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('manual-emp-submit').click();
    }
});

// Onglets biens
document.querySelectorAll('[data-bien-tab]').forEach(btn => {
    btn.addEventListener('click', () => BienTabs.activate(btn.dataset.bienTab));
});

// Saisie manuelle numero d'ordre
document.getElementById('manual-num-ordre-submit').addEventListener('click', () => {
    const input = document.getElementById('manual-num-ordre-input');
    const raw = input.value.trim();
    const numOrdre = parseInt(raw, 10);
    if (!raw || isNaN(numOrdre)) {
        UI.showToast('Saisissez un numero valide', 'warning');
        return;
    }
    input.value = '';
    submitBien(numOrdre);
});
document.getElementById('manual-num-ordre-input').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('manual-num-ordre-submit').click();
    }
});

// Liste a cocher : changement d'une case
document.getElementById('list-biens-checkboxes').addEventListener('change', (e) => {
    const cb = e.target.closest('.checkbox-bien');
    if (!cb) return;
    const numOrdre = parseInt(cb.dataset.numOrdre, 10);

    if (cb.checked) {
        // Re-check: si deja present, ne rien faire ; sinon ouvrir modal etat
        if (!AppState.biensScannes.some(b => b.num_ordre === numOrdre)) {
            submitBien(numOrdre);
            // Si modal annulee, la case sera reinitialisee via renderCheckList
        }
    } else {
        UI.removeBien(numOrdre);
        UI.renderCheckList();
    }
});

// Bouton "tout cocher"
document.getElementById('btn-tout-cocher').addEventListener('click', () => {
    const restants = AppState.biensAttendus.filter(b =>
        !AppState.biensScannes.some(s => s.num_ordre === b.num_ordre)
    );
    if (!restants.length) {
        UI.showToast('Tous les biens sont deja coches', 'info');
        return;
    }
    // Ajouter sans modal (etat null = "bon" cote API)
    restants.forEach(bien => {
        AppState.biensScannes.push({ num_ordre: bien.num_ordre, etat_id: null, photo: null });
    });
    HapticFeedback.success();
    UI.showToast(`${restants.length} bien(s) coches`, 'success');
    UI.updateBiensList();
    UI.updateProgress();
    UI.renderCheckList();
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
    AppState.biensScannes = [];
    UI.showView('scanner');
    EmplacementTabs.activate('scan');
});

// Modal etat du bien
let modalEtatSelectionne = null;
let modalPhotoBase64 = null;

document.getElementById('modal-etat-buttons').addEventListener('click', (e) => {
    const btn = e.target.closest('.modal-etat-btn');
    if (!btn) return;

    document.querySelectorAll('.modal-etat-btn').forEach(b => b.classList.remove('border-indigo-600', 'bg-indigo-50'));
    btn.classList.add('border-indigo-600', 'bg-indigo-50');
    modalEtatSelectionne = btn.dataset.etatId;
    const requirePhoto = btn.dataset.requirePhoto === '1';

    const photoSection = document.getElementById('modal-etat-photo-section');
    const confirmBtn = document.getElementById('modal-etat-confirmer');

    if (requirePhoto) {
        photoSection.classList.remove('hidden');
        confirmBtn.disabled = !modalPhotoBase64;
    } else {
        photoSection.classList.add('hidden');
        modalPhotoBase64 = null;
        confirmBtn.disabled = false;
    }
});

document.getElementById('modal-etat-photo-input').addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
        modalPhotoBase64 = event.target.result;
        document.getElementById('modal-etat-photo-img').src = modalPhotoBase64;
        document.getElementById('modal-etat-photo-preview').classList.remove('hidden');
        document.getElementById('modal-etat-confirmer').disabled = false;
    };
    reader.readAsDataURL(file);
});

document.getElementById('modal-etat-photo-retake').addEventListener('click', () => {
    modalPhotoBase64 = null;
    document.getElementById('modal-etat-photo-input').value = '';
    document.getElementById('modal-etat-photo-preview').classList.add('hidden');
    const btn = document.querySelector('.modal-etat-btn.border-indigo-600');
    const requirePhoto = btn && btn.dataset.requirePhoto === '1';
    document.getElementById('modal-etat-confirmer').disabled = requirePhoto;
});

function closeModalEtat() {
    UI.hideModalEtatBien();
    modalEtatSelectionne = null;
    modalPhotoBase64 = null;
    // Si on a annule depuis la checklist, decocher la case
    const activeTab = document.querySelector('[data-bien-tab].active')?.dataset.bienTab;
    if (activeTab === 'list') UI.renderCheckList();
}

document.getElementById('modal-etat-annuler').addEventListener('click', closeModalEtat);
document.getElementById('modal-etat-overlay').addEventListener('click', closeModalEtat);

document.getElementById('modal-etat-confirmer').addEventListener('click', () => {
    if (!modalEtatSelectionne && AppState.etats.length > 0) {
        UI.showToast('Selectionnez un etat', 'warning');
        return;
    }
    const btn = document.querySelector('.modal-etat-btn.border-indigo-600');
    const requirePhoto = btn && btn.dataset.requirePhoto === '1';
    if (requirePhoto && !modalPhotoBase64) {
        UI.showToast('Prenez une photo du bien defectueux', 'warning');
        return;
    }
    UI.confirmModalEtatBien(modalEtatSelectionne, modalPhotoBase64);
    modalEtatSelectionne = null;
    modalPhotoBase64 = null;
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
