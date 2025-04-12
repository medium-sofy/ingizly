@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Users List</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add New User
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET">
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" name="search" placeholder="Search users..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <!-- Registration Date Range -->
                    <div class="flex items-center">
                        <input type="date" name="date_from" id="date_from" placeholder="From"
                               value="{{ request('date_from') }}"
                               class="border border-gray-300 rounded p-2">
                        <span class="px-2">-</span>
                        <input type="date" name="date_to" id="date_to" placeholder="To"
                               value="{{ request('date_to') }}"
                               class="border border-gray-300 rounded p-2">
                    </div>

                    <!-- Role Filter -->

                    <select name="role" id="role_id"
                            class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="All Roles">All Roles</option>

                        <option value="admin" {{ old('role', $user->role ?? null) == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                        <option value="service_buyer" {{ old('role', $user->role ?? null) == 'service_buyer' ? 'selected' : '' }}>
                            Service Buyer
                        </option>
                        <option value="service_provider" {{ old('role', $user->role ?? null) == 'service_provider' ? 'selected' : '' }}>
                            Service Provider
                        </option>

                    </select>




                    <!-- Apply Filters Button -->
                    <button type="reset" id=resetDates" class="bg-red-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                        Reset
                    </button>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-left font-medium text-gray-700">User Info</th>
                        <th class="py-3 text-left font-medium text-gray-700">Contact Details</th>
                        <th class="py-3 text-left font-medium text-gray-700">Role</th>
                        <th class="py-3 text-left font-medium text-gray-700">Joined Date</th>
                        <th class="py-3 text-left font-medium text-gray-700">Last Login</th>
                        <th class="py-3 text-left font-medium text-gray-700">Services</th>
                        <th class="py-3 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full overflow-hidden mr-3">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-blue-500 text-white">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->username }}</p>
                                        <span class="inline-block px-2 py-1 text-xs {{
                                            $user->status == 'active' ? 'bg-green-100 text-green-800' :
                                            ($user->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                        }} rounded-full mt-1">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <div>
                                    <p>{{ $user->email }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->phone ?? 'No phone' }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->location ?? 'No location' }}</p>
                                </div>
                            </td>
                            <td class="py-4">{{ $user->role }}</td>
                            <td class="py-4">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="py-4">{{ $user->last_login ? $user->last_login->format('M d, Y H:i') : 'Never' }}</td>
                            <td class="py-4">{{ $user->services_count ?? 0 }}</td>
                            <td class="py-4">
                                <div class="flex space-x-2">

                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded">
                                        Edit
                                    </a>
                                    <button onclick="deleteUser({{ $user->id }})" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} results
                </div>
                <div class="flex">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>

    @foreach($users as $user)
        <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Reset date filters button
            $('#resetDates').click(function(e) {
                e.preventDefault();

                // Clear date input fields
                $('#date_from').val('');
                $('#date_to').val('');

                // Resubmit the form with other filters intact
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
