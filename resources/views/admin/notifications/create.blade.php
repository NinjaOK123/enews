@extends('layouts.admin')
@section('title', 'Tạo thông báo mới')

@section('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 250px;
        border-radius: 0 0 8px 8px !important;
    }
    .recipient-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 10px 16px;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }
    .recipient-card:has(input:checked) {
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.07);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0" style="max-width: 860px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Thông báo</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4 p-md-5">
            <h4 class="fw-bold mb-4"><i class="bi bi-bell-plus text-success me-2"></i>Tạo thông báo mới</h4>

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                {{-- Tiêu đề --}}
                <div class="mb-4">
                    <label for="title" class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title"
                           class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title') }}"
                           placeholder="Nhập tiêu đề thông báo..."
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nội dung (CKEditor 5) --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" id="notifContent" class="@error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Đối tượng nhận --}}
                <div class="mb-5">
                    <label class="form-label fw-semibold d-block">Đối tượng nhận <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-3">Chọn một hoặc nhiều nhóm đối tượng sẽ nhận thông báo này.</p>

                    @if($errors->has('recipients'))
                        <div class="text-danger small mb-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first('recipients') }}</div>
                    @endif

                    <div class="row g-2">
                        {{-- All --}}
                        <div class="col-6 col-md-4">
                            <label class="recipient-card d-flex align-items-center gap-2">
                                <input type="checkbox" name="recipients[]" value="all" class="form-check-input mt-0"
                                       {{ in_array('all', old('recipients', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-people-fill text-success me-1"></i>Tất cả</div>
                                    <small class="text-muted">Mọi người dùng</small>
                                </div>
                            </label>
                        </div>
                        {{-- Editor --}}
                        <div class="col-6 col-md-4">
                            <label class="recipient-card d-flex align-items-center gap-2">
                                <input type="checkbox" name="recipients[]" value="editor" class="form-check-input mt-0"
                                       {{ in_array('editor', old('recipients', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-pencil-square text-primary me-1"></i>Biên tập</div>
                                    <small class="text-muted">Nhóm Editor</small>
                                </div>
                            </label>
                        </div>
                        {{-- Contributor --}}
                        <div class="col-6 col-md-4">
                            <label class="recipient-card d-flex align-items-center gap-2">
                                <input type="checkbox" name="recipients[]" value="contributor" class="form-check-input mt-0"
                                       {{ in_array('contributor', old('recipients', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-pen text-secondary me-1"></i>Cộng tác viên</div>
                                    <small class="text-muted">Nhóm Contributor</small>
                                </div>
                            </label>
                        </div>
                        {{-- Reader --}}
                        <div class="col-6 col-md-4">
                            <label class="recipient-card d-flex align-items-center gap-2">
                                <input type="checkbox" name="recipients[]" value="reader" class="form-check-input mt-0"
                                       {{ in_array('reader', old('recipients', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-person text-info me-1"></i>Độc giả</div>
                                    <small class="text-muted">Nhóm Reader</small>
                                </div>
                            </label>
                        </div>
                        {{-- Admin --}}
                        <div class="col-6 col-md-4">
                            <label class="recipient-card d-flex align-items-center gap-2">
                                <input type="checkbox" name="recipients[]" value="admin" class="form-check-input mt-0"
                                       {{ in_array('admin', old('recipients', [])) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-semibold"><i class="bi bi-shield-fill text-danger me-1"></i>Admin</div>
                                    <small class="text-muted">Nhóm Admin</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Lưu nháp
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-light border px-4">Huỷ</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#notifContent'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
                      'bulletedList', 'numberedList', 'blockQuote', '|',
                      'link', 'insertTable', '|', 'undo', 'redo'],
            placeholder: 'Nhập nội dung thông báo tại đây...'
        })
        .catch(error => console.error(error));
</script>
@endsection
