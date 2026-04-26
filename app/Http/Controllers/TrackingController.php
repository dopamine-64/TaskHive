<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Notifications\BookingAcceptedNotification;
use App\Notifications\BookingCompletedNotification;
use App\Notifications\BookingDeclinedNotification;
use App\Notifications\BookingRequestNotification;
use App\Notifications\BookingAcceptedConfirmation;
use App\Notifications\BookingCompletedConfirmation;
use App\Models\Rating;
use App\Models\Tracking; // Added for Eloquent model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function initiateTracking(Request $request, $providerId) {
        $serviceId = $request->service_id;
        
        if (!$serviceId) {
            $service = \App\Models\Service::where('user_id', $providerId)->first();
            $serviceId = $service ? $service->id : null;
        }
        
        $service = \App\Models\Service::find($serviceId);
        
        $trackingId = DB::table('trackings')->insertGetId([
            'customer_id' => Auth::id(),
            'provider_id' => $providerId,
            'service_id' => $serviceId,
            'booking_date' => $request->booking_date ?? now()->format('Y-m-d'),
            'booking_time' => $request->booking_time ?? '09:00:00',
            'address' => $request->address ?? 'To be confirmed',
            'duration' => $service ? $service->duration : 60,
            'status' => 'requested',
            'points_earned' => 0,   // default
            'points_used' => 0,      // default
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tracking = DB::table('trackings')->where('id', $trackingId)->first();
        $provider = User::find($providerId);
        if ($provider && $tracking) {
            $provider->notify(new BookingRequestNotification($tracking));
        }

        return redirect()->route('tracking.live', $trackingId);
    }

    public function liveTracking($id) {
        $tracking = DB::table('trackings')->where('id', $id)->first();
        if (!$tracking) { abort(404); }

        $customer = User::findOrFail($tracking->customer_id);
        $provider = User::findOrFail($tracking->provider_id);

        $customerLocation = [
            'lat' => $tracking->current_lat ?? 23.8103, 
            'lng' => $tracking->current_lng ?? 90.4125
        ];
        
        $providerLocation = [
            'lat' => 23.8150, 
            'lng' => 90.4200
        ];

        $hasRatedCurrentTracking = false;
        if ((int) Auth::id() === (int) $tracking->customer_id) {
            $hasRatedCurrentTracking = Rating::where('reviewer_id', Auth::id())
                ->where('tracking_id', $tracking->id)
                ->exists();
        }

        return view('tracking.live', compact('tracking', 'customer', 'provider', 'customerLocation', 'providerLocation', 'hasRatedCurrentTracking'));
    }

    public function accept($id) {
        $provider = Auth::user();
        if (!$provider || $provider->role !== 'provider') {
            abort(403, 'Only providers can accept requests.');
        }

        $updated = DB::table('trackings')
            ->where('id', $id)
            ->where('status', 'requested')
            ->where('provider_id', $provider->id)
            ->update([
                'status' => 'accepted',
                'provider_id' => $provider->id,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->withErrors([
                'tracking' => 'This request could not be accepted. It may already be handled.',
            ]);
        }

        $tracking = DB::table('trackings')->where('id', $id)->first();
        $customer = $tracking ? User::find($tracking->customer_id) : null;
        if ($customer) {
            $customer->notify(new BookingAcceptedNotification());
        }
        if ($tracking) {
            $provider->notify(new BookingAcceptedConfirmation($tracking));
        }

        return back()->with('success', 'Job Accepted!');
    }

    public function showProviderProfile($id) {
        $provider = User::findOrFail($id);
        
        $incomingRequests = DB::table('trackings')
            ->join('users', 'trackings.customer_id', '=', 'users.id')
            ->where('trackings.provider_id', $id)
            ->where('trackings.status', 'requested')
            ->select('trackings.*', 'users.name as customer_name')
            ->get();

        $activeJobs = DB::table('trackings')
            ->join('users', 'trackings.customer_id', '=', 'users.id')
            ->where('trackings.provider_id', $id)
            ->whereIn('trackings.status', ['accepted', 'in_progress'])
            ->select('trackings.*', 'users.name as customer_name')
            ->get();

        return view('services.profile-show', compact('provider', 'incomingRequests', 'activeJobs'));
    }

    public function customerProfile()
    {
        $userId = Auth::id();

        $bookings = DB::table('trackings')
            ->leftJoin('services', 'trackings.service_id', '=', 'services.id')
            ->join('users as providers', 'trackings.provider_id', '=', 'providers.id')
            ->where('trackings.customer_id', $userId)
            ->orderBy('trackings.created_at', 'desc')
            ->select(
                'trackings.*',
                'providers.name as provider_name',
                'services.title as service_title',
                'services.category as service_category'
            )
            ->get();

        $ratedTrackingIds = Rating::where('reviewer_id', $userId)
            ->whereNotNull('tracking_id')
            ->pluck('tracking_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('profile.customer', compact('bookings', 'ratedTrackingIds'));
    }

    /**
     * Mark a booking as completed and award reward points to the customer.
     */
    public function complete($id) {
        $provider = Auth::user();
        if (!$provider || $provider->role !== 'provider') {
            abort(403, 'Only providers can complete jobs.');
        }

        $job = DB::table('trackings')
            ->where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$job) {
            abort(404, 'Job not found.');
        }

        if (($job->payment_status ?? 'pending') !== 'paid') {
            return back()->with('error', 'Cannot finish this job before customer payment is completed.');
        }

        if (!in_array($job->status, ['accepted', 'in_progress'], true)) {
            return back()->with('error', 'Only active jobs can be marked as completed.');
        }

        // Update status to completed
        $updated = DB::table('trackings')
            ->where('id', $id)
            ->where('provider_id', $provider->id)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->where('payment_status', 'paid')
            ->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'This job could not be completed. Please refresh and try again.');
        }

        // --- REWARD POINTS LOGIC ---
        // Reload the updated tracking record
        $completedJob = DB::table('trackings')->where('id', $id)->first();
        
        // Only award points if not already awarded (points_earned == 0)
        if ($completedJob && $completedJob->points_earned == 0) {
            $amount = $completedJob->amount; // final paid amount (after discount)
            // Example: 1 point per 10 BDT (higher amount = higher points)
            $points = max(1, floor($amount / 10));
            
            // Update tracking record with points earned
            DB::table('trackings')->where('id', $id)->update([
                'points_earned' => $points,
                'updated_at' => now(),
            ]);
            
            // Add points to customer's wallet
            $customer = User::find($completedJob->customer_id);
            if ($customer) {
                $customer->reward_points = ($customer->reward_points ?? 0) + $points;
                $customer->save();
                
                // Optional: trigger a notification
                // $customer->notify(new PointsAwardedNotification($points));
            }
        }

        // Send notifications
        $customer = User::find($job->customer_id);
        if ($customer) {
            $customer->notify(new BookingCompletedNotification());
        }

        $updatedTracking = DB::table('trackings')->where('id', $id)->first();
        $provider->notify(new BookingCompletedConfirmation($updatedTracking ?? $job));

        return redirect()->route('provider.show', $provider->id)->with('success', 'Job Completed! You earned points.');
    }

    public function decline($id) {
        DB::table('trackings')->where('id', $id)->update(['status' => 'declined', 'updated_at' => now()]);

        $tracking = DB::table('trackings')->where('id', $id)->first();
        $customer = $tracking ? User::find($tracking->customer_id) : null;
        if ($customer) {
            $customer->notify(new BookingDeclinedNotification());
        }

        return back()->with('success', 'Job Declined.');
    }
}