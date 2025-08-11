<x-guest-layout>
    <x-slot name="title">Login - Social Learning Hub</x-slot>
    <!-- Fullscreen Background with Image -->
    <div class="relative min-h-screen flex flex-col items-center justify-center px-4 py-8">

        <!-- Optional: Semi-transparent overlay over background -->
        <div class="absolute inset-0 bg-white/50 backdrop-blur-sm z-0"></div>

        <!-- Login Content -->
        <div class="relative z-10 w-full max-w-sm flex flex-col items-center">

            <!-- Custom Logo -->
            <div class="flex justify-center mb-4">
                <a href="/">
                    <img src="/image/logo.jpg" alt="eLearning Logo" class="w-20 h-20 rounded-full shadow-md">
                </a>
            </div>

            <!-- Welcome Text -->
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome to <span class="text-indigo-600">Social Learning</span></h2>
            <p class="text-gray-600 mb-4 text-sm">Please log in to continue</p>

            <!-- Login Card (white box) -->
            <div class="w-full bg-white rounded-lg shadow-sm p-6 border border-gray-200">

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus class="block mt-1 w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" type="password" name="password" required class="block mt-1 w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mb-4">
                        <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ml-2">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                                {{ __('Forgot?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <div>
                        <x-primary-button class="w-full justify-center py-2 text-sm font-medium rounded-md">
                            {{ __('Log In') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="mt-4 text-center">
                    <span class="text-sm text-gray-600">Don't have an account?</span>
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:underline text-sm font-medium ml-1">Register</a>
                </div>
            </div>
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
