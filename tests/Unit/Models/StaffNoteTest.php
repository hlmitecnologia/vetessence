<?php

namespace Tests\Unit\Models;

use App\Models\StaffNote;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffNoteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $creator = User::factory()->create();
        $assigned = User::factory()->create();

        StaffNote::create([
            'title' => 'Nota importante',
            'content' => 'Conteudo da nota',
            'priority' => 'high',
            'created_by' => $creator->id,
            'assigned_to' => $assigned->id,
            'category' => 'general',
            'is_read' => false,
            'read_at' => null,
            'branch_id' => null,
        ]);

        $this->assertDatabaseHas('staff_notes', [
            'title' => 'Nota importante',
            'priority' => 'high',
            'created_by' => $creator->id,
        ]);
    }

    public function test_creator_relationship()
    {
        $creator = User::factory()->create();
        $note = StaffNote::create([
            'title' => 'Nota',
            'content' => 'Conteudo',
            'priority' => 'low',
            'created_by' => $creator->id,
            'is_read' => false,
        ]);

        $this->assertInstanceOf(User::class, $note->creator);
        $this->assertEquals($creator->id, $note->creator->id);
    }

    public function test_assigned_to_relationship()
    {
        $creator = User::factory()->create();
        $assigned = User::factory()->create();
        $note = StaffNote::create([
            'title' => 'Nota',
            'content' => 'Conteudo',
            'priority' => 'medium',
            'created_by' => $creator->id,
            'assigned_to' => $assigned->id,
            'is_read' => false,
        ]);

        $this->assertInstanceOf(User::class, $note->assignedTo);
        $this->assertEquals($assigned->id, $note->assignedTo->id);
    }

    public function test_unread_scope()
    {
        $user = User::factory()->create();
        StaffNote::create(['title' => 'A', 'is_read' => false, 'created_by' => $user->id]);
        StaffNote::create(['title' => 'B', 'is_read' => true, 'created_by' => $user->id]);
        StaffNote::create(['title' => 'C', 'is_read' => false, 'created_by' => $user->id]);

        $this->assertCount(2, StaffNote::unread()->get());
    }

    public function test_mark_as_read_method()
    {
        $user = User::factory()->create();
        $note = StaffNote::create([
            'title' => 'Teste',
            'is_read' => false,
            'created_by' => $user->id,
        ]);

        $note->markAsRead();

        $this->assertDatabaseHas('staff_notes', [
            'id' => $note->id,
            'is_read' => true,
        ]);
    }
}
