@extends('layouts.admin')
@section('title', 'Quản lý chuyên mục')
@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            @if(isset($parentCat))
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ route('admin.categories.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors shadow-sm" title="Quay lại danh mục chính">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Chi nhánh con: <span class="text-emerald-600">{{ $parentCat->name }}</span></h3>
                </div>
            @else
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Quản lý chuyên mục</h2>
                <p class="text-sm text-slate-500 mt-1">Sắp xếp, thêm mới và quản lý cấu trúc website</p>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}{{ request('parent_id') ? '?parent_id='.request('parent_id') : '' }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg text-lg"></i>
                Thêm chuyên mục
            </a>
        </div>
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
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 mb-6 transition-colors">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row gap-3">
            @if(request('parent_id'))
                <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
            @endif
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400 dark:text-zinc-500 transition-colors"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc slug chuyên mục..." class="w-full !pl-10 pr-4 py-2 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 rounded-xl text-sm focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 outline-none transition-all placeholder:text-zinc-400 dark:placeholder:text-zinc-600">
            </div>
            <div class="w-full sm:w-48 relative">
                <select name="status" class="w-full px-4 py-2 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 text-gray-700 dark:text-zinc-200 rounded-xl text-sm focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">-- Mọi trạng thái --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hiện</option>
                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400 dark:text-zinc-500 transition-colors"><i class="bi bi-chevron-down text-xs"></i></div>
            </div>
            <button type="submit" class="px-6 py-2 bg-zinc-900 dark:bg-zinc-100 hover:bg-black dark:hover:bg-white text-white dark:text-zinc-900 text-sm font-semibold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-zinc-100 w-full sm:w-auto">
                <i class="bi bi-funnel"></i> Lọc
            </button>
            @if(request()->anyFilled(['search', 'status', 'parent_id']))
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold rounded-xl transition-all text-center w-full sm:w-auto">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 mb-6 relative pb-16 transition-colors" 
         x-data="{
            selected: [],
            selectAll: false,
            categories: [{{ $categories->pluck('id')->join(',') }}],
            toggleAll() {
                if(this.selectAll) { this.selected = [...this.categories]; }
                else { this.selected = []; }
            },
            updateOrder(id, val) {
                if(val === '') return;
                axios.post('/admin/categories/' + id + '/update-order', { order: val })
                    .then(res => { /* success handle silently */ })
                    .catch(err => { alert('Lỗi cập nhật thứ tự'); });
            },
            initSortable() {
                if (typeof Sortable === 'undefined') return;
                let el = document.getElementById('sortable-tbody');
                if (!el) return;
                Sortable.create(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'bg-emerald-50',
                    onEnd: function () {
                        let orderIds = [];
                        el.querySelectorAll('tr[data-id]').forEach(row => {
                            orderIds.push(row.getAttribute('data-id'));
                        });
                        axios.post('{{ route('admin.categories.reorder') }}', { orders: orderIds })
                            .then(res => { /* updated */ })
                            .catch(err => { alert('Lỗi sắp xếp!'); });
                    }
                });
            }
         }" x-init="$nextTick(() => initSortable())">
        
        <!-- Bulk Action Floating Bar -->
        <div x-show="selected.length > 0" x-transition.opacity.duration.300ms class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white dark:bg-zinc-900 px-5 py-3 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-200 dark:border-zinc-700 z-40 flex items-center gap-4 transition-colors">
            <span class="text-sm font-bold text-gray-700 dark:text-zinc-200 whitespace-nowrap">Đã chọn <span x-text="selected.length" class="text-emerald-600 dark:text-emerald-400"></span> mục</span>
            <div class="w-px h-5 bg-gray-200 dark:bg-zinc-700"></div>
            <form method="POST" action="{{ route('admin.categories.bulk-action') }}" class="m-0 flex items-center gap-2">
                @csrf
                <template x-for="id in selected">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" name="action" value="show_menu" class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 whitespace-nowrap">
                    <i class="bi bi-eye text-base"></i> Hiện lên Menu
                </button>
                <button type="submit" name="action" value="hide_menu" class="flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-300 dark:border-zinc-600 rounded-xl text-sm font-semibold shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-zinc-700 whitespace-nowrap">
                    <i class="bi bi-eye-slash text-base"></i> Ẩn khỏi Menu
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-t-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-zinc-950/40 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-semibold text-[11px] tracking-wider uppercase transition-colors">
                    <tr>
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="w-4 h-4 text-emerald-600 dark:text-emerald-500 bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 cursor-pointer">
                        </th>
                        <th class="px-3 py-4 w-28 text-center" title="Sắp xếp">Thứ tự</th>
                        <th class="px-5 py-4">Tên chuyên mục</th>
                        <th class="px-4 py-4">Slug</th>
                        <th class="px-4 py-4 text-left whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-4 text-left whitespace-nowrap">Hiển thị Menu</th>
                        <th class="px-5 py-4 text-right whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/80 text-gray-700 dark:text-zinc-300" id="sortable-tbody">
                    @forelse($categories as $category)
                    <tr data-id="{{ $category->id }}" class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/50 transition-colors group" :class="selected.includes({{ $category->id }}) ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : ''">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" x-model="selected" value="{{ $category->id }}" class="w-4 h-4 text-emerald-600 dark:text-emerald-500 bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 cursor-pointer">
                        </td>
                        <td class="px-3 py-3 text-center">
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-1.5">
                                <span class="drag-handle cursor-move opacity-50 hover:opacity-100 transition-opacity" title="Kéo thả để di chuyển">
                                    <i class="bi bi-grip-vertical text-xl text-gray-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 border-none"></i>
                                </span>
                                <input type="number" 
                                       value="{{ $category->order }}" 
                                       @change="updateOrder({{ $category->id }}, $event.target.value)"
                                       onfocus="this.select()"
                                       class="w-12 px-1 py-1 text-center bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800 rounded-lg text-xs font-bold text-gray-700 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-900 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all">
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex flex-col">
                                <strong class="text-gray-900 dark:text-zinc-100 font-bold group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $category->name }}</strong>
                                @if($category->parent)
                                    <span class="inline-flex items-center gap-1 mt-1 w-max px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-zinc-400 transition-colors">
                                        <i class="bi bi-arrow-return-right"></i> {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1 w-max px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide uppercase bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 transition-colors">
                                        Mục gốc
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-zinc-400 font-mono text-xs transition-colors">
                            {{ $category->slug }}
                        </td>
                        <td class="px-4 py-3">
                            <div x-data="{
                                isOn: {{ $category->is_active ? 'true' : 'false' }},
                                toggle() {
                                    let oldState = this.isOn;
                                    this.isOn = !this.isOn; // Optimistic update immediately
                                    
                                    axios.post('{{ route('admin.categories.toggle-active', $category->id) }}')
                                    .then(res => {
                                        if(res.data.success) {
                                            this.isOn = !!res.data.is_active;
                                        } else {
                                            this.isOn = oldState; // Revert
                                        }
                                    })
                                    .catch(err => {
                                        this.isOn = oldState; // Revert
                                        alert('Đã xảy ra lỗi cập nhật!');
                                        console.error(err);
                                    });
                                }
                            }" class="flex justify-start items-center gap-2.5 w-max">
                                <button type="button" @click="toggle()" :class="isOn ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-zinc-700'" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer !rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 dark:focus:ring-offset-zinc-900">
                                    <span class="sr-only">Toggle Status</span>
                                    <span aria-hidden="true" :class="isOn ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block h-4 w-4 transform !rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span x-text="isOn ? 'Đang hiện' : 'Đang tắt'" class="text-[12px] font-bold transition-colors" :class="isOn ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500 dark:text-zinc-500'"></span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div x-data="{
                                isOn: {{ $category->show_in_menu ? 'true' : 'false' }},
                                toggle() {
                                    let oldState = this.isOn;
                                    this.isOn = !this.isOn; // Optimistic update immediately
                                    axios.post('{{ route('admin.categories.toggle-menu', $category->id) }}', {
                                        show_in_menu: this.isOn
                                    })
                                    .then(res => {
                                        if(res.data.success) {
                                            this.isOn = !!res.data.show_in_menu;
                                        } else {
                                            this.isOn = oldState; // Revert
                                        }
                                    })
                                    .catch(err => {
                                        this.isOn = oldState; // Revert
                                        alert('Lỗi!');
                                    });
                                }
                            }" class="flex justify-start items-center gap-2.5 w-max">
                                <button type="button" @click="toggle()" :class="isOn ? 'bg-blue-500' : 'bg-gray-200 dark:bg-zinc-700'" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer !rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-zinc-900" title="Bật/Tắt hiển thị trên navbar">
                                    <span class="sr-only">Toggle Menu</span>
                                    <span aria-hidden="true" :class="isOn ? 'translate-x-4' : 'translate-x-0'" class="pointer-events-none inline-block h-4 w-4 transform !rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                                <span x-text="isOn ? 'Hiển thị Menu' : 'Ẩn khỏi Menu'" class="text-[12px] font-bold transition-colors" :class="isOn ? 'text-blue-700 dark:text-blue-400' : 'text-gray-500 dark:text-zinc-500'"></span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                @if(!$category->parent_id)
                                <a href="{{ route('admin.categories.index', ['parent_id' => $category->id]) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 dark:hover:bg-emerald-500 hover:text-white transition-colors shadow-sm" title="Xem các chuyên mục con trực thuộc">
                                    <i class="bi bi-list-nested"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.categories.edit', $category) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white transition-colors shadow-sm" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="m-0" onsubmit="window.confirmFormSubmit(event, 'Chắc chắn xoá chuyên mục?');">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-500 hover:text-white transition-colors shadow-sm" title="Xóa">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-zinc-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-folder-x text-4xl text-gray-300 dark:text-zinc-600"></i>
                                <p>Chưa có chuyên mục nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-50 dark:border-zinc-800/80 bg-gray-50/50 dark:bg-zinc-950/40 rounded-b-2xl transition-colors">
            {{ $categories->links() }}
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection
