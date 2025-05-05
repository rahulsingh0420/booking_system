<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Listing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:tenant')->only(['create', 'store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isRenter()) {
            $bookings = Booking::whereHas('listing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['user', 'listing'])->latest()->paginate(10);
        } else {
            $bookings = $user->bookings()->with('listing')->latest()->paginate(10);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $listing = Listing::findOrFail($request->listing_id);
        $this->authorize('create', [Booking::class, $listing]);
        return view('bookings.create', compact('listing'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $listing = Listing::findOrFail($validated['listing_id']);
        $this->authorize('create', [Booking::class, $listing]);

        // Check if the listing is available for the selected dates
        $isAvailable = !Booking::where('listing_id', $listing->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })->exists();

        if (!$isAvailable) {
            return back()->withErrors(['dates' => 'The selected dates are not available.']);
        }

        // Calculate total price
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $days * $listing->price;

        $booking = auth()->user()->bookings()->create([
            'listing_id' => $validated['listing_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.bookings.show', $booking)
            ->with('success', 'Booking request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Only allow renters to approve/reject bookings
        if (auth()->user()->isRenter() && !in_array($validated['status'], ['confirmed', 'cancelled'])) {
            return back()->withErrors(['status' => 'Invalid status for renter.']);
        }

        // Only allow tenants to cancel bookings
        if (auth()->user()->isTenant() && $validated['status'] !== 'cancelled') {
            return back()->withErrors(['status' => 'Invalid status for tenant.']);
        }

        $booking->update($validated);

        // Redirect based on user role
        if (auth()->user()->isRenter()) {
            return redirect()->route('renter.bookings.show', $booking)
                ->with('success', 'Booking status updated successfully.');
        }

        return redirect()->route('tenant.bookings.show', $booking)
            ->with('success', 'Booking status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        $booking->delete();

        return redirect()->route('tenant.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}
