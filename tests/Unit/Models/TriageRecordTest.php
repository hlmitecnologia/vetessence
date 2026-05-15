<?php

namespace Tests\Unit\Models;

use App\Models\TriageRecord;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TriageRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $triage = TriageRecord::create([
            'pet_id' => $pet->id,
            'check_in_at' => now(),
            'severity' => 'yellow',
            'chief_complaint' => 'Vomiting and diarrhea',
            'status' => 'waiting',
        ]);

        $this->assertDatabaseHas('triage_records', [
            'pet_id' => $pet->id,
            'severity' => 'yellow',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $triage = TriageRecord::factory()->create(['pet_id' => $pet->id]);
        $this->assertInstanceOf(Pet::class, $triage->pet);
    }

    public function test_severity_enum()
    {
        TriageRecord::factory()->create(['severity' => 'red']);
        TriageRecord::factory()->create(['severity' => 'green']);
        $this->assertEquals(1, TriageRecord::where('severity', 'red')->count());
    }

    public function test_waiting_scope()
    {
        TriageRecord::factory()->count(2)->create(['status' => 'waiting']);
        TriageRecord::factory()->create(['status' => 'in_consultation']);
        $this->assertEquals(2, TriageRecord::where('status', 'waiting')->count());
    }
}
