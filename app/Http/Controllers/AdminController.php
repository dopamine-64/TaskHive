<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function getUsers()
    {
        $users = User::all();

        return response()->json([
            'message' => 'All users retrieved successfully',
            'data' => $users
        ]);
    }
}