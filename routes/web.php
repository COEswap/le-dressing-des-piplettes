<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;

// Routes API avec préfixe explicite
Route::prefix('api')->group(function () {
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
        
        // Routes d'administration protégées par le rôle admin
        Route::middleware('role:admin')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
            
            // Gestion des utilisateurs
            Route::get('/admin/users', [UserController::class, 'index']);
            Route::post('/admin/users', [UserController::class, 'store']); // Ajout de la route de création
            Route::get('/admin/users/{user}', [UserController::class, 'show']);
            Route::put('/admin/users/{user}', [UserController::class, 'update']);
            Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
            
            // Gestion des produits
            Route::get('/admin/products', [ProductController::class, 'adminIndex']);
            Route::post('/admin/products', [ProductController::class, 'store']);
            Route::get('/admin/products/{product}', [ProductController::class, 'show']);
            Route::put('/admin/products/{product}', [ProductController::class, 'update']);
            Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
            
            // Gestion des commandes
            Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
            Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
            Route::put('/admin/orders/{order}', [OrderController::class, 'adminUpdate']);
            
            // Gestion des catégories
            Route::get('/admin/categories', [CategoryController::class, 'index']);
            Route::post('/admin/categories', [CategoryController::class, 'store']);
            Route::put('/admin/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);
        });
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
});

// Routes Web pour servir l'application Vue.js
Route::get('/', function () {
    return view('app');
});

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');