<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter kota
        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        // Urutan
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        $customers = $query->paginate(10)->withQueryString();
        $totalCustomers = Customer::count();

        // Generate next suggested customer ID for modal preview
        $latestCustomer = Customer::latest('id')->first();
        $nextIdNumber = $latestCustomer ? (8920 + $latestCustomer->id) : 8921;
        $nextCustomerCode = '#RD-' . $nextIdNumber;

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'nextCustomerCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100',
            'phone'   => 'required|string|max:20',
            'city'    => 'required|string|max:50',
            'address' => 'required|string',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Pelanggan baru "' . $validated['name'] . '" berhasil ditambahkan!');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:100',
            'phone'   => 'required|string|max:20',
            'city'    => 'required|string|max:50',
            'address' => 'required|string',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan "' . $customer->name . '" berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan "' . $name . '" berhasil dihapus.');
    }
}
