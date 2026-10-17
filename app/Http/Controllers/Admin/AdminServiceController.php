<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter kategori jika ada
        if ($request->filled('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->where('name', 'like', "%{$cat}%");
        }

        $sort = $request->get('sort', 'id_asc');
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $services = $query->paginate(10)->withQueryString();
        $totalServices = ServiceType::count();

        $latestService = ServiceType::latest('id')->first();
        $nextServiceId = $latestService ? ($latestService->id + 1) : 1;

        return view('admin.services.index', compact('services', 'totalServices', 'nextServiceId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        ServiceType::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan baru "' . $validated['name'] . '" berhasil ditambahkan!');
    }

    public function update(Request $request, ServiceType $service)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan "' . $service->name . '" berhasil diperbarui!');
    }

    public function destroy(ServiceType $service)
    {
        $name = $service->name;
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan "' . $name . '" berhasil dihapus.');
    }
}
