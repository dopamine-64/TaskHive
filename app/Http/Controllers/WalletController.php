<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $totalEarned = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'earned')
            ->sum('points');
        
        $totalRedeemed = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'redeemed')
            ->sum('points');
        
        return view('wallet.index', compact('user', 'transactions', 'totalEarned', 'totalRedeemed'));
    }
    
    public function addPoints($userId, $points, $bookingId = null, $description = null)
    {
        $user = User::findOrFail($userId);
        $user->wallet_points += $points;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'points' => $points,
            'type' => 'earned',
            'description' => $description ?? "Earned {$points} points",
        ]);
        
        return true;
    }
    
    public function deductPoints($userId, $points, $bookingId = null, $description = null)
    {
        $user = User::findOrFail($userId);
        
        if ($user->wallet_points < $points) {
            return false;
        }
        
        $user->wallet_points -= $points;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'points' => -$points,
            'type' => 'redeemed',
            'description' => $description ?? "Redeemed {$points} points",
        ]);
        
        return true;
    }
    
    public function addEarnings($userId, $amount, $bookingId = null, $description = null)
    {
        $user = User::findOrFail($userId);
        $user->wallet_balance += $amount;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'type' => 'deposit',
            'description' => $description ?? "Earned {$amount} Tk",
        ]);
        
        return true;
    }
    
    public function refundPoints($userId, $points, $bookingId = null, $description = null)
    {
        $user = User::findOrFail($userId);
        $user->wallet_points += $points;
        $user->save();
        
        WalletTransaction::create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'points' => $points,
            'type' => 'refund',
            'description' => $description ?? "Refunded {$points} points",
        ]);
        
        return true;
    }
}