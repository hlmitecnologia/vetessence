<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class VeterinarioFlowTest extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected Tutor $tutor;
    protected Pet $pet;
    protected User $vet;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Vet Teste']);
        $this->tutor = Tutor::factory()->create(['name' => 'Tutor Teste']);
        $this->pet = Pet::factory()->create(['name' => 'Rex']);
        $this->tutor->pets()->attach($this->pet->id);
        $this->vet = $this->createUser('veterinario', ['branch_id' => $this->branch->id]);
        $this->admin = $this->createUser('admin', ['branch_id' => $this->branch->id]);
    }

    public function test_medical_record_list(): void
    {
        MedicalRecord::factory()->create([
            'pet_id' => $this->pet->id,
            'user_id' => $this->vet->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/medical-records')
                ->waitForText('Prontuários')
                ->assertSee('Prontuários');
        });
    }

    public function test_prescription_flow(): void
    {
        $record = MedicalRecord::factory()->create([
            'pet_id' => $this->pet->id,
            'user_id' => $this->vet->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->vet)
                ->visit('/prescriptions')
                ->waitForText('Prescrições')
                ->assertSee('Prescrições')
                ->assertSee('Novo');
        });
    }

    public function test_treatment_plan_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/treatment-plans')
                ->waitForText('Planos de Tratamento')
                ->assertSee('Planos de Tratamento');
        });
    }

    public function test_vaccination_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/vaccinations')
                ->waitForText('Vacinas')
                ->assertSee('Vacinas');
        });
    }

    public function test_surgery_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/surgeries')
                ->waitForText('Cirurgias')
                ->assertSee('Cirurgias');
        });
    }

    public function test_hospitalization_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/hospitalizations')
                ->waitForText('Internações')
                ->assertSee('Internações');
        });
    }
}
