@extends('layouts.app') <!-- Please adjust if layout name differs -->

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>{{ isset($post) ? 'Chỉnh sửa bài viết nháp' : 'Viết bài mới' }}</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    Cố lỗi xảy ra, vui lòng kiểm tra lại.
                </div>
            @endif

            <form id="postForm" method="POST" 
                  action="{{ isset($post) ? route('contributor.posts.update', $post) : route('contributor.posts.store') }}" 
                  enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Tiêu đề bài viết (*)</label>
                        <input type="text" class="form-control" id="title" name="title" required
                               value="{{ old('title', $post->title ?? '') }}">
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Chuyên mục (*)</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Chọn chuyên mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name ?? 'Category ' . $cat->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="thumbnail" class="form-label">Ảnh đại diện (Thumbnail)</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                        @error('thumbnail') <span class="text-danger">{{ $message }}</span> @enderror
                        @if(isset($post) && $post->thumbnail)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Thumbnail" width="150" class="img-thumbnail">
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-outline-info" id="btnImportWord">
                            <i class="bi bi-file-word"></i> Import Word (.docx)
                        </button>
                        <input type="file" id="wordFileInput" accept=".docx,.doc" class="d-none">

                        <button type="button" class="btn btn-outline-primary" id="btnAIGenerate">
                            <i class="bi bi-robot"></i> AI Tự động viết
                        </button>

                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#mediaModal">
                            <i class="bi bi-images"></i> Media Library
                        </button>
                    </div>
                </div>

                <!-- AI Prompt Section -->
                <div class="row mb-3 d-none" id="aiPromptTarget">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" class="form-control" id="aiPromptInput" placeholder="Nhập chủ đề để AI tự động viết...">
                            <button class="btn btn-primary" type="button" id="btnExecuteAI">Tạo nội dung</button>
                        </div>
                        <small class="text-muted">Tính năng sử dụng AI để tạo nháp bài viết theo yêu cầu.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="editor" class="form-label">Nội dung bài viết (*)</label>
                    <textarea class="form-control" id="editor" name="content">{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSaveDraft">
                        <i class="bi bi-save"></i> Lưu nháp
                    </button>

                    @if(isset($post))
                        <button type="button" class="btn btn-success" id="btnSubmitReview">
                            <i class="bi bi-send-check"></i> Gửi duyệt
                        </button>
                    @endif
                </div>

                <span id="autoSaveStatus" class="ms-3 text-muted" style="font-size: 0.9em;"></span>
            </form>

            @if(isset($post))
                <!-- Hidden form for Submit Review -->
                <form id="submitReviewForm" action="{{ route('contributor.posts.submit', $post) }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Modal Media Library -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mediaModalLabel">Media Library</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="mediaTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">Tải lên</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="library-tab" data-bs-toggle="tab" data-bs-target="#library" type="button" role="tab">Thư viện của tôi</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="shared-tab" data-bs-toggle="tab" data-bs-target="#shared" type="button" role="tab">Ảnh Shared</button>
          </li>
        </ul>
        <div class="tab-content pt-3" id="mediaTabContent">
          <!-- Upload Tab -->
          <div class="tab-pane fade show active" id="upload" role="tabpanel">
             <input type="file" id="mediaUploadInput" class="form-control mb-2" accept="image/*,video/*">
             <button class="btn btn-primary" id="btnUploadMediaFile">Tải lên</button>
             <div id="uploadResult" class="mt-2"></div>
          </div>
          <!-- Personal Library -->
          <div class="tab-pane fade" id="library" role="tabpanel">
             <div id="personalMediaList" class="row g-2 overflow-auto" style="max-height: 400px;">
                <p class="text-muted text-center w-100 mt-3">Đang tải...</p>
             </div>
          </div>
          <!-- Shared Assets -->
          <div class="tab-pane fade" id="shared" role="tabpanel">
             <div id="sharedMediaList" class="row g-2 overflow-auto" style="max-height: 400px;">
                <p class="text-muted text-center w-100 mt-3">Đang tải...</p>
             </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let myEditor;
    const postId = {{ isset($post) ? $post->id : 'null' }};
    const uploadMediaUrl = "{{ route('contributor.media.upload') }}";
    
    // Khởi tạo CKEditor 5
    ClassicEditor
        .create(document.querySelector('#editor'), {
            ckfinder: {
                uploadUrl: uploadMediaUrl + '?_token={{ csrf_token() }}'
            }
        })
        .then(editor => {
            myEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // --- IMPORT WORD ---
    document.getElementById('btnImportWord').addEventListener('click', function() {
        document.getElementById('wordFileInput').click();
    });

    document.getElementById('wordFileInput').addEventListener('change', function(e) {
        if(e.target.files.length === 0) return;
        const file = e.target.files[0];
        
        const formData = new FormData();
        formData.append('document', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('contributor.posts.import-word') }}", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.title) {
                document.getElementById('title').value = data.title;
            }
            if(data.content && myEditor) {
                const currentContent = myEditor.getData();
                myEditor.setData(currentContent + data.content);
            }
            alert('Import thành công!');
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi khi import file Word');
        });
    });

    // --- AI GENERATE ---
    document.getElementById('btnAIGenerate').addEventListener('click', function() {
        document.getElementById('aiPromptTarget').classList.toggle('d-none');
    });

    document.getElementById('btnExecuteAI').addEventListener('click', function() {
        const prompt = document.getElementById('aiPromptInput').value;
        if(!prompt) {
            alert('Vui lòng nhập chủ đề'); return;
        }

        this.disabled = true;
        this.innerHTML = 'Đang xử lý...';

        const formData = new FormData();
        formData.append('prompt', prompt);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('ai.generate-post') }}", {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.content && myEditor) {
                const currentContent = myEditor.getData();
                myEditor.setData(currentContent + data.content);
            }
            document.getElementById('btnExecuteAI').disabled = false;
            document.getElementById('btnExecuteAI').innerHTML = 'Tạo nội dung';
            document.getElementById('aiPromptTarget').classList.add('d-none');
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi khởi tạo AI');
            document.getElementById('btnExecuteAI').disabled = false;
            document.getElementById('btnExecuteAI').innerHTML = 'Tạo nội dung';
        });
    });

    // --- MEDIA UPLOAD MODAL ---
    document.getElementById('btnUploadMediaFile').addEventListener('click', function() {
        const fileInput = document.getElementById('mediaUploadInput');
        if(fileInput.files.length === 0) return;
        
        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append('upload', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch(uploadMediaUrl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.url) {
                document.getElementById('uploadResult').innerHTML = `<p class="text-success">Thành công!</p><p>URL: <a href="${data.url}" target="_blank">${data.url}</a></p>`;
                // Insert into CKEditor
                insertMediaToEditor(data.url, data.type);
            } else {
                alert('Upload thất bại: ' + (data.error?.message || 'Lỗi không xác định'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Upload thất bại');
        });
    });

    // Hàm chèn media chung
    function insertMediaToEditor(url, type) {
        if (!myEditor) return;
        const contentToInsert = type === 'image' 
            ? `<figure class="image"><img src="${url}" alt="media"></figure>`
            : `<figure class="media"><video controls src="${url}"></video></figure>`;
        
        const viewFragment = myEditor.data.processor.toView(contentToInsert);
        const modelFragment = myEditor.data.toModel(viewFragment);
        myEditor.model.insertContent(modelFragment, myEditor.model.document.selection);
        
        // Close modal
        const modalEl = document.getElementById('mediaModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if(modal) modal.hide();
    }

    // Load Personal Library
    document.getElementById('library-tab').addEventListener('click', function() {
        const container = document.getElementById('personalMediaList');
        // Tránh load lại nhiều lần nếu ko cần, hoặc set cờ re-load. Ở đây load mỡi lần click
        container.innerHTML = '<p class="text-muted text-center w-100 mt-3">Đang tải...</p>';
        
        fetch("{{ route('contributor.media.personal') }}")
            .then(res => res.json())
            .then(data => renderMediaList(data, container))
            .catch(err => { console.error(err); container.innerHTML = '<p class="text-danger w-100 text-center">Lỗi tải dữ liệu.</p>'; });
    });

    // Load Shared Library
    document.getElementById('shared-tab').addEventListener('click', function() {
        const container = document.getElementById('sharedMediaList');
        container.innerHTML = '<p class="text-muted text-center w-100 mt-3">Đang tải...</p>';
        
        fetch("{{ route('contributor.media.shared') }}")
            .then(res => res.json())
            .then(data => renderMediaList(data, container))
            .catch(err => { console.error(err); container.innerHTML = '<p class="text-danger w-100 text-center">Lỗi tải dữ liệu.</p>'; });
    });

    function renderMediaList(mediaArray, targetContainer) {
        if(!mediaArray || mediaArray.length === 0) {
            targetContainer.innerHTML = '<p class="text-muted text-center w-100 mt-3">Không có file nào.</p>';
            return;
        }

        let html = '';
        mediaArray.forEach(m => {
            const preview = m.file_type === 'image' 
                ? `<img src="${m.url}" class="card-img-top" style="height:120px; object-fit:cover;" alt="media">`
                : `<div class="bg-dark text-white d-flex align-items-center justify-content-center" style="height:120px;"><i class="bi bi-play-circle" style="font-size:2rem;"></i></div>`;
            
            html += `
            <div class="col-4 col-md-3">
                <div class="card h-100">
                    ${preview}
                    <div class="card-body p-2 text-center">
                        <small class="d-block text-truncate mb-2" title="${m.file_name}">${m.file_name}</small>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-insert-media" data-url="${m.url}" data-type="${m.file_type}">Chèn</button>
                    </div>
                </div>
            </div>`;
        });
        targetContainer.innerHTML = html;

        // Gắn sự kiện click
        targetContainer.querySelectorAll('.btn-insert-media').forEach(btn => {
            btn.addEventListener('click', function() {
                insertMediaToEditor(this.getAttribute('data-url'), this.getAttribute('data-type'));
            });
        });
    }

    // --- SUBMIT REVIEW ---
    const btnSubmitReview = document.getElementById('btnSubmitReview');
    if(btnSubmitReview) {
        btnSubmitReview.addEventListener('click', function() {
            if(confirm('Bạn có chắc chắn muốn gửi bài viết này để chờ duyệt?')) {
                document.getElementById('submitReviewForm').submit();
            }
        });
    }

    // --- AUTO SAVE ---
    if(postId) {
        setInterval(() => {
            if(!myEditor) return;
            const content = myEditor.getData();
            if(!content) return;

            const formData = new FormData();
            formData.append('content', content);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/contributor/posts/${postId}/autosave`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const statusSpan = document.getElementById('autoSaveStatus');
                const now = new Date();
                statusSpan.innerHTML = `Đã tự động lưu nháp lúc ${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`;
                statusSpan.classList.add('text-success');
                setTimeout(() => { statusSpan.innerHTML = ''; }, 5000);
            })
            .catch(err => console.error('Lỗi auto-save', err));
        }, 30000); // 30s
    }
</script>
@endpush
