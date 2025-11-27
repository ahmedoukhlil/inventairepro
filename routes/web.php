<?php

use App\Http\Controllers\BienController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\LocalisationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Redirection de la page d'accueil vers le login
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
|
| Les routes d'authentification (login, register, password reset, etc.)
| sont gérées par Laravel Breeze et se trouvent dans le fichier
| auth.php (si configuré) ou directement dans ce fichier.
|
*/

// Inclusion des routes d'authentification (si le fichier existe)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
|
| Toutes les routes ci-dessous nécessitent que l'utilisateur soit authentifié.
|
*/

Route::middleware(['auth', 'session.timeout'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard
    |----------------------------------------------------------------------
    |
    | Accessible à tous les utilisateurs authentifiés (admin et agent).
    |
    */
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Routes avec Middleware 'inventory' (Admin + Agent)
    |----------------------------------------------------------------------
    |
    | Ces routes sont accessibles aux administrateurs et aux agents.
    | Elles concernent la gestion des localisations, biens, inventaires
    | et rapports.
    |
    */
    Route::middleware(['inventory'])->group(function () {

        /*
        |------------------------------------------------------------------
        | Localisations
        |------------------------------------------------------------------
        |
        | Gestion des localisations (bureaux, ateliers, etc.)
        |
        */
        Route::prefix('localisations')->name('localisations.')->group(function () {
            // Liste des localisations
            Route::get('/', \App\Livewire\Localisations\ListeLocalisations::class)->name('index');
            
            // Export Excel des localisations (doit être avant les routes avec paramètres)
            Route::get('/export/excel', [LocalisationController::class, 'exportExcel'])->name('export-excel');
            
            // Impression en masse d'étiquettes
            Route::post('/imprimer-etiquettes', [LocalisationController::class, 'imprimerEtiquettes'])->name('imprimer-etiquettes');
            
            // Création d'une localisation
            Route::get('/create', \App\Livewire\Localisations\FormLocalisation::class)->name('create');
            
            // Génération du QR code d'une localisation
            Route::get('/{localisation}/qr-code', [LocalisationController::class, 'generateQRCode'])->name('qr-code');
            
            // Téléchargement de l'étiquette d'une localisation
            Route::get('/{localisation}/etiquette', [LocalisationController::class, 'downloadEtiquette'])->name('etiquette');
            
            // Édition d'une localisation
            Route::get('/{localisation}/edit', \App\Livewire\Localisations\FormLocalisation::class)->name('edit');
            
            // Détails d'une localisation (doit être en dernier car il capture toutes les routes)
            Route::get('/{localisation}', \App\Livewire\Localisations\DetailLocalisation::class)->name('show');
        });

        /*
        |------------------------------------------------------------------
        | Biens
        |------------------------------------------------------------------
        |
        | Gestion des biens (mobilier, informatique, véhicules, etc.)
        |
        */
        Route::prefix('biens')->name('biens.')->group(function () {
            // Liste des biens
            Route::get('/', \App\Livewire\Biens\ListeBiens::class)->name('index');
            
            // Création d'un bien
            Route::get('/create', \App\Livewire\Biens\FormBien::class)->name('create');
            
            // Édition d'un bien
            Route::get('/{bien}/edit', \App\Livewire\Biens\FormBien::class)->name('edit');
            
            // Détails d'un bien
            Route::get('/{bien}', \App\Livewire\Biens\DetailBien::class)->name('show');
            
            // Génération du QR code d'un bien
            Route::get('/{bien}/qr-code', [BienController::class, 'generateQRCode'])->name('qr-code');
            
            // Téléchargement de l'étiquette d'un bien
            Route::get('/{bien}/etiquette', [BienController::class, 'downloadEtiquette'])->name('etiquette');
            
            // Impression en masse d'étiquettes
            Route::post('/imprimer-etiquettes', [BienController::class, 'imprimerEtiquettes'])->name('imprimer-etiquettes');
            
            // Export Excel des biens
            Route::get('/export/excel', [BienController::class, 'exportExcel'])->name('export-excel');
            
            // Export PDF des biens
            Route::get('/export/pdf', [BienController::class, 'exportPDF'])->name('export-pdf');
        });

        /*
        |------------------------------------------------------------------
        | Inventaires
        |------------------------------------------------------------------
        |
        | Gestion des inventaires annuels
        |
        */
        Route::prefix('inventaires')->name('inventaires.')->group(function () {
            // Liste des inventaires
            Route::get('/', \App\Livewire\Inventaires\ListeInventaires::class)->name('index');
            
            // Création/démarrage d'un inventaire
            Route::get('/create', \App\Livewire\Inventaires\DemarrerInventaire::class)->name('create');
            
            // Dashboard d'un inventaire (vue principale)
            Route::get('/{inventaire}', \App\Livewire\Inventaires\DashboardInventaire::class)->name('show');
            
            // Rapport détaillé d'un inventaire
            Route::get('/{inventaire}/rapport', \App\Livewire\Inventaires\RapportInventaire::class)->name('rapport');
            
            // Export PDF d'un inventaire
            Route::get('/{inventaire}/export-pdf', [InventaireController::class, 'exportPDF'])->name('export-pdf');
            
            // Export Excel d'un inventaire
            Route::get('/{inventaire}/export-excel', [InventaireController::class, 'exportExcel'])->name('export-excel');
            
            // Clôture d'un inventaire
            Route::post('/{inventaire}/cloturer', [InventaireController::class, 'cloturer'])->name('cloturer');
        });

        /*
        |------------------------------------------------------------------
        | Rapports
        |------------------------------------------------------------------
        |
        | Génération de rapports personnalisés
        |
        */
        // Routes de rapports désactivées pour l'instant (composants à créer)
        // Route::prefix('rapports')->name('rapports.')->group(function () {
        //     // Page de génération de rapports
        //     Route::get('/', \App\Livewire\Rapports\GenerateurRapport::class)->name('index');
        //     
        //     // Génération d'un rapport
        //     Route::post('/generer', [\App\Http\Controllers\RapportController::class, 'generer'])->name('generer');
        // });
    });

    /*
    |----------------------------------------------------------------------
    | Routes avec Middleware 'admin' (Administrateur uniquement)
    |----------------------------------------------------------------------
    |
    | Ces routes sont accessibles uniquement aux administrateurs.
    |
    */
    Route::middleware(['admin'])->group(function () {

        /*
        |------------------------------------------------------------------
        | Utilisateurs
        |------------------------------------------------------------------
        |
        | Gestion des utilisateurs (création, modification, etc.)
        |
        */
        Route::prefix('users')->name('users.')->group(function () {
            // Liste des utilisateurs
            Route::get('/', \App\Livewire\Users\ListeUsers::class)->name('index');
            
            // Création d'un utilisateur
            Route::get('/create', \App\Livewire\Users\FormUser::class)->name('create');
            
            // Édition d'un utilisateur
            Route::get('/{user}/edit', \App\Livewire\Users\FormUser::class)->name('edit');
        });

        /*
        |------------------------------------------------------------------
        | Paramètres
        |------------------------------------------------------------------
        |
        | Configuration générale de l'application
        |
        */
        // Route::get('/settings', \App\Livewire\Settings\ParametresGeneraux::class)->name('settings.index');
    });
});
