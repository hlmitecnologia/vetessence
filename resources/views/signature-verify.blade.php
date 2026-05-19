@extends('layouts.guest')

@section('title', 'Verificação de Assinatura Digital')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: #f4f6f9;">
    <div class="card shadow-sm" style="max-width: 500px; width: 100%;">
        <div class="card-body text-center p-5">
            @if($valid)
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                </div>
                <h4 class="text-success font-weight-bold mb-3">Assinatura Válida</h4>
                <p class="text-muted mb-2">Este documento foi assinado digitalmente e seu conteúdo <strong>não foi alterado</strong> desde a assinatura.</p>
                <p class="text-muted mb-4">
                    <small>
                        Assinado em: {{ \Carbon\Carbon::parse($signedAt)->format('d/m/Y H:i:s') }}<br>
                        @if($model === 'prescription')
                            Prescrição #{{ $record->id }}
                        @else
                            Prontuário #{{ $record->id }}
                        @endif
                    </small>
                </p>
                <div class="bg-light rounded p-3 mb-3">
                    <small class="text-muted">Hash de verificação: <code class="text-dark">{{ $record->content_hash }}</code></small>
                </div>
            @else
                <div class="mb-4">
                    <i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
                </div>
                <h4 class="text-danger font-weight-bold mb-3">Assinatura Inválida</h4>
                <p class="text-muted mb-4">{{ $message ?? 'O conteúdo deste documento foi alterado ou a assinatura não é válida.' }}</p>
            @endif

            <hr>
            <p class="text-muted mb-0">
                <small>
                    <i class="fas fa-shield-alt mr-1"></i>
                    Verificação baseada em SHA-256 — {{ config('app.name', 'VetEssence') }}
                </small>
            </p>
        </div>
    </div>
</div>
@endsection
