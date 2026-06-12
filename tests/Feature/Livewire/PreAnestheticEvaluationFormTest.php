<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\PreAnestheticEvaluation;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class PreAnestheticEvaluationFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create()
    {
        $pet = Pet::factory()->create();

        Livewire::test('pre-anesthetic-evaluation-form')
            ->set('pet_id', $pet->id)
            ->set('asa_score', '2')
            ->set('status', 'pending')
            ->set('fasted', true)
            ->set('exam_checklist', ['hemogram', 'ecg'])
            ->call('save')
            ->assertDispatched('pre-anesthetic-evaluation-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('pre_anesthetic_evaluations', [
            'pet_id' => $pet->id,
            'asa_score' => 2,
            'status' => 'pending',
            'fasted' => true,
            'vet_id' => auth()->id(),
        ]);

        $evaluation = PreAnestheticEvaluation::where('pet_id', $pet->id)->first();
        $this->assertEquals(['hemogram', 'ecg'], $evaluation->exam_checklist);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('pre-anesthetic-evaluation-form')
            ->call('save')
            ->assertHasErrors(['pet_id']);
    }

    public function test_can_edit()
    {
        $evaluation = PreAnestheticEvaluation::factory()->create([
            'asa_score' => 1,
            'status' => 'pending',
        ]);

        Livewire::test('pre-anesthetic-evaluation-form', ['id' => $evaluation->id])
            ->assertSet('asa_score', (string) $evaluation->asa_score)
            ->set('asa_score', '3')
            ->set('status', 'approved')
            ->call('save')
            ->assertDispatched('pre-anesthetic-evaluation-saved');

        $this->assertDatabaseHas('pre_anesthetic_evaluations', [
            'id' => $evaluation->id,
            'asa_score' => 3,
            'status' => 'approved',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $evaluation = PreAnestheticEvaluation::factory()->create([
            'asa_score' => 2,
            'status' => 'pending',
        ]);

        Livewire::test('pre-anesthetic-evaluation-form')
            ->dispatch('editPreAnestheticEvaluation', id: $evaluation->id)
            ->assertSet('preAnestheticEvaluationId', $evaluation->id)
            ->assertSet('asa_score', (string) $evaluation->asa_score)
            ->set('status', 'rejected')
            ->call('save')
            ->assertDispatched('pre-anesthetic-evaluation-saved');

        $this->assertDatabaseHas('pre_anesthetic_evaluations', [
            'id' => $evaluation->id,
            'status' => 'rejected',
        ]);
    }

    public function test_reset_form()
    {
        $evaluation = PreAnestheticEvaluation::factory()->create();

        Livewire::test('pre-anesthetic-evaluation-form')
            ->dispatch('editPreAnestheticEvaluation', id: $evaluation->id)
            ->assertSet('preAnestheticEvaluationId', $evaluation->id)
            ->dispatch('resetForm')
            ->assertSet('preAnestheticEvaluationId', null)
            ->assertSet('pet_id', '')
            ->assertSet('asa_score', '1')
            ->assertSet('status', 'pending')
            ->assertSet('fasted', false)
            ->assertSet('hydrated', false)
            ->assertSet('exam_checklist', []);
    }
}
