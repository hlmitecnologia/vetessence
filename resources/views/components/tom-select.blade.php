<div
    wire:ignore
    class="tom-select-wrapper"
    @if(isset($wire) && $wire) data-wire="{{ $wire }}" @endif
    @if(isset($value) && $value !== '' && $value !== null) data-value="{{ $value }}" @endif
>
    <select
        @if(isset($id) && $id) id="{{ $id }}" @endif
        name="{{ $name ?? $wire ?? '' }}"
        @if(isset($required) && $required) required @endif
        @if(isset($multiple) && $multiple) multiple @endif
        @if(isset($placeholder) && $placeholder) data-placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'form-control tom-select']) }}
    >
        @if(!isset($multiple) || !$multiple)
            <option value="">{{ $placeholder ?? 'Selecione...' }}</option>
        @endif
        {{ $slot }}
    </select>
</div>
