<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Tracking;
use App\Models\User;
use App\Notifications\BookingRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Show booking form
    public function create($serviceId)
    {
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
            'redeem_points' => 'nullable|integer|min:0',   // New field
        ]);

        $service = Service::findOrFail($request->service_id);
        $customer = Auth::user();
        $baseAmount = $service->price;

        // --- Reward Points Redemption ---
        $pointsToRedeem = (int) $request->input('redeem_points', 0);
        $pointsAvailable = $customer->reward_points ?? 0;
        $pointsValue = 0;   // discount in BDT

        if ($pointsToRedeem > 0) {
            // Limit to available points
            if ($pointsToRedeem > $pointsAvailable) {
                return back()->with('error', "You only have $pointsAvailable points available.");
            }
            // Conversion: 10 points = 1 BDT (adjust as needed)
            $pointsValue = floor($pointsToRedeem / 10);
            // Cannot discount more than the service price
            if ($pointsValue > $baseAmount) {
                $pointsValue = $baseAmount;
                $pointsToRedeem = $pointsValue * 100;
            }
        }

        $finalAmount = $baseAmount - $pointsValue;
        if ($finalAmount < 0) $finalAmount = 0;

        // 1. Prevent customer from booking the same service twice
        $activeServiceBooking = Tracking::where('customer_id', $customer->id)
            ->where('service_id', $service->id)
            ->whereIn('status', ['requested', 'accepted', 'in_progress'])
            ->first();

        if ($activeServiceBooking) {
            return back()->with('error', 'You already have an active request for this service. Please wait for it to finish or cancel it first.');
        }

        // 2. Prevent time clash for the same customer
        $customerTimeClash = Tracking::where('customer_id', $customer->id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['requested', 'accepted', 'in_progress'])
            ->first();

        if ($customerTimeClash) {
            return back()->with('error', 'You already have a service booked at this exact date and time.');
        }

        // 3. Check provider availability
        $existingBooking = Tracking::where('provider_id', $service->user_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['requested', 'accepted'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Provider is not available at this date and time. Please choose another slot.');
        }

        // --- Begin atomic transaction (points deduction + booking creation) ---
        DB::beginTransaction();

        try {
            // Deduct points if any
            if ($pointsToRedeem > 0) {
                $customer->reward_points -= $pointsToRedeem;
                $customer->save();
            }

            // Create the booking with discounted amount
            $booking = Tracking::create([
                'service_id' => $service->id,
                'customer_id' => $customer->id,
                'provider_id' => $service->user_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'address' => $request->address,
                'duration' => $service->duration ?? 60,
                'amount' => $finalAmount,
                'points_used' => $pointsToRedeem,
                'payment_status' => 'pending',
                'status' => 'requested',
            ]);

            DB::commit();

            $provider = User::find($booking->provider_id);
            if ($provider) {
                $provider->notify(new BookingRequestNotification($booking));
            }

            $successMessage = 'Booking request sent to provider!';
            if ($pointsToRedeem > 0) {
                $successMessage .= " You redeemed $pointsToRedeem points and saved ৳$pointsValue.";
            }
            return redirect()->route('customer.profile')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
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

        // Check provider availability for the new time
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

        if ($booking->status == 'completed') {
            return back()->with('error', 'Cannot cancel completed booking.');
        }

        // Use 'declined' status – matches your existing logic
        $booking->update(['status' => 'declined']);

        // Optionally, return used points if you want to refund them
        if ($booking->points_used > 0) {
            $customer = Auth::user();
            $customer->reward_points += $booking->points_used;
            $customer->save();
            // You might also want to add a flash message
        }

        return redirect()->route('customer.profile')->with('success', 'Booking cancelled successfully.');
    }
}