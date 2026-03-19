@extends('layouts.app')
@section('title', ($user->name ?? 'Người dùng') . ' — Trang cá nhân')
@push('meta')
<meta name="description" content="Trang cá nhân {{ $user->name }} trên eNews AGU">
@endpush

@push('styles')
<style>
/* ───────────────────────────────────────────
   PROFILE PAGE – TikTok-inspired design
   ─────────────────────────────────────────── */

.profile-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 0 60px;
}

/* Cover */
.profile-cover {
    height: 200px;
    background: linear-gradient(135deg, #1a5c38 0%, #2d9e60 50%, #0d3b22 100%);
    position: relative;
    overflow: hidden;
}
.profile-cover::after {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
}

/* Header card */
.profile-header-card {
    background: #fff;
    border-radius: 0 0 20px 20px;
    padding: 0 24px 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    position: relative;
    margin-bottom: 20px;
}

/* Avatar */
.profile-avatar-wrap {
    position: relative;
    display: inline-block;
    margin-top: -50px;
    margin-bottom: 12px;
}
.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.profile-avatar-initials {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary, #2a7a27), #16a34a);
    color: #fff;
    font-weight: 900;
    font-size: 2.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    letter-spacing: -1px;
}
.avatar-upload-btn {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 28px;
    height: 28px;
    background: var(--primary, #2a7a27);
    color: #fff;
    border: 2px solid #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: .75rem;
    transition: transform .2s;
}
.avatar-upload-btn:hover { transform: scale(1.1); }

/* Role badge */
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.role-badge.admin       { background: #fef3c7; color: #d97706; }
.role-badge.editor      { background: #dbeafe; color: #2563eb; }
.role-badge.contributor { background: #dcfce7; color: #16a34a; }
.role-badge.reader      { background: #f3f4f6; color: #6b7280; }

/* Stats row */
.profile-stats {
    display: flex;
    gap: 24px;
    margin: 16px 0;
    flex-wrap: wrap;
}
.stat-item {
    text-align: center;
    cursor: pointer;
}
.stat-item strong {
    display: block;
    font-size: 1.3rem;
    font-weight: 900;
    color: #111;
    line-height: 1.2;
}
.stat-item small {
    font-size: .75rem;
    color: #888;
    font-weight: 500;
}

/* Action buttons */
.profile-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 16px;
}
.btn-edit-profile {
    background: #f3f4f6;
    color: #333;
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-weight: 600;
    font-size: .9rem;
    cursor: pointer;
    transition: background .2s;
}
.btn-edit-profile:hover { background: #e5e7eb; }

/* Tabs */
.profile-tabs {
    display: flex;
    border-bottom: 1.5px solid #f0f0f0;
    background: #fff;
    border-radius: 12px 12px 0 0;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.profile-tab {
    flex: 1;
    padding: 14px 8px;
    text-align: center;
    font-weight: 600;
    font-size: .85rem;
    color: #888;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all .2s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.profile-tab:hover { color: var(--primary, #2a7a27); background: #f9fafb; }
.profile-tab.active {
    color: var(--primary, #2a7a27);
    border-bottom-color: var(--primary, #2a7a27);
    background: #f0fdf4;
}

/* Tab content */
.tab-pane { display: none; padding: 20px 0; }
.tab-pane.active { display: block; }

/* Post grid */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
}
.post-card-mini {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    text-decoration: none;
    color: inherit;
    transition: transform .2s, box-shadow .2s;
    display: block;
}
.post-card-mini:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    color: inherit;
}
.post-card-mini img {
    width: 100%;
    height: 130px;
    object-fit: cover;
}
.post-card-mini .mini-body {
    padding: 10px 12px;
}
.post-card-mini .mini-title {
    font-size: .82rem;
    font-weight: 600;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 6px;
    color: #222;
}
.post-card-mini .mini-meta {
    font-size: .72rem;
    color: #aaa;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Collections grid */
.collections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 14px;
}
.collection-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: transform .2s, box-shadow .2s;
    text-decoration: none;
    color: inherit;
    display: block;
}
.collection-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    color: inherit;
}
.collection-cover {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
    background: #e5e7eb;
}
.collection-cover-placeholder {
    width: 100%;
    height: 120px;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}
.collection-body {
    padding: 10px 12px;
}
.collection-name {
    font-size: .85rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 2px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.collection-count {
    font-size: .73rem;
    color: #999;
}

/* New collection btn */
.btn-new-collection {
    background: #f0fdf4;
    border: 2px dashed #86efac;
    border-radius: 14px;
    height: 100%;
    min-height: 170px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    font-size: .82rem;
    font-weight: 600;
    color: #16a34a;
    transition: background .2s;
}
.btn-new-collection:hover { background: #dcfce7; }

/* Empty state */
.empty-tab {
    text-align: center;
    padding: 48px 24px;
    color: #bbb;
}
.empty-tab svg { width: 56px; height: 56px; margin-bottom: 12px; }
.empty-tab p { font-size: .9rem; margin: 0; }

/* Edit modal */
.modal-profile { border-radius: 16px; overflow: hidden; }
.modal-profile .modal-header {
    background: linear-gradient(135deg, #1a5c38, #2d9e60);
    color: #fff;
    border: none;
}
.modal-profile .modal-header .btn-close { filter: brightness(0) invert(1); }

/* Bio */
.profile-bio {
    font-size: .88rem;
    color: #666;
    margin: 4px 0 0;
    max-width: 400px;
}

@media (max-width: 600px) {
    .profile-stats { gap: 16px; }
    .posts-grid { grid-template-columns: repeat(2, 1fr); }
    .collections-grid { grid-template-columns: repeat(2, 1fr); }
}

/* ── Pagination trong Profile ─────────────────────── */
.profile-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin-top: 20px;
    flex-wrap: wrap;
}
.profile-pagination a,
.profile-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 10px;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e5e7eb;
    color: #555;
    background: #fff;
    transition: all .15s;
}
.profile-pagination a:hover {
    background: #f0fdf4;
    border-color: var(--primary, #2a7a27);
    color: var(--primary, #2a7a27);
}
.profile-pagination .active-page {
    background: var(--primary, #2a7a27);
    border-color: var(--primary, #2a7a27);
    color: #fff;
    cursor: default;
}
.profile-pagination .dots {
    border: none;
    background: none;
    color: #aaa;
    cursor: default;
}
.profile-pagination svg {
    width: 14px !important;
    height: 14px !important;
}
</style>
@endpush

@section('content')
<div class="profile-wrapper">

    {{-- Cover --}}
    <div class="profile-cover"></div>

    {{-- Header Card --}}
    <div class="profile-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            {{-- Avatar + info --}}
            <div>
                <div class="profile-avatar-wrap">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="profile-avatar">
                    @else
                        <div class="profile-avatar-initials">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 2)) }}
                        </div>
                    @endif

                    @if($isOwnProfile)
                    <label for="avatarInput" class="avatar-upload-btn" title="Đổi ảnh đại diện">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </label>
                    <form id="avatarForm" method="POST" action="{{ route('profile.avatar', $user->id) }}" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                    </form>
                    @endif
                </div>

                <h1 style="font-size:1.4rem;font-weight:800;margin:0 0 4px;">{{ $user->name }}</h1>
                <span class="role-badge {{ $user->role }}">
                    @if($user->role === 'admin') 👑 Quản trị viên
                    @elseif($user->role === 'editor') ✏️ Biên tập viên
                    @elseif($user->role === 'contributor') 🖊️ Cộng tác viên
                    @else 📖 Người dùng
                    @endif
                </span>
                @if($user->bio ?? false)
                    <p class="profile-bio">{{ $user->bio }}</p>
                @endif
            </div>

            {{-- Stats --}}
            <div class="profile-stats">
                <div class="stat-item">
                    <strong>{{ number_format($articlesCount) }}</strong>
                    <small>Bài viết</small>
                </div>
                <div class="stat-item">
                    <strong>{{ number_format($totalViews) }}</strong>
                    <small>Lượt xem</small>
                </div>
                <div class="stat-item">
                    <strong>{{ number_format($totalLikes) }}</strong>
                    <small>Lượt thích</small>
                </div>
            </div>
        </div>

        {{-- Edit button --}}
        @if($isOwnProfile)
        <div class="profile-actions">
            <button class="btn-edit-profile" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                ✏️ Chỉnh sửa thông tin
            </button>
        </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="profile-tabs" id="profileTabs">
        <a href="#tab-posts" class="profile-tab active" data-tab="tab-posts">
            📝 <span>Bài viết</span>
        </a>
        @if($isOwnProfile)
        <a href="#tab-saved" class="profile-tab" data-tab="tab-saved">
            🔖 <span>Đã lưu</span>
        </a>
        <a href="#tab-liked" class="profile-tab" data-tab="tab-liked">
            ❤️ <span>Đã thích</span>
        </a>
        @endif
        <a href="#tab-about" class="profile-tab" data-tab="tab-about">
            👤 <span>Giới thiệu</span>
        </a>
    </div>

    {{-- Tab: Bài viết --}}
    <div class="tab-pane active" id="tab-posts">
        @if($posts->count())
        <div class="posts-grid">
            @foreach($posts as $post)
            <a href="{{ route('post.show', $post->slug) }}" class="post-card-mini">
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy">
                <div class="mini-body">
                    <div class="mini-title">{{ $post->title }}</div>
                    <div class="mini-meta">
                        👁️ {{ number_format($post->view_count) }}
                        @if($post->like_count)
                        &nbsp;·&nbsp; ❤️ {{ $post->like_count }}
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        {{-- Phân trang đẹp --}}
        @if($posts->hasPages())
        <div class="profile-pagination">
            {{-- << Đầu --}}
            @if($posts->onFirstPage())
                <span style="opacity:.35;">&#171;</span>
            @else
                <a href="{{ $posts->url(1) }}" title="Trang đầu">&#171;</a>
            @endif

            {{-- < Trước --}}
            @if($posts->onFirstPage())
                <span style="opacity:.35;">&#8249;</span>
            @else
                <a href="{{ $posts->previousPageUrl() }}" title="Trang trước">&#8249;</a>
            @endif

            {{-- Các số trang (hiện ±2 quanh trang hiện tại) --}}
            @foreach($posts->getUrlRange(max(1, $posts->currentPage()-2), min($posts->lastPage(), $posts->currentPage()+2)) as $page => $url)
                @if($page == $posts->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- > Sau --}}
            @if($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}" title="Trang sau">&#8250;</a>
            @else
                <span style="opacity:.35;">&#8250;</span>
            @endif

            {{-- >> Cuối --}}
            @if($posts->hasMorePages())
                <a href="{{ $posts->url($posts->lastPage()) }}" title="Trang cuối">&#187;</a>
            @else
                <span style="opacity:.35;">&#187;</span>
            @endif
        </div>
        @endif
        @else
        <div class="empty-tab">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            <p>Chưa có bài viết nào.</p>
        </div>
        @endif
    </div>

    {{-- Tab: Đã lưu --}}
    @if($isOwnProfile)
    <div class="tab-pane" id="tab-saved">
        <div class="collections-grid">
            @foreach($collections as $col)
            <div class="collection-card" onclick="window.location='/bo-suu-tap/{{ $col->id }}'" style="cursor:pointer">
                @php $cover = $col->cover_image; @endphp
                @if($cover && str_starts_with($cover, 'http'))
                    <img src="{{ $cover }}" class="collection-cover" alt="{{ $col->name }}" loading="lazy">
                @else
                    <div class="collection-cover-placeholder">🔖</div>
                @endif
                <div class="collection-body">
                    <div class="collection-name">{{ $col->name }}</div>
                    <div class="collection-count">{{ $col->posts_count }} bài viết</div>
                </div>
            </div>
            @endforeach

            {{-- Nút tạo mới --}}
            <button class="btn-new-collection" data-bs-toggle="modal" data-bs-target="#newCollectionModal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:28px;height:28px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tạo bộ sưu tập
            </button>
        </div>

        @if($collections->isEmpty())
        <div class="empty-tab" style="margin-top:8px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" /></svg>
            <p>Bạn chưa lưu bài viết nào. Hãy lưu bài bạn yêu thích!</p>
        </div>
        @endif
    </div>

    {{-- Tab: Đã thích --}}
    <div class="tab-pane" id="tab-liked">
        @if($likedPosts->count())
        <div class="posts-grid">
            @foreach($likedPosts as $post)
            <a href="{{ route('post.show', $post->slug) }}" class="post-card-mini">
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy">
                <div class="mini-body">
                    <div class="mini-title">{{ $post->title }}</div>
                    <div class="mini-meta">❤️ {{ $post->like_count }} &nbsp;·&nbsp; 👁️ {{ number_format($post->view_count) }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="empty-tab">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
            <p>Bạn chưa thích bài viết nào.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Tab: Giới thiệu --}}
    <div class="tab-pane" id="tab-about">
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div class="d-flex gap-3 align-items-center">
                    <span style="font-size:1.3rem;">👤</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Họ tên</div>
                        <div style="font-weight:600;color:#222;">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <span style="font-size:1.3rem;">📧</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Email</div>
                        <div style="font-weight:600;color:#222;">{{ $isOwnProfile ? $user->email : '***@***.***' }}</div>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <span style="font-size:1.3rem;">🎓</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Vai trò</div>
                        <div style="font-weight:600;color:#222;">{{ $user->roleLabel() }}</div>
                    </div>
                </div>
                @if($user->bio ?? false)
                <div class="d-flex gap-3 align-items-start">
                    <span style="font-size:1.3rem;">📝</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Giới thiệu</div>
                        <div style="color:#444;">{{ $user->bio }}</div>
                    </div>
                </div>
                @endif
                <div class="d-flex gap-3 align-items-center">
                    <span style="font-size:1.3rem;">📅</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Tham gia từ</div>
                        <div style="font-weight:600;color:#222;">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal: Sửa thông tin --}}
@if($isOwnProfile)
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-profile">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">✏️ Chỉnh sửa thông tin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giới thiệu bản thân</label>
                        <textarea name="bio" rows="3" class="form-control" maxlength="300" placeholder="Ví dụ: Sinh viên năm 3 Khoa CNTT, yêu thích viết lách...">{{ old('bio', $user->bio ?? '') }}</textarea>
                        <small class="text-muted">Tối đa 300 ký tự</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Tạo bộ sưu tập mới --}}
<div class="modal fade" id="newCollectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-profile">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">🔖 Tạo bộ sưu tập mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên bộ sưu tập</label>
                    <input type="text" id="newColName" class="form-control" placeholder="Ví dụ: Bài viết yêu thích" maxlength="100">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="newColPublic">
                    <label class="form-check-label" for="newColPublic">Công khai (mọi người đều xem được)</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success px-4" id="createColBtn">🔖 Tạo bộ sưu tập</button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('data-tab');

        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

        this.classList.add('active');
        document.getElementById(targetId)?.classList.add('active');
    });
});

// Tạo bộ sưu tập mới
const createColBtn = document.getElementById('createColBtn');
if (createColBtn) {
    createColBtn.addEventListener('click', function() {
        const name = document.getElementById('newColName').value.trim();
        const isPublic = document.getElementById('newColPublic').checked;
        if (!name) { alert('Vui lòng nhập tên bộ sưu tập!'); return; }

        fetch("{{ route('collections.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name, is_public: isPublic }),
        }).then(r => r.json()).then(data => {
            if (data.collection) {
                bootstrap.Modal.getInstance(document.getElementById('newCollectionModal')).hide();
                window.location.reload();
            }
        });
    });
}
</script>
@endpush
