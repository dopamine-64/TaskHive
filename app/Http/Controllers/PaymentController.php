<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Tracking;
use App\Models\User;
use App\Notifications\PaymentConfirmationNotification;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function initiate($booking_id)
    {
        $booking = Tracking::findOrFail($booking_id);

        // Fetch values from the config we just created
        $store_id = config('services.sslcommerz.store_id');
        $store_password = config('services.sslcommerz.store_password');

        // --- NEW SAFETY CHECK ---
        if (empty($store_id) || empty($store_password)) {
            return back()->with('error', 'CRITICAL ERROR: Variables are STILL blank. Please check your Render Environment tab!');
        }

        // 1. Create pending transaction in our database
        $transaction_id = uniqid('TXN_') . time(); 
        
        $transaction = Transaction::create([
            'booking_id'     => $booking->id,
            'transaction_id' => $transaction_id,
            'amount'         => $booking->amount ?? 1000, 
            'currency'       => 'BDT',
            'status'         => 'pending',
        ]);

        // 2. Prepare SSLCommerz API Data
        $post_data = array();
        $post_data['store_id'] = $store_id;         
        $post_data['store_passwd'] = $store_password;  
        $post_data['total_amount'] = $transaction->amount;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = $transaction->transaction_id;
        
        // Return URLs (Where SSL sends the user back)
        $post_data['success_url'] = route('payment.success');
        $post_data['fail_url'] = route('payment.fail');
        $post_data['cancel_url'] = route('payment.cancel');

        // Customer Information
        $post_data['cus_name'] = auth()->user()->name ?? 'Customer';
        $post_data['cus_email'] = auth()->user()->email ?? 'customer@test.com';
        $post_data['cus_add1'] = $booking->address ?? 'Dhaka';
        $post_data['cus_city'] = "Dhaka";
        $post_data['cus_postcode'] = "1000";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = auth()->user()->phone ?? '01711111111';

        // Product Profile
        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Service Booking #" . $booking->id;
        $post_data['product_category'] = "Service";
        $post_data['product_profile'] = "general";

        // 3. Connect to SSLCommerz API
        // 👈 Use the config variable and cast it
        $is_sandbox = config('services.sslcommerz.is_sandbox'); 
        $is_sandbox = ($is_sandbox === true || $is_sandbox === 'true');
        
        $url = $is_sandbox 
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' 
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); 
        
        $content = curl_exec($handle);
        $curl_error = curl_error($handle); 
        curl_close($handle);

        if ($content === false) {
            return back()->with('error', 'cURL Connection Failed: ' . $curl_error);
        }

        $sslcommerzResponse = json_decode($content, true);

        // 4. Redirect to Gateway
        if (isset($sslcommerzResponse['status']) && $sslcommerzResponse['status'] == 'SUCCESS') {
            return redirect()->away($sslcommerzResponse['GatewayPageURL']);
        } else {
            $errorMessage = $sslcommerzResponse['failedreason'] ?? 'Unknown API Error. Raw: ' . $content;
            return back()->with('error', 'SSLCommerz Rejected: ' . $errorMessage);
        }
    }

    public function success(Request $request)
    {
        // 1. Find the transaction using the tran_id sent by SSLCommerz
        $transaction = Transaction::where('transaction_id', $request->input('tran_id'))->first();

        if ($transaction) {
            // 2. Find the associated booking
            $booking = Tracking::find($transaction->booking_id);

            // 🔥 THE FIX: Log the user back in using the customer ID from the booking!
            if ($booking) {
                Auth::loginUsingId($booking->customer_id);
            }

            // 3. Update Statuses
            $transaction->update(['status' => 'success']);
            
            if ($booking) {
                $booking->update(['payment_status' => 'paid']);

                $customer = User::find($booking->customer_id);
                if ($customer) {
                    $customer->notify(new PaymentConfirmationNotification());
                }

                $provider = User::find($booking->provider_id);
                if ($provider) {
                    $provider->notify(new PaymentReceivedNotification($booking, $transaction->amount));
                }
            }

            return redirect()->route('customer.profile')->with('success', 'Payment Successful! You can now view your invoice.');
        }

        return redirect()->route('login')->with('error', 'Transaction not found.');
    }

    public function fail(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->input('tran_id'))->first();

        if ($transaction) {
            $booking = Tracking::find($transaction->booking_id);

            // 🔥 THE FIX: Log the user back in!
            if ($booking) {
                Auth::loginUsingId($booking->customer_id);
            }

            $transaction->update(['status' => 'failed']);
            return redirect()->route('customer.profile')->with('error', 'Payment failed. Please try again.');
        }

        return redirect()->route('login')->with('error', 'Transaction not found.');
    }

    public function cancel(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->input('tran_id'))->first();

        if ($transaction) {
            $booking = Tracking::find($transaction->booking_id);

            // 🔥 THE FIX: Log the user back in!
            if ($booking) {
                Auth::loginUsingId($booking->customer_id);
            }

            $transaction->update(['status' => 'cancelled']);
            return redirect()->route('customer.profile')->with('error', 'Payment was cancelled.');
        }

        return redirect()->route('login')->with('error', 'Transaction not found.');
    }

    public function invoice($id)
    {
        // Fetch the booking with its related customer, provider, and service
        $booking = Tracking::with(['customer', 'provider', 'service'])->findOrFail($id);

        // Fetch the successful transaction details
        $transaction = Transaction::where('booking_id', $id)
            ->where('status', 'success')
            ->first();

        // Security check: Only the customer who booked it or the provider who accepted it can see the invoice
        if (auth()->id() !== $booking->customer_id && auth()->id() !== $booking->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('invoice.show', compact('booking', 'transaction'));
    }
}
