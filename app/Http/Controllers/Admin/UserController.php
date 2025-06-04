<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction; // Assuming you have a transaction model that relates to users
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Display a listing of users
    public function index()
    {
        // Get all users
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Show details of a specific user
    public function show($id)
    {
        $user = User::findOrFail($id);
        $transactions = Transaction::where('user_id', $user->id)->get(); // Get transactions for this user

        return view('admin.users.show', compact('user', 'transactions'));
    }

    // Update user status (active/inactive)
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = $request->status; // Assuming 'status' is passed with 1 or 0 for active/inactive
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User status updated successfully');
    }

    // Show the form for editing a user (if needed)
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Update user details (example for editing a user)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully');
    }
}
