<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
    </x-slot>
    <main class="p-6 space-y-6 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-4">Edit User</h1>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-medium">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2" required>
                        <option value="admin" @if($user->role=='admin') selected @endif>Admin</option>
                        <option value="lecturer" @if($user->role=='lecturer') selected @endif>Lecturer</option>
                        <option value="student" @if($user->role=='student') selected @endif>Student</option>
                    </select>
                </div>
                <div class="flex space-x-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</x-app-layout> 