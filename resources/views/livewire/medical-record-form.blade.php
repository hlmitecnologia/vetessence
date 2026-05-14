<div>
    <form wire:submit.prevent="save" class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Informações Básicas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                    <select wire:model="pet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($pets as $pet)
                        <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->tutors->first()->name ?? '' }}</option>
                        @endforeach
                    </select>
                    @error('pet_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário *</label>
                    <select wire:model="vet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($veterinarians as $vet)
                        <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                        @endforeach
                    </select>
                    @error('vet_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select wire:model="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        <option value="consulta">Consulta</option>
                        <option value="cirurgia">Cirurgia</option>
                        <option value="emergencia">Emergência</option>
                        <option value="vacina">Vacina</option>
                        <option value="retorno">Retorno</option>
                        <option value="exame">Exame</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                    <input type="date" wire:model="date" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                    <input type="time" wire:model="time" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Sinais Vitais</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temperatura</label>
                    <input type="text" wire:model="vital_signs.temperature" placeholder="ºC" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frequência Cardíaca</label>
                    <input type="text" wire:model="vital_signs.heart_rate" placeholder="bpm" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frequência Respiratória</label>
                    <input type="text" wire:model="vital_signs.respiratory_rate" placeholder="mrm" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso</label>
                    <input type="text" wire:model="vital_signs.weight" placeholder="kg" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mucosas</label>
                    <input type="text" wire:model="vital_signs.mucosa" placeholder="Normocoradas" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hidratação</label>
                    <input type="text" wire:model="vital_signs.hydration" placeholder="Normal" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Linfonodos</label>
                    <input type="text" wire:model="vital_signs.lymph_nodes" placeholder="Normais" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Anamnese e Exame Físico</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Queixa Principal</label>
                    <textarea wire:model="chief_complaint" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anamnese</label>
                    <textarea wire:model="anamnesis" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exame Físico</label>
                    <textarea wire:model="physical_exam" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
        </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Diagnóstico e Tratamento</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                <textarea wire:model="diagnosis" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tratamento</label>
                <textarea wire:model="treatment" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prognóstico</label>
                    <select wire:model="prognosis" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        <option value="bom">Bom</option>
                        <option value="reservado">Reservado</option>
                        <option value="grave">Grave</option>
                        <option value="obito">Óbito</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea wire:model="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-biohazard text-red-500 mr-2"></i>Doenças Zoonóticas</h3>
        <p class="text-sm text-gray-500 mb-4">Registre doenças zoonóticas associadas a este atendimento.</p>
        <div class="space-y-3">
            @foreach($selectedDiseases as $index => $sd)
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <select wire:model="selectedDiseases.{{ $index }}.disease_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">Selecione uma doença...</option>
                        @foreach($zoonoticDiseases as $disease)
                        <option value="{{ $disease->id }}">{{ $disease->name }}
                            @if($disease->is_notifiable) 🔔 @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center space-x-2 text-sm whitespace-nowrap">
                    <input type="checkbox" wire:model="selectedDiseases.{{ $index }}.is_suspected" class="rounded text-yellow-500">
                    <span>Suspeito</span>
                </label>
                <button type="button" wire:click="removeDisease({{ $index }})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endforeach
        </div>
        <button type="button" wire:click="addDisease" class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm">
            <i class="fas fa-plus mr-1"></i> Adicionar Doença Zoonótica
        </button>
    </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Prescrições</h3>
            <div class="space-y-4">
                @foreach($prescriptions as $index => $prescription)
                <div class="prescription-item flex flex-wrap gap-4 items-end p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 mb-1">Medicamento *</label>
                        <input type="text" wire:model="prescriptions.{{ $index }}.medication" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Nome do medicamento">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-gray-500 mb-1">Dosagem</label>
                        <input type="text" wire:model="prescriptions.{{ $index }}.dosage" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 50mg">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-gray-500 mb-1">Unidade</label>
                        <select wire:model="prescriptions.{{ $index }}.unit" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">...</option>
                            <option value="mg">mg</option>
                            <option value="g">g</option>
                            <option value="mL">mL</option>
                            <option value="cp">cp</option>
                            <option value="gotas">gotas</option>
                            <option value="UI">UI</option>
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-gray-500 mb-1">Frequência</label>
                        <input type="text" wire:model="prescriptions.{{ $index }}.frequency" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 8h">
                    </div>
                    <div class="w-24">
                        <label class="block text-xs text-gray-500 mb-1">Duração</label>
                        <input type="text" wire:model="prescriptions.{{ $index }}.duration" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 7 dias">
                    </div>
                    <div class="w-28">
                        <label class="block text-xs text-gray-500 mb-1">Via</label>
                        <select wire:model="prescriptions.{{ $index }}.route" class="w-full px-3 py-2 border rounded-lg text-sm">
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
                    <button type="button" wire:click="removePrescription({{ $index }})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" wire:click="addPrescription" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm">
                <i class="fas fa-plus mr-1"></i> Adicionar Prescrição
            </button>
            @error('prescriptions.*.medication') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('medical-records.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Salvar
            </button>
        </div>
    </form>
</div>
