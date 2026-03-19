<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_public', 'cover_post_id'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_saves')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }

    // Ảnh bìa: lấy ảnh của bài đầu tiên hoặc cover_post_id nếu có
    public function getCoverImageAttribute(): string
    {
        if ($this->cover_post_id) {
            $post = Post::find($this->cover_post_id);
            if ($post) return $post->thumbnail_url;
        }
        $first = $this->posts()->first();
        if ($first) return $first->thumbnail_url;
        return 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&q=70';
    }
}
