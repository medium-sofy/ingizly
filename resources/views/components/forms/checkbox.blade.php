@props(['label', 'name'])

@php
    $defaults = [
        'type' => 'checkbox',
        'id' => $name,
        'name' => $name,
        'value' => old($name)
    ];
@endphp

<x-forms.field :$label :$name>
    <div class="rounded-xl bg-gray-50 border border-gray-200 px-5 py-4 w-full">
        <input {{ $attributes($defaults) }}>
        <span class="pl-1 text-gray-800">{{ $label }}</span>
    </div>
</x-forms.field>