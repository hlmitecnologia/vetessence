<?php

namespace Tests\Feature\Modules;

use App\Models\StaffNote;
use App\Models\User;
use Tests\ModuleTestCase;

class StaffNoteTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('staff-notes.index'));
        $response->assertOk();
    }

    public function test_store_creates_note()
    {
        $target = $this->makeUser('recepcionista');
        $response = $this->post(route('staff-notes.store'), [
            'title' => 'Reunião amanhã',
            'content' => '10h na sala de reuniões',
            'priority' => 'normal',
            'assigned_to' => $target->id,
            'category' => 'Reunião',
        ]);
        $response->assertRedirect(route('staff-notes.index'));
        $this->assertDatabaseHas('staff_notes', ['title' => 'Reunião amanhã']);
    }

    public function test_mark_read()
    {
        $note = StaffNote::factory()->create([
            'assigned_to' => auth()->id(),
            'is_read' => false,
        ]);
        $this->post(route('staff-notes.mark-read', $note));
        $this->assertTrue($note->fresh()->is_read);
    }

    public function test_own_note_edit_guard()
    {
        $other = $this->makeUser('veterinario');
        $note = StaffNote::factory()->create(['created_by' => $other->id]);
        $response = $this->get(route('staff-notes.edit', $note));
        $response->assertRedirect();
    }

    public function test_inbox_tab()
    {
        StaffNote::factory()->create(['assigned_to' => auth()->id()]);
        $response = $this->get(route('staff-notes.index', ['tab' => 'inbox']));
        $response->assertOk();
    }

    public function test_sent_tab()
    {
        StaffNote::factory()->create(['created_by' => auth()->id()]);
        $response = $this->get(route('staff-notes.index', ['tab' => 'sent']));
        $response->assertOk();
    }
}
