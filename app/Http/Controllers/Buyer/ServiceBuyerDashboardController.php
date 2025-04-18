<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class ServiceBuyerDashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     */
    public function index()
    {
        $buyerId = Auth::id();
        $orders = Order::where('buyer_id', $buyerId)->with('service')->get();

        // Basic statistics
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $completedOrders = $orders->where('status', 'completed')->count();
//        $totalSpent = $orders->where('status', 'completed')->sum('total_amount');
        $totalSpent = Order::where('buyer_id', $buyerId)
            ->with(['payments' => function ($query) {
                $query->where('payment_status', 'successful');
            }])
            ->get()
            ->sum(function ($order) {
                return $order->payments->sum('amount');
            });

        $recentOrders = Order::with(['service', 'service.provider.user'])
            ->where('buyer_id', $buyerId)
            ->latest()
            ->take(5)
            ->get();

        return view('service_buyer.dashboard.index', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalSpent',
            'recentOrders'
        ));
    }
}
