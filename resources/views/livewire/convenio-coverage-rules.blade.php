<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Regras de Cobertura</span>
            <button type="button" class="btn btn-sm btn-success" wire:click="addRule">
                <i class="fas fa-plus"></i> Nova Regra
            </button>
        </div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="saveRules">
                @forelse ($rules as $i => $rule)
                    <div class="card bg-light mb-2">
                        <div class="card-body py-2">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <label class="small">Tipo</label>
                                    <select wire:model="rules.{{ $i }}.item_type" class="form-control form-control-sm">
                                        @foreach ($itemTypes as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Serviço (opcional)</label>
                                    <select wire:model="rules.{{ $i }}.service_id" class="form-control form-control-sm">
                                        <option value="">Qualquer</option>
                                        @foreach ($services as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Cobertura %</label>
                                    <input type="number" wire:model="rules.{{ $i }}.coverage_percent" class="form-control form-control-sm @error('rules.'.$i.'.coverage_percent') is-invalid @enderror" min="0" max="100" step="0.01">
                                    @error('rules.'.$i.'.coverage_percent') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Valor Máx. (R$)</label>
                                    <input type="number" wire:model="rules.{{ $i }}.max_value" class="form-control form-control-sm" min="0" step="0.01" placeholder="Ilimitado">
                                </div>
                                <div class="col-md-2">
                                    <div class="custom-control custom-switch mt-3">
                                        <input type="checkbox" wire:model="rules.{{ $i }}.requires_pre_authorization" class="custom-control-input" id="preAuth_{{ $i }}">
                                        <label class="custom-control-label small" for="preAuth_{{ $i }}">Pré-autorização</label>
                                    </div>
                                </div>
                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-sm btn-danger mt-3" wire:click="removeRule({{ $i }})" wire:confirm="Remover regra?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nenhuma regra de cobertura definida. Clique em "Nova Regra" para adicionar.</p>
                @endforelse

                @if (count($rules) > 0)
                    <div class="text-right mt-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Regras</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
