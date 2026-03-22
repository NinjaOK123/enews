<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributorRequest extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'bank_name', 'bank_account',
        'account_holder', 'note', 'status', 'admin_note',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    // Danh sách ngân hàng Việt Nam phổ biến
    public static function banks(): array
    {
        return [
            'Vietcombank', 'Vietinbank', 'BIDV', 'Agribank', 'Techcombank',
            'MB Bank', 'ACB', 'Sacombank', 'VPBank', 'TPBank',
            'SHB', 'HDBank', 'OCB', 'VIB', 'LienVietPostBank',
            'SeABank', 'Nam A Bank', 'Bac A Bank', 'MSB', 'Eximbank',
            'NCB', 'PVcomBank', 'BaoViet Bank', 'Kienlongbank', 'Saigonbank',
        ];
    }
}
