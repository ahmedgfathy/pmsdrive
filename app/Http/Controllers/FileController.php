<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Models\FileExtensionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Get all files for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $folderId = $request->query('folder_id');

        $files = File::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($files);
    }

    /**
     * Upload files
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:524288', // 512MB max per file
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $user = Auth::user();
        $uploadedFiles = [];

        foreach ($request->file('files') as $uploadedFile) {
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $rule = FileExtensionRule::normalizedExtension($extension)->first();

            if ($rule && !$rule->is_allowed) {
                return response()->json([
                    'message' => $rule->message ?: "Uploads with .{$extension} files are not permitted.",
                    'extension' => $extension,
                ], 422);
            }

            // Check storage quota
            $fileSize = $uploadedFile->getSize();
            if (($user->storage_used + $fileSize) > $user->storage_quota) {
                return response()->json([
                    'message' => 'Storage quota exceeded. Cannot upload file.',
                    'storage_used' => $user->formatted_storage_used,
                    'storage_quota' => $user->formatted_storage_quota,
                ], 400);
            }

            // Generate unique filename
            $originalName = $uploadedFile->getClientOriginalName();
            $uniqueName = Str::uuid() . '.' . $extension;

            // Store file
            $path = $uploadedFile->storeAs('user_files/' . $user->id, $uniqueName, 'local');

            // Create file record
            $file = File::create([
                'user_id' => $user->id,
                'folder_id' => $request->folder_id,
                'name' => $uniqueName,
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $fileSize,
            ]);

            // Update user storage
            $user->increment('storage_used', $fileSize);

            $uploadedFiles[] = $file;
        }

        return response()->json([
            'message' => 'Files uploaded successfully',
            'files' => $uploadedFiles,
            'storage_used' => $user->fresh()->formatted_storage_used,
            'storage_percentage' => $user->fresh()->storage_percentage,
        ], 201);
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $user = Auth::user();
        $file = File::findOrFail($id);

        // Check if user owns the file or it's shared with them
        if ($file->user_id !== $user->id) {
            $sharedFile = $file->sharedWith()->where('shared_with_user_id', $user->id)->first();
            if (!$sharedFile) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        if (!Storage::disk('local')->exists($file->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    /**
     * Get file details
     */
    public function show($id)
    {
        $user = Auth::user();
        $file = File::with('folder')->findOrFail($id);

        // Check ownership or shared access
        if ($file->user_id !== $user->id) {
            $sharedFile = $file->sharedWith()->where('shared_with_user_id', $user->id)->first();
            if (!$sharedFile) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return response()->json($file);
    }

    /**
     * Update file details
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $file = File::findOrFail($id);

        if ($file->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'original_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
        ]);

        $file->update($request->only(['original_name', 'description']));

        return response()->json([
            'message' => 'File updated successfully',
            'file' => $file,
        ]);
    }

    /**
     * Delete file
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $file = File::findOrFail($id);

        if ($file->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete physical file
        if (Storage::disk('local')->exists($file->path)) {
            Storage::disk('local')->delete($file->path);
        }

        // Update user storage
        $user->decrement('storage_used', $file->size);

        // Delete record
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
            'storage_used' => $user->fresh()->formatted_storage_used,
            'storage_percentage' => $user->fresh()->storage_percentage,
        ]);
    }

    /**
     * Delete multiple files
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'required|integer|exists:files,id',
        ]);

        $user = Auth::user();
        $deletedCount = 0;
        $totalSizeFreed = 0;

        foreach ($request->file_ids as $fileId) {
            $file = File::find($fileId);
            
            if ($file && $file->user_id === $user->id) {
                // Delete physical file
                if (Storage::disk('local')->exists($file->path)) {
                    Storage::disk('local')->delete($file->path);
                }

                $totalSizeFreed += $file->size;
                $file->delete();
                $deletedCount++;
            }
        }

        // Update user storage
        $user->decrement('storage_used', $totalSizeFreed);

        return response()->json([
            'message' => "$deletedCount file(s) deleted successfully",
            'storage_used' => $user->fresh()->formatted_storage_used,
            'storage_percentage' => $user->fresh()->storage_percentage,
        ]);
    }

    /**
     * Search files
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $query = $request->query('q');

        $files = File::where('user_id', $user->id)
            ->where(function($q) use ($query) {
                $q->where('original_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($files);
    }
}
