@extends('layouts.adminlte', ['title' => 'Editar Cirurgia'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('surgeries.update', $surgery) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data</label>
                                <input type="datetime-local" name="scheduled_date" value="{{ \Carbon\Carbon::parse($surgery->scheduled_date)->format('Y-m-d\TH:i') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo</label>
                                <input type="text" name="surgery_type" value="{{ $surgery->surgery_type }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    @foreach(['scheduled', 'pre_op', 'in_progress', 'post_op', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ $surgery->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duração (min)</label>
                                <input type="number" name="surgery_duration" value="{{ $surgery->surgery_duration }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notas Pós-op</label>
                        <textarea name="post_op_notes" rows="2" class="form-control">{{ $surgery->post_op_notes }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Complicações</label>
                        <textarea name="complications" rows="2" class="form-control">{{ $surgery->complications }}</textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('surgeries.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
