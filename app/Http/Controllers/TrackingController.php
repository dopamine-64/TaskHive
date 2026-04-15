<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function initiateTracking(Request $request, $providerId) {
        $trackingId = DB::table('trackings')->insertGetId([
            'customer_id' => Auth::id(),
            'provider_id' => $providerId,
            'status' => 'requested',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
        DB::table('trackings')->where('id', $id)->update(['status' => 'accepted', 'updated_at' => now()]);
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
    // Add this method to your existing TrackingController

// Add this method to your existing TrackingController

public function customerProfile()
{
    $userId = Auth::id();

    // Fetch ONLY the booking that is 'accepted'
    $activeBooking = DB::table('trackings')
        ->join('users', 'trackings.provider_id', '=', 'users.id')
        ->where('trackings.customer_id', $userId)
        ->where('trackings.status', 'accepted') 
        ->select('trackings.*', 'users.name as provider_name')
        ->first();

    // History
    $completedBookings = DB::table('trackings')
        ->join('users', 'trackings.provider_id', '=', 'users.id')
        ->where('trackings.customer_id', $userId)
        ->where('trackings.status', 'completed')
        ->select('trackings.*', 'users.name as provider_name')
        ->get();

    return view('profile.customer', compact('activeBooking', 'completedBookings'));
}
    public function complete($id) {
        DB::table('trackings')->where('id', $id)->update(['status' => 'completed', 'updated_at' => now()]);
        return redirect()->route('provider.show', Auth::id())->with('success', 'Job Completed!');
    }

    public function decline($id) {
        DB::table('trackings')->where('id', $id)->update(['status' => 'declined', 'updated_at' => now()]);
        return back()->with('success', 'Job Declined.');
    }
}