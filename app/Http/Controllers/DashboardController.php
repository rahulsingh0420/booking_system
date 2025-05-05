<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $data = [
                'totalUsers' => User::count(),
                'totalListings' => Listing::count(),
                'totalBookings' => Booking::count(),
                'pendingRenters' => User::where('role', 'renter')
                    ->where('is_approved', false)
                    ->latest()
                    ->get(),
                'approvedRenters' => User::where('role', 'renter')
                    ->where('is_approved', true)
                    ->latest()
                    ->get()
            ];
            return view('dashboard', $data);
        } elseif ($user->isRenter()) {
            $data = [
                'myListings' => $user->listings()->latest()->get(),
                'recentBookings' => Booking::whereHas('listing', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->with(['user', 'listing'])->latest()->get(),
            ];
            return view('dashboard', $data);
        } else {
            $data = [
                'myBookings' => $user->bookings()->with('listing')->latest()->get(),
                'availableListings' => Listing::where('status', 'available')
                    ->where('user_id', '!=', $user->id)
                    ->latest()
                    ->get()
            ];
            return view('dashboard', $data);
        }
    }

    public function approveRenter(User $user)
    {
        if (!$user->isRenter()) {
            abort(404);
        }

        // Check if renter has any active bookings
        $hasActiveBookings = $user->listings()
            ->whereHas('bookings', function($query) {
                $query->where('status', 'approved');
            })
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()->with('error', 'Cannot approve renter with active bookings.');
        }

        $user->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Renter approved successfully.');
    }

    public function rejectRenter(User $user)
    {
        if (!$user->isRenter()) {
            abort(404);
        }

        // Check if renter has any active bookings
        $hasActiveBookings = $user->listings()
            ->whereHas('bookings', function($query) {
                $query->where('status', 'approved');
            })
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()->with('error', 'Cannot reject renter with active bookings.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Renter rejected and removed from the system.');
    }

    public function deactivateRenter(User $user)
    {
        if (!$user->isRenter()) {
            abort(404);
        }

        // Check if renter has any active bookings
        $hasActiveBookings = $user->listings()
            ->whereHas('bookings', function($query) {
                $query->where('status', 'approved');
            })
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()->with('error', 'Cannot deactivate renter with active bookings.');
        }

        $user->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Renter deactivated successfully.');
    }
}
