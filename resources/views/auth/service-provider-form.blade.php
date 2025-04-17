<x-home.layout>
    <x-home.page-heading>
        Complete your info
    </x-home.page-heading>
<form method="POST" action="{{ route('service_provider.store') }}">
    @csrf
        <x-forms.input name="phone_number" label="Phone Number"/>
        <div class="mt-4">
            <x-forms.input name="location" label="Location"/>
        </div>
        <div class="mt-4">
            <x-forms.input name="business_name" label="Business Name"/>
        </div>
        <div class="mt-4">
            <x-forms.input name="business_address" label="Business Address"/>
        </div>
        <div class="mt-4">
            <x-forms.select name="provider_type" label="Provider Type">
            <option value="">Select provider type</option>
            <option value="handyman" {{ old('provider_type') == 'handyman' ? 'selected' : '' }}>Handyman</option>
            <option value="bussiness_owner" {{ old('provider_type') == 'bussiness_owner' ? 'selected' : '' }}>Business Owner</option>
        </x-forms.select>
        </div>

        <div class="mt-4">
            <x-forms.field name="bio" label="Bio">
                <textarea cols=40 name="bio"></textarea>
            </x-forms.field>
        </div>

    <div class="flex justify-end">
        <x-primary-button>
            Continue
        </x-primary-button>
    </div>
</form>
</x-home.layout>
