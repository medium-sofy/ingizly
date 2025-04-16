<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ServiceBuyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceBuyerController extends Controller
{
    /**
     * Show the form for creating a new service buyer profile.
     */
    public function create()
    {
        return view('auth.service-buyer-form');
    }

    /**
     * Store a newly created service buyer profile.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'location' => 'required|string|max:255',
        ]);

        // Create service buyer profile
        ServiceBuyer::create([
            'user_id' => Auth::id(),
            'phone_number' => $request->phone_number,
            'location' => $request->location,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile completed successfully!');
    }
}