<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('role') && in_array($request->role, ['admin', 'lecturer', 'student'])) {
            $query->where('role', $request->role);
        }
        $users = $query->get();
        return view('admin.dashboard', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.user-show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,lecturer,student',
        ]);
        $user->update($validated);
        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.');
    }
}
