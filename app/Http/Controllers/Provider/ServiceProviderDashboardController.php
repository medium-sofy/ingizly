<?php

namespace App\Http\Controllers\Provider;
use App\Http\Controllers\Controller;
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
        $pendingBookings = \App\Models\Order::whereIn('service_id', $services->pluck('id'))
        ->whereIn('status', ['pending', 'accepted']) 
        ->count();
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
            'totalViews',
            'pendingBookings',
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
