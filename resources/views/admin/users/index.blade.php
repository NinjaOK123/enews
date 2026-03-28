@extends('layouts.admin')
@section('title', 'Quản lý người dùng')
@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="{ showBulkConfirm: false, selectedActionText: '', showDeleteConfirm: false, deleteUrl: '' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Quản lý người dùng</h3>
        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-person-plus-fill text-lg"></i> Thêm người dùng
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-exclamation-circle-fill text-xl text-red-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email..." class="w-full !pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            </div>
            <div class="w-full sm:w-48 relative">
                <select name="role" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">Tất cả chức danh</option>
                    <option value="reader" {{ request('role') == 'reader' ? 'selected' : '' }}>Người đọc</option>
                    <option value="contributor" {{ request('role') == 'contributor' ? 'selected' : '' }}>Cộng tác viên</option>
                    <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Biên tập viên</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-xs"></i></div>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 w-full sm:w-auto">
                <i class="bi bi-funnel"></i> Lọc dữ liệu
            </button>
            @if(request()->anyFilled(['search', 'role']))
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all text-center w-full sm:w-auto">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Bulk Action & Table Form -->
    <form method="POST" action="{{ route('admin.users.bulk') }}" id="bulkForm">
        @csrf
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-4 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <span class="text-sm text-gray-600 font-medium pl-2"><i class="bi bi-ui-checks"></i> Chọn nhiều:</span>
                <select name="bulk_action" id="bulkActionSelect" class="flex-1 sm:flex-none px-4 py-2 border border-gray-200 rounded-xl text-sm outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 min-w-[200px] cursor-pointer" required>
                    <option value="">-- Chọn thao tác hàng loạt --</option>
                    <option value="upgrade_contributor" {{ request('bulk_action') == 'upgrade_contributor' ? 'selected' : '' }}>🚀 Nâng hạng lên Cộng tác viên</option>
                    <option value="downgrade_reader">Ngưng cấp quyền (Về Người đọc)</option>
                    <option value="lock_account">🔒 Khoá tài khoản</option>
                    <option value="unlock_account">🔓 Mở khoá tài khoản</option>
                    <option value="delete">🗑 Xoá tài khoản</option>
                </select>
            </div>
            <button type="button" @click="
                let actionSelect = document.getElementById('bulkActionSelect');
                let checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                if(checkedCount === 0) {
                    alert('Vui lòng chọn ít nhất 1 người dùng!');
                } else if(actionSelect.value === '') {
                    alert('Vui lòng chọn thao tác hàng loạt!');
                } else {
                    selectedActionText = actionSelect.options[actionSelect.selectedIndex].text;
                    showBulkConfirm = true;
                }
            " class="w-full sm:w-auto px-6 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-sm transition">
                Áp dụng thao tác
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-4 py-3 pl-6 w-10">
                            <input type="checkbox" id="selectAllCheckbox" onchange="document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(c => c.checked = this.checked)" class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3">Người dùng</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3 text-center">Vai trò</th>
                        <th class="px-4 py-3 text-center">Trạng thái</th>
                        <th class="px-4 py-3 pr-6 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-4 py-3 pl-6">
                            @if(auth()->id() !== $user->id)
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                            @else
                            <input type="checkbox" disabled class="w-4 h-4 text-gray-300 bg-gray-100 border-gray-200 rounded cursor-not-allowed" title="Bạn không thể thao tác trên chính mình">
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap min-w-[220px]">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-100 to-emerald-200 text-emerald-800 font-bold flex items-center justify-center text-sm shadow-sm shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 leading-snug group-hover:text-green-700 transition-colors truncate">{{ $user->name }}</p>
                                    @if($user->username)
                                    <p class="text-[11px] font-medium text-gray-500 truncate mt-0.5"><i class="bi bi-person-badge text-gray-400 mr-1"></i>{{ $user->username }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-600 text-sm">
                            <i class="bi bi-envelope-at text-gray-400 mr-1.5"></i>{{ $user->email }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @php
                                $roleColorClasses = match($user->role) {
                                    'admin'       => 'bg-purple-50 text-purple-700 border-purple-200/60',
                                    'editor'      => 'bg-blue-50 text-blue-700 border-blue-200/60',
                                    'contributor' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                                    default       => 'bg-gray-50 text-gray-600 border-gray-200/60',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $roleColorClasses }} text-[11px] font-bold border shadow-sm">
                                <i class="bi bi-person-fill"></i> {{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-[11px] font-bold border border-green-200/60 shadow-sm"><i class="bi bi-check-circle-fill"></i> HĐộng</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-[11px] font-bold border border-red-200/60 shadow-sm"><i class="bi bi-lock-fill"></i> Khoá</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 pr-6 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.users.edit', $user) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-colors shadow-sm" title="Sửa thông tin">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <button type="button" @click="deleteUrl = '{{ route('admin.users.destroy', $user) }}'; showDeleteConfirm = true;" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Xóa tài khoản">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                @else
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-300 cursor-not-allowed" title="Không thể tự xóa chính mình">
                                    <i class="bi bi-trash3"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="bi bi-people text-4xl text-gray-300"></i>
                                @if(request('status') === 'inactive')
                                    <p>Hiện tại không có tài khoản nào bị khoá.</p>
                                @else
                                    <p>Chưa có người dùng nào được tìm thấy.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    
    <!-- Alpine Modal Xác Nhận -->
    <div x-show="showBulkConfirm" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="showBulkConfirm = false" 
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
             
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-4 text-2xl mx-auto border-4 border-yellow-50">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Xác nhận thao tác</h3>
                <p class="text-gray-600 text-center text-sm leading-relaxed mb-4">
                    Bạn sắp thực hiện thao tác <br> <strong class="text-green-700" x-text="selectedActionText"></strong> <br>lên các tài khoản đã chọn.
                </p>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-xs text-center text-gray-500 font-medium mb-6">
                    <i class="bi bi-info-circle mr-1"></i>Hành động này sẽ gửi một thông báo trực tiếp đến họ.
                </div>
                
                <div class="flex gap-3">
                    <button type="button" @click="showBulkConfirm = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="button" onclick="document.getElementById('bulkForm').submit()" class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-sm shadow-green-600/30 transition-colors">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alpine Modal Xác Nhận Xóa Từng User -->
    <div x-show="showDeleteConfirm" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="showDeleteConfirm = false" 
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
             
            <div class="p-6">
                <!-- Icon cảnh báo mức độ cao (Xóa) -->
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4 text-2xl mx-auto border-4 border-red-50">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                
                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Xóa người dùng</h3>
                <p class="text-gray-600 text-center text-sm leading-relaxed mb-4">
                    Bạn có chắc chắn muốn xóa tài khoản này không?
                </p>
                
                <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-xs text-center text-red-700 font-medium mb-6">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i>Hành động này không thể hoàn tác và sẽ xóa toàn bộ dữ liệu liên quan.
                </div>
                
                <form method="POST" :action="deleteUrl" class="m-0">
                    @csrf
                    @method('DELETE')
                    <div class="flex w-full gap-3">
                        <button type="button" @click="showDeleteConfirm = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-sm shadow-red-600/30 transition-colors">
                            Xác nhận xóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('bulkActionSelect');
        select.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;
            
            const urlParams = new URLSearchParams(window.location.search);
            let shouldRedirect = false;

            if (val === 'upgrade_contributor') {
                if (urlParams.get('role') !== 'reader') {
                    urlParams.set('role', 'reader');
                    shouldRedirect = true;
                }
            } 
            else if (val === 'unlock_account') {
                if (urlParams.get('status') !== 'inactive') {
                    urlParams.set('status', 'inactive');
                    urlParams.delete('role'); // xoá bộ lọc chức danh nếu có
                    shouldRedirect = true;
                }
            }
            else if (val === 'lock_account' || val === 'delete') {
                if (urlParams.has('status') || urlParams.has('role')) {
                    urlParams.delete('status');
                    urlParams.delete('role');
                    shouldRedirect = true;
                }
            }

            if (shouldRedirect) {
                urlParams.set('bulk_action', val); // Giữ lại lựa chọn hiện tại
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            }
        });
    });
</script>
@endsection

