@extends('layouts.admin')
@section('title', 'Quản lý Media')

@section('styles')
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
<style>
    .dropzone {
        border: 2px dashed var(--agu-primary);
        border-radius: 12px;
        background: #f8f9fa;
        padding: 40px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .dropzone:hover {
        background: #e8f5e9;
    }
    .dropzone .dz-message {
        margin: 0;
        font-weight: 500;
        color: #555;
    }
    .dropzone .dz-message i {
        font-size: 3rem;
        color: var(--agu-primary);
        margin-bottom: 10px;
    }
    
    .media-card {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #eee;
        transition: transform 0.2s;
    }
    .media-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .media-preview {
        height: 150px;
        background: #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .media-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .media-preview i {
        font-size: 3rem;
        color: #999;
    }
    .media-info {
        padding: 12px;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1"><i class="bi bi-images text-success me-2"></i>Thư viện Media</h2>
            <p class="text-muted mb-0">Tổng cộng: <strong class="text-dark">{{ number_format($totalMedia) }}</strong> tập tin chia sẻ</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
            <button type="button" class="btn btn-success fw-semibold hover-lift" data-bs-toggle="collapse" data-bs-target="#uploadSection">
                <i class="bi bi-cloud-arrow-up me-1"></i> Tải lên Media
            </button>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="collapse mb-4" id="uploadSection">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Tải tệp mới (Kéo thả hoặc click)</h5>
                <form action="{{ route('admin.media.upload') }}" class="dropzone" id="mediaDropzone">
                    @csrf
                    <div class="dz-message">
                        <i class="bi bi-cloud-upload"></i><br>
                        <span>Kéo và thả ảnh/video vào đây, hoặc click để chọn tệp.<br>
                        <small class="text-muted">Hỗ trợ: JPG, PNG, WEBP, MP4 (Tối đa 20MB)</small></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data List -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <!-- Filter -->
            <form method="GET" action="{{ route('admin.media.index') }}" class="mb-4 d-flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width: 250px;" placeholder="Tìm tên file...">
                <select name="type" class="form-select" style="width: 150px;">
                    <option value="">-- Mọi loại --</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Chỉ Ảnh</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Chỉ Video</option>
                </select>
                <button type="submit" class="btn btn-secondary px-4"><i class="bi bi-search"></i> Lọc</button>
                <a href="{{ route('admin.media.index') }}" class="btn btn-light border">Xoá lọc</a>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ảnh gốc</th>
                            <th>Tên file</th>
                            <th>Loại</th>
                            <th>Kích thước</th>
                            <th>Nguời Upload</th>
                            <th>Shared</th>
                            <th>Ngày Upload</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($media as $item)
                        <tr>
                            <td style="width: 80px;">
                                <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background:#f0f0f0;" class="d-flex align-items-center justify-content-center border">
                                    @if(str_starts_with($item->file_type, 'image/'))
                                        <img src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="bi bi-file-play-fill text-primary fs-3"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="fw-semibold">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}" class="text-decoration-none text-dark hover-lift">
                                    {{ Str::limit($item->file_name, 30) }}
                                </a>
                            </td>
                            <td><span class="badge bg-secondary">{{ explode('/', $item->file_type)[0] }}</span></td>
                            <td>{{ number_format($item->file_size / 1024, 1) }} KB</td>
                            <td>{{ $item->user->name ?? 'Unknown' }}</td>
                            <td>
                                @if($item->is_shared)
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i> Yes</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning">No</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group gap-2">
                                    <button class="btn btn-sm btn-light border text-primary rounded" title="Preview" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Bạn có chắc xoá file này vĩnh viễn không?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded" title="XOÁ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Preview Modal -->
                        <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 p-0 overflow-hidden" style="border-radius: 16px;">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        @if(str_starts_with($item->file_type, 'image/'))
                                            <img src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 70vh;">
                                        @else
                                            <video controls style="width: 100%; max-height: 70vh;" class="rounded shadow-sm">
                                                <source src="{{ Str::startsWith($item->file_path, 'http') ? $item->file_path : asset($item->file_path) }}" type="{{ $item->file_type }}">
                                                Trình duyệt không hỗ trợ video này.
                                            </video>
                                        @endif
                                        <div class="mt-3 text-muted">
                                            Path: <code>{{ $item->file_path }}</code> (Sao chép đường dẫn này để bỏ vào bài viết)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-images fs-1 d-block mb-3 text-light"></i>
                                Chưa có media nào. Hãy upload ngay!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $media->links() }}
            </div>
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
