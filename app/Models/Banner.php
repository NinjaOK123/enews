<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'image', 'link', 'is_active', 'order'];

    protected $casts = ['is_active' => 'boolean'];

    // Scope: chỉ lấy banner đang active, sắp theo order
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return 'https://placehold.co/400x80/e8f5e2/2a7a27?text=Banner';
        }

        // Nếu là link ngoài (http/https) → trả về thẳng
        if (\Illuminate\Support\Str::startsWith($this->image, 'http')) {
            return $this->image;
        }

        // Dùng asset() thay vì Storage::url() để URL tự động khớp domain đang truy cập
        // (tránh bị cứng theo APP_URL trong .env khi dev local)
        return asset('storage/' . ltrim($this->image, '/'));
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home.page.data');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home.page.data');
        });
    }
}
