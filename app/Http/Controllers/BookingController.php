<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Tracking;
use App\Models\User;
use App\Notifications\BookingRequestNotification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Show booking form
    public function create($serviceId)
    {
        // Only customers can book
        if (auth()->user()->role !== 'user') {
            abort(403, 'Only customers can book services.');
        }
        
        $service = Service::findOrFail($serviceId);
        return view('booking.create', compact('service'));
    }

    // Store booking
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'address' => 'required|string',
        ]);

        $service = Service::findOrFail($request->service_id);

        // 1. Prevent customer from booking the exact same service if they already have an active request
        $activeServiceBooking = Tracking::where('customer_id', Auth::id())
            ->where('service_id', $request->service_id)
            ->whereIn('status', ['requested', 'accepted', 'in_progress'])
            ->first();

        if ($activeServiceBooking) {
            return back()->with('error', 'You already have an active request for this service. Please wait for it to finish or cancel it first.');
        }

        // 2. Prevent customer from double-booking themselves at the exact same date and time
        $customerTimeClash = Tracking::where('customer_id', Auth::id())
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['requested', 'accepted', 'in_progress'])
            ->first();

        if ($customerTimeClash) {
            return back()->with('error', 'You already have a service booked at this exact date and time.');
        }

        // 3. Check if the provider is available
        $existingBooking = Tracking::where('provider_id', $service->user_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['requested', 'accepted'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Provider is not available at this date and time. Please choose another slot.');
        }

        // Create booking
        $booking = Tracking::create([
            'service_id' => $service->id,
            'customer_id' => Auth::id(),
            'provider_id' => $service->user_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'address' => $request->address,
            'duration' => $service->duration ?? 60,
            'amount' => $service->price,             
            'payment_status' => 'pending',
            'status' => 'requested',
        ]);

        $provider = User::find($booking->provider_id);
        if ($provider) {
            $provider->notify(new BookingRequestNotification());
        }

        return redirect()->route('customer.profile')->with('success', 'Booking request sent to provider!');
    }

    // Show reschedule form
    public function rescheduleForm($id)
    {
        $booking = Tracking::where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        if ($booking->status !== 'requested') {
            return redirect()->route('customer.profile')->with('error', 'Cannot reschedule - booking already accepted or completed.');
        }

        return view('booking.reschedule', compact('booking'));
    }

    // Update booking (reschedule)
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        $booking = Tracking::where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        // Check availability for new time
        $existingBooking = Tracking::where('provider_id', $booking->provider_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->where('id', '!=', $id)
            ->whereIn('status', ['requested', 'accepted'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Provider not available at this new time.');
        }

        $booking->update([
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Booking rescheduled successfully!');
    }

    // Cancel booking
    public function cancel($id)
    {
        $booking = Tracking::where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        // Only prevent cancellation if already completed
        if ($booking->status == 'completed') {
            return back()->with('error', 'Cannot cancel completed booking.');
        }

        // Using 'declined' here so your database doesn't throw the 1265 Data Truncated error!
        $booking->update(['status' => 'declined']);

        return redirect()->route('customer.profile')->with('success', 'Booking cancelled successfully.');
    }
}
