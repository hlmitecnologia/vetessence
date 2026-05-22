@extends('layouts.adminlte', ['title' => 'Editar Fatura'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Vencimento</label>
                        <input type="date" name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}" class="form-control">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
