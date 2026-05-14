@extends('layouts.adminlte', ['title' => 'Relatório Financeiro'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <div class="form-group mx-2">
                <label class="mx-2">Data Início:</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="form-group mx-2">
                <label class="mx-2">Data Fim:</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary mx-2"><i class="fas fa-filter"></i> Filtrar</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>R$ {{ number_format($totalRevenue, 2, ',', '.') }}</h3>
                <p>Receita Total</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>R$ {{ number_format($totalPending, 2, ',', '.') }}</h3>
                <p>A Receber</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>R$ {{ number_format($totalOverdue, 2, ',', '.') }}</h3>
                <p>Vencidas</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>R$ {{ number_format($totalDiscounts, 2, ',', '.') }}</h3>
                <p>Descontos</p>
            </div>
            <div class="icon"><i class="fas fa-percent"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Receita por Forma de Pagamento</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Forma</th>
                            <th class="text-right">Valor</th>
                            <th class="text-right">Qtd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByPaymentMethod as $method)
                        @php
                            $methodLabels = [
                                'pix' => 'PIX',
                                'dinheiro' => 'Dinheiro',
                                'cartao_credito' => 'Cartão de Crédito',
                                'cartao_debito' => 'Cartão de Débito',
                                'boleto' => 'Boleto',
                                'transferencia' => 'Transferência',
                                'convenio' => 'Convênio'
                            ];
                            $label = $methodLabels[$method->payment_method] ?? $method->payment_method;
                        @endphp
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="text-right">R$ {{ number_format($method->total, 2, ',', '.') }}</td>
                            <td class="text-right">{{ $method->count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">Nenhum registro</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Atendimentos por Status</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusLabels = [
                                'scheduled' => 'Agendados',
                                'confirmed' => 'Confirmados',
                                'in_progress' => 'Em Andamento',
                                'completed' => 'Concluídos',
                                'cancelled' => 'Cancelados',
                                'no_show' => 'Faltantes'
                            ];
                            $statusColors = [
                                'scheduled' => 'badge-primary',
                                'confirmed' => 'badge-indigo',
                                'in_progress' => 'badge-warning',
                                'completed' => 'badge-success',
                                'cancelled' => 'badge-danger',
                                'no_show' => 'badge-secondary'
                            ];
                        @endphp
                        @forelse($appointmentStats as $status => $stat)
                        <tr>
                            <td><span class="badge {{ $statusColors[$status] ?? 'badge-secondary' }}">{{ $statusLabels[$status] ?? $status }}</span></td>
                            <td class="text-right">{{ $stat->count }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center">Nenhum registro</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Principais Clientes</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <tbody>
                        @forelse($topClients as $client)
                        <tr>
                            <td>{{ $client->name }}</td>
                            <td class="text-right">R$ {{ number_format($client->total, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td class="text-center">Nenhum registro</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Serviços Mais Rentáveis</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <tbody>
                        @forelse($serviceStats as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td class="text-right">R$ {{ number_format($service->revenue, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td class="text-center">Nenhum registro</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estatísticas</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td>Vacinas</td><td class="text-right"><span class="badge badge-success">{{ $vaccinationStats }}</span></td></tr>
                    <tr><td>Exames</td><td class="text-right"><span class="badge badge-info">{{ $examStats }}</span></td></tr>
                    <tr><td>Cirurgias</td><td class="text-right"><span class="badge badge-purple">{{ $surgeryStats }}</span></td></tr>
                    <tr><td>Total de Pets</td><td class="text-right">{{ $totalPets }}</td></tr>
                    <tr><td>Pets Ativos</td><td class="text-right">{{ $activePets }}</td></tr>
                    <tr><td>Novos Pets</td><td class="text-right">{{ $newPets }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($lowStockProducts->count() > 0)
<div class="card border-danger">
    <div class="card-header bg-danger">
        <h3 class="card-title text-white"><i class="fas fa-exclamation-triangle"></i> Produtos com Estoque Baixo</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr><th>Produto</th><th class="text-right">Estoque</th><th class="text-right">Mínimo</th></tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr class="text-danger">
                    <td>{{ $product->name }}</td>
                    <td class="text-right">{{ $product->stock }}</td>
                    <td class="text-right">{{ $product->min_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($overdueInvoices->count() > 0)
<div class="card border-warning">
    <div class="card-header bg-warning">
        <h3 class="card-title"><i class="fas fa-clock"></i> Faturas Vencidas</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr><th>Fatura</th><th>Cliente</th><th class="text-right">Valor</th><th>Vencimento</th></tr>
            </thead>
            <tbody>
                @foreach($overdueInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->tutor->name ?? '-' }}</td>
                    <td class="text-right">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($zoonosisStats->count() > 0)
<div class="card">
    <div class="card-header" style="background: #dc3545; color: white;">
        <h3 class="card-title"><i class="fas fa-biohazard"></i> Doenças Zoonóticas no Período</h3>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> {{ $notifiableCount }} doenças de notificação obrigatória registradas</span>
        </p>
        <table class="table table-striped">
            <thead>
                <tr><th>Doença</th><th class="text-right">Registros</th></tr>
            </thead>
            <tbody>
                @foreach($zoonosisStats as $stat)
                <tr>
                    <td>{{ $stat->name }}</td>
                    <td class="text-right">{{ $stat->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
