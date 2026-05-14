@extends('layouts.adminlte', ['title' => 'Nota Interna'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $staffNote->title }}</h3>
        <div class="card-tools">
            @if($staffNote->created_by === auth()->id())
                <a href="{{ route('staff-notes.edit', $staffNote) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            @endif
            <a href="{{ route('staff-notes.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>De:</strong><p>{{ $staffNote->creator->name ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Para:</strong><p>{{ $staffNote->assignedTo->name ?? 'Todos' }}</p></div>
            <div class="col-md-3"><strong>Prioridade:</strong><p>
                @if($staffNote->priority == 'urgent') <span class="badge badge-danger">Urgente</span>
                @elseif($staffNote->priority == 'high') <span class="badge badge-warning">Alta</span>
                @elseif($staffNote->priority == 'normal') <span class="badge badge-info">Normal</span>
                @else <span class="badge badge-secondary">Baixa</span> @endif
            </p></div>
            <div class="col-md-3"><strong>Categoria:</strong><p>{{ $staffNote->category ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Data:</strong><p>{{ $staffNote->created_at->format('d/m/Y H:i') }}</p></div>
            <div class="col-md-6">
                <strong>Status:</strong>
                <p>
                    @if($staffNote->is_read)
                        <span class="badge badge-success">Lida em {{ optional($staffNote->read_at)->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="badge badge-warning">Não lida</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Conteúdo:</strong>
                <div class="p-3 bg-light rounded border mt-2" style="white-space: pre-wrap;">
                    {{ $staffNote->content }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
