@extends('layouts.adminlte', ['title' => 'Termos de Consentimento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Termos de Consentimento</h3>
        <div class="card-tools">
            <a href="{{ route('consent-forms.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Termo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($consentForms->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº do Termo</th>
                    <th>Pet</th>
                    <th>Tutor</th>
                    <th>Modelo</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consentForms as $form)
                <tr>
                    <td><strong>{{ $form->consent_number }}</strong></td>
                    <td>{{ $form->pet->name ?? '-' }}</td>
                    <td>{{ $form->tutor->name ?? '-' }}</td>
                    <td>{{ $form->template->name ?? '-' }}</td>
                    <td>
                        @php
                            $statusLabels = ['draft' => 'Rascunho', 'signed' => 'Assinado', 'cancelled' => 'Cancelado'];
                            $statusColors = ['draft' => 'secondary', 'signed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$form->status] ?? 'secondary' }}">
                            {{ $statusLabels[$form->status] ?? $form->status }}
                        </span>
                    </td>
                    <td>{{ $form->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('consent-forms.show', $form) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('consent-forms.edit', $form) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum termo de consentimento encontrado.</p>
        @endif
    </div>
</div>
@endsection
