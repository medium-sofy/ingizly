<?php

namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use App\Models\Review;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
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


    // In ServiceProviderDashboardController
    public function acceptOrder(Order $order)
    {
        // Verify provider owns this order
        if ($order->service->provider->user_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }
    
        // Update order status
        $order->update(['status' => 'accepted']);
    
        // Delete any existing acceptance notifications for this order
        Notification::where('user_id', $order->buyer_id)
            ->where('notification_type', 'order_update')
            ->where(function($query) use ($order) {
                $query->where('content', 'like', '%"order_id":"'.$order->id.'"%')
                      ->orWhere('content', 'like', '%'.$order->service->title.'%');
            })
            ->delete();
    
        // Create single standardized notification
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Booking Accepted #' . $order->id,
            'content' => json_encode([
                'message' => "Your booking for '{$order->service->title}' has been accepted",
                'order_id' => $order->id,
                'service_id' => $order->service_id,
                'source' => 'provider_dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
    
        return redirect()->back()->with('success', 'Order accepted successfully');
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

    public function wallet()
    {
        $providerId = Auth::id(); // Logged-in provider ID

        // Fetch payments with successful status and completed orders
        $payments = Payment::with(['order.user', 'order.service'])
            ->whereHas('order', function ($query) use ($providerId) {
                $query->where('status', 'completed')
                      ->whereHas('service', function ($query) use ($providerId) {
                          $query->where('provider_id', $providerId);
                      });
            })
            ->where('payment_status', 'successful')
            ->get();

        return view('service_provider.wallet', compact('payments'));
    }

    public function downloadTransaction($paymentId)
    {
        $payment = Payment::with('order.buyer.user')->findOrFail($paymentId);

        // Update the view path to 'service_provider.transaction_pdf'
        $pdf = Pdf::loadView('service_provider.transaction_pdf', compact('payment'));
        return $pdf->download('transaction_' . $payment->transaction_id . '.pdf');
    }
}
