<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Branch;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $services = Service::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('category', 'like', "%{$s}%"))
            ->orderBy('category')->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('services.index', compact('services', 'search'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('services.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        $branches = Branch::where('is_active', true)->get();
        return view('services.edit', compact('service', 'branches'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted.');
    }
}
