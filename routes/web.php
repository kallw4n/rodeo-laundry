<?php

use App\Http\Controllers\OrderController;
use App\Models\ServiceType; // INI UNTUK PELANGGAN
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. ROUTE PUBLIK (Bisa diakses tanpa login)
// ==========================================
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

    $viewName = view()->exists('pelanggan.frontPage') ? 'pelanggan.frontPage' : 'frontPage';
    return view($viewName, compact('services'));
})->name('dashboard');


// ==========================================
// 2. ROUTE PELANGGAN (Wajib Login)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // ----------------------------------------
    // A. ALUR PEMESANAN (Pemesanan -> Pengiriman -> Pembayaran)
    // ----------------------------------------
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

        $viewName = view()->exists('pelanggan.pemesanan') ? 'pelanggan.pemesanan' : 'pemesanan';
        return view($viewName, [
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
        $customer = \App\Models\Customer::where('user_id', auth()->id())->first();
        
        // Ambil alamat dari tabel addresses untuk ditampilkan di halaman checkout
        $addresses = \App\Models\Address::where('user_id', auth()->id())->orderBy('is_default', 'desc')->get();

        $viewName = view()->exists('pelanggan.pengiriman') ? 'pelanggan.pengiriman' : 'pengiriman';
        return view($viewName, [
            'services'      => $otherServices,
            'kiloanService' => $kiloanService,
            'customer'      => $customer,
            'addresses'     => $addresses,
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

        $viewName = view()->exists('pelanggan.pembayaran') ? 'pelanggan.pembayaran' : 'pembayaran';
        return view($viewName, [
            'services'      => $otherServices,
            'kiloanService' => $kiloanService,
        ]);
    })->name('pembayaran');

    // Proses Checkout (Simpan pesanan)
    Route::post('/pesan', [OrderController::class, 'checkout'])->name('pesan.store');


    // ----------------------------------------
    // B. RIWAYAT PESANAN
    // ----------------------------------------
    Route::get('/orders', [OrderController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancelCustomerOrder'])->name('pesanan.cancel');
    Route::patch('/pesanan/{order}/cancel', [OrderController::class, 'cancelCustomerOrder']);


    // ----------------------------------------
    // C. PROFIL & ALAMAT PELANGGAN
    // ----------------------------------------
    // Profil Data Diri
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    
    // Kelola Alamat (Tambah, Hapus, Jadikan Utama)
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
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
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updateOrderStatus'])->name('orders.updateStatus');

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
