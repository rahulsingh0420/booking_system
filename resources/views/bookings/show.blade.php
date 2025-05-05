<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ $booking->listing->title }}</h3>
                        <p class="text-gray-600">{{ $booking->listing->location }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Booking Information</h4>
                            <dl class="mt-2 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->start_date->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->end_date->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Price</dt>
                                    <dd class="text-sm text-gray-900">₹{{ number_format($booking->total_price, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $booking->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Contact Information</h4>
                            <dl class="mt-2 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Booked By</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->user->email }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if(auth()->user()->id === $booking->listing->user_id && $booking->status === 'pending')
                        <div class="mt-6 flex space-x-4">
                            <form action="{{ auth()->user()->isRenter() ? route('renter.bookings.update', $booking) : route('tenant.bookings.update', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Approve Booking
                                </button>
                            </form>

                            <form action="{{ auth()->user()->isRenter() ? route('renter.bookings.update', $booking) : route('tenant.bookings.update', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Reject Booking
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(auth()->user()->id === $booking->user_id && $booking->status === 'pending')
                        <div class="mt-6">
                            <form action="{{ auth()->user()->isRenter() ? route('renter.bookings.update', $booking) : route('tenant.bookings.update', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel Booking
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 