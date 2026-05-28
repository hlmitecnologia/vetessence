<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\VaccinationReminder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaccinationReminderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $vaccination = Vaccination::factory()->create();
        $pet = Pet::factory()->create();
        VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now()->addDays(30),
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => $vaccination->id,
            'status' => 'pending',
        ]);
    }

    public function test_scheduled_date_cast()
    {
        $vaccination = Vaccination::factory()->create();
        $pet = Pet::factory()->create();
        $reminder = VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => '2026-06-15',
            'status' => 'pending',
        ]);
        $this->assertInstanceOf(\Carbon\Carbon::class, $reminder->scheduled_date);
    }

    public function test_vaccination_relationship()
    {
        $vaccination = Vaccination::factory()->create();
        $pet = Pet::factory()->create();
        $reminder = VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now(),
            'status' => 'pending',
        ]);
        $this->assertTrue($reminder->vaccination->is($vaccination));
    }

    public function test_pet_relationship()
    {
        $vaccination = Vaccination::factory()->create();
        $pet = Pet::factory()->create();
        $reminder = VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'scheduled_date' => now(),
            'status' => 'pending',
        ]);
        $this->assertTrue($reminder->pet->is($pet));
    }
}
