<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:configuracoes.branding');
    }

    public function index()
    {
        return view('configuracoes.branding.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name'           => 'nullable|string|max:255',
            'primary_color'         => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'logo'                  => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon'               => 'nullable|image|mimes:png,ico|max:1024',
            'remove_logo'           => 'nullable|boolean',
            'remove_favicon'        => 'nullable|boolean',
            'show_clinic_name'      => 'nullable|boolean',
            'clinic_name_position'  => 'nullable|in:above,below,left,right',
            'login_background'      => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'sidebar_logo_width'    => 'nullable|integer|min:20|max:200',
            'sidebar_bg'            => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color'       => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color'          => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        if ($request->filled('clinic_name')) {
            Setting::set('branding.clinic_name', $request->clinic_name);
        }

        if ($request->filled('primary_color')) {
            Setting::set('branding.primary_color', $request->primary_color);
        }

        Setting::set('branding.show_clinic_name', $request->boolean('show_clinic_name') ? '1' : '0');

        if ($request->filled('clinic_name_position')) {
            Setting::set('branding.clinic_name_position', $request->clinic_name_position);
        }

        if ($request->filled('login_background')) {
            Setting::set('branding.login_background', $request->login_background);
        }

        if ($request->filled('sidebar_logo_width')) {
            Setting::set('branding.sidebar_logo_width', $request->sidebar_logo_width);
        }

        if ($request->filled('sidebar_bg')) {
            Setting::set('branding.sidebar_bg', $request->sidebar_bg);
        }

        if ($request->filled('secondary_color')) {
            Setting::set('branding.secondary_color', $request->secondary_color);
        }

        if ($request->filled('accent_color')) {
            Setting::set('branding.accent_color', $request->accent_color);
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

        return redirect()->route('configuracoes.branding.index')
            ->with('success', 'Personalização atualizada com sucesso.');
    }

    private function deleteBrandingFile(string $key): void
    {
        $path = Setting::get('branding.' . $key);
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
