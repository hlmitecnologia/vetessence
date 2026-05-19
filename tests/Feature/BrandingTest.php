<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use DatabaseTransactions;

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'branding-test', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => 'branding', 'guard_name' => 'web']));
        $user->assignRole($role);
        return $user;
    }

    public function test_non_admin_cannot_access_branding()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('branding.index'))->assertStatus(403);
    }

    public function test_admin_can_access_branding()
    {
        $this->actingAs($this->adminUser())->get(route('branding.index'))->assertOk();
    }

    public function test_can_update_clinic_name()
    {
        $this->actingAs($this->adminUser())->put(route('branding.update'), [
            'clinic_name' => 'Minha Clínica',
        ])->assertRedirect(route('branding.index'));

        $this->assertEquals('Minha Clínica', Setting::get('branding.clinic_name'));
    }

    public function test_can_update_primary_color()
    {
        $this->actingAs($this->adminUser())->put(route('branding.update'), [
            'primary_color' => '#ff0000',
        ])->assertRedirect(route('branding.index'));

        $this->assertEquals('#ff0000', Setting::get('branding.primary_color'));
    }

    public function test_can_upload_logo()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $this->actingAs($this->adminUser())->put(route('branding.update'), [
            'logo' => $file,
        ])->assertRedirect(route('branding.index'));

        $path = Setting::get('branding.logo_path');
        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_branding_values_appear_in_layouts()
    {
        Setting::set('branding.clinic_name', 'Minha Clínica');
        Setting::set('branding.primary_color', '#ff0000');

        $this->actingAs($this->adminUser())->get(route('branding.index'))
            ->assertOk()
            ->assertSee('Minha Clínica')
            ->assertSee('#ff0000');
    }
}
