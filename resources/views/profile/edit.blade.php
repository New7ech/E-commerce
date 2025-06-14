<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Navigation for Profile Section --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold {{ request()->routeIs('profile.edit') ? 'underline' : '' }}">
                        {{ __('Edit Profile') }}
                    </a>
                    <a href="{{ route('profile.orders') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold {{ request()->routeIs('profile.orders') ? 'underline' : '' }}">
                        {{ __('Order History') }}
                    </a>
                    {{-- Add other profile related links here if needed --}}
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
