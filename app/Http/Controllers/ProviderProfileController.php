<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);
        $profile = ProviderProfile::where('user_id', $userId)->first();
        
        // If profile doesn't exist and it's the logged-in user, redirect to create/edit
        if (!$profile) {
            if (auth()->id() === $user->id) {
                return redirect()->route('profile.edit')
                    ->with('info', 'Please complete your provider profile first.');
            }
            // If it's another user without a profile, show 404
            abort(404, 'Provider profile not found');
        }
        
        $ratings = $profile->ratings()->with('reviewer')->latest()->paginate(10);

        return view('services.profile-show', compact('user', 'profile', 'ratings'));
    }

    public function edit()
    {
        $user = auth()->user();
        $profile = $user->providerProfile ?? new ProviderProfile();

        return view('services.profile-edit', compact('profile'));
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

        // Decode skills JSON string to array
        if (!empty($validated['skills'])) {
            $validated['skills'] = json_decode($validated['skills'], true);
        } else {
            $validated['skills'] = [];
        }

        $profile = ProviderProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('provider.show', $user->id)
            ->with('success', 'Profile updated successfully.');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $profile = $user->providerProfile;
        $recentRatings = $profile->ratings()->latest()->limit(5)->get();

        return view('services.profile-dashboard', compact('profile', 'recentRatings'));
    }
}
