@extends('layouts.admin')

@section('title', 'Cộng tác viên Dashboard')

@section('content')
@php 
    $user = auth()->user(); 
@endphp

{{-- 1. PAGE HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white tracking-tight">
            Cổng thông tin Cộng Tác Viên
        </h2>
        <p class="text-[14px] text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
            Xin chào, <strong class="text-zinc-800 dark:text-zinc-200">{{ $user->name ?? 'Cộng Tác Viên' }}</strong> • 
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">Cộng Tác Viên</span> • 
            {{ now()->locale('vi')->isoFormat('dddd, D/M/YYYY') }}
        </p>
    </div>
    
    <div class="flex justify-end gap-2 shrink-0">
        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[13px] font-medium rounded-lg shadow-sm transition-colors outline-none whitespace-nowrap">
            <i class="bi bi-house-door"></i>
            <span>Trang chủ</span>
        </a>
        <a href="{{ route('contributor.posts.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-medium rounded-lg shadow-sm transition-colors outline-none whitespace-nowrap">
            <i class="bi bi-pencil-square"></i>
            <span>Viết bài mới</span>
        </a>
    </div>
</div>

{{-- 2. STATS CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">
    @php
        $cards = [
            ['label'=>'Tổng đã viết','value'=>$stats['total'],'icon'=>'bi-file-earmark-text','bg'=>'bg-blue-500','shadow'=>'shadow-[0_4px_14px_rgba(59,130,246,0.3)]','text'=>'text-blue-50'],
            ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','bg'=>'bg-emerald-500','shadow'=>'shadow-[0_4px_14px_rgba(16,185,129,0.3)]','text'=>'text-emerald-50'],
            ['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','bg'=>'bg-amber-500','shadow'=>'shadow-[0_4px_14px_rgba(245,158,11,0.3)]','text'=>'text-amber-50'],
            ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','bg'=>'bg-zinc-500','shadow'=>'shadow-[0_4px_14px_rgba(113,113,122,0.3)]','text'=>'text-zinc-50'],
            ['label'=>'Tổng lượt xem','value'=>number_format($stats['total_views']),'icon'=>'bi-eye-fill','bg'=>'bg-rose-500','shadow'=>'shadow-[0_4px_14px_rgba(244,63,94,0.3)]','text'=>'text-rose-50'],
        ];
    @endphp

    @foreach($cards as $c)
    <div class="{{ $c['bg'] }} border border-transparent rounded-xl p-5 {{ $c['shadow'] }} transition-all duration-200 text-white group overflow-hidden relative">
        <div class="flex items-center justify-between mb-2 relative z-10">
            <span class="text-[12px] font-semibold {{ $c['text'] }} uppercase tracking-wider flex items-center gap-2">
                <i class="bi {{ $c['icon'] }} text-[14px]"></i> {{ $c['label'] }}
            </span>
        </div>
        <div class="text-[32px] font-bold tracking-tight leading-none relative z-10 mt-3">{{ $c['value'] }}</div>
        
        <!-- Decorative subtle icon -->
        <i class="bi {{ $c['icon'] }} absolute -right-4 -bottom-4 text-[90px] text-white opacity-10 group-hover:scale-110 group-hover:-translate-y-2 transition-transform duration-500"></i>
    </div>
    @endforeach
</div>

{{-- 3. MY POSTS TABLE --}}
<div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm overflow-hidden flex flex-col transition-colors duration-200 mb-8">
    <div class="px-6 py-5 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/20">
        <h3 class="text-[16px] font-bold text-zinc-900 dark:text-white tracking-tight leading-none m-0 flex items-center gap-2">
            <i class="bi bi-card-list text-emerald-600 dark:text-emerald-400"></i> Bài viết của tôi
            <span class="inline-flex items-center justify-center px-2 py-0.5 ml-2 text-xs font-semibold rounded-full bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                {{ $stats['total'] }}
            </span>
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Tiêu đề & Chuyên mục</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-center">Lượt xem</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Cập nhật lúc</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Trạng thái</th>
                    <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-white/10">
                @forelse($myPosts as $p)
                <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                    <td class="px-6 py-4">
                        <a href="{{ route('post.show', $p->slug) }}" class="block font-semibold text-[14px] text-zinc-900 dark:text-zinc-100 leading-tight mb-1 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                            {{ Str::limit($p->title, 80) }}
                        </a>
                        <div class="text-[12px] text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                            <i class="bi bi-folder2-open text-[11px]"></i> {{ $p->category->name ?? 'Chưa phân loại' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 text-[12px] font-medium border border-zinc-200 dark:border-white/5">
                            <i class="bi bi-eye opacity-70"></i> {{ number_format($p->view_count) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[13px] text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock-history opacity-70 border border-zinc-200 dark:border-zinc-700 rounded p-1"></i>
                            <div>
                                <div class="font-medium">{{ $p->updated_at->format('d/m/Y') }}</div>
                                <div class="text-[11px]">{{ $p->updated_at->format('H:i') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusConfig = [
                                'published' => ['label' => 'Đã đăng', 'class' => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20', 'icon' => 'bi-check-circle-fill'],
                                'pending'   => ['label' => 'Chờ duyệt', 'class' => 'bg-amber-50 text-amber-600 border border-amber-200/60 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20', 'icon' => 'bi-stopwatch-fill'],
                                'draft'     => ['label' => 'Nháp', 'class' => 'bg-zinc-100 text-zinc-600 border border-zinc-200/60 dark:bg-zinc-500/10 dark:text-zinc-400 dark:border-zinc-500/20', 'icon' => 'bi-file-earmark-text-fill'],
                                'rejected'  => ['label' => 'Từ chối', 'class' => 'bg-rose-50 text-rose-600 border border-rose-200/60 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20', 'icon' => 'bi-x-circle-fill'],
                            ];
                            $st = $statusConfig[$p->status] ?? $statusConfig['draft'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $st['class'] }} shadow-sm whitespace-nowrap">
                            <i class="bi {{ $st['icon'] }} opacity-80"></i>{{ $st['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            @if(in_array($p->status, ['draft', 'rejected']))
                                <a href="{{ route('contributor.posts.edit', $p) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors border border-transparent dark:border-white/5 active:scale-95" 
                                   title="Sửa">
                                    <i class="bi bi-pencil-square text-[14px]"></i>
                                </a>
                                <form action="{{ route('contributor.posts.destroy', $p) }}" method="POST" class="inline-block" onsubmit="window.confirmFormSubmit(event, 'Bạn có chắc chắn muốn xóa bài viết này không?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 hover:text-rose-600 dark:hover:text-rose-400 transition-colors border border-transparent dark:border-white/5 active:scale-95" 
                                            title="Xóa">
                                        <i class="bi bi-trash text-[14px]"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('post.show', $p->slug) }}" target="_blank" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-blue-500/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors border border-transparent dark:border-white/5 text-[12px] font-semibold active:scale-95">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 mb-4">
                            <i class="bi bi-pencil-square text-3xl"></i>
                        </div>
                        <h4 class="text-[15px] font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Bạn chưa có bài viết nào</h4>
                        <p class="text-[13px] text-zinc-500 dark:text-zinc-400 mb-4">Hãy bắt đầu chia sẻ kiến thức của bạn với cộng đồng.</p>
                        <a href="{{ route('contributor.posts.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 text-white font-medium text-[13px] hover:bg-emerald-700 transition shadow-sm">
                            <i class="bi bi-plus-lg"></i> Viết bài đầu tiên
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($myPosts->hasPages())
    <div class="px-6 py-4 border-t border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900/20">
        {{ $myPosts->links() }}
    </div>
    @endif
</div>
@endsection
