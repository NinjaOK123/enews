@extends('layouts.admin')
@section('title', 'Quản lý Banners Cuộc thi')
@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="bannerManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                <i class="bi bi-images text-emerald-500 me-2"></i>Quản lý Banner Cuộc thi
            </h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium mt-1">Sắp xếp, thêm mới và quản lý các hình ảnh slider cuộc thi</p>
        </div>
        <button @click="openModal('add')" type="button" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-semibold shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-zinc-100">
            <i class="bi bi-plus-circle text-lg"></i> Thêm Banner
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-exclamation-circle-fill text-xl text-red-500 shrink-0"></i> 
            <span class="text-sm font-medium">Lỗi: {{ $errors->first() }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 mb-6 relative pb-4 transition-colors">
        <div class="overflow-x-auto rounded-t-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-zinc-950/40 border-b border-gray-100 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-semibold text-[11px] tracking-wider uppercase transition-colors">
                    <tr>
                        <th class="px-3 py-4 w-16 text-center" title="Sắp xếp">STT</th>
                        <th class="px-5 py-4 w-48 text-center">Hình ảnh</th>
                        <th class="px-5 py-4">Tiêu đề & Link</th>
                        <th class="px-4 py-4 text-center whitespace-nowrap w-32">Hiển thị</th>
                        <th class="px-5 py-4 text-right whitespace-nowrap w-24">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/80 text-gray-700 dark:text-zinc-300" id="sortable-banners">
                    @forelse($banners as $banner)
                    <tr data-id="{{ $banner->id }}" class="hover:bg-gray-50/80 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-3 py-3 text-center align-middle">
                            <span class="drag-handle cursor-move opacity-50 hover:opacity-100 transition-opacity" title="Kéo thả để di chuyển">
                                <i class="bi bi-grip-vertical text-xl text-gray-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400"></i>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center align-middle">
                            <div class="w-full h-16 bg-gray-100 dark:bg-zinc-950/50 rounded-lg overflow-hidden border border-gray-200 dark:border-zinc-800/80 flex items-center justify-center transition-colors">
                                @if($banner->image)
                                    <img src="{{ Str::startsWith($banner->image, 'http') ? $banner->image : asset('storage/' . $banner->image) }}" alt="Banner" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="bi bi-images text-gray-400 dark:text-zinc-600 text-2xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 align-middle">
                            <div class="font-semibold text-gray-800 dark:text-zinc-100 text-sm mb-1 transition-colors">{{ $banner->title ?: '(Không có tiêu đề)' }}</div>
                            @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" class="text-xs text-blue-500 hover:underline flex items-center gap-1">
                                    <i class="bi bi-link-45deg"></i> Xem liên kết
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Không có link</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-middle text-center">
                            <div x-data="{
                                isOn: {{ $banner->is_active ? 'true' : 'false' }},
                                toggle() {
                                    let oldState = this.isOn;
                                    this.isOn = !this.isOn; // Optimistic update
                                    axios.post('{{ route('admin.banners.toggle-active', $banner->id) }}')
                                    .then(res => {
                                        if(res.data.success) {
                                            this.isOn = !!res.data.is_active;
                                        } else {
                                            this.isOn = oldState; // Revert
                                        }
                                    })
                                    .catch(err => { 
                                        this.isOn = oldState; // Revert
                                        alert('Lỗi chuyển trạng thái!'); 
                                    });
                                }
                            }">
                                <button type="button" @click="toggle()" :class="isOn ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-zinc-700'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer !rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-500/50 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                    <span class="sr-only">Toggle</span>
                                    <span :class="isOn ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform !rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-3 align-middle text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click="openModal('edit', {{ $banner->toJson() }})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white transition-colors shadow-sm" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="window.confirmFormSubmit(event, 'Bạn có chắc chắn muốn xoá Banner này? Ảnh sẽ bị xoá vĩnh viễn.');" class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-500 hover:text-white transition-colors shadow-sm" title="Xóa">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-zinc-400 border-b border-transparent">
                            Chưa có banner cuộc thi nào. Hãy bấm "Thêm Banner".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <!-- Modal panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-visible rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg">
                
                <div class="absolute top-4 right-4">
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <form :action="formAction" method="POST" enctype="multipart/form-data" @paste.window="handlePaste">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="bg-white dark:bg-zinc-900 px-6 py-6 border-b border-gray-100 dark:border-zinc-800 rounded-t-2xl transition-colors">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                                <i class="bi bi-image text-emerald-600 dark:text-emerald-400 text-lg"></i>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-zinc-100" id="modal-title" x-text="mode === 'add' ? 'Thêm Banner Cuộc thi mới' : 'Cập nhật Banner'"></h3>
                        </div>
                        
                        <div class="space-y-4">
                            
                            <!-- Tiêu đề -->
                            <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-1">Tiêu đề (Không bắt buộc)</label>
                                        <input type="text" name="title" x-model="formData.title" class="w-full px-4 py-2 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-800 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-colors" placeholder="Ví dụ: Cuộc thi viết về Môi trường">
                                    </div>
                                    
                                    <!-- Ảnh Banner -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-2">Hình ảnh Banner <span class="text-gray-400 dark:text-zinc-500 text-xs font-normal ml-1">(Tải ảnh, Ctr+V dán, hoặc dùng Link)</span></label>
                                        
                                        <!-- Khu vực upload & preview -->
                                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-zinc-700 border-dashed rounded-xl overflow-hidden relative bg-gray-50 dark:bg-zinc-950 hover:bg-gray-100 dark:hover:bg-zinc-900/80 transition-colors"
                                            :class="{ 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/10': previewUrl }">
                                            
                                            <div class="space-y-1 text-center" x-show="!previewUrl">
                                                <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 dark:text-zinc-600"></i>
                                                <div class="flex text-sm text-gray-600 dark:text-zinc-400 justify-center gap-1">
                                                    <label for="image_upload" class="relative cursor-pointer bg-white dark:bg-zinc-800 rounded-md font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-1 shadow-sm border border-gray-200 dark:border-zinc-700 transition-colors">
                                                        <span>Chọn file ảnh</span>
                                                        <input id="image_upload" name="image" type="file" accept="image/*" class="sr-only" @change="handleFileChange">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-zinc-500 pt-2"><i class="bi bi-keyboard"></i> Mẹo: Ấn <kbd class="bg-gray-200 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-gray-700 dark:text-zinc-300 font-mono">Ctrl</kbd> + <kbd class="bg-gray-200 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-gray-700 dark:text-zinc-300 font-mono">V</kbd> để dán ảnh trực tiếp.</p>
                                            </div>

                                            <div x-show="previewUrl" class="relative w-full flex flex-col items-center" style="display: none;">
                                                <img :src="previewUrl" class="max-h-32 object-contain rounded border border-gray-200 dark:border-zinc-700 shadow-sm" alt="Preview">
                                                <button type="button" @click="clearFile()" class="mt-3 px-3 py-1.5 bg-white border border-gray-300 !rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 transition-colors shadow-sm">
                                                    <i class="bi bi-trash"></i> Bỏ chọn ảnh
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Phân cách -->
                                        <div class="mt-4 relative flex items-center">
                                            <div class="flex-grow border-t border-gray-200 dark:border-zinc-800"></div>
                                            <span class="flex-shrink-0 mx-4 text-[10px] text-gray-400 dark:text-zinc-600 uppercase tracking-widest font-semibold">Hoặc dùng Link Ảnh</span>
                                            <div class="flex-grow border-t border-gray-200 dark:border-zinc-800"></div>
                                        </div>

                                        <!-- Input URL -->
                                        <div class="mt-4">
                                            <input type="url" name="image_url" x-model="formData.image_url" class="w-full px-4 py-2 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-800 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-zinc-600" placeholder="... dán link ảnh từ Google Drive hoặc Web khác">
                                        </div>
                                    </div>

                                <!-- Link liên kết -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-200 mb-1">Link liên kết cuộc thi (Khi nhấp vào Ảnh)</label>
                                    <input type="url" name="link" x-model="formData.link" class="w-full px-4 py-2 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-sm text-gray-800 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all" placeholder="https://...">
                                </div>
                                
                                <!-- Bật Hiển thị -->
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" id="is_active_cb" name="is_active" value="1" x-model="formData.is_active" class="h-4 w-4 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500 dark:focus:ring-emerald-500/50 border-gray-300 dark:border-zinc-800 rounded bg-white dark:bg-zinc-950/50">
                                    <label for="is_active_cb" class="ml-2 block text-sm text-gray-900 dark:text-zinc-200 font-medium cursor-pointer">Cho phép hiển thị ngay lên trang chủ</label>
                                </div>

                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-zinc-950/40 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-gray-100 dark:border-zinc-800 transition-colors">
                        <button type="submit" class="w-full inline-flex justify-center items-center !rounded-xl border border-transparent shadow-sm !px-5 !py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-base font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            <span x-text="mode === 'add' ? 'Lưu & Thêm mới' : 'Lưu Thay đổi'"></span>
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center items-center !rounded-xl border border-gray-300 dark:border-zinc-700 shadow-sm !px-5 !py-2.5 bg-white dark:bg-zinc-800 text-base font-semibold text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 dark:focus:ring-zinc-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thư viện SortableJS để kéo thả -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bannerManager', () => ({
            showModal: false,
            mode: 'add',
            formAction: '{{ route('admin.banners.store') }}',
            formData: {
                id: null,
                title: '',
                link: '',
                image_url: '',
                is_active: true
            },
            previewUrl: null,
            
            init() {
                // Khởi tạo Sortable
                this.$nextTick(() => {
                    let el = document.getElementById('sortable-banners');
                    if(el) {
                        Sortable.create(el, {
                            handle: '.drag-handle',
                            animation: 150,
                            ghostClass: 'bg-emerald-50',
                            onEnd: function () {
                                let orderIds = [];
                                el.querySelectorAll('tr[data-id]').forEach(row => {
                                    orderIds.push(row.getAttribute('data-id'));
                                });
                                axios.post('{{ route('admin.banners.update-order') }}', { order: orderIds })
                                    .then(res => { /* success handle silently */ })
                                    .catch(err => { alert('Lỗi lưu thứ tự mới!'); });
                            }
                        });
                    }
                });
            },

            openModal(mode, banner = null) {
                this.mode = mode;
                this.previewUrl = null;
                document.getElementById('image_upload').value = '';

                if(mode === 'edit' && banner) {
                    this.formAction = '{{ url('admin/banners') }}/' + banner.id;
                    this.formData.id = banner.id;
                    this.formData.title = banner.title || '';
                    this.formData.link = banner.link || '';
                    this.formData.image_url = ''; 
                    this.formData.is_active = banner.is_active ? true : false;
                    
                    // Show current image as preview if local
                    if (banner.image) {
                        this.previewUrl = banner.image.startsWith('http') ? banner.image : '{{ asset('storage') }}/' + banner.image;
                    }
                } else {
                    this.formAction = '{{ route('admin.banners.store') }}';
                    this.formData = { id: null, title: '', link: '', image_url: '', is_active: true };
                }
                this.showModal = true;
            },
            
            closeModal() {
                this.showModal = false;
            },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            },

            clearFile() {
                document.getElementById('image_upload').value = '';
                this.previewUrl = null;
                if (this.mode === 'edit') {
                    // Cố tình xóa ảnh cũ để force họ update (thực tế backend sẽ ignore nếu rỗng, trừ khi nhập link)
                }
            },

            handlePaste(e) {
                if (!this.showModal) return;
                const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                for (let index in items) {
                    const item = items[index];
                    if (item.kind === 'file' && item.type.startsWith('image/')) {
                        const blob = item.getAsFile();
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(blob);
                        document.getElementById('image_upload').files = dataTransfer.files;
                        this.previewUrl = URL.createObjectURL(blob);
                        break;
                    }
                }
            }
        }));
    });
</script>
@endsection
