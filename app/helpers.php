<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (!function_exists('branding')) {
    function branding(string $key, $default = null)
    {
        return Setting::get('branding.' . $key, $default);
    }
}

if (!function_exists('branding_logo_url')) {
    function branding_logo_url(): string
    {
        $path = branding('logo_path');
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }
        return asset('img/logo-default.png');
    }
}

if (!function_exists('branding_favicon_url')) {
    function branding_favicon_url(): string
    {
        $path = branding('favicon_path');
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }
        return asset('favicon.ico');
    }
}

if (!function_exists('branding_css_vars')) {
    function branding_css_vars(): string
    {
        $color = branding('primary_color', '#4f46e5');
        return ":root { --brand-primary: {$color}; --brand-primary-rgb: " . hex_to_rgb($color) . "; }";
    }
}

if (!function_exists('hex_to_rgb')) {
    function hex_to_rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "{$r},{$g},{$b}";
    }
}

if (!function_exists('branding_clinic_name')) {
    function branding_clinic_name(): string
    {
        return branding('clinic_name', config('app.name', 'VetEssence'));
    }
}
