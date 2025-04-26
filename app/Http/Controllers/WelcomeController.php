<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $allCategories = Category::all(); // Fetch all categories for the search bar
        $popularCategories = Category::take(8)->get(); // Fetch top 8 categories for the popular section

        return view('welcome', compact('allCategories', 'popularCategories'));
    }
}
