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
                    @click="$dispatch('open-contributor-form')">
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
            @foreach($posts->getUrlRange(max(1, $posts->currentPage()-2), min($posts->lastPage(), $posts->currentPage()+2)) as $page => $url)
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
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div class="flex items-center gap-3">
                    <span style="font-size:1.3rem;">👤</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Họ tên</div>
                        <div style="font-weight:600;color:#222;">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span style="font-size:1.3rem;">📧</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Email</div>
                        <div style="font-weight:600;color:#222;">{{ $isOwnProfile ? $user->email : '***@***.***' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span style="font-size:1.3rem;">🎓</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Vai trò</div>
                        <div style="font-weight:600;color:#222;">{{ $user->roleLabel() }}</div>
                    </div>
                </div>
                @if($user->bio ?? false)
                <div class="flex items-start gap-3">
                    <span style="font-size:1.3rem;">📝</span>
                    <div>
                        <div style="font-size:.75rem;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Giới thiệu</div>
                        <div style="color:#444;">{{ $user->bio }}</div>
                    </div>
                </div>
                @endif
                <div class="flex items-center gap-3">
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
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden" @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-[#1a5c38] to-[#2d9e60]">
            <h5 class="text-white font-bold text-lg">🔖 Tạo bộ sưu tập mới</h5>
            <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 flex flex-col gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tên bộ sưu tập</label>
                <input type="text" x-model="name" maxlength="100" placeholder="Ví dụ: Bài viết yêu thích"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#2a7a27] focus:ring-2 focus:ring-[#2a7a27]/20 transition">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="isPublic" class="w-4 h-4 accent-[#2a7a27]">
                <span class="text-sm text-gray-600">Công khai (mọi người đều xem được)</span>
            </label>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button type="button" @click="open = false"
                    class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Hủy</button>
            <button type="button" @click="createCollection(name, isPublic, () => open = false)"
                    class="px-5 py-2 text-sm font-bold text-white bg-[#2a7a27] hover:bg-[#1b5e20] rounded-xl shadow transition">🔖 Tạo bộ sưu tập</button>
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
<div x-data="{ open: false, loading: false, success: false, errorMsg: '' }"
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
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-[#d97706] to-[#f59e0b] sticky top-0 z-10">
            <div>
                <h5 class="text-white font-bold text-xl">🤝 Đăng ký Cộng tác viên</h5>
                <p class="text-white/80 text-sm mt-0.5">Điền thông tin để gửi yêu cầu đến Admin</p>
            </div>
            <button @click="if(!loading) open = false" class="text-white/80 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Loading overlay ── --}}
        <div x-show="loading" x-transition
             class="absolute inset-0 bg-white/90 z-20 flex flex-col items-center justify-center gap-4"
             style="display:none;">
            <svg class="animate-spin w-14 h-14 text-amber-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <p class="text-amber-600 font-semibold text-lg">Đang gửi yêu cầu...</p>
        </div>

        {{-- ── Success screen ── --}}
        <div x-show="success" x-transition
             class="absolute inset-0 bg-white z-20 flex flex-col items-center justify-center gap-5 text-center p-8"
             style="display:none;">
            {{-- Animated tick --}}
            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center"
                 style="animation: popIn .4s cubic-bezier(.68,-.55,.27,1.55) both;">
                <svg class="w-14 h-14 text-green-500" style="animation: drawTick .5s ease .3s both;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"
                          style="stroke-dasharray:30;stroke-dashoffset:30;animation:dashDraw .5s ease .3s forwards;"/>
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Gửi thành công! 🎉</h3>
                <p class="text-gray-500 text-base">Yêu cầu Cộng tác viên của bạn đã được gửi đến Admin.<br>Bạn sẽ nhận thông báo qua chuông khi được xét duyệt.</p>
            </div>
            <button @click="open = false; window.location.reload()"
                    class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl shadow transition text-base">
                Đóng
            </button>
        </div>

        {{-- Error --}}
        <div x-show="errorMsg" x-transition class="mx-8 mt-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <span x-text="errorMsg"></span>
        </div>

        {{-- Body form --}}
        <form id="ctvForm" @submit.prevent="submitCTV($el, $data)">
            @csrf
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Họ tên --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên đầy đủ <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ $user->name }}" required
                           placeholder="TRẦN NGUYỄN MINH THIÊN"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base outline-none focus:border-[#f59e0b] focus:ring-2 focus:ring-[#f59e0b]/20 transition">
                </div>
                {{-- Ngân hàng --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ngân hàng <span class="text-red-500">*</span></label>
                    <select name="bank_name" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base outline-none focus:border-[#f59e0b] focus:ring-2 focus:ring-[#f59e0b]/20 transition bg-white">
                        <option value="">-- Chọn ngân hàng --</option>
                        @foreach(\App\Models\ContributorRequest::banks() as $bank)
                        <option value="{{ $bank }}">{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Số tài khoản --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Số tài khoản <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_account" required
                           placeholder="0123456789" pattern="[0-9]{6,20}" title="Chỉ nhập số, 6–20 ký tự"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base font-mono outline-none focus:border-[#f59e0b] focus:ring-2 focus:ring-[#f59e0b]/20 transition">
                </div>
                {{-- Chủ tài khoản --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Tên chủ tài khoản <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal text-xs">(đúng như in trên thẻ)</span>
                    </label>
                    <input type="text" name="account_holder" value="{{ strtoupper($user->name) }}" required
                           placeholder="TRAN NGUYEN MINH THIEN"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base font-medium outline-none focus:border-[#f59e0b] focus:ring-2 focus:ring-[#f59e0b]/20 transition uppercase"
                           oninput="this.value=this.value.toUpperCase()">
                </div>
                {{-- Ghi chú --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ghi chú thêm</label>
                    <textarea name="note" rows="3" maxlength="500"
                              placeholder="Lý do muốn trở thành cộng tác viên, kinh nghiệm viết lách..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base outline-none focus:border-[#f59e0b] focus:ring-2 focus:ring-[#f59e0b]/20 transition resize-none"></textarea>
                </div>
                {{-- Info --}}
                <div class="md:col-span-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                    ℹ️ Admin sẽ xét duyệt và thông báo kết quả qua chuông thông báo của bạn.
                </div>
            </div>
            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-8 py-5 border-t border-gray-100">
                <button type="button" @click="open = false" :disabled="loading"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Hủy</button>
                <button type="submit" :disabled="loading"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl shadow transition disabled:opacity-60">
                    📨 Gửi yêu cầu
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes popIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes dashDraw { to { stroke-dashoffset: 0; } }
</style>

<script>
function submitCTV(formEl, data) {
    const form    = formEl;
    const fd      = new FormData(form);
    const csrf    = document.querySelector('meta[name="csrf-token"]').content;
    data.loading  = true;
    data.errorMsg = '';

    fetch('{{ route("contributor.request.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: fd,
    })
    .then(res => res.json())
    .then(json => {
        data.loading = false;
        if (json.success) {
            data.success = true;
        } else {
            // Validation errors
            const errs = json.errors ? Object.values(json.errors).flat().join(' ') : (json.error || 'Có lỗi xảy ra.');
            data.errorMsg = errs;
        }
    })
    .catch(() => {
        data.loading  = false;
        data.errorMsg = 'Không thể kết nối. Vui lòng thử lại.';
    });
}
</script>
@endif

