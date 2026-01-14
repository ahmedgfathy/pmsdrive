<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    /**
     * Share file with internal user
     */
    public function shareWithUser(Request $request, $fileId)
    {
        $validated = $request->validate([
            'user_email' => 'required|email|exists:users,email',
            'permission' => 'in:view,download',
        ]);

        $file = File::findOrFail($fileId);

        // Check if user owns the file
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find user to share with
        $sharedWithUser = User::where('email', $validated['user_email'])->first();

        if ($sharedWithUser->id === Auth::id()) {
            return response()->json(['message' => 'Cannot share with yourself'], 400);
        }

        // Check if already shared
        $existing = DB::table('shared_files')
            ->where('file_id', $fileId)
            ->where('shared_with_user_id', $sharedWithUser->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'File already shared with this user'], 400);
        }

        // Create share record
        DB::table('shared_files')->insert([
            'file_id' => $fileId,
            'shared_by_user_id' => Auth::id(),
            'shared_with_user_id' => $sharedWithUser->id,
            'permission' => $validated['permission'] ?? 'view',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'File shared successfully',
            'shared_with' => $sharedWithUser->name
        ]);
    }

    /**
     * Generate public share link
     */
    public function generatePublicLink(Request $request, $fileId)
    {
        $validated = $request->validate([
            'expires_in_days' => 'nullable|integer|min:1|max:30',
            'password' => 'nullable|string|min:4',
        ]);

        $file = File::findOrFail($fileId);

        // Check if user owns the file
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate unique token
        $token = Str::random(32);

        // Calculate expiration
        $expiresAt = $validated['expires_in_days'] 
            ? now()->addDays($validated['expires_in_days'])
            : now()->addDays(7); // Default 7 days

        // Store public link
        $linkId = DB::table('public_file_links')->insertGetId([
            'file_id' => $fileId,
            'token' => $token,
            'created_by_user_id' => Auth::id(),
            'password' => $validated['password'] ?? null,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publicUrl = url("/share/{$token}");

        return response()->json([
            'message' => 'Public link generated',
            'link' => $publicUrl,
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    /**
     * Send share link via email
     */
    public function sendLinkByEmail(Request $request, $fileId)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
            'expires_in_days' => 'nullable|integer|min:1|max:30',
        ]);

        $file = File::findOrFail($fileId);

        // Check if user owns the file
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate link if not exists
        $token = Str::random(32);
        $expiresAt = now()->addDays($validated['expires_in_days'] ?? 7);

        DB::table('public_file_links')->insert([
            'file_id' => $fileId,
            'token' => $token,
            'created_by_user_id' => Auth::id(),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publicUrl = url("/share/{$token}");

        // Send email
        try {
            Mail::raw(
                "Hello,\n\n" .
                Auth::user()->name . " has shared a file with you: " . $file->original_name . "\n\n" .
                ($validated['message'] ? "Message: " . $validated['message'] . "\n\n" : "") .
                "Click the link below to download:\n" .
                $publicUrl . "\n\n" .
                "This link will expire on: " . $expiresAt->format('F j, Y g:i A') . "\n\n" .
                "Best regards,\n" .
                config('app.name'),
                function ($message) use ($validated, $file) {
                    $message->to($validated['email'])
                        ->subject('Shared File: ' . $file->original_name);
                }
            );

            return response()->json([
                'message' => 'Link sent successfully',
                'email' => $validated['email'],
                'link' => $publicUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shared files (files shared WITH current user)
     */
    public function getSharedFiles()
    {
        $sharedFiles = DB::table('shared_files')
            ->join('files', 'shared_files.file_id', '=', 'files.id')
            ->join('users', 'shared_files.shared_by_user_id', '=', 'users.id')
            ->where('shared_files.shared_with_user_id', Auth::id())
            ->select(
                'files.*',
                'shared_files.permission',
                'shared_files.created_at as shared_at',
                'users.name as shared_by_name',
                'users.email as shared_by_email'
            )
            ->orderBy('shared_files.created_at', 'desc')
            ->get()
            ->map(function ($file) {
                $file->shared_by = [
                    'name' => $file->shared_by_name,
                    'email' => $file->shared_by_email
                ];
                unset($file->shared_by_name, $file->shared_by_email);
                return $file;
            });

        return response()->json($sharedFiles);
    }

    /**
     * Remove share (unshare file)
     */
    public function removeShare($fileId, $userId)
    {
        $file = File::findOrFail($fileId);

        // Check if user owns the file
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::table('shared_files')
            ->where('file_id', $fileId)
            ->where('shared_with_user_id', $userId)
            ->delete();

        return response()->json(['message' => 'Share removed successfully']);
    }

    /**
     * Download file via public link
     */
    public function downloadViaPublicLink($token, Request $request)
    {
        $link = DB::table('public_file_links')
            ->where('token', $token)
            ->first();

        if (!$link) {
            abort(404, 'Link not found');
        }

        // Check expiration
        if ($link->expires_at && now()->gt($link->expires_at)) {
            abort(410, 'Link expired');
        }

        // Check password if set
        if ($link->password) {
            $password = $request->input('password') ?? $request->header('X-Share-Password');
            if ($password !== $link->password) {
                return response()->json(['message' => 'Password required'], 401);
            }
        }

        // Increment download count
        DB::table('public_file_links')
            ->where('id', $link->id)
            ->increment('download_count');

        // Get file
        $file = File::findOrFail($link->file_id);
        $filePath = storage_path('app/private/uploads/' . $file->stored_name);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $file->original_name);
    }
}
