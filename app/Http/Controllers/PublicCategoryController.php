<?php

namespace App\Http\Controllers;

use App\Models\Category;

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
}
