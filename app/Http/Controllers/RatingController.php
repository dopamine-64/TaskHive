<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $providerId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        Rating::create([
            'provider_id' => $providerId,
            'reviewer_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        $profile = ProviderProfile::where('user_id', $providerId)->first();
        if ($profile) {
            $profile->updateRating();
        }

        return redirect()->back()->with('success', 'Rating submitted successfully.');
    }

    public function destroy(Rating $rating)
    {
        $this->authorize('delete', $rating);

        $providerId = $rating->provider_id;
        $rating->delete();

        $profile = ProviderProfile::where('user_id', $providerId)->first();
        if ($profile) {
            $profile->updateRating();
        }

        return redirect()->back()->with('success', 'Rating deleted successfully.');
    }
}
