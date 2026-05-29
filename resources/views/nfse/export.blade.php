@php
    $title = 'Exportar NFSe';
@endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exportar XMLs para Contabilidade</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('nfse.export') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Data Inicial</label>
                        <input type="date" name="date_from" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Data Final</label>
                        <input type="date" name="date_to" class="form-control" required>
                    </div>
                    @can('branches.view')
                    <div class="form-group">
                        <label>Unidade</label>
                        <select name="branch_id" class="form-control">
                            <option value="">Todas</option>
                            @foreach(\App\Models\Branch::orderBy('name')->get() as $branch)
                            <option value="{{ $branch->id }}" @selected(auth()->user()->branch_id === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endcan
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-export"></i> Exportar ZIP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
