<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $data = [
            'logo' => Setting::get('clinic_logo'),
            'clinicName' => Setting::get('clinic_name', 'Clinic Management System'),
            // OnSend.io (dedicated section)
            'onsend_enabled' => Setting::get('onsend_enabled', '0'),
            'onsend_token' => Setting::get('onsend_token'),
            'onsend_endpoint' => Setting::get('onsend_endpoint'),
            // WhatsApp other providers
            'whatsapp_enabled' => Setting::get('whatsapp_enabled', '0'),
            'whatsapp_provider' => Setting::get('whatsapp_provider', 'cloud_api'),
            'whatsapp_token' => Setting::get('whatsapp_token'),
            'whatsapp_phone_id' => Setting::get('whatsapp_phone_id'),
            'whatsapp_endpoint' => Setting::get('whatsapp_endpoint'),
            // Billplz
            'billplz_enabled' => Setting::get('billplz_enabled', '0'),
            'billplz_sandbox' => Setting::get('billplz_sandbox', '1'),
            'billplz_api_key' => Setting::get('billplz_api_key'),
            'billplz_collection_id' => Setting::get('billplz_collection_id'),
            'billplz_x_signature' => Setting::get('billplz_x_signature'),
        ];
        return view('settings.index', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'clinic_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'whatsapp_provider' => 'nullable|in:cloud_api,fonnte,wassenger',
            'whatsapp_token' => 'nullable|string|max:500',
            'whatsapp_phone_id' => 'nullable|string|max:100',
            'whatsapp_endpoint' => 'nullable|url|max:500',
            'onsend_token' => 'nullable|string|max:500',
            'onsend_endpoint' => 'nullable|url|max:500',
            'billplz_api_key' => 'nullable|string|max:255',
            'billplz_collection_id' => 'nullable|string|max:100',
            'billplz_x_signature' => 'nullable|string|max:255',
        ]);

        Setting::set('clinic_name', $request->clinic_name);

        // OnSend.io settings (dedicated)
        Setting::set('onsend_enabled', $request->boolean('onsend_enabled') ? '1' : '0');
        if ($request->filled('onsend_token')) Setting::set('onsend_token', $request->onsend_token);
        Setting::set('onsend_endpoint', $request->onsend_endpoint);

        // WhatsApp other-provider settings
        Setting::set('whatsapp_enabled', $request->boolean('whatsapp_enabled') ? '1' : '0');
        Setting::set('whatsapp_provider', $request->whatsapp_provider ?: 'cloud_api');
        if ($request->filled('whatsapp_token')) Setting::set('whatsapp_token', $request->whatsapp_token);
        if ($request->filled('whatsapp_phone_id')) Setting::set('whatsapp_phone_id', $request->whatsapp_phone_id);
        Setting::set('whatsapp_endpoint', $request->whatsapp_endpoint);

        // Billplz settings
        Setting::set('billplz_enabled', $request->boolean('billplz_enabled') ? '1' : '0');
        Setting::set('billplz_sandbox', $request->boolean('billplz_sandbox') ? '1' : '0');
        if ($request->filled('billplz_api_key')) Setting::set('billplz_api_key', $request->billplz_api_key);
        if ($request->filled('billplz_collection_id')) Setting::set('billplz_collection_id', $request->billplz_collection_id);
        if ($request->filled('billplz_x_signature')) Setting::set('billplz_x_signature', $request->billplz_x_signature);

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
