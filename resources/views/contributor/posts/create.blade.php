@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header Bar --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">
          {{ isset($post) ? '✏️ Chỉnh sửa bài viết' : '📝 Viết bài mới' }}
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Điền đầy đủ thông tin và nội dung bài viết</p>
      </div>
      <div class="flex items-center gap-2">
        <span id="autoSaveStatus" class="text-xs text-gray-400 hidden">
          <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 px-3 py-1.5 rounded-full border border-green-200">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span id="autoSaveText">Đã lưu tự động</span>
          </span>
        </span>
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
          ← Quay lại
        </a>
      </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
      ✅ {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
      ⚠️ Có lỗi xảy ra, vui lòng kiểm tra lại các trường bắt buộc.
    </div>
    @endif

    {{-- Main Form --}}
    <form id="postForm" method="POST"
          action="{{ isset($post) ? route('contributor.posts.update', $post) : route('contributor.posts.store') }}"
          enctype="multipart/form-data">
      @csrf



      <div class="flex gap-6 items-start">

        {{-- ═══ LEFT: Main Editor ═══ --}}
        <div class="flex-1 min-w-0 space-y-5">

          {{-- Tiêu đề --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label for="title" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
              Tiêu đề bài viết <span class="text-red-500">*</span>
            </label>
            <input type="text" id="title" name="title" required
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="Nhập tiêu đề hấp dẫn, rõ ràng..."
                   class="w-full text-xl font-semibold text-gray-900 border-0 outline-none placeholder:text-gray-300 bg-transparent">
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- AI Prompt (ẩn mặc định) --}}
          <div id="aiPromptTarget" class="hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-4">
              <p class="text-sm font-semibold text-indigo-700 mb-2">🤖 AI Tự động viết</p>
              <div class="flex gap-2">
                <input type="text" id="aiPromptInput"
                       placeholder="Nhập chủ đề, AI sẽ tạo nháp bài viết cho bạn..."
                       class="flex-1 border border-indigo-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-400 bg-white">
                <button type="button" id="btnExecuteAI"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                  Tạo nội dung
                </button>
              </div>
              <p class="text-xs text-indigo-400 mt-1.5">AI sẽ tạo bản nháp, bạn chỉnh lại trước khi đăng.</p>
            </div>
          </div>

          {{-- Editor --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/60">
              <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Nội dung bài viết <span class="text-red-500">*</span>
              </span>
              <div class="flex items-center gap-2">
                <button type="button" id="btnImportWord"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  Import Word
                </button>
                <input type="file" id="wordFileInput" accept=".docx,.doc" class="hidden">

                <button type="button" id="btnAIGenerate"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                  AI viết
                </button>

                <button type="button" onclick="document.getElementById('mediaModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg border border-gray-200 transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  Media
                </button>
              </div>
            </div>
            <div class="p-0">
              <textarea id="editor" name="content">{{ old('content', $post->content ?? '') }}</textarea>
              @error('content')<p class="px-5 pb-3 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Action Buttons --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mt-5">
            <div class="flex flex-wrap items-center gap-3">
              <button type="submit" name="action" value="draft" id="btnSaveDraft"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl border border-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                {{ isset($post) && $post->status === 'published' ? 'Lưu cập nhật' : 'Lưu nháp' }}
              </button>

              @if(in_array(auth()->user()->role ?? '', ['admin', 'editor']))
              <button type="button" id="btnApprovePost" onclick="if(confirm('Bạn có chắc muốn {{ isset($post) && $post->status==='published' ? 'CẬP NHẬT' : 'DUYỆT XUẤT BẢN' }} bài viết này?')) { let a = document.getElementById('hiddenActionInput'); if(!a) { a = document.createElement('input'); a.type='hidden'; a.name='action'; a.id='hiddenActionInput'; document.getElementById('postForm').appendChild(a); } a.value='approve'; document.getElementById('postForm').submit(); }"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-xl shadow-lg shadow-blue-200 transition">
                <i class="bi bi-check-all text-lg"></i> {{ isset($post) && $post->status==='published' ? 'Lưu & Đăng' : 'Duyệt bài' }}
              </button>
              
              <button type="button" id="btnRejectPost" onclick="if(confirm('Bạn có chắc muốn TỪ CHỐI bài viết này?')) { let a = document.getElementById('hiddenActionInput'); if(!a) { a = document.createElement('input'); a.type='hidden'; a.name='action'; a.id='hiddenActionInput'; document.getElementById('postForm').appendChild(a); } a.value='reject'; document.getElementById('postForm').submit(); }"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 rounded-xl shadow-lg shadow-rose-200 transition">
                <i class="bi bi-x-circle text-lg"></i> Từ chối
              </button>

              <div class="h-8 w-px bg-gray-200 hidden xl:block mx-1"></div>

              <a href="https://free-turnitin-plagiarism-checker-tfrg.onrender.com/" target="_blank"
                 class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 rounded-xl border border-gray-200 shadow-sm transition">
                <i class="bi bi-shield-check text-indigo-500 text-lg"></i> Check Đạo Văn
              </a>
              @else
              @if(!isset($post) || !in_array($post->status, ['pending', 'published']))
              <button type="submit" name="action" value="pending" id="btnSubmitReview"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-xl shadow-lg shadow-green-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Gửi bài chờ duyệt
              </button>
              @endif
              @endif

              <p class="text-xs text-gray-400 sm:ml-auto">
                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tự động lưu mỗi 30 giây
              </p>
            </div>
          </div>
        </div>

        {{-- ═══ RIGHT: Sidebar ═══ --}}
        <div class="w-80 shrink-0 space-y-5">

          {{-- Chuyên mục --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
              Chuyên mục <span class="text-red-500">*</span>
            </label>
            <select name="category_id" id="category_id" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 bg-white transition">
              <option value="">-- Chọn chuyên mục --</option>
              @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name ?? 'Category ' . $cat->id }}
              </option>
              @endforeach
            </select>
            @error('category_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- Thumbnail --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
              Ảnh đại diện (Thumbnail)
            </label>

            {{-- Preview --}}
            <div id="thumbPreviewWrap" class="{{ (isset($post) && $post->thumbnail) ? '' : 'hidden' }} mb-3 relative group">
              <img id="thumbPreview"
                   src="{{ (isset($post) && $post->thumbnail) ? asset('storage/' . $post->thumbnail) : '' }}"
                   class="w-full h-44 object-cover rounded-xl border border-gray-200 block">
              <button type="button" id="btnRemoveThumb"
                      class="absolute top-2 right-2 bg-white/90 hover:bg-red-50 text-red-500 rounded-full w-7 h-7 flex items-center justify-center shadow text-xs border border-red-100 opacity-0 group-hover:opacity-100 transition">✕</button>
            </div>

            {{-- Drop zone --}}
            <label for="thumbnail" id="thumbDropzone"
                   class="{{ (isset($post) && $post->thumbnail) ? 'hidden' : '' }} flex flex-col items-center justify-center gap-2 h-36 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50/30 transition">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span class="text-xs text-gray-400 text-center">Click, kéo thả, hoặc ấn Ctrl+V (Paste)<br><span class="text-gray-300">để dán ảnh bìa vào đây</span></span>
            </label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden">
            @error('thumbnail')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- Tên tác giả / Nguồn --}}
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label for="source_author" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
              Tác giả / Nguồn
            </label>
            <input type="text" name="source_author" id="source_author"
                   value="{{ old('source_author', $post->source_author ?? '') }}"
                   placeholder="VD: Cẩm Thiều - TV"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition">
            <p class="text-xs text-gray-400 mt-1.5">Hiển thị in đậm cuối bài viết</p>
          </div>

          {{-- Tips card --}}
          <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-100 p-5">
            <p class="text-xs font-bold text-amber-700 mb-2">💡 Mẹo viết bài hay</p>
            <ul class="space-y-1.5 text-xs text-amber-600">
              <li>• Tiêu đề ngắn gọn, dưới 80 ký tự</li>
              <li>• Ảnh đại diện tỉ lệ 16:9, rõ nét</li>
              <li>• Dùng AI để tạo bản nháp ban đầu</li>
              <li>• Kiểm tra chính tả trước khi gửi</li>
            </ul>
          </div>


        </div>
      </div>
    </form>
  </div>
</div>

{{-- ══════ Confirm Submit Modal ══════ --}}
<div id="confirmSubmitModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('confirmSubmitModal').classList.add('hidden')"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100 opacity-100 border border-gray-100">
    <div class="flex items-start gap-4 mb-2">
      <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-inner">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
      </div>
      <div class="pt-1">
        <h3 class="text-lg font-bold text-gray-900 leading-none">Xác nhận gửi bài?</h3>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">Ban Biên tập sẽ nhận được bài viết này. <strong class="text-gray-700">Bạn sẽ không thể tự chỉnh sửa</strong> trong lúc chờ duyệt.</p>
      </div>
    </div>
    
    <div class="flex items-center justify-end gap-3 mt-6">
      <button type="button" onclick="document.getElementById('confirmSubmitModal').classList.add('hidden')"
              class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition">
        Chưa, quay lại
      </button>
      <button type="button" onclick="processSubmit()"
              class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 rounded-xl shadow-lg shadow-emerald-200 focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 transition transform active:scale-95">
        Đồng ý gửi
      </button>
    </div>
  </div>
</div>

{{-- ══════ Media Library Modal ══════ --}}
<div id="mediaModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('mediaModal').classList.add('hidden')"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/60">
      <h3 class="font-bold text-gray-800">🖼️ Media Library</h3>
      <button type="button" onclick="document.getElementById('mediaModal').classList.add('hidden')"
              class="text-gray-400 hover:text-gray-700 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-lg">✕</button>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-100 px-6">
      <button type="button" class="media-tab-btn active-tab px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition" data-tab="upload">📤 Tải lên</button>
      <button type="button" class="media-tab-btn px-4 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent -mb-px hover:text-gray-700 transition" data-tab="library" id="library-tab">📁 Của tôi</button>
      <button type="button" class="media-tab-btn px-4 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent -mb-px hover:text-gray-700 transition" data-tab="shared" id="shared-tab">🌐 Shared</button>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
      {{-- Upload Tab --}}
      <div id="tab-upload" class="media-tab-pane">
        <label for="mediaUploadInput" id="mediaDropZone"
               class="flex flex-col items-center gap-3 h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-green-400 hover:bg-green-50/30 transition justify-center w-full relative">
          <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          <span class="text-sm text-gray-400" id="mediaUploadText">Click hoặc kéo thả file ảnh/video vào đây</span>
        </label>
        <input type="file" id="mediaUploadInput" accept="image/*,video/*" class="hidden">
        <button type="button" id="btnUploadMediaFile"
                class="mt-3 w-full py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition">
          Tải lên
        </button>
        <div id="uploadResult" class="mt-3 text-sm"></div>
      </div>

      {{-- Library Tab --}}
      <div id="tab-library" class="media-tab-pane hidden">
        <div id="personalMediaList" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <p class="col-span-full text-center text-sm text-gray-400 py-8">Đang tải...</p>
        </div>
      </div>

      {{-- Shared Tab --}}
      <div id="tab-shared" class="media-tab-pane hidden">
        <div id="sharedMediaList" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <p class="col-span-full text-center text-sm text-gray-400 py-8">Đang tải...</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════ CUTE TOAST NOTIFICATION ══════ --}}
<div id="cuteToast" class="fixed top-24 right-5 z-[200] transform transition-all duration-500 translate-x-[150%] opacity-0 flex items-center gap-4 bg-white px-5 py-4 rounded-[1.25rem] shadow-2xl shadow-green-900/10 border-b-4 border-emerald-400">
  <div id="cuteToastIcon" class="text-3xl animate-bounce">✨📝</div>
  <div>
    <h4 id="cuteToastTitle" class="font-bold text-emerald-600 text-sm mb-0.5">Xong rồi nè!</h4>
    <p id="cuteToastMsg" class="text-xs text-gray-500 font-medium leading-relaxed">Đã copy nội dung vào trình soạn thảo.</p>
  </div>
</div>

<style>
  .ck-editor__editable { min-height: 420px; border: 0 !important; }
  .ck.ck-toolbar { border: 0 !important; border-bottom: 1px solid #f3f4f6 !important; background: #fafafa !important; }
  .ck.ck-editor { border: 0 !important; }
  .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) { border: 0 !important; }
  .media-tab-btn.active-tab { color: #16a34a; border-color: #16a34a; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
let myEditor;
const postId = {{ isset($post) ? $post->id : 'null' }};
const uploadMediaUrl = "{{ route('contributor.media.upload') }}";

// CKEditor init
ClassicEditor.create(document.querySelector('#editor'), {
    toolbar: {
        items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'alignment', '|',
            'link', 'blockQuote', '|',
            'bulletedList', 'numberedList', '|',
            'outdent', 'indent', '|',
            'insertTable', '|',
            'uploadImage', 'mediaEmbed', '|',
            'undo', 'redo', '|',
            'sourceEditing'
        ],
        shouldNotGroupWhenFull: true
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Đoạn văn', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Tiêu đề 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Tiêu đề 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Tiêu đề 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Tiêu đề 4', class: 'ck-heading_heading4' },
        ]
    },
    table: {
        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties' ]
    },
    image: {
        toolbar: [ 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|', 'toggleImageCaption', 'imageTextAlternative' ]
    },
    language: 'vi',
    ckfinder: { uploadUrl: uploadMediaUrl + '?_token={{ csrf_token() }}' }
}).then(e => { myEditor = e; }).catch(console.error);


// Thumbnail drag-drop preview
document.getElementById('thumbnail').addEventListener('change', function(e) {
    const file = e.target.files[0]; if(!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('thumbPreview').src = ev.target.result;
        document.getElementById('thumbPreviewWrap').classList.remove('hidden');
        document.getElementById('thumbDropzone').classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
document.getElementById('btnRemoveThumb')?.addEventListener('click', function() {
    document.getElementById('thumbnail').value = '';
    document.getElementById('thumbPreviewWrap').classList.add('hidden');
    document.getElementById('thumbDropzone').classList.remove('hidden');
});

// Paste image from clipboard to thumbnail
document.addEventListener('paste', function(e) {
    // Không can thiệp nếu user đang gõ trong input text hoặc CKEditor
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.closest('.ck-editor')) {
        // Nhưng nếu là ô input type="file" thì vẫn nên bỏ qua vì nó không nhận paste text thông thường
        if (e.target.type !== 'file') return;
    }
    
    // Kiểm tra xem có file ảnh trong clipboard không
    if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
        let file = e.clipboardData.files[0];
        if (file.type.startsWith('image/')) {
            e.preventDefault();
            // Gán file vào input thumbnail
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('thumbnail').files = dataTransfer.files;
            
            // Kích hoạt sự kiện thay đổi để hiện Preview
            document.getElementById('thumbnail').dispatchEvent(new Event('change'));
        }
    }
});

// Cute Toast Function
function showCuteToast(type, title, msg) {
    const toast = document.getElementById('cuteToast');
    const icon = document.getElementById('cuteToastIcon');
    const tTitle = document.getElementById('cuteToastTitle');
    const tMsg = document.getElementById('cuteToastMsg');
    
    // Setup Icon & Colors
    if (type === 'success') {
        icon.innerHTML = '✨📝';
        tTitle.className = 'font-bold text-emerald-600 text-sm mb-0.5';
        toast.className = 'fixed top-24 right-5 z-[200] transform transition-all duration-500 flex items-center gap-4 bg-white px-5 py-4 rounded-[1.25rem] shadow-2xl shadow-green-900/10 border-b-4 border-emerald-400 translate-x-[150%] opacity-0';
    } else {
        icon.innerHTML = '😿💔';
        tTitle.className = 'font-bold text-red-600 text-sm mb-0.5';
        toast.className = 'fixed top-24 right-5 z-[200] transform transition-all duration-500 flex items-center gap-4 bg-white px-5 py-4 rounded-[1.25rem] shadow-2xl shadow-red-900/10 border-b-4 border-red-400 translate-x-[150%] opacity-0';
    }
    
    tTitle.textContent = title;
    tMsg.textContent = msg;
    
    // Float It In
    requestAnimationFrame(() => {
        setTimeout(() => toast.classList.remove('translate-x-[150%]', 'opacity-0'), 100);
    });
    
    // Fly It Out after 4 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-[150%]', 'opacity-0');
    }, 4000);
}

// Import Word
const btnImportWord = document.getElementById('btnImportWord');
btnImportWord.addEventListener('click', () => document.getElementById('wordFileInput').click());

document.getElementById('wordFileInput').addEventListener('change', function(e) {
    if(!e.target.files.length) return;
    
    // Magic Loading State
    const originalText = btnImportWord.innerHTML;
    btnImportWord.innerHTML = '⏳ Đang hút chữ...';
    btnImportWord.classList.add('opacity-70', 'pointer-events-none', 'animate-pulse');
    
    const fd = new FormData(); 
    fd.append('document', e.target.files[0]); 
    fd.append('_token', '{{ csrf_token() }}');
    
    fetch("{{ route('contributor.posts.import-word') }}", { method:'POST', body:fd })
    .then(r => r.json())
    .then(data => {
        if(data.title) document.getElementById('title').value = data.title;
        if(data.content && myEditor) myEditor.setData(myEditor.getData() + data.content);
        
        showCuteToast('success', 'Hút chữ thành công! 🎉', 'Mọi thứ đã ngoan ngoãn chui vào khung soạn thảo.');
    }).catch(() => {
        showCuteToast('error', 'Ôi hỏng! 😿', 'Có gì đó sai sai, không thể bóc tách file Word này.');
    }).finally(() => {
        // Reset Magic Component
        btnImportWord.innerHTML = originalText;
        btnImportWord.classList.remove('opacity-70', 'pointer-events-none', 'animate-pulse');
        e.target.value = ''; // Reset the input to allow selecting same file again
    });
});

// AI Generate
document.getElementById('btnAIGenerate').addEventListener('click', () => {
    document.getElementById('aiPromptTarget').classList.toggle('hidden');
});
document.getElementById('btnExecuteAI').addEventListener('click', function() {
    const prompt = document.getElementById('aiPromptInput').value;
    if(!prompt) { alert('Vui lòng nhập chủ đề'); return; }
    this.disabled = true; this.textContent = 'Đang xử lý...';
    const fd = new FormData(); fd.append('prompt', prompt); fd.append('_token', '{{ csrf_token() }}');
    fetch("{{ route('ai.generate-post') }}", { method:'POST', body:fd })
    .then(r => r.json()).then(data => {
        if(data.content && myEditor) myEditor.setData(myEditor.getData() + data.content);
        this.disabled = false; this.textContent = 'Tạo nội dung';
        document.getElementById('aiPromptTarget').classList.add('hidden');
    }).catch(() => { alert('Lỗi AI'); this.disabled = false; this.textContent = 'Tạo nội dung'; });
});

// Media tabs
document.querySelectorAll('.media-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.media-tab-btn').forEach(b => b.classList.remove('active-tab'));
        document.querySelectorAll('.media-tab-pane').forEach(p => p.classList.add('hidden'));
        this.classList.add('active-tab');
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        if(this.dataset.tab === 'library') loadMedia("{{ route('contributor.media.personal') }}", 'personalMediaList');
        if(this.dataset.tab === 'shared') loadMedia("{{ route('contributor.media.shared') }}", 'sharedMediaList');
    });
});

// Xử lý kéo thả (Drag and Drop)
const dropZone = document.getElementById('mediaDropZone');
const fi = document.getElementById('mediaUploadInput');
const uploadText = document.getElementById('mediaUploadText');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});
function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.add('border-green-400', 'bg-green-50'));
});
['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-green-400', 'bg-green-50'));
});

dropZone.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    if (dt.files && dt.files.length > 0) {
        fi.files = dt.files;
        fi.dispatchEvent(new Event('change'));
    }
});

fi.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        uploadText.innerHTML = `<span class="text-green-600 font-medium tracking-tight whitespace-normal break-all line-clamp-2 px-4 shadow-sm">Đã chọn: ${this.files[0].name}</span>`;
    } else {
        uploadText.innerHTML = 'Click hoặc kéo thả file ảnh/video vào đây';
    }
});

// Upload media
document.getElementById('btnUploadMediaFile').addEventListener('click', function() {
    if(!fi.files.length) { alert('Vui lòng chọn 1 file trước khi tải lên!'); return; }
    
    // Đổi trạng thái nút
    const originalText = this.innerHTML;
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split animate-spin me-2"></i> Đang tải...';
    
    const fd = new FormData(); fd.append('upload', fi.files[0]); fd.append('_token', '{{ csrf_token() }}');
    fetch(uploadMediaUrl, { method:'POST', body:fd })
    .then(r => r.json()).then(data => {
        this.disabled = false; this.innerHTML = originalText;
        if(data.url) {
            document.getElementById('uploadResult').innerHTML = `<p class="text-green-600 text-xs mt-2">✅ Upload thành công!</p>`;
            insertMediaToEditor(data.url, data.type);
            fi.value = ''; // Reset input
            fi.dispatchEvent(new Event('change'));
        } else { alert('Upload thất bại: ' + (data.error?.message || 'Không rõ lỗi')); }
    }).catch(() => {
        this.disabled = false; this.innerHTML = originalText;
        alert('Upload thất bại, mã mạng lỗi'); 
    });
});

// Paste event cho Media Modal
window.addEventListener('paste', function(e) {
    const modal = document.getElementById('mediaModal');
    // Chỉ xử lý nếu Modal đang mở và hiển thị
    if (modal && !modal.classList.contains('hidden')) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image/') === 0 || items[i].type.indexOf('video/') === 0) {
                const file = items[i].getAsFile();
                // Gán file vào ô input hidden
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('mediaUploadInput').files = dt.files;
                
                // Hiển thị trạng thái
                document.getElementById('uploadResult').innerHTML = `<p class="text-blue-600 text-xs">⏳ Đang tự động tải lên thao tác Dán: ${file.name}...</p>`;
                
                // Tự động bấm nút tải lên
                document.getElementById('btnUploadMediaFile').click();
                break; // Chỉ xử lý file đầu tiên
            }
        }
    }
});

function insertMediaToEditor(url, type) {
    if(!myEditor) return;
    const html = type === 'image'
        ? `<figure class="image"><img src="${url}" alt="media"></figure>`
        : `<figure class="media"><video controls src="${url}"></video></figure>`;
    const view = myEditor.data.processor.toView(html);
    myEditor.model.insertContent(myEditor.data.toModel(view), myEditor.model.document.selection);
    document.getElementById('mediaModal').classList.add('hidden');
}

function loadMedia(url, containerId) {
    const c = document.getElementById(containerId);
    c.innerHTML = '<p class="col-span-full text-center text-sm text-gray-400 py-8">Đang tải...</p>';
    fetch(url).then(r => r.json()).then(data => {
        if(!data || !data.length) { c.innerHTML = '<p class="col-span-full text-center text-sm text-gray-400 py-8">Không có file nào.</p>'; return; }
        c.innerHTML = data.map(m => `
            <div class="rounded-xl overflow-hidden border border-gray-100 hover:border-green-300 cursor-pointer group transition" onclick="insertMediaToEditor('${m.url}','${m.file_type}')">
                ${m.file_type === 'image'
                    ? `<img src="${m.url}" class="w-full h-24 object-cover">`
                    : `<div class="w-full h-24 bg-gray-800 flex items-center justify-center text-white text-2xl">▶</div>`}
                <p class="text-xs text-gray-500 truncate px-2 py-1">${m.file_name}</p>
            </div>`).join('');
    }).catch(() => c.innerHTML = '<p class="col-span-full text-center text-sm text-red-400 py-8">Lỗi tải dữ liệu.</p>');
}

// Confirm submit custom modal
document.getElementById('btnSubmitReview').addEventListener('click', function(e) {
    const form = document.getElementById('postForm');
    if (!form.checkValidity()) return; // Để trình duyệt hiện tooltip báo lỗi nếu thiếu field required
    e.preventDefault();

    // Tự động nhận diện tên tác giả (nếu người dùng gõ ở cuối bài mà quên điền ô Tác giả)
    const authorInput = document.getElementById('source_author');
    if (!authorInput.value.trim() && typeof myEditor !== 'undefined' && myEditor) {
        try {
            const content = myEditor.getData();
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');
            
            // Xóa các node trống (chứa toàn khoảng trắng hoặc &nbsp;) ở cuối
            const elements = Array.from(doc.body.children);
            for (let i = elements.length - 1; i >= 0; i--) {
                const el = elements[i];
                // Loại bỏ &nbsp; và khoảng trắng để kiểm tra
                const text = el.textContent.replace(/\u00a0/g, ' ').trim();
                
                if (text.length > 0) {
                    // Dấu hiệu nhận biết: Dài dưới 60 ký tự + (In đậm hoặc Căn phải hoặc có dấu gạch ngang chữ hoa)
                    const isShort = text.length > 2 && text.length <= 60;
                    const html = el.innerHTML.toLowerCase();
                    const isBold = el.querySelector('strong, b') !== null || el.tagName === 'STRONG' || el.tagName === 'B' || html.includes('<strong>') || html.includes('<b>');
                    const isRightAligned = el.style.textAlign === 'right' || html.includes('text-align: right') || el.classList.contains('text-right');
                    const hasHyphen = text.includes('-');
                    
                    if (isShort && (isBold || isRightAligned || hasHyphen)) {
                        // Tự động điền tên tác giả
                        authorInput.value = text;
                        // Làm nổi bật ô input một chút để người dùng nhận ra
                        authorInput.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-50');
                        setTimeout(() => authorInput.classList.remove('ring-2', 'ring-emerald-400', 'bg-emerald-50'), 2000);
                        
                        // Xóa element này khỏi nội dung editor để không bị lặp
                        el.remove();
                        myEditor.setData(doc.body.innerHTML);
                    }
                    break; // Dừng lại ở block văn bản thực sự cuối cùng
                }
            }
        } catch(err) { console.error('Auto-extract author failed', err); }
    }

    document.getElementById('confirmSubmitModal').classList.remove('hidden');
});

function processSubmit() {
    document.getElementById('confirmSubmitModal').classList.add('hidden');
    const sourceAuthor = document.getElementById('source_author')?.value?.trim();
    const form = document.getElementById('postForm');
    
    let actionInput = document.getElementById('hiddenActionInput');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.id = 'hiddenActionInput';
        form.appendChild(actionInput);
    }
    actionInput.value = 'pending';

    if (!sourceAuthor) {
        // Hiện thông báo và KHÔNG submit, để người dùng có thời gian điền
        showAuthorToast();
    } else {
        form.submit();
    }
}

function showAuthorToast() {
    const msgs = [
        '😱 Ôi bạn ơi! Quên điền tên rồi — ai nhận nhuận bút đây?!',
        '🫢 Ủa bạn ơi! Bài hay vậy mà không để tên à? Nhuận bút ai lấy!',
        '🤦 Bạn ơi bạn ơi! Quên điền tên tác giả rồi kìa — điền nhanh lên!',
        '😅 Khoan! Bài này của ai vậy? Điền tên vào để nhận nhuận bút nha bạn~',
    ];
    const msg = msgs[Math.floor(Math.random() * msgs.length)];
    const emoji = msg.split(' ')[0];
    const text = msg.substring(msg.indexOf(' ') + 1);

    document.getElementById('authorToast')?.remove();

    const toast = document.createElement('div');
    toast.id = 'authorToast';
    toast.innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <span style="font-size:1.5rem;line-height:1.2;">${emoji}</span>
            <div style="flex:1;">
                <p style="font-weight:700;font-size:0.85rem;margin:0 0 4px;">${text}</p>
                <p style="font-size:0.75rem;color:#a7f3d0;margin:0;">Bạn có 3 giây để điền — hoặc bài gửi không tên 🤷</p>
            </div>
            <button onclick="document.getElementById('authorToast').remove()" style="background:none;border:none;color:#6ee7b7;cursor:pointer;font-size:1.1rem;line-height:1;padding:0;">✕</button>
        </div>`;
    Object.assign(toast.style, {
        position:'fixed', bottom:'24px', right:'24px', zIndex:'9999',
        background:'linear-gradient(135deg,#065f46,#047857)',
        color:'#fff', padding:'16px 20px', borderRadius:'16px',
        boxShadow:'0 20px 40px rgba(0,0,0,0.25)', maxWidth:'380px',
        borderLeft:'4px solid #34d399',
        animation:'slideInToast 0.4s cubic-bezier(.23,1,.32,1)',
    });
    if (!document.getElementById('_toast_style')) {
        const s = document.createElement('style');
        s.id = '_toast_style';
        s.textContent = '@keyframes slideInToast{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}';
        document.head.appendChild(s);
    }
    document.body.appendChild(toast);
    document.getElementById('source_author')?.focus();
    setTimeout(() => toast?.remove(), 4000);
}

// Auto-save
if(postId) {
    setInterval(() => {
        if(!myEditor) return; const content = myEditor.getData(); if(!content) return;
        const fd = new FormData(); fd.append('content', content); fd.append('_token', '{{ csrf_token() }}');
        fetch(`/contributor/posts/${postId}/autosave`, { method:'POST', body:fd })
        .then(r => r.json()).then(() => {
            const s = document.getElementById('autoSaveStatus');
            const now = new Date();
            document.getElementById('autoSaveText').textContent = `Lưu lúc ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
            s.classList.remove('hidden');
            setTimeout(() => s.classList.add('hidden'), 5000);
        }).catch(console.error);
    }, 30000);
}
</script>
@endpush
