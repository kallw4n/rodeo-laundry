<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }

    public function index()
    {
        return view('service-types.index', ['items' => ServiceType::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('service-types.create');
    }

    public function store(Request $request)
    {
        ServiceType::create($request->validate($this->rules()));
        return redirect()->route('service-types.index')->with('success', 'Data disimpan.');
    }

    public function show(ServiceType $item)
    {
        return view('service-types.show', ['item' => $item]);
    }

    public function edit(ServiceType $item)
    {
        return view('service-types.edit', ['item' => $item]);
    }

    public function update(Request $request, ServiceType $item)
    {
        $item->update($request->validate($this->rules()));
        return redirect()->route('service-types.index')->with('success', 'Data diperbarui.');
    }

    public function destroy(ServiceType $item)
    {
        $item->delete();
        return redirect()->route('service-types.index')->with('success', 'Data dihapus.');
    }
}