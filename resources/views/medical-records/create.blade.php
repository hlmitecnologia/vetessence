@extends('layouts.app', ['title' => 'Novo Prontuário'])

@section('header')
    <a href="{{ route('medical-records.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Registro de Prontuário</h2>
@endsection

@section('content')
<form action="{{ route('medical-records.store') }}" method="POST" class="max-w-4xl mx-auto">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Informações Básicas</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                <select name="pet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPet->id ?? '') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
                @error('pet_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário *</label>
                <select name="vet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    @foreach($veterinarians as $vet)
                    <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option value="consulta" {{ old('type') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                    <option value="cirurgia" {{ old('type') == 'cirurgia' ? 'selected' : '' }}>Cirurgia</option>
                    <option value="emergencia" {{ old('type') == 'emergencia' ? 'selected' : '' }}>Emergência</option>
                    <option value="vacina" {{ old('type') == 'vacina' ? 'selected' : '' }}>Vacina</option>
                    <option value="retorno" {{ old('type') == 'retorno' ? 'selected' : '' }}>Retorno</option>
                    <option value="exame" {{ old('type') == 'exame' ? 'selected' : '' }}>Exame</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                <input type="time" name="time" value="{{ old('time', date('H:i')) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Anamnese e Exame Físico</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Queixa Principal</label>
                <textarea name="chief_complaint" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('chief_complaint') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Anamnese</label>
                <textarea name="anamnesis" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('anamnesis') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exame Físico</label>
                <textarea name="physical_exam" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('physical_exam') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Diagnóstico e Tratamento</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                <textarea name="diagnosis" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('diagnosis') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tratamento</label>
                <textarea name="treatment" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('treatment') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prognóstico</label>
                    <select name="prognosis" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        <option value="bom" {{ old('prognosis') == 'bom' ? 'selected' : '' }}>Bom</option>
                        <option value="reservado" {{ old('prognosis') == 'reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="grave" {{ old('prognosis') == 'grave' ? 'selected' : '' }}>Grave</option>
                        <option value="obito" {{ old('prognosis') == 'obito' ? 'selected' : '' }}>Óbito</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Prescrições</h3>
        <div id="prescriptions-container" class="space-y-4">
            <div class="prescription-item flex flex-wrap gap-4 items-end p-4 bg-gray-50 rounded-lg">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Medicamento</label>
                    <input type="text" name="prescriptions[0][medication]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Nome do medicamento">
                </div>
                <div class="w-24">
                    <label class="block text-xs text-gray-500 mb-1">Dosagem</label>
                    <input type="text" name="prescriptions[0][dosage]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 50mg">
                </div>
                <div class="w-32">
                    <label class="block text-xs text-gray-500 mb-1">Frequência</label>
                    <input type="text" name="prescriptions[0][frequency]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 8h">
                </div>
                <div class="w-24">
                    <label class="block text-xs text-gray-500 mb-1">Duração</label>
                    <input type="text" name="prescriptions[0][duration]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 7 dias">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <button type="button" onclick="addPrescription()" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm">
            <i class="fas fa-plus mr-1"></i> Adicionar Prescrição
        </button>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('medical-records.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-save mr-2"></i> Salvar
        </button>
    </div>
</form>

@push('scripts')
<script>
let prescriptionIndex = 1;
function addPrescription() {
    const container = document.getElementById('prescriptions-container');
    const html = `<div class="prescription-item flex flex-wrap gap-4 items-end p-4 bg-gray-50 rounded-lg">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Medicamento</label>
            <input type="text" name="prescriptions[${prescriptionIndex}][medication]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Nome do medicamento">
        </div>
        <div class="w-24">
            <label class="block text-xs text-gray-500 mb-1">Dosagem</label>
            <input type="text" name="prescriptions[${prescriptionIndex}][dosage]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 50mg">
        </div>
        <div class="w-32">
            <label class="block text-xs text-gray-500 mb-1">Frequência</label>
            <input type="text" name="prescriptions[${prescriptionIndex}][frequency]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 8h">
        </div>
        <div class="w-24">
            <label class="block text-xs text-gray-500 mb-1">Duração</label>
            <input type="text" name="prescriptions[${prescriptionIndex}][duration]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Ex: 7 dias">
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
            <i class="fas fa-trash"></i>
        </button>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    prescriptionIndex++;
}
</script>
@endpush
@endsection
