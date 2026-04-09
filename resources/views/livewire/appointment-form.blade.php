<div>
    <form wire:submit.prevent="save">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Informações da Consulta</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                    <select wire:model="pet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                    <input type="date" wire:model="date" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                    <input type="time" wire:model="time" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select wire:model="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="consulta">Consulta</option>
                        <option value="retorno">Retorno</option>
                        <option value="emergencia">Emergência</option>
                        <option value="cirurgia">Cirurgia</option>
                        <option value="vacina">Vacina</option>
                        <option value="exame">Exame</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo/Observações</label>
                    <textarea wire:model="reason" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold mb-4">Serviços</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($services as $service)
                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer {{ in_array($service->id, $selectedServices) ? 'border-indigo-500 bg-indigo-50' : '' }}">
                    <input type="checkbox" wire:model="selectedServices" value="{{ $service->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <div class="ml-3">
                        <div class="font-medium text-sm">{{ $service->name }}</div>
                        <div class="text-xs text-gray-500">R$ {{ number_format($service->price, 2, ',', '.') }}</div>
                    </div>
                </label>
                @endforeach
            </div>

            @if(count($selectedServices) > 0)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg flex justify-between items-center">
                <span class="text-gray-600">Total estimado:</span>
                <span class="text-xl font-bold text-indigo-600">R$ {{ number_format($total, 2, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('appointments.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Agendar
            </button>
        </div>
    </form>
</div>
