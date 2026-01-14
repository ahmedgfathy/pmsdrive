<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
        $this->authorize('update', User::class);

        $user = User::findOrFail($id);
        $user->approved = true;
        $user->save();

        return response()->json([
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    public function rejectUser($id)
    {
        $this->authorize('delete', User::class);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User rejected and deleted successfully'
        ]);
    }

    public function allUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }
}
