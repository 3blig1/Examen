<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'country' => 'required|string',
            'date_of_birth' => 'required|date',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'date_of_birth' => $validated['date_of_birth'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        // Handle email verification behavior based on env flag
        if (env('REQUIRE_EMAIL_VERIFICATION', true)) {
            // Create email verification token
            $token = Str::random(60);
            EmailVerification::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => now()->addHours(24),
            ]);

            // Send verification email
            $this->sendVerificationEmail($user, $token);

            return redirect()->route('login')
                ->with('success', 'Registration successful! Please check your email to verify your account.');
        }

        // If verification is not required, mark email as verified immediately
        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('login')
            ->with('success', 'Registration successful! You can now login.');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->with('error', __($status));
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Handle reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->with('error', __($status));
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Invalid credentials');
        }

        if (env('REQUIRE_EMAIL_VERIFICATION', true) && !$user->isEmailVerified()) {
            return back()->with('error', 'Votre email n’est pas encore vérifié. Utilisez le bouton "Renvoyer l’email de vérification" ci-dessous.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'Your account is disabled');
        }

        auth()->login($user);
        $request->session()->regenerate();

        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isExaminer()) {
            return redirect()->route('examiner.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        if ($user->isEmailVerified()) {
            return back()->with('success', 'Email already verified. You can login.');
        }

        if (!env('REQUIRE_EMAIL_VERIFICATION', true)) {
            return back()->with('error', 'Email verification is not required.');
        }

        $token = Str::random(60);

        EmailVerification::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token, 'expires_at' => now()->addHours(24), 'verified_at' => null]
        );

        $this->sendVerificationEmail($user, $token);

        return back()->with('success', 'Verification email sent. Please check your inbox.');
    }

    /**
     * Verify email
     */
    public function verifyEmail($token)
    {
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification || !$verification->isValid()) {
            return redirect()->route('login')
                ->with('error', 'Verification link is invalid or expired');
        }

        $user = $verification->user;
        $user->forceFill(['email_verified_at' => now()])->save();
        $verification->verify();

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now login.');
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user, string $token)
    {
        $url = route('verify-email', $token);

        Mail::to($user->email)->send(new EmailVerificationMail($user, $url));
    }

    /**
     * Logout
     */
    public function logout()
    {
        auth()->logout();
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
