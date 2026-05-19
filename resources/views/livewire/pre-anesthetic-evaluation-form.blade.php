<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label>Pet *</label>
            <x-tom-select wire="pet_id" :value="$pet_id" required>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                @endforeach
            </x-tom-select>
            @error('pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Classificação ASA *</label>
                    <select wire:model="asa_score" class="form-control @error('asa_score') is-invalid @enderror" required>
                        <option value="1">ASA I - Saudável</option>
                        <option value="2">ASA II - Doença sistêmica leve</option>
                        <option value="3">ASA III - Doença sistêmica grave</option>
                        <option value="4">ASA IV - Risco de vida</option>
                        <option value="5">ASA V - Crítico</option>
                        <option value="6">ASA VI - Doador de órgãos</option>
                    </select>
                    @error('asa_score') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status *</label>
                    <select wire:model="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="pending">Pendente</option>
                        <option value="approved">Aprovado</option>
                        <option value="rejected">Rejeitado</option>
                    </select>
                    @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" wire:model="fasted" class="custom-control-input" id="paeFasted">
                    <label class="custom-control-label" for="paeFasted">Jejum Realizado</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" wire:model="hydrated" class="custom-control-input" id="paeHydrated">
                    <label class="custom-control-label" for="paeHydrated">Hidratado</label>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <label>Exames Realizados</label>
            <div class="row">
                @foreach($examOptions as $key => $label)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" wire:model="exam_checklist" value="{{ $key }}" class="custom-control-input" id="exam_{{ $key }}">
                        <label class="custom-control-label" for="exam_{{ $key }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model="observations" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label>Recomendações</label>
            <textarea wire:model="recommendations" class="form-control" rows="2"></textarea>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
