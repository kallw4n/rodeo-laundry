<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Address; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Support\Facades\Auth;

class CustomerProfileController extends Controller
{
    public function edit()
    {
        // Ambil data customer, kalau belum ada (user baru), otomatis buatkan data kosong
        $customer = Customer::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'name'    => Auth::user()->username,
                'email'   => Auth::user()->email,
                'phone'   => '',
                'address' => '',
                'city'    => '',
            ]
        );

        $addresses = Address::where('user_id', Auth::id())->orderBy('is_default', 'desc')->get();
        return view('profile.edit', compact('customer', 'addresses'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        
        $customer->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Data diri berhasil diperbarui!');
    }
}
