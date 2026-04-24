<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'nullable|string|max:100',
            'target_id' => 'nullable|integer',
            'description' => 'required|string',
            'evidence' => 'nullable|string',
        ]);

        Complaint::create([
            'user_id' => auth()->id(),
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'description' => $request->description,
            'evidence' => $request->evidence,
        ]);

        return redirect()->route('dashboard')->with('success', 'Complaint submitted. Administrators will review it.');
    }
}
