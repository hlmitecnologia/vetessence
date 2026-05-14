@extends('layouts.adminlte', ['title' => 'Backups'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Backups do Sistema</h3>
        <div class="card-tools">
            <a href="{{ route('backups.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Backup
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($files->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome do Arquivo</th>
                    <th>Tamanho</th>
                    <th>Data</th>
                    <th style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $backup)
                <tr>
                    <td>{{ $backup->name ?? basename($backup->path ?? $backup) }}</td>
                    <td>
                        @php
                            $size = $backup->size ?? (file_exists($backup->path ?? $backup) ? filesize($backup->path ?? $backup) : 0);
                            if ($size >= 1073741824) {
                                $formatted = number_format($size / 1073741824, 2) . ' GB';
                            } elseif ($size >= 1048576) {
                                $formatted = number_format($size / 1048576, 2) . ' MB';
                            } elseif ($size >= 1024) {
                                $formatted = number_format($size / 1024, 2) . ' KB';
                            } else {
                                $formatted = $size . ' B';
                            }
                        @endphp
                        {{ $formatted }}
                    </td>
                    <td>{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('backups.download', $backup) }}" class="btn btn-action btn-success" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <form action="{{ route('backups.destroy', $backup) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este backup?')" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum backup encontrado.</p>
        @endif
    </div>
</div>
@endsection
