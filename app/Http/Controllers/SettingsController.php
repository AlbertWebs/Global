<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        // Only Super Admin can access settings
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can access system settings. Please contact your administrator if you need access to this page.'
            ]);
        }

        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Only Super Admin can update settings
        if (!auth()->user()->isSuperAdmin()) {
            return view('errors.unauthorized', [
                'message' => 'Only Super Administrators can update system settings. Please contact your administrator if you need access to this feature.'
            ]);
        }

        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'receipt_logo' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        // Handle normal logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            Setting::set('school_logo', $logoPath, 'image', 'school', 'School logo image (normal)');
        }

        // Handle receipt logo upload
        if ($request->hasFile('receipt_logo')) {
            $receiptLogoPath = $request->file('receipt_logo')->store('logos', 'public');
            Setting::set('receipt_logo', $receiptLogoPath, 'image', 'school', 'School logo image (for receipts)');
        }

        // Update other settings
        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                // Skip logo if it's being handled separately
                if ($key !== 'school_logo') {
                    $setting->update(['value' => $value]);
                }
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
