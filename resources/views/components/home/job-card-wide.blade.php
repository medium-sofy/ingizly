@props(['job'])
<x-home.panel class="flex gap-x-6">
    <div>
        <x-home.employer-logo :employer="$job->employer"/>
    </div>
    <div class="flex-1 flex flex-col">
        <a href="" class="self-start text-sm text-gray-500">{{ $job->employer->name }}</a>
        <h3 class="font-bold text-xl mt-3 group-hover:text-blue-600 transition-colors duration-300">
            <a href="{{ $job->url }}" target="_blank">
                {{ $job->title }}
            </a>
        </h3>
        <p class="text-sm text-gray-500 mt-auto">{{ $job->job_type }} - {{ $job->lower_salary }} to {{ $job->upper_salary }} EGP</p>
    </div>
    <div>

        @php
            $tag = $job->category
        @endphp
        <x-home.tag :$tag/>

    </div>
</x-home.panel>
