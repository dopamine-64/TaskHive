<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tracking;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Helper to log admin actions
    private function logActivity($action, $entityType = null, $entityId = null, $description = null)
    {
        ActivityLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'admin_id' => auth()->id(),
        ]);
    }

    // Dashboard
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalProviders = User::where('role', 'provider')->count();
        $totalBookings = Tracking::count();
        $revenue = Tracking::where('status', 'completed')->sum('amount');
        $pendingRequests = Tracking::where('status', 'requested')->count();
        $totalServices = Service::count();

        // Complaints overview for admin dashboard
        $complaintsCount = \App\Models\Complaint::count();
        $recentComplaints = \App\Models\Complaint::with(['user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalProviders', 'totalBookings', 'revenue', 'pendingRequests', 'totalServices', 'complaintsCount', 'recentComplaints'
        ));
    }

    // ---------- MANAGE USERS ----------
    public function manageUsers()
    {
        $users = User::where('role', 'user')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();

        $action = $user->is_banned ? 'Banned user' : 'Unbanned user';
        $this->logActivity($action, 'user', $user->id, "User {$user->email} was {$action}");
        return back()->with('success', $user->is_banned ? 'User banned.' : 'User unbanned.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $email = $user->email;
        $user->delete();
        $this->logActivity('Deleted user', 'user', $id, "Deleted user {$email}");
        return back()->with('success', 'User deleted.');
    }

    // ---------- MANAGE PROVIDERS ----------
    public function manageProviders()
    {
        $providers = User::where('role', 'provider')->with('providerProfile')->latest()->paginate(10);
        return view('admin.providers', compact('providers'));
    }

    public function verifyProvider($id)
    {
        $provider = User::findOrFail($id);
        $profile = $provider->providerProfile;
        if ($profile) {
            $profile->is_verified = !$profile->is_verified;
            $profile->save();
            $this->logActivity($profile->is_verified ? 'Verified provider' : 'Unverified provider', 'provider', $id, "Provider {$provider->email} verification changed");
        }
        return back()->with('success', 'Verification status updated.');
    }

    public function deleteProvider($id)
    {
        $provider = User::findOrFail($id);
        $email = $provider->email;
        $provider->delete();
        $this->logActivity('Deleted provider', 'provider', $id, "Deleted provider {$email}");
        return back()->with('success', 'Provider removed.');
    }

    // ---------- MANAGE BOOKINGS ----------
    public function manageBookings()
    {
        $bookings = Tracking::with(['customer', 'provider', 'service'])->latest()->paginate(15);
        return view('admin.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Tracking::findOrFail($id);
        $oldStatus = $booking->status;
        $booking->status = $request->status;
        $booking->save();
        $this->logActivity('Updated booking status', 'booking', $id, "Changed from {$oldStatus} to {$booking->status}");
        return back()->with('success', 'Booking status updated.');
    }

    public function deleteBooking($id)
    {
        $booking = Tracking::findOrFail($id);
        $booking->delete();
        $this->logActivity('Deleted booking', 'booking', $id, "Deleted booking #{$id}");
        return back()->with('success', 'Booking record deleted.');
    }

    // ---------- MANAGE SERVICES (NEW) ----------
    public function manageServices()
    {
        $services = Service::with('provider')->latest()->paginate(10);
        return view('admin.services', compact('services'));
    }

    public function toggleServiceStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();
        $status = $service->is_active ? 'activated' : 'deactivated';
        $this->logActivity('Toggled service status', 'service', $id, "Service '{$service->title}' {$status}");
        return back()->with('success', "Service {$status}.");
    }

    public function editService($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.service-edit', compact('service'));
    }

    public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'location' => 'required|string',
            'duration' => 'nullable|integer',
        ]);

        $service->update($request->only(['title', 'description', 'price', 'category', 'location', 'duration']));
        $this->logActivity('Updated service', 'service', $id, "Updated service '{$service->title}'");
        return redirect()->route('admin.services')->with('success', 'Service updated.');
    }

    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $title = $service->title;
        $service->delete();
        $this->logActivity('Deleted service', 'service', $id, "Deleted service '{$title}'");
        return back()->with('success', 'Service deleted.');
    }

    // ---------- COMPLAINTS MANAGEMENT ----------
    public function manageComplaints()
    {
        $complaints = \App\Models\Complaint::with('user')->latest()->paginate(15);
        return view('admin.complaints', compact('complaints'));
    }

    public function showComplaint($id)
    {
        $complaint = \App\Models\Complaint::with('user','resolver')->findOrFail($id);
        return view('admin.complaint_show', compact('complaint'));
    }

    public function updateComplaint(Request $request, $id)
    {
        $complaint = \App\Models\Complaint::findOrFail($id);
        $request->validate([
            'status' => 'required|string',
            'admin_notes' => 'nullable|string'
        ]);

        $complaint->status = $request->status;
        $complaint->admin_notes = $request->admin_notes;
        if ($request->status === 'resolved') {
            $complaint->resolved_by = auth()->id();
        }
        $complaint->save();

        $this->logActivity('Updated complaint', 'complaint', $id, "Status set to {$complaint->status}");
        return back()->with('success', 'Complaint updated.');
    }

    public function deleteComplaint($id)
    {
        $complaint = \App\Models\Complaint::findOrFail($id);
        $complaint->delete();
        $this->logActivity('Deleted complaint', 'complaint', $id, "Complaint deleted");
        return back()->with('success', 'Complaint removed.');
    }

    // ---------- SYSTEM ACTIVITIES ----------
    public function activities()
    {
        $activities = ActivityLog::with('admin')->latest()->paginate(20);
        return view('admin.activities', compact('activities'));
    }
}