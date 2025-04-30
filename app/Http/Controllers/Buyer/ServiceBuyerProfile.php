<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\ServiceBuyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ServiceBuyerProfile extends Controller
{
    /**
     * Display the service buyer profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('service_buyer.profile.edit', compact('user'));
    }

    /**
     * Update the service buyer profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user details
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = $imagePath;
        }
        
        $user->save();

        // Update or create service buyer profile
        if ($user->serviceBuyer) {
            $user->serviceBuyer->update([
                'phone_number' => $validated['phone_number'],
                'location' => $validated['location'],
            ]);
        } else {
            ServiceBuyer::create([
                'user_id' => $user->id,
                'phone_number' => $validated['phone_number'],
                'location' => $validated['location'],
            ]);
        }

        return redirect()->route('service_buyer.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('service_buyer.profile.edit')->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
