<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'thumbnail',
        'author_id', 'category_id', 'status', 'published_at', 'view_count',
    ];

    protected $casts = ['published_at' => 'datetime'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    // ─── Advanced Search ─────────────────────────────────────────────────────

    /**
     * Scope for advanced search: keyword, author name, date range, category
     */
    public function scopeSearch(Builder $query, array $filters): Builder
    {
        // Keyword: title or content or excerpt
        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('excerpt', 'like', "%{$kw}%")
                  ->orWhere('content', 'like', "%{$kw}%");
            });
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by author name (join users)
        if (!empty($filters['author'])) {
            $query->whereHas('author', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['author'] . '%');
            });
        }

        // Date from
        if (!empty($filters['date_from'])) {
            $query->whereDate('published_at', '>=', $filters['date_from']);
        }

        // Date to
        if (!empty($filters['date_to'])) {
            $query->whereDate('published_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('article', $this->slug);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&q=70';
    }

    public function getExcerptShortAttribute(): string
    {
        return $this->excerpt ?: \Str::limit(strip_tags($this->content ?? ''), 120);
    }
}
