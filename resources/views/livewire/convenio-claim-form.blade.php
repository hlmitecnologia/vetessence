<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Vínculo Convênio/Pet *</label>
            <x-tom-select wire="convenio_pet_id" :value="$convenio_pet_id" required>
                @foreach($convenioPets as $cp)
                    <option value="{{ $cp->id }}">
                        {{ optional($cp->convenio)->name ?? 'N/A' }} - {{ optional($cp->pet)->name ?? 'N/A' }}
                    </option>
                @endforeach
            </x-tom-select>
            @error('convenio_pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Fatura</label>
            <x-tom-select wire="invoice_id" :value="$invoice_id">
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}">#{{ $inv->id }} - R$ {{ number_format($inv->total, 2, ',', '.') }}</option>
                @endforeach
            </x-tom-select>
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
            <textarea wire:model="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="2"></textarea>
            @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
