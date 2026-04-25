<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $logo = Setting::get('clinic_logo');
        $clinicName = Setting::get('clinic_name', 'Clinic Management System');
        return view('settings.index', compact('logo', 'clinicName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'clinic_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        Setting::set('clinic_name', $request->clinic_name);

        if ($request->hasFile('clinic_logo')) {
            $oldLogo = Setting::get('clinic_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('clinic_logo')->store('settings', 'public');
            Setting::set('clinic_logo', $path);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function removeLogo()
    {
        $logo = Setting::get('clinic_logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }
        Setting::set('clinic_logo', null);

        return back()->with('success', 'Logo removed.');
    }
}
