<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProviderProfile;
use App\Models\Otp;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    private $otpSentFlag = false;

    public function showAuth()
    {
        return view('auth.combined');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if ($user->is_banned) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been banned. Please contact support.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        \Log::info("===== REGISTER FUNCTION STARTED =====");
        \Log::info("Email being registered: " . $request->email);
        
        // Check if email exists
        $existingEmail = User::where('email', $request->email)->exists();
        if ($existingEmail) {
            return back()->withErrors(['email' => 'This email is already registered.'])->onlyInput('email');
        }
        
        \Log::info("Email is unique, proceeding with validation");
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,provider'],
        ]);

        // Generate OTP code
        $code = rand(100000, 999999);
        \Log::info("GENERATED OTP CODE: " . $code);
        
        // Store data in session
        session(['register_data' => $request->except('_token')]);
        session(['otp_code' => $code]);
        session(['otp_expires_at' => Carbon::now()->addMinutes(10)]);
        
        // SEND EMAIL WITH OTP
        try {
            Mail::to($request->email)->send(new OtpMail($code));
            \Log::info("OTP email sent to: " . $request->email);
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP email: " . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
        
        // Show OTP verification page with EMAIL
        return view('auth.otp', [
            'email' => $request->email,
            'type' => 'register'
        ]);
    }
    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showOtpForm($type, $email)
    {
        return view('auth.otp', compact('type', 'email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);
        
        $storedCode = session('otp_code');
        $expiresAt = session('otp_expires_at');
        $data = session('register_data');
        $email = $data['email'] ?? null;
        
        // Check if session expired
        if (!$storedCode || !$data) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }
        
        // Check if OTP expired
        if (Carbon::now() > $expiresAt) {
            session()->forget(['otp_code', 'otp_expires_at', 'register_data']);
            return redirect()->route('register')->with('error', 'OTP expired. Please register again.');
        }
        
        // Check if code is WRONG - Stay on OTP page
        if ($request->code != $storedCode) {
            return redirect()->route('otp.form', [
                'type' => 'register',
                'email' => $email
            ])->with('error', 'Invalid OTP. Please try again.');
        }
        
        // FINAL DUPLICATE CHECK - Email only (phone removed)
        if (User::where('email', $data['email'])->exists()) {
            session()->forget(['otp_code', 'otp_expires_at', 'register_data']);
            return redirect()->route('register')->with('error', 'This email is already registered.');
        }
        
        // Create user (phone removed)
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        // $user->phone = $data['phone']; ← REMOVED
        $user->password = bcrypt($data['password']);
        $user->role = $data['role'];
        $user->wallet_balance = 2000;
        $user->save();

        \Log::info("User created - ID: {$user->id}, Email: '{$user->email}', Wallet Balance: {$user->wallet_balance}");
        
        // Create wallet transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => 2000,
            'type' => 'deposit',
            'description' => 'Welcome bonus - 2000 Taka'
        ]);
        
        // Create provider profile if needed
        if ($data['role'] === 'provider') {
            ProviderProfile::create(['user_id' => $user->id]);
        }
        
        Auth::login($user);
        session()->forget(['otp_code', 'otp_expires_at', 'register_data']);
        
        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome bonus of 2000 Taka added!');
    }
    

    public function resendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);
    
    $data = session('register_data');
    
    if (!$data) {
        return redirect()->route('register')->with('error', 'Session expired. Please register again.');
    }
    
    $newCode = rand(100000, 999999);
    
    session(['otp_code' => $newCode]);
    session(['otp_expires_at' => Carbon::now()->addMinutes(10)]);
    
    try {
        Mail::to($request->email)->send(new OtpMail($newCode));
        \Log::info("Resent OTP email to: " . $request->email);
    } catch (\Exception $e) {
        \Log::error("Failed to resend OTP: " . $e->getMessage());
        return back()->with('error', 'Failed to resend OTP. Please try again.');
    }
    
    return redirect()->route('otp.form', [
        'type' => 'register',
        'email' => $request->email
    ])->with('info', 'New OTP sent to your email!');
}
    

}