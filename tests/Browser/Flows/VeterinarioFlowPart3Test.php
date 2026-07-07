<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class VeterinarioFlowPart3Test extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected Tutor $tutor;
    protected Pet $pet;
    protected User $vet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Vet Teste']);
        $this->tutor = Tutor::factory()->create(['name' => 'Tutor Teste']);
        $this->pet = Pet::factory()->create(['name' => 'Rex']);
        $this->tutor->pets()->attach($this->pet->id);
        $this->vet = $this->createUser('veterinario', ['branch_id' => $this->branch->id]);
    }

    public function test_triage_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/triage')
                ->waitForText('Triagem')
                ->assertSee('Triagem');
        });
    }

    public function test_emergency_protocol_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/emergency-protocols')
                ->waitForText('Protocolos')
                ->assertSee('Protocolos de Emergencia');
        });
    }

    public function test_dosage_calculator(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/drug-formulary')
                ->waitForText('Formulario')
                ->assertSee('Formulario de Farmacos');
        });
    }

    public function test_weight_record_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/weight-records')
                ->waitForText('Registros de Peso')
                ->assertSee('Registros de Peso');
        });
    }
}
