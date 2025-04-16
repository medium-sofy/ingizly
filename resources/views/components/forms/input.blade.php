@props(['label', 'name', 'type'])

@php
    $defaults = [
        'type' => $type ?? 'text',
        'id' => $name,
        'name' => $name,
        'class' => 'rounded-xl bg-gray-50 border border-gray-200 px-5 py-4 w-full text-gray-800 focus:border-blue-500 focus:ring-blue-500',
        'value' => old($name)
    ];
@endphp

<x-forms.field :$label :$name>
    <input {{ $attributes($defaults) }}>
</x-forms.field>