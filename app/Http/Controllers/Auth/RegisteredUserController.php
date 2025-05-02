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
use Illuminate\Support\Facades\Http;


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
        $messages = [
            'name.regex' => 'Name must contain only letters.',
            'profile_image.max' => 'Image must not exceed 2MB.',
            'profile_image.mimes' => 'Only JPG, JPEG, or PNG images are allowed.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
        $request->validate([
            'name' => ['required', 'regex:/^[\pL\s\-]+$/u', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' =>  ['required', 'confirmed', 'min:8'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // Max 5MB


        ], $messages);
        $response = Http::get('http://apilayer.net/api/check', [
            'access_key' => config('services.mailboxlayer.api_key'),
            'email' => $request->email,
            'smtp' => 1,
            'format' => 1,
        ]);
        if (!$response->ok() || !$response['smtp_check']) {
            return back()->withErrors(['email' => 'This email address seems invalid or unreachable. Please enter a working email.'])->withInput();
        }
        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('user_images', 'public');
        }else{
            $imagePath = 'avatar/avatar.png';
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
