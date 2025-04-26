<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

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
            'phone_number' => 'required|string|max:11|unique:service_providers,phone_number',
            'location' => 'required|string|max:255',
            'business_name' => 'required|string|max:255', // Make business name required
            'business_address' => 'nullable|string|max:255',
            'provider_type' => 'required|in:handyman,bussiness_owner',
            'bio' => 'nullable|string|max:1000',
            'avg_rating' => 'nullable|numeric|min:0|max:5', // Ensure avg_rating is between 0 and 5
            'is_verified' => 'nullable|boolean', // Ensure is_verified is a boolean value
        ], [
            // Custom error messages
            'phone_number.required' => 'The phone number is required.',
            'phone_number.string' => 'The phone number must be a valid string.',
            'phone_number.max' => 'The phone number is too long. It must not exceed 11 characters.',
            'phone_number.unique' => 'This phone number is already used.',
            'location.required' => 'The location is required.',
            'location.string' => 'The location must be a valid string.',
            'location.max' => 'The location must not exceed 255 characters.',
            'business_name.required' => 'The business name is required.', // Custom error message for business name
            'business_name.string' => 'The business name must be a valid string.',
            'business_name.max' => 'The business name must not exceed 255 characters.',
            'business_address.string' => 'The business address must be a valid string.',
            'business_address.max' => 'The business address must not exceed 255 characters.',
            'provider_type.required' => 'The provider type is required.',
            'provider_type.in' => 'The provider type must be either Handyman or Business Owner.',
            'bio.string' => 'The bio must be a valid string.',
            'bio.max' => 'The bio must not exceed 1000 characters.',
            'avg_rating.numeric' => 'The average rating must be a number.',
            'avg_rating.min' => 'The average rating must be at least 0.',
            'avg_rating.max' => 'The average rating must not exceed 5.',
            'is_verified.boolean' => 'The verification status must be true or false.',
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
            'avg_rating' => $request->avg_rating ?? 0, // Default to 0 if not provided
            'is_verified' => $request->is_verified ?? false, // Default to false if not provided
        ]);

        // Redirect with a success message
        return redirect()->route('provider.dashboard')->with('success', 'Profile completed successfully!');
    }

    /**
     * Show the form for editing the service provider profile.
     */
    public function edit()
    {
        return view('service_provider.profile');
    }

    /**
     * Update the service provider profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|max:2048', // Max 2MB
            'phone_number' => 'required|string|max:11|unique:service_providers,phone_number,' . $user->id . ',user_id',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
        ], [
            'phone_number.unique' => 'The phone number is already in use.',
            'phone_number.max' => 'The phone number must not exceed 11 characters.',
            'email.unique' => 'The email is already in use.',
        ]);

        // Update user details
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $user->save();
        }

        // Update service provider details
        $user->serviceProvider->update([
            'phone_number' => $validated['phone_number'],
            'bio' => $validated['bio'],
            'location' => $validated['location'],
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
        ]);

        return redirect()->route('service_provider.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Delete associated service provider profile
        $user->serviceProvider->delete();

        // Delete user account
        $user->delete();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}