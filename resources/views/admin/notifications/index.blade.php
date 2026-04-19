@extends('layouts.admin')
@section('title', 'Quản lý Thông báo')

@section('content')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 tracking-tight transition-colors"><i class="bi bi-bell-fill text-emerald-500 me-2"></i>Quản lý Thông báo</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1 transition-colors">Tạo và gửi thông báo hệ thống đến người dùng</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-[0_4px_14px_0_rgb(5,150,105,39%)]">
            <i class="bi bi-plus-lg"></i> Tạo thông báo mới
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-400 rounded-xl flex items-center gap-3 animate-fade-in-up shadow-sm transition-colors">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-800 dark:text-red-400 rounded-xl flex items-center gap-3 animate-fade-in-up shadow-sm transition-colors">
            <i class="bi bi-x-circle-fill text-xl text-red-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden mb-6 transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-zinc-950/40 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-semibold text-[11px] tracking-wider uppercase transition-colors">
                    <tr>
                        <th class="px-5 py-4 w-16 text-center">#</th>
                        <th class="px-4 py-4 min-w-[200px]">Tiêu đề</th>
                        <th class="px-4 py-4">Đối tượng</th>
                        <th class="px-4 py-4 text-center">Trạng thái</th>
                        <th class="px-4 py-4 text-center">Ngày tạo</th>
                        <th class="px-5 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/80 text-gray-700 dark:text-zinc-300">
                    @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-5 py-3 text-center text-gray-400 dark:text-zinc-500 font-medium transition-colors">{{ $notification->id }}</td>
                        <td class="px-4 py-3 min-w-[200px]">
                            <div class="font-semibold text-gray-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate" title="{{ $notification->title }}">{{ Str::limit($notification->title, 40) }}</div>
                            <div class="text-[11px] text-gray-500 dark:text-zinc-500 mt-1 truncate transition-colors" title="{{ strip_tags($notification->content) }}">{{ Str::limit(strip_tags($notification->content), 50) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                @php
                                    $recipientsList = is_string($notification->recipients) ? json_decode($notification->recipients, true) : $notification->recipients;
                                    $recipientsList = is_array($recipientsList) || is_object($recipientsList) ? $recipientsList : [];
                                @endphp
                                @foreach($recipientsList as $r)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-400 rounded text-[10px] font-medium tracking-wide uppercase transition-colors">{{ $r }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($notification->sent_at)
                                <div class="inline-flex flex-col items-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[11px] font-bold border border-emerald-200/60 dark:border-emerald-500/20 shadow-sm transition-colors"><i class="bi bi-check-circle-fill"></i> Đã gửi</span>
                                    <span class="text-[10px] text-gray-500 dark:text-zinc-500 mt-1 transition-colors">{{ $notification->sent_at->format('d/m/y H:i') }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 text-[11px] font-bold border border-orange-200/60 dark:border-orange-500/20 shadow-sm transition-colors"><i class="bi bi-clock-history"></i> Mới tạo (Nháp)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-zinc-400 transition-colors">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <!-- Chỉnh sửa -->
                                <a href="{{ route('admin.notifications.edit', $notification) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 dark:hover:text-white transition-colors shadow-sm" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Gửi -->
                                <form action="{{ route('admin.notifications.send', $notification) }}" method="POST" class="m-0" onsubmit="window.confirmAction(event, this, '{{ $notification->sent_at ? 'Bạn có chắc muốn GỬI LẠI thông báo này vào tất cả email của người nhận?' : 'Gửi thông báo «' . addslashes($notification->title) . '» ngay bây giờ?' }}')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg shadow-sm transition-colors {{ $notification->sent_at ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 dark:hover:text-white' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white' }}" title="{{ $notification->sent_at ? 'Gửi lại (Resend)' : 'Gửi thông báo ngay' }}">
                                        <i class="bi {{ $notification->sent_at ? 'bi-arrow-repeat font-bold cursor-pointer' : 'bi-send-fill cursor-pointer' }}"></i>
                                    </button>
                                </form>

                                <!-- Xoá -->
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="m-0" onsubmit="window.confirmAction(event, this, 'Bạn có chắc muốn xoá thông báo này không? Việc này không thể phục hồi.')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white dark:hover:bg-red-500 dark:hover:text-white transition-colors shadow-sm" title="Xoá">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500 dark:text-zinc-400 transition-colors border-b border-transparent">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="bi bi-bell-slash text-4xl text-gray-300 dark:text-zinc-700 transition-colors"></i>
                                <p>Chưa có thông báo nào. <a href="{{ route('admin.notifications.create') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium transition-colors">Tạo ngay</a></p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800/80 bg-gray-50/50 dark:bg-zinc-950/40 transition-colors">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
