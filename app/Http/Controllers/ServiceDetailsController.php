<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\Violation;
use Illuminate\Http\Request;

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
                $query->where('buyer_id', 16); // Only show orders for hardcoded buyer
            }
        ])->findOrFail($id);
    
        $averageRating = $service->reviews->avg('rating') ?? 0;
        $totalReviews = $service->reviews->count();
    
        return view('service_buyer.service_details.show', [
            'service' => $service,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
            'images' => $service->images
        ]);
    }
    public function submitReview(Request $request, $serviceId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:500',
            'order_id' => 'required|exists:orders,id',
            'buyer_id' => 'required|exists:service_buyers,user_id'

        ]);

        Review::create([
            'service_id' => $serviceId,
            'buyer_id' => 15,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment
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
            'user_id' => 1, 
            'service_id' => $serviceId,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('service.details', $serviceId)
                         ->with('success', 'Service reported successfully. Our team will review it shortly.');
    }
}
