<?php

namespace Tests\Feature\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationDailyRecord;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class HospitalizationDailyRecordControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('hospitalization-daily-records.index'));
        $response->assertOk();
    }

    public function test_store_creates_daily_record()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $hospitalization = Hospitalization::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'admission_date' => now()->format('Y-m-d'),
            'admission_reason' => 'Observação',
            'status' => 'admitted',
        ]);

        $response = $this->post(route('hospitalization-daily-records.store'), [
            'hospitalization_id' => $hospitalization->id,
            'record_date' => now()->format('Y-m-d'),
            'shift' => 'morning',
            'observations' => 'Paciente estável',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalization_daily_records', [
            'hospitalization_id' => $hospitalization->id,
            'shift' => 'morning',
        ]);
    }

    public function test_show()
    {
        $record = HospitalizationDailyRecord::factory()->create();

        $response = $this->get(route('hospitalization-daily-records.show', $record));
        $response->assertOk();
    }

    public function test_update()
    {
        $record = HospitalizationDailyRecord::factory()->create();

        $response = $this->put(route('hospitalization-daily-records.update', $record), [
            'hospitalization_id' => $record->hospitalization_id,
            'record_date' => now()->format('Y-m-d'),
            'shift' => $record->shift,
            'observations' => 'Observação atualizada',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hospitalization_daily_records', [
            'id' => $record->id,
            'observations' => 'Observação atualizada',
        ]);
    }

    public function test_destroy()
    {
        $record = HospitalizationDailyRecord::factory()->create();

        $response = $this->delete(route('hospitalization-daily-records.destroy', $record));
        $response->assertRedirect();
        $this->assertDatabaseMissing('hospitalization_daily_records', ['id' => $record->id]);
    }
}
