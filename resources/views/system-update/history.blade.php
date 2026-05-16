@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-history"></i> Historico de Atualizacoes</h4>
        <a href="{{ route('system-update.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            @forelse($logs as $log)
                <pre class="m-0 p-3 border-bottom" style="font-size:12px; white-space:pre-wrap;">{{ $log }}</pre>
            @empty
                <p class="text-muted p-3 text-center">Nenhuma atualizacao registrada.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
