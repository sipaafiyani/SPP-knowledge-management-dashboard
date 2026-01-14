<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\VendorController;

/*
|--------------------------------------------------------------------------
| API Routes - Strategic KM Inventory Dashboard
|--------------------------------------------------------------------------
|
| Knowledge Access Layer - RESTful API untuk akses data inventaris
| Implementasi Knowledge Storage & Retrieval menggunakan HTTP protocol
|
*/

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Strategic KM Inventory API is running',
        'timestamp' => now()->toISOString()
    ]);
});

// Inventory Management Routes
Route::prefix('inventaris')->group(function () {
    Route::get('/', [InventoryController::class, 'index']); // GET all materials
    Route::post('/', [InventoryController::class, 'store']); // CREATE new material
    Route::get('/{id}', [InventoryController::class, 'show']); // GET single material
    Route::put('/{id}', [InventoryController::class, 'update']); // UPDATE material
    Route::delete('/{id}', [InventoryController::class, 'destroy']); // DELETE material
});

// Vendor Intelligence Routes (Knowledge-Based View)
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']); // GET all vendors
    Route::post('/', [VendorController::class, 'store']); // CREATE new vendor (Knowledge Storage)
    Route::get('/strategic-partners', [VendorController::class, 'strategicPartners']); // GET strategic partners only
    Route::get('/{id}', [VendorController::class, 'show']); // GET single vendor
    Route::put('/{id}', [VendorController::class, 'update']); // UPDATE vendor
    Route::delete('/{id}', [VendorController::class, 'destroy']); // DELETE vendor
});

// Alternative route naming (for consistency)
Route::apiResource('materials', InventoryController::class)->except(['create', 'edit']);

