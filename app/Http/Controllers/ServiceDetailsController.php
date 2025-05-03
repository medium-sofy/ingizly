<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    Service::where('id', $id)->increment('view_count');
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

    $order = Order::where('buyer_id', auth()->id())
                ->where('service_id', $serviceId)
                ->where('status', 'completed')
                ->first();

    if (!$order) {
        return back()->with('error', 'You need to complete an order before reviewing');
    }

    if (Review::where('order_id', $order->id)->exists()) {
        return back()->with('error', 'You have already reviewed this service');
    }

    $service = Service::findOrFail($serviceId);

    $reviewId = Review::create([
        'service_id' => $serviceId,
        'buyer_id' => auth()->id(),
        'order_id' => $order->id,
        'rating' => $validated['rating'],
        'comment' => $validated['comment'],
    ])->id;

    // Notify provider
    Notification::create([
        'user_id' => $service->provider_id, 
        'title' => 'New Review Received',
        'content' => json_encode([
            'message' => "You received a new {$validated['rating']}-star review for your service '{$service->title}'"
        ]),
        'notification_type' => 'review',
        'is_read' => false
    ]);

    // Notify admin (without related_id)
    $admin = User::where('role', 'admin')->first();
    if ($admin) {
        Notification::create([
            'user_id' => $admin->id,
            'title' => 'New Review Submitted',
            'content' => json_encode([
                'message' => "A customer submitted a new review for service '{$service->title}'",
                'service_id' => $serviceId,
                'review_id' => $reviewId
            ]),
            'notification_type' => 'review',
            'is_read' => false
        ]);
    }

    $service->update([
        'avg_rating' => $service->reviews()->avg('rating')
    ]);

    return back()->with('success', 'Review submitted successfully!');
}

public function showReportForm($serviceId)
{
    $service = Service::with(['violations' => function($query) {
        $query->where('user_id', Auth::id())
              ->latest(); // Get the most recent report first
    }])->findOrFail($serviceId);

    // Check if user has an active report (pending or investigating)
    $hasActiveReport = $service->violations->contains(function($violation) {
        return in_array($violation->status, ['pending', 'investigating']);
    });

    if ($hasActiveReport) {
        return redirect()->route('service.details', $serviceId)
                        ->with('error', 'You already have an active report for this service.');
    }

    return view('service_buyer.service_details.report_form', compact('service'));
}

    public function submitReport(Request $request, $serviceId)
    {

        $request->validate([
            'reason_type' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
            'agree_terms' => 'required|accepted'
        ]);

        $hasActiveReport = Violation::where('user_id', Auth::id())
                             ->where('service_id', $serviceId)
                             ->whereIn('status', ['pending', 'investigating'])
                             ->exists();

    if ($hasActiveReport) {
        return redirect()->route('service.details', $serviceId)
                        ->with('error', 'You already have an active report for this service.');
    }
    
        $violation = Violation::create([
            'user_id' => Auth::id(),
            'service_id' => $serviceId,
            'reason_type' => $request->reason_type,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);
        $serviceTitle = Service::find($serviceId)->title;

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Service Report',
                'content' => json_encode([
                    'message' => "A customer reported an issue with service '{$serviceTitle}' (Reason: {$request->reason_type})",
                    'service_id' => $serviceId,
                    'violation_id' => $violation->id
                ]),
                'notification_type' => 'system',
                'is_read' => false
            ]);
        }

        return redirect()->route('service.details', $serviceId)
                         ->with('success', 'Service reported successfully. Our team will review it shortly.');
    }
}


