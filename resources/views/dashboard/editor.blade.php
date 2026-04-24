@extends('layouts.admin')

@section('title', 'Biên tập viên Dashboard')

@section('content')
@php 
    $user = auth()->user(); 
@endphp

{{-- 1. PAGE HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white tracking-tight">
            Cổng thông tin Biên Tập Viên
        </h2>
        <p class="text-[14px] text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
            Xin chào, <strong class="text-zinc-800 dark:text-zinc-200">{{ $user->name ?? 'Biên Tập Viên' }}</strong> • 
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">Biên Tập Viên</span> • 
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
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    @php
        $cards = [
            ['label'=>'Chờ duyệt','value'=>$stats['pending'],'icon'=>'bi-hourglass-split','bg'=>'bg-amber-500','shadow'=>'shadow-[0_4px_14px_rgba(245,158,11,0.3)]','text'=>'text-amber-50'],
            ['label'=>'Đã đăng','value'=>$stats['published'],'icon'=>'bi-check-circle-fill','bg'=>'bg-emerald-500','shadow'=>'shadow-[0_4px_14px_rgba(16,185,129,0.3)]','text'=>'text-emerald-50'],
            ['label'=>'Bản nháp','value'=>$stats['draft'],'icon'=>'bi-file-earmark','bg'=>'bg-zinc-500','shadow'=>'shadow-[0_4px_14px_rgba(113,113,122,0.3)]','text'=>'text-zinc-50'],
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

{{-- 3. MAIN CONTENT GRID --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

    {{-- LEFT: PENDING POSTS TABLE (Takes 2/3 width on large screens) --}}
    <div class="xl:col-span-2 bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm overflow-hidden flex flex-col transition-colors duration-200">
        <div class="px-6 py-5 border-b border-zinc-200 dark:border-white/10 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/20">
            <h3 class="text-[16px] font-bold text-zinc-900 dark:text-white tracking-tight leading-none m-0 flex items-center gap-2">
                <i class="bi bi-hourglass-split text-amber-500"></i> Bài viết chờ duyệt
                <span class="inline-flex items-center justify-center px-2 py-0.5 ml-2 text-xs font-semibold rounded-full bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                    {{ $stats['pending'] }}
                </span>
            </h3>
        </div>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900/50">
                        <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-left w-1/2">Bài viết / Tác giả</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-left">Chuyên mục</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-white/10 flex-col">
                    @forelse($pendingPosts as $p)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4">
                            <a href="{{ route('post.show', $p->slug) }}" class="block font-semibold text-[14px] text-zinc-900 dark:text-zinc-100 leading-tight mb-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                {{ Str::limit($p->title, 70) }}
                            </a>
                            <div class="flex items-center gap-3 text-[12px] text-zinc-500 dark:text-zinc-400">
                                <span class="flex items-center gap-1 opacity-80"><i class="bi bi-person text-[11px]"></i> {{ $p->author->name ?? '-' }}</span>
                                <span class="flex items-center gap-1 opacity-80"><i class="bi bi-clock text-[11px]"></i> {{ $p->created_at->locale('vi')->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-zinc-600 dark:text-zinc-300">
                            {{ $p->category->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right align-middle">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('post.show', $p->slug) }}" target="_blank" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors border border-indigo-100 dark:border-indigo-500/20 text-[12px] font-semibold shadow-sm active:scale-95" title="Xem trước">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                                
                                <form action="{{ route('editor.posts.approve', $p) }}" method="POST" class="inline-block" onsubmit="window.confirmFormSubmit(event, 'Bạn muốn duyệt bài viết này?');">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[12px] font-semibold transition-colors shadow-sm active:scale-95" title="Duyệt bài">
                                        <i class="bi bi-check-lg text-[14px]"></i> Duyệt
                                    </button>
                                </form>
                                
                                <form action="{{ route('editor.posts.reject', $p) }}" method="POST" class="inline-block" onsubmit="window.confirmFormSubmit(event, 'Bạn có chắc chắn từ chối bài này?');">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-[30px] h-[30px] p-0 rounded-lg bg-white dark:bg-zinc-800 border border-rose-200 dark:border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors shadow-sm active:scale-95" title="Từ chối">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 mb-4">
                                <i class="bi bi-check2-all text-3xl"></i>
                            </div>
                            <h4 class="text-[15px] font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Không có bài chờ duyệt!</h4>
                            <p class="text-[13px] text-zinc-500 dark:text-zinc-400">Bạn đã xử lý xong tất cả các bài viết.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pendingPosts->hasPages())
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900/20">
            {{ $pendingPosts->links() }}
        </div>
        @endif
    </div>

    {{-- RIGHT: RECENTLY PUBLISHED (Takes 1/3 width on large screens) --}}
    <div class="bg-white dark:bg-zinc-900/40 dark:backdrop-blur-md border border-zinc-200 dark:border-white/10 rounded-xl shadow-sm flex flex-col overflow-hidden transition-colors duration-200 h-fit">
        <div class="px-6 py-5 border-b border-zinc-200 dark:border-white/10 bg-zinc-50/50 dark:bg-zinc-900/20">
            <h3 class="text-[16px] font-bold text-zinc-900 dark:text-white tracking-tight leading-none m-0 flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500"></i> Bài vừa đăng
            </h3>
        </div>
        
        <div class="p-6 flex flex-col gap-4">
            @forelse($recentPublished as $p)
            <div class="border border-zinc-100 dark:border-white/5 bg-white dark:bg-zinc-900/50 rounded-xl p-4 hover:shadow-md transition-shadow group">
                <a href="{{ route('post.show', $p->slug) }}" class="block font-semibold text-[13px] text-zinc-900 dark:text-zinc-100 leading-snug mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    {{ Str::limit($p->title, 60) }}
                </a>
                <div class="flex justify-between items-center mt-auto">
                    <div class="text-[12px] text-zinc-500 dark:text-zinc-400 flex items-center gap-1 opacity-80">
                        <i class="bi bi-person"></i> {{ $p->author->name ?? 'N/A' }}
                    </div>
                    <div class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold">
                        {{ $p->published_at?->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="py-10 text-center">
                <p class="text-[13px] text-zinc-500 dark:text-zinc-400">Trống. Chưa có bài viết nào được đăng gần đây.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

