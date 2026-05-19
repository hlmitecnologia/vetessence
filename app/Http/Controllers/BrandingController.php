<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:branding');
    }

    public function index()
    {
        return view('branding.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name'  => 'nullable|string|max:255',
            'primary_color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'logo'         => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon'      => 'nullable|image|mimes:png,ico|max:1024',
            'remove_logo'  => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ]);

        if ($request->filled('clinic_name')) {
            Setting::set('branding.clinic_name', $request->clinic_name);
        }

        if ($request->filled('primary_color')) {
            Setting::set('branding.primary_color', $request->primary_color);
        }

        if ($request->boolean('remove_logo')) {
            $this->deleteBrandingFile('logo_path');
            Setting::set('branding.logo_path', '');
        }

        if ($request->boolean('remove_favicon')) {
            $this->deleteBrandingFile('favicon_path');
            Setting::set('branding.favicon_path', '');
        }

        if ($request->hasFile('logo')) {
            $this->deleteBrandingFile('logo_path');
            $path = $request->file('logo')->store('branding', 'public');
            Setting::set('branding.logo_path', $path);
        }

        if ($request->hasFile('favicon')) {
            $this->deleteBrandingFile('favicon_path');
            $path = $request->file('favicon')->store('branding', 'public');
            Setting::set('branding.favicon_path', $path);
        }

        return redirect()->route('branding.index')
            ->with('success', 'Identidade visual atualizada com sucesso.');
    }

    private function deleteBrandingFile(string $key): void
    {
        $path = Setting::get('branding.' . $key);
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
