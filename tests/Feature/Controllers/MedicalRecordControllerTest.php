<?php

namespace Tests\Feature\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class MedicalRecordControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('medical-records.index'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
        ]);

        $response = $this->post(route('medical-records.store'), [
            'pet_id' => $pet->id,
            'user_id' => $user->id,
            'appointment_id' => $appointment->id,
            'date' => now()->format('Y-m-d'),
            'type' => 'consultation',
            'complaint' => 'Tosse',
            'diagnosis' => 'Gripe',
            'treatment' => 'Repouso',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_show()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->get(route('medical-records.show', $record));
        $response->assertOk();
    }

    public function test_update()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->put(route('medical-records.update', $record), [
            'diagnosis' => 'Nova diagnosis',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'diagnosis' => 'Nova diagnosis',
        ]);
    }

    public function test_destroy()
    {
        $record = MedicalRecord::factory()->create();

        $response = $this->delete(route('medical-records.destroy', $record));
        $response->assertRedirect();
        $this->assertDatabaseMissing('medical_records', ['id' => $record->id]);
    }
}
