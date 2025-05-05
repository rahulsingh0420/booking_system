<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->isAdmin())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-800">Total Users</h4>
                                <p class="text-2xl font-bold text-blue-900">{{ $totalUsers }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800">Total Listings</h4>
                                <p class="text-2xl font-bold text-green-900">{{ $totalListings }}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-purple-800">Total Bookings</h4>
                                <p class="text-2xl font-bold text-purple-900">{{ $totalBookings }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Renter Management Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Renter Management</h3>
                            <div class="flex space-x-2">
                                <button type="button" class="px-4 py-2 text-sm font-medium rounded-md bg-blue-500 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="pending-tab">
                                    Pending Approvals
                                </button>
                                <button type="button" class="px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="approved-tab">
                                    Approved Renters
                                </button>
                            </div>
                        </div>

                        <!-- Pending Renters Section -->
                        <div id="pending-renters" class="space-y-4">
                            @forelse($pendingRenters as $renter)
                                <div class="border-b border-gray-200 py-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">{{ $renter->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $renter->email }}</p>
                                            <p class="text-sm text-gray-500">Registered: {{ $renter->created_at->format('M j, Y') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="{{ route('admin.renters.approve', $renter) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.renters.reject', $renter) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">No pending renter approvals.</p>
                            @endforelse
                        </div>

                        <!-- Approved Renters Section -->
                        <div id="approved-renters" class="space-y-4 hidden">
                            @forelse($approvedRenters as $renter)
                                <div class="border-b border-gray-200 py-4 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-medium text-gray-900">{{ $renter->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $renter->email }}</p>
                                            <p class="text-sm text-gray-500">Approved: {{ $renter->updated_at->format('M j, Y') }}</p>
                                            <p class="text-sm text-gray-500">
                                                Active Bookings: {{ $renter->listings()->whereHas('bookings', function($q) {
                                                    $q->where('status', 'approved');
                                                })->count() }}
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="{{ route('admin.renters.deactivate', $renter) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                                    Deactivate
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">No approved renters found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->isRenter())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">My Listings</h3>
                            <a href="{{ route('renter.listings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New Listing
                            </a>
                        </div>
                        @forelse($myListings as $listing)
                            <div class="border-b border-gray-200 py-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $listing->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $listing->location }}</p>
                                        <p class="text-sm text-gray-500">₹{{ number_format($listing->price, 2) }} per day</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('renter.listings.edit', $listing) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Edit
                                        </a>
                                        <a href="{{ route('renter.listings.show', $listing) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No listings found. Create your first listing!</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Bookings</h3>
                        @forelse($recentBookings as $booking)
                            <div class="border-b border-gray-200 py-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $booking->listing->title }}</h4>
                                        <p class="text-sm text-gray-600">Booked by: {{ $booking->user->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $booking->start_date->format('M j, Y') }} - {{ $booking->end_date->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $booking->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <a href="{{ route('renter.bookings.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No recent bookings found.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            @if(auth()->user()->isTenant())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">My Bookings</h3>
                        @forelse($myBookings as $booking)
                            <div class="border-b border-gray-200 py-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $booking->listing->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $booking->listing->location }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $booking->start_date->format('M j, Y') }} - {{ $booking->end_date->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $booking->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <a href="{{ route('tenant.bookings.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500">No bookings found.</p>
                                <a href="{{ route('tenant.listings.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-900">
                                    Browse Listings
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
    @endpush
</x-app-layout>
