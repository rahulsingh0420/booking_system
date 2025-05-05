<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-6" x-data="{ role: '{{ old('role', 'tenant') }}' }">
            <x-input-label :value="__('I want to')" class="text-lg font-medium text-gray-900" />
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all duration-200 hover:border-blue-300"
                      :class="{ 'border-blue-500 ring-2 ring-blue-500': role === 'tenant', 'border-gray-300': role !== 'tenant' }">
                    <input type="radio" name="role" value="tenant" class="sr-only" x-model="role" {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }}>
                    <div class="flex flex-1">
                        <div class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">Book Properties</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                <svg class="mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                I want to find and book properties
                            </span>
                        </div>
                    </div>
                    <div class="pointer-events-none absolute -inset-px rounded-lg border-2 transition-colors duration-200"
                         :class="{ 'border-blue-500': role === 'tenant', 'border-transparent': role !== 'tenant' }"
                         aria-hidden="true"></div>
                </label>

                <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all duration-200 hover:border-blue-300"
                      :class="{ 'border-blue-500 ring-2 ring-blue-500': role === 'renter', 'border-gray-300': role !== 'renter' }">
                    <input type="radio" name="role" value="renter" class="sr-only" x-model="role" {{ old('role') === 'renter' ? 'checked' : '' }}>
                    <div class="flex flex-1">
                        <div class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900">List Properties</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                <svg class="mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd" />
                                </svg>
                                I want to list and manage my properties
                            </span>
                        </div>
                    </div>
                    <div class="pointer-events-none absolute -inset-px rounded-lg border-2 transition-colors duration-200"
                         :class="{ 'border-blue-500': role === 'renter', 'border-transparent': role !== 'renter' }"
                         aria-hidden="true"></div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
