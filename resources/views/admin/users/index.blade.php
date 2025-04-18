@extends('layouts.app')

@section('content')
    <div class="py-4">
        {{-- Header --}}
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6"> {{-- Added flex-wrap and gap for better small screen handling --}}
            <h1 class="text-3xl font-bold">Users List</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded whitespace-nowrap"> {{-- Added whitespace-nowrap --}}
                Add New User
            </a>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET">
                {{-- Search Bar --}}
                <div class="mb-4">
                    <label for="search" class="sr-only">Search Users</label> {{-- Added label for accessibility --}}
                    <input type="text" name="search" id="search" placeholder="Search users by name, username, email..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Filter Controls --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    {{-- Registration Date Range --}}
                    <div class="flex flex-wrap items-center gap-2"> {{-- Added flex-wrap and gap here too --}}
                        <label for="date_from" class="sr-only">Date From</label>
                        <input type="date" name="date_from" id="date_from" title="Registration date from" {{-- Added title --}}
                        value="{{ request('date_from') }}"
                               class="border border-gray-300 rounded p-2">
                        <span class="hidden sm:inline px-2">-</span> {{-- Hide hyphen on very small screens if dates stack --}}
                        <label for="date_to" class="sr-only">Date To</label>
                        <input type="date" name="date_to" id="date_to" title="Registration date to" {{-- Added title --}}
                        value="{{ request('date_to') }}"
                               class="border border-gray-300 rounded p-2">
                    </div>

                    {{-- Role Filter --}}
                    <div> {{-- Wrap select for potential label later --}}
                        <label for="role" class="sr-only">Role</label>
                        <select name="role" id="role"
                                class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{-- Corrected selected logic to use request() --}}
                            <option value="All Roles">All Roles</option> {{-- Use empty value for 'all' --}}
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="service_buyer" {{ request('role') == 'service_buyer' ? 'selected' : '' }}>Service Buyer</option>
                            <option value="service_provider" {{ request('role') == 'service_provider' ? 'selected' : '' }}>Service Provider</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2"> {{-- Group buttons for clarity --}}
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Apply Filters
                        </button>
                        {{-- Link-styled reset button is often clearer than a red button --}}
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">
                            Reset
                        </a>
                        {{-- Original Reset Button (if preferred) - corrected ID --}}
                        {{-- <button type="reset" id="resetFilters" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"> Reset </button> --}}
                    </div>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-lg shadow-sm ">
            <div class="overflow-x-auto p-6"> {{-- Moved padding here so it doesn't get cut off by scroll --}}
                <table class="min-w-full divide-y divide-gray-200"> {{-- Added min-w to ensure overflow activates reliably --}}
                    <thead>
                    <tr class="border-b border-gray-200">
                        {{-- Adjusted padding for potentially better mobile view within scroll --}}
                        <th class="p-3 text-left font-semibold text-gray-700">User Info</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Contact</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Role</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Joined</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Last Login</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Services</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50"> {{-- Added hover effect --}}
                            <td class="p-3 align-top"> {{-- Use align-top if content height varies --}}
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full overflow-hidden mr-3 flex-shrink-0"> {{-- Added flex-shrink-0 --}}
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="" class="w-full h-full object-cover"> {{-- Empty alt is okay for decorative images --}}
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-indigo-500 text-white font-medium">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->username }}</p>
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium {{
                                            $user->status == 'active' ? 'bg-green-100 text-green-800' :
                                            ($user->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                        }} rounded-full mt-1">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 text-sm text-gray-700 align-top">
                                <div>
                                    <a href="mailto:{{ $user->email }}" class="hover:text-indigo-600">{{ $user->email }}</a>
                                    @if($user->phone)<p class="text-gray-500">{{ $user->phone }}</p>@endif
                                    @if($user->location)<p class="text-xs text-gray-400 mt-1">{{ $user->location }}</p>@endif
                                </div>
                            </td>
                            <td class="p-3 text-sm text-gray-600 align-top">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td> {{-- Nicer formatting --}}
                            <td class="p-3 text-sm text-gray-600 align-top whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="p-3 text-sm text-gray-600 align-top whitespace-nowrap">{{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}</td> {{-- Relative time might be nicer --}}
                            <td class="p-3 text-sm text-gray-600 align-top text-center">{{ $user->services_count ?? 0 }}</td> {{-- Center count --}}
                            <td class="p-3 align-top whitespace-nowrap"> {{-- Prevent actions wrapping --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded">
                                        Edit
                                    </a>
                                    {{-- Use a button triggering JS for delete --}}
                                    <button onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded">
                                        Delete
                                    </button>
                                    {{-- Hidden delete form remains the same --}}
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">No users found matching your criteria.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="flex flex-wrap justify-between items-center p-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600 mb-2 md:mb-0">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} results
                    </div>
                    <div> {{-- Ensure pagination links don't break layout --}}
                        {{ $users->withQueryString()->links() }} {{-- Use withQueryString to preserve filters --}}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden Delete Forms (Keep as is) --}}
    {{-- @foreach($users as $user)
        <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach --}}

@endsection

@push('scripts')
    <script>
        // Vanilla JS Delete Confirmation
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete the user "${name}"? This action cannot be undone.`)) {
                const form = document.getElementById('delete-form-' + id);
                if (form) {
                    form.submit();
                } else {
                    console.error('Delete form not found for user ID:', id);
                }
            }
        }

        // Vanilla JS (or jQuery if you have it) for resetting filters
        // Using a simple link redirect for reset is often easiest:
        // The "Reset" anchor tag <a href="{{ route('admin.users.index') }}"> already handles this.

        // If you wanted to keep the JS approach for the Reset button:
        // document.addEventListener('DOMContentLoaded', function() {
        //     const resetButton = document.getElementById('resetFilters'); // Ensure button has this ID
        //     const filterForm = resetButton ? resetButton.closest('form') : null;

        //     if (resetButton && filterForm) {
        //         resetButton.addEventListener('click', function(e) {
        //             e.preventDefault(); // Prevent default reset behavior if needed

        //             // Clear specific fields or reset the form
        //             filterForm.reset(); // Standard form reset
        //             // Or clear fields manually:
        //             // document.getElementById('search').value = '';
        //             // document.getElementById('date_from').value = '';
        //             // document.getElementById('date_to').value = '';
        //             // document.getElementById('role').value = '';

        //             // Resubmit to get unfiltered list
        //             filterForm.submit();
        //         });
        //     }
        // });

    </script>
@endpush
