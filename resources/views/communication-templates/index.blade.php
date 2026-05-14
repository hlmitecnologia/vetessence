@extends('layouts.adminlte', ['title' => 'Modelos de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modelos de Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-templates.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Modelo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($templates->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Canal</th>
                    <th>Assunto</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>
                        @php
                            $typeLabels = ['reminder' => 'Lembrete', 'recall' => 'Rechamada', 'promotional' => 'Promocional', 'notification' => 'Notificação', 'other' => 'Outro'];
                        @endphp
                        <span class="badge badge-info">{{ $typeLabels[$template->type] ?? $template->type }}</span>
                    </td>
                    <td>
                        @php
                            $channelLabels = ['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'];
                            $channelIcons = ['whatsapp' => 'fab fa-whatsapp', 'email' => 'fas fa-envelope', 'sms' => 'fas fa-sms', 'push' => 'fas fa-bell'];
                        @endphp
                        @if($template->channel)
                            <i class="{{ $channelIcons[$template->channel] ?? 'fas fa-comment' }}"></i>
                            {{ $channelLabels[$template->channel] ?? $template->channel }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $template->subject ?? '-' }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('communication-templates.edit', $template) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('communication-templates.destroy', $template) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum modelo de comunicação encontrado.</p>
        @endif
    </div>
</div>
@endsection
