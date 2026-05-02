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
    pointer-events: none;
}

/* Cover hover effect */
.profile-cover:hover .cover-upload-overlay {
    background: rgba(0, 0, 0, 0.28) !important;
}
.profile-cover:hover .cover-upload-label {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

@media (min-width: 640px) {
    .profile-cover { height: 260px; }
}
@media (min-width: 1024px) {
    .profile-cover { height: 300px; }
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
    display: block;
    margin-top: -50px;
    margin-bottom: 12px;
    width: fit-content;
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
    padding: 120px 24px;
    color: #bbb;
}
.empty-tab svg { width: 80px; height: 80px; margin-bottom: 16px; }
.empty-tab p { font-size: 1rem; margin: 0; color: #ccc; }

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
/* Spinner */
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

/* ───────────────────────────────────────────
   CẬP NHẬT GIAO DIỆN DARK MODE
   ─────────────────────────────────────────── */
.dark .profile-header-card {
    background: #18181b; /* zinc-900 */
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}
.dark .profile-tabs {
    background: #18181b;
    border-bottom: 1.5px solid #27272a; /* zinc-800 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.dark .profile-tab {
    color: #a1a1aa; /* zinc-400 */
}
.dark .profile-tab:hover {
    background: #27272a; /* zinc-800 */
    color: var(--primary, #4ade80);
}
.dark .profile-tab.active {
    background: rgba(42, 122, 39, 0.15);
    border-bottom-color: var(--primary, #4ade80);
    color: var(--primary, #4ade80);
}
.dark .post-card-mini {
    background: #18181b; /* zinc-900 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.dark .collection-card {
    background: #18181b; /* zinc-900 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.dark .post-card-mini .mini-title, .dark .collection-name {
    color: #f4f4f5; /* zinc-100 */
}
.dark .stat-item strong {
    color: #f4f4f5; /* zinc-100 */
}
.dark .stat-item small {
    color: #a1a1aa; /* zinc-400 */
}
.dark .profile-bio {
    color: #a1a1aa; /* zinc-400 */
}
.dark .btn-edit-profile {
    background: #27272a; /* zinc-800 */
    color: #e4e4e7; /* zinc-200 */
}
.dark .btn-edit-profile:hover {
    background: #3f3f46; /* zinc-700 */
}
.dark .btn-new-collection {
    background: rgba(42, 122, 39, 0.1);
    border-color: #166534; /* green-800 */
    color: #4ade80; /* green-400 */
}
.dark .btn-new-collection:hover {
    background: rgba(42, 122, 39, 0.2);
}
.dark .profile-pagination a, .dark .profile-pagination span {
    background: #18181b; /* zinc-900 */
    border-color: #27272a; /* zinc-800 */
    color: #d4d4d8; /* zinc-300 */
}
.dark .profile-pagination a:hover {
    background: rgba(42, 122, 39, 0.1);
    border-color: var(--primary, #4ade80);
    color: var(--primary, #4ade80);
}
.dark .profile-pagination .active-page {
    background: var(--primary, #2a7a27);
    border-color: var(--primary, #2a7a27);
    color: #fff;
}

/* Các tuỳ chỉnh thêm cho dark mode */
.dark h1 { color: #f4f4f5; }
.dark .empty-tab { color: #71717a; }
.dark .empty-tab p { color: #a1a1aa; }
.dark .collection-cover-placeholder {
    background: linear-gradient(135deg, #166534, #14532d);
}

/* Modals Override in Dark Mode */
.dark [style*="background:#fff"],
.dark [style*="background: #fff"],
.dark .bg-white {
    background-color: #18181b !important;
    border-color: #27272a !important;
    color: #e4e4e7 !important;
}
.dark .bg-white\/90 {
    background-color: rgba(24, 24, 27, 0.9) !important;
}
.dark .text-gray-700, .dark .text-gray-900 { color: #e4e4e7 !important; }
.dark .text-gray-600 { color: #d4d4d8 !important; }
.dark .text-gray-500, .dark .text-gray-400 { color: #a1a1aa !important; }
.dark .bg-gray-50, .dark .bg-gray-100 { background-color: #27272a !important; }
.dark [style*="background:#f0f0f0"], .dark [style*="background:#e0e0e0"], .dark [style*="background:#f3f4f6"], .dark [style*="background:#e5e7eb"] {
    background-color: #27272a !important;
    color: #e4e4e7 !important;
}
.dark .bg-amber-50 { background-color: rgba(245, 158, 11, 0.1) !important; color: #fcd34d !important; border-color: #f59e0b !important; }
.dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300 { border-color: #3f3f46 !important; }
.dark input, .dark textarea {
    background-color: #18181b !important;
    color: #e4e4e7 !important;
    border-color: #3f3f46 !important;
}
.dark input:focus, .dark textarea:focus {
    border-color: var(--primary, #4ade80) !important;
}

</style>
@endpush

@section('content')
<div class="profile-wrapper">

    {{-- ══ COVER PHOTO ══ --}}
    <div x-data="{
            preview: '{{ $user->cover_photo ? asset('storage/' . $user->cover_photo) : '' }}',
            loading: false,
            pick(e) {
                const f = e.target.files[0];
                if (!f) return;
                if (f.size > 5 * 1024 * 1024) {
                    alert('Ảnh phải nhỏ hơn 5MB!');
                    return;
                }
                const reader = new FileReader();
                reader.onload = ev => {
                    this.preview = ev.target.result;
                    this.loading = true;
                    this.$refs.coverForm.submit();
                };
                reader.readAsDataURL(f);
            }
         }"
         class="profile-cover"
         style="position:relative; overflow:hidden; cursor:{{ $isOwnProfile ? 'pointer' : 'default' }};"
         @if($isOwnProfile) @click="$refs.coverInput.click()" @endif>

        {{-- Background image or gradient --}}
        <template x-if="preview">
            <img :src="preview" alt="Ảnh bìa"
                 style="width:100%;height:100%;object-fit:cover;display:block;">
        </template>
        <template x-if="!preview">
            <div style="width:100%;height:100%;
                        background:linear-gradient(135deg,#1a5c38 0%,#2d9e60 50%,#0d3b22 100%);"></div>
        </template>

        {{-- Gradient overlay bottom --}}
        <div style="position:absolute;inset:0;
                    background:linear-gradient(to top, rgba(0,0,0,.35) 0%, transparent 60%);
                    pointer-events:none;"></div>

        @if($isOwnProfile)
        {{-- Hidden form --}}
        <form x-ref="coverForm" method="POST"
              action="{{ route('profile.cover', $user->id) }}"
              enctype="multipart/form-data" style="display:none;">
            @csrf
            <input x-ref="coverInput" type="file" name="cover_photo"
                   accept="image/jpeg,image/png,image/jpg,image/webp"
                   @change="pick($event)">
        </form>

        {{-- Hover overlay with camera icon --}}
        <div class="cover-upload-overlay"
             style="position:absolute;inset:0;display:flex;align-items:center;
                    justify-content:center;gap:8px;
                    background:rgba(0,0,0,0);
                    transition:background .25s ease;
                    pointer-events:none;">
            <div class="cover-upload-label"
                 style="display:flex;align-items:center;gap:8px;
                        background:rgba(255,255,255,.9);
                        color:#222;font-size:.82rem;font-weight:700;
                        padding:9px 18px;border-radius:10px;
                        opacity:0;transform:translateY(6px);
                        transition:all .25s ease;
                        box-shadow:0 4px 16px rgba(0,0,0,.2);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
                Cập nhật ảnh bìa
            </div>
        </div>

        {{-- Loading spinner overlay --}}
        <div x-show="loading"
             style="position:absolute;inset:0;background:rgba(0,0,0,.4);
                    display:flex;align-items:center;justify-content:center;">
            <svg style="width:36px;height:36px;animation:spin 1s linear infinite;"
                 viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.3)" stroke-width="3"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        @endif
    </div>

    {{-- Header Card --}}
    <div class="profile-header-card">
        <div class="flex flex-wrap justify-between items-start gap-4">
            {{-- Avatar + info --}}
            <div>
                {{-- Avatar wrap với Alpine modal --}}
                <div class="profile-avatar-wrap"
                     x-data="{
                        open: false,
                        preview: null,
                        loading: false,
                        file: null,
                        error: null,
                        pick(e) {
                            const f = e.target.files[0];
                            if (!f) return;
                            if (f.size > 2 * 1024 * 1024) {
                                this.error = 'Ảnh phải nhỏ hơn 2MB!';
                                this.preview = null;
                                return;
                            }
                            this.error = null;
                            this.file = f;
                            const reader = new FileReader();
                            reader.onload = ev => { this.preview = ev.target.result; };
                            reader.readAsDataURL(f);
                        },
                        submit() {
                            if (!this.file) return;
                            this.loading = true;
                            this.$refs.avatarForm.submit();
                        },
                        reset() {
                            this.open = false;
                            this.preview = null;
                            this.file = null;
                            this.error = null;
                            this.loading = false;
                            this.$refs.avatarInput.value = '';
                        }
                     }">

                    {{-- Current avatar display --}}
                    @if($user->avatar)
                        @php
                            $avatarSrc = filter_var($user->avatar, FILTER_VALIDATE_URL)
                                ? $user->avatar
                                : asset('storage/' . $user->avatar);
                        @endphp
                        <img src="{{ $avatarSrc }}"
                             alt="{{ $user->name }}"
                             class="profile-avatar"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="profile-avatar-initials" style="display:none;">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @else
                        <div class="profile-avatar-initials">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif

                    @if($isOwnProfile)
                    {{-- Camera button --}}
                    <button @click="open = true" type="button"
                            class="avatar-upload-btn" title="Đổi ảnh đại diện">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </button>

                    {{-- Hidden form --}}
                    <form x-ref="avatarForm"
                          method="POST"
                          action="{{ route('profile.avatar', $user->id) }}"
                          enctype="multipart/form-data"
                          class="hidden">
                        @csrf
                        <input x-ref="avatarInput" type="file" name="avatar"
                               accept="image/jpeg,image/png,image/jpg"
                               @change="pick($event)">
                    </form>

                    {{-- ═══ MODAL ═══ --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click.self="reset()"
                         style="display:none; position:fixed; inset:0; z-index:999;
                                background:rgba(0,0,0,0.55); display:flex;
                                align-items:center; justify-content:center; padding:16px;">

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             @click.stop
                             style="background:#fff; border-radius:20px; width:100%;
                                    max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,0.25);
                                    overflow:hidden;">

                            {{-- Modal Header --}}
                            <div style="padding:18px 20px; border-bottom:1px solid #f0f0f0;
                                        display:flex; align-items:center; justify-content:space-between;">
                                <span style="font-size:1rem; font-weight:800; color:#111;">📷 Cập nhật ảnh đại diện</span>
                                <button @click="reset()" type="button"
                                        style="width:32px;height:32px;border-radius:50%;border:none;
                                               background:#f0f0f0;cursor:pointer;font-size:1rem;
                                               display:flex;align-items:center;justify-content:center;
                                               transition:background .2s;"
                                        onmouseover="this.style.background='#e0e0e0'"
                                        onmouseout="this.style.background='#f0f0f0'">✕</button>
                            </div>

                            {{-- Modal Body --}}
                            <div style="padding:24px; text-align:center;">

                                {{-- Preview area --}}
                                <div style="position:relative; width:150px; height:150px;
                                            margin:0 auto 20px; border-radius:50%;
                                            overflow:hidden; border:3px solid #e5e7eb;
                                            background:#f9fafb; cursor:pointer;"
                                     @click="$refs.avatarInput.click()">

                                    {{-- Current / preview image --}}
                                    <template x-if="preview">
                                        <img :src="preview" alt="Preview"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </template>
                                    <template x-if="!preview">
                                        <div style="width:100%;height:100%;display:flex;
                                                    align-items:center;justify-content:center;">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                     alt="{{ $user->name }}"
                                                     style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                <div style="font-size:3.5rem;font-weight:900;
                                                            color:#2a7a27;line-height:1;">
                                                    {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </template>

                                    {{-- Hover overlay --}}
                                    <div style="position:absolute;inset:0;background:rgba(0,0,0,0);
                                                display:flex;align-items:center;justify-content:center;
                                                transition:background .2s;"
                                         onmouseover="this.style.background='rgba(0,0,0,0.35)'; this.querySelector('span').style.opacity='1'"
                                         onmouseout="this.style.background='rgba(0,0,0,0)'; this.querySelector('span').style.opacity='0'">
                                        <span style="opacity:0;color:#fff;font-size:.78rem;
                                                     font-weight:700;text-align:center;
                                                     transition:opacity .2s;pointer-events:none;">
                                            📷<br>Chọn ảnh
                                        </span>
                                    </div>
                                </div>

                                {{-- Error message --}}
                                <p x-show="error" x-text="error"
                                   style="color:#dc2626;font-size:.8rem;margin-bottom:12px;
                                          background:#fef2f2;padding:8px 14px;border-radius:8px;"></p>

                                {{-- Choose file button --}}
                                <button type="button"
                                        @click="$refs.avatarInput.click()"
                                        style="background:#f3f4f6;color:#333;border:none;
                                               border-radius:10px;padding:10px 22px;
                                               font-size:.88rem;font-weight:600;cursor:pointer;
                                               transition:background .2s;margin-bottom:8px;"
                                        onmouseover="this.style.background='#e5e7eb'"
                                        onmouseout="this.style.background='#f3f4f6'">
                                    🖼️ Chọn ảnh từ máy
                                </button>
                                <p style="font-size:.72rem;color:#9ca3af;margin:0 0 20px;">
                                    JPG, PNG · Tối đa 2 MB
                                </p>
                            </div>

                            {{-- Modal Footer --}}
                            <div style="padding:16px 20px; border-top:1px solid #f0f0f0;
                                        display:flex; gap:10px; justify-content:flex-end;">
                                <button @click="reset()" type="button"
                                        style="padding:10px 22px;border-radius:10px;border:none;
                                               background:#f3f4f6;color:#333;font-size:.88rem;
                                               font-weight:600;cursor:pointer;transition:background .2s;"
                                        onmouseover="this.style.background='#e5e7eb'"
                                        onmouseout="this.style.background='#f3f4f6'">
                                    Hủy
                                </button>
                                <button @click="submit()" type="button"
                                        :disabled="!file || loading"
                                        :style="(!file || loading)
                                            ? 'opacity:.5;cursor:not-allowed;'
                                            : 'cursor:pointer;'"
                                        style="padding:10px 26px;border-radius:10px;border:none;
                                               background:#2a7a27;color:#fff;font-size:.88rem;
                                               font-weight:700;transition:all .2s;display:flex;
                                               align-items:center;gap:8px;">
                                    <template x-if="loading">
                                        <svg style="width:16px;height:16px;animation:spin 1s linear infinite;"
                                             viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.3)" stroke-width="3"/>
                                            <path d="M12 2a10 10 0 0 1 10 10" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                                        </svg>
                                    </template>
                                    <span x-text="loading ? 'Đang lưu...' : '💾 Lưu ảnh'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>{{-- /profile-avatar-wrap --}}

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
        <div class="profile-actions" x-data>
            <button class="btn-edit-profile" @click="$dispatch('open-edit-profile')">
                ✏️ Chỉnh sửa thông tin
            </button>

            {{-- Nút trở thành CTV: chỉ hiện nếu là reader và chưa gửi request --}}
            @if($user->role === 'reader')
            @php
                $hasPendingRequest = \App\Models\ContributorRequest::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved'])->exists();
            @endphp
            @if(!$hasPendingRequest)
            <button class="btn-edit-profile" style="background:#f59e0b;color:#fff;border-color:#f59e0b;"
                    @click="window.dispatchEvent(new CustomEvent('open-contributor-form'))">
                🤝 Trở thành Cộng tác viên
            </button>
            @else
            <span style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;color:#6b7280;padding:8px 16px;background:#f3f4f6;border-radius:999px;font-weight:600;">
                ⏳ Đang chờ duyệt CTV
            </span>
            @endif
            @endif
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
        @if($posts->hasPages())
        <div class="profile-pagination">
            @if($posts->onFirstPage())
                <span style="opacity:.35;">&#171;</span>
            @else
                <a href="{{ $posts->url(1) }}" title="Trang đầu">&#171;</a>
            @endif
            @if($posts->onFirstPage())
                <span style="opacity:.35;">&#8249;</span>
            @else
                <a href="{{ $posts->previousPageUrl() }}" title="Trang trước">&#8249;</a>
            @endif
            @php
                $startPage = max(1, $posts->currentPage() - 2);
                $endPage = min($posts->lastPage(), $posts->currentPage() + 2);
            @endphp
            @foreach($posts->getUrlRange($startPage, $endPage) as $page => $url)
                @if($page == $posts->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}" title="Trang sau">&#8250;</a>
            @else
                <span style="opacity:.35;">&#8250;</span>
            @endif
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
            <button class="btn-new-collection" x-data @click="$dispatch('open-new-collection')">
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
        <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-sm border border-transparent dark:border-zinc-800">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xl">👤</span>
                    <div>
                        <div class="text-xs text-gray-400 dark:text-zinc-500 font-semibold uppercase tracking-wide">Họ tên</div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xl">📧</span>
                    <div>
                        <div class="text-xs text-gray-400 dark:text-zinc-500 font-semibold uppercase tracking-wide">Email</div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $isOwnProfile ? $user->email : '***@***.***' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xl">🎓</span>
                    <div>
                        <div class="text-xs text-gray-400 dark:text-zinc-500 font-semibold uppercase tracking-wide">Vai trò</div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $user->roleLabel() }}</div>
                    </div>
                </div>
                @if($user->bio ?? false)
                <div class="flex items-start gap-3">
                    <span class="text-xl mt-1">📝</span>
                    <div>
                        <div class="text-xs text-gray-400 dark:text-zinc-500 font-semibold uppercase tracking-wide mb-1">Giới thiệu</div>
                        <div class="text-gray-700 dark:text-zinc-300">{{ $user->bio }}</div>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3">
                    <span class="text-xl">📅</span>
                    <div>
                        <div class="text-xs text-gray-400 dark:text-zinc-500 font-semibold uppercase tracking-wide">Tham gia từ</div>
                        <div class="font-semibold text-gray-900 dark:text-zinc-100">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine Modal: Sửa thông tin --}}
@if($isOwnProfile)
<div x-data="{ open: false }"
     x-on:open-edit-profile.window="open = true"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[999] flex items-center justify-center p-4"
     style="display:none;">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    {{-- Modal panel --}}
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>
        {{-- Header --}}
        <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-[#1a5c38] to-[#2d9e60]">
            <h5 class="text-white font-bold text-xl">✏️ Chỉnh sửa thông tin</h5>
            <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        {{-- Body --}}
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <div class="p-8 flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           placeholder="Nhập họ và tên..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base outline-none focus:border-[#2a7a27] focus:ring-2 focus:ring-[#2a7a27]/20 transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Giới thiệu bản thân</label>
                    <textarea name="bio" rows="5" maxlength="300"
                              placeholder="Ví dụ: Sinh viên năm 4 Khoa CNTT, yêu thích viết lách và báo chí..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base outline-none focus:border-[#2a7a27] focus:ring-2 focus:ring-[#2a7a27]/20 transition resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1.5">Tối đa 300 ký tự</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Hủy</button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-bold text-white bg-[#2a7a27] hover:bg-[#1b5e20] rounded-xl shadow transition">💾 Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

{{-- Alpine Modal: Tạo bộ sưu tập mới --}}
<div x-data="{ open: false, name: '', isPublic: false }"
     x-on:open-new-collection.window="open = true"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[999] flex items-center justify-center p-4"
     style="display:none;">
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden" @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-[#1a5c38] to-[#2d9e60]">
            <h5 class="text-white font-bold text-xl">🔖 Tạo bộ sưu tập mới</h5>
            <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-8 flex flex-col gap-6">
            <div>
                <label class="block text-base font-bold text-gray-700 mb-2">Tên bộ sưu tập</label>
                <input type="text" x-model="name" maxlength="100" placeholder="Ví dụ: Bài viết yêu thích"
                       class="w-full border border-gray-300 rounded-xl px-5 py-3.5 text-base outline-none focus:border-[#2a7a27] focus:ring-2 focus:ring-[#2a7a27]/20 transition shadow-sm">
            </div>
            <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded-lg transition-colors -ml-2">
                <input type="checkbox" x-model="isPublic" class="w-5 h-5 accent-[#2a7a27] rounded cursor-pointer">
                <span class="text-base text-gray-700 font-medium cursor-pointer">Công khai (mọi người đều xem được)</span>
            </label>
        </div>
        <div class="flex items-center justify-end gap-3 px-8 py-5 border-t border-gray-100 bg-gray-50/50">
            <button type="button" @click="open = false"
                    class="px-6 py-2.5 text-base font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl transition-colors shadow-sm">Hủy</button>
            <button type="button" @click="createCollection(name, isPublic, () => open = false)"
                    class="px-6 py-2.5 text-base font-bold text-white bg-[#2a7a27] hover:bg-[#1b5e20] rounded-xl shadow-md transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tạo bộ sưu tập
            </button>
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

// Tạo bộ sưu tập (Alpine x-data gọi hàm này)
function createCollection(name, isPublic, onSuccess) {
    if (!name || !name.trim()) { alert('Vui lòng nhập tên bộ sưu tập!'); return; }
    fetch("{{ route('collections.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ name: name.trim(), is_public: isPublic }),
    }).then(r => r.json()).then(data => {
        if (data.collection) { onSuccess && onSuccess(); window.location.reload(); }
    });
}
</script>
@endpush

{{-- ══════ Modal: Đăng ký Cộng tác viên ══════ --}}
@if($isOwnProfile && $user->role === 'reader')
<div x-data="ctvModal()"
     x-on:open-contributor-form.window="open = true; loading = false; success = false; errorMsg = ''"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
     style="display:none;">
    <div class="absolute inset-0 bg-black/60" @click="if(!loading) open = false"></div>
    <div class="relative w-full max-w-2xl bg-white dark:bg-[#1c1c1e] rounded-[24px] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] border border-gray-100 dark:border-[#2c2c2e] overflow-hidden max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-[0.97] translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-8 py-6 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-xl sticky top-0 z-10 border-b border-gray-100/80 dark:border-[#2c2c2e]/80">
            <div>
                <h5 class="text-gray-900 dark:text-white font-bold text-[22px] tracking-tight">Đăng ký Cộng tác viên</h5>
                <p class="text-gray-500 dark:text-gray-400 text-[13px] font-medium mt-1">Vui lòng điền thông tin chính xác để nhận nhuận bút</p>
            </div>
            <button @click="if(!loading) open = false" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-[#2c2c2e] hover:bg-gray-200 dark:hover:bg-[#3c3c3e] text-gray-500 dark:text-gray-400 rounded-full transition-colors focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Loading overlay ── --}}
        <div x-show="loading" x-transition
             class="absolute inset-0 bg-white/90 dark:bg-[#1c1c1e]/90 z-20 flex flex-col items-center justify-center gap-4 backdrop-blur-md"
             style="display:none;">
            <svg class="animate-spin w-10 h-10 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <p class="text-gray-900 dark:text-white font-semibold text-[15px] animate-pulse tracking-tight">Đang gửi yêu cầu...</p>
        </div>

        {{-- ── Success screen ── --}}
        <div x-show="success" x-transition
             class="absolute inset-0 bg-white dark:bg-[#1c1c1e] z-20 flex flex-col items-center justify-center gap-5 text-center p-8"
             style="display:none;">
            {{-- Animated tick --}}
            <div class="w-20 h-20 rounded-full bg-blue-50 dark:bg-[#0a84ff]/20 flex items-center justify-center"
                 style="animation: popIn .4s cubic-bezier(.68,-.55,.27,1.55) both;">
                <svg class="w-10 h-10 text-[#007aff] dark:text-[#0a84ff]" style="animation: drawTick .5s ease .3s both;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"
                          style="stroke-dasharray:30;stroke-dashoffset:30;animation:dashDraw .5s ease .3s forwards;"/>
                </svg>
            </div>
            <div>
                <h3 class="text-[22px] font-bold text-gray-900 dark:text-white mb-2 tracking-tight">Đăng ký thành công</h3>
                <p class="text-gray-500 dark:text-gray-400 text-[15px] leading-relaxed max-w-sm mx-auto">Yêu cầu của bạn đã được gửi đến Ban Biên tập. Chúng tôi sẽ xét duyệt và thông báo sớm.</p>
            </div>
            <button @click="open = false; window.location.reload()"
                    class="mt-4 px-10 py-3.5 bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-200 text-white dark:text-gray-900 font-semibold rounded-full transition-all text-[15px]">
                Hoàn tất
            </button>
        </div>

        {{-- Error --}}
        <div x-show="errorMsg" x-transition class="mx-8 mt-6 flex items-start gap-3 bg-red-50 dark:bg-red-500/10 border-none text-red-600 dark:text-red-400 rounded-[14px] px-4 py-3.5 text-[14px] font-medium">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span x-text="errorMsg"></span>
        </div>

        {{-- Body form --}}
        <form id="ctvForm" @submit.prevent="submit($el)">
            @csrf
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                <div class="md:col-span-2 pb-2 mb-2 border-b border-gray-100 dark:border-[#2c2c2e] flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 flex items-center justify-center font-semibold text-xs shrink-0">1</span>
                    <h6 class="font-bold text-gray-900 dark:text-white text-[15px] tracking-tight">Thông tin sinh viên</h6>
                </div>

                {{-- Họ tên --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Họ và tên đầy đủ <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ $user->name }}" required
                           placeholder="Trần Nguyễn Minh Thiên"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Địa chỉ Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                           placeholder="example@email.com"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                {{-- MSSV --}}
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Mã số sinh viên <span class="text-red-500">*</span></label>
                    <input type="text" name="mssv" required
                           placeholder="2100001234"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                {{-- Lớp --}}
                <div>
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Lớp <span class="text-red-500">*</span></label>
                    <input type="text" name="class_name" required
                           placeholder="21BITV01"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none uppercase shadow-sm dark:shadow-none" oninput="this.value=this.value.toUpperCase()">
                </div>

                {{-- Số CMND/CCCD --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Số CMND / Căn cước công dân <span class="text-red-500">*</span></label>
                    <input type="text" name="id_card" required pattern="[0-9]{9,12}" title="Gồm 9 hoặc 12 số"
                           placeholder="079203001234"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                <div class="md:col-span-2 pb-2 mb-2 mt-4 border-b border-gray-100 dark:border-[#2c2c2e] flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 flex items-center justify-center font-semibold text-xs shrink-0">2</span>
                    <h6 class="font-bold text-gray-900 dark:text-white text-[15px] tracking-tight">Thông tin nhận nhuận bút</h6>
                </div>

                {{-- Ngân hàng --}}
                <div x-data="{
                        openBank: false,
                        searchQuery: '',
                        selectedBank: null,
                        banks: [],
                        loadingBanks: true,
                        get filteredBanks() {
                            if (this.searchQuery === '') return this.banks;
                            const q = this.searchQuery.toLowerCase();
                            return this.banks.filter(b => 
                                b.name.toLowerCase().includes(q) || 
                                b.shortName.toLowerCase().includes(q) || 
                                b.code.toLowerCase().includes(q)
                            );
                        },
                        async init() {
                            try {
                                const res = await fetch('https://api.vietqr.io/v2/banks');
                                const json = await res.json();
                                if (json.code === '00') {
                                    this.banks = json.data;
                                }
                            } catch (e) {
                                console.error('Failed to fetch banks', e);
                                // Fallback
                                this.banks = [
                                    @foreach(\App\Models\ContributorRequest::banks() as $bank)
                                    { name: '{{ addslashes($bank) }}', shortName: '{{ addslashes($bank) }}', logo: '' },
                                    @endforeach
                                ];
                            } finally {
                                this.loadingBanks = false;
                            }
                        }
                    }"
                    class="relative md:col-span-1">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngân hàng <span class="text-red-500">*</span></label>
                    
                    {{-- Input ẩn --}}
                    <input type="hidden" name="bank_name" :value="selectedBank ? selectedBank.shortName : ''" required>

                    {{-- Nút Dropdown --}}
                    <button type="button" @click="openBank = !openBank" @click.outside="openBank = false"
                            class="w-full flex items-center justify-between border rounded-[14px] px-4 py-3.5 text-[15px] outline-none transition-all text-left shadow-sm dark:shadow-none"
                            :class="openBank ? 'bg-white border-[#007aff] ring-4 ring-[#007aff]/10 dark:bg-white/10 dark:border-[#0a84ff] dark:ring-[#0a84ff]/20' : 'bg-[#f5f5f7] border-transparent dark:bg-white/5 dark:border-white/10'">
                        <div class="flex items-center gap-3 truncate" :class="!selectedBank ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-white'">
                            <template x-if="selectedBank && selectedBank.logo">
                                <img :src="selectedBank.logo" class="w-6 h-6 object-contain rounded-sm bg-white p-0.5" alt="">
                            </template>
                            <span class="truncate font-medium transition-colors" x-text="selectedBank ? selectedBank.shortName : 'Chọn ngân hàng'"></span>
                        </div>
                        <i class="bi bi-chevron-down text-gray-400 dark:text-gray-500 transition-transform" :class="openBank ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Menu --}}
                    <div x-show="openBank" x-cloak
                         x-transition:enter="transition ease-out duration-250"
                         x-transition:enter-start="opacity-0 scale-[0.98] -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-[0.98] -translate-y-2"
                         class="absolute z-50 w-[140%] sm:w-full mt-3 top-full left-0 bg-white/90 dark:bg-[#1c1c1e]/85 backdrop-blur-3xl border border-gray-200/50 dark:border-white/10 rounded-[22px] shadow-[0_20px_60px_rgba(0,0,0,0.2)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col origin-top"
                         style="display:none; max-height: 350px;">
                        
                        {{-- Ô Search --}}
                        <div class="p-3 border-b border-gray-100/80 dark:border-white/5 shrink-0 bg-gray-50/50 dark:bg-white/[0.02]">
                            <div class="relative group">
                                <input type="text" x-model="searchQuery" placeholder="Tìm tên, mã ngân hàng..."
                                       @keydown.escape="openBank = false"
                                       class="w-full px-4 py-2.5 bg-white dark:bg-black/40 border border-gray-200 dark:border-white/5 rounded-[12px] text-[14px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 outline-none focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all shadow-sm dark:shadow-none">
                            </div>
                        </div>

                        {{-- DSS Ngân hàng --}}
                        <ul class="overflow-y-auto w-full py-2 overscroll-contain apple-scrollbar flex-1">
                            {{-- Loading --}}
                            <div x-show="loadingBanks" class="px-4 py-10 text-center flex flex-col items-center gap-3">
                                <svg class="animate-spin w-6 h-6 text-[#007aff]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span class="text-[13px] text-gray-500">Đang tải danh sách...</span>
                            </div>

                            <template x-for="bank in filteredBanks" :key="bank.id || bank.name">
                                <li @click="selectedBank = bank; openBank = false; searchQuery = ''"
                                    class="px-4 py-3 mx-2 my-1 rounded-[12px] hover:bg-gray-100 dark:hover:bg-white/[0.08] cursor-pointer flex items-center gap-3 transition-all active:scale-[0.98]">
                                    <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shrink-0 border border-gray-100 p-1 shadow-sm">
                                        <template x-if="bank.logo">
                                            <img :src="bank.logo" class="w-full h-full object-contain" :alt="bank.shortName">
                                        </template>
                                        <template x-if="!bank.logo">
                                            <span class="text-[12px] font-bold text-gray-400" x-text="bank.shortName.substring(0, 2).toUpperCase()"></span>
                                        </template>
                                    </div>
                                    <div class="flex flex-col flex-1 min-w-0">
                                        <span x-text="bank.shortName" class="text-[15px] text-gray-900 dark:text-white font-bold leading-tight truncate"></span>
                                        <span x-text="bank.name" class="text-[11px] text-gray-500 dark:text-gray-400 truncate"></span>
                                    </div>
                                    <div x-show="selectedBank && selectedBank.code === bank.code" class="shrink-0">
                                        <svg class="w-5 h-5 text-[#007aff] dark:text-[#0a84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                            <li x-show="!loadingBanks && filteredBanks.length === 0" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500 flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span class="text-[14px] font-medium">Không tìm thấy ngân hàng này</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Số tài khoản --}}
                <div class="md:col-span-1">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Số tài khoản <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_account" required
                           placeholder="0123456789" pattern="[0-9]{6,20}" title="Chỉ nhập số, 6–20 ký tự"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] font-mono text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                {{-- Chi nhánh --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên chi nhánh / Tỉnh thành <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_branch" required
                           placeholder="Ví dụ: Chi nhánh Gò Vấp, TP.HCM"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none shadow-sm dark:shadow-none">
                </div>

                {{-- Chủ tài khoản --}}
                <div class="md:col-span-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Tên chủ tài khoản <span class="text-red-500">*</span>
                        <span class="text-gray-400 dark:text-gray-500 font-normal ml-1">(đúng như in trên thẻ)</span>
                    </label>
                    <input type="text" name="account_holder" value="{{ strtoupper($user->name) }}" required
                           placeholder="TRAN NGUYEN MINH THIEN"
                           class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] font-medium text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none uppercase shadow-sm dark:shadow-none"
                           oninput="this.value=this.value.toUpperCase()">
                </div>

                {{-- Ghi chú --}}
                <div class="md:col-span-2 mt-2">
                    <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2">Ghi chú thêm</label>
                    <textarea name="note" rows="2" maxlength="500"
                              placeholder="Kinh nghiệm viết lách của bạn (nếu có)..."
                              class="w-full bg-[#f5f5f7] dark:bg-white/5 border border-transparent dark:border-white/10 rounded-[14px] px-4 py-3.5 text-[15px] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-white dark:focus:bg-white/10 focus:border-[#007aff] dark:focus:border-[#0a84ff] focus:ring-4 focus:ring-[#007aff]/10 dark:focus:ring-[#0a84ff]/20 transition-all outline-none resize-none shadow-sm dark:shadow-none"></textarea>
                </div>

                {{-- Info --}}
                <div class="md:col-span-2 text-center text-[13px] text-gray-400 mt-2">
                    Yêu cầu sẽ được Ban Biên tập xem xét trong vòng 24-48 giờ.
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row-reverse items-center justify-start gap-3 px-8 py-6 bg-white dark:bg-[#1c1c1e] border-t border-gray-100 dark:border-[#2c2c2e]">
                <button type="submit" :disabled="loading"
                        class="w-full sm:w-auto px-8 py-3 text-[15px] font-semibold text-white bg-[#007aff] dark:bg-[#0a84ff] hover:bg-[#005bb5] dark:hover:bg-[#007aff] rounded-full transition-all disabled:opacity-60">
                    Gửi yêu cầu
                </button>
                <button type="button" @click="open = false" :disabled="loading"
                        class="w-full sm:w-auto px-8 py-3 text-[15px] font-semibold text-gray-600 dark:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-[#2c2c2e] rounded-full transition-all">
                    Hủy bỏ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom Apple-like Scrollbar for Dropdown */
.apple-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.apple-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.apple-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.4); /* gray-400 equivalent */
    border-radius: 10px;
}
.apple-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.7); /* gray-500 equivalent */
}
.dark .apple-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.2);
}
.dark .apple-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.4);
}
</style>

<style>
@keyframes popIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes dashDraw { to { stroke-dashoffset: 0; } }
</style>

<script>
function ctvModal() {
    return {
        open: false,
        loading: false,
        success: false,
        errorMsg: '',
        submit(formEl) {
            const fd   = new FormData(formEl);
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            this.loading  = true;
            this.errorMsg = '';

            fetch('{{ route("contributor.request.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: fd,
            })
            .then(res => res.json())
            .then(json => {
                this.loading = false;
                if (json.success) {
                    this.success = true;
                } else {
                    const errs = json.errors
                        ? Object.values(json.errors).flat().join(' ')
                        : (json.error || 'Có lỗi xảy ra.');
                    this.errorMsg = errs;
                }
            })
            .catch(() => {
                this.loading  = false;
                this.errorMsg = 'Không thể kết nối. Vui lòng thử lại.';
            });
        }
    };
}
</script>
@endif

