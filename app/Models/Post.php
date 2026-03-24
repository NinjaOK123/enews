<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'thumbnail',
        'author_id', 'category_id', 'status', 'published_at', 'view_count',
        'is_featured', 'meta_desc', 'meta_key',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
    ];

    /**
     * Auto-clear trang chủ cache khi post thay đổi
     */
    protected static function boot(): void
    {
        parent::boot();
        $clear = fn() => Cache::forget('home.page.data');
        static::created($clear);
        static::updated($clear);
        static::deleted($clear);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class)->latest('created_at');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
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

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
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
        return route('post.show', $this->slug);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->thumbnail) {
            return 'https://placehold.co/400x250/e8f5e2/2a7a27?text=eNews+AGU';
        }

        // Đã là URL đầy đủ (http/https) — dùng luôn
        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }

        // Path Joomla cũ (images/...) → ghép domain enews.agu.edu.vn
        if (str_starts_with($this->thumbnail, 'images/') || str_starts_with($this->thumbnail, '/images/')) {
            return 'https://enews.agu.edu.vn/' . ltrim($this->thumbnail, '/');
        }

        // Ảnh mới upload lên Laravel storage
        return asset('storage/' . $this->thumbnail);
    }

    public function getExcerptShortAttribute(): string
    {
        $raw   = $this->excerpt ?: $this->content ?? '';
        $clean = html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return \Str::limit(trim($clean), 160);
    }

    // ─── Social Helpers ──────────────────────────────────────────────────

    public function getLikeCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function isLikedBy(?\App\Models\User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isSavedBy(?\App\Models\User $user): bool
    {
        if (!$user) return false;
        return \DB::table('post_saves')
            ->where('user_id', $user->id)
            ->where('post_id', $this->id)
            ->exists();
    }
}
