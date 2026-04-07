@extends('layouts.admin')
@section('title', 'Quản lý Media')

@section('styles')
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<style>
    .dropzone {
        border: 2px dashed #cbd5e1; /* slate-300 */
        border-radius: 1.25rem;
        background: #f8fafc; /* slate-50 */
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        min-height: 260px;
    }
    .dropzone:hover {
        border-color: #10b981; /* emerald-500 */
        background: #ecfdf5; /* emerald-50 */
    }
    .dropzone.dz-drag-hover {
        border-color: #059669; /* emerald-600 */
        background: #d1fae5; /* emerald-100 */
        transform: scale(1.02);
    }
    .dropzone .dz-message {
        margin: 0;
        color: #64748b; /* slate-500 */
        transition: color 0.3s ease;
    }
    .dropzone .dz-message i {
        font-size: 4rem;
        color: #94a3b8; /* slate-400 */
        margin-bottom: 0.5rem;
        display: inline-block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dropzone:hover .dz-message i {
        color: #10b981;
        transform: translateY(-5px) scale(1.05);
    }
    .dropzone:hover .dz-message {
        color: #334155; /* slate-700 */
    }
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="{ openUpload: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 tracking-tight"><i class="bi bi-images text-emerald-500 me-2"></i>Thư viện Media</h3>
            <p class="text-sm text-emerald-50/90 font-medium mt-1">Tổng cộng: <strong class="text-white">{{ number_format($totalMedia) }}</strong> tập tin</p>
        </div>
        <button type="button" @click="openUpload = !openUpload" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-cloud-arrow-up text-lg"></i> <span x-text="openUpload ? 'Đóng tải lên' : 'Tải lên Media'"></span>
        </button>
    </div>

    <!-- Upload Section -->
    <div x-show="openUpload" x-collapse x-cloak class="mb-8" x-data="{ shareType: 'public' }">
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-emerald-100 p-6 md:p-8 relative overflow-hidden">
            <!-- Decorative background blob -->
            <div class="absolute top-0 right-0 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none transition-all duration-700 transform translate-x-1/3 -translate-y-1/3"></div>
            
            <div class="flex flex-col gap-6 md:gap-8 relative z-10 w-full max-w-4xl mx-auto">
                <!-- Step 1: Share Options -->
                <div class="w-full bg-gray-50/70 border border-gray-100 rounded-2xl p-5 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 md:gap-8">
                    <div class="md:w-5/12 shrink-0">
                        <div class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-emerald-100 to-green-50 text-emerald-600 rounded-xl mb-3 shadow-inner border border-emerald-50">
                            <i class="bi bi-shield-lock-fill text-xl"></i>
                        </div>
                        <h5 class="text-lg font-extrabold text-gray-900 tracking-tight">Cài đặt Quyền xem</h5>
                        <p class="text-xs text-gray-500 mt-1.5 leading-relaxed pr-4">Thiết lập trước giới hạn đối tượng có thể truy cập những tập tin bạn sắp tải lên.</p>
                    </div>

                    <div class="md:w-7/12 w-full flex flex-col sm:flex-row gap-4">
                        <!-- Select: Share Type -->
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                <i class="bi bi-diagram-3-fill text-emerald-500 me-1"></i> Chế độ chia sẻ
                            </label>
                            <div class="relative group">
                                <select id="shareTypeSelect" name="share_type" x-model="shareType" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 hover:border-emerald-300 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all cursor-pointer appearance-none shadow-sm">
                                    <option value="public">🌍 Công khai (Tất cả)</option>
                                    <option value="private">🔒 Riêng tư (Chỉ mình tôi)</option>
                                    <option value="role">👥 Nhóm quyền</option>
                                    <option value="user">👤 Một user cụ thể</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400 group-hover:text-emerald-500 transition-colors">
                                    <i class="bi bi-chevron-expand"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div class="flex-1" x-show="shareType === 'role'" style="display: none;">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Chọn Nhóm</label>
                            <div class="relative group">
                                <select id="sharedRoleSelect" name="shared_role" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 hover:border-emerald-300 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all cursor-pointer appearance-none shadow-sm">
                                    @foreach($roles as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-[10px]"></i></div>
                            </div>
                        </div>

                        <!-- User Selection -->
                        <div class="flex-1" x-show="shareType === 'user'" style="display: none;">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Chọn Người dùng</label>
                            <div class="relative group">
                                <select id="sharedUserSelect" name="shared_user_id" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 hover:border-emerald-300 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all cursor-pointer appearance-none shadow-sm">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ mb_strimwidth($user->name, 0, 20, '...') }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-[10px]"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Dropzone Area -->
                <div class="w-full">
                    <form action="{{ route('admin.media.upload') }}" class="dropzone w-full flex-col items-center justify-center p-8 bg-white" id="mediaDropzone">
                        @csrf
                        <div class="dz-message group text-center flex flex-col items-center w-full">
                            <i class="bi bi-cloud-arrow-up-fill text-6xl text-emerald-500 mb-2 group-hover:scale-110 transition-transform"></i>
                            <h4 class="text-xl md:text-2xl font-extrabold text-gray-800 tracking-tight">Kéo & Thả tập tin vào đây</h4>
                            <p class="text-sm text-gray-500 mt-2 mb-4">Hoặc click để duyệt tệp từ máy tính của bạn</p>
                            
                            <!-- Phím tắt (Sử dụng thẻ span giả kbd để không bị ghi đè CSS) -->
                            <div class="inline-flex items-center box-border gap-2 text-[11px] font-medium text-gray-600 bg-gray-50 px-4 py-2 rounded-xl border border-gray-200 shadow-sm mt-1 mb-4">
                                <span>PHÍM TẮT DÁN NHANH TRỰC TIẾP:</span>
                                <div class="flex items-center gap-1.5 box-border">
                                    <span class="inline-block min-w-[32px] text-center px-2 py-1 bg-white border border-gray-300 border-b-[3px] rounded-lg text-gray-800 font-mono font-bold">Ctrl</span>
                                    <span class="text-gray-400 font-bold">+</span>
                                    <span class="inline-block min-w-[32px] text-center px-2 py-1 bg-white border border-gray-300 border-b-[3px] rounded-lg text-gray-800 font-mono font-bold">V</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-2 mt-2">
                                <span class="px-2.5 py-1 box-border bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase">JPG</span>
                                <span class="px-2.5 py-1 box-border bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase">PNG</span>
                                <span class="px-2.5 py-1 box-border bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase">WEBP</span>
                                <span class="px-2.5 py-1 box-border bg-gray-100 rounded text-[10px] font-bold text-gray-500 uppercase">MP4</span>
                                <span class="px-2.5 py-1 box-border bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase ml-1">MAX 20MB</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên file..." class="w-full !pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all">
            </div>
            <div class="w-full sm:w-48 relative">
                <select name="type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">-- Mọi loại --</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Chỉ Ảnh</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Chỉ Video</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-xs"></i></div>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 w-full sm:w-auto">
                <i class="bi bi-funnel"></i> Lọc dữ liệu
            </button>
            @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('admin.media.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all text-center w-full sm:w-auto">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-5 py-4 w-20 text-center">Ảnh gốc</th>
                        <th class="px-4 py-4">Tên file</th>
                        <th class="px-4 py-4 text-center">Loại</th>
                        <th class="px-4 py-4 text-center">Kích thước</th>
                        <th class="px-4 py-4 text-center">Người Upload</th>
                        <th class="px-4 py-4 text-center min-w-[120px]">Shared</th>
                        <th class="px-4 py-4 text-center">Ngày Upload</th>
                        <th class="px-5 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse ($media as $item)
                    <tr class="hover:bg-gray-50/80 transition-colors group" x-data="{ 
                        previewOpen: false, 
                        editOpen: false,
                        deleteOpen: false,
                        editShareType: '{{ !$item->is_shared ? "private" : (is_null($item->shared_role) && is_null($item->shared_user_id) ? "public" : (!is_null($item->shared_role) ? "role" : "user")) }}'
                    }">
                        <td class="px-5 py-3 text-center">
                            <div class="w-12 h-12 mx-auto rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">
                                @if(str_starts_with($item->file_type, 'image/'))
                                    <img src="{{ $item->publicUrl() }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-file-play-fill text-emerald-500 text-2xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 group-hover:text-emerald-700 transition">
                            <button type="button" @click.prevent="previewOpen = true" class="text-left hover:underline">
                                {{ Str::limit($item->file_name, 30) }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded bg-gray-100 text-gray-600 text-[10px] font-bold tracking-wide uppercase">{{ explode('/', $item->file_type)[0] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center font-medium text-gray-500 text-xs">
                            {{ number_format($item->file_size / 1024, 1) }} KB
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 text-xs font-medium">
                            {{ mb_strimwidth($item->user->name ?? 'Unknown', 0, 15, '...') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$item->is_shared)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px] font-bold border border-gray-200/60 shadow-sm"><i class="bi bi-lock-fill"></i> Riêng tư</span>
                            @else
                                @if(is_null($item->shared_role) && is_null($item->shared_user_id))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 shadow-sm"><i class="bi bi-globe"></i> Công khai</span>
                                @elseif(!is_null($item->shared_role))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200/60 shadow-sm"><i class="bi bi-people-fill"></i> Nhóm: {{ ucfirst($item->shared_role) }}</span>
                                @elseif(!is_null($item->shared_user_id))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-[11px] font-bold border border-purple-200/60 shadow-sm"><i class="bi bi-person-fill"></i> User: {{ mb_strimwidth($item->sharedUser->name ?? 'Unknown', 0, 10, '...') }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs font-medium">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button @click.prevent="previewOpen = true" type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors shadow-sm" title="Xem trước">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button @click.prevent="editOpen = true" type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-colors shadow-sm" title="Sửa quyền quản lý">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button @click.prevent="deleteOpen = true" type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Xóa">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>

                            <!-- Preview Modal Alpine -->
                            <template x-teleport="body">
                                <div x-show="previewOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @click.self="previewOpen = false">
                                    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col items-center justify-center relative p-6">
                                        <button type="button" @click="previewOpen = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors z-10"><i class="bi bi-x-lg"></i></button>
                                        
                                        <div class="w-full flex-1 overflow-auto flex items-center justify-center">
                                            @if(str_starts_with($item->file_type, 'image/'))
                                                <img src="{{ $item->publicUrl() }}" class="max-w-full h-auto rounded-lg shadow-sm border border-gray-200 object-contain mx-auto" style="max-height: 60vh;">
                                            @else
                                                <video controls class="w-full rounded-lg shadow-sm border border-gray-200 bg-black" style="max-height: 60vh;">
                                                    <source src="{{ $item->publicUrl() }}" type="{{ $item->file_type }}">
                                                </video>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-xl w-full text-xs text-gray-600 text-left flex flex-col gap-1.5 shrink-0">
                                            <div><span class="font-bold">Đường dẫn:</span> <code class="bg-white px-2 py-1 border border-gray-100 rounded text-pink-600 font-medium select-all">{{ $item->file_path }}</code></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
        
                            <!-- Edit Modal Alpine -->
                            <template x-teleport="body">
                                <div x-show="editOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @click.self="editOpen = false">
                                    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
                                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                            <h3 class="font-bold text-gray-800 text-lg">Cập nhật quyền</h3>
                                            <button type="button" @click="editOpen = false" class="text-gray-400 hover:text-gray-600"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <form action="{{ route('admin.media.update-share', $item) }}" method="POST" class="p-6">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="space-y-4 mb-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chế độ chia sẻ</label>
                                                    <select name="share_type" x-model="editShareType" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all cursor-pointer">
                                                        <option value="public">Công khai (Tất cả)</option>
                                                        <option value="private">Riêng tư (Chỉ mình tôi)</option>
                                                        <option value="role">Chia sẻ cho Nhóm quyền</option>
                                                        <option value="user">Chia sẻ cho Cá nhân</option>
                                                    </select>
                                                </div>
        
                                                <!-- Role Selection -->
                                                <div x-show="editShareType === 'role'" x-collapse>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Nhóm quyền</label>
                                                    <select name="shared_role" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-100 transition-all cursor-pointer">
                                                        @foreach($roles as $key => $label)
                                                            <option value="{{ $key }}" {{ $item->shared_role == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
        
                                                <!-- User Selection -->
                                                <div x-show="editShareType === 'user'" x-collapse>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn User cụ thể</label>
                                                    <select name="shared_user_id" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-100 transition-all cursor-pointer">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}" {{ $item->shared_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
        
                                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                                <button type="button" @click="editOpen = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">Huỷ</button>
                                                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-200">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>

                            <!-- Delete Modal Alpine -->
                            <template x-teleport="body">
                                <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     @click.self="deleteOpen = false">
                                    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl p-6 text-center overflow-hidden">
                                        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-5 relative">
                                            <div class="absolute inset-0 rounded-full bg-red-100 animate-ping opacity-20"></div>
                                            <i class="bi bi-exclamation-triangle-fill text-3xl text-red-500"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Xác nhận xoá file</h3>
                                        <p class="text-sm text-gray-500 mb-6 leading-relaxed">Bạn có chắc muốn xoá file này vĩnh viễn không? Hành động này có thể làm <span class="text-red-500 font-medium">lỗi ảnh trong các bài viết</span> đang sử dụng.</p>
                                        
                                        <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="flex justify-center gap-3 w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="deleteOpen = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all focus:ring-2 focus:ring-gray-200 focus:outline-none w-1/2">Huỷ bỏ</button>
                                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 bg-gradient-to-r from-red-500 to-red-600 rounded-xl transition-all shadow-sm shadow-red-200 focus:ring-2 focus:ring-red-200 focus:outline-none w-1/2">Có, xoá ngay</button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="bi bi-images text-4xl text-gray-300"></i>
                                <p>Chưa có media nào. Hãy tải lên ngay!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-50">
            {{ $media->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    const previewTemplate = `
<div class="dz-preview dz-file-preview flex flex-col p-4 bg-white border border-gray-200 rounded-2xl mb-3 shadow-sm relative overflow-hidden group">
    <div class="flex items-center gap-4 mb-3">
        <div class="w-12 h-12 shrink-0 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-100">
            <img data-dz-thumbnail class="w-full h-full object-cover" />
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start mb-1">
                <div class="font-medium text-gray-800 text-sm truncate pr-2" data-dz-name></div>
                <div class="text-xs font-mono text-gray-500 whitespace-nowrap" data-dz-size></div>
            </div>
            <div class="text-xs text-gray-500 flex items-center gap-2 dz-status-text">
                <span class="dz-upload-status text-blue-600 font-medium">Đang tải lên...</span>
                <span class="dz-upload-percentage text-gray-400">0%</span>
            </div>
            <div class="dz-error-message text-red-500 text-xs mt-1 hidden" data-dz-errormessage></div>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        <!-- Progress Bar -->
        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200/50">
            <div class="dz-upload h-full bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-300 w-0 relative" data-dz-uploadprogress>
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex items-center gap-1 shrink-0">
            <button type="button" class="dz-btn-pause w-7 h-7 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white transition-colors tooltip" title="Tạm dừng">
                <i class="bi bi-pause-fill text-sm"></i>
            </button>
            <button type="button" class="dz-btn-resume w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors hidden tooltip" title="Tiếp tục">
                <i class="bi bi-play-fill text-sm"></i>
            </button>
            <button type="button" class="dz-btn-cancel w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors tooltip" title="Huỷ tải lên" data-dz-remove>
                <i class="bi bi-x-lg text-xs"></i>
            </button>
        </div>
    </div>
</div>
`;

    // Khởi tạo dropzone
    const myDropzone = new Dropzone("#mediaDropzone", {
        acceptedFiles: "image/*,video/mp4",
        maxFilesize: 20, // MB
        previewTemplate: previewTemplate,
        dictDefaultMessage: "Kéo thả file vào đây để tải lên",
        dictFallbackMessage: "Trình duyệt của bạn không hỗ trợ kéo thả file.",
        dictFileTooBig: "File quá lớn (@{{filesize}}MB). Tối đa: @{{maxFilesize}}MB.",
        dictInvalidFileType: "Không thể upload loại file này.",
        init: function() {
            var dz = this;
            
            this.on("addedfile", function(file) {
                const btnPause = file.previewElement.querySelector('.dz-btn-pause');
                const btnResume = file.previewElement.querySelector('.dz-btn-resume');
                const statusText = file.previewElement.querySelector('.dz-upload-status');
                const progressBar = file.previewElement.querySelector('.dz-upload');
                
                if (btnPause) {
                    btnPause.addEventListener('click', function(e) {
                        e.preventDefault();
                        btnPause.classList.add('hidden');
                        btnResume.classList.remove('hidden');
                        statusText.textContent = "Đã tạm dừng";
                        statusText.className = "dz-upload-status text-yellow-600 font-medium";
                        progressBar.classList.remove('from-blue-400', 'to-blue-600');
                        progressBar.classList.add('from-yellow-400', 'to-yellow-500');
                    });
                }
                
                if (btnResume) {
                    btnResume.addEventListener('click', function(e) {
                        e.preventDefault();
                        btnResume.classList.add('hidden');
                        btnPause.classList.remove('hidden');
                        statusText.textContent = "Đang tải lên...";
                        statusText.className = "dz-upload-status text-blue-600 font-medium";
                        progressBar.classList.remove('from-yellow-400', 'to-yellow-500');
                        progressBar.classList.add('from-blue-400', 'to-blue-600');
                    });
                }
            });

            this.on("uploadprogress", function(file, progress, bytesSent) {
                if (file.previewElement) {
                    const pct = file.previewElement.querySelector('.dz-upload-percentage');
                    if (pct) pct.textContent = Math.round(progress) + '%';
                }
            });

            this.on("error", function(file, errorMessage) {
                if (file.previewElement) {
                    const statusText = file.previewElement.querySelector('.dz-upload-status');
                    const progressBar = file.previewElement.querySelector('.dz-upload');
                    const errEl = file.previewElement.querySelector('.dz-error-message');
                    
                    if (statusText) {
                        statusText.textContent = "Lỗi tải lên!";
                        statusText.className = "dz-upload-status text-red-600 font-medium";
                    }
                    if (progressBar) {
                        progressBar.classList.remove('from-blue-400', 'to-blue-600');
                        progressBar.classList.add('from-red-400', 'to-red-500');
                    }
                    if (errEl) {
                        errEl.textContent = errorMessage;
                        errEl.classList.remove('hidden');
                    }
                }
            });

            this.on("sending", function(file, xhr, formData) {
                // Đính kèm các cấu hình Share vào form request
                var shareType = document.getElementById('shareTypeSelect') ? document.getElementById('shareTypeSelect').value : 'public';
                var sharedRole = document.getElementById('sharedRoleSelect') ? document.getElementById('sharedRoleSelect').value : '';
                var sharedUserId = document.getElementById('sharedUserSelect') ? document.getElementById('sharedUserSelect').value : '';
                
                formData.append("share_type", shareType);
                if (shareType === 'role') formData.append("shared_role", sharedRole);
                if (shareType === 'user') formData.append("shared_user_id", sharedUserId);
            });

            this.on("success", function(file, response) {
                if (file.previewElement) {
                    const statusText = file.previewElement.querySelector('.dz-upload-status');
                    const progressBar = file.previewElement.querySelector('.dz-upload');
                    const controls = file.previewElement.querySelectorAll('.dz-btn-pause, .dz-btn-resume, .dz-btn-cancel');
                    
                    if (statusText) {
                        statusText.textContent = "Tải lên thành công!";
                        statusText.className = "dz-upload-status text-emerald-600 font-medium";
                    }
                    if (progressBar) {
                        progressBar.classList.remove('from-blue-400', 'to-blue-600');
                        progressBar.classList.add('from-emerald-400', 'to-emerald-500');
                        progressBar.style.width = "100%";
                    }
                    
                    controls.forEach(b => b.classList.add('hidden'));
                }
                setTimeout(() => {
                    this.removeFile(file);
                }, 2000);
            });
            
            this.on("queuecomplete", function() {
                // Đợi 2.2s sau khi hiển thị báo cáo thành công thì hãy reload
                setTimeout(() => {
                    window.location.reload(); 
                }, 2200);
            });
        }
    });

    // Lắng nghe sự kiện Paste (Ctrl+V) trên toàn trang
    document.addEventListener('paste', function(e) {
        if (e.clipboardData && e.clipboardData.files.length > 0) {
            const files = e.clipboardData.files;
            
            // Hiện panel nếu đang đóng (optional)
            // Lấy component Alpine của upload wrapper nếu cần
            
            for (let i = 0; i < files.length; i++) {
                if(files[i].type.startsWith('image/') || files[i].type.startsWith('video/')) {
                    // Thêm trực tiếp file vào Dropzone Queue, nó sẽ kích hoạt Upload
                    myDropzone.addFile(files[i]);
                }
            }
        }
    });
</script>
@endsection
