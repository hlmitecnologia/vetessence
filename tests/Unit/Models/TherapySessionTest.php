<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\TherapySession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TherapySessionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        TherapySession::create([
            'pet_id' => $pet->id,
            'type' => 'physiotherapy',
            'session_date' => now(),
            'status' => 'scheduled',
        ]);

        $this->assertDatabaseHas('therapy_sessions', [
            'pet_id' => $pet->id,
            'type' => 'physiotherapy',
            'status' => 'scheduled',
        ]);
    }

    public function test_type_enum()
    {
        $pet = Pet::factory()->create();
        TherapySession::create(['pet_id' => $pet->id, 'type' => 'physiotherapy', 'session_date' => now(), 'status' => 'scheduled']);
        TherapySession::create(['pet_id' => $pet->id, 'type' => 'acupuncture', 'session_date' => now(), 'status' => 'scheduled']);
        TherapySession::create(['pet_id' => $pet->id, 'type' => 'hydrotherapy', 'session_date' => now(), 'status' => 'scheduled']);
        TherapySession::create(['pet_id' => $pet->id, 'type' => 'massage', 'session_date' => now(), 'status' => 'scheduled']);

        $this->assertCount(4, TherapySession::all());
    }
}
