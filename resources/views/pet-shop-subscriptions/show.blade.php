@extends('layouts.adminlte', ['title' => 'Assinatura - ' . $petShopSubscription->pet->name])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $petShopSubscription->pet->name }} — {{ $petShopSubscription->package->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('pet-shop-subscriptions.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Usos</span>
                                <span class="info-box-number">{{ $petShopSubscription->remaining_uses }}/{{ $petShopSubscription->total_uses }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Economia Total</span>
                                <span class="info-box-number">R$ {{ number_format($petShopSubscription->total_savings, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Validade</span>
                                <span class="info-box-number">{{ $petShopSubscription->end_date?->format('d/m/Y') ?? 'Indeterminado' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-{{ $petShopSubscription->status === 'active' ? 'primary' : 'secondary' }}">
                            <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status</span>
                                <span class="info-box-number">{{ ucfirst($petShopSubscription->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($petShopSubscription->status === 'active' && $petShopSubscription->remaining_uses > 0)
                <hr>
                <h5>Registrar Consumo</h5>
                <form action="{{ route('pet-shop-consumptions.store') }}" method="POST" class="form-inline mb-3">
                    @csrf
                    <input type="hidden" name="subscription_id" value="{{ $petShopSubscription->id }}">
                    <select name="service_id" class="form-control mr-2" required>
                        <option value="">Selecione o serviço</option>
                        @foreach($petShopSubscription->package->services ?? [] as $svc)
                            <option value="{{ $svc['service_id'] ?? '' }}">{{ $svc['name'] ?? 'Serviço #' . ($svc['service_id'] ?? '') }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="service_date" class="form-control mr-2" value="{{ date('Y-m-d') }}" required>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Consumir</button>
                </form>
                @endif

                <hr>
                <h5>Histórico de Consumo</h5>
                @if($petShopSubscription->consumptions->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Serviço</th>
                            <th>Economia</th>
                            <th>Responsável</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($petShopSubscription->consumptions->sortByDesc('service_date') as $c)
                        <tr>
                            <td>{{ $c->service_date->format('d/m/Y') }}</td>
                            <td>{{ $c->service->name ?? '-' }}</td>
                            <td class="text-success">R$ {{ number_format($c->savings_amount, 2, ',', '.') }}</td>
                            <td>{{ $c->user->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-center text-muted">Nenhum consumo registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
