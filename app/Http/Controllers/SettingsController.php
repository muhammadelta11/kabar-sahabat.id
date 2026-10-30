<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'tagline' => 'required|string|max:255',
            'copyright' => 'required|string|max:500',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp_enabled' => 'nullable|boolean',
            'whatsapp_number' => [
                'nullable',
                'required_if:whatsapp_enabled,1',
                'max:20',
                'regex:/^[0-9+\-\s]+$/',
            ],
            'whatsapp_message' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:1000',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'threads_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'analytics_code' => 'nullable|string|max:10000',
            'logo' => 'nullable|image|max:2048',
        ]);

        $setting = Setting::first() ?? new Setting;

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $path = $request->file('logo')->store('settings', 'public');
            $setting->logo_path = $path;
        }

        $setting->tagline = $request->tagline;
        $setting->copyright = $request->copyright;
        $setting->email = $request->email;
        $setting->phone = $request->phone;

        $number = preg_replace('/\D/', '', (string) $request->input('whatsapp_number'));
        $setting->whatsapp_enabled = $request->boolean('whatsapp_enabled') && $number !== '';
        $setting->whatsapp_number = match (true) {
            $number === '' => null,
            str_starts_with($number, '62') => $number,
            str_starts_with($number, '0') => '62'.substr($number, 1),
            default => '62'.$number,
        };
        $setting->whatsapp_message = $request->input('whatsapp_message');

        $setting->address = $request->address;
        $setting->instagram_url = $request->instagram_url;
        $setting->twitter_url = $request->twitter_url;
        $setting->youtube_url = $request->youtube_url;
        $setting->facebook_url = $request->facebook_url;
        $setting->threads_url = $request->threads_url;
        $setting->tiktok_url = $request->tiktok_url;
        $setting->analytics_code = $request->analytics_code;
        $setting->save();

        return redirect()->route('filament.admin.pages.manage-settings')
            ->with('success', 'Pengaturan berhasil disimpan!');
    }
}
