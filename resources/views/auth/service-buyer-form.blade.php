<x-home.layout>
    <x-home.page-heading>
        Complete your info
    </x-home.page-heading>

<form method="POST" action="{{ route('service_buyer.store') }}">
    <div>
        <x-forms.input name="phone_number" label="Phone Number"/>
    </div>
    <div class="mt-4">
        <x-forms.input name="location" label="Location"/>
    </div>
    <div class="flex justify-self-end mt-4">
        <x-primary-button class="ms-4">
            {{ __('Continue') }}
        </x-primary-button>
    </div>
</form>
</x-home.layout>
