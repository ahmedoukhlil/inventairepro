/* PWA Collecte Initiale - module autonome */

const CONFIG = {
    API_BASE_URL: `${window.location.origin}/api/v1`,
    TOKEN_KEY: 'inventaire_token_v2',
    USER_KEY: 'inventaire_user_v2',
    DRAFT_KEY: 'collecte_initiale_draft_v1',
    PENDING_KEY: 'collecte_initiale_pending_v1',
};

const AppState = {
    token: null,
    user: null,
    items: [],
};

const els = {
    viewLogin: document.getElementById('view-login'),
    viewApp: document.getElementById('view-app'),
    voiceSupportNotice: document.getElementById('voice-support-notice'),
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
    saveDraftBtn: document.getElementById('save-draft-btn'),
    resetDraftBtn: document.getElementById('reset-draft-btn'),
    itemTranscript: document.getElementById('item-transcript'),
    voiceItemBtn: document.getElementById('voice-item-btn'),
    addItemBtn: document.getElementById('add-item-btn'),
    addManyBtn: document.getElementById('add-many-btn'),
    showListBtn: document.getElementById('show-list-btn'),
    quickList: document.getElementById('quick-list'),
    statsLines: document.getElementById('stats-lines'),
    statsQty: document.getElementById('stats-qty'),
    validationPanel: document.getElementById('validation-panel'),
    validationBody: document.getElementById('validation-body'),
    addEmptyRowBtn: document.getElementById('add-empty-row-btn'),
    submitLotBtn: document.getElementById('submit-lot-btn'),
    hideListBtn: document.getElementById('hide-list-btn'),
    syncPendingBtn: document.getElementById('sync-pending-btn'),
    pendingCount: document.getElementById('pending-count'),
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

function showResult(messageHtml) {
    els.resultBox.innerHTML = messageHtml;
    els.resultBox.classList.remove('hidden');
}

function normalizeText(value) {
    return String(value || '').replace(/\s+/g, ' ').trim();
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
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
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

    let response;
    try {
        response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, { ...options, headers });
    } catch (_) {
        throw new Error('Reseau indisponible');
    }

    if (!response.ok) {
        let message = `Erreur HTTP ${response.status}`;
        try {
            const data = await response.json();
            message = data.message || message;
        } catch (_) {
            // ignore
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
    const text = normalizeText(transcript);
    if (!text) {
        return null;
    }

    const lower = text.toLowerCase();
    const marker = lower.indexOf('affectation');
    if (marker === -1) {
        return { emplacement: text, affectation: '' };
    }

    const left = normalizeText(text.slice(0, marker).replace(/[,:\-]+$/g, ''));
    const right = normalizeText(text.slice(marker).replace(/^affectation\s*[:\-]?\s*/i, ''));
    return { emplacement: left, affectation: right };
}

function parseNumberPrefix(text) {
    const cleaned = normalizeText(text.toLowerCase());
    const direct = cleaned.match(/^(\d+)\s+(.+)$/);
    if (direct) {
        return { quantity: parseInt(direct[1], 10), designation: direct[2] };
    }

    const words = {
        un: 1, une: 1, deux: 2, trois: 3, quatre: 4, cinq: 5,
        six: 6, sept: 7, huit: 8, neuf: 9, dix: 10,
    };
    const word = cleaned.match(/^([a-zA-Z]+)/);
    if (word && words[word[1]] !== undefined) {
        const designation = normalizeText(cleaned.slice(word[1].length));
        return { quantity: words[word[1]], designation };
    }

    return { quantity: 1, designation: cleaned };
}

function parseItem(transcript) {
    const raw = normalizeText(transcript);
    if (!raw) {
        return null;
    }
    const { quantity, designation } = parseNumberPrefix(raw);
    if (!designation) {
        return null;
    }
    return {
        designation: designation.charAt(0).toUpperCase() + designation.slice(1),
        quantite: Math.max(1, quantity),
        etat: 'bon',
        observations: '',
        transcription_brute: raw,
        confiance: null,
    };
}

function parseManyItems(transcript) {
    const text = normalizeText(transcript)
        .replace(/\s+et\s+/gi, ', ')
        .replace(/[;|]/g, ',');
    if (!text) {
        return [];
    }
    return text
        .split(',')
        .map((part) => parseItem(part))
        .filter(Boolean);
}

function getDraftPayload() {
    return {
        emplacement_label: normalizeText(els.emplacementLabel.value),
        affectation_label: normalizeText(els.affectationLabel.value),
        contexte_transcript: els.contexteTranscript.value || '',
        item_transcript: els.itemTranscript.value || '',
        items: AppState.items,
    };
}

function saveDraft() {
    localStorage.setItem(CONFIG.DRAFT_KEY, JSON.stringify(getDraftPayload()));
}

function loadDraft() {
    const raw = localStorage.getItem(CONFIG.DRAFT_KEY);
    if (!raw) {
        return;
    }
    try {
        const draft = JSON.parse(raw);
        els.emplacementLabel.value = draft.emplacement_label || '';
        els.affectationLabel.value = draft.affectation_label || '';
        els.contexteTranscript.value = draft.contexte_transcript || '';
        els.itemTranscript.value = draft.item_transcript || '';
        AppState.items = Array.isArray(draft.items) ? draft.items : [];
    } catch (_) {
        // ignore invalid draft
    }
}

function clearDraft() {
    localStorage.removeItem(CONFIG.DRAFT_KEY);
}

function readPendingLots() {
    const raw = localStorage.getItem(CONFIG.PENDING_KEY);
    if (!raw) {
        return [];
    }
    try {
        const value = JSON.parse(raw);
        return Array.isArray(value) ? value : [];
    } catch (_) {
        return [];
    }
}

function writePendingLots(value) {
    localStorage.setItem(CONFIG.PENDING_KEY, JSON.stringify(value));
}

function updatePendingCount() {
    const count = readPendingLots().length;
    els.pendingCount.textContent = String(count);
}

function queuePending(payload) {
    const pending = readPendingLots();
    pending.push({ payload, queued_at: new Date().toISOString() });
    writePendingLots(pending);
    updatePendingCount();
}

async function syncPendingLots() {
    const pending = readPendingLots();
    if (pending.length === 0) {
        showResult('<p class="font-semibold">Aucun lot en attente.</p>');
        return;
    }

    const failed = [];
    let success = 0;

    for (const item of pending) {
        try {
            await apiRequest('/collecte-initiale/enregistrer-lot', {
                method: 'POST',
                body: JSON.stringify(item.payload),
            });
            success += 1;
        } catch (_) {
            failed.push(item);
        }
    }

    writePendingLots(failed);
    updatePendingCount();
    showResult(`<p class="font-semibold">Synchronisation terminee</p><p>Succes: ${success}</p><p>Echecs: ${failed.length}</p>`);
}

function updateStats() {
    const lines = AppState.items.length;
    const qty = AppState.items.reduce((sum, item) => sum + (parseInt(item.quantite, 10) || 0), 0);
    els.statsLines.textContent = String(lines);
    els.statsQty.textContent = String(qty);
}

function renderQuickList() {
    if (AppState.items.length === 0) {
        els.quickList.innerHTML = '<p class="text-gray-500">Aucun bien ajoute.</p>';
        updateStats();
        return;
    }

    els.quickList.innerHTML = AppState.items
        .map((item, index) => `<div>${index + 1}. ${item.quantite} x ${escapeHtml(item.designation)}</div>`)
        .join('');

    updateStats();
}

function renderValidationTable() {
    els.validationBody.innerHTML = '';
    if (AppState.items.length === 0) {
        els.validationBody.innerHTML = '<tr><td class="py-3 text-gray-500" colspan="5">Aucun bien a valider.</td></tr>';
        return;
    }

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
                    ${['neuf', 'bon', 'moyen', 'mauvais'].map((e) => `<option value="${e}" ${item.etat === e ? 'selected' : ''}>${e}</option>`).join('')}
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

function getSpeechRecognition() {
    return window.SpeechRecognition || window.webkitSpeechRecognition || null;
}

function applyVoiceSupportUI() {
    const supported = Boolean(getSpeechRecognition());
    if (supported) {
        els.voiceSupportNotice.classList.add('hidden');
        els.voiceContextBtn.disabled = false;
        els.voiceItemBtn.disabled = false;
        els.voiceContextBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        els.voiceItemBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        return;
    }
    els.voiceSupportNotice.classList.remove('hidden');
    els.voiceContextBtn.disabled = true;
    els.voiceItemBtn.disabled = true;
    els.voiceContextBtn.classList.add('opacity-60', 'cursor-not-allowed');
    els.voiceItemBtn.classList.add('opacity-60', 'cursor-not-allowed');
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
    recognition.onresult = (event) => onResult(event.results?.[0]?.[0]?.transcript || '');
    recognition.onerror = (event) => showError(`Erreur vocale: ${event.error || 'inconnue'}`);
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

function buildPayload() {
    const emplacementLabel = normalizeText(els.emplacementLabel.value);
    const affectationLabel = normalizeText(els.affectationLabel.value);
    if (!emplacementLabel || !affectationLabel) {
        throw new Error('Emplacement et affectation sont obligatoires.');
    }
    const validItems = AppState.items.filter((it) => normalizeText(it.designation).length > 0);
    if (validItems.length === 0) {
        throw new Error('Ajoute au moins un bien avant validation.');
    }
    return {
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
}

async function submitLot() {
    clearError();
    els.resultBox.classList.add('hidden');
    const payload = buildPayload();

    els.submitLotBtn.disabled = true;
    els.submitLotBtn.textContent = 'Envoi en cours...';

    try {
        const response = await apiRequest('/collecte-initiale/enregistrer-lot', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        showResult(`
            <p class="font-semibold mb-1">Validation enregistree</p>
            <p>Lot: <span class="font-mono">${escapeHtml(response.lot_uid)}</span></p>
            <p>Items recus: ${response.resume?.items_recus ?? 0}</p>
            <p>Lignes enregistrees: ${response.resume?.lignes_enregistrees ?? 0}</p>
        `);
        AppState.items = [];
        els.itemTranscript.value = '';
        saveDraft();
        renderQuickList();
        renderValidationTable();
    } catch (error) {
        if (error.message.includes('Reseau')) {
            queuePending(payload);
            showResult('<p class="font-semibold">Reseau indisponible</p><p>Le lot a ete place en attente de synchronisation.</p>');
        } else {
            throw error;
        }
    } finally {
        els.submitLotBtn.disabled = false;
        els.submitLotBtn.textContent = "Valider l'emplacement";
    }
}

function bindEvents() {
    els.loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        els.loginError.classList.add('hidden');
        els.loginError.textContent = '';
        try {
            await login(els.loginUsers.value, els.loginMdp.value);
            setAuthenticatedUI(true);
            loadDraft();
            renderQuickList();
            renderValidationTable();
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
            saveDraft();
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
        saveDraft();
    });

    els.voiceItemBtn.addEventListener('click', () => {
        startVoiceCapture((text) => {
            els.itemTranscript.value = text;
            saveDraft();
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
        saveDraft();
        renderQuickList();
        renderValidationTable();
    });

    els.addManyBtn.addEventListener('click', () => {
        const items = parseManyItems(els.itemTranscript.value);
        if (items.length === 0) {
            showError('Aucun bien detecte dans la transcription.');
            return;
        }
        AppState.items.push(...items);
        els.itemTranscript.value = '';
        clearError();
        saveDraft();
        renderQuickList();
        renderValidationTable();
        showResult(`<p class="font-semibold">${items.length} ligne(s) ajoutee(s).</p>`);
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
        saveDraft();
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
        AppState.items[index][field] = field === 'quantite'
            ? Math.max(1, parseInt(target.value, 10) || 1)
            : target.value;
        saveDraft();
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
        saveDraft();
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

    els.syncPendingBtn.addEventListener('click', async () => {
        try {
            await syncPendingLots();
        } catch (error) {
            showError(error.message || 'Echec synchronisation');
        }
    });

    els.saveDraftBtn.addEventListener('click', () => {
        saveDraft();
        showResult('<p class="font-semibold">Brouillon sauvegarde.</p>');
    });

    els.resetDraftBtn.addEventListener('click', () => {
        if (!window.confirm('Reinitialiser le contexte et la liste des biens ?')) {
            return;
        }
        AppState.items = [];
        els.emplacementLabel.value = '';
        els.affectationLabel.value = '';
        els.contexteTranscript.value = '';
        els.itemTranscript.value = '';
        clearDraft();
        renderQuickList();
        renderValidationTable();
        showResult('<p class="font-semibold">Brouillon reinitialise.</p>');
    });

    // Autosave contexte
    [els.emplacementLabel, els.affectationLabel, els.contexteTranscript, els.itemTranscript].forEach((input) => {
        input.addEventListener('input', saveDraft);
    });
}

function init() {
    AppState.token = localStorage.getItem(CONFIG.TOKEN_KEY);
    const userRaw = localStorage.getItem(CONFIG.USER_KEY);
    AppState.user = userRaw ? JSON.parse(userRaw) : null;

    setAuthenticatedUI(Boolean(AppState.token));
    applyVoiceSupportUI();
    bindEvents();
    loadDraft();
    renderQuickList();
    renderValidationTable();
    updatePendingCount();
}

document.addEventListener('DOMContentLoaded', init);
