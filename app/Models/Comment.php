<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'post_id', 'user_id', 'content', 'is_approved',
        'guest_name', 'guest_email', 'parent_id',
    ];

    protected $casts = ['is_approved' => 'boolean'];

    // ─── Relationships ────────────────────────────────────────────
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        // nullable: khách (Komento guest) không có user_id
        return $this->belongsTo(User::class);
    }

    /** Bình luận cha (Komento nested) */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /** Bình luận con */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /** Tên hiển thị: ưu tiên user đăng nhập, fallback sang guest_name */
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Ẩn danh';
    }

    // ─── Scopes ─────────────────────────────────────────────────
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->whereNull('is_approved');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
