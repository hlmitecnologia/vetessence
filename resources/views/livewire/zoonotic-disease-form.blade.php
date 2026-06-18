<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Categoria *</label>
                    <select wire:model="category" class="form-control @error('category') is-invalid @enderror" required>
                        <option value="viral">Viral</option>
                        <option value="bacterial">Bacteriana</option>
                        <option value="parasitic">Parasitária</option>
                        <option value="fungal">Fúngica</option>
                        <option value="prion">Prion</option>
                    </select>
                    @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Agente Causador</label>
            <input type="text" wire:model="causative_agent" class="form-control">
        </div>

        <div class="form-group">
            <label>Transmissão</label>
            <textarea wire:model="transmission" class="wysiwyg form-control @error('transmission') is-invalid @enderror" rows="2"></textarea>
            @error('transmission') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Sintomas em Animais</label>
                    <textarea wire:model="animal_symptoms" class="wysiwyg form-control @error('animal_symptoms') is-invalid @enderror" rows="3"></textarea>
                    @error('animal_symptoms') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Sintomas em Humanos</label>
                    <textarea wire:model="human_symptoms" class="wysiwyg form-control @error('human_symptoms') is-invalid @enderror" rows="3"></textarea>
                    @error('human_symptoms') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Período de Incubação</label>
            <input type="text" wire:model="incubation_period" class="form-control" placeholder="Ex: 2-14 dias">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Prevenção</label>
                    <textarea wire:model="prevention" class="wysiwyg form-control @error('prevention') is-invalid @enderror" rows="2"></textarea>
                    @error('prevention') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tratamento</label>
                    <textarea wire:model="treatment" class="wysiwyg form-control @error('treatment') is-invalid @enderror" rows="2"></textarea>
                    @error('treatment') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Espécies Afetadas</label>
            <div class="row">
                @foreach($speciesOptions as $sp)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" wire:model="species_affected" value="{{ $sp }}" class="custom-control-input" id="sp_{{ $sp }}">
                        <label class="custom-control-label" for="sp_{{ $sp }}">@lang('species.' . $sp)</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" wire:model="is_notifiable" class="custom-control-input" id="zdNotifiable">
                    <label class="custom-control-label" for="zdNotifiable">Notificação Obrigatória</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="custom-control custom-switch">
                    <input type="checkbox" wire:model="is_active" class="custom-control-input" id="zdIsActive">
                    <label class="custom-control-label" for="zdIsActive">Ativo</label>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
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
