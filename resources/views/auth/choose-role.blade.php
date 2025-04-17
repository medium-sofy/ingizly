<x-home.layout>
<div class="flex items-center justify-center min-h-screen bg-gray-100 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full space-y-8">
        {{-- Card Container --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-center py-6 px-6">
                <h2 class="text-3xl font-extrabold">Choose Your Role</h2>
                <p class="mt-2 text-sm text-indigo-100">Select how you want to use our platform</p>
            </div>

            {{-- Body --}}
            <div class="p-6 sm:p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">

                    {{-- Service Provider Card --}}
                    <div class="border border-gray-200 rounded-lg p-6 text-center transition duration-300 ease-in-out hover:shadow-lg hover:border-blue-400 flex flex-col">
                        <div class="mb-4">
                            {{-- Using Font Awesome icon --}}
                            <i class="fas fa-briefcase fa-3x text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Service Provider</h3>
                        <p class="text-sm text-gray-600 mb-6 flex-grow">Offer your services, manage bookings, and connect with clients.</p>
                        <form action="{{ route('select.role') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="role" value="service_provider">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Continue as Provider
                            </button>
                        </form>
                    </div>

                    {{-- Service Buyer Card --}}
                    <div class="border border-gray-200 rounded-lg p-6 text-center transition duration-300 ease-in-out hover:shadow-lg hover:border-green-400 flex flex-col">
                        <div class="mb-4">
                            {{-- Using Font Awesome icon --}}
                            <i class="fas fa-search-dollar fa-3x text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Service Buyer</h3>
                        <p class="text-sm text-gray-600 mb-6 flex-grow">Find and book services from talented providers for your needs.</p>
                        <form action="{{ route('select.role') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="role" value="service_buyer">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Continue as Buyer
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</x-home.layout>
