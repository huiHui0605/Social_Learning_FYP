<x-guest-layout>
    <x-slot name="title">Register - Social Learning Hub</x-slot>
    
    <!-- Page Wrapper with Background Image -->
    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-8">

        <!-- Optional: Semi-transparent overlay over background -->
        <div class="absolute inset-0 bg-white/50 backdrop-blur-sm z-0"></div>

        <!-- Logo -->
        <div class="relative z-10 flex justify-center mb-2">
            <a href="/">
                <img src="/image/logo.jpg" alt="eLearning Logo" class="w-20 h-20 rounded-full shadow-md">
            </a>
        </div>

         <!-- Welcome Text -->
        <h2 class="relative z-10 text-2xl font-bold text-gray-800 mb-1">Welcome to <span class="text-indigo-600">Social Learning</span></h2>
        <p class="relative z-10 text-gray-600 mb-4 text-sm">Please register to continue</p>

        <!-- Registration Form Card -->
        <div class="relative z-10 w-full max-w-sm bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Role -->
                <div class="mb-3">
                    <x-input-label for="role" :value="__('Register as')" />
                    <select id="role" name="role" required class="block mt-1 w-full">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" type="password" name="password" required class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation" required class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Register Button -->
                <div class="flex items-center justify-between mt-4">
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="px-4 py-2 text-sm font-medium rounded-md">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <style>
        body {
            background-image: url('/image/login_regis.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</x-guest-layout>
