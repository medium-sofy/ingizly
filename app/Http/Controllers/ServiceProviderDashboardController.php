<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ServiceProviderDashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     */
    public function index()
    {
        $providerId = Auth::id();

        // Get all services provided by this user
        $services = Service::with('orders')->where('provider_id', $providerId)->get();

        // Basic statistics
        $totalServices = $services->count();
        $totalViews = $services->sum('view_count');

        // Active bookings (orders with 'accepted' status)
        $activeBookings = Order::whereIn('service_id', $services->pluck('id'))
            ->where('status', 'accepted')
            ->count();

        // Average Rating from reviews
        $averageRating = Review::whereIn('service_id', $services->pluck('id'))->avg('rating');

        // Recent Bookings
        $recentOrders = Order::with(['service', 'buyer.user'])
            ->whereIn('service_id', $services->pluck('id'))
            ->latest()
            ->take(5)
            ->get();

        // Recent Reviews
        $recentReviews = Review::with(['service', 'buyer.user'])
            ->whereIn('service_id', $services->pluck('id'))
            ->latest()
            ->take(5)
            ->get();

        return view('service_provider.dashboard.index', compact(
            'services',
            'totalServices',
            'activeBookings',
            'totalViews',
            'averageRating',
            'recentOrders',
            'recentReviews'
        ));
    }

    public function create()
    {
        abort(404); // Not used for dashboard
    }

    public function store(Request $request)
    {
        abort(404); // Not used for dashboard
    }

    public function show(string $id)
    {
        abort(404); // Not used for dashboard
    }

    public function edit(string $id)
    {
        abort(404); // Not used for dashboard
    }

    public function update(Request $request, string $id)
    {
        abort(404); // Not used for dashboard
    }

    public function destroy(string $id)
    {
        abort(404); // Not used for dashboard
    }
}
