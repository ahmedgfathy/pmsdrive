<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        if (!$request->user() || !$request->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
    }

    public function pendingUsers(Request $request)
    {
        $this->ensureAdmin($request);

        $users = User::where('approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    public function approveUser(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);
        $user->approved = true;
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function rejectUser(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User rejected successfully']);
    }

    public function allUsers(Request $request)
    {
        $this->ensureAdmin($request);

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    public function updateQuota(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'storage_quota_gb' => 'required|numeric|min:0.1|max:10240',
        ]);

        $user = User::findOrFail($id);
        $user->storage_quota = (int) round($data['storage_quota_gb'] * 1073741824);
        $user->save();

        return response()->json([
            'message' => 'Storage quota updated',
            'storage_quota' => $user->storage_quota,
            'formatted_storage_quota' => $user->formatted_storage_quota,
        ]);
    }
}
