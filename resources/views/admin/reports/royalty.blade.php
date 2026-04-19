@extends('layouts.admin')

@section('title', 'Báo Cáo Nhuận Bút')

@section('content')
<div class="max-w-7xl mx-auto w-full flex flex-col gap-6" x-data="royaltyReport()">
    {{-- Header --}}
    <div class="bg-emerald-700 dark:bg-emerald-900 shadow-sm border border-emerald-800 dark:border-emerald-950 rounded-2xl transition-colors">
        <div class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold font-heading text-white flex items-center gap-2">
                    <i class="bi bi-wallet2 text-3xl text-emerald-300 dark:text-emerald-400"></i> Báo Cáo Nhuận Bút
                </h2>
                <p class="text-emerald-50/90 dark:text-emerald-100/70 mt-1">Xuất bảng kê thanh toán nhuận bút hàng tháng theo Quyết định chuẩn.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.royalty.export', request()->all()) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#006e2e] dark:bg-emerald-600 hover:bg-[#005c26] dark:hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg shadow-sm focus:ring-2 focus:ring-[#006e2e] dark:focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Báo Cáo (Excel)
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-900 shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 rounded-2xl transition-colors">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.reports.royalty.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Tháng</label>
                    <select name="month" class="w-full border border-gray-200 dark:border-zinc-800/80 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 text-gray-900 dark:text-zinc-200 transition text-sm">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }} class="dark:bg-zinc-900">Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Năm</label>
                    <select name="year" class="w-full border border-gray-200 dark:border-zinc-800/80 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 text-gray-900 dark:text-zinc-200 transition text-sm">
                        @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="dark:bg-zinc-900">Năm {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-2 transition-colors">Lọc theo Đơn vị</label>
                    <select name="unit_name" class="w-full border border-gray-200 dark:border-zinc-800/80 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 dark:focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 text-gray-900 dark:text-zinc-200 transition text-sm">
                        <option value="" class="dark:bg-zinc-900">-- Tất cả đơn vị --</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ $unitFilter === $u ? 'selected' : '' }} class="dark:bg-zinc-900">{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gray-800 dark:bg-zinc-100 hover:bg-gray-900 dark:hover:bg-white text-white dark:text-zinc-900 font-medium px-4 py-2.5 rounded-xl transition duration-200 text-center text-sm shadow">
                        <i class="bi bi-funnel"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-emerald-50 to-green-100 dark:from-emerald-900/30 dark:to-emerald-800/20 rounded-2xl border border-emerald-200 dark:border-emerald-800/50 p-6 flex flex-col items-center justify-center text-center shadow-sm transition-colors">
            <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2 transition-colors">Bài viết Tính Nhuận Bút</span>
            <h3 class="text-4xl font-extrabold text-emerald-900 dark:text-emerald-100 transition-colors">{{ $posts->count() }} <span class="text-lg font-medium text-emerald-700 dark:text-emerald-400">bài</span></h3>
            <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-2 transition-colors">Trong Tháng {{ $month }} / {{ $year }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-100 dark:from-amber-900/30 dark:to-amber-800/20 rounded-2xl border border-amber-200 dark:border-amber-800/50 p-6 flex flex-col items-center justify-center text-center shadow-sm transition-colors">
            <span class="text-sm font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 transition-colors">Tổng Quy Đổi (Dự tính)</span>
            <h3 class="text-4xl font-extrabold text-amber-900 dark:text-amber-100 transition-colors">{{ number_format($totalRoyalty) }} <span class="text-lg font-medium text-amber-700 dark:text-amber-400">VND</span></h3>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-2 transition-colors">Đây là số liệu tổng, chi tiết xem trong Excel</p>
        </div>
    </div>

    {{-- Data Table Preview --}}
    <div class="bg-white dark:bg-zinc-900 shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 rounded-2xl overflow-hidden transition-colors">
        <div class="bg-gray-50 dark:bg-zinc-900/50 border-b border-gray-100 dark:border-zinc-800 px-6 py-4 transition-colors">
            <h3 class="text-lg font-bold text-gray-800 dark:text-zinc-100 transition-colors">Bản xem trước Danh sách</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-zinc-950/50 text-gray-500 dark:text-zinc-400 text-xs uppercase tracking-wider transition-colors">
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">STT</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Bài viết</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Tác giả (Đơn vị)</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Thể loại</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 text-right transition-colors">Tổng (VND)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 text-sm transition-colors">
                    @forelse($posts as $idx => $post)
                        <tr class="hover:bg-green-50/50 dark:hover:bg-zinc-800/50 transition duration-150">
                            <td class="px-6 py-4 text-gray-500 dark:text-zinc-400 transition-colors">{{ $idx + 1 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="font-bold text-gray-900 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition">{{ $post->title }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-zinc-200 transition-colors">{{ $post->source_author ?: ($post->author->name ?? 'N/A') }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400 transition-colors">{{ $post->author->unit_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-medium inline-block transition-colors">
                                    {{ $post->royaltyRate->name ?? 'N/A' }}
                                </span>
                                @if($post->image_count > 0)
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1 transition-colors"><i class="bi bi-images"></i> +{{ $post->image_count }} ảnh</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400 transition-colors">
                                {{ number_format($post->royalty_total) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-zinc-500 transition-colors">
                                <i class="bi bi-inbox text-4xl text-gray-300 dark:text-zinc-700 block mb-3 transition-colors"></i>
                                Không có bài viết nào được tính nhuận bút trong tháng này.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
