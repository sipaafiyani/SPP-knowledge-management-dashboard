<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - Strategic KM Inventory Dashboard
|--------------------------------------------------------------------------
|
| Knowledge Access Layer - RESTful API untuk akses data inventaris
| Implementasi Knowledge Storage & Retrieval menggunakan HTTP protocol
| 
| Protected Routes: Menggunakan Sanctum auth untuk distribusi pengetahuan
| berdasarkan role (Admin, Manager, Staff)
|
*/

// Health Check (Public)
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Strategic KM Inventory API is running',
        'timestamp' => now()->toISOString()
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']); // Optional
});

/*
|--------------------------------------------------------------------------
| Protected Routes - Require Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth User Management
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Inventory Management Routes (Staff, Manager, Admin)
    Route::prefix('inventaris')->group(function () {
        Route::get('/', [InventoryController::class, 'index']); // GET all materials
        Route::post('/', [InventoryController::class, 'store']); // CREATE new material
        Route::get('/{id}', [InventoryController::class, 'show']); // GET single material
        Route::put('/{id}', [InventoryController::class, 'update']); // UPDATE material
        Route::delete('/{id}', [InventoryController::class, 'destroy']); // DELETE material (Manager/Admin only)
    });

    // Vendor Intelligence Routes - Knowledge-Based View (Manager, Admin only)
    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index']); // GET all vendors
        Route::post('/', [VendorController::class, 'store']); // CREATE new vendor
        Route::get('/strategic-partners', [VendorController::class, 'strategicPartners']); // GET strategic partners
        Route::get('/{id}', [VendorController::class, 'show']); // GET single vendor
        Route::put('/{id}', [VendorController::class, 'update']); // UPDATE vendor
        Route::delete('/{id}', [VendorController::class, 'destroy']); // DELETE vendor
    });
    
    // Alternative resource routes
    Route::apiResource('materials', InventoryController::class)->except(['create', 'edit']);
});

/*
|--------------------------------------------------------------------------
| Public Routes (Demo Mode Fallback)
|--------------------------------------------------------------------------
*/

