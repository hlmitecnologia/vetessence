@extends('layouts.adminlte', ['title' => 'Solicitações de Reembolso'])
@section('content')
    <div class="card">
        <div class="card-header"><a href="{{ route('convenio-claims.create') }}" class="btn btn-primary">Nova Solicitação</a></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Nº</th><th>Convênio</th><th>Pet</th><th>Solicitado</th><th>Aprovado</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                @foreach($claims as $c)
                    <tr>
                        <td>{{ $c->claim_number }}</td>
                        <td>{{ optional($c->convenioPet->convenio)->name ?? '-' }}</td>
                        <td>{{ optional($c->convenioPet->pet)->name ?? '-' }}</td>
                        <td>R$ {{ number_format($c->amount_requested, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($c->amount_approved ?? 0, 2, ',', '.') }}</td>
                        <td>{{ $c->status }}</td>
                        <td><a href="{{ route('convenio-claims.show', $c) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $claims->links() }}</div>
    </div>
@endsection
