<?php

namespace Tests\Feature\Controllers;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\ModuleTestCase;

class BrandingControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('configuracoes.branding.index'));
        $response->assertOk();
    }

    public function test_update_saves_text_settings()
    {
        $response = $this->put(route('configuracoes.branding.update'), [
            'clinic_name' => 'Minha Clínica',
            'primary_color' => '#3490dc',
            'show_clinic_name' => true,
            'clinic_name_position' => 'below',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Minha Clínica', Setting::get('branding.clinic_name'));
        $this->assertEquals('#3490dc', Setting::get('branding.primary_color'));
        $this->assertEquals('1', Setting::get('branding.show_clinic_name'));
    }

    public function test_update_saves_color_settings()
    {
        $response = $this->put(route('configuracoes.branding.update'), [
            'login_background' => '#ffffff',
            'sidebar_bg' => '#2d3748',
            'secondary_color' => '#718096',
            'accent_color' => '#e53e3e',
        ]);

        $response->assertRedirect();
        $this->assertEquals('#ffffff', Setting::get('branding.login_background'));
        $this->assertEquals('#2d3748', Setting::get('branding.sidebar_bg'));
        $this->assertEquals('#718096', Setting::get('branding.secondary_color'));
        $this->assertEquals('#e53e3e', Setting::get('branding.accent_color'));
    }

    public function test_update_validates_hex_color()
    {
        $response = $this->put(route('configuracoes.branding.update'), [
            'primary_color' => 'not-a-hex',
        ]);
        $response->assertSessionHasErrors('primary_color');
    }

    public function test_update_uploads_logo()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $response = $this->put(route('configuracoes.branding.update'), [
            'logo' => $file,
        ]);

        $response->assertRedirect();
        $path = Setting::get('branding.logo_path');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_update_removes_logo()
    {
        Storage::fake('public');
        $path = Storage::disk('public')->put('branding/old-logo.png', 'fake');
        Setting::set('branding.logo_path', $path);

        $response = $this->put(route('configuracoes.branding.update'), [
            'remove_logo' => true,
        ]);

        $response->assertRedirect();
        $this->assertEquals('', Setting::get('branding.logo_path'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_update_validates_logo_dimensions()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 500);

        $response = $this->put(route('configuracoes.branding.update'), [
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }
}
