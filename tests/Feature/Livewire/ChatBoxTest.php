<?php

namespace Tests\Feature\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ChatBoxTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_send_message_between_two_users()
    {
        $receiver = User::factory()->create(['is_active' => true]);

        Livewire::test('chat-box')
            ->set('receiverId', $receiver->id)
            ->set('newMessage', 'Hello, how are you?')
            ->call('sendMessage')
            ->assertSet('newMessage', '');

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => auth()->id(),
            'receiver_id' => $receiver->id,
            'message' => 'Hello, how are you?',
        ]);
    }

    public function test_cannot_send_empty_message()
    {
        $receiver = User::factory()->create(['is_active' => true]);

        Livewire::test('chat-box')
            ->set('receiverId', $receiver->id)
            ->set('newMessage', '')
            ->call('sendMessage');

        $this->assertDatabaseCount('chat_messages', 0);
    }

    public function test_can_select_user()
    {
        $user = User::factory()->create(['is_active' => true]);

        Livewire::test('chat-box')
            ->call('selectUser', $user->id)
            ->assertSet('receiverId', $user->id);
    }

    public function test_unread_count_updates()
    {
        $sender = User::factory()->create(['is_active' => true]);

        ChatMessage::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => auth()->id(),
        ]);

        Livewire::test('chat-box')
            ->assertSet('unreadCount', 1);
    }
}
