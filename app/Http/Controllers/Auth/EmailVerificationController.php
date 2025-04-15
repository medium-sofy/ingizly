<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.verify-email');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $code = Str::random(6); // Or use rand(100000, 999999)

        EmailVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(15)
            ]
        );

        Mail::to($request->email)->send(new VerificationCodeMail($code));

        return redirect()->route('verification.code.form')->with([
            'email' => $request->email,
            'status' => 'Verification code sent to your email'
        ]);
    }

    public function showCodeForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$verification || $verification->isExpired()) {
            return back()->withErrors(['code' => 'Invalid or expired verification code']);
        }

        // Mark email as verified (update users table)
        User::where('email', $request->email)->update(['email_verified_at' => now()]);

        // Delete the verification record
        $verification->delete();

        return redirect('/')->with('status', 'Email verified successfully!');
    }
}