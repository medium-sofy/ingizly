<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
class WelcomeController extends Controller
{
    public function index()
    {
        $allCategories = Category::all(); // Fetch all categories for the search bar
        $popularCategories = Category::take(8)->get(); // Fetch top 8 categories for the popular section

        return view('welcome', compact('allCategories', 'popularCategories'));
    }

    public function search(Request $request)
    {
        $query = Service::where('status', 'active')->with('category', 'images');

        // Apply search filter
        if ($request->has('service') && $request->service != '') {
            $search = strtolower($request->service);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Apply location filter
        if ($request->has('location') && $request->location != '') {
            $query->where('location', $request->location);
        }

        // Paginate results
        $services = $query->paginate(12)->appends($request->all()); // Keep query parameters in pagination links
        $categories = Category::all();

        return view('services.all', compact('services', 'categories'));
    }
}
