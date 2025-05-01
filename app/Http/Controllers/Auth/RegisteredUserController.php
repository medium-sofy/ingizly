<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // Max 2MB


        ]);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('user_images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_image' => $imagePath,
        ]);

        //event(new Registered($user));

       // Generate and store OTP
        $otp = rand(100000, 999999);
        $user->update([
            'email_otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP to user's email
        Mail::to($user->email)->send(new SendOtpMail($otp));

        // Log the user in and redirect to OTP form
        Auth::login($user);

        return redirect()->route('verify.otp.form')->with('status', 'OTP sent to your email.');
    }

    /**
     * Show the role selection page.
     */
    public function showRoleSelection(): View
    {
        return view('auth.choose-role');
    }

    /**
     * Handle role selection after registration.
     */
    public function selectRole(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:service_buyer,service_provider'],
        ]);

        $user = Auth::user();
        $user->update(['role' => $request->role]);

        // Redirect based on role
        if ($user->role == 'service_buyer') {
            return redirect()->route('service_buyer.form');
        } else {
            return redirect()->route('service_provider.form');
        }
    }
}
