<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\Violation;
use App\Models\Notification;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicedetailsController extends Controller
{
    public function show($id)
{
    $service = Service::with([
        'provider.user',
        'category',
        'images',
        'reviews.buyer.user',
        'orders' => function($query) {
            $query->where('buyer_id', Auth::id())
                  ->whereIn('status', ['pending', 'accepted', 'in_progress', 'completed']);
        },
        'cancelledOrders' => function($query) {
            $query->where('buyer_id', Auth::id())
                  ->where('status', 'cancelled')
                  ->latest();
        },
        'violations' => function($query) {
            $query->where('user_id', Auth::id());
        }
    ])->findOrFail($id);

    $averageRating = $service->reviews->avg('rating') ?? 0;
    $totalReviews = $service->reviews->count();
    $hasReviewed = Auth::check() ? $service->reviews->where('buyer_id', Auth::id())->count() > 0 : false;
    $hasReported = Auth::check() ? $service->violations->count() > 0 : false;
    
    $currentOrder = $service->orders->first();
    $cancelledOrder = $service->cancelledOrders->first();

    return view('service_buyer.service_details.show', [
        'service' => $service,
        'currentOrder' => $currentOrder,
        'cancelledOrder' => $cancelledOrder,
        'averageRating' => $averageRating,
        'totalReviews' => $totalReviews,
        'images' => $service->images,
        'hasReviewed' => $hasReviewed,
        'hasReported' => $hasReported
    ]);
}
public function submitReview(Request $request, $serviceId)
{
    $validated = $request->validate([
        'rating' => 'required|integer|between:1,5',
        'comment' => 'required|string|max:500',
    ]);

    // Find the first completed order for this service by current user
    $order = Order::where('buyer_id', auth()->id())
                ->where('service_id', $serviceId)
                ->where('status', 'completed')
                ->first();

    if (!$order) {
        return back()->with('error', 'You need to complete an order before reviewing');
    }

    // Check for existing review
    if (Review::where('order_id', $order->id)->exists()) {
        return back()->with('error', 'You have already reviewed this service');
    }

    // Get the service first
    $service = Service::findOrFail($serviceId);

    // Create review
    Review::create([
        'service_id' => $serviceId,
        'buyer_id' => auth()->id(),
        'order_id' => $order->id,
        'rating' => $validated['rating'],
        'comment' => $validated['comment'],
    ]);

    // Create notification for the provider
    Notification::create([
        'user_id' => $service->provider_id, 
        'title' => 'New Review Received',
        'content' => "You received a new review for your service '{$service->title}'",
        'notification_type' => 'review',
        'is_read' => false
    ]);

    // Update service rating
    $service->update([
        'avg_rating' => $service->reviews()->avg('rating')
    ]);

    return back()->with('success', 'Review submitted successfully!');
}

    public function showReportForm($serviceId)
    {
        $service = Service::with(['violations' => function($query) {
            $query->where('user_id', Auth::id());
        }])->findOrFail($serviceId);

        // Check if user has already reported this service
        if ($service->violations->count() > 0) {
            return redirect()->route('service.details', $serviceId)
                            ->with('error', 'You have already reported this service.');
        }

        return view('service_buyer.service_details.report_form', compact('service'));
    }


    public function submitReport(Request $request, $serviceId)
    {
        // Check if user has already reported this service
        $existingReport = Violation::where('user_id', Auth::id())
                                ->where('service_id', $serviceId)
                                ->first();

        if ($existingReport) {
            return redirect()->route('service.details', $serviceId)
                            ->with('error', 'You have already reported this service.');
        }

        $request->validate([
            'reason_type' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
            'agree_terms' => 'required|accepted'
        ]);

        Violation::create([
            'user_id' => Auth::id(),
            'service_id' => $serviceId,
            'reason_type' => $request->reason_type,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('service.details', $serviceId)
                         ->with('success', 'Service reported successfully. Our team will review it shortly.');
    }


}