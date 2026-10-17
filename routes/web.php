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

// ==========================================
// ADMIN PORTAL & POS ROUTES (ROLE: ADMIN)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // 1. Dashboard Overview
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. POS / Kasir
    Route::get('/pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos');
    Route::post('/pos/order', [\App\Http\Controllers\Admin\PosController::class, 'storeOrder'])->name('pos.order');

    // 3. Kelola Pelanggan
    Route::get('/customers', [\App\Http\Controllers\Admin\AdminCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [\App\Http\Controllers\Admin\AdminCustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{customer}', [\App\Http\Controllers\Admin\AdminCustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [\App\Http\Controllers\Admin\AdminCustomerController::class, 'destroy'])->name('customers.destroy');

    // 4. Kelola Layanan
    Route::get('/services', [\App\Http\Controllers\Admin\AdminServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [\App\Http\Controllers\Admin\AdminServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'destroy'])->name('services.destroy');

    // 5. Kelola Pembayaran
    Route::get('/payments', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'destroy'])->name('payments.destroy');

    // 6. Kelola Pengiriman
    Route::get('/deliveries', [\App\Http\Controllers\Admin\AdminDeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('/deliveries', [\App\Http\Controllers\Admin\AdminDeliveryController::class, 'store'])->name('deliveries.store');
    Route::put('/deliveries/{delivery}', [\App\Http\Controllers\Admin\AdminDeliveryController::class, 'update'])->name('deliveries.update');
    Route::patch('/deliveries/{delivery}/status', [\App\Http\Controllers\Admin\AdminDeliveryController::class, 'updateStatus'])->name('deliveries.updateStatus');
    Route::delete('/deliveries/{delivery}', [\App\Http\Controllers\Admin\AdminDeliveryController::class, 'destroy'])->name('deliveries.destroy');
});

require __DIR__.'/auth.php';
