<div>
    <form wire:submit.prevent="save">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informações Básicas</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pet *</label>
                            <x-tom-select wire="pet_id" :value="$pet_id" required>
                                @foreach($pets as $pet)
                                <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->tutors->first()->name ?? '' }}</option>
                                @endforeach
                            </x-tom-select>
                            @error('pet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo *</label>
                            <select wire:model="type" required class="form-control">
                                <option value="">Selecione...</option>
                                <option value="consulta">Consulta</option>
                                <option value="cirurgia">Cirurgia</option>
                                <option value="emergencia">Emergência</option>
                                <option value="vacina">Vacina</option>
                                <option value="retorno">Retorno</option>
                                <option value="exame">Exame</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Data *</label>
                            <input type="date" wire:model="date" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hora *</label>
                            <input type="time" wire:model="time" required class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Sinais Vitais</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Temperatura</label>
                            <input type="text" wire:model="vital_signs.temperature" placeholder="ºC" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Frequência Cardíaca</label>
                            <input type="text" wire:model="vital_signs.heart_rate" placeholder="bpm" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Frequência Respiratória</label>
                            <input type="text" wire:model="vital_signs.respiratory_rate" placeholder="mrm" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Peso</label>
                            <input type="text" wire:model="vital_signs.weight" placeholder="kg" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mucosas</label>
                            <input type="text" wire:model="vital_signs.mucosa" placeholder="Normocoradas" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hidratação</label>
                            <input type="text" wire:model="vital_signs.hydration" placeholder="Normal" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Linfonodos</label>
                            <input type="text" wire:model="vital_signs.lymph_nodes" placeholder="Normais" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Anamnese e Exame Físico</h5>
                <div class="form-group">
                    <label>Queixa Principal</label>
                    <textarea wire:model="chief_complaint" rows="2" class="wysiwyg form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Anamnese</label>
                    <textarea wire:model="anamnesis" rows="3" class="wysiwyg form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Exame Físico</label>
                    <textarea wire:model="physical_exam" rows="3" class="wysiwyg form-control"></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Diagnóstico e Tratamento</h5>
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0">Diagnóstico</label>
                        <button type="button" class="btn btn-outline-info btn-sm" wire:click="suggestDiagnosis" wire:loading.attr="disabled" wire:target="suggestDiagnosis" title="Sugerir diagnóstico com IA">
                            <span wire:loading.remove wire:target="suggestDiagnosis"><i class="fas fa-robot"></i> Sugerir (IA)</span>
                            <span wire:loading wire:target="suggestDiagnosis"><i class="fas fa-spinner fa-spin"></i> Analisando...</span>
                        </button>
                    </div>
                    <textarea wire:model="diagnosis" rows="2" class="wysiwyg form-control" style="resize:vertical;"></textarea>
                    @if($suggestionError)
                        <small class="text-danger">{{ $suggestionError }}</small>
                    @endif
                </div>
                <div class="form-group">
                    <label>Tratamento</label>
                    <textarea wire:model="treatment" rows="3" class="wysiwyg form-control"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prognóstico</label>
                            <select wire:model="prognosis" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="bom">Bom</option>
                                <option value="reservado">Reservado</option>
                                <option value="grave">Grave</option>
                                <option value="obito">Óbito</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea wire:model="notes" rows="2" class="wysiwyg form-control"></textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-biohazard text-danger mr-2"></i>Doenças Zoonóticas</h5>
                <p class="text-muted small mb-3">Registre doenças zoonóticas associadas a este atendimento.</p>
                @foreach($selectedDiseases as $index => $sd)
                <div class="row align-items-end mb-2 p-3 bg-light rounded">
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <x-tom-select wire="selectedDiseases.{{ $index }}.disease_id" :value="$sd['disease_id'] ?? ''" placeholder="Selecione uma doença...">
                                @foreach($zoonoticDiseases as $disease)
                                <option value="{{ $disease->id }}">{{ $disease->name }}
                                    @if($disease->is_notifiable) 🔔 @endif
                                </option>
                                @endforeach
                            </x-tom-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" wire:model="selectedDiseases.{{ $index }}.is_suspected" class="form-check-input" id="disease-susp-{{ $index }}">
                            <label class="form-check-label small" for="disease-susp-{{ $index }}">Suspeito</label>
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" wire:click="removeDisease({{ $index }})" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                @endforeach
                <button type="button" wire:click="addDisease" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Adicionar Doença Zoonótica
                </button>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Prescrições</h5>
                @foreach($prescriptions as $index => $prescription)
                <div class="prescription-item row align-items-end mb-2 p-3 bg-light rounded">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Medicamento *</label>
                            <input type="text" wire:model="prescriptions.{{ $index }}.medication" class="form-control form-control-sm" placeholder="Nome do medicamento">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Dosagem</label>
                            <input type="text" wire:model="prescriptions.{{ $index }}.dosage" class="form-control form-control-sm" placeholder="Ex: 50mg">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Unidade</label>
                            <select wire:model="prescriptions.{{ $index }}.unit" class="form-control form-control-sm">
                                <option value="">...</option>
                                <option value="mg">mg</option>
                                <option value="g">g</option>
                                <option value="mL">mL</option>
                                <option value="cp">cp</option>
                                <option value="gotas">gotas</option>
                                <option value="UI">UI</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Frequência</label>
                            <input type="text" wire:model="prescriptions.{{ $index }}.frequency" class="form-control form-control-sm" placeholder="Ex: 8h">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Duração</label>
                            <input type="text" wire:model="prescriptions.{{ $index }}.duration" class="form-control form-control-sm" placeholder="Ex: 7 dias">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label class="small text-muted">Via</label>
                            <select wire:model="prescriptions.{{ $index }}.route" class="form-control form-control-sm">
                                <option value="oral">Oral</option>
                                <option value="topic">Tópico</option>
                                <option value="sc">SC</option>
                                <option value="im">IM</option>
                                <option value="iv">IV</option>
                                <option value="otologic">Otológico</option>
                                <option value="oftalmic">Oftálmico</option>
                                <option value="rectal">Retal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" wire:click="removePrescription({{ $index }})" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                @endforeach
                <button type="button" wire:click="addPrescription" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-plus mr-1"></i> Adicionar Prescrição
                </button>
                @error('prescriptions.*.medication') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="text-right">
            <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
        </div>
    </form>
</div>
