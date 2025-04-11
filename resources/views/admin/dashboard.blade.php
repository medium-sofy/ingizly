@extends('layouts.app')

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
            <div class="text-3xl font-bold mb-2">2,451</div>
            <div class="text-sm text-green-500">
                <i class="fas fa-arrow-up"></i>
                +12% from last month
            </div>
        </div>

        <!-- Active Services -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">Active Services</h3>
                <i class="fas fa-cogs text-purple-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">1,257</div>
            <div class="text-sm text-green-500">
                <i class="fas fa-arrow-up"></i>
                +5% from last month
            </div>
        </div>

        <!-- New Reviews -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-gray-600">New Reviews</h3>
                <i class="fas fa-star text-yellow-500"></i>
            </div>
            <div class="text-3xl font-bold mb-2">847</div>
            <div class="text-sm text-red-500">
                <i class="fas fa-arrow-down"></i>
                -3% from last month
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="md:col-span-2 bg-white rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-6">Recent Activity</h2>
            <div class="space-y-4">
                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-blue-500"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">New user registration</h4>
                        <p class="text-sm text-gray-500">2 minutes ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-check text-green-500"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">Service approved</h4>
                        <p class="text-sm text-gray-500">5 minutes ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-star text-yellow-500"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">New review submitted</h4>
                        <p class="text-sm text-gray-500">1 hour ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-red-50 rounded-lg">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation text-red-500"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">Reports submitted</h4>
                        <p class="text-sm text-gray-500">1 hour ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-6">Pending Approvals</h2>
            <div class="space-y-4">
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium">Web Development Service</h4>
                        <span class="text-sm text-gray-500">by John Doe</span>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                            Approve
                        </button>
                        <button class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                            Reject
                        </button>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium">Logo Design Service</h4>
                        <span class="text-sm text-gray-500">by Jane Smith</span>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                            Approve
                        </button>
                        <button class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                            Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
