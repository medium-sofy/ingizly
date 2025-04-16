<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $query = Service::where('status', 'active')
            ->with(['category', 'provider.user', 'images']);

        // Apply category filter
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Apply service type filter
        if ($request->has('service_type') && $request->service_type != 'all') {
            $query->where('service_type', $request->service_type);
        }

        // Apply price range filter
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Apply search query
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get services with pagination
        $services = $query->latest()->paginate(12);
        
        // Get all categories for filter dropdown
        $categories = Category::all();

        return view('service_buyer.services.index', compact('services', 'categories'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // Increment view count
        $service->increment('view_count');
        
        // Load relationships
        $service->load('category', 'provider.user', 'images', 'reviews.user');
        
        // Calculate average rating
        $averageRating = $service->reviews->avg('rating') ?? 0;
        
        return view('service_buyer.services.show', compact('service', 'averageRating'));
    }

    /**
     * Show the form for ordering a service.
     */
    public function order(Service $service)
    {
        return view('service_buyer.services.order', compact('service'));
    }
}