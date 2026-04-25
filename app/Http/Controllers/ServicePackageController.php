<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use App\Models\PackageItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServicePackageController extends Controller
{
    public function index()
    {
        $packages = ServicePackage::with('items')->orderBy('sort_order')->paginate(15);
        return view('service-packages.index', compact('packages'));
    }

    public function create() { return view('service-packages.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:one_time,subscription,bundle',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:once,monthly,quarterly,yearly',
            'duration_days' => 'nullable|integer|min:1',
            'max_visits' => 'nullable|integer|min:1',
            'allow_partial_payment' => 'nullable|boolean',
            'min_deposit_amount' => 'nullable|numeric|min:0',
            'min_deposit_percent' => 'nullable|numeric|min:0|max:100',
            'items' => 'nullable|array',
            'items.*.item_type' => 'required|in:consultation,lab,medicine,service',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_value' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $package = ServicePackage::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . uniqid(),
                'description' => $request->description,
                'type' => $request->type,
                'price' => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'duration_days' => $request->duration_days,
                'max_visits' => $request->max_visits,
                'allow_partial_payment' => $request->boolean('allow_partial_payment'),
                'min_deposit_amount' => $request->min_deposit_amount,
                'min_deposit_percent' => $request->min_deposit_percent,
                'is_active' => true,
            ]);

            foreach ($request->items ?? [] as $item) {
                PackageItem::create([
                    'package_id' => $package->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_value' => $item['unit_value'] ?? 0,
                ]);
            }
        });

        return redirect()->route('service-packages.index')->with('success', 'Package created.');
    }

    public function show(ServicePackage $servicePackage)
    {
        $servicePackage->load('items', 'subscriptions.patient');
        return view('service-packages.show', compact('servicePackage'));
    }

    public function destroy(ServicePackage $servicePackage)
    {
        $servicePackage->update(['is_active' => false]);
        return redirect()->route('service-packages.index')->with('success', 'Package deactivated.');
    }
}
