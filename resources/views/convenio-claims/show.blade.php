@extends('layouts.adminlte', ['title' => 'Solicitação de Reembolso'])
@section('content')
    <div class="card"><div class="card-body">
        <p><strong>Convênio:</strong> {{ optional($convenioClaim->convenioPet->convenio)->name ?? '-' }}</p>
        <p><strong>Pet:</strong> {{ optional($convenioClaim->convenioPet->pet)->name ?? '-' }}</p>
        <p><strong>Valor Solicitado:</strong> R$ {{ number_format($convenioClaim->amount_requested, 2, ',', '.') }}</p>
        <p><strong>Valor Aprovado:</strong> R$ {{ number_format($convenioClaim->amount_approved ?? 0, 2, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ $convenioClaim->status }}</p>
        <p><strong>Protocolo:</strong> {{ $convenioClaim->claim_number }}</p>
        @if($convenioClaim->filed_at)<p><strong>Data de Envio:</strong> {{ $convenioClaim->filed_at->format('d/m/Y H:i') }}</p>@endif
        <a href="{{ route('convenio-claims.edit', $convenioClaim) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('convenio-claims.index') }}" class="btn btn-secondary">Voltar</a>
    </div></div>
@endsection
