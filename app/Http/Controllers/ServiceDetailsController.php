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
            'reviews.user',
            'primaryImage',
            'orders' // Load orders relationship
        ])->findOrFail($id);
    
        return view('service_buyer.service_details.show', [
            'service' => $service,
            'averageRating' => $service->averageRating(),
            'totalReviews' => $service->totalReviews(),
            'primaryImage' => $service->primaryImage,
            'images' => $service->images
            // Orders will be checked in the view with temporary buyer_id
        ]);
    }
    public function submitReview(Request $request, $serviceId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:500',
            'order_id' => 'required|exists:orders,id'
        ]);

        $review = Review::create([
            'service_id' => $serviceId,
            'buyer_id' => 15, 
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);


        $service = Service::find($serviceId);
        $service->updateAverageRating();

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
