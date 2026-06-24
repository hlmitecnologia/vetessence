@extends('layouts.adminlte', ['title' => 'Exportar NF-e'])

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        @if(isset($nfeInvoices))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">NF-e Exportadas</h3>
            </div>
            <div class="card-body">
                @if($nfeInvoices->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>NF-e</th>
                            <th>Fatura</th>
                            <th>Tutor</th>
                            <th>Valor</th>
                            <th>Emissão</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nfeInvoices as $nfe)
                        <tr>
                            <td>{{ $nfe->nfe_number ?? '-' }}</td>
                            <td>{{ $nfe->invoice->invoice_number ?? '-' }}</td>
                            <td>{{ $nfe->invoice->tutor->name ?? '-' }}</td>
                            <td>R$ {{ number_format($nfe->invoice->total ?? 0, 2, ',', '.') }}</td>
                            <td data-order="{{ $nfe->issuance_date ? $nfe->issuance_date->format('Y-m-d') : '' }}">{{ $nfe->issuance_date ? $nfe->issuance_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted">Nenhum resultado para o período.</p>
                @endif
                <a href="{{ route('nfe.export-form') }}" class="btn btn-default mt-3">
                    <i class="fas fa-arrow-left"></i> Nova consulta
                </a>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exportar NF-e</h3>
            </div>
            <form action="{{ route('nfe.export') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Início *</label>
                                <input type="date" name="date_from" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Fim *</label>
                                <input type="date" name="date_to" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
