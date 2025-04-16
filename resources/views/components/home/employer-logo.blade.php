@props(['employer','width'=>90])
<img src="{{ asset($employer->company_logo) }}" alt="" class="rounded-xl shadow-sm" width="{{ $width }}">
