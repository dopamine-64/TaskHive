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
        \Log::info("Phone being registered: " . $request->phone);
        
        $existingPhone = User::where('phone', $request->phone)->exists();
        \Log::info("Phone exists in database: " . ($existingPhone ? "YES" : "NO"));
        
        if ($existingPhone) {
            \Log::info("BLOCKING duplicate registration for: " . $request->phone);
            return back()->withErrors([
                'phone' => 'This phone number is already registered. Please login instead.'
            ])->onlyInput('phone');
        }
        
        \Log::info("Phone is unique, proceeding with validation");
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:user,provider'],
        ], [
            'phone.regex' => 'Please enter a valid 11-digit phone number (e.g., 01XXXXXXXXX).',
            'email.unique' => 'This email is already registered.',
        ]);

        $existingPhoneAgain = User::where('phone', $request->phone)->exists();
        if ($existingPhoneAgain) {
            \Log::info("BLOCKING in double-check for: " . $request->phone);
            return back()->withErrors([
                'phone' => 'This phone number is already registered. Please login instead.'
            ])->onlyInput('phone');
        }

        \Log::info("All checks passed, sending OTP for: " . $request->phone);
        
        session(['register_data' => $request->all()]);
        session()->save();
        
        $this->sendOtp($request->phone, 'register', json_encode($request->only('name', 'email', 'phone', 'password', 'role')));
        
        return view('auth.otp', [
            'phone' => $request->phone,
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

    public function showOtpForm($type, $phone)
    {
        return view('auth.otp', compact('type', 'phone'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'code' => 'required|digits:6',
            'type' => 'required',
        ]);
        
        if ($this->checkOtpCode($request->phone, $request->code, $request->type)) {
            
            if ($request->type == 'register') {
                $data = session('register_data');
                
                if (!$data) {
                    \Log::warning("Session lost for phone: " . $request->phone . " - Attempting DB recovery");
                    
                    $otpRecord = Otp::where('phone', $request->phone)
                                    ->where('type', 'register')
                                    ->first();
                    
                    if ($otpRecord && $otpRecord->data) {
                        $data = is_string($otpRecord->data) ? json_decode($otpRecord->data, true) : $otpRecord->data;
                        session(['register_data' => $data]);
                        \Log::info("Successfully recovered registration data from DB");
                    }
                }
                
                if (!$data) {
                    \Log::error("Registration data permanently lost for phone: " . $request->phone); 
                    return redirect()->route('register')->with('error', 'Session expired. Please register again.');
                }
                
                \Log::info("Data before user creation: " . json_encode($data));
                
                if (User::where('phone', $data['phone'])->exists()) {
                    session()->forget('register_data');
                    return redirect()->route('register')->with('error', 'This phone number is already registered. Please login instead.');
                }
                
                $user = new User();
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->phone = $data['phone'];
                $user->password = bcrypt($data['password']);
                $user->role = $data['role'];
                
                // Wallet balance (2000 Taka welcome bonus)
                $user->wallet_balance = 2000;
                $user->save();

                \Log::info("User created - ID: {$user->id}, Phone: '{$user->phone}', Wallet Balance: {$user->wallet_balance}");
                
                // Create welcome bonus wallet transaction
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
                
                // Send welcome email notification
                try {
                    $user->notify(new WelcomeNotification($user));
                    \Log::info("Welcome email sent to {$user->email}");
                } catch (TransportExceptionInterface $e) {
                    \Log::error("Failed to send welcome email: " . $e->getMessage());
                    // Continue registration even if email fails
                }
                
                Auth::login($user);
                session()->forget('register_data');
                Otp::where('phone', $request->phone)->where('type', 'register')->delete();
                
                return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome bonus of 2000 Taka added!');
            }
            
            if ($request->type == 'booking') {
                return redirect()->route('booking.my')->with('success', 'Booking confirmed!');
            }
        }
        
        session()->keep(['register_data']);
        
        return redirect()->route('otp.form', [
            'type' => $request->type, 
            'phone' => $request->phone
        ])->with('error', 'Invalid or expired OTP. Please try again.')
        ->withInput();
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'type' => 'required',
        ]);
        
        $data = session('register_data');
        
        if (!$data) {
            $oldOtp = Otp::where('phone', $request->phone)
                            ->where('type', $request->type)
                            ->first();
            $data = $oldOtp ? $oldOtp->data : null;
            
            if ($data) {
                $decodedData = is_string($data) ? json_decode($data, true) : $data;
                session(['register_data' => $decodedData]);
                \Log::info("Restored registration data from DB for resend: " . $request->phone);
            }
        }
        
        session()->keep(['register_data']);
        
        $this->otpSentFlag = false;
        
        $this->sendOtp($request->phone, $request->type, $data);
        
        return redirect()->route('otp.form', [
            'type' => $request->type, 
            'phone' => $request->phone
        ])->with('info', 'New OTP sent to your phone.');
    }
    
    private function sendOtp($phone, $type, $data = null)
    {
        if ($this->otpSentFlag) {
            \Log::info("OTP already sent, skipping duplicate for: {$phone}");
            return;
        }
        $this->otpSentFlag = true;
        
        Otp::where('phone', $phone)->where('type', $type)->delete();
        
        $code = rand(100000, 999999);
        
        if ($data === null && $type == 'register') {
            $data = session('register_data');
            if ($data && is_array($data)) {
                $data = json_encode($data);
            }
        }
        
        $encodedData = null;
        if ($type == 'register') {
            if (is_array($data)) {
                $encodedData = json_encode($data);
            } elseif (is_string($data)) {
                $encodedData = $data;
            } elseif ($data !== null) {
                $encodedData = json_encode($data);
            }
            
            if (!$encodedData) {
                \Log::error("Attempting to send registration OTP without user data for phone: $phone");
            } else {
                \Log::info("Storing registration data in OTP record for phone: $phone");
            }
        }
        
        Otp::create([
            'phone' => $phone,
            'code' => $code,
            'type' => $type,
            'data' => $encodedData,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);
        
        // SMS API call
        $apiUrl = 'https://sms.sslwireless.com/pushapi/dynamic/server.php';
        $apiParams = [
            'user' => env('SSL_USER', 'demo_user'),
            'pass' => env('SSL_PASS', 'demo_pass'),
            'sms[0][0]' => $phone,
            'sms[0][1]' => "Your TaskHive verification code is: {$code}. Valid for 10 minutes.",
            'sms[0][2]' => env('SSL_SENDER_ID', 'TaskHive')
        ];
        
        if (env('SMS_MOCK_MODE', true)) {
            \Log::info(" EXTERNAL API CALL (MOCK MODE)");
            \Log::info("API URL: {$apiUrl}");
            \Log::info("Parameters: " . json_encode($apiParams));
            \Log::info("OTP for {$phone}: {$code}");
        } else {
            try {
                $response = Http::post($apiUrl, $apiParams);
                \Log::info(" Real SMS sent: " . $response->body());
            } catch (\Exception $e) {
                \Log::error(" SMS API failed: " . $e->getMessage());
                \Log::info(" Fallback OTP for {$phone}: {$code}");
            }
        }
        
        session(["otp_{$type}_{$phone}" => $code]);
        session()->flash('debug_otp', $code);
        
        return $code;
    }
    
    private function checkOtpCode($phone, $code, $type)
    {
        \Log::info("Checking OTP - Phone: $phone, Code: $code, Type: $type");
        
        $otp = Otp::where('phone', $phone)
            ->where('type', $type)
            ->where('code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        
        if ($otp) {
            $otp->update(['is_used' => true]);
            session()->forget("otp_{$type}_{$phone}");
            return true;
        }
        
        $sessionCode = session("otp_{$type}_{$phone}");
        if ($sessionCode && $sessionCode == $code) {
            session()->forget("otp_{$type}_{$phone}");
            return true;
        }
        
        return false;
    }
}