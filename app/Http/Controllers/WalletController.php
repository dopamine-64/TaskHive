<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tracking;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {   
        $user = Auth::user();
        
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->where(function($q) {
                $q->whereNotNull('amount')
                  ->where('amount', '!=', 0);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Calculate totals for BDT
        $totalDeposited = WalletTransaction::where('user_id', $user->id)
            ->whereIn('type', ['deposit', 'earned', 'refund'])
            ->sum('amount');
        
        $totalWithdrawn = WalletTransaction::where('user_id', $user->id)
            ->whereIn('type', ['payment', 'withdrawal'])
            ->sum('amount');
        
        $currentBalance = $user->wallet_balance ?? 0;
        
        return view('wallet.index', compact('user', 'transactions', 'totalDeposited', 'totalWithdrawn', 'currentBalance'));
    }
    
    // Add money to wallet (when customer deposits or earns)
    public function addMoney($userId, $amount, $bookingId = null, $description = null, $type = 'deposit')
    {
        $user = User::findOrFail($userId);
        $user->wallet_balance += $amount;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description ?? "Added {$amount} Tk",
        ]);
        
        return true;
    }
    
    // Deduct money from wallet (when customer pays or redeems)
    public function deductMoney($userId, $amount, $bookingId = null, $description = null, $type = 'payment')
    {
        $user = User::findOrFail($userId);
        
        if ($user->wallet_balance < $amount) {
            return false;
        }
        
        $user->wallet_balance -= $amount;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'amount' => -$amount,
            'type' => $type,
            'description' => $description ?? "Deducted {$amount} Tk",
        ]);
        
        return true;
    }
    
    // Add earnings to provider wallet
    public function addEarnings($userId, $amount, $bookingId = null, $description = null)
    {
        return $this->addMoney($userId, $amount, $bookingId, $description ?? "Earned {$amount} Tk from booking", 'earned');
    }
    
    // Refund money to customer wallet
    public function refund($userId, $amount, $bookingId = null, $description = null)
    {
        return $this->addMoney($userId, $amount, $bookingId, $description ?? "Refunded {$amount} Tk", 'refund');
    }
    
    // Check if user has sufficient balance
    public function hasSufficientBalance($userId, $amount)
    {
        $user = User::findOrFail($userId);
        return $user->wallet_balance >= $amount;
    }
    
    // Get current balance
    public function getBalance($userId)
    {
        $user = User::findOrFail($userId);
        return $user->wallet_balance;
    }
    
    // NEW: Pay for booking using wallet
    public function payWithWallet($bookingId)
    {
        $booking = Tracking::where('id', $bookingId)
            ->where('customer_id', Auth::id())
            ->firstOrFail();
        
        $amount = $booking->amount ?? 0;
        $user = Auth::user();
        
        // Check if user has sufficient balance
        if ($user->wallet_balance < $amount) {
            return redirect()->route('customer.profile')->with('error', 'Insufficient wallet balance. Please add money or use other payment method.');
        }
        
        // Deduct from wallet
        $user->wallet_balance -= $amount;
        $user->save();
        
        // Record transaction
        WalletTransaction::create([
            'user_id' => $user->id,
            'booking_id' => $bookingId,
            'amount' => -$amount,
            'type' => 'payment',
            'description' => "Payment for booking #{$bookingId} - ৳{$amount}",
        ]);
        
        // Update booking payment status
        $booking->payment_status = 'paid';
        $booking->save();
        
        return redirect()->route('customer.profile')->with('success', 'Payment successful!');
    }
    
    // Clean up old point transactions (run once via route)
    public function cleanupPointTransactions()
    {
        $deleted = WalletTransaction::whereNull('amount')
            ->orWhere('amount', 0)
            ->delete();
        
        return response()->json(['message' => "Deleted {$deleted} old point transactions"]);
    }
}
