<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ZoonoticDisease;
use App\Livewire\ZoonoticDiseaseForm;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ZoonoticDiseaseFormTest extends ModuleTestCase
{
    public function test_creates_new_disease()
    {
        $this->loginAs('admin');

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', 'My Test Disease')
            ->set('category', 'viral')
            ->set('species_affected', ['canine', 'feline'])
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'My Test Disease',
            'slug' => 'my-test-disease',
            'category' => 'viral',
        ]);
    }

    public function test_creates_without_species()
    {
        $this->loginAs('admin');

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', 'No Species Disease')
            ->set('category', 'bacterial')
            ->call('save');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'No Species Disease',
        ]);
    }

    public function test_generates_slug_auto()
    {
        $this->loginAs('admin');

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', 'Doença Complexa')
            ->set('category', 'parasitic')
            ->call('save');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'Doença Complexa',
            'slug' => 'doenca-complexa',
        ]);
    }

    public function test_validates_required_name()
    {
        $this->loginAs('admin');

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', '')
            ->set('category', 'viral')
            ->call('save')
            ->assertHasErrors('name');
    }

    public function test_user_with_permission_can_create()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'Zoonotic', 'guard_name' => 'web', 'slug' => 'zoonotic']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
        $user->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'zoonotic-diseases.create', 'guard_name' => 'web']));
        $this->actingAs($user);

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', 'Perm Test')
            ->set('category', 'fungal')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'name' => 'Perm Test',
        ]);
    }

    public function test_shows_error_on_duplicate_slug()
    {
        $this->loginAs('admin');

        ZoonoticDisease::create([
            'name' => 'Existing',
            'slug' => 'existing',
            'category' => 'viral',
        ]);

        Livewire::test(ZoonoticDiseaseForm::class)
            ->set('name', 'Existing')
            ->set('category', 'viral')
            ->call('save')
            ->assertHasErrors('name')
            ->assertNotDispatched('zoonotic-disease-saved');
    }

    public function test_includes_extra_zoonotic_species_options()
    {
        $this->loginAs('admin');

        Livewire::test(ZoonoticDiseaseForm::class)
            ->assertSet('speciesOptions', function ($options) {
                return in_array('wild_mammals', $options)
                    && in_array('wild_canids', $options)
                    && in_array('rodents', $options)
                    && in_array('birds', $options)
                    && in_array('psittacidae', $options);
            });
    }

    public function test_updates_existing_disease()
    {
        $this->loginAs('admin');

        $disease = ZoonoticDisease::create([
            'name' => 'Original',
            'slug' => 'original',
            'category' => 'viral',
        ]);

        Livewire::test(ZoonoticDiseaseForm::class, ['id' => $disease->id])
            ->set('name', 'Updated')
            ->set('category', 'bacterial')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $this->assertDatabaseHas('zoonotic_diseases', [
            'id' => $disease->id,
            'name' => 'Updated',
            'category' => 'bacterial',
        ]);
    }

    public function test_preserves_extra_species_on_update()
    {
        $this->loginAs('admin');

        $disease = ZoonoticDisease::create([
            'name' => 'Multi Species',
            'slug' => 'multi-species',
            'category' => 'viral',
            'species_affected' => ['canine', 'wild_mammals', 'rodents', 'birds'],
        ]);

        Livewire::test(ZoonoticDiseaseForm::class, ['id' => $disease->id])
            ->set('name', 'Multi Species Updated')
            ->call('save')
            ->assertDispatched('zoonotic-disease-saved');

        $updated = ZoonoticDisease::find($disease->id);
        $this->assertContains('wild_mammals', $updated->species_affected);
        $this->assertContains('rodents', $updated->species_affected);
        $this->assertContains('birds', $updated->species_affected);
    }
}
