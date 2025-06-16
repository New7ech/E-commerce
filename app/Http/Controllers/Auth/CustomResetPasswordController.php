<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker; // Alias to avoid conflict with Password Rule
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;


class CustomResetPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email');

        // Optional: Basic check if token for email exists before showing form
        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            // ->where('token', $token) // Don't check token value here, just existence for the email
            ->first();

        if (!$passwordResetToken) {
            return redirect()->route('custom.password.request')
                             ->withErrors(['email' => 'Invalid password reset link or email.']);
        }

        // Further check: if using hashed tokens, this check would be different or done in store().
        // For plain tokens, we can verify it matches now or just pass to form.
        // Let's assume the token in URL is the one we stored (plain).
        // A more robust check for token validity (including expiry) can also be done here.

        return view('auth.custom-reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'token'));
        }

        // Retrieve the token record from the database
        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Verify the token and its expiration
        if (!$passwordResetToken || !Hash::check($request->token, $passwordResetToken->token)) {
             // If storing plain tokens, the check would be: $request->token !== $passwordResetToken->token
             // The initial setup stored plain token: 'token' => $token.
             // So, we should compare plain tokens or switch to storing hashed tokens.
             // For now, assuming plain token was stored:
             if (!$passwordResetToken || $request->token !== $passwordResetToken->token) {
                return back()->withErrors(['email' => 'Invalid or expired password reset token.'])->withInput($request->only('email', 'token'));
             }
        }

        // Check token expiry (e.g., within 60 minutes)
        // auth.passwords.users.expire is the config for default broker. We can reuse the value.
        $expiresIn = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);
        if (Carbon::parse($passwordResetToken->created_at)->addMinutes($expiresIn)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete(); // Delete expired token
            return back()->withErrors(['email' => 'Invalid or expired password reset token.'])->withInput($request->only('email', 'token'));
        }

        // Find the user and update their password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Should not happen if 'exists:users,email' validation passed, but good for robustness
            return back()->withErrors(['email' => 'User not found.'])->withInput($request->only('email', 'token'));
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60)); // Invalidate other sessions
        $user->save();

        // Delete the used password reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('custom.login')->with('status', 'Your password has been reset successfully. Please login.');
    }
}
