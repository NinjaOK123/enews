@extends('layouts.admin')
@section('title', 'Yêu cầu Cộng tác viên')

@section('content')
<div class="p-6 max-w-full">
    
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending -->
        <div class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl p-6 shadow-sm border border-amber-200/50 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl">⏳</div>
            <div>
                <div class="text-4xl font-black text-amber-900 leading-none mb-1">{{ $counts['pending'] }}</div>
                <div class="text-xs font-bold text-amber-700 uppercase tracking-widest">Chờ duyệt</div>
            </div>
        </div>
        <!-- Approved -->
        <div class="bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl p-6 shadow-sm border border-emerald-200/50 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl">✅</div>
            <div>
                <div class="text-4xl font-black text-emerald-900 leading-none mb-1">{{ $counts['approved'] }}</div>
                <div class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Đã duyệt</div>
            </div>
        </div>
        <!-- Rejected -->
        <div class="bg-gradient-to-br from-red-100 to-red-200 rounded-2xl p-6 shadow-sm border border-red-200/50 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl">❌</div>
            <div>
                <div class="text-4xl font-black text-red-900 leading-none mb-1">{{ $counts['rejected'] }}</div>
                <div class="text-xs font-bold text-red-700 uppercase tracking-widest">Từ chối</div>
            </div>
        </div>
        <!-- Total -->
        <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl p-6 shadow-sm border border-purple-200/50 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl">📋</div>
            <div>
                <div class="text-4xl font-black text-purple-900 leading-none mb-1">{{ $counts['pending'] + $counts['approved'] + $counts['rejected'] }}</div>
                <div class="text-xs font-bold text-purple-700 uppercase tracking-widest">Tổng cộng</div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex flex-wrap items-center gap-3 mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <span class="font-bold text-gray-500 text-sm mr-2"><i class="bi bi-funnel me-1"></i>Lọc theo:</span>
        @foreach([
            'pending'  => ['label' => 'Chờ duyệt', 'icon' => '⏳', 'color' => 'amber'],
            'approved' => ['label' => 'Đã duyệt',  'icon' => '✅', 'color' => 'emerald'],
            'rejected' => ['label' => 'Từ chối',   'icon' => '❌', 'color' => 'red'],
            'all'      => ['label' => 'Tất cả',    'icon' => '📋', 'color' => 'gray'],
        ] as $s => $opt)
        @php
            $isActive = $status === $s;
            $bgClass = $isActive 
                ? "bg-{$opt['color']}-500 text-white shadow-md shadow-{$opt['color']}-200" 
                : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-white hover:border-gray-300";
            $badgeBg = $isActive ? "bg-white text-{$opt['color']}-700" : "bg-gray-200 text-gray-700";
        @endphp
        <a href="{{ route('admin.contributor.index', ['status' => $s]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all {{ $isActive ? 'border-transparent' : 'border-gray-200' }} {{ $bgClass }}">
            <span>{{ $opt['icon'] }}</span>
            <span>{{ $opt['label'] }}</span>
            @if($s !== 'all' && $counts[$s] > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full {{ $badgeBg }}">
                    {{ $counts[$s] }}
                </span>
            @endif
        </a>
        @endforeach
    </div>

    <!-- Flash Message -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between animate-fade-in-up shadow-sm">
        <div class="flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 transition-colors">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                        <th class="py-4 px-6 whitespace-nowrap">Người đăng ký</th>
                        <th class="py-4 px-6 whitespace-nowrap">Thông tin Ngân hàng</th>
                        <th class="py-4 px-6 max-w-[200px]">Ghi chú</th>
                        <th class="py-4 px-6 whitespace-nowrap">Ngày gửi đơn</th>
                        <th class="py-4 px-6 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="py-4 px-6 text-center whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        
                        <!-- User Info -->
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="font-bold text-gray-900 text-[15px] whitespace-nowrap">{{ $req->full_name }}</div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1"><i class="bi bi-person text-gray-400"></i> {{ $req->user->name }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span class="inline-flex items-center gap-1"><i class="bi bi-envelope text-gray-400"></i> {{ $req->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Bank Info -->
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="font-mono font-bold text-gray-900 tracking-wider whitespace-nowrap text-[15px]">
                                    {{ $req->bank_account }}
                                </div>
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100/50">
                                        {{ $req->bank_name }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-600 uppercase">{{ $req->account_holder }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Note -->
                        <td class="py-4 px-6">
                            <div class="text-gray-600 text-sm max-w-[220px] line-clamp-2" title="{{ $req->note }}">
                                {{ $req->note ?? '—' }}
                            </div>
                        </td>
                        
                        <!-- Date -->
                        <td class="py-4 px-6 text-gray-500 whitespace-nowrap">
                            <div class="font-bold text-gray-700">{{ $req->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] mt-0.5"><i class="bi bi-clock me-1 text-gray-400"></i>{{ $req->created_at->format('H:i') }}</div>
                        </td>
                        
                        <!-- Status -->
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 border border-amber-200 shadow-sm text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Chờ duyệt
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 border border-emerald-200 shadow-sm text-emerald-700">
                                    <i class="bi bi-check-circle-fill text-emerald-500"></i> Đã duyệt
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 border border-red-200 shadow-sm text-red-700">
                                    <i class="bi bi-x-circle-fill text-red-500"></i> Từ chối
                                </span>
                                @if($req->admin_note)
                                    <div class="text-[11px] text-red-600 mt-2 max-w-[140px] truncate mx-auto font-medium" title="{{ $req->admin_note }}"><i class="bi bi-info-circle me-1"></i>{{ $req->admin_note }}</div>
                                @endif
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="py-4 px-6">
                            @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-2">
                                <!-- Approve (Triggers Modal) -->
                                <button type="button" title="Duyệt yêu cầu"
                                        @click="$dispatch('open-approve', { id: {{ $req->id }}, name: '{{ addslashes($req->full_name) }}' })"
                                        class="w-8 h-8 flex items-center justify-center bg-white text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-200 hover:border-emerald-500 rounded-lg transition-colors shadow-sm">
                                    <i class="bi bi-check-lg text-lg"></i>
                                </button>
                                <!-- Reject (Triggers Modal) -->
                                <button type="button" title="Từ chối"
                                        @click="$dispatch('open-reject', { id: {{ $req->id }}, name: '{{ addslashes($req->full_name) }}' })"
                                        class="w-8 h-8 flex items-center justify-center bg-white text-red-600 hover:bg-red-500 hover:text-white border border-red-200 hover:border-red-500 rounded-lg transition-colors shadow-sm">
                                    <i class="bi bi-x-lg text-lg"></i>
                                </button>
                            </div>
                            @else
                                <div class="text-center text-[11px] text-gray-400 font-medium whitespace-nowrap bg-gray-50 rounded-lg py-1.5 px-2 border border-gray-100">
                                    @if($req->status === 'approved')
                                        Duyệt: {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '—' }}
                                    @else
                                        Từ chối: {{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '—' }}
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="text-6xl mb-4 opacity-50">📭</div>
                                <p class="text-gray-500 font-medium text-sm">Hiện không có yêu cầu ứng tuyển Cộng tác viên nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
    
    <!-- AlpineJS Global Reject Modal -->
    <div x-data="{ isRejectModalOpen: false, reqId: null, reqName: '' }"
         @open-reject.window="isRejectModalOpen = true; reqId = $event.detail.id; reqName = $event.detail.name; $nextTick(() => $refs.note.focus())"
         x-show="isRejectModalOpen"
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm px-4">
        
        <div x-show="isRejectModalOpen" 
             @click.outside="isRejectModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Từ chối Yêu cầu</h3>
                <p class="text-sm text-gray-500 mt-1">Cung cấp lý do từ chối cho ứng viên: <span x-text="reqName" class="font-bold text-red-600"></span></p>
            </div>

            <form :action="'/admin/cong-tac-vien/' + reqId + '/tu-choi'" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lý do từ chối <span class="text-red-500">*</span></label>
                    <textarea x-ref="note" name="admin_note" rows="3" required
                              placeholder="VD: Thông tin ngân hàng không hợp lệ, hoặc không đạt yêu cầu..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-100 focus:border-red-400 outline-none transition-all placeholder:text-gray-400 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click.prevent="isRejectModalOpen = false" 
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">
                        Đóng
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-sm shadow-red-200 transition-colors">
                        Xác nhận từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AlpineJS Global Approve Modal -->
    <div x-data="{ isApproveModalOpen: false, reqId: null, reqName: '' }"
         @open-approve.window="isApproveModalOpen = true; reqId = $event.detail.id; reqName = $event.detail.name;"
         x-show="isApproveModalOpen"
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm px-4">
        
        <div x-show="isApproveModalOpen" 
             @click.outside="isApproveModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            
            <div class="px-6 pb-6 pt-8 border-b border-gray-100 flex flex-col items-center text-center decoration-emerald-100">
                <div class="w-16 h-16 bg-emerald-100/50 text-emerald-500 flex items-center justify-center rounded-full mb-4 shadow-sm border border-emerald-100">
                    <i class="bi bi-check-circle-fill text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Xác nhận Duyệt</h3>
                <p class="text-sm text-gray-500">Bạn có chắc chắn muốn duyệt và cấp quyền Cộng tác viên cho ứng viên <span x-text="reqName" class="font-bold text-gray-800"></span>?</p>
            </div>

            <form :action="'/admin/cong-tac-vien/' + reqId + '/duyet'" method="POST" class="p-6 bg-gray-50/50">
                @csrf
                <div class="flex gap-3">
                    <button type="button" @click.prevent="isApproveModalOpen = false" 
                            class="flex-1 px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 font-bold rounded-xl transition-colors border border-gray-200 shadow-sm">
                        Hủy thoát
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-[0_4px_14px_0_rgb(5,150,105,39%)] hover:shadow-[0_6px_20px_rgba(5,150,105,23%)] transition-all flex justify-center items-center gap-2">
                        <i class="bi bi-check2-circle text-lg"></i> Duyệt ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
