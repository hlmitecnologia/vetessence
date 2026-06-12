@php $title = isset($bankAccount) ? 'Editar Conta Bancária' : 'Nova Conta Bancária'; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ $title }}</h3></div>
            <div class="card-body">
                <form action="{{ isset($bankAccount) ? route('bank-accounts.update', $bankAccount) : route('bank-accounts.store') }}" method="POST">
                    @csrf
                    @isset($bankAccount) @method('PUT') @endisset

                    <div class="form-group">
                        <label>Banco *</label>
                        <input type="text" name="bank" class="form-control @error('bank') is-invalid @enderror" value="{{ old('bank', $bankAccount->bank ?? '') }}" required>
                        @error('bank') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agência *</label>
                                <input type="text" name="agency" class="form-control @error('agency') is-invalid @enderror" value="{{ old('agency', $bankAccount->agency ?? '') }}" required>
                                @error('agency') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conta *</label>
                                <input type="text" name="account" class="form-control @error('account') is-invalid @enderror" value="{{ old('account', $bankAccount->account ?? '') }}" required>
                                @error('account') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="account_type" class="form-control">
                            <option value="checking" @selected(old('account_type', $bankAccount->account_type ?? '') === 'checking')>Corrente</option>
                            <option value="savings" @selected(old('account_type', $bankAccount->account_type ?? '') === 'savings')>Poupança</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Unidade</label>
                        <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                            <option value="">Selecione uma unidade</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id', $bankAccount->branch_id ?? '') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="description" class="wysiwyg form-control" rows="2">{{ old('description', $bankAccount->description ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" @checked(old('is_active', $bankAccount->is_active ?? true))>
                            <label class="custom-control-label" for="isActive">Ativa</label>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('bank-accounts.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
