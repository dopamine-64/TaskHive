<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\ProviderProfile;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $providerId)
    {
        $reviewerId = auth()->id();

        $validator = Validator::make($request->all(), [
            'tracking_id' => 'required|integer|exists:trackings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $validator->after(function ($validator) use ($providerId, $reviewerId, $request) {
            if ((int) $providerId === (int) $reviewerId) {
                $validator->errors()->add('rating', 'You cannot rate yourself.');
            }

            $trackingId = (int) $request->input('tracking_id');

            $tracking = Tracking::query()->find($trackingId);
            if (!$tracking) {
                return;
            }

            $isReviewerBooking = (int) $tracking->customer_id === (int) $reviewerId;
            $isProviderMatch = (int) $tracking->provider_id === (int) $providerId;
            $isCompleted = $tracking->status === 'completed';

            if (!$isReviewerBooking || !$isProviderMatch || !$isCompleted) {
                $validator->errors()->add('rating', 'You can only rate providers after a completed booking.');
            }

            $alreadyRated = Rating::where('tracking_id', $trackingId)
                ->exists();

            if ($alreadyRated) {
                $validator->errors()->add('rating', 'You have already rated this completed booking.');
            }
        });

        $validated = $validator->validate();

        Rating::create([
            'provider_id' => $providerId,
            'reviewer_id' => $reviewerId,
            'tracking_id' => $validated['tracking_id'],
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
