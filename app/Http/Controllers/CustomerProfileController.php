<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
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

        return view('profile.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string',
            'city'    => 'required|string',
        ]);

        $customer = Customer::where('user_id', Auth::id())->first();
        
        $customer->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'city'    => $request->city,
        ]);

        return redirect()->back()->with('success', 'Profil dan alamat berhasil diperbarui!');
    }
}