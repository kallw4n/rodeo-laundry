<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\ExpenseController;
use App\Models\ServiceType; // INI UNTUK PELANGGAN
use Illuminate\Support\Facades\Route;

Route::get('/riwayat', [OrderController::class, 'history'])->name('riwayat');
Route::get('/profil', [CustomerController::class, 'profile'])->name('profil');
Route::get('/laporan', [OrderController::class, 'report'])->name('laporan');
Route::resource('expenses', ExpenseController::class);

//INI UNTUK PAGE NYA PELANGGAN
Route::get('/', function () {
    $services = ServiceType::take(6)->get()->map(function ($s) {
        $name = strtolower($s->name);
        $unit = 'pcs';
        if (str_contains($name, 'kiloan')) $unit = 'kg';
        elseif (str_contains($name, 'sepatu')) $unit = 'psg';
        elseif (str_contains($name, 'gorden')) $unit = 'meter';

        return (object) [
            'id'          => $s->id,
            'name'        => $s->name,
            'price'       => (int) $s->price,
            'description' => $s->description,
            'unit'        => $unit,
        ];
    });

    return view('frontPage', compact('services'));
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/pemesanan', function (\Illuminate\Http\Request $request) {
        $preselectId = $request->query('service');
        $preselectName = null;

        if ($preselectId) {
            $found = ServiceType::find($preselectId);
            if ($found) $preselectName = $found->name;
        }

        $allServices = ServiceType::all()->map(function ($s) {
            $name = strtolower($s->name);
            $unit = 'pcs';
            if (str_contains($name, 'kiloan')) $unit = 'kg';
            elseif (str_contains($name, 'sepatu')) $unit = 'psg';
            elseif (str_contains($name, 'gorden')) $unit = 'meter';
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        });

        $kiloanService = $allServices->firstWhere('unit', 'kg');
        $otherServices = $allServices->where('unit', '!=', 'kg')->values();

        return view('pemesanan', [
            'services'      => $otherServices,
            'kiloanService' => $kiloanService,
            'preselectId'   => $preselectId,
            'preselectName' => $preselectName,
        ]);
    })->name('pemesanan');

    Route::get('/pengiriman', function () {
        $allServices = ServiceType::all()->map(function ($s) {
            $name = strtolower($s->name);
            $unit = 'pcs';
            if (str_contains($name, 'kiloan')) $unit = 'kg';
            elseif (str_contains($name, 'sepatu')) $unit = 'psg';
            elseif (str_contains($name, 'gorden')) $unit = 'meter';

            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        });

        $kiloanService = $allServices->firstWhere('unit', 'kg');
        $otherServices = $allServices->where('unit', '!=', 'kg')->values();

        return view('pengiriman', [
            'services'      => $otherServices,
            'kiloanService' => $kiloanService,
        ]);
    })->name('pengiriman');

    Route::get('/pembayaran', function () {
        $allServices = ServiceType::all()->map(function ($s) {
            $name = strtolower($s->name);
            $unit = 'pcs';
            if (str_contains($name, 'kiloan')) $unit = 'kg';
            elseif (str_contains($name, 'sepatu')) $unit = 'psg';
            elseif (str_contains($name, 'gorden')) $unit = 'meter';

            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'price'       => (int) $s->price,
                'description' => $s->description,
                'unit'        => $unit,
                'tag'         => 'Layanan',
            ];
        });

        $kiloanService = $allServices->firstWhere('unit', 'kg');
        $otherServices = $allServices->where('unit', '!=', 'kg')->values();

        return view('pembayaran', [
            'services'      => $otherServices,
            'kiloanService' => $kiloanService,
        ]);
    })->name('pembayaran');

    Route::get('/orders', function () {
        return view('pesanan');
    })->name('pesanan.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
