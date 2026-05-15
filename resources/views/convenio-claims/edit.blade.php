@extends('layouts.adminlte', ['title' => 'Editar Solicitação'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('convenio-claims.update', $convenioClaim) }}">@csrf @method('PUT')
            <div class="form-group"><label>Status</label>
                <select name="status" class="form-control" required>
                    @foreach(['draft'=>'Rascunho','filed'=>'Enviado','approved'=>'Aprovado','rejected'=>'Rejeitado'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('status', $convenioClaim->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="form-group"><label>Valor Aprovado</label><input name="amount_approved" type="number" step="0.01" class="form-control" value="{{ old('amount_approved', $convenioClaim->amount_approved) }}"></div>
            <div class="form-group"><label>Observações</label><textarea name="notes" class="form-control">{{ old('notes', $convenioClaim->notes) }}</textarea></div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div></div>
@endsection
