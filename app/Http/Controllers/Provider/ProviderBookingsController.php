<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Notification;

class ProviderBookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providerId = Auth::id(); // Logged-in provider ID
        $orders = Order::with(["user", "service"])
            ->whereHas("service", function ($query) use ($providerId) {
                $query->where("provider_id", $providerId);
            })
            ->orderBy('created_at','DESC')
            ->get();

        return view(
            "service_provider.dashboard.bookings.index",
            compact("orders")
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('service', 'service.provider.user');
        return view('service_provider.dashboard.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function startService(Order $order)
    {
        $providerName = $order->service->provider->user->name;
        $order->update(["status" => 'in_progress']);
        // Notify the buyer that the service has started
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'The provider '. $providerName .' has started working on your order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You order #{$order->id} for '{$order->service->title}' is in progress (Service ID: {$order->service_id})",
                'source' => 'landing'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);

        return redirect()->back()->with('success', 'Order has been started');
    }

    public function completeService(Order $order)
    {
        $providerName = $order->service->provider->user->name;
        $order->update(["status" => 'pending_approval']);

        // Notify the buyer that the service is pending his approval
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'The provider '. $providerName .' has completed on your order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You order #{$order->id} for '{$order->service->title}' is completed, please approve the completion of the service (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);

        // Notify the provider that the service is pending the buyer approval
        Notification::create([
            'user_id' => $order->service->provider->user->id,
            'title' => "The service '{$order->service->title}' is pending the buyer's approval. order #" . $order->id,
            'content' => json_encode([
                'message' =>  "An approval request was sent to '{$order->buyer->user->name}' for service '{$order->service->title}', please wait for the approval of the service (Service ID: {$order->service_id})",
                'source' => 'landing'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);

        return redirect()->back()->with('success', 'Order has been started');

    }
}
