<?php

namespace Tests\Feature;

use App\Models\DrugFormulary;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DrugFormularyTest extends TestCase
{
    use DatabaseTransactions;

    private function adminUser()
    {
        $user = User::factory()->create();
        if (!Role::where('name', 'admin-test')->exists()) {
            $role = Role::create(['name' => 'admin-test', 'guard_name' => 'web']);
            foreach (['drug-formulary.view', 'drug-formulary.create', 'drug-formulary.edit', 'drug-formulary.delete'] as $p) {
                Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
                $role->givePermissionTo($p);
            }
        }
        $user->assignRole('admin-test');
        return $user;
    }

    public function test_index_requires_authentication()
    {
        $this->get(route('drug-formulary.index'))->assertRedirect(route('login'));
    }

    public function test_index_displays_formularies()
    {
        DrugFormulary::factory()->create(['drug' => 'Fenobarbital', 'species' => 'Canina']);

        $this->actingAs($this->adminUser())->get(route('drug-formulary.index'))
            ->assertOk()
            ->assertSee('Fenobarbital');
    }

    public function test_create_and_store()
    {
        $this->actingAs($this->adminUser())->post(route('drug-formulary.store'), [
            'drug' => 'Omeprazol',
            'species' => 'Canina',
            'dosage_mg_kg' => 1.0,
        ])->assertRedirect(route('drug-formulary.index'));

        $this->assertDatabaseHas('drug_formulary', ['drug' => 'Omeprazol']);
    }

    public function test_calculate_endpoint()
    {
        $entry = DrugFormulary::factory()->create([
            'drug' => 'Ranitidina',
            'species' => 'Canina',
            'dosage_mg_kg' => 2.0,
            'max_dose' => 50,
        ]);

        $response = $this->actingAs($this->adminUser())->post(route('drug-formulary.calculate'), [
            'drug_formulary_id' => $entry->id,
            'weight_kg' => 20,
            'species' => 'Canina',
        ]);

        $response->assertOk();
        $response->assertJson(['calculated_dose_mg' => 40.0]);
    }

    public function test_calculate_respects_max_dose()
    {
        $entry = DrugFormulary::factory()->create([
            'drug' => 'MaxTest',
            'species' => 'Canina',
            'dosage_mg_kg' => 5.0,
            'max_dose' => 30,
        ]);

        $response = $this->actingAs($this->adminUser())->post(route('drug-formulary.calculate'), [
            'drug_formulary_id' => $entry->id,
            'weight_kg' => 10,
            'species' => 'Canina',
        ]);

        $response->assertOk();
        $response->assertJson(['calculated_dose_mg' => 30.0]);
    }
}
