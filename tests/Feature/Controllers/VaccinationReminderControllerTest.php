<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\VaccinationReminder;
use Tests\ModuleTestCase;

class VaccinationReminderControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    private function createPet(): Pet
    {
        return Pet::factory()->create();
    }

    private function createVaccination(Pet $pet): Vaccination
    {
        return Vaccination::create([
            'pet_id' => $pet->id,
            'vaccine' => 'V10',
            'batch' => 'BATCH001',
            'date' => now()->format('Y-m-d'),
        ]);
    }

    private function createReminder(): VaccinationReminder
    {
        $pet = $this->createPet();
        $vaccination = $this->createVaccination($pet);

        return VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);
    }

    public function test_index()
    {
        $response = $this->get(route('vaccination-reminders.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('vaccination-reminders.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $pet = $this->createPet();
        $vaccination = $this->createVaccination($pet);

        $response = $this->post(route('vaccination-reminders.store'), [
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now()->addDays(30)->format('Y-m-d'),
            'channel' => 'email',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('vaccination-reminders.index'));
        $this->assertDatabaseHas('vaccination_reminders', [
            'pet_id' => $pet->id,
            'vaccination_id' => $vaccination->id,
        ]);
    }

    public function test_show()
    {
        $reminder = $this->createReminder();

        $response = $this->get(route('vaccination-reminders.show', $reminder));
        $response->assertOk();
    }

    public function test_edit()
    {
        $reminder = $this->createReminder();

        $response = $this->get(route('vaccination-reminders.edit', $reminder));
        $response->assertOk();
    }

    public function test_update()
    {
        $reminder = $this->createReminder();

        $response = $this->put(route('vaccination-reminders.update', $reminder), [
            'vaccination_id' => $reminder->vaccination_id,
            'pet_id' => $reminder->pet_id,
            'scheduled_date' => now()->addDays(60)->format('Y-m-d'),
            'channel' => 'whatsapp',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('vaccination-reminders.index'));
        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'channel' => 'whatsapp',
        ]);
    }

    public function test_destroy()
    {
        $reminder = $this->createReminder();

        $response = $this->delete(route('vaccination-reminders.destroy', $reminder));
        $response->assertRedirect(route('vaccination-reminders.index'));
        $this->assertDatabaseMissing('vaccination_reminders', ['id' => $reminder->id]);
    }
}
