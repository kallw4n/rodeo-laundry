<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
        ]);

        // Kalau ini alamat pertama, otomatis jadi alamat utama
        $isFirst = Address::where('user_id', Auth::id())->count() === 0;

        Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'is_default' => $isFirst,
        ]);

        return redirect()->back()->with('success', 'Alamat baru berhasil ditambahkan!');
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
        ]);

        $address->update([
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui!');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();
        return redirect()->back()->with('success', 'Alamat berhasil dihapus!');
    }
    
    public function setDefault(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return redirect()->back()->with('success', 'Alamat utama berhasil diubah!');
    }
}