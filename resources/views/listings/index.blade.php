<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Listings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($listings as $listing)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ $listing->title }}</h3>
                            <p class="text-gray-600 mb-2">{{ $listing->location }}</p>
                            <p class="text-gray-800 mb-4">{{ Str::limit($listing->description, 100) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">₹{{ number_format($listing->price, 2) }}</span>
                                <a href="{{ route(auth()->user()->role . '.listings.show', $listing) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-4">
                        No listings found
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $listings->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 