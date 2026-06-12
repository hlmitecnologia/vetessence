@extends('layouts.adminlte', ['title' => 'Editar Exame'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('exams.update', $exam) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo</label>
                        <input type="text" name="type" value="{{ $exam->type }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach(['requested', 'collected', 'analyzing', 'ready', 'delivered', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $exam->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Resultado</label>
                                <input type="date" name="result_date" value="{{ $exam->result_date ? $exam->result_date->format('Y-m-d') : '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Laboratório</label>
                                <input type="text" name="lab_name" value="{{ $exam->lab_name }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Resultado</label>
                        <textarea name="result" rows="4" class="wysiwyg form-control">{!! $exam->result !!}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="wysiwyg form-control">{!! $exam->notes !!}</textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
