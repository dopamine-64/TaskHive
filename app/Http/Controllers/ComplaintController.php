<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ActivityLog;
use App\Models\User;

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

        $complaint = Complaint::create([
            'user_id' => auth()->id(),
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'description' => $request->description,
            'evidence' => $request->evidence,
        ]);

        // Log activity so admins can see this in System Activities
        $user = User::find(auth()->id());
        $desc = "Complaint #{$complaint->id} submitted by " . ($user?->name ?? 'User') . ".";
        if ($complaint->target_type) {
            $desc .= " Related to: {$complaint->target_type} " . ($complaint->target_id ? "(#{$complaint->target_id})" : '');
        }

        ActivityLog::create([
            'action' => 'Submitted complaint',
            'entity_type' => 'complaint',
            'entity_id' => $complaint->id,
            'description' => $desc,
            'admin_id' => null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Complaint submitted. Administrators will review it.');
    }
}
