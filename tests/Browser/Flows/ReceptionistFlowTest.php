<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class ReceptionistFlowTest extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected Tutor $tutor;
    protected Pet $pet;
    protected User $recep;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Teste']);
        $this->tutor = Tutor::factory()->create(['name' => 'Tutor Teste']);
        $this->pet = Pet::factory()->create(['name' => 'Rex']);
        $this->tutor->pets()->attach($this->pet->id);
        $this->recep = $this->createUser('recepcionista', ['branch_id' => $this->branch->id]);
    }

    public function test_tutor_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/tutors')
                ->waitForText('Tutores')
                ->assertSee('Tutores');
        });
    }

    public function test_pet_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/pets')
                ->waitForText('Pets')
                ->assertSee('Pets');
        });
    }

    public function test_patient_timeline(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/pets/' . $this->pet->id . '/timeline')
                ->waitForText('Timeline')
                ->assertSee('Timeline');
        });
    }

    public function test_appointment_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/appointments')
                ->waitForText('Agenda')
                ->assertSee('Agenda');
        });
    }

    public function test_online_booking_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/online-bookings')
                ->waitForText('Agendamentos Online')
                ->assertSee('Agendamentos Online');
        });
    }

    public function test_boarding_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/boardings')
                ->waitForText('Hospedagem')
                ->assertSee('Hospedagem & Banho/Tosa');
        });
    }

    public function test_chat_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/chat')
                ->waitForText('Chat Interno')
                ->assertSee('Chat Interno');
        });
    }

    public function test_notification_logs(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->recep)
                ->visit('/notification-logs')
                ->waitForText('Logs de Notificação')
                ->assertSee('Logs de Notificação');
        });
    }
}
