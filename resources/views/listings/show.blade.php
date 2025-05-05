<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $listing->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $listing->title }}</h3>
                        <p class="text-gray-600">{{ $listing->location }}</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2">Description</h4>
                        <p class="text-gray-800">{{ $listing->description }}</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2">Price</h4>
                        <p class="text-2xl font-bold text-blue-600">₹{{ number_format($listing->price, 2) }} per day</p>
                    </div>

                    @auth
                        @if(auth()->user()->isTenant() && $listing->status === 'available')
                            <div class="mt-8">
                                <a href="{{ route('tenant.bookings.create', ['listing_id' => $listing->id]) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Book Now
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->id === $listing->user_id)
                            <div class="mt-8 flex space-x-4">
                                <a href="{{ route('renter.listings.edit', $listing) }}" 
                                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Listing
                                </a>
                                <form action="{{ route('renter.listings.destroy', $listing) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this listing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Delete Listing
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">
                                Login to book this listing
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 