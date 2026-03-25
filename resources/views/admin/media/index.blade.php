@extends('layouts.admin')
@section('title', 'Quản lý Media')

@section('styles')
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<style>
    .dropzone {
        border: 2px dashed #10b981; /* emerald-500 */
        border-radius: 1rem;
        background: #f8fafc;
        padding: 40px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .dropzone:hover {
        background: #ecfdf5;
    }
    .dropzone .dz-message {
        margin: 0;
        font-weight: 500;
        color: #64748b;
    }
    .dropzone .dz-message i {
        font-size: 3.5rem;
        color: #10b981;
        margin-bottom: 15px;
        display: inline-block;
    }
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="{ openUpload: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight"><i class="bi bi-images text-emerald-500 me-2"></i>Thư viện Media</h3>
            <p class="text-sm text-gray-500 mt-1">Tổng cộng: <strong class="text-gray-900">{{ number_format($totalMedia) }}</strong> tập tin</p>
        </div>
        <button type="button" @click="openUpload = !openUpload" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-emerald-500/20">
            <i class="bi bi-cloud-arrow-up text-lg"></i> <span x-text="openUpload ? 'Đóng tải lên' : 'Tải lên Media'"></span>
        </button>
    </div>

    <!-- Upload Section -->
    <div x-show="openUpload" x-collapse x-cloak class="mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h5 class="text-lg font-bold text-gray-800 mb-4">Tải tệp mới (Kéo thả hoặc click)</h5>
            <form action="{{ route('admin.media.upload') }}" class="dropzone" id="mediaDropzone">
                @csrf
                <div class="dz-message">
                    <i class="bi bi-cloud-upload"></i><br>
                    <span class="text-sm">Kéo và thả ảnh/video vào đây, hoặc click để chọn tệp.<br>
                    <span class="text-xs text-gray-500 font-medium mt-1 inline-block">Hỗ trợ: JPG, PNG, WEBP, MP4 (Tối đa 20MB)</span></span>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-check-circle-fill text-xl text-emerald-500 shrink-0"></i> 
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên file..." class="w-full !pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all">
            </div>
            <div class="w-full sm:w-48 relative">
                <select name="type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">-- Mọi loại --</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Chỉ Ảnh</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Chỉ Video</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400"><i class="bi bi-chevron-down text-xs"></i></div>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 w-full sm:w-auto">
                <i class="bi bi-funnel"></i> Lọc dữ liệu
            </button>
            @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('admin.media.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all text-center w-full sm:w-auto">
                Xóa lọc
            </a>
            @endif
        </form>
    </div>

    <!-- Data List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold text-[11px] tracking-wider uppercase">
                    <tr>
                        <th class="px-5 py-4 w-20 text-center">Ảnh gốc</th>
                        <th class="px-4 py-4">Tên file</th>
                        <th class="px-4 py-4 text-center">Loại</th>
                        <th class="px-4 py-4 text-center">Kích thước</th>
                        <th class="px-4 py-4 text-center">Người Upload</th>
                        <th class="px-4 py-4 text-center">Shared</th>
                        <th class="px-4 py-4 text-center">Ngày Upload</th>
                        <th class="px-5 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700">
                    @forelse ($media as $item)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-5 py-3 text-center">
                            <div class="w-12 h-12 mx-auto rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">
                                @if(str_starts_with($item->file_type, 'image/'))
                                    <img src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-file-play-fill text-emerald-500 text-2xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 group-hover:text-emerald-700 transition">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}">
                                {{ Str::limit($item->file_name, 30) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded bg-gray-100 text-gray-600 text-[10px] font-bold tracking-wide uppercase">{{ explode('/', $item->file_type)[0] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center font-medium text-gray-500 text-xs">
                            {{ number_format($item->file_size / 1024, 1) }} KB
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 text-xs font-medium">
                            {{ mb_strimwidth($item->user->name ?? 'Unknown', 0, 15, '...') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->is_shared)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 shadow-sm"><i class="bi bi-check-circle-fill"></i> Có</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 text-[11px] font-bold border border-orange-200/60 shadow-sm">Không</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 text-xs font-medium">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors shadow-sm" title="Xem trước" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc xoá file này vĩnh viễn không? Hành động này có thể làm lỗi ảnh trong bài viết đang sử dụng nó.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors shadow-sm" title="Xóa">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Preview Modal -->
                    <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 p-0 overflow-hidden rounded-2xl shadow-xl">
                                <div class="modal-header border-b border-gray-100 bg-gray-50/50">
                                    <h5 class="text-sm font-bold text-gray-700">Xem trước Media</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-6 bg-gray-50/30">
                                    @if(str_starts_with($item->file_type, 'image/'))
                                        <img src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" class="inline-block max-w-full rounded-lg shadow-sm border border-gray-200 object-contain bg-white" style="max-height: 60vh;">
                                    @else
                                        <video controls class="w-full rounded-lg shadow-sm border border-gray-200 bg-black" style="max-height: 60vh;">
                                            <source src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" type="{{ $item->file_type }}">
                                            Trình duyệt không hỗ trợ video này.
                                        </video>
                                    @endif
                                    <div class="mt-4 p-3 bg-white border border-gray-200 rounded-xl text-xs text-gray-600 text-left flex flex-col gap-1.5">
                                        <div><span class="font-bold">Đường dẫn:</span> <code class="bg-gray-100 px-2 py-1 rounded text-pink-600 font-medium select-all">{{ $item->file_path }}</code></div>
                                        <div class="text-[11px] text-gray-500"><i class="bi bi-info-circle"></i> Bạn có thể sao chép đường dẫn này để nhúng vào bài viết/dịch vụ khác.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="bi bi-images text-4xl text-gray-300"></i>
                                <p>Chưa có media nào. Hãy tải lên ngay!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-50">
            {{ $media->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    // Khởi tạo dropzone
    const myDropzone = new Dropzone("#mediaDropzone", {
        acceptedFiles: "image/*,video/mp4",
        maxFilesize: 20, // MB
        dictDefaultMessage: "Kéo thả file vào đây để tải lên",
        dictFallbackMessage: "Trình duyệt của bạn không hỗ trợ kéo thả file.",
        dictFileTooBig: "File quá lớn (@{{filesize}}MB). Tối đa: @{{maxFilesize}}MB.",
        dictInvalidFileType: "Không thể upload loại file này.",
        init: function() {
            this.on("success", function(file, response) {
                // Remove file visually when successful
                setTimeout(() => {
                    this.removeFile(file);
                }, 2000);
            });
            this.on("queuecomplete", function() {
                // Tải lại trang khi queue xử lý xong để thấy ảnh mới
                window.location.reload(); 
            });
        }
    });
</script>
@endsection
