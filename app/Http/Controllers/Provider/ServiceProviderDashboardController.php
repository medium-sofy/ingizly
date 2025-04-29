<?php

namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
use App\Models\Notification;
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
        $providerId =  Auth::id();
        $services = Service::where('provider_id', $providerId)->with('orders')->get();

        // Basic statistics
        $totalServices = $services->count();
        $totalViews = $services->sum('view_count');
        $pendingBookings = Order::whereIn('service_id', $services->pluck('id'))
        ->whereIn('status', ['pending', 'accepted'])
        ->count();
        $averageRating = Review::whereIn('service_id', $services->pluck('id'))->avg('rating');

        // Recent Bookings
        $recentOrders = Order::with(['service', 'buyer.user'])
            ->whereIn('service_id', $services->pluck('id'))
            ->where('status',  'pending')
            ->latest()
            ->orderBy('created_at', 'ASC')
            ->take(5)
            ->get();

        // Recent Reviews
        $recentReviews = Review::with(['service', 'buyer.user'])
            ->whereIn('service_id', $services->pluck('id'))
            ->latest()
            ->take(5)
            ->get();
//        $reviewDate = Carbon::timestamp($-)

        return view('service_provider.dashboard.index', compact(
            'services',
            'totalServices',
            'totalViews',
            'pendingBookings',
            'averageRating',
            'recentOrders',
            'recentReviews'
        ));
    }


    public function acceptOrder(Order $order)
    {
        $providerName = $order->service->provider->user->name;
        $order->update(['status' => 'accepted']);

        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'The provider '. $providerName .' accepted your order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You order #{$order->id} for '{$order->service->title}' has been accepted (Service ID: {$order->service_id})",
                'source' => 'landing'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        return redirect()->back()->with('success', 'Order has been Accepted');
    }

    public function rejectOrder(Order $order)
    {
        $providerName = $order->service->provider->user->name;
        $order->update(['status' => 'rejected']);
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'The provider '. $providerName .' rejected your order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You order #{$order->id} for '{$order->service->title}' has been rejected (Service ID: {$order->service_id})",
                'source' => 'landing'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        return redirect()->back()->with('success', 'Order has been Rejected');
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
