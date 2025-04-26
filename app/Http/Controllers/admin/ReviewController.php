<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['service.provider.user', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply rating filter
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $reviews = $query->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['service.provider.user', 'user', 'order']);
        return view('admin.reviews.show', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $review->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review status updated successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
} 