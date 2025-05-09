<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::where('buyer_id', Auth::id())
            ->with(['service', 'service.provider.user']);

        // Apply filters if provided
        if ($request->has('status') && $request->status != 'all' && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10);

        return view('service_buyer.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(Service $service)
    {
        return view('service_buyer.orders.create', compact('service'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'location' => 'required_if:service_type,on_site',
            'special_instructions' => 'nullable|string',
        ]);

        $service = Service::findOrFail($request->service_id);

        // Create the order
        $order = Order::create([
            'service_id' => $service->id,
            'buyer_id' => Auth::id(),
            'status' => 'pending',
            'total_amount' => $service->price,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'location' => $request->location,
            'special_instructions' => $request->special_instructions,
        ]);

        // Redirect to checkout
        return redirect()->route('checkout.show', $order->id);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->buyer_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('service', 'service.provider.user');
        return view('service_buyer.orders.show', compact('order'));
    }


    public function destroy(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->buyer_id != Auth::id()) {
            return redirect()->route('buyer.orders.index')
                ->with('error', 'You are not authorized to cancel this order.');
        }
    
        // Check if the order is in pending status
        if ($order->status !== 'pending') {
            return redirect()->route('buyer.orders.index')
                ->with('error', 'Only pending orders can be cancelled.');
        }
    
        // Update the order status to cancelled
        $order->update(['status' => 'cancelled']);
    
        return redirect()->route('buyer.orders.index')
            ->with('success', 'Order has been cancelled successfully.');
    }
}