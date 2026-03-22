<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['post_id', 'reviewer_id', 'action', 'note'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function actionLabel(): string
    {
        return match ($this->action) {
            'approve'      => '✅ Đã duyệt',
            'reject'       => '❌ Từ chối',
            'request_edit' => '✏️ Yêu cầu chỉnh sửa',
            default        => $this->action,
        };
    }
}
