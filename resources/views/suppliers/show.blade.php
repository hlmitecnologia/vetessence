@extends('layouts.adminlte', ['title' => $supplier->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">CNPJ</small>
                        <p>{{ $supplier->cnpj ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Telefone</small>
                        <p>{{ $supplier->phone ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Email</small>
                        <p>{{ $supplier->email ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Contato</small>
                        <p>{{ $supplier->contact ?? '-' }}</p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-uppercase">Endereço</small>
                        <p>{{ $supplier->address ? $supplier->address . ', ' . $supplier->city . ' - ' . $supplier->state : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection
