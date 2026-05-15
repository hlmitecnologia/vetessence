@extends('layouts.adminlte', ['title' => 'Editar Triagem'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('triage.update', $triage) }}">@csrf @method('PUT')
            <div class="form-group"><label>Severidade</label>
                <select name="severity" class="form-control" required>
                    @foreach(['green'=>'Verde','yellow'=>'Amarela','orange'=>'Laranja','red'=>'Vermelho'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('severity', $triage->severity) == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="form-group"><label>Status</label>
                <select name="status" class="form-control" required>
                    @foreach(['waiting'=>'Aguardando','in_consultation'=>'Em Consulta','seen'=>'Atendido','discharged'=>'Liberado'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('status', $triage->status) == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="form-group"><label>Queixa</label><textarea name="chief_complaint" class="form-control">{{ old('chief_complaint', $triage->chief_complaint) }}</textarea></div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div></div>
@endsection
