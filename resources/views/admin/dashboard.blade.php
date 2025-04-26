@extends('layouts.sidbar')

@section('content')
    <h1 class="text-3xl font-semibold mb-8">Dashboard Overview</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">Total Users</h3>
                <i class="fas fa-users text-blue-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($totalUsers) }}</div>
            <div class="text-sm {{ $userPercentageChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                <i class="fas {{ $userPercentageChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $userPercentageChange >= 0 ? '+' : '' }}{{ $userPercentageChange }}% from last month
            </div>
{{--            total spent on the platform--}}
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">Total Spent On Platform</h3>
                <i class="fas fa-money-bill mr-3 text-green-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($totalSpent) }} EGP</div>
            <div class="text-sm {{ $spentPercentageChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                <i class="fas {{ $spentPercentageChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $spentPercentageChange >= 0 ? '+' : '' }}{{ $spentPercentageChange }}% from last month
            </div>
        </div>

        <!-- Active Services -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">Active Services</h3>
                <i class="fas fa-cogs text-purple-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($activeServices) }}</div>
            <div class="text-sm {{ $servicePercentageChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                <i class="fas {{ $servicePercentageChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $servicePercentageChange >= 0 ? '+' : '' }}{{ $servicePercentageChange }}% from last month
            </div>
        </div>

        <!-- New Reviews -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">New Reviews</h3>
                <i class="fas fa-star text-yellow-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">{{ number_format($newReviews) }}</div>
            <div class="text-sm {{ $reviewPercentageChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                <i class="fas {{ $reviewPercentageChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                {{ $reviewPercentageChange >= 0 ? '+' : '' }}{{ $reviewPercentageChange }}% from last month
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="md:col-span-2 bg-white rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-6">Recent Activity</h2>
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                    @php
                        $colorMap = [
                            'user' => 'blue',
                            'service' => 'green',
                            'review' => 'yellow',
                            'report' => 'red',
                        ];
                        $color = $colorMap[$activity->type] ?? 'gray';
                    @endphp
                    <div class="flex items-center p-3 bg-{{ $color }}-50 rounded-lg">
                        <div class="w-10 h-10 bg-{{ $color }}-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas
                                @if($activity->type === 'user') fa-user
                                @elseif($activity->type === 'service') fa-check
                                @elseif($activity->type === 'review') fa-star
                                @elseif($activity->type === 'report') fa-exclamation
                                @endif
                                text-{{ $color }}-500"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">{{ $activity->activity }}</h4>
                            <p class="text-sm text-gray-500">
                                {{ $activity->timestamp ? Carbon\Carbon::parse($activity->timestamp)->diffForHumans() : 'N/A' }}
                            </p>                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No recent activity.</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-6">Pending Approvals</h2>
            <div class="space-y-4">
                @forelse($pendingServices as $service)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium">{{ $service->title }}</h4>
                            {{-- Corrected line using 'provider' and nullsafe operator '?->' --}}
                            <span class="text-sm text-gray-500">by {{ $service->provider?->user?->name ?? 'Unknown Provider' }}</span>
                        </div>
                        {{-- Approval/Rejection Buttons --}}
                        <div class="flex justify-end space-x-2">
                            <form action="{{ route('services.approve', $service->id) }}" method="POST"> {{-- Adjusted route name assumption --}}
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('services.reject', $service->id) }}" method="POST"> {{-- Adjusted route name assumption --}}
                                @csrf
                                <button type="submit" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No pending services.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
