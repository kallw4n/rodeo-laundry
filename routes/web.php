<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/riwayat', [App\Http\Controllers\OrderController::class, 'history'])->name('riwayat');
Route::get('/profil', [App\Http\Controllers\CustomerController::class, 'profile'])->name('profil');
Route::get('/laporan', [App\Http\Controllers\OrderController::class, 'report'])->name('laporan');
Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

Route::get('/', function () {
    return view('welcome');
});

// Semua parameter route dinamai "item" agar cocok dengan route-model binding di controller.
foreach ([
    'service-types' => ServiceTypeController::class,
    'customers' => CustomerController::class,
    'orders' => OrderController::class,
    'payments' => PaymentController::class,
    'deliveries' => DeliveryController::class,
] as $name => $controller) {
    Route::resource($name, $controller)->parameters([$name => 'item']);
}