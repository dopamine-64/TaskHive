<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProviderProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->role !== 'provider') {
            abort(404, 'Provider not found');
        }

        $profile = ProviderProfile::where('user_id', $userId)->first();
        
        if (!$profile) {
            if (auth()->id() === $user->id) {
                return redirect()->route('profile.edit')
                    ->with('info', 'Please complete your provider profile first.');
            }
            abort(404, 'Provider profile not found');
        }
        
        $ratings = $profile->ratings()->with('reviewer')->latest()->paginate(10);

        // --- Provider Job Data (For the Working Progress Table) ---
        $incomingRequests = [];
        $activeJobs = [];

        if (auth()->id() == $userId) {
            // New requests for the provider
            $incomingRequests = DB::table('trackings')
                ->join('users', 'trackings.customer_id', '=', 'users.id')
                ->where('trackings.provider_id', $userId)
                ->where('trackings.status', 'requested')
                ->select('trackings.*', 'users.name as customer_name')
                ->orderBy('trackings.created_at', 'desc')
                ->get();

            // Jobs currently in progress for the provider
            $activeJobs = DB::table('trackings')
                ->join('users', 'trackings.customer_id', '=', 'users.id')
                ->where('trackings.provider_id', $userId)
                ->whereIn('trackings.status', ['accepted', 'in_progress'])
                ->select('trackings.*', 'users.name as customer_name')
                ->orderBy('trackings.updated_at', 'desc')
                ->get();
        }

        return view('services.profile-show', compact('user', 'profile', 'ratings', 'incomingRequests', 'activeJobs'));
    }

    public function edit()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Customer Notification Data
        $activeBooking = DB::table('trackings')
            ->where('customer_id', $userId)
            ->whereIn('status', ['requested', 'accepted', 'in_progress'])
            ->join('users', 'trackings.provider_id', '=', 'users.id')
            ->select('trackings.*', 'users.name as provider_name')
            ->orderBy('trackings.created_at', 'desc')
            ->first();

        $completedBooking = DB::table('trackings')
            ->where('customer_id', $userId)
            ->where('status', 'completed')
            ->orderBy('trackings.updated_at', 'desc')
            ->first();

        $profile = $user->providerProfile ?? new ProviderProfile();
        return view('services.profile-edit', compact('profile', 'activeBooking', 'completedBooking'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_rate' => 'nullable|numeric|min:0',
            'service_area' => 'nullable|string|max:255',
            'service_radius_km' => 'nullable|numeric|min:0',
            'certifications' => 'nullable|string',
        ]);

        if (!empty($validated['skills'])) {
            $validated['skills'] = json_decode($validated['skills'], true);
        } else { $validated['skills'] = []; }

        $profile = ProviderProfile::updateOrCreate(['user_id' => $user->id], $validated);

        return redirect()->route('provider.show', $user->id)->with('success', 'Profile updated successfully.');
    }

    public function dashboard()
    {
        return redirect()->route('provider.show', auth()->id());
    }
}
