@extends('layouts.admin')
@section('title', 'Quản lý Thông báo')

@section('content')
<div class="p-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight"><i class="bi bi-bell-fill text-emerald-500 me-2"></i>Quản lý Thông báo</h2>
            <p class="text-sm text-gray-500 mt-1">Tạo và gửi thông báo hệ thống đến người dùng</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-plus-lg"></i> Tạo thông báo mới
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-x-circle-fill text-xl text-red-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-5 py-4 w-16 text-center">#</th>
                        <th class="px-4 py-4 min-w-[200px]">Tiêu đề</th>
                        <th class="px-4 py-4">Đối tượng</th>
                        <th class="px-4 py-4 text-center">Trạng thái</th>
                        <th class="px-4 py-4 text-center">Ngày tạo</th>
                        <th class="px-5 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-5 py-3 text-center text-gray-400 font-medium">{{ $notification->id }}</td>
                        <td class="px-4 py-3 min-w-[200px]">
                            <div class="font-semibold text-gray-900 group-hover:text-emerald-700 transition truncate" title="{{ $notification->title }}">{{ Str::limit($notification->title, 40) }}</div>
                            <div class="text-[11px] text-gray-500 mt-1 truncate" title="{{ strip_tags($notification->content) }}">{{ Str::limit(strip_tags($notification->content), 50) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                @foreach($notification->recipients as $r)
                                    <span class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-600 rounded text-[10px] font-medium tracking-wide uppercase">{{ $r }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($notification->sent_at)
                                <div class="inline-flex flex-col items-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 shadow-sm"><i class="bi bi-check-circle-fill"></i> Đã gửi</span>
                                    <span class="text-[10px] text-gray-500 mt-1">{{ $notification->sent_at->format('d/m/y H:i') }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 text-[11px] font-bold border border-orange-200/60 shadow-sm"><i class="bi bi-clock-history"></i> Mới tạo (Nháp)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-medium text-gray-500">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <!-- Chỉnh sửa -->
                                <a href="{{ route('admin.notifications.edit', $notification) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors shadow-sm" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Gửi -->
                                <form action="{{ route('admin.notifications.send', $notification) }}" method="POST" class="m-0" onsubmit="window.confirmAction(event, this, '{{ $notification->sent_at ? 'Bạn có chắc muốn GỬI LẠI thông báo này vào tất cả email của người nhận?' : 'Gửi thông báo «' . addslashes($notification->title) . '» ngay bây giờ?' }}')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg shadow-sm transition-colors {{ $notification->sent_at ? 'bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white' }}" title="{{ $notification->sent_at ? 'Gửi lại (Resend)' : 'Gửi thông báo ngay' }}">
                                        <i class="bi {{ $notification->sent_at ? 'bi-arrow-repeat font-bold cursor-pointer' : 'bi-send-fill cursor-pointer' }}"></i>
                                    </button>
                                </form>

                                <!-- Xoá -->
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="m-0" onsubmit="window.confirmAction(event, this, 'Bạn có chắc muốn xoá thông báo này không? Việc này không thể phục hồi.')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Xoá">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="bi bi-bell-slash text-4xl text-gray-300"></i>
                                <p>Chưa có thông báo nào. <a href="{{ route('admin.notifications.create') }}" class="text-emerald-600 hover:underline font-medium">Tạo ngay</a></p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
