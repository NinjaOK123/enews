@extends('layouts.admin')
@section('title', 'Yêu cầu Cộng tác viên')

@section('content')
<div class="p-6 max-w-full">
    
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending -->
        <div class="bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/40 dark:to-amber-800/20 rounded-2xl p-6 shadow-sm border border-amber-200/50 dark:border-amber-500/20 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl opacity-90">⏳</div>
            <div>
                <div class="text-4xl font-black text-amber-900 dark:text-amber-400 leading-none mb-1">{{ $counts['pending'] }}</div>
                <div class="text-xs font-bold text-amber-700 dark:text-amber-500/80 uppercase tracking-widest">Chờ duyệt</div>
            </div>
        </div>
        <!-- Approved -->
        <div class="bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/20 rounded-2xl p-6 shadow-sm border border-emerald-200/50 dark:border-emerald-500/20 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl opacity-90">✅</div>
            <div>
                <div class="text-4xl font-black text-emerald-900 dark:text-emerald-400 leading-none mb-1">{{ $counts['approved'] }}</div>
                <div class="text-xs font-bold text-emerald-700 dark:text-emerald-500/80 uppercase tracking-widest">Đã duyệt</div>
            </div>
        </div>
        <!-- Rejected -->
        <div class="bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/40 dark:to-red-800/20 rounded-2xl p-6 shadow-sm border border-red-200/50 dark:border-red-500/20 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl opacity-90">❌</div>
            <div>
                <div class="text-4xl font-black text-red-900 dark:text-red-400 leading-none mb-1">{{ $counts['rejected'] }}</div>
                <div class="text-xs font-bold text-red-700 dark:text-red-500/80 uppercase tracking-widest">Từ chối</div>
            </div>
        </div>
        <!-- Total -->
        <div class="bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/40 dark:to-purple-800/20 rounded-2xl p-6 shadow-sm border border-purple-200/50 dark:border-purple-500/20 flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="text-4xl opacity-90">📋</div>
            <div>
                <div class="text-4xl font-black text-purple-900 dark:text-purple-400 leading-none mb-1">{{ $counts['pending'] + $counts['approved'] + $counts['rejected'] }}</div>
                <div class="text-xs font-bold text-purple-700 dark:text-purple-500/80 uppercase tracking-widest">Tổng cộng</div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex flex-wrap items-center gap-3 mb-8 bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 transition-colors">
        <span class="font-bold text-gray-500 dark:text-zinc-400 text-sm mr-2"><i class="bi bi-funnel me-1"></i>Lọc theo:</span>
        @foreach([
            'pending'  => ['label' => 'Chờ duyệt', 'icon' => '⏳', 'color' => 'amber'],
            'approved' => ['label' => 'Đã duyệt',  'icon' => '✅', 'color' => 'emerald'],
            'rejected' => ['label' => 'Từ chối',   'icon' => '❌', 'color' => 'red'],
            'all'      => ['label' => 'Tất cả',    'icon' => '📋', 'color' => 'gray'],
        ] as $s => $opt)
        @php
            $isActive = $status === $s;
            $bgClass = $isActive 
                ? "bg-{$opt['color']}-500 dark:bg-{$opt['color']}-600 text-white shadow-md shadow-{$opt['color']}-200 dark:shadow-none border-transparent" 
                : "bg-gray-50 dark:bg-zinc-950/50 text-gray-600 dark:text-zinc-400 border-gray-200 dark:border-zinc-700/50 hover:bg-white dark:hover:bg-zinc-800 hover:border-gray-300 dark:hover:border-zinc-600 hover:text-gray-900 dark:hover:text-zinc-200";
            $badgeBg = $isActive ? "bg-white text-{$opt['color']}-700 dark:bg-white/20 dark:text-white" : "bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300";
        @endphp
        <a href="{{ route('admin.contributor.index', ['status' => $s]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all {{ $bgClass }}">
            <span class="opacity-90">{{ $opt['icon'] }}</span>
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
    <div x-data="{ show: true }" x-show="show" class="mb-6 px-5 py-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-400 rounded-xl flex items-center justify-between animate-fade-in-up shadow-sm">
        <div class="flex items-center gap-3">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-emerald-600 dark:text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50 dark:bg-zinc-950/40 border-b border-gray-100 dark:border-zinc-800 text-xs uppercase tracking-wider text-gray-500 dark:text-zinc-400 font-bold transition-colors">
                        <th class="py-4 px-6 whitespace-nowrap">Người đăng ký</th>
                        <th class="py-4 px-6 whitespace-nowrap">Thông tin Ngân hàng</th>
                        <th class="py-4 px-6 max-w-[200px]">Ghi chú</th>
                        <th class="py-4 px-6 whitespace-nowrap">Ngày gửi đơn</th>
                        <th class="py-4 px-6 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="py-4 px-6 text-center whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/80 text-sm">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/50 transition-colors group">
                        
                        <!-- User Info -->
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="font-bold text-gray-900 dark:text-zinc-100 text-[15px] whitespace-nowrap transition-colors">{{ $req->full_name }}</div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-zinc-400 whitespace-nowrap transition-colors">
                                    <span class="inline-flex items-center gap-1"><i class="bi bi-person text-gray-400 dark:text-zinc-500"></i> {{ $req->user->name }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-zinc-700"></span>
                                    <span class="inline-flex items-center gap-1"><i class="bi bi-envelope text-gray-400 dark:text-zinc-500"></i> {{ $req->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Bank Info -->
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="font-mono font-bold text-gray-900 dark:text-zinc-100 tracking-wider whitespace-nowrap text-[15px] transition-colors">
                                    {{ $req->bank_account }}
                                </div>
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100/50 dark:border-blue-500/20 transition-colors">
                                        {{ $req->bank_name }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-600 dark:text-zinc-400 uppercase transition-colors">{{ $req->account_holder }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Note -->
                        <td class="py-4 px-6">
                            <div class="text-gray-600 dark:text-zinc-400 text-sm max-w-[220px] line-clamp-2 transition-colors" title="{{ $req->note }}">
                                {{ $req->note ?? '—' }}
                            </div>
                        </td>
                        
                        <!-- Date -->
                        <td class="py-4 px-6 text-gray-500 dark:text-zinc-400 whitespace-nowrap transition-colors">
                            <div class="font-bold text-gray-700 dark:text-zinc-300">{{ $req->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] mt-0.5"><i class="bi bi-clock me-1 text-gray-400 dark:text-zinc-500"></i>{{ $req->created_at->format('H:i') }}</div>
                        </td>
                        
                        <!-- Status -->
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 shadow-sm text-amber-700 dark:text-amber-400 transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Chờ duyệt
                                </span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 shadow-sm text-emerald-700 dark:text-emerald-400 transition-colors">
                                    <i class="bi bi-check-circle-fill text-emerald-500 text-[10px]"></i> Đã duyệt
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 shadow-sm text-red-700 dark:text-red-400 transition-colors">
                                    <i class="bi bi-x-circle-fill text-red-500 text-[10px]"></i> Từ chối
                                </span>
                                @if($req->admin_note)
                                    <div class="text-[11px] text-red-600 dark:text-red-400 mt-2 max-w-[140px] truncate mx-auto font-medium transition-colors" title="{{ $req->admin_note }}"><i class="bi bi-info-circle me-1"></i>{{ $req->admin_note }}</div>
                                @endif
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="py-4 px-6">
                            @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                <!-- Approve (Triggers Modal) -->
                                <button type="button" title="Duyệt yêu cầu"
                                        @click="$dispatch('open-approve', { id: {{ $req->id }}, name: '{{ addslashes($req->full_name) }}' })"
                                        class="w-8 h-8 flex items-center justify-center bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white rounded-lg transition-colors shadow-sm">
                                    <i class="bi bi-check-lg text-lg"></i>
                                </button>
                                <!-- Reject (Triggers Modal) -->
                                <button type="button" title="Từ chối"
                                        @click="$dispatch('open-reject', { id: {{ $req->id }}, name: '{{ addslashes($req->full_name) }}' })"
                                        class="w-8 h-8 flex items-center justify-center bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white rounded-lg transition-colors shadow-sm">
                                    <i class="bi bi-x-lg text-lg"></i>
                                </button>
                            </div>
                            @else
                                <div class="text-center text-[11px] text-gray-400 dark:text-zinc-500 font-medium whitespace-nowrap bg-gray-50 dark:bg-zinc-800/80 rounded-lg py-1.5 px-2 border border-gray-100 dark:border-zinc-700/50 transition-colors">
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
                        <td colspan="6" class="py-16 text-center border-b border-transparent">
                            <div class="flex flex-col items-center justify-center">
                                <div class="text-6xl mb-4 opacity-50 grayscale dark:opacity-20">📭</div>
                                <p class="text-gray-500 dark:text-zinc-400 font-medium text-sm transition-colors">Hiện không có yêu cầu ứng tuyển Cộng tác viên nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800/80 bg-gray-50/50 dark:bg-zinc-950/40 transition-colors">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
    
    <!-- AlpineJS Global Reject Modal -->
    <div x-data="{ isRejectModalOpen: false, reqId: null, reqName: '' }"
         @open-reject.window="isRejectModalOpen = true; reqId = $event.detail.id; reqName = $event.detail.name; $nextTick(() => $refs.note.focus())"
         x-show="isRejectModalOpen"
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 dark:bg-zinc-900/80 backdrop-blur-sm px-4 transition-colors">
        
        <div x-show="isRejectModalOpen" 
             @click.outside="isRejectModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-zinc-800 transition-colors">
            
            <div class="p-6 border-b border-gray-100 dark:border-zinc-800/80 min-h-[80px]">
                 <button type="button" @click="isRejectModalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-zinc-300 focus:outline-none">
                     <i class="bi bi-x-lg text-xl"></i>
                 </button>
                <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100">Từ chối Yêu cầu</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">Cung cấp lý do từ chối cho ứng viên: <span x-text="reqName" class="font-bold text-red-600 dark:text-red-400"></span></p>
            </div>

            <form :action="'/admin/cong-tac-vien/' + reqId + '/tu-choi'" method="POST" class="p-6 bg-white dark:bg-zinc-900 z-10">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-200 mb-2">Lý do từ chối <span class="text-red-500">*</span></label>
                    <textarea x-ref="note" name="admin_note" rows="3" required
                              placeholder="VD: Thông tin ngân hàng không hợp lệ, hoặc không đạt yêu cầu..."
                              class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-800 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-red-100 dark:focus:ring-red-500/20 focus:border-red-400 dark:focus:border-red-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-zinc-600 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click.prevent="isRejectModalOpen = false" 
                            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-bold rounded-xl transition-colors">
                        Đóng
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white font-bold rounded-xl shadow-[0_4px_14px_0_rgb(220,38,38,39%)] hover:shadow-[0_6px_20px_rgba(220,38,38,23%)] transition-colors">
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
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 dark:bg-zinc-900/80 backdrop-blur-sm px-4 transition-colors">
        
        <div x-show="isApproveModalOpen" 
             @click.outside="isApproveModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-zinc-800 transition-colors">
            
            <div class="px-6 pb-6 pt-8 border-b border-gray-100 dark:border-zinc-800 flex flex-col items-center text-center">
                <button type="button" @click="isApproveModalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-zinc-300 focus:outline-none">
                     <i class="bi bi-x-lg text-xl"></i>
                 </button>
                <div class="w-16 h-16 bg-emerald-100/50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center rounded-full mb-4 shadow-sm border border-emerald-100 dark:border-emerald-500/20">
                    <i class="bi bi-check-circle-fill text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-zinc-100 mb-1">Xác nhận Duyệt</h3>
                <p class="text-sm text-gray-500 dark:text-zinc-400 mt-2">Bạn có chắc chắn muốn duyệt và cấp quyền Cộng tác viên cho ứng viên <span x-text="reqName" class="font-bold text-gray-800 dark:text-zinc-200"></span>?</p>
            </div>

            <form :action="'/admin/cong-tac-vien/' + reqId + '/duyet'" method="POST" class="p-6 bg-gray-50/50 dark:bg-zinc-950/40 z-10 transition-colors">
                @csrf
                <div class="flex gap-3">
                    <button type="button" @click.prevent="isApproveModalOpen = false" 
                            class="flex-1 px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 font-bold rounded-xl transition-colors border border-gray-200 dark:border-zinc-700 shadow-sm">
                        Hủy thoát
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white font-bold rounded-xl shadow-[0_4px_14px_0_rgb(5,150,105,39%)] hover:shadow-[0_6px_20px_rgba(5,150,105,23%)] transition-all flex justify-center items-center gap-2">
                        <i class="bi bi-check2-circle text-lg"></i> Duyệt ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
