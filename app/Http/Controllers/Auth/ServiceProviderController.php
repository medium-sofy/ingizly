<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceProviderController extends Controller
{
    /**
     * Show the form for creating a new service provider profile.
     */
    public function create()
    {
        return view('auth.service-provider-form');
    }

    /**
     * Store a newly created service provider profile.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'provider_type' => 'required|in:handyman,bussiness_owner',
            'bio' => 'nullable|string',
        ]);

        // Create service provider profile
        ServiceProvider::create([
            'user_id' => Auth::id(),
            'phone_number' => $request->phone_number,
            'location' => $request->location,
            'business_name' => $request->business_name,
            'business_address' => $request->business_address,
            'provider_type' => $request->provider_type,
            'bio' => $request->bio,
            'avg_rating' => 0,
            'is_verified' => false,
        ]);

        return redirect()->route('provider.dashboard')->with('success', 'Profile completed successfully!');
    }
}