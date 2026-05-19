<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Pet *</label>
            <select wire:model="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                @endforeach
            </select>
            @error('pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Data do Óbito *</label>
                    <input type="date" wire:model="death_date" class="form-control @error('death_date') is-invalid @enderror" required>
                    @error('death_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Destinação</label>
                    <select wire:model="disposition" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="cremation">Cremação</option>
                        <option value="burial">Sepultamento</option>
                        <option value="donation">Doação</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Causa</label>
            <input type="text" wire:model="cause" class="form-control" maxlength="255">
        </div>

        <div class="form-group">
            <label>Veterinário Responsável</label>
            <input type="text" wire:model="attending_vet" class="form-control" maxlength="255">
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
