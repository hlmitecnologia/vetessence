<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ChatBox extends Component
{
    use WithPagination;

    public $receiverId;
    public $newMessage = '';
    public $search = '';
    public $unreadCount = 0;

    public function mount()
    {
        $this->unreadCount = ChatMessage::unread(auth()->id())->count();
    }

    public function selectUser($userId)
    {
        $this->receiverId = $userId;
        $this->resetPage();
    }

    public function sendMessage()
    {
        if (!$this->receiverId || trim($this->newMessage) === '') return;

        ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverId,
            'message' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
    }

    public function markAsRead($userId)
    {
        ChatMessage::unread(auth()->id())
            ->where('sender_id', $userId)
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', auth()->id())
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $user->unread = ChatMessage::unread(auth()->id())
                    ->where('sender_id', $user->id)
                    ->count();
                return $user;
            });

        $messages = collect();
        if ($this->receiverId) {
            $this->markAsRead($this->receiverId);
            $messages = ChatMessage::with(['sender', 'receiver'])
                ->between(auth()->id(), $this->receiverId)
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        }

        $this->unreadCount = ChatMessage::unread(auth()->id())->count();
        $this->dispatch('unread-count', count: $this->unreadCount);

        return view('livewire.chat-box', compact('users', 'messages'));
    }
}
