/* PWA Collecte Initiale - module autonome */

const CONFIG = {
    API_BASE_URL: `${window.location.origin}/api/v1`,
    TOKEN_KEY: 'inventaire_token_v2',
    USER_KEY: 'inventaire_user_v2',
};

const AppState = {
    token: null,
    user: null,
    items: [],
};

const els = {
    viewLogin: document.getElementById('view-login'),
    viewApp: document.getElementById('view-app'),
    loginForm: document.getElementById('login-form'),
    loginUsers: document.getElementById('login-users'),
    loginMdp: document.getElementById('login-mdp'),
    loginError: document.getElementById('login-error'),
    logoutBtn: document.getElementById('logout-btn'),
    emplacementLabel: document.getElementById('emplacement-label'),
    affectationLabel: document.getElementById('affectation-label'),
    contexteTranscript: document.getElementById('contexte-transcript'),
    voiceContextBtn: document.getElementById('voice-context-btn'),
    parseContextBtn: document.getElementById('parse-context-btn'),
    itemTranscript: document.getElementById('item-transcript'),
    voiceItemBtn: document.getElementById('voice-item-btn'),
    addItemBtn: document.getElementById('add-item-btn'),
    showListBtn: document.getElementById('show-list-btn'),
    quickList: document.getElementById('quick-list'),
    validationPanel: document.getElementById('validation-panel'),
    validationBody: document.getElementById('validation-body'),
    addEmptyRowBtn: document.getElementById('add-empty-row-btn'),
    submitLotBtn: document.getElementById('submit-lot-btn'),
    hideListBtn: document.getElementById('hide-list-btn'),
    resultBox: document.getElementById('result-box'),
    appError: document.getElementById('app-error'),
};

function showError(message) {
    els.appError.textContent = message;
    els.appError.classList.remove('hidden');
}

function clearError() {
    els.appError.classList.add('hidden');
    els.appError.textContent = '';
}

async function apiRequest(endpoint, options = {}) {
    const headers = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        ...(options.headers || {}),
    };

    if (AppState.token) {
        headers.Authorization = `Bearer ${AppState.token}`;
    }

    const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
        ...options,
        headers,
    });

    if (!response.ok) {
        let message = `Erreur HTTP ${response.status}`;
        try {
            const data = await response.json();
            message = data.message || message;
        } catch (_) {
            // no-op
        }
        throw new Error(message);
    }

    return response.json();
}

function setAuthenticatedUI(authenticated) {
    if (authenticated) {
        els.viewLogin.classList.add('hidden');
        els.viewApp.classList.remove('hidden');
    } else {
        els.viewApp.classList.add('hidden');
        els.viewLogin.classList.remove('hidden');
    }
}

function parseContext(transcript) {
    const text = (transcript || '').trim();
    if (!text) {
        return null;
    }

    const lower = text.toLowerCase();
    const marker = lower.indexOf('affectation');
    if (marker === -1) {
        return { emplacement: text, affectation: '' };
    }

    const left = text.slice(0, marker).replace(/[,:\-]+$/g, '').trim();
    const right = text
        .slice(marker)
        .replace(/^affectation\s*[:\-]?\s*/i, '')
        .trim();

    return {
        emplacement: left || '',
        affectation: right || '',
    };
}

function normalizeText(value) {
    return value.replace(/\s+/g, ' ').trim();
}

function parseNumberPrefix(text) {
    const cleaned = normalizeText(text.toLowerCase());
    const direct = cleaned.match(/^(\d+)\s+(.+)$/);
    if (direct) {
        return { quantity: parseInt(direct[1], 10), designation: direct[2] };
    }

    const words = {
        un: 1,
        une: 1,
        deux: 2,
        trois: 3,
        quatre: 4,
        cinq: 5,
        six: 6,
        sept: 7,
        huit: 8,
        neuf: 9,
        dix: 10,
    };

    const word = cleaned.match(/^([a-zA-Z]+)\s+(.+)$/);
    if (word && words[word[1]] !== undefined) {
        return { quantity: words[word[1]], designation: word[2] };
    }

    return { quantity: 1, designation: cleaned };
}

function parseItem(transcript) {
    const raw = normalizeText(transcript || '');
    if (!raw) {
        return null;
    }

    const { quantity, designation } = parseNumberPrefix(raw);
    return {
        designation: designation.charAt(0).toUpperCase() + designation.slice(1),
        quantite: Math.max(1, quantity),
        etat: 'bon',
        observations: '',
        transcription_brute: raw,
        confiance: null,
    };
}

function renderQuickList() {
    if (AppState.items.length === 0) {
        els.quickList.innerHTML = '<p class="text-gray-500">Aucun bien ajoute.</p>';
        return;
    }

    els.quickList.innerHTML = AppState.items
        .map((item, index) => `<div>${index + 1}. ${item.quantite} x ${item.designation}</div>`)
        .join('');
}

function renderValidationTable() {
    els.validationBody.innerHTML = '';

    AppState.items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'border-b align-top';
        row.innerHTML = `
            <td class="py-2 pr-3">
                <input data-field="designation" data-index="${index}" class="w-56 border rounded px-2 py-1" value="${escapeHtml(item.designation)}">
            </td>
            <td class="py-2 pr-3">
                <input type="number" min="1" data-field="quantite" data-index="${index}" class="w-20 border rounded px-2 py-1" value="${item.quantite}">
            </td>
            <td class="py-2 pr-3">
                <select data-field="etat" data-index="${index}" class="border rounded px-2 py-1">
                    ${['neuf', 'bon', 'moyen', 'mauvais']
                        .map((e) => `<option value="${e}" ${item.etat === e ? 'selected' : ''}>${e}</option>`)
                        .join('')}
                </select>
            </td>
            <td class="py-2 pr-3">
                <input data-field="observations" data-index="${index}" class="w-64 border rounded px-2 py-1" value="${escapeHtml(item.observations || '')}">
            </td>
            <td class="py-2 pr-3">
                <button type="button" data-action="delete" data-index="${index}" class="text-red-600 hover:underline">Supprimer</button>
            </td>
        `;

        els.validationBody.appendChild(row);
    });
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function newUuid() {
    if (window.crypto && window.crypto.randomUUID) {
        return window.crypto.randomUUID();
    }
    // Fallback UUID v4
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}

function getSpeechRecognition() {
    return window.SpeechRecognition || window.webkitSpeechRecognition || null;
}

function startVoiceCapture(onResult) {
    const SpeechRecognition = getSpeechRecognition();
    if (!SpeechRecognition) {
        showError('Reconnaissance vocale non supportee sur ce navigateur.');
        return;
    }

    clearError();
    const recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onresult = (event) => {
        const text = event.results?.[0]?.[0]?.transcript || '';
        onResult(text);
    };

    recognition.onerror = (event) => {
        showError(`Erreur vocale: ${event.error || 'inconnue'}`);
    };

    recognition.start();
}

async function login(username, password) {
    const data = await apiRequest('/login', {
        method: 'POST',
        body: JSON.stringify({ users: username, mdp: password }),
    });

    AppState.token = data.token;
    AppState.user = data.user || null;

    localStorage.setItem(CONFIG.TOKEN_KEY, data.token);
    localStorage.setItem(CONFIG.USER_KEY, JSON.stringify(data.user || null));
}

function logout() {
    AppState.token = null;
    AppState.user = null;
    localStorage.removeItem(CONFIG.TOKEN_KEY);
    localStorage.removeItem(CONFIG.USER_KEY);
    setAuthenticatedUI(false);
}

async function submitLot() {
    clearError();
    els.resultBox.classList.add('hidden');

    const emplacementLabel = normalizeText(els.emplacementLabel.value || '');
    const affectationLabel = normalizeText(els.affectationLabel.value || '');

    if (!emplacementLabel || !affectationLabel) {
        showError('Emplacement et affectation sont obligatoires.');
        return;
    }

    const validItems = AppState.items.filter((it) => normalizeText(it.designation || '').length > 0);
    if (validItems.length === 0) {
        showError('Ajoute au moins un bien avant validation.');
        return;
    }

    const payload = {
        lot_uid: newUuid(),
        emplacement_label: emplacementLabel,
        affectation_label: affectationLabel,
        items: validItems.map((it) => ({
            designation: normalizeText(it.designation),
            quantite: Math.max(1, parseInt(it.quantite, 10) || 1),
            etat: it.etat || 'bon',
            observations: it.observations || null,
            transcription_brute: it.transcription_brute || null,
            confiance: it.confiance ?? null,
        })),
    };

    const response = await apiRequest('/collecte-initiale/enregistrer-lot', {
        method: 'POST',
        body: JSON.stringify(payload),
    });

    els.resultBox.classList.remove('hidden');
    els.resultBox.innerHTML = `
        <p class="font-semibold mb-1">Validation enregistree</p>
        <p>Lot: <span class="font-mono">${response.lot_uid}</span></p>
        <p>Items recus: ${response.resume?.items_recus ?? 0}</p>
        <p>Lignes enregistrees: ${response.resume?.lignes_enregistrees ?? 0}</p>
    `;

    AppState.items = [];
    els.itemTranscript.value = '';
    renderQuickList();
    renderValidationTable();
}

function bindEvents() {
    els.loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        els.loginError.classList.add('hidden');
        els.loginError.textContent = '';

        try {
            await login(els.loginUsers.value, els.loginMdp.value);
            setAuthenticatedUI(true);
        } catch (error) {
            els.loginError.textContent = error.message || 'Connexion impossible';
            els.loginError.classList.remove('hidden');
        }
    });

    els.logoutBtn.addEventListener('click', logout);

    els.voiceContextBtn.addEventListener('click', () => {
        startVoiceCapture((text) => {
            els.contexteTranscript.value = text;
            const parsed = parseContext(text);
            if (parsed) {
                els.emplacementLabel.value = parsed.emplacement;
                els.affectationLabel.value = parsed.affectation;
            }
        });
    });

    els.parseContextBtn.addEventListener('click', () => {
        const parsed = parseContext(els.contexteTranscript.value);
        if (!parsed) {
            showError('Transcription contexte vide.');
            return;
        }
        els.emplacementLabel.value = parsed.emplacement;
        els.affectationLabel.value = parsed.affectation;
        clearError();
    });

    els.voiceItemBtn.addEventListener('click', () => {
        startVoiceCapture((text) => {
            els.itemTranscript.value = text;
        });
    });

    els.addItemBtn.addEventListener('click', () => {
        const item = parseItem(els.itemTranscript.value);
        if (!item) {
            showError('Transcription bien vide.');
            return;
        }
        AppState.items.push(item);
        els.itemTranscript.value = '';
        clearError();
        renderQuickList();
    });

    els.showListBtn.addEventListener('click', () => {
        els.validationPanel.classList.remove('hidden');
        renderValidationTable();
    });

    els.hideListBtn.addEventListener('click', () => {
        els.validationPanel.classList.add('hidden');
    });

    els.addEmptyRowBtn.addEventListener('click', () => {
        AppState.items.push({
            designation: '',
            quantite: 1,
            etat: 'bon',
            observations: '',
            transcription_brute: null,
            confiance: null,
        });
        renderValidationTable();
        renderQuickList();
    });

    els.validationBody.addEventListener('input', (event) => {
        const target = event.target;
        const index = parseInt(target.dataset.index, 10);
        const field = target.dataset.field;
        if (Number.isNaN(index) || !field || !AppState.items[index]) {
            return;
        }

        let value = target.value;
        if (field === 'quantite') {
            value = Math.max(1, parseInt(value, 10) || 1);
        }
        AppState.items[index][field] = value;
        renderQuickList();
    });

    els.validationBody.addEventListener('click', (event) => {
        const target = event.target;
        if (target.dataset.action !== 'delete') {
            return;
        }
        const index = parseInt(target.dataset.index, 10);
        if (Number.isNaN(index)) {
            return;
        }
        AppState.items.splice(index, 1);
        renderValidationTable();
        renderQuickList();
    });

    els.submitLotBtn.addEventListener('click', async () => {
        try {
            await submitLot();
        } catch (error) {
            showError(error.message || 'Echec enregistrement lot');
        }
    });
}

function init() {
    AppState.token = localStorage.getItem(CONFIG.TOKEN_KEY);
    const userRaw = localStorage.getItem(CONFIG.USER_KEY);
    AppState.user = userRaw ? JSON.parse(userRaw) : null;

    setAuthenticatedUI(Boolean(AppState.token));
    bindEvents();
    renderQuickList();
}

document.addEventListener('DOMContentLoaded', init);
