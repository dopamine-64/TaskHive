<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Notifications\BookingAcceptedNotification;
use App\Notifications\BookingCompletedNotification;
use App\Notifications\BookingDeclinedNotification;
use App\Notifications\BookingRequestNotification;
use App\Notifications\BookingAcceptedConfirmation;
use App\Notifications\BookingCompletedConfirmation;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function initiateTracking(Request $request, $providerId) {
    // Get service_id from request or use provider's first service
    $serviceId = $request->service_id;
    
    if (!$serviceId) {
        // If no service_id provided, get provider's first service
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

        return view('tracking.live', compact('tracking', 'customer', 'provider', 'customerLocation', 'providerLocation'));
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

    // This method handles the Provider Profile logic
    public function showProviderProfile($id) {
        $provider = User::findOrFail($id);
        
        // 1. Fetch NEW requests (Status: requested)
        $incomingRequests = DB::table('trackings')
            ->join('users', 'trackings.customer_id', '=', 'users.id')
            ->where('trackings.provider_id', $id)
            ->where('trackings.status', 'requested')
            ->select('trackings.*', 'users.name as customer_name')
            ->get();

        // 2. Fetch ACTIVE jobs (Status: accepted or in_progress)
        $activeJobs = DB::table('trackings')
            ->join('users', 'trackings.customer_id', '=', 'users.id')
            ->where('trackings.provider_id', $id)
            ->whereIn('trackings.status', ['accepted', 'in_progress'])
            ->select('trackings.*', 'users.name as customer_name')
            ->get();

        return view('services.profile-show', compact('provider', 'incomingRequests', 'activeJobs'));
    }

    // Customer Profile - Shows ALL bookings
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

        return view('profile.customer', compact('bookings'));
    }

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

        $customer = User::find($job->customer_id);
        if ($customer) {
            $customer->notify(new BookingCompletedNotification());
        }

        $updatedTracking = DB::table('trackings')->where('id', $id)->first();
        $provider->notify(new BookingCompletedConfirmation($updatedTracking ?? $job));

        return redirect()->route('provider.show', $provider->id)->with('success', 'Job Completed!');
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
