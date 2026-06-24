@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-sync-alt"></i> Atualização do Sistema</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Configuração do Git</h5></div>
                <div class="card-body">
                    <form action="{{ route('system-update.token') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>GitHub Token</label>
                            <input type="password" name="github_token" class="form-control"
                                placeholder="{{ $hasToken ? 'Token já configurado. Deixe vazio para manter.' : 'ghp_... (token do GitHub)' }}">
                            <small class="text-muted">Token com permissão de leitura no repositório.</small>
                        </div>
                        <div class="form-group">
                            <label>Repositório</label>
                            <input type="text" name="github_repo" class="form-control"
                                placeholder="{{ $hasRepo ? $repo . ' (já configurado)' : config('update.repo') }}">
                            <small class="text-muted">Deixe vazio para manter o atual.</small>
                        </div>
                        <div class="form-group">
                            <label>Branch</label>
                            <input type="text" name="github_branch" class="form-control"
                                placeholder="{{ $hasBranch ? $branch . ' (já configurado)' : config('update.branch') }}">
                            <small class="text-muted">Deixe vazio para manter a atual.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>Licença</h5></div>
                <div class="card-body">
                    <form action="{{ route('system-update.license') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Chave de Licença</label>
                            <input type="text" name="license_key" class="form-control"
                                placeholder="{{ $licenseKey ? 'Licença configurada. Deixe vazio para manter.' : 'Insira a chave de licença' }}"
                                value="">
                            @error('license_key') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </form>
                </div>
            </div>

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
                                <span class="badge badge-warning">{{ $behind }} commit(s) atrás</span>
                            @else
                                <span class="badge badge-success">Atualizado</span>
                            @endif
                        </dd>
                        @endif
                    </dl>

                    <div class="btn-group w-100">
                        <a href="{{ route('system-update.check') }}" class="btn btn-info"><i class="fas fa-search"></i> Verificar</a>
                        @if($behind > 0)
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#applyModal">
                            <i class="fas fa-play"></i> Aplicar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Histórico de Atualizações</h5></div>
                <div class="card-body p-0">
                    @forelse($logs as $log)
                        <pre class="m-0 p-3 border-bottom" style="font-size:12px; max-height:300px; overflow-y:auto;">{{ $log }}</pre>
                    @empty
                        <p class="text-muted p-3">Nenhuma atualização registrada.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmação de senha --}}
<div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('system-update.apply') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Confirmar Atualização</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>O sistema entrará em manutenção durante a atualização. Confirme sua senha para prosseguir.</p>
                    <div class="form-group">
                        <label for="apply_password">Sua senha</label>
                        <input type="password" name="password" id="apply_password" class="form-control" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-play"></i> Aplicar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
