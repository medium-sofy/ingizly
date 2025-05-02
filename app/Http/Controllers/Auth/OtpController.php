<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function showForm()
    {



        return view('auth.verify-otp');
    }

    /**
     * Verify the submitted OTP.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('register')->withErrors(['auth' => 'User not authenticated.']);
        }

        // Check if OTP is correct and not expired
        if ($user->email_otp === $request->otp && Carbon::now()->lt($user->otp_expires_at)) {
            $user->update([
                'email_verified_at' => now(),
                'is_email_verified' => true,
                'email_otp' => null,
                'otp_expires_at' => null,
            ]);
            if ($user->role) {
                return redirect()->route('welcome')->with('status', 'Email verified successfully.');
            }
            return redirect()->route('choose.role')->with('status', 'Email verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Resend a new OTP to the user's email.
     */
    public function resend()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('register')->withErrors(['auth' => 'User not authenticated.']);
        }

        $otp = rand(100000, 999999);
        $user->update([
            'email_otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        \Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}
