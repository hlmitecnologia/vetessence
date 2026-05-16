<?php

namespace Tests\Unit\Models;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ChatMessageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Teste de mensagem',
        ]);
        $this->assertDatabaseHas('chat_messages', ['message' => 'Teste de mensagem']);
    }

    public function test_sender_relationship()
    {
        $msg = ChatMessage::factory()->create();
        $this->assertInstanceOf(User::class, $msg->sender);
    }

    public function test_receiver_relationship()
    {
        $msg = ChatMessage::factory()->create();
        $this->assertInstanceOf(User::class, $msg->receiver);
    }

    public function test_unread_scope()
    {
        $receiver = User::factory()->create();
        ChatMessage::factory()->count(2)->create([
            'receiver_id' => $receiver->id,
            'read_at' => null,
        ]);
        ChatMessage::factory()->create([
            'receiver_id' => $receiver->id,
            'read_at' => now(),
        ]);
        $this->assertCount(2, ChatMessage::unread($receiver->id)->get());
    }

    public function test_between_scope()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        ChatMessage::factory()->create(['sender_id' => $user1->id, 'receiver_id' => $user2->id]);
        ChatMessage::factory()->create(['sender_id' => $user2->id, 'receiver_id' => $user1->id]);
        $this->assertCount(2, ChatMessage::between($user1->id, $user2->id)->get());
    }
}
