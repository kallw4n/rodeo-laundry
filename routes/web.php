<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceTypeController;
use App\Models\ServiceType; // INI UNTUK PELANGGAN
use Illuminate\Support\Facades\Route;

Route::get('/riwayat', [App\Http\Controllers\OrderController::class, 'history'])->name('riwayat');
Route::get('/profil', [App\Http\Controllers\CustomerController::class, 'profile'])->name('profil');
Route::get('/laporan', [App\Http\Controllers\OrderController::class, 'report'])->name('laporan');
Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

//INI UNTUK PAGE NYA PELANGGAN
Route::get('/', function () {
    $services = ServiceType::take(6)->get(); 
    return view('frontPage', compact('services'));
});
Route::get('/pemesanan', function (\Illuminate\Http\Request $request) {
    $preselectId = $request->query('service');
    $preselectName = null;
    
    if ($preselectId) {
        $found = \App\Models\ServiceType::find($preselectId);
        if ($found) $preselectName = $found->name;
    }
    
    $services = \App\Models\ServiceType::take(6)->get()
        ->filter(fn($s) => !str_contains(strtolower($s->name), 'kiloan')) // Filter kiloan, karena sudah hardcode
        ->map(function ($s) {
            $unit = 'pcs';
            if (str_contains(strtolower($s->name), 'sepatu')) $unit = 'psg';
            elseif (str_contains(strtolower($s->name), 'gorden')) $unit = 'meter';
            
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        })->values();
    
    return view('pemesanan', [
        'services'      => $services,
        'preselectId'   => $preselectId,
        'preselectName' => $preselectName,
    ]);
})->name('pemesanan');

Route::get('/pengiriman', function () {
    $services = \App\Models\ServiceType::take(6)->get()
        ->filter(fn($s) => !str_contains(strtolower($s->name), 'kiloan'))
        ->map(function ($s) {
            $unit = 'pcs';
            if (str_contains(strtolower($s->name), 'sepatu')) $unit = 'psg';
            elseif (str_contains(strtolower($s->name), 'gorden')) $unit = 'meter';
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        })->values();

    return view('pengiriman', compact('services'));
})->name('pengiriman');
// INI MASIH UHHH DATA DUMMY
Route::get('/orders', function () {
    return view('pesanan');
})->name('pesanan.index');

Route::get('/pembayaran', function () {
    $services = \App\Models\ServiceType::take(6)->get()
        ->filter(fn($s) => !str_contains(strtolower($s->name), 'kiloan'))
        ->map(function ($s) {
            $unit = 'pcs';
            if (str_contains(strtolower($s->name), 'sepatu')) $unit = 'psg';
            elseif (str_contains(strtolower($s->name), 'gorden')) $unit = 'meter';
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        })->values();

    return view('pembayaran', compact('services'));
})->name('pembayaran');

Route::get('/pesanan', function () {
    return view('pesanan');
})->name('pesanan');

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