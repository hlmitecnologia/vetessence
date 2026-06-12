<?php

namespace Tests\Feature\Controllers;

use App\Models\StaffNote;
use App\Models\User;
use Tests\ModuleTestCase;

class StaffNoteControllerTest extends ModuleTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->loginAs('admin');
    }

    public function test_index()
    {
        StaffNote::factory()->count(3)->create();
        $response = $this->get(route('staff-notes.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_tab()
    {
        StaffNote::factory()->create(['created_by' => $this->user->id]);
        StaffNote::factory()->create(['created_by' => User::factory()->create()->id]);

        $response = $this->get(route('staff-notes.index', ['tab' => 'sent']));
        $response->assertOk();
    }

    public function test_index_filters_by_priority()
    {
        StaffNote::factory()->create(['priority' => 'high', 'created_by' => $this->user->id]);
        $response = $this->get(route('staff-notes.index', ['priority' => 'high']));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('staff-notes.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('staff-notes.store'), [
            'title' => 'Reunião de equipe amanhã',
            'content' => 'Confirmar presença de todos.',
            'priority' => 'high',
            'category' => 'reuniao',
        ]);
        $response->assertRedirect(route('staff-notes.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('staff_notes', [
            'title' => 'Reunião de equipe amanhã',
            'priority' => 'high',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('staff-notes.store'), []);
        $response->assertSessionHasErrors(['title', 'content', 'priority']);
    }

    public function test_store_validates_priority_enum()
    {
        $response = $this->post(route('staff-notes.store'), [
            'title' => 'Test',
            'content' => 'Test content',
            'priority' => 'critical',
        ]);
        $response->assertSessionHasErrors('priority');
    }

    public function test_show()
    {
        $note = StaffNote::factory()->create(['created_by' => $this->user->id]);
        $response = $this->get(route('staff-notes.show', $note));
        $response->assertOk();
    }

    public function test_show_marks_as_read_when_assigned()
    {
        $note = StaffNote::factory()->create([
            'assigned_to' => $this->user->id,
            'is_read' => false,
        ]);

        $response = $this->get(route('staff-notes.show', $note));
        $response->assertOk();
        $this->assertDatabaseHas('staff_notes', [
            'id' => $note->id,
            'is_read' => true,
        ]);
    }

    public function test_edit()
    {
        $note = StaffNote::factory()->create(['created_by' => $this->user->id]);
        $response = $this->get(route('staff-notes.edit', $note));
        $response->assertOk();
    }

    public function test_edit_fails_for_other_users_note()
    {
        $otherUser = User::factory()->create();
        $note = StaffNote::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->get(route('staff-notes.edit', $note));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_record()
    {
        $note = StaffNote::factory()->create([
            'created_by' => $this->user->id,
            'priority' => 'normal',
        ]);

        $response = $this->put(route('staff-notes.update', $note), [
            'title' => $note->title,
            'content' => $note->content,
            'priority' => 'urgent',
            'category' => 'financeiro',
        ]);
        $response->assertRedirect(route('staff-notes.show', $note));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('staff_notes', [
            'id' => $note->id,
            'priority' => 'urgent',
            'category' => 'financeiro',
        ]);
    }

    public function test_update_fails_for_other_users_note()
    {
        $otherUser = User::factory()->create();
        $note = StaffNote::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->put(route('staff-notes.update', $note), [
            'title' => 'Hacked',
            'content' => 'Malicious content',
            'priority' => 'low',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_destroy_deletes_record()
    {
        $note = StaffNote::factory()->create(['created_by' => $this->user->id]);

        $response = $this->delete(route('staff-notes.destroy', $note));
        $response->assertRedirect(route('staff-notes.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('staff_notes', ['id' => $note->id]);
    }

    public function test_destroy_fails_for_other_users_note()
    {
        $otherUser = User::factory()->create();
        $note = StaffNote::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->delete(route('staff-notes.destroy', $note));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('staff_notes', ['id' => $note->id]);
    }

    public function test_mark_read()
    {
        $note = StaffNote::factory()->create([
            'assigned_to' => $this->user->id,
            'is_read' => false,
        ]);

        $response = $this->post(route('staff-notes.mark-read', $note));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('staff_notes', [
            'id' => $note->id,
            'is_read' => true,
        ]);
    }
}
