@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-sync-alt"></i> Atualizacao do Sistema</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Configuracao do Git</h5></div>
                <div class="card-body">
                    <form action="{{ route('system-update.token') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>GitHub Token</label>
                            <input type="password" name="github_token" class="form-control" value="{{ $token }}" placeholder="ghp_...">
                            <small class="text-muted">Token com permissao de leitura no repositorio.</small>
                        </div>
                        <div class="form-group">
                            <label>Repositorio</label>
                            <input type="text" name="github_repo" class="form-control" value="{{ $repo }}">
                        </div>
                        <div class="form-group">
                            <label>Branch</label>
                            <input type="text" name="github_branch" class="form-control" value="{{ $branch }}">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </form>
                </div>
            </div>

            @if($token)
            <div class="card">
                <div class="card-header"><h5>Status</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Commit atual</dt>
                        <dd class="col-sm-7"><code>{{ $currentHash }}</code></dd>

                        @if($remoteHash)
                        <dt class="col-sm-5">Commit remoto</dt>
                        <dd class="col-sm-7"><code>{{ Str::limit($remoteHash, 20) }}</code></dd>
                        @endif

                        @if($behind !== null)
                        <dt class="col-sm-5">Atraso</dt>
                        <dd class="col-sm-7">
                            @if($behind > 0)
                                <span class="badge badge-warning">{{ $behind }} commit(s) atras</span>
                            @else
                                <span class="badge badge-success">Atualizado</span>
                            @endif
                        </dd>
                        @endif
                    </dl>

                    <div class="btn-group w-100">
                        <a href="{{ route('system-update.check') }}" class="btn btn-info"><i class="fas fa-search"></i> Verificar</a>
                        @if($behind > 0)
                        <form action="{{ route('system-update.apply') }}" method="POST" class="d-inline" onsubmit="return confirm('Aplicar atualizacao? O sistema entrara em manutencao durante o processo.');">
                            @csrf
                            <button class="btn btn-success"><i class="fas fa-play"></i> Aplicar</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Historico de Atualizacoes</h5></div>
                <div class="card-body p-0">
                    @forelse($logs as $log)
                        <pre class="m-0 p-3 border-bottom" style="font-size:12px; max-height:300px; overflow-y:auto;">{{ $log }}</pre>
                    @empty
                        <p class="text-muted p-3">Nenhuma atualizacao registrada.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
