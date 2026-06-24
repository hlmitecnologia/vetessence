<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\PetShopPackage;
use App\Models\Service;
use Tests\ModuleTestCase;

class PetShopPackageControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_lists_packages()
    {
        PetShopPackage::factory()->count(3)->create();

        $response = $this->get(route('pet-shop-packages.index'));

        $response->assertOk();
        $response->assertViewHas('packages');
    }

    public function test_create_form_loads()
    {
        $response = $this->get(route('pet-shop-packages.create'));

        $response->assertOk();
    }

    public function test_store_creates_package()
    {
        $branch = Branch::factory()->create();
        $service = Service::factory()->create();

        $response = $this->post(route('pet-shop-packages.store'), [
            'name' => 'Pacote Banho Premium',
            'type' => 'grooming',
            'services' => [['service_id' => $service->id, 'qty' => 1]],
            'total_price' => 89.90,
            'original_price' => 120.00,
            'validity_days' => 30,
            'max_uses' => 5,
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('pet-shop-packages.index'));
        $this->assertDatabaseHas('pet_shop_packages', [
            'name' => 'Pacote Banho Premium',
            'type' => 'grooming',
        ]);
    }

    public function test_edit_form_loads()
    {
        $package = PetShopPackage::factory()->create();

        $response = $this->get(route('pet-shop-packages.edit', $package));

        $response->assertOk();
        $response->assertViewHas('petShopPackage');
    }

    public function test_update_modifies_package()
    {
        $service = Service::factory()->create();
        $package = PetShopPackage::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('pet-shop-packages.update', $package), [
            'name' => 'New Name',
            'type' => 'boarding',
            'services' => [['service_id' => $service->id, 'qty' => 2]],
            'total_price' => 150.00,
            'original_price' => 200.00,
            'validity_days' => 60,
            'max_uses' => 10,
            'branch_id' => $package->branch_id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('pet-shop-packages.index'));
        $this->assertDatabaseHas('pet_shop_packages', [
            'id' => $package->id,
            'name' => 'New Name',
        ]);
    }

    public function test_destroy_deletes_package()
    {
        $package = PetShopPackage::factory()->create();

        $response = $this->delete(route('pet-shop-packages.destroy', $package));

        $response->assertRedirect(route('pet-shop-packages.index'));
        $this->assertDatabaseMissing('pet_shop_packages', ['id' => $package->id]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('pet-shop-packages.store'), []);

        $response->assertSessionHasErrors(['name', 'type', 'services', 'total_price', 'original_price', 'validity_days', 'max_uses', 'branch_id']);
    }

    public function test_index_requires_authentication()
    {
        $this->logout();

        $response = $this->get(route('pet-shop-packages.index'));

        $response->assertRedirect(route('login'));
    }

    protected function logout(): void
    {
        $this->app['auth']->logout();
    }
}
