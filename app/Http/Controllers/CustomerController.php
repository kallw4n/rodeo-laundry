<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
        ];
    }

    public function index()
    {
        return view('customers.index', ['items' => Customer::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        Customer::create($request->validate($this->rules()));
        return redirect()->route('customers.index')->with('success', 'Data disimpan.');
    }

    public function show(Customer $item)
    {
        return view('customers.show', ['item' => $item]);
    }

    public function edit(Customer $item)
    {
        return view('customers.edit', ['item' => $item]);
    }

    public function update(Request $request, Customer $item)
    {
        $item->update($request->validate($this->rules()));
        return redirect()->route('customers.index')->with('success', 'Data diperbarui.');
    }

    public function destroy(Customer $item)
    {
        $item->delete();
        return redirect()->route('customers.index')->with('success', 'Data dihapus.');
    }
}