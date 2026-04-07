@extends('layouts.admin')
@section('title', 'Quản lý Bình luận')

@section('content')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                <i class="bi bi-chat-dots text-emerald-600"></i> Quản lý Bình luận
                @if($pendingCount > 0)
                    <span class="inline-flex items-center justify-center px-2 py-0.5 ml-2 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                        {{ $pendingCount }} chờ duyệt
                    </span>
                @endif
            </h2>
            <p class="text-sm text-gray-500 mt-1">Duyệt, ẩn và xoá bình luận từ người dùng hệ thống</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between animate-fade-in-up">
            <div class="flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-xl text-emerald-500"></i> 
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Filter Tabs -->
        <div class="border-b border-gray-100 px-4 pt-3 flex gap-2 overflow-x-auto hide-scrollbar">
            @foreach([
                'pending'  => ['label' => 'Chờ duyệt',  'icon' => 'bi-hourglass-split', 'color' => 'amber'],
                'approved' => ['label' => 'Đã duyệt',   'icon' => 'bi-check-circle',    'color' => 'emerald'],
                'rejected' => ['label' => 'Từ chối',    'icon' => 'bi-x-circle',        'color' => 'red'],
                'all'      => ['label' => 'Tất cả',     'icon' => 'bi-list-ul',         'color' => 'gray'],
            ] as $key => $tab)
            <a href="{{ route('admin.comments.index', ['status' => $key]) }}"
               class="px-4 py-2.5 text-sm font-semibold rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $status === $key 
                   ? 'bg-' . $tab['color'] . '-50 text-' . $tab['color'] . '-700 border-b-2 border-' . $tab['color'] . '-500' 
                   : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-b-2 border-transparent' }}">
                <i class="bi {{ $tab['icon'] }} {{ $status === $key ? '' : 'opacity-70' }}"></i>
                {{ $tab['label'] }}
                @if($key === 'pending' && $pendingCount > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 ml-1 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            @endforeach
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 w-56">Người bình luận</th>
                        <th class="py-3 px-4 min-w-[250px]">Nội dung</th>
                        <th class="py-3 px-4 w-64">Bài viết</th>
                        <th class="py-3 px-4 w-32 text-center">Trạng thái</th>
                        <th class="py-3 px-4 w-28 text-center">Ngày đăng</th>
                        <th class="py-3 px-4 w-32 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($comments as $comment)
                        @php
                            $rowClass = '';
                            if ($comment->is_approved === null) $rowClass = 'bg-amber-50/50 hover:bg-amber-50';
                            elseif ($comment->is_approved === false) $rowClass = 'bg-red-50/50 hover:bg-red-50';
                            else $rowClass = 'hover:bg-gray-50';
                        @endphp
                        <tr class="transition-colors {{ $rowClass }}">
                            <td class="py-3 px-4 text-center text-sm text-gray-400 font-medium">
                                {{ $comment->id }}
                            </td>
                            
                            <!-- User -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $comment->user->name ?? 'Ẩn danh' }}</div>
                                        <div class="text-[11px] text-gray-500 truncate w-40">{{ $comment->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Nội dung -->
                            <td class="py-3 px-4">
                                <p class="text-sm text-gray-700 leading-relaxed max-w-md">
                                    {{ Str::limit($comment->content, 120) }}
                                </p>
                            </td>

                            <!-- Bài viết -->
                            <td class="py-3 px-4">
                                @if($comment->post)
                                    <a href="{{ route('post.show', $comment->post) }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline flex items-start gap-1 group truncate max-w-[200px]" title="{{ $comment->post->title }}">
                                        <i class="bi bi-box-arrow-up-right mt-0.5 opacity-70 group-hover:opacity-100"></i>
                                        <span class="truncate">{{ $comment->post->title }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Bài đã xoá</span>
                                @endif
                            </td>

                            <!-- Trạng thái -->
                            <td class="py-3 px-4 text-center">
                                @if($comment->is_approved === true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <i class="bi bi-check-circle-fill"></i> Đã duyệt
                                    </span>
                                @elseif($comment->is_approved === false)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                        <i class="bi bi-x-circle-fill"></i> Từ chối
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        <i class="bi bi-hourglass-split"></i> Chờ duyệt
                                    </span>
                                @endif
                            </td>

                            <!-- Ngày đăng -->
                            <td class="py-3 px-4 text-center">
                                <div class="text-[13px] text-gray-700 font-medium">{{ $comment->created_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-gray-500">{{ $comment->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Thao tác -->
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($comment->is_approved !== true)
                                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors" title="Duyệt">
                                                <i class="bi bi-check-lg text-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($comment->is_approved !== false)
                                        <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors" title="Từ chối/Ẩn">
                                                <i class="bi bi-x-lg text-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Xoá vĩnh viễn bình luận này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors" title="Xoá">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="bi bi-chat-slash text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm font-medium">
                                        @if($status === 'pending')
                                            Không có bình luận nào đang chờ duyệt. 🎉
                                        @elseif($status === 'approved')
                                            Chưa có bình luận nào được duyệt.
                                        @elseif($status === 'rejected')
                                            Tuyệt vời, không có bình luận nào bị từ chối!
                                        @else
                                            Chưa có bình luận nào trong hệ thống, hoặc bạn chưa chọn bộ lọc.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($comments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
