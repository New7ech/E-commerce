<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // For hashing token if chosen
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Password; // Not using Laravel's default broker directly here
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Mail\CustomPasswordResetLinkMail; // Import Mailable


class CustomForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function create(): View
    {
        // This will be updated to return a proper view later
        return view('auth.custom-forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate a token
        $token = Str::random(60);

        // Store the token. Using updateOrInsert to handle resends for the same email.
        // Storing plain token here. If hashing, ensure the link sent to user also uses this plain token
        // and the verification step compares hashes. For simplicity, plain token storage is often used
        // by Laravel's default broker when it emails a token that is part of the URL.
        // The security relies on the token's randomness and HTTPS for the reset link.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token, // Storing plain token. Laravel's broker might hash it.
                                   // If using Password::broker()->sendResetLink, it handles token generation & storage.
                                   // For this manual approach, we store it directly.
                'created_at' => Carbon::now()
            ]
        );

        // For now, dump the token and email. Actual email sending will be in the next subtask.
        // In a real application, you would pass $token (not hashed) to the notification/mailable.
        // The link in the email would contain this $token.
        // When the user clicks the link, the token from the URL is compared against the (possibly hashed) one in the DB.
        // For this step, we'll just log/dd it.

        // dd('Token: ' . $token, 'Email: ' . $request->email); // For verification during development

        // Send the password reset link email
        try {
            Mail::to($request->email)->send(new CustomPasswordResetLinkMail($request->email, $token));
        } catch (\Exception $e) {
            // Log the error or handle it gracefully if mail sending fails
            // For now, we'll proceed, but in production, you might want to inform the user or retry
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            // If mail is critical, you might even rollback the token storage or queue the email.
            // For this exercise, we'll assume it's okay to show success even if mail fails silently here,
            // or rely on global exception handling. A better approach would be to queue emails.
        }

        return back()->with('status', 'If your email address exists in our system, you will receive a password reset link shortly.');
    }
}
