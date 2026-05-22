<div>
    <form wire:submit.prevent="save">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informações da Consulta</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pet *</label>
                            <x-tom-select wire="pet_id" :value="$pet_id" required>
                                @foreach($pets as $pet)
                                <option value="{{ $pet->id }}">
                                    {{ $pet->name }}
                                    @if($pet->tutors->first()) - {{ $pet->tutors->first()->name }} @endif
                                </option>
                                @endforeach
                            </x-tom-select>
                            @error('pet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Veterinário *</label>
                            <x-tom-select wire="vet_id" :value="$vet_id" required>
                                @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                                @endforeach
                            </x-tom-select>
                            @error('vet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data *</label>
                            <input type="date" wire:model="date" required class="form-control">
                            @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hora *</label>
                            <input type="time" wire:model="time" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo *</label>
                            <select wire:model="type" required class="form-control">
                                <option value="consulta">Consulta</option>
                                <option value="retorno">Retorno</option>
                                <option value="emergencia">Emergência</option>
                                <option value="cirurgia">Cirurgia</option>
                                <option value="vacina">Vacina</option>
                                <option value="exame">Exame</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Motivo/Observações</label>
                            <textarea wire:model="reason" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Serviços</h5>
                <div class="row">
                    @foreach($services as $service)
                    <div class="col-md-4 mb-2">
                        <label class="d-flex align-items-center p-3 border rounded cursor-pointer {{ in_array($service->id, $selectedServices) ? 'border-primary bg-light' : '' }}" style="cursor:pointer">
                            <input type="checkbox" wire:model="selectedServices" value="{{ $service->id }}" class="mr-2">
                            <div>
                                <div class="font-weight-medium small">{{ $service->name }}</div>
                                <div class="small text-muted">R$ {{ number_format($service->price, 2, ',', '.') }}</div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

                @if(count($selectedServices) > 0)
                <div class="mt-3 p-3 bg-light rounded d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total estimado:</span>
                    <span class="h5 font-weight-bold text-primary">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="text-right">
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Agendar</button>
        </div>
    </form>
</div>
