@extends('layouts.admin')

@section('title', 'Báo Cáo Nhuận Bút')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="royaltyReport()">
    {{-- Header --}}
    <div class="card bg-white shadow-sm border border-gray-100 rounded-2xl mb-6">
        <div class="card-body p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold font-heading text-emerald-800 flex items-center gap-2">
                    <i class="bi bi-wallet2 text-3xl"></i> Báo Cáo Nhuận Bút
                </h2>
                <p class="text-gray-500 mt-1">Xuất bảng kê thanh toán nhuận bút hàng tháng theo Quyết định chuẩn.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.royalty.export', request()->all()) }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#006e2e] hover:bg-[#005c26] text-white text-sm font-semibold rounded-lg shadow-sm focus:ring-2 focus:ring-[#006e2e] focus:ring-offset-2 transition-all">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Báo Cáo (Excel)
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-white shadow-sm border border-gray-100 rounded-2xl mb-8">
        <div class="card-body p-6">
            <form method="GET" action="{{ route('admin.reports.royalty.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tháng</label>
                    <select name="month" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 bg-white transition text-sm">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Năm</label>
                    <select name="year" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 bg-white transition text-sm">
                        @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lọc theo Đơn vị</label>
                    <select name="unit_name" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 bg-white transition text-sm">
                        <option value="">-- Tất cả đơn vị --</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ $unitFilter === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium px-4 py-2.5 rounded-xl transition duration-200 text-center text-sm shadow">
                        <i class="bi bi-funnel"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-2xl border border-emerald-200 p-6 flex flex-col items-center justify-center text-center shadow-sm">
            <span class="text-sm font-bold text-emerald-700 uppercase tracking-wider mb-2">Bài viết Tính Nhuận Bút</span>
            <h3 class="text-4xl font-extrabold text-emerald-900">{{ $posts->count() }} <span class="text-lg font-medium text-emerald-700">bài</span></h3>
            <p class="text-xs text-emerald-600 mt-2">Trong Tháng {{ $month }} / {{ $year }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-2xl border border-amber-200 p-6 flex flex-col items-center justify-center text-center shadow-sm">
            <span class="text-sm font-bold text-amber-700 uppercase tracking-wider mb-2">Tổng Quy Đổi (Dự tính)</span>
            <h3 class="text-4xl font-extrabold text-amber-900">{{ number_format($totalRoyalty) }} <span class="text-lg font-medium text-amber-700">VND</span></h3>
            <p class="text-xs text-amber-600 mt-2">Đây là số liệu tổng, chi tiết xem trong Excel</p>
        </div>
    </div>

    {{-- Data Table Preview --}}
    <div class="card bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <div class="card-header bg-gray-50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-gray-800">Bản xem trước Danh sách</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold border-b border-gray-100">STT</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100">Bài viết</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100">Tác giả (Đơn vị)</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100">Thể loại</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 text-right">Tổng (VND)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($posts as $idx => $post)
                        <tr class="hover:bg-green-50/50 transition duration-150">
                            <td class="px-6 py-4 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="font-bold text-gray-900 hover:text-emerald-600 transition">{{ $post->title }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $post->source_author ?: ($post->author->name ?? 'N/A') }}</div>
                                <div class="text-xs text-gray-500">{{ $post->author->unit_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-medium inline-block">
                                    {{ $post->royaltyRate->name ?? 'N/A' }}
                                </span>
                                @if($post->image_count > 0)
                                    <div class="text-xs text-gray-500 mt-1"><i class="bi bi-images"></i> +{{ $post->image_count }} ảnh</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                {{ number_format($post->royalty_total) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="bi bi-inbox text-4xl text-gray-300 block mb-3"></i>
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
