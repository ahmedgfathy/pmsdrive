<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileExtensionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'extension',
        'is_allowed',
        'message',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];

    public function setExtensionAttribute(string $value): void
    {
        $this->attributes['extension'] = strtolower(ltrim($value, '.'));
    }

    public function scopeNormalizedExtension($query, string $extension)
    {
        return $query->where('extension', strtolower(ltrim($extension, '.')));
    }
}
