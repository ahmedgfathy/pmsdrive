<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    /**
     * Get storage statistics for user
     */
    public function stats()
    {
        $user = Auth::user();

        // Get file type statistics
        $fileTypeStats = File::where('user_id', $user->id)
            ->select(
                DB::raw('COALESCE(
                    CASE 
                        WHEN mime_type LIKE "image/%" THEN "Images"
                        WHEN mime_type LIKE "video/%" THEN "Videos"
                        WHEN mime_type LIKE "audio/%" THEN "Audio"
                        WHEN mime_type IN ("application/pdf") THEN "PDFs"
                        WHEN mime_type IN ("application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-word") THEN "Documents"
                        WHEN mime_type IN ("application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") THEN "Spreadsheets"
                        WHEN mime_type IN ("application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation") THEN "Presentations"
                        WHEN mime_type IN ("application/zip", "application/x-rar-compressed", "application/x-7z-compressed") THEN "Archives"
                        WHEN mime_type LIKE "text/%" THEN "Text Files"
                        ELSE "Other"
                    END, "Other"
                ) as type'),
                DB::raw('SUM(size) as total_size'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('type')
            ->get();

        // Get recent files
        $recentFiles = File::where('user_id', $user->id)
            ->with('folder')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get largest files
        $largestFiles = File::where('user_id', $user->id)
            ->with('folder')
            ->orderBy('size', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'storage_used' => $user->storage_used,
            'storage_quota' => $user->storage_quota,
            'storage_percentage' => $user->storage_percentage,
            'formatted_storage_used' => $user->formatted_storage_used,
            'formatted_storage_quota' => $user->formatted_storage_quota,
            'file_type_stats' => $fileTypeStats,
            'total_files' => File::where('user_id', $user->id)->count(),
            'recent_files' => $recentFiles,
            'largest_files' => $largestFiles,
        ]);
    }

    /**
     * Get dashboard overview
     */
    public function overview()
    {
        $user = Auth::user();

        return response()->json([
            'storage_used' => $user->storage_used,
            'storage_quota' => $user->storage_quota,
            'storage_percentage' => $user->storage_percentage,
            'formatted_storage_used' => $user->formatted_storage_used,
            'formatted_storage_quota' => $user->formatted_storage_quota,
            'total_files' => File::where('user_id', $user->id)->count(),
            'total_folders' => $user->folders()->count(),
        ]);
    }
}
