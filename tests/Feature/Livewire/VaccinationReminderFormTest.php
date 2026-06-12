<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\VaccinationReminder;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class VaccinationReminderFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create()
    {
        $pet = Pet::factory()->create(['is_active' => true]);
        $vaccination = Vaccination::factory()->create();

        Livewire::test('vaccination-reminder-form')
            ->set('vaccination_id', $vaccination->id)
            ->set('pet_id', $pet->id)
            ->set('scheduled_date', '2026-06-15')
            ->set('status', 'pending')
            ->call('save')
            ->assertDispatched('vaccination-reminder-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'status' => 'pending',
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('vaccination-reminder-form')
            ->call('save')
            ->assertHasErrors(['vaccination_id', 'pet_id', 'scheduled_date']);
    }

    public function test_can_edit()
    {
        $reminder = VaccinationReminder::factory()->create([
            'status' => 'pending',
        ]);

        Livewire::test('vaccination-reminder-form', ['id' => $reminder->id])
            ->assertSet('status', $reminder->status)
            ->set('status', 'sent')
            ->call('save')
            ->assertDispatched('vaccination-reminder-saved');

        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'sent',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $reminder = VaccinationReminder::factory()->create([
            'status' => 'pending',
        ]);

        Livewire::test('vaccination-reminder-form')
            ->dispatch('editVaccinationReminder', id: $reminder->id)
            ->assertSet('vaccinationReminderId', $reminder->id)
            ->assertSet('status', 'pending')
            ->set('status', 'failed')
            ->call('save')
            ->assertDispatched('vaccination-reminder-saved');

        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'failed',
        ]);
    }

    public function test_reset_form()
    {
        $reminder = VaccinationReminder::factory()->create();

        Livewire::test('vaccination-reminder-form')
            ->dispatch('editVaccinationReminder', id: $reminder->id)
            ->assertSet('vaccinationReminderId', $reminder->id)
            ->dispatch('resetForm')
            ->assertSet('vaccinationReminderId', null)
            ->assertSet('vaccination_id', '')
            ->assertSet('pet_id', '')
            ->assertSet('scheduled_date', '')
            ->assertSet('status', 'pending');
    }
}
