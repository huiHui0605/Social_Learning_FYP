<x-app-layout>
    <x-slot name="title">System Administration - Admin</x-slot>
    <!-- HEADER SLOT -->
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">System Administration</h2>
        </div>
    </x-slot>

    <!-- MAIN CONTENT -->
    <main class="p-6 space-y-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Dashboard Info Box -->
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-indigo-700">System Administration</h1>
                <p class="text-gray-500">Overview of administrative actions.</p>
            </div>

            <!-- Search and Filter Form -->
            <div class="mb-4">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="border rounded px-3 py-2 w-96" />
                    <select name="role" class="border rounded px-3 py-2 w-96">
                        <option value="">All Roles</option>
                        <option value="admin" @if(request('role')=='admin') selected @endif>Admin</option>
                        <option value="lecturer" @if(request('role')=='lecturer') selected @endif>Lecturer</option>
                        <option value="student" @if(request('role')=='student') selected @endif>Student</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Search</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Reset</a>
                    @endif
                </form>
            </div>

            <!-- User List Table -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">User List</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border">
                        <thead class="bg-indigo-600 text-white">
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Role</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $user->id }}</td>
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                                    <td class="px-4 py-2 space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" title="View" class="inline-block align-middle text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-block align-middle text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end of max-w-7xl container -->
    </main>
</x-app-layout>
