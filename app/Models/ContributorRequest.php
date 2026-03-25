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

    // Danh sách ngân hàng Việt Nam đầy đủ
    public static function banks(): array
    {
        return [
            // Ngân hàng Nhà nước & Big 4
            'Agribank (Ngân hàng Nông nghiệp)',
            'BIDV (Đầu tư & Phát triển)',
            'Vietcombank (Ngoại thương)',
            'Vietinbank (Công thương)',
            // Ngân hàng thương mại cổ phần lớn
            'ACB (Á Châu)',
            'MB Bank (Quân đội)',
            'Sacombank (Sài Gòn Thương Tín)',
            'Techcombank (Kỹ thương)',
            'VPBank (Việt Nam Thịnh Vượng)',
            'TPBank (Tiên Phong)',
            'SHB (Sài Gòn - Hà Nội)',
            'HDBank (Phát triển TP.HCM)',
            'VIB (Quốc tế)',
            'OCB (Phương Đông)',
            'MSB (Hàng hải)',
            'Eximbank (Xuất nhập khẩu)',
            'SeABank (Đông Nam Á)',
            'LienVietPostBank (Bưu điện Liên Việt)',
            'Nam A Bank (Nam Á)',
            'Bac A Bank (Bắc Á)',
            'SCB (Sài Gòn)',
            'NCB (Quốc dân)',
            'PVcomBank (Đại chúng)',
            'BaoViet Bank (Bảo Việt)',
            'Kienlongbank (Kiên Long)',
            'Saigonbank (Sài Gòn Công Thương)',
            'VietBank (Việt Nam Thương Tín)',
            'GPBank (Dầu khí Toàn cầu)',
            'ABBank (An Bình)',
            'VietCapital Bank (Bản Việt)',
            'PG Bank (Xăng dầu Petrolimex)',
            'OceanBank',
            'CBBank (Xây dựng)',
            'DongA Bank (Đông Á)',
            'Coopbank (Hợp tác xã)',
            'Indovina Bank',
            'VietA Bank (Việt Á)',
            'NamViet Bank (Nam Việt)',
            // Ngân hàng số / Fintech
            'Cake by VPBank',
            'Timo by Ban Viet Bank',
            'Tnex (MSB Digital)',
            'TNEX',
            'ViettelMoney (Viettel)',
            'MoMo',
            'ZaloPay',
            'ShopeePay',
            // Ngân hàng nước ngoài tại VN
            'HSBC Việt Nam',
            'Standard Chartered VN',
            'Shinhan Bank Việt Nam',
            'Woori Bank Việt Nam',
            'United Overseas Bank (UOB)',
            'CIMB Việt Nam',
            'Hong Leong Bank VN',
        ];
    }
}
