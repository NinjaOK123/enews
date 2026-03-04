<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // ─── Role Constants ──────────────────────────────────────────────────────
    const ROLE_ADMIN       = 'admin';
    const ROLE_EDITOR      = 'editor';
    const ROLE_CONTRIBUTOR = 'contributor';
    const ROLE_VIEWER      = 'viewer';
    const ROLE_READER      = 'reader';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'google_id',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool       { return $this->role === self::ROLE_ADMIN; }
    public function isEditor(): bool      { return $this->role === self::ROLE_EDITOR; }
    public function isContributor(): bool { return $this->role === self::ROLE_CONTRIBUTOR; }
    public function isViewer(): bool      { return $this->role === self::ROLE_VIEWER; }
    public function isReader(): bool      { return $this->role === self::ROLE_READER; }

    /** True nếu có quyền quản lý nội dung (admin hoặc editor) */
    public function canManageContent(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR]);
    }

    /** True nếu có thể tạo bài viết (admin, editor, contributor) */
    public function canWriteArticle(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_CONTRIBUTOR]);
    }

    /** Trả về nhãn tiếng Việt của role */
    public function roleLabel(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN       => 'Quản trị viên',
            self::ROLE_EDITOR      => 'Biên tập viên',
            self::ROLE_CONTRIBUTOR => 'Cộng tác viên',
            self::ROLE_VIEWER      => 'Người xem nội bộ',
            default                => 'Người dùng AGU',
        };
    }
}
