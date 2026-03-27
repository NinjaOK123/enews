<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_shared',
        'shared_role',
        'shared_user_id',
    ];

    public function sharedUser()
    {
        return $this->belongsTo(User::class, 'shared_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function absolutePath(): string
    {
        $path = (string) $this->file_path;

        // Pattern A (contributor flow): "media/123/file.jpg" stored on disk "public"
        if ($path !== '' && !str_starts_with($path, '/')) {
            return storage_path('app/public/' . $path);
        }

        // Pattern B (admin legacy flow): "/storage/media/shared/xxx.jpg"
        if (str_starts_with($path, '/storage/')) {
            return public_path($path);
        }

        return public_path($path);
    }

    public function publicUrl(): string
    {
        $path = (string) $this->file_path;
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        if (str_starts_with($path, '/storage/')) {
            return asset($path);
        }
        return asset('storage/' . $path);
    }
}
