<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    
    // Routes pour les commandes de l'utilisateur connecté
    Route::get('/user/orders', [OrderController::class, 'index']);
});

// Routes pour les produits
Route::apiResource('products', ProductController::class);

// Routes pour les commandes
Route::apiResource('orders', OrderController::class);

// Route spécifique pour créer une commande depuis le live
Route::post('orders/live', [OrderController::class, 'createLiveOrder']);

// Route pour obtenir les produits disponibles en live
Route::get('products/live', [ProductController::class, 'getLiveProducts']);

// Route pour mettre à jour le stock d'un produit
Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
