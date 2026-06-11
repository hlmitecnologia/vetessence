<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TriageBoard;
use App\Models\Pet;
use App\Models\TriageRecord;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class TriageBoardTest extends ModuleTestCase
{
    public function test_board_renders_cases_grouped_by_status()
    {
        $this->loginAs('veterinario');

        $waiting = TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'red',
        ]);
        $inConsultation = TriageRecord::factory()->create([
            'status' => 'in_consultation',
            'severity' => 'yellow',
        ]);
        $seen = TriageRecord::factory()->create([
            'status' => 'seen',
            'severity' => 'green',
        ]);

        $component = Livewire::test(TriageBoard::class);

        $component->assertOk();
        $component->assertSee($waiting->pet->name);
        $component->assertSee($inConsultation->pet->name);
        $component->assertSee($seen->pet->name);
    }

    public function test_update_status_changes_case_status()
    {
        $this->loginAs('veterinario');

        $record = TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'yellow',
        ]);

        Livewire::test(TriageBoard::class)
            ->call('updateStatus', $record->id, 'in_consultation');

        $this->assertEquals('in_consultation', $record->fresh()->status);
    }

    public function test_update_status_to_seen_sets_seen_at_and_vet()
    {
        $this->loginAs('veterinario');

        $record = TriageRecord::factory()->create([
            'status' => 'in_consultation',
        ]);

        Livewire::test(TriageBoard::class)
            ->call('updateStatus', $record->id, 'seen');

        $record->refresh();
        $this->assertEquals('seen', $record->status);
        $this->assertNotNull($record->seen_at);
        $this->assertEquals(auth()->id(), $record->triage_vet_id);
    }

    public function test_update_status_to_discharged_sets_discharged_at()
    {
        $this->loginAs('veterinario');

        $record = TriageRecord::factory()->create([
            'status' => 'seen',
        ]);

        Livewire::test(TriageBoard::class)
            ->call('updateStatus', $record->id, 'discharged');

        $record->refresh();
        $this->assertEquals('discharged', $record->status);
        $this->assertNotNull($record->discharged_at);
    }

    public function test_update_status_with_invalid_status_does_nothing()
    {
        $this->loginAs('veterinario');

        $record = TriageRecord::factory()->create([
            'status' => 'waiting',
        ]);

        Livewire::test(TriageBoard::class)
            ->call('updateStatus', $record->id, 'invalid_status');

        $this->assertEquals('waiting', $record->fresh()->status);
    }

    public function test_new_critical_case_detected_on_render()
    {
        $this->loginAs('veterinario');

        TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'red',
        ]);

        $component = Livewire::test(TriageBoard::class);

        TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'red',
        ]);

        $component->call('$refresh');

        $component->assertDispatched('new-red-triage');
    }

    public function test_board_requires_authentication()
    {
        $response = $this->get(route('triage.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_cases_sorted_by_severity()
    {
        $this->loginAs('veterinario');

        $green = TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'green',
            'check_in_at' => now()->subMinutes(10),
        ]);
        $yellow = TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'yellow',
            'check_in_at' => now()->subMinutes(5),
        ]);
        $red = TriageRecord::factory()->create([
            'status' => 'waiting',
            'severity' => 'red',
            'check_in_at' => now(),
        ]);

        $component = Livewire::test(TriageBoard::class);

        $rendered = $component->html();
        $redPos = strpos($rendered, $red->pet->name);
        $greenPos = strpos($rendered, $green->pet->name);
        $yellowPos = strpos($rendered, $yellow->pet->name);

        $this->assertNotFalse($redPos);
        $this->assertNotFalse($greenPos);
        $this->assertNotFalse($yellowPos);
        $this->assertLessThan($yellowPos, $redPos);
    }
}
