<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @forelse($bookings as $booking)
                        <div class="border-b border-gray-200 py-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $booking->listing->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $booking->listing->location }}</p>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Dates: {{ $booking->start_date->format('M j, Y') }} - {{ $booking->end_date->format('M j, Y') }}</p>
                                        <p>Total: ₹{{ number_format($booking->total_price, 2) }}</p>
                                    </div>
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
                            @if(auth()->user()->isTenant())
                                <a href="{{ route('tenant.listings.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-900">
                                    Browse Listings
                                </a>
                            @endif
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 