<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function pendingUsers(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::where('approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    public function approveUser(Request $request, $id)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser(Request $request, $id)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User rejected successfully']);
    }

    public function allUsers(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }
}
