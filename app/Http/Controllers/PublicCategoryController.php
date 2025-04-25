<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class PublicCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // Fetch all categories
        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        // only active services for the selected category
        $services = $category->services()->where('status', 'active')->with('images')->get();

        return view('categories.show', compact('category', 'services'));
    }

    public function allServices(Request $request)
    {
        $query = \App\Models\Service::where('status', 'active')->with('category', 'images');

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Paginate results
        $services = $query->paginate(12)->appends($request->all()); // Keep query parameters in pagination links
        $categories = \App\Models\Category::all();

        return view('services.all', compact('services', 'categories'));
    }
}
