<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>CNPJ</label>
                    <input type="text" wire:model="cnpj" class="form-control" placeholder="00.000.000/0000-00">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome do Plano</label>
                    <input type="text" wire:model="plan_name" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Desconto (%)</label>
                    <input type="number" wire:model="discount_percent" class="form-control" min="0" max="100" step="0.01">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Máx. Consultas/Mês</label>
                    <input type="number" wire:model="max_consults_month" class="form-control" min="1">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Cobertura</label>
            <textarea wire:model="coverage" class="wysiwyg form-control" rows="2"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data Início</label>
                    <input type="date" wire:model="start_date" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data Fim</label>
                    <input type="date" wire:model="end_date" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" wire:model="is_active" class="custom-control-input" id="convIsActive">
                <label class="custom-control-label" for="convIsActive">Ativo</label>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
