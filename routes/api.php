<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgaveController;
use App\Http\Controllers\Api\CategoriaBlogController;
use App\Http\Controllers\Api\CategoriaInsigniaController;
use App\Http\Controllers\Api\CategoriaMezcalController;
use App\Http\Controllers\Api\CategoriaMisionController;
use App\Http\Controllers\Api\EtiquetaController;
use App\Http\Controllers\Api\InsigniaController;
use App\Http\Controllers\Api\MaestroController;
use App\Http\Controllers\Api\MarcaController;
use App\Http\Controllers\Api\MezcalController;
use App\Http\Controllers\Api\MisionController;
use App\Http\Controllers\Api\PalenqueController;
use App\Http\Controllers\Api\PublicacionController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\TipoElaboracionController;
use App\Http\Controllers\Api\TipoMaduracionController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\UserMisionController;
use App\Http\Controllers\Api\UserInsigniaController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas API

// Rutas para agaves
Route::apiResource('agaves', AgaveController::class);
Route::patch('agaves/{id}/partial', [AgaveController::class, 'updatePartial']);

Route::apiResource('categoria-blogs', CategoriaBlogController::class);

// Rutas para categorías de insignias
Route::apiResource('categoria-insignias', CategoriaInsigniaController::class);
Route::patch('categoria-insignias/{id}/partial', [CategoriaInsigniaController::class, 'updatePartial']);

// Rutas para categorías de mezcal
Route::apiResource('categoria-mezcales', CategoriaMezcalController::class);
Route::patch('categoria-mezcales/{id}/partial', [CategoriaMezcalController::class, 'updatePartial']);   

// Rutas para categorías de misiones
Route::apiResource('categoria-misiones', CategoriaMisionController::class);
Route::patch('categoria-misiones/{id}/partial', [CategoriaMisionController::class, 'updatePartial']);

Route::apiResource('etiquetas', EtiquetaController::class);

// Rutas para insignias
Route::apiResource('insignias', InsigniaController::class);
Route::patch('insignias/{id}/partial', [InsigniaController::class, 'updatePartial']);

// Rutas para maestros
Route::apiResource('maestros', MaestroController::class);
Route::patch('maestros/{id}/partial', [MaestroController::class, 'updatePartial']);

// Rutas para marcas
Route::apiResource('marcas', MarcaController::class);
Route::patch('marcas/{id}/partial', [MarcaController::class, 'updatePartial']);

// Rutas para mezcales
Route::apiResource('mezcales', MezcalController::class);
Route::patch('mezcales/{id}/partial', [MezcalController::class, 'updatePartial']);

// Rutas para misiones
Route::apiResource('misiones', MisionController::class);
Route::patch('misiones/{id}/partial', [MisionController::class, 'updatePartial']);

// Rutas para palenques
Route::apiResource('palenques', PalenqueController::class);
Route::patch('palenques/{id}/partial', [PalenqueController::class, 'updatePartial']);

Route::apiResource('publicaciones', PublicacionController::class);

// Rutas para regiones
Route::apiResource('regiones', RegionController::class);
Route::patch('regiones/{id}/partial', [RegionController::class, 'updatePartial']);

// Rutas para tipos de elaboración
Route::apiResource('tipo-elaboraciones', TipoElaboracionController::class);
Route::patch('tipo-elaboraciones/{id}/partial', [TipoElaboracionController::class, 'updatePartial']);

// Rutas para tipos de maduración
Route::apiResource('tipo-maduraciones', TipoMaduracionController::class);
Route::patch('tipo-maduraciones/{id}/partial', [TipoMaduracionController::class, 'updatePartial']);

// Rutas para manejo de imágenes
Route::post('images', [ImageController::class, 'store']);
Route::post('images/{id}', [ImageController::class, 'update']);
Route::delete('images/{id}', [ImageController::class, 'destroy']);

// Rutas para progreso de misiones
Route::prefix('user-misiones')->group(function () {
    Route::get('/', [UserMisionController::class, 'index']);
    Route::post('/', [UserMisionController::class, 'store']);
    Route::get('/{id}', [UserMisionController::class, 'show']);
    Route::post('/{id}/progress', [UserMisionController::class, 'updateProgress']);
    Route::post('/{id}/fail', [UserMisionController::class, 'markAsFailed']);
    Route::post('/{id}/restart', [UserMisionController::class, 'restart']);
    Route::delete('/{id}/abandon', [UserMisionController::class, 'abandon']);
});

// Rutas para progreso de insignias
Route::prefix('user-insignias')->group(function () {
    Route::get('/', [UserInsigniaController::class, 'index']);
    Route::post('/', [UserInsigniaController::class, 'store']);
    Route::get('/{id}', [UserInsigniaController::class, 'show']);
    Route::post('/{id}/progress', [UserInsigniaController::class, 'updateProgress']);
    Route::post('/award', [UserInsigniaController::class, 'award']);
    Route::delete('/{id}/revoke', [UserInsigniaController::class, 'revoke']);
});

// Rutas para usuarios
Route::prefix('users')->group(function () {
    // Rutas CRUD básicas
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    
    // Rutas de gamificación
    Route::post('/{id}/experience', [UserController::class, 'updateExperience']);
    Route::post('/{id}/coins', [UserController::class, 'updateCoins']);
    Route::post('/{id}/streak', [UserController::class, 'updateStreak']);
});
