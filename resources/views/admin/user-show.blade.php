<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">User Details</h2>
    </x-slot>
    <main class="p-6 space-y-6 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-4">User Details</h1>
            <div class="mb-2"><strong>ID:</strong> {{ $user->id }}</div>
            <div class="mb-2"><strong>Name:</strong> {{ $user->name }}</div>
            <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
            <div class="mb-2"><strong>Role:</strong> {{ ucfirst($user->role) }}</div>
            <div class="mt-6 flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Back</a>
            </div>
        </div>
    </main>
</x-app-layout> 