<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Get all folders for authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $parentId = $request->query('parent_id');

        $folders = Folder::where('user_id', $user->id)
            ->where('parent_id', $parentId)
            ->withCount(['files', 'children'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($folders);
    }

    /**
     * Create new folder
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $user = Auth::user();

        // Build path
        $path = $request->name;
        if ($request->parent_id) {
            $parent = Folder::findOrFail($request->parent_id);
            if ($parent->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            $path = $parent->path ? $parent->path . '/' . $request->name : $request->name;
        }

        $folder = Folder::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'path' => $path,
        ]);

        return response()->json([
            'message' => 'Folder created successfully',
            'folder' => $folder,
        ], 201);
    }

    /**
     * Get folder details with contents
     */
    public function show($id)
    {
        $user = Auth::user();
        $folder = Folder::with(['children', 'files'])->findOrFail($id);

        if ($folder->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($folder);
    }

    /**
     * Update folder
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $folder = Folder::findOrFail($id);

        if ($folder->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update folder name and rebuild path
        $oldPath = $folder->path;
        $folder->name = $request->name;

        // Rebuild path
        if ($folder->parent_id) {
            $parent = Folder::findOrFail($folder->parent_id);
            $folder->path = $parent->path ? $parent->path . '/' . $request->name : $request->name;
        } else {
            $folder->path = $request->name;
        }

        $folder->save();

        // Update all child folder paths
        $this->updateChildPaths($folder, $oldPath);

        return response()->json([
            'message' => 'Folder updated successfully',
            'folder' => $folder,
        ]);
    }

    /**
     * Delete folder
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $folder = Folder::with(['files', 'children'])->findOrFail($id);

        if ($folder->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Calculate total size to free
        $totalSize = $this->calculateFolderSize($folder);

        // Delete all files and subfolders recursively
        $this->deleteFolderRecursive($folder);

        // Update user storage
        $user->decrement('storage_used', $totalSize);

        return response()->json([
            'message' => 'Folder deleted successfully',
            'storage_used' => $user->fresh()->formatted_storage_used,
            'storage_percentage' => $user->fresh()->storage_percentage,
        ]);
    }

    /**
     * Get folder breadcrumb path
     */
    public function breadcrumb($id)
    {
        $user = Auth::user();
        $folder = Folder::findOrFail($id);

        if ($folder->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $breadcrumb = [];
        $current = $folder;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
            ]);
            $current = $current->parent;
        }

        return response()->json($breadcrumb);
    }

    /**
     * Recursively delete folder and contents
     */
    private function deleteFolderRecursive($folder)
    {
        // Delete all files in this folder
        foreach ($folder->files as $file) {
            if (Storage::disk('local')->exists($file->path)) {
                Storage::disk('local')->delete($file->path);
            }
            $file->delete();
        }

        // Recursively delete child folders
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursive($child);
        }

        // Delete the folder itself
        $folder->delete();
    }

    /**
     * Calculate total size of folder and contents
     */
    private function calculateFolderSize($folder)
    {
        $size = 0;

        // Add files size
        $size += $folder->files()->sum('size');

        // Recursively add child folder sizes
        foreach ($folder->children as $child) {
            $size += $this->calculateFolderSize($child);
        }

        return $size;
    }

    /**
     * Update child folder paths recursively
     */
    private function updateChildPaths($folder, $oldPath)
    {
        $children = Folder::where('parent_id', $folder->id)->get();

        foreach ($children as $child) {
            $child->path = str_replace($oldPath, $folder->path, $child->path);
            $child->save();
            $this->updateChildPaths($child, $oldPath);
        }
    }
}
