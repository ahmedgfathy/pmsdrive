<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pendingUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::where('approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $user->approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'User rejected successfully']);
    }

    public function allUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }
}
