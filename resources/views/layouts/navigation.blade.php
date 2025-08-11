<nav x-data="{ open: false }" class="bg-gray-50 border-b border-gray-200 shadow-md rounded-b-xl">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- FLEX CONTAINER SPLIT LEFT + RIGHT -->
        <div class="flex justify-between items-center h-16">

            <!-- LEFT: Logo + Nav Links -->
            <div class="flex items-center gap-6">
           <a href="
                @if(Auth::user()->role === 'student')
                    {{ route('student.dashboard') }}
                @elseif(Auth::user()->role === 'lecturer')
                    {{ route('lecturer.dashboard') }}
                @elseif(Auth::user()->role === 'admin')
                    {{ route('admin.dashboard') }}
                @endif">
                <img src="/image/logo.jpg" alt="Logo" class="h-16 w-16 rounded-full">
            </a>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:flex items-center">
                    @if(Auth::user()->role === 'student')
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">Student Home</x-nav-link>
                        <x-nav-link :href="route('student.courses.index')" :active="request()->routeIs('student.courses.index')">Courses</x-nav-link>
                        <!-- Removed Assessments link -->
                        <x-nav-link :href="route('feedback.index')" :active="request()->routeIs('feedback.*')">
                            {{ __('Feedback') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.messages.index')" :active="request()->routeIs('student.messages.index')">Teams Chat</x-nav-link>
                    @elseif(Auth::user()->role === 'lecturer')
                        <x-nav-link :href="route('lecturer.dashboard')" :active="request()->routeIs('lecturer.dashboard')">Lecturer Home</x-nav-link>
                        <x-nav-link :href="route('lecturer.courses.index')" :active="request()->routeIs('lecturer.courses.index')">Courses</x-nav-link>
                        <!-- Removed Assessments link for lecturer -->
                        <x-nav-link :href="route('L.feedback.index')" :active="request()->routeIs('L.feedback.*')">
                            {{ __('Feedback') }}
                        </x-nav-link>
                        <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.index')">Teams Chat</x-nav-link>
                    @elseif(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-black-600">Admin Panel</x-nav-link>
                        <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.index')">Teams Chat</x-nav-link>
                        <x-nav-link :href="route('admin.feedback.index')" :active="request()->routeIs('admin.feedback.*')">
                            {{ __('Feedback') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- RIGHT: Settings Dropdown + Hamburger -->
            <div class="flex items-center space-x-4">
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-indigo-600 transition ease-in-out duration-150">
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <!-- Online Status Indicator -->
                                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="flex flex-col items-start">
                                        <div class="font-medium">{{ Auth::user()->name }}</div>
                                        <div class="flex items-center space-x-1">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-xs text-green-600">Online</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                        <div class="flex items-center space-x-1 mt-1">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-xs text-green-600 font-medium">Online</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-indigo-600 hover:bg-gray-100 transition-transform duration-300 transform hover:scale-105">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white shadow-lg rounded-b-lg p-4">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin Panel</x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="relative">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <!-- Online Status Indicator -->
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <div class="font-semibold text-lg text-indigo-700">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500 italic">{{ Auth::user()->email }}</div>
                        <div class="flex items-center space-x-1 mt-1">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

