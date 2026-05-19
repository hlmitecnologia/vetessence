<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Vínculo Convênio/Pet *</label>
            <select wire:model="convenio_pet_id" class="form-control @error('convenio_pet_id') is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach($convenioPets as $cp)
                    <option value="{{ $cp->id }}">
                        {{ optional($cp->convenio)->name ?? 'N/A' }} - {{ optional($cp->pet)->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
            @error('convenio_pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Fatura</label>
            <select wire:model="invoice_id" class="form-control">
                <option value="">Selecione...</option>
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}">#{{ $inv->id }} - R$ {{ number_format($inv->total, 2, ',', '.') }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Valor Solicitado *</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                <input type="number" wire:model="amount_requested" class="form-control @error('amount_requested') is-invalid @enderror" step="0.01" min="0" required>
            </div>
            @error('amount_requested') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="notes" class="form-control" rows="2"></textarea>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
