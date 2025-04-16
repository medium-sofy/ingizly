@props(['name', 'label','square'=>true])

<div {{$attributes->merge(['class' =>"inline-flex items-center gap-x-2"])}}>

    @php
        if($square)
            echo '<span class="w-2 h-2 bg-blue-600 inline-block"></span>';
    @endphp
    <label class="font-bold text-gray-800" for="{{ $name ?? '' }}">{{ $label }}</label>
</div>