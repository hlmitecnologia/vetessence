<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class VeterinarioFlowPart2Test extends DuskTestCase
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

    public function test_exam_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/exams')
                ->waitForText('Exames')
                ->assertSee('Exames');
        });
    }

    public function test_imaging_exam_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/imaging-exams')
                ->waitForText('Exames de Imagem')
                ->assertSee('Exames de Imagem');
        });
    }

    public function test_diet_plan_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/diet-plans')
                ->waitForText('Planos Alimentares')
                ->assertSee('Planos Alimentares');
        });
    }

    public function test_consent_form_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/consent-forms')
                ->waitForText('Termos de Consentimento')
                ->assertSee('Termos de Consentimento');
        });
    }

    public function test_dental_chart_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/dental-charts')
                ->waitForText('Odontogramas')
                ->assertSee('Odontogramas');
        });
    }

    public function test_health_certificate_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/health-certificates')
                ->waitForText('Certificados Sanitários')
                ->assertSee('Certificados Sanitários');
        });
    }

    public function test_pet_death_record_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->vet)
                ->visit('/pet-death-records')
                ->waitForText('Registros de Óbito')
                ->assertSee('Registros de Óbito');
        });
    }
}
