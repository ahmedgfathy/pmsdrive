<?php

namespace App\Http\Controllers;

use App\Models\FileExtensionRule;
use Illuminate\Http\Request;

class FileRestrictionController extends Controller
{
    private function ensureAdmin(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    private function normalizeExtension(string $value): string
    {
        return strtolower(ltrim($value, '.'));
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        return FileExtensionRule::orderBy('extension')->get();
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'extension' => 'required|string|max:20',
            'is_allowed' => 'required|boolean',
            'message' => 'nullable|string|max:255',
        ]);

        $data['extension'] = $this->normalizeExtension($data['extension']);
        if (FileExtensionRule::where('extension', $data['extension'])->exists()) {
            return response()->json(['message' => 'Extension already configured.'], 422);
        }

        if ($data['is_allowed']) {
            $data['message'] = null;
        }

        $rule = FileExtensionRule::create($data);

        return response()->json($rule, 201);
    }

    public function update(Request $request, FileExtensionRule $rule)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'extension' => 'sometimes|string|max:20',
            'is_allowed' => 'sometimes|boolean',
            'message' => 'nullable|string|max:255',
        ]);

        if (array_key_exists('extension', $data)) {
            $data['extension'] = $this->normalizeExtension($data['extension']);
            $exists = FileExtensionRule::where('extension', $data['extension'])
                ->where('id', '!=', $rule->id)
                ->exists();
            if ($exists) {
                return response()->json(['message' => 'Extension already configured.'], 422);
            }
        }

        if (array_key_exists('is_allowed', $data) && $data['is_allowed']) {
            $data['message'] = null;
        }

        if (array_key_exists('message', $data) && is_string($data['message'])) {
            $data['message'] = trim($data['message']) ?: null;
        }

        $rule->update($data);

        return response()->json($rule);
    }

    public function destroy(Request $request, FileExtensionRule $rule)
    {
        $this->ensureAdmin($request);

        $rule->delete();

        return response()->json(['message' => 'Rule removed']);
    }
}
