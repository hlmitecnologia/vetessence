<?php

namespace Tests\Unit\Models;

use App\Models\HospitalizationDailyRecord;
use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HospitalizationDailyRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        HospitalizationDailyRecord::create([
            'hospitalization_id' => $h->id, 'user_id' => $user->id,
            'record_date' => now(), 'shift' => 'manha', 'subjective' => 'Paciente alerta',
        ]);
        $this->assertDatabaseHas('hospitalization_daily_records', ['hospitalization_id' => $h->id, 'shift' => 'manha']);
    }

    public function test_hospitalization_relationship()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $dr = HospitalizationDailyRecord::create(['hospitalization_id' => $h->id, 'record_date' => now(), 'shift' => 'tarde']);
        $this->assertInstanceOf(Hospitalization::class, $dr->hospitalization);
    }

    public function test_user_relationship()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $dr = HospitalizationDailyRecord::create(['hospitalization_id' => $h->id, 'user_id' => $user->id, 'record_date' => now(), 'shift' => 'tarde']);
        $this->assertInstanceOf(User::class, $dr->user);
    }
}
