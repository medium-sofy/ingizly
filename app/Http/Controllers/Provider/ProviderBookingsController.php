<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

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
    public function show(Order $order)
    {
        //
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
}
