<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\Violation;
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
                $query->where('buyer_id', Auth::id());
            }
        ])->findOrFail($id);
    
        $averageRating = $service->reviews->avg('rating') ?? 0;
        $totalReviews = $service->reviews->count();
        $hasReviewed = Auth::check() ? $service->reviews->where('buyer_id', Auth::id())->count() > 0 : false;
    
        return view('service_buyer.service_details.show', [
            'service' => $service,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'images' => $service->images,
            'hasReviewed' => $hasReviewed
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
    
        // Create review
        Review::create([
            'service_id' => $serviceId,
            'buyer_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);
    
        // Update service rating
        $service = Service::find($serviceId);
        $service->update([
            'avg_rating' => $service->reviews()->avg('rating')
        ]);
    
        return back()->with('success', 'Review submitted successfully!');
    }

    public function showReportForm($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return view('service_buyer.service_details.report_form', compact('service'));
    }

    public function submitReport(Request $request, $serviceId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        Violation::create([
            'user_id' => Auth::id(),
            'service_id' => $serviceId,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('service.details', $serviceId)
                         ->with('success', 'Service reported successfully. Our team will review it shortly.');
    }
}