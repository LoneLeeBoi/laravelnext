<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // Import your AuthController
use App\Http\Controllers\UserController; // Import your UserController
use App\Http\Controllers\ProductController; // Import your ProductController

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

// Public routes for authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes for products (no authentication required)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Protected routes that require Sanctum authentication
Route::middleware('auth:sanctum')->group(function () {
    // Route to get the authenticated user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // You might add a logout route here if your frontend needs to trigger server-side token invalidation
    Route::post('/logout', [AuthController::class, 'logout']);

    // User profile routes
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'updateProfile']);

    // Admin routes
    Route::get('/admin/users', [UserController::class, 'listUsers']);
    Route::delete('/admin/users/{user}', [UserController::class, 'deleteUser']);
    Route::put('/admin/users/{user}', [UserController::class, 'update']);

    // Admin Product routes (Resource routes for CRUD)
    Route::resource('admin/products', ProductController::class)->except(['create', 'edit']); // We typically don't need create/edit views for an API

    // Add your other protected API routes here, e.g., for Product or Admin User CRUD
    // Route::apiResource('products', ProductController::class);
    // Route::apiResource('admin/users', AdminUserController::class)->middleware('can:manage-users'); // Example with authorization
});
