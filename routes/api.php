<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\PreferenceController;

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

Route::group(['middleware' => 'web'], function () {


    Route::auth();

    Route::apiResource('products', ProductController::class);
    Route::get('/products/export', [ProductController::class, 'export'])->name('api.products.export');
    Route::post('/products/import', [ProductController::class, 'import'])->name('api.products.import');
    Route::post('/products/truncate', [ProductController::class, 'truncate'])->name('api.products.truncate');
    Route::post('/products/delete/selected', [ProductController::class, 'deleteMultiple'])->name('api.products.deleteMultiple');
    Route::get('/preferences', [PreferenceController::class, 'index'])->name('api.preferences.show');
    Route::put('/preferences', [PreferenceController::class, 'update'])->name('api.preferences.save');
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('api.preferences.store');
    
});