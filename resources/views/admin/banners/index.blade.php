@extends('layouts.admin')
@section('title', 'Quản lý Banners Cuộc thi')
@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="bannerManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-slate-800 tracking-tight">
                <i class="bi bi-images text-emerald-500 me-2"></i>Quản lý Banner Cuộc thi
            </h3>
            <p class="text-sm text-slate-500 font-medium mt-1">Sắp xếp, thêm mới và quản lý các hình ảnh slider cuộc thi</p>
        </div>
        <button @click="openModal('add')" type="button" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 relative pb-4">
        <div class="overflow-x-auto rounded-t-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-3 py-4 w-16 text-center" title="Sắp xếp">STT</th>
                        <th class="px-5 py-4 w-48 text-center">Hình ảnh</th>
                        <th class="px-5 py-4">Tiêu đề & Link</th>
                        <th class="px-4 py-4 text-center whitespace-nowrap w-32">Hiển thị</th>
                        <th class="px-5 py-4 text-right whitespace-nowrap w-24">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700" id="sortable-banners">
                    @forelse($banners as $banner)
                    <tr data-id="{{ $banner->id }}" class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-3 py-3 text-center align-middle">
                            <span class="drag-handle cursor-move opacity-50 hover:opacity-100 transition-opacity" title="Kéo thả để di chuyển">
                                <i class="bi bi-grip-vertical text-xl text-gray-400 hover:text-emerald-600"></i>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center align-middle">
                            <div class="w-full h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center">
                                @if($banner->image)
                                    <img src="{{ Str::startsWith($banner->image, 'http') ? $banner->image : asset('storage/' . $banner->image) }}" alt="Banner" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="bi bi-image text-gray-400 text-2xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 align-middle">
                            <div class="font-semibold text-gray-800 text-sm mb-1">{{ $banner->title ?: '(Không có tiêu đề)' }}</div>
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
                                loading: false,
                                toggle() {
                                    if(this.loading) return;
                                    this.loading = true;
                                    axios.post('{{ route('admin.banners.toggle-active', $banner->id) }}')
                                    .then(res => {
                                        if(res.data.success) {
                                            this.isOn = res.data.is_active;
                                        }
                                    })
                                    .catch(err => { alert('Lỗi chuyển trạng thái!'); })
                                    .finally(() => { this.loading = false; });
                                }
                            }">
                                <button type="button" @click="toggle()" :class="isOn ? 'bg-emerald-500' : 'bg-gray-200'" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer !rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                    <span class="sr-only">Toggle</span>
                                    <span :class="isOn ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform !rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-3 align-middle text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="openModal('edit', {{ $banner->toJson() }})" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 !rounded-lg transition-colors" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá Banner này? Ảnh sẽ bị xoá vĩnh viễn.');" class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 !rounded-lg transition-colors" title="Xóa">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">
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
                 class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg">
                
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
                    
                    <div class="bg-white px-6 py-6 border-b border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-emerald-100">
                                <i class="bi bi-image text-emerald-600 text-lg"></i>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title" x-text="mode === 'add' ? 'Thêm Banner Cuộc thi mới' : 'Cập nhật Banner'"></h3>
                        </div>
                        
                        <div class="space-y-4">
                            
                            <!-- Tiêu đề -->
                            <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề (Không bắt buộc)</label>
                                        <input type="text" name="title" x-model="formData.title" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none" placeholder="Ví dụ: Cuộc thi viết về Môi trường">
                                    </div>
                                    
                                    <!-- Ảnh Banner -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Banner <span class="text-gray-400 text-xs font-normal ml-1">(Tải ảnh, Ctr+V dán, hoặc dùng Link)</span></label>
                                        
                                        <!-- Khu vực upload & preview -->
                                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl overflow-hidden relative bg-gray-50 hover:bg-gray-100 transition-colors"
                                            :class="{ 'border-emerald-500 bg-emerald-50/50': previewUrl }">
                                            
                                            <div class="space-y-1 text-center" x-show="!previewUrl">
                                                <i class="bi bi-cloud-arrow-up text-3xl text-gray-400"></i>
                                                <div class="flex text-sm text-gray-600 justify-center gap-1">
                                                    <label for="image_upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-1 shadow-sm border border-gray-200">
                                                        <span>Chọn file ảnh</span>
                                                        <input id="image_upload" name="image" type="file" accept="image/*" class="sr-only" @change="handleFileChange">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500 pt-2"><i class="bi bi-keyboard"></i> Mẹo: Ấn <kbd class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-700 font-mono">Ctrl</kbd> + <kbd class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-700 font-mono">V</kbd> để dán ảnh trực tiếp.</p>
                                            </div>

                                            <div x-show="previewUrl" class="relative w-full flex flex-col items-center" style="display: none;">
                                                <img :src="previewUrl" class="max-h-32 object-contain rounded border border-gray-200 shadow-sm" alt="Preview">
                                                <button type="button" @click="clearFile()" class="mt-3 px-3 py-1.5 bg-white border border-gray-300 !rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 transition-colors shadow-sm">
                                                    <i class="bi bi-trash"></i> Bỏ chọn ảnh
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Phân cách -->
                                        <div class="mt-4 relative flex items-center">
                                            <div class="flex-grow border-t border-gray-200"></div>
                                            <span class="flex-shrink-0 mx-4 text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Hoặc dùng Link Ảnh</span>
                                            <div class="flex-grow border-t border-gray-200"></div>
                                        </div>

                                        <!-- Input URL -->
                                        <div class="mt-4">
                                            <input type="url" name="image_url" x-model="formData.image_url" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400" placeholder="... dán link ảnh từ Google Drive hoặc Web khác">
                                        </div>
                                    </div>

                                <!-- Link liên kết -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Link liên kết cuộc thi (Khi nhấp vào Ảnh)</label>
                                    <input type="url" name="link" x-model="formData.link" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none" placeholder="https://...">
                                </div>
                                
                                <!-- Bật Hiển thị -->
                                <div class="flex items-center mt-2">
                                    <input type="checkbox" id="is_active_cb" name="is_active" value="1" x-model="formData.is_active" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                    <label for="is_active_cb" class="ml-2 block text-sm text-gray-900 font-medium">Cho phép hiển thị ngay lên trang chủ</label>
                                </div>

                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center items-center !rounded-xl border border-transparent shadow-sm !px-5 !py-2.5 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            <span x-text="mode === 'add' ? 'Lưu & Thêm mới' : 'Lưu Thay đổi'"></span>
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center items-center !rounded-xl border border-gray-300 shadow-sm !px-5 !py-2.5 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
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
