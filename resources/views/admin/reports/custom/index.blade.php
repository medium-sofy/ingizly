@extends('layouts.app') {{-- Assuming layouts.app includes Tailwind CSS --}}

@section('content')
<div class="py-4"> {{-- Added py-4 for vertical padding, similar to payments index --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> {{-- Using a max-width container with horizontal padding --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"> {{-- Replaced card with bg-white, shadow, rounded --}}
            <div class="p-6 text-gray-900"> {{-- Replaced card-body padding --}}

                {{-- Card Header Style --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Custom Reports</h3> {{-- Replaced card-title with Tailwind typography --}}
                </div>

                <div class="p-6"> {{-- Added padding inside the card body area --}}
                    <form action="{{ route('reports.custom.generate') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6"> {{-- Replaced row and col-md-6 with grid layout --}}

                            {{-- Report Type --}}
                            <div> {{-- Replaced form-group wrapper --}}
                                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label> {{-- Tailwind form label --}}
                                <select name="report_type" id="report_type" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required> {{-- Updated classes --}}
                                    <option value="">Select Report Type</option>
                                    <option value="user_transactions">User Transactions</option>
                                    <option value="pending_transactions">Pending Transactions</option>
                                    <option value="user_orders">User Orders</option>
                                    <option value="status_orders">Orders by Status</option>
                                </select>
                                @error('report_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> {{-- Tailwind error message --}}
                                @enderror
                            </div>

                            {{-- User --}}
                            <div> {{-- Replaced form-group wrapper --}}
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User (Optional)</label> {{-- Tailwind form label --}}
                                <select name="user_id" id="user_id" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"> {{-- Updated classes --}}
                                    <option value="">Select User</option>
                                    @foreach(App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> {{-- Tailwind error message --}}
                                @enderror
                            </div>

                            {{-- Order Status --}}
                            <div> {{-- Replaced form-group wrapper --}}
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status (Optional)</label> {{-- Tailwind form label --}}
                                <select name="status" id="status" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"> {{-- Updated classes --}}
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> {{-- Tailwind error message --}}
                                @enderror
                            </div>

                             {{-- Empty div for alignment in grid if needed --}}
                            <div></div>

                            {{-- Start Date --}}
                            <div> {{-- Replaced form-group wrapper --}}
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label> {{-- Tailwind form label --}}
                                <input type="date" name="start_date" id="start_date" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"> {{-- Updated classes --}}
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> {{-- Tailwind error message --}}
                                @enderror
                            </div>

                            {{-- End Date --}}
                            <div> {{-- Replaced form-group wrapper --}}
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label> {{-- Tailwind form label --}}
                                <input type="date" name="end_date" id="end_date" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"> {{-- Updated classes --}}
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> {{-- Tailwind error message --}}
                                @enderror
                            </div>

                        </div>

                        <div class="mt-6"> {{-- Replaced row mt-3 col-12 --}}
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"> {{-- Tailwind button styling --}}
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportType = document.getElementById('report_type');
        // Adjusted selectors to find the parent div based on the new structure
        const userIdField = document.getElementById('user_id').closest('div');
        const statusField = document.getElementById('status').closest('div');

        function toggleFields() {
            const selectedType = reportType.value;

            // Hide all fields first
            userIdField.style.display = 'none';
            statusField.style.display = 'none';

            // Show relevant fields based on report type
            switch(selectedType) {
                case 'user_transactions':
                case 'user_orders':
                    userIdField.style.display = 'block';
                    break;
                case 'status_orders':
                    statusField.style.display = 'block';
                    break;
            }
        }

        reportType.addEventListener('change', toggleFields);
        toggleFields(); // Initial state
    });
</script>
@endpush