<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'approved',
        'storage_quota',
        'storage_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'formatted_storage_used',
        'formatted_storage_quota',
        'storage_percentage',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'approved' => 'boolean',
            'storage_quota' => 'integer',
            'storage_used' => 'integer',
        ];
    }

    /**
     * Get user's files
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get user's folders
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Get files shared by this user
     */
    public function sharedFiles(): HasMany
    {
        return $this->hasMany(SharedFile::class, 'owner_id');
    }

    /**
     * Get files shared with this user
     */
    public function filesSharedWithMe(): HasMany
    {
        return $this->hasMany(SharedFile::class, 'shared_with_user_id');
    }

    /**
     * Get storage usage percentage
     */
    public function getStoragePercentageAttribute(): float
    {
        if ($this->storage_quota == 0) {
            return 0;
        }
        return round(($this->storage_used / $this->storage_quota) * 100, 2);
    }

    /**
     * Get formatted storage used
     */
    public function getFormattedStorageUsedAttribute(): string
    {
        return $this->formatBytes($this->storage_used);
    }

    /**
     * Get formatted storage quota
     */
    public function getFormattedStorageQuotaAttribute(): string
    {
        return $this->formatBytes($this->storage_quota);
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
