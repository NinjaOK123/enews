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
                <form id="exportForm" action="{{ route('admin.reports.royalty.export') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="unit_name" value="{{ $unitFilter }}">
                    <input type="hidden" id="preview_input" name="preview_mode" value="false">
                    <input type="hidden" name="edited_data" :value="JSON.stringify(posts)">
                </form>

                <!-- Tải lên File Mẫu button removed because generation is now hardcoded correctly -->
                <button type="submit" onclick="document.getElementById('exportForm').target='_blank'; document.getElementById('preview_input').value='true';" form="exportForm"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 border-2 border-emerald-600 dark:border-emerald-500 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-sm font-semibold rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                    <i class="bi bi-eye"></i> Xem Trước (Excel Code)
                </button>
                <button type="submit" onclick="document.getElementById('exportForm').target='_self'; document.getElementById('preview_input').value='false';" form="exportForm"
                   class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#006e2e] dark:bg-emerald-600 hover:bg-[#005c26] dark:hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg shadow-sm focus:ring-2 focus:ring-[#006e2e] dark:focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-all">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Báo Cáo (Excel)
                </button>
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
                        <option value="all" {{ $month == 'all' ? 'selected' : '' }} class="dark:bg-zinc-900">Cả năm</option>
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
            <h3 class="text-4xl font-extrabold text-emerald-900 dark:text-emerald-100 transition-colors"><span x-text="totalPosts"></span> <span class="text-lg font-medium text-emerald-700 dark:text-emerald-400">bài</span></h3>
            <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-2 transition-colors">Trong {{ $month == 'all' ? 'Năm' : 'Tháng ' . $month . ' /' }} {{ $year }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-orange-100 dark:from-amber-900/30 dark:to-amber-800/20 rounded-2xl border border-amber-200 dark:border-amber-800/50 p-6 flex flex-col items-center justify-center text-center shadow-sm transition-colors">
            <span class="text-sm font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 transition-colors">Tổng Quy Đổi (Dự tính)</span>
            <h3 class="text-4xl font-extrabold text-amber-900 dark:text-amber-100 transition-colors"><span x-text="totalAmount.toLocaleString('en-US')"></span> <span class="text-lg font-medium text-amber-700 dark:text-amber-400">VND</span></h3>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-2 transition-colors">Số liệu này sẽ tự động cập nhật nếu bạn sửa bảng bên dưới</p>
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
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Bài viết / Nội dung</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Tác giả / Nghiệp vụ</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 transition-colors">Đơn vị / Nhóm</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 text-right transition-colors">Số tiền (VND)</th>
                        <th class="px-6 py-4 font-semibold border-b border-gray-100 dark:border-zinc-800 text-center transition-colors"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 text-sm transition-colors">
                    <template x-for="(post, index) in posts" :key="index">
                        <tr class="hover:bg-green-50/50 dark:hover:bg-zinc-800/50 transition duration-150 group">
                            <td class="px-6 py-4 text-gray-500 dark:text-zinc-400 transition-colors" x-text="index + 1"></td>
                            <td class="px-6 py-4">
                                <template x-if="post.slug">
                                    <a :href="'/bai-viet/' + post.slug" target="_blank" class="text-[10px] text-gray-400 hover:text-emerald-500 transition block mb-1">
                                        <i class="bi bi-box-arrow-up-right"></i> Xem bài
                                    </a>
                                </template>
                                <div contenteditable="true" @blur="post.title = $event.target.innerText" class="font-bold text-gray-900 dark:text-zinc-200 outline-none focus:ring-2 focus:ring-emerald-500 rounded px-2 -ml-2 min-w-[50px] inline-block bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 py-1 transition-colors w-full" x-text="post.title"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div contenteditable="true" @blur="post.author_name = $event.target.innerText" class="font-medium text-gray-900 dark:text-zinc-200 outline-none focus:ring-2 focus:ring-emerald-500 rounded px-2 -ml-2 min-w-[50px] inline-block bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 py-1 transition-colors w-full" x-text="post.author_name"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div contenteditable="true" @blur="post.unit_name = $event.target.innerText" class="text-xs text-gray-500 dark:text-zinc-400 outline-none focus:ring-2 focus:ring-emerald-500 rounded px-2 -ml-2 min-w-[50px] inline-block bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 py-1 transition-colors w-full mb-1" x-text="post.unit_name"></div>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-zinc-800 text-[10px] text-gray-500 dark:text-zinc-400" x-text="post.rate_group"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div contenteditable="true" 
                                     @blur="let val = $event.target.innerText.replace(/[^0-9]/g, ''); post.total = val; $event.target.innerText = parseInt(val || 0).toLocaleString('en-US')" 
                                     class="font-bold text-emerald-600 dark:text-emerald-400 outline-none focus:ring-2 focus:ring-emerald-500 rounded px-2 -mr-2 min-w-[80px] inline-block bg-white dark:bg-zinc-900 hover:bg-gray-50 dark:hover:bg-zinc-800 py-1 transition-colors" 
                                     x-text="parseInt(post.total || 0).toLocaleString('en-US')"></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="removeRow(index)" class="text-gray-300 hover:text-red-500 transition-colors p-1" title="Xóa dòng này">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr class="bg-gray-50/30 dark:bg-zinc-950/20">
                        <td colspan="6" class="px-6 py-3">
                            <div class="flex gap-2">
                                <button @click="addRow('Tin')" class="text-xs bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition flex items-center gap-1 text-gray-600 dark:text-zinc-300">
                                    <i class="bi bi-plus-circle"></i> Thêm bài viết
                                </button>
                                <button @click="addRow('Ban Biên tập')" class="text-xs bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition flex items-center gap-1 text-gray-600 dark:text-zinc-300">
                                    <i class="bi bi-people"></i> Thêm Ban biên tập
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="posts.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-zinc-500 transition-colors">
                            <i class="bi bi-inbox text-4xl text-gray-300 dark:text-zinc-700 block mb-3 transition-colors"></i>
                            Không có bài viết nào được tính nhuận bút trong khoảng thời gian này.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Upload Template Modal --}}
    <div x-data="{ open: false, fileName: '' }" @open-template-modal.window="open = true; fileName = ''" class="relative z-50">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm z-40"></div>
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div @click.away="open = false" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-zinc-800">
                    <form action="{{ route('admin.reports.royalty.template') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white dark:bg-zinc-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tải lên File Mẫu (.xlsx)</h3>
                            <p class="text-sm text-gray-500 dark:text-zinc-400 mb-6">File mẫu dùng để định dạng Báo cáo Nhuận bút phải có 2 Sheet Khuôn đúc (Tổng hợp và Chi tiết).</p>
                            
                            <div class="flex items-center justify-center w-full min-h-[160px]">
                                <label for="template_file" :class="{'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20': fileName, 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50': !fileName}" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 dark:text-zinc-500 mb-3" x-show="!fileName"></i>
                                        <i class="bi bi-file-earmark-excel text-3xl text-emerald-500 mb-3" x-show="fileName" style="display: none;"></i>
                                        
                                        <p class="mb-2 text-sm text-gray-500 dark:text-zinc-400" x-show="!fileName"><span class="font-semibold text-emerald-600 dark:text-emerald-400">Nhấn để tải lên</span> hoặc kéo thả</p>
                                        <p class="mb-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400 text-center px-4 break-all" x-show="fileName" x-text="fileName" style="display: none;"></p>
                                        
                                        <p class="text-xs text-gray-500 dark:text-zinc-500" x-show="!fileName">Chỉ nhận file .xlsx</p>
                                        <p class="text-xs text-emerald-600/70 dark:text-emerald-500/70" x-show="fileName" style="display: none;">Nhấn để chọn file khác</p>
                                    </div>
                                    <input id="template_file" name="template" type="file" accept=".xlsx" class="hidden" required @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                                </label>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-zinc-950/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100 dark:border-zinc-800">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition">Lưu File Mẫu</button>
                            <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 sm:mt-0 sm:w-auto transition">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('royaltyReport', () => ({
            posts: [],
            
            init() {
                const dbPosts = {!! json_encode($posts->map(function($p) {
                    return [
                        'id' => $p->id,
                        'title' => $p->title,
                        'slug' => $p->slug ?? null,
                        'author_name' => $p->author_name,
                        'unit_name' => $p->unit_name,
                        'category_name' => $p->category_name,
                        'rate_name' => $p->rate_name,
                        'rate_group' => $p->rate_group,
                        'unit_price' => $p->unit_price ?? 0,
                        'multiplier' => $p->multiplier ?? 1,
                        'image_count' => $p->image_count ?? 0,
                        'total' => $p->royalty_total
                    ];
                })) !!};
                
                const bbtItems = {!! json_encode(array_map(function($ed) {
                    return [
                        'title' => 'Nhuận bút điều hành',
                        'author_name' => $ed['name'],
                        'unit_name' => '',
                        'rate_name' => $ed['role'],
                        'rate_group' => 'Ban Biên tập',
                        'total' => $ed['amount']
                    ];
                }, $defaultEditors)) !!};

                this.posts = [...bbtItems, ...dbPosts];
            },

            get totalAmount() {
                return this.posts.reduce((sum, post) => sum + (parseFloat(post.total) || 0), 0);
            },

            get totalPosts() {
                // Đếm số lượng bài viết duy nhất dựa trên slug (không tính Ban biên tập)
                const uniqueSlugs = new Set(
                    this.posts
                        .filter(p => p.slug)
                        .map(p => p.slug)
                );
                return uniqueSlugs.size;
            },

            addRow(group) {
                this.posts.push({
                    title: group === 'Ban Biên tập' ? 'Nhuận bút điều hành mới' : 'Nội dung tin bài mới',
                    author_name: 'Tên người nhận',
                    unit_name: group === 'Ban Biên tập' ? '' : 'Đơn vị',
                    rate_group: group,
                    total: 0
                });
            },

            removeRow(index) {
                if(confirm('Bạn có chắc chắn muốn xóa dòng này?')) {
                    this.posts.splice(index, 1);
                }
            },
            
            get totalAmount() {
                return this.posts.reduce((sum, p) => sum + parseInt(p.total || 0), 0);
            }
        }));
    });
</script>
@endpush
@endsection
