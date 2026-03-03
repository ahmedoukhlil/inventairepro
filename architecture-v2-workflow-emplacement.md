# Architecture V2 — Workflow Emplacement (Collecte Initiale Autonome)
> PWA de collecte premiere saisie des biens, avec stockage dans une table dediee sans relation aux tables metier existantes.

---

## 1) Objectif

Ce document definit une architecture orientee collecte initiale, alignee a l'application actuelle.

Finalite produit:
- Permettre la collecte des donnees des biens pour la premiere fois.
- Capturer les donnees terrain emplacement par emplacement.
- Utiliser la voix pour accelerer la saisie terrain avec validation humaine.
- Stocker les donnees dans une table dediee autonome, sans relation avec les autres tables de la base.

Cas cible prioritaire:
- Entreprise sans donnees initiales de materiel.
- Creation de la base immobilisations par dictee vocale et validation agent.

---

## 1.1) Positionnement metier V1

### Mode unique V1 - Initialisation d'un parc vide
- Aucune immobilisation preexistante.
- L'agent dicte un emplacement, puis dicte les biens de cet emplacement.
- La PWA transcrit, propose un formulaire pre-rempli, l'agent valide.
- A la validation, la PWA enregistre un lot de collecte dans une table dediee.
- Puis passage a l'emplacement suivant.

Note de cadrage:
- Le mode "inventaire d'un parc existant" reste hors perimetre de cette V1.
- Il pourra etre reactive en V2 sans remettre en cause le module de collecte initiale.

---

## 2) Ce qui est deja en place dans l'app (reference)

### Workflow backend reel (non utilise par le module autonome)

1. Auth API via Sanctum (`POST /api/v1/login`).
2. Recuperation inventaire actif (`GET /api/v1/inventaires/current`).
3. Recuperation des localisations assignees (`GET /api/v1/inventaires/{inventaire}/mes-localisations`).
4. Demarrage localisation (`POST /api/v1/inventaires/{inventaire}/demarrer-localisation`).
5. Workflow par emplacement:
   - Charger les biens attendus: `GET /api/v1/emplacements/{idEmplacement}/biens`
   - Scanner un bien: `POST /api/v1/emplacements/{idEmplacement}/scan`
   - Cloturer emplacement + calcul ecarts: `POST /api/v1/emplacements/{idEmplacement}/terminer`

### Regles metier deja codees

- Le code-barres 128 correspond a `NumOrdre`.
- Un bien scanne hors emplacement est marque `deplace`.
- La reponse retourne `emplacement_initial` pour les biens deplaces.
- A la cloture, l'API calcule:
  - `biens_manquants`
  - `biens_en_trop` (deplaces)
  - `taux_conformite`

Note importante:
- Le module "collecte initiale autonome" ne depend pas de ce workflow ni de ces tables.
- Aucune cle etrangere n'est creee vers `gesimmo`, `emplacements`, `inventaire_localisations` ou `inventaire_scans`.

---

## 3) Changement de cap pour la PWA vocale

### Ancienne proposition (a eviter)

- Endpoints fictifs de type `/api/voice/*` et `/api/sessions/*`.
- Tables nouvelles (`inventory_sessions`) non presentes dans l'app.

### Nouvelle proposition (retenue)

- La voix orchestre un workflow de collecte dedie.
- Les APIs de collecte sont dediees au module autonome.
- La PWA ajoute seulement:
  - Capture vocale (Web Speech API).
  - Interpreteur d'intentions cote client.
  - Buffer local offline (IndexedDB).
  - Mode "initialisation" pour enregistrement en table dediee.

---

## 3.1) Workflow detaille du mode initialisation (nouveau)

```text
1) L'agent dicte le contexte: "Bureau DG, affectation Direction generale"
2) La PWA confirme emplacement + affectation reconnus
3) L'agent dicte les biens: "un bureau, 2 chaises, un ordinateur..."
4) La PWA construit une liste temporaire des biens dictes
5) L'agent clique "Afficher la liste" pour ouvrir l'ecran de validation
6) L'agent modifie/corrige les lignes si necessaire (designation, quantite, etat, observations)
7) L'agent clique "Valider l'emplacement" pour la sauvegarde en base
8) Passage a "emplacement suivant"
```

Exemple de sequence terrain:
- Contexte: "Bureau DG, affectation Direction generale"
- Biens: "un bureau", "deux chaises", "un ordinateur portable"
- Action UI: bouton "Afficher la liste"
- Validation: edition manuelle puis bouton "Valider l'emplacement"

### 3.1.1 Champs saisis par voix (alignes formulaire immo)

Champs attendus a minima:
- designation (obligatoire)
- categorie (derivee par designation si possible, sinon selection)
- etat (obligatoire)
- emplacement (heritage de la session emplacement)
- annee acquisition (optionnel)
- quantite (defaut = 1)

Champs non obligatoires en mode initialisation:
- source de financement
- propriete / nature juridique

### 3.1.2 Validation agent avant creation

Avant envoi, la PWA affiche une liste editable:
- transcription brute
- contexte extrait (emplacement + affectation)
- lignes biens extraites (designation + quantite)
- niveau de confiance
- alertes sur champs manquants

L'agent peut:
- corriger un champ de contexte (emplacement, affectation)
- corriger une ligne bien (designation, quantite, etat, commentaire)
- supprimer une ligne erronee
- ajouter une ligne manuellement
- valider et creer

---

## 4) Workflow cible PWA (collecte initiale V1)

```text
Connexion
      -> saisir/dicter emplacement + affectation
      -> boucle creation vocale (collecte initiale)
      -> valider emplacement (enregistrement lot)
      -> recap enregistrement (nombre accepte / rejete)
```

### Etape A — Connexion et contexte

1. `POST /api/v1/login` -> token Sanctum.
2. Ouverture ecran "collecte initiale autonome".
3. Aucun chargement obligatoire des tables metier existantes.

### Etape B — Ouverture d'un emplacement

1. L'agent saisit ou dicte l'emplacement et l'affectation.
2. PWA confirme le contexte reconnu avant la collecte des biens.
3. L'UI affiche:
   - Emplacement
   - Affectation
   - Localisation (optionnel, texte libre)
   - Indicateur "collecte initiale"

### Etape C — Boucle creation vocale (mode unique V1)

Pour chaque bien dicte:
1. L'agent dicte un item simple: "un bureau", "2 chaises", "un ordinateur".
2. La PWA transcrit et parse automatiquement designation + quantite.
3. La PWA ajoute la ligne a la liste locale `biens_a_creer`.
4. L'agent continue la dictee jusqu'a terminer l'emplacement.

Commande UI de fin:
- bouton "Afficher la liste" -> ouverture de l'ecran de validation modifiable.

### Etape D — Validation emplacement

1. L'agent controle la liste des biens dictes dans l'ecran de validation.
2. L'agent modifie les lignes necessaires.
3. L'agent clique "Valider l'emplacement".
4. La PWA envoie la liste complete:
   - `POST /api/v1/collecte-initiale/enregistrer-lot`
5. Backend:
   - valide les items
   - enregistre les lignes dans la table dediee
   - retourne le detail cree / rejete
6. L'UI affiche le recap:
   - nombre d'items recus
   - nombre de lignes enregistrees
   - rejets et motifs de rejet

---

## 5) Contrats API utilises (collecte initiale)

### 5.1 Besoin API principal (a ajouter)

Pour supporter la premiere collecte des biens, ajouter un endpoint dedie:

`POST /api/v1/collecte-initiale/enregistrer-lot`

Payload propose:

```json
{
  "lot_uid": "a0f3c2b1-9e7f-4e09-9f3e-8823456c1001",
  "emplacement_label": "Bureau DG",
  "affectation_label": "Direction generale",
  "localisation_label": "Siege",
  "items": [
    {
      "designation": "Bureau",
      "quantite": 2,
      "etat": "bon",
      "date_acquisition": 2024,
      "observations": "dicte vocalement"
    }
  ]
}
```

Traitement backend:
- validation batch
- insertion des lignes dans la table dediee
- retour detail des lignes creees/rejetees

---

## 5.2 Contrainte technique importante (etat actuel)

Regle d'isolation du module:
- une table dediee unique pour la collecte initiale, sans FK vers les tables existantes
- les valeurs emplacement/affectation/localisation sont stockees en texte
- aucune dependance aux contraintes `idNatJur` / `idSF` des formulaires existants
- aucun impact schema sur `gesimmo`, `emplacements`, `inventaire_localisations`, `inventaire_scans`

---

Reponse cle:

```json
{
  "lot_uid": "a0f3c2b1-9e7f-4e09-9f3e-8823456c1001",
  "emplacement_label": "Bureau DG",
  "affectation_label": "Direction generale",
  "resume": {
    "items_recus": 12,
    "lignes_enregistrees": 11,
    "items_rejetes": 1
  },
  "items_rejetes": [
    {
      "index": 4,
      "motif": "designation vide"
    }
  ],
  "ids_collecte_crees": [901, 902, 903]
}
```

---

## 6) Reconnaissance vocale: role exact dans la PWA

La voix assiste l'operateur pour la creation des fiches biens.

### Intentions vocales minimales (V1 collecte)

- "etat bon/moyen/mauvais/neuf"
- "ajouter commentaire {texte}"
- "prendre photo"
- "supprimer dernier bien"
- "valider emplacement"
- "bien suivant"

### Exemple de moteur d'intentions cote client

```ts
type VoiceIntent =
  | { type: "SET_ETAT"; value: "neuf" | "bon" | "moyen" | "mauvais" }
  | { type: "ADD_COMMENT"; value: string }
  | { type: "TAKE_PHOTO" }
  | { type: "REMOVE_LAST_ITEM" }
  | { type: "NEXT_ITEM" }
  | { type: "VALIDATE_EMPLACEMENT" }
  | { type: "UNKNOWN"; raw: string };
```

---

## 7) Mode offline (module autonome)

### Strategie

1. Stocker localement:
   - contexte emplacement
   - liste `biens_a_creer`
   - photos compressees
2. Si reseau KO:
   - continuer la saisie locale
   - marquer la session locale `pending_sync`
3. Au retour reseau:
   - envoi final `POST /collecte-initiale/enregistrer-lot`
4. Eviter les doublons:
   - cle locale: `lot_uid + hash(item)`

### Regle de coherence

- Le backend reste source de verite.
- La PWA affiche "synchro en attente" tant que la validation emplacement n'est pas acceptee.

---

## 8) Mapping donnees (noms reels de l'app)

### Entites principales

Table dediee proposee: `collecte_biens_initiale`
- `id` (PK auto increment)
- `lot_uid` (uuid du lot de validation)
- `emplacement_label` (texte libre)
- `affectation_label` (texte libre)
- `localisation_label` (texte libre nullable)
- `designation` (texte)
- `quantite` (entier)
- `etat` (texte nullable)
- `date_acquisition` (entier nullable)
- `observations` (texte nullable)
- `transcription_brute` (texte nullable)
- `confiance` (decimal nullable)
- `agent_label` (texte nullable)
- `created_at` / `updated_at`

Regle d'architecture:
- aucune relation (FK) avec les autres tables de la base
- toutes les donnees de contexte sont portees par la table dediee

### Convention front

- `lot_uid` identifie un envoi de validation emplacement
- chaque ligne item est autoportee (pas de reference a une table externe)

---

## 9) Plan d'implementation PWA vocale (collecte initiale)

### Lot 1 — Socle collecte initiale

- Ecrans:
  - login
  - collecte initiale (emplacement + affectation)
  - creation biens par emplacement
  - recap creations
- Brancher endpoint dedie `collecte-initiale/enregistrer-lot`.

### Lot 2 — Commandes vocales operationnelles

- Integrer SpeechRecognition web.
- Mapper intentions vocales sur actions UI.
- Ajouter confirmations vocales/visuelles.
- Ajouter parser "creation immo" (designation, etat, quantite, annee).

### Lot 3 — Offline + sync robuste

- IndexedDB + queue de sync.
- Retry exponentiel.
- Gestion des conflits et doublons.
- Support queue `biens_a_creer`.

### Lot 4 — Optimisation terrain

- Raccourcis vocaux personnalises.
- Mode mains libres.
- Journal d'audit (qui a dit quoi / quand) si necessaire.
- Tableau de validation rapide avant "emplacement suivant".

---

## 10) KPI proposes

- Temps moyen de traitement par emplacement.
- Nombre moyen de biens crees par emplacement.
- Taux d'usage des commandes vocales.
- Taux de validations offline puis synchro reussie.
- Taux d'erreur de reconnaissance (intention inconnue).

---

## 11) Decision architecture (recommandee)

**Construire la V1 autour de la collecte initiale des biens et ajouter la voix cote PWA** est l'option la plus sure:
- impact faible sur l'existant,
- delai plus court,
- risque de regression limite,
- adoption terrain progressive.

---

*Version alignee Gesimmos — collecte initiale autonome des biens + assistance vocale PWA.*
