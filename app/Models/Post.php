<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;

class Post extends Model
{
    use Searchable;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'thumbnail',
        'author_id', 'category_id', 'status', 'published_at', 'view_count',
        'is_featured', 'meta_desc', 'meta_key', 'source_author',
        'royalty_rate_id', 'royalty_multiplier', 'image_count', 'royalty_total'
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
        static::deleted($clear);

        $notifyPostStatus = function ($post) {
            $isNew = $post->wasRecentlyCreated;
            $statusChanged = $post->wasChanged('status');

            if (!$isNew && !$statusChanged) {
                return;
            }

            if ($post->status === 'pending') {
                $reviewUrl = route('admin.posts.edit', $post->id);
                \App\Models\Notification::create([
                    'title' => 'Bài viết mới đang chờ duyệt',
                    'content' => '<p>Cộng tác viên <b>' . ($post->author->name ?? 'Khuyết danh') . '</b> vừa gửi một bài viết mới: <b>"' . $post->title . '"</b>.</p><p><br></p><p><a href="' . $reviewUrl . '" style="display:inline-block; padding: 10px 20px; background-color: #1a5c38; color: white; border-radius: 8px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">👀 Xem bài viết để xử lý</a></p>',
                    'sent_at' => now(),
                    'recipients' => json_encode(['admin', 'editor'])
                ]);
            } elseif ($post->status === 'published' && $statusChanged) {
                \App\Models\Notification::create([
                    'title' => 'Bài viết của bạn đã được xuất bản 🎉',
                    'content' => '<p>Tin vui! Bài viết <b>"' . $post->title . '"</b> của bạn đã được Ban biên tập duyệt và chính thức xuất bản.</p><p>Cảm ơn bạn đã đóng góp nội dung chất lượng!</p>',
                    'sent_at' => now(),
                    'recipients' => json_encode([(string)$post->author_id])
                ]);
            } elseif ($post->status === 'rejected' && $statusChanged) {
                $reviewUrl = route('contributor.posts.edit', $post->id);
                \App\Models\Notification::create([
                    'title' => 'Bài viết chưa được duyệt 😔',
                    'content' => '<p>Rất tiếc, bài viết <b>"' . $post->title . '"</b> của bạn chưa được duyệt lền này. Bạn cần cố gắng hơn nhé!</p><p><br></p><p><a href="' . $reviewUrl . '" style="display:inline-block; padding: 10px 20px; background-color: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db; border-radius: 8px; text-decoration: none; font-weight: bold;">Sửa lại bài viết</a></p>',
                    'sent_at' => now(),
                    'recipients' => json_encode([(string)$post->author_id])
                ]);
            }
        };

        static::created(function($post) use ($clear, $notifyPostStatus) {
            $clear();
            $notifyPostStatus($post);
        });

        static::updated(function($post) use ($clear, $notifyPostStatus) {
            $clear();
            $notifyPostStatus($post);
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function royaltyRate(): BelongsTo
    {
        return $this->belongsTo(RoyaltyRate::class, 'royalty_rate_id');
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

    // ─── Advanced Search / Filter ─────────────────────────────────────────────

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    #[SearchUsingFullText(['title', 'excerpt', 'content'])]
    public function toSearchableArray()
    {
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }

    /**
     * Scope for advanced filters: author name, date range, category.
     * Note: Keyword searching is now handled natively by Laravel Scout via Model::search($keyword).
     */
    public function scopeFilterAdvanced(Builder $query, array $filters): Builder
    {
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
        $thumb = $this->thumbnail;

        // Xoá chuỗi 'assets/' dư thừa nếu trường thumbnail trong DB bị dính
        if (!empty($thumb)) {
            $thumb = str_replace(['images/assets/', '/images/assets/'], 'images/', $thumb);
        }

        // Nếu DB không có ảnh đại diện tĩnh, đào ảnh đầu tiên trong ruột bài báo ra làm hình đại diện
        if (empty($thumb) && !empty($this->content)) {
            preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $this->content, $matches);
            if (!empty($matches[1])) {
                $thumb = $matches[1];
                
                // Vì cái lấy trong content ra có thể là "images/..." hoặc "/images/..." hoặc url
                if (!str_starts_with($thumb, 'http') && !str_starts_with($thumb, '/')) {
                    $thumb = '/' . $thumb; 
                }
                
                return asset(ltrim($thumb, '/')); // Ép thành link tuyệt đối asset
            }
        }

        // Đào mãi không ra gì thì mới đưa cái hình Fake eNews màu xanh lá rỗng
        if (empty($thumb)) {
            return 'https://placehold.co/400x250/e8f5e2/2a7a27?text=eNews+AGU';
        }

        // Đã là URL đầy đủ (http/https) — dùng luôn
        if (str_starts_with($thumb, 'http')) {
            return $thumb;
        }

        // Ưu tiên đường dẫn cũ (images/...) load thẳng bằng asset() ở máy local
        // Vì toàn bộ 17GB ảnh đã được nhét vào thư mục public/images
        if (str_starts_with($thumb, 'images/') || str_starts_with($thumb, '/images/')) {
            return asset(ltrim($thumb, '/'));
        }

        // Ảnh mới định dạng upload vào Laravel storage (storage/app/public/...)
        return asset('storage/' . $thumb);
    }

    /**
     * Tự động sửa lại những đường dẫn ảnh gốc bị lỗi bên trong Nội dung bài báo cũ.
     * Biến thẻ <img src="images/..." thành <img src="/images/..."
     */
    public function getContentAttribute($value)
    {
        if (empty($value)) return $value;

        // Bước 1: Chuẩn hóa mọi dạng link Joomla cũ (có hoặc không có http)
        $value = str_replace(
            ['http://enews.agu.edu.vn/images/assets/', 'https://enews.agu.edu.vn/images/assets/', 'http://enews.agu.edu.vn/images/', 'https://enews.agu.edu.vn/images/'], 
            'images/', 
            $value
        );
        
        // Fix đặc thù Joomla 1: xoá chữ 'assets/' dư thừa nếu vẫn còn
        $value = str_replace(['images/assets/', '/images/assets/'], 'images/', $value);

        // Fix đặc thù Joomla 2: map ảnh upload vào đúng kho 1.4GB joomla-images
        $value = str_replace(['images/upload/imgposts/', '/images/upload/imgposts/'], 'storage/joomla-images/', $value);

        // Bước 2: Bọc toàn bộ các thẻ img src="images..." hoặc src="storage..." qua hàm asset() của Laravel
        // Điều này đảm bảo ảnh luôn đúng đường dẫn bất kể chạy trên localhost (subfolder) hay hosting thật
        return preg_replace_callback('/src=["\']\/?((?:images|storage)\/[^"\']+)["\']/i', function($matches) {
            return 'src="' . asset($matches[1]) . '"';
        }, $value);
    }

    public function getExcerptShortAttribute(): string
    {
        // Lấy raw value (không qua Accessor) để tránh vòng lặp regex
        $raw   = $this->excerpt ?: ($this->getRawOriginal('content') ?? '');
        $text  = strip_tags($raw);
        $text  = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Xóa các entity HTML không hoàn chỉnh (thiếu dấu ;) còn sót sau decode
        $text  = preg_replace('/&[a-zA-Z0-9#]{1,10}(?!;)/', '', $text);
        return \Str::limit(trim($text), 160);
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
