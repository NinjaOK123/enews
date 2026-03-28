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

    // Helper: trả URL ảnh đầy đủ
    public function getImageUrlAttribute(): string
    {
        return \Storage::disk('public')->url($this->image);
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
