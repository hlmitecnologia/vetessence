<div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Conversas</h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-2">
                        <input wire:model="search" type="text" class="form-control form-control-sm" placeholder="Buscar usuário...">
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @forelse($users as $user)
                        <button wire:click="selectUser({{ $user->id }})"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                            {{ $receiverId == $user->id ? 'active' : '' }}">
                            {{ $user->name }}
                            @if($user->unread > 0)
                            <span class="badge badge-primary badge-pill">{{ $user->unread }}</span>
                            @endif
                        </button>
                        @empty
                        <div class="list-group-item text-muted">Nenhum usuário encontrado</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($receiverId)
                            Conversando com {{ $users->firstWhere('id', $receiverId)->name ?? '...' }}
                        @else
                            Selecione um usuário
                        @endif
                    </h3>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;" id="chat-messages">
                    @if($receiverId)
                        @forelse($messages as $msg)
                        <div class="mb-2 p-2 rounded {{ $msg->sender_id === auth()->id() ? 'bg-light text-right' : 'bg-primary text-white' }}"
                            style="max-width: 80%; margin-left: {{ $msg->sender_id === auth()->id() ? 'auto' : '0' }}; margin-right: {{ $msg->sender_id === auth()->id() ? '0' : 'auto' }}">
                            <small>{{ $msg->message }}</small>
                            <br><small class="text-muted" style="font-size: 9px;">{{ $msg->created_at->format('H:i') }}</small>
                        </div>
                        @empty
                        <p class="text-muted text-center">Nenhuma mensagem ainda.</p>
                        @endforelse
                        {{ $messages->links() }}
                    @else
                        <p class="text-muted text-center">Selecione um usuário à esquerda para iniciar uma conversa.</p>
                    @endif
                </div>
                @if($receiverId)
                <div class="card-footer">
                    <form wire:submit.prevent="sendMessage">
                        <div class="input-group">
                            <input wire:model="newMessage" type="text" class="form-control" placeholder="Digite sua mensagem...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('refreshChat', function () {
                var el = document.getElementById('chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        });
    </script>
</div>
