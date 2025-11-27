<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BienController;
use App\Http\Controllers\Api\InventaireController;
use App\Http\Controllers\Api\LocalisationController;
use App\Http\Controllers\Api\ScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes pour Progressive Web App (PWA)
|--------------------------------------------------------------------------
|
| Ces routes sont utilisées par l'application mobile PWA pour scanner
| les QR codes et effectuer les inventaires.
|
| Toutes les routes sont préfixées par /api/v1
|
| NOTE IMPORTANTE: Les contrôleurs API doivent être créés dans 
| app/Http/Controllers/Api/ avant d'activer ces routes.
|
*/

Route::prefix('v1')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Authentification API
    |----------------------------------------------------------------------
    |
    | Routes pour l'authentification via l'API (utilise Sanctum)
    |
    */
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('api.logout');

    /*
    |----------------------------------------------------------------------
    | Routes Protégées (nécessitent authentification Sanctum)
    |----------------------------------------------------------------------
    |
    | Toutes les routes ci-dessous nécessitent un token d'authentification
    | valide (Sanctum).
    |
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |------------------------------------------------------------------
        | Inventaires API
        |------------------------------------------------------------------
        |
        | Routes pour gérer les inventaires depuis l'application mobile
        |
        */
        Route::prefix('inventaires')->name('api.inventaires.')->group(function () {
            // Récupérer l'inventaire en cours pour l'utilisateur
            Route::get('/current', [InventaireController::class, 'current'])->name('current');
            
            // Récupérer les localisations assignées à l'utilisateur pour un inventaire
            Route::get('/{inventaire}/mes-localisations', [InventaireController::class, 'mesLocalisations'])
                ->name('mes-localisations');
            
            // Démarrer le scan d'une localisation
            Route::post('/{inventaire}/demarrer-localisation', [InventaireController::class, 'demarrerLocalisation'])
                ->name('demarrer-localisation');
            
            // Terminer le scan d'une localisation
            Route::post('/{inventaire}/terminer-localisation', [InventaireController::class, 'terminerLocalisation'])
                ->name('terminer-localisation');
            
            // Statistiques d'un inventaire
            Route::get('/{inventaire}/stats', [InventaireController::class, 'stats'])
                ->name('stats');
        });

        /*
        |------------------------------------------------------------------
        | Localisations API
        |------------------------------------------------------------------
        |
        | Routes pour récupérer les informations des localisations
        |
        */
        Route::prefix('localisations')->name('api.localisations.')->group(function () {
            // Récupérer une localisation par son code QR
            Route::get('/by-code/{code}', [LocalisationController::class, 'byCode'])
                ->name('by-code');
            
            // Récupérer tous les biens d'une localisation
            Route::get('/{localisation}/biens', [LocalisationController::class, 'biens'])
                ->name('biens');
        });

        /*
        |------------------------------------------------------------------
        | Biens API
        |------------------------------------------------------------------
        |
        | Routes pour récupérer les informations des biens
        |
        */
        Route::prefix('biens')->name('api.biens.')->group(function () {
            // Détails d'un bien
            Route::get('/{bien}', [BienController::class, 'show'])
                ->name('show');
        });

        /*
        |------------------------------------------------------------------
        | Scans API
        |------------------------------------------------------------------
        |
        | Routes pour enregistrer les scans depuis l'application mobile
        |
        */
        Route::prefix('inventaires')->name('api.scans.')->group(function () {
            // Enregistrer un scan de bien
            Route::post('/{inventaire}/scan', [ScanController::class, 'store'])
                ->name('store');
        });
    });
});
