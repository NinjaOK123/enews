<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'post_count'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePopular($query, int $limit = 20)
    {
        return $query->orderByDesc('post_count')->limit($limit);
    }
}
