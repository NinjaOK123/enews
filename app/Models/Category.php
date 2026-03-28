<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'order', 'is_active', 'show_in_menu'];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(Post::class)->where('status', 'published')->orderByDesc('published_at');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all descendant IDs recursively
     */
    public function getAllDescendantIds(): array
    {
        $ids = [];
        // Lading children if not loaded
        if (!$this->relationLoaded('children')) {
            $this->load('children');
        }
        
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('category', $this->slug);
    }
}
