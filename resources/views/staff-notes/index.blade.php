@extends('layouts.adminlte', ['title' => 'Notas Internas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notas Internas
            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="badge badge-danger ml-2">{{ $unreadCount }} não lida(s)</span>
            @endif
        </h3>
        <div class="card-tools">
            <a href="{{ route('staff-notes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Nota
            </a>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request('tab', 'inbox') == 'inbox' ? 'active' : '' }}" href="{{ route('staff-notes.index', ['tab' => 'inbox']) }}">Recebidas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'unread' ? 'active' : '' }}" href="{{ route('staff-notes.index', ['tab' => 'unread']) }}">Não Lidas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'sent' ? 'active' : '' }}" href="{{ route('staff-notes.index', ['tab' => 'sent']) }}">Enviadas</a>
            </li>
        </ul>

        <form method="GET" class="row mb-3">
            <input type="hidden" name="tab" value="{{ request('tab', 'inbox') }}">
            <div class="col-md-3">
                <select name="priority" class="form-control form-control-sm">
                    <option value="">Todas as prioridades</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-control form-control-sm">
                    <option value="">Todas as categorias</option>
                    <option value="Geral" {{ request('category') == 'Geral' ? 'selected' : '' }}>Geral</option>
                    <option value="Lembrete" {{ request('category') == 'Lembrete' ? 'selected' : '' }}>Lembrete</option>
                    <option value="Urgência" {{ request('category') == 'Urgência' ? 'selected' : '' }}>Urgência</option>
                    <option value="Procedimento" {{ request('category') == 'Procedimento' ? 'selected' : '' }}>Procedimento</option>
                    <option value="Reunião" {{ request('category') == 'Reunião' ? 'selected' : '' }}>Reunião</option>
                    <option value="Financeiro" {{ request('category') == 'Financeiro' ? 'selected' : '' }}>Financeiro</option>
                    <option value="Outros" {{ request('category') == 'Outros' ? 'selected' : '' }}>Outros</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('staff-notes.index', ['tab' => request('tab', 'inbox')]) }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if($notes->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>Título</th>
                        <th>De</th>
                        <th>Para</th>
                        <th>Prioridade</th>
                        <th>Categoria</th>
                        <th>Data</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                    <tr class="{{ !$note->is_read && $note->assigned_to === auth()->id() ? 'table-info' : '' }}">
                        <td>
                            @if(!$note->is_read && ($note->assigned_to === auth()->id() || !$note->assigned_to))
                                <i class="fas fa-envelope text-primary"></i>
                            @else
                                <i class="fas fa-envelope-open text-muted"></i>
                            @endif
                        </td>
                        <td><strong>{{ $note->title }}</strong></td>
                        <td>{{ $note->creator->name ?? '-' }}</td>
                        <td>{{ $note->assignedTo->name ?? 'Todos' }}</td>
                        <td>
                            @if($note->priority == 'urgent')
                                <span class="badge badge-danger">Urgente</span>
                            @elseif($note->priority == 'high')
                                <span class="badge badge-warning">Alta</span>
                            @elseif($note->priority == 'normal')
                                <span class="badge badge-info">Normal</span>
                            @else
                                <span class="badge badge-secondary">Baixa</span>
                            @endif
                        </td>
                        <td>{{ $note->category ?? '-' }}</td>
                        <td>{{ $note->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('staff-notes.show', $note) }}" class="btn btn-action btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                            @if($note->created_by === auth()->id())
                            <a href="{{ route('staff-notes.edit', $note) }}" class="btn btn-action btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('staff-notes.destroy', $note) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action btn-danger" title="Excluir" onclick="return confirm('Excluir nota?')"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $notes->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhuma nota interna encontrada.</p>
        @endif
    </div>
</div>
@endsection
