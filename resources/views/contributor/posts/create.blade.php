@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50/50 dark:bg-zinc-950 py-6 transition-colors">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header Bar --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">
          {{ isset($post) ? '✏️ Chỉnh sửa bài viết' : '📝 Viết bài mới' }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-zinc-400 mt-0.5">Điền đầy đủ thông tin và nội dung bài viết</p>
      </div>
      <div class="flex items-center gap-2">
        <span id="autoSaveStatus" class="text-xs text-gray-400 dark:text-zinc-500 hidden">
          <span class="inline-flex items-center gap-1 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 px-3 py-1.5 rounded-full border border-green-200 dark:border-green-500/20">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span id="autoSaveText">Đã lưu tự động</span>
          </span>
        </span>
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-800 transition shadow-sm">
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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 overflow-hidden transition-colors relative group focus-within:border-emerald-500/50 dark:focus-within:border-emerald-500/50">
            <label for="title" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
              Tiêu đề bài viết <span class="text-red-500">*</span>
            </label>
            <input type="text" id="title" name="title" required
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="Nhập tiêu đề hấp dẫn, rõ ràng..."
                   class="w-full text-xl font-semibold text-gray-900 dark:text-zinc-100 border-0 outline-none placeholder:text-gray-300 dark:placeholder:text-zinc-600 bg-transparent ring-0 focus:ring-0 p-0 m-0">
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- AI Prompt (ẩn mặc định) --}}
          <div id="aiPromptTarget" class="hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/40 dark:to-purple-900/20 border border-indigo-200 dark:border-indigo-500/20 rounded-2xl p-4 overflow-hidden transition-colors">
              <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-400 mb-2">🤖 AI Tự động viết</p>
              <div class="flex gap-2">
                <input type="text" id="aiPromptInput"
                       placeholder="Nhập chủ đề, AI sẽ tạo nháp bài viết cho bạn..."
                       class="flex-1 border border-indigo-200 dark:border-indigo-500/30 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-400 dark:focus:border-indigo-500/50 bg-white dark:bg-zinc-950/50 dark:text-zinc-200 transition-colors">
                <button type="button" id="btnExecuteAI"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition">
                  Tạo nội dung
                </button>
              </div>
              <p class="text-xs text-indigo-400 dark:text-indigo-500/80 mt-1.5">AI sẽ tạo bản nháp, bạn chỉnh lại trước khi đăng.</p>
            </div>
          </div>

          {{-- Editor --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm overflow-hidden transition-colors">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/60 dark:bg-zinc-950/50">
              <span class="text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                Nội dung bài viết <span class="text-red-500">*</span>
              </span>
              <div class="flex items-center gap-2">
                <button type="button" id="btnImportWord"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-xl border border-blue-200 dark:border-blue-500/20 shadow-sm transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  Import Word
                </button>
                <input type="file" id="wordFileInput" accept=".docx,.doc" class="hidden">

                <button type="button" id="btnAIGenerate" onclick="document.getElementById('aiPromptTarget').classList.toggle('hidden'); setTimeout(() => document.getElementById('aiPromptInput').focus(), 100);"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 hover:bg-purple-100 dark:hover:bg-purple-500/20 rounded-xl border border-purple-200 dark:border-purple-500/20 shadow-sm transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                  AI viết
                </button>

                <button type="button" onclick="document.getElementById('mediaModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-4 mt-5 transition-colors">
            <div class="flex flex-wrap items-center gap-3">
              <button type="submit" name="action" value="draft" id="btnSaveDraft"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-gray-700 dark:text-zinc-300 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-xl border border-gray-200 dark:border-zinc-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                {{ isset($post) && $post->status === 'published' ? 'Lưu cập nhật' : 'Lưu nháp' }}
              </button>

              @if(in_array(auth()->user()->role ?? '', ['admin', 'editor']))
              <button type="button" id="btnApprovePost" onclick="window.confirmCustomAction('Bạn có chắc muốn {{ isset($post) && $post->status==='published' ? 'CẬP NHẬT' : 'DUYỆT XUẤT BẢN' }} bài viết này?', () => { let a = document.getElementById('hiddenActionInput'); if(!a) { a = document.createElement('input'); a.type='hidden'; a.name='action'; a.id='hiddenActionInput'; document.getElementById('postForm').appendChild(a); } a.value='approve'; document.getElementById('postForm').submit(); })"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-xl shadow-lg shadow-blue-200 dark:shadow-none transition">
                <i class="bi bi-check-all text-lg"></i> {{ isset($post) && $post->status==='published' ? 'Lưu & Đăng' : 'Duyệt bài' }}
              </button>
              
              <button type="button" id="btnRejectPost" onclick="window.confirmCustomAction('Bạn có chắc muốn TỪ CHỐI bài viết này?', () => { let a = document.getElementById('hiddenActionInput'); if(!a) { a = document.createElement('input'); a.type='hidden'; a.name='action'; a.id='hiddenActionInput'; document.getElementById('postForm').appendChild(a); } a.value='reject'; document.getElementById('postForm').submit(); })"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 rounded-xl shadow-lg shadow-rose-200 dark:shadow-none transition">
                <i class="bi bi-x-circle text-lg"></i> Từ chối
              </button>

              <div class="h-8 w-px bg-gray-200 dark:bg-zinc-700 hidden xl:block mx-1"></div>

              <button type="button" onclick="openPlagiarismModal()"
                 class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm transition">
                <i class="bi bi-shield-check text-indigo-500 text-lg"></i> Check Đạo Văn (Nội bộ)
              </button>
              @else
              @if(!isset($post) || !in_array($post->status, ['pending', 'published']))
              <button type="submit" name="action" value="pending" id="btnSubmitReview"
                      class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-xl shadow-lg shadow-green-200 dark:shadow-none transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Gửi bài chờ duyệt
              </button>
              @endif
              @endif

              <p class="text-xs text-gray-400 dark:text-zinc-500 sm:ml-auto">
                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tự động lưu mỗi 30 giây
              </p>
            </div>
          </div>
        </div>

        {{-- ═══ RIGHT: Sidebar ═══ --}}
        <div class="w-80 shrink-0 space-y-5">

          {{-- Chuyên mục --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 overflow-hidden transition-colors">
            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              Chuyên mục <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select name="category_id" id="category_id" required
                      class="w-full border border-gray-200 dark:border-zinc-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 dark:text-zinc-100 transition appearance-none cursor-pointer">
                <option value="">-- Chọn chuyên mục --</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                  {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name ?? 'Category ' . $cat->id }}
                </option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500 dark:text-zinc-400">
                <i class="bi bi-chevron-down text-xs"></i>
              </div>
            </div>
            @error('category_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- Đặt làm Tiêu Điểm / Nổi bật (Chỉ dành cho Ban Biên tập) --}}
          @if(in_array(auth()->user()->role ?? '', ['admin', 'editor']))
          <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/40 dark:to-blue-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-500/20 shadow-sm p-5 relative overflow-hidden transition-colors">
            <div class="absolute -right-4 -bottom-4 text-indigo-200 dark:text-indigo-500/10 opacity-20 dark:opacity-100 pointer-events-none">
              <i class="bi bi-star-fill" style="font-size: 6rem;"></i>
            </div>
            <label class="flex items-center gap-3 cursor-pointer relative z-10 group">
              <div class="relative flex items-center justify-center w-5 h-5 shrink-0">
                  <input type="checkbox" name="is_featured" value="1"
                         {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }}
                         class="w-5 h-5 rounded-md border-2 border-indigo-200 dark:border-indigo-500/50 text-indigo-600 focus:ring-indigo-500 bg-white dark:bg-zinc-950 transition shadow-sm cursor-pointer">
              </div>
              <span class="text-sm font-bold text-indigo-800 dark:text-indigo-300 tracking-wide uppercase"><i class="bi bi-star me-1"></i> Đặt làm Bài Tiêu biểu</span>
            </label>
            <p class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-2 relative z-10 font-medium">✨ Bài viết này sẽ được ưu tiên xuất hiện nhẹ nhàng trên banner khổng lồ ở Trang chủ.</p>
          </div>
          @endif

          {{-- Nhuận bút (Chỉ dành cho Ban Biên tập) --}}
          @if(in_array(auth()->user()->role ?? '', ['admin', 'editor']) && isset($royaltyRates))
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 shadow-sm p-5 relative overflow-hidden transition-colors">
            <div class="absolute -right-4 -top-4 text-emerald-200 dark:text-emerald-500/10 opacity-30 dark:opacity-100 pointer-events-none">
              <i class="bi bi-wallet2" style="font-size: 5rem;"></i>
            </div>
            <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-3 relative z-10">
              <i class="bi bi-cash-coin me-1"></i> Định mức Nhuận bút
            </label>
            
            <div class="space-y-3 relative z-10">
              <div>
                <label class="block text-xs text-gray-600 dark:text-zinc-400 mb-1">Thể loại bài viết</label>
                <div class="relative">
                  <select name="royalty_rate_id" id="royalty_rate_id"
                          class="w-full border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 dark:text-zinc-200 transition appearance-none cursor-pointer">
                    <option value="">-- Bỏ qua (Không tính) --</option>
                    @php $currentGroup = ''; @endphp
                    @foreach($royaltyRates as $rate)
                      @if($currentGroup != $rate->group_name)
                        @if($currentGroup != '') </optgroup> @endif
                        <optgroup label="{{ $rate->group_name }}">
                        @php $currentGroup = $rate->group_name; @endphp
                      @endif
                      <option value="{{ $rate->id }}" data-amount="{{ $rate->amount }}"
                        {{ old('royalty_rate_id', $post->royalty_rate_id ?? '') == $rate->id ? 'selected' : '' }}>
                        {{ $rate->name }} ({{ number_format($rate->amount) }}đ)
                      </option>
                    @endforeach
                    @if($currentGroup != '') </optgroup> @endif
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-emerald-500 dark:text-emerald-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                  </div>
                </div>
              </div>
              
              <div>
                <label class="block text-xs text-gray-600 dark:text-zinc-400 mb-1">Số lượng Ảnh (Chiết tính)</label>
                <div class="flex items-center gap-2">
                  <input type="number" name="image_count" id="image_count" min="0" value="{{ old('image_count', $post->image_count ?? 0) }}"
                         class="w-20 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 bg-white dark:bg-zinc-950/50 dark:text-zinc-200 transition text-center">
                  <span class="text-xs text-gray-500 dark:text-zinc-400 cursor-pointer hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors" onclick="document.getElementById('image_count').value = (document.getElementById('editor').value.match(/<img/g) || []).length">
                    <i class="bi bi-arrow-repeat"></i> Đếm tự động
                  </span>
                </div>
                <p class="text-[10px] text-emerald-600 dark:text-emerald-500 mt-1">* 10.000đ / ảnh</p>
              </div>
              
              <div class="pt-2 mt-2 border-t border-emerald-200 dark:border-emerald-500/20 flex justify-between items-center bg-white/50 dark:bg-black/20 px-3 py-2 rounded-lg">
                <span class="text-xs font-semibold text-gray-600 dark:text-zinc-400">Thành tiền:</span>
                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400" id="royaltyTotalPreview">0 đ</span>
              </div>
            </div>
          </div>
          
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const rateSelect = document.getElementById('royalty_rate_id');
              const imageInput = document.getElementById('image_count');
              const totalPreview = document.getElementById('royaltyTotalPreview');
              
              function updateRoyaltyPreview() {
                if(!rateSelect || !imageInput || !totalPreview) return;
                const option = rateSelect.options[rateSelect.selectedIndex];
                const amount = option && option.value ? parseInt(option.getAttribute('data-amount') || 0) : 0;
                const images = parseInt(imageInput.value || 0);
                const total = amount + (images * 10000);
                totalPreview.textContent = total.toLocaleString('vi-VN') + ' đ';
              }
              
              if(rateSelect) rateSelect.addEventListener('change', updateRoyaltyPreview);
              if(imageInput) imageInput.addEventListener('input', updateRoyaltyPreview);
              
              // Run once on load
              setTimeout(updateRoyaltyPreview, 500);
            });
          </script>
          @endif

          {{-- Thumbnail --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 overflow-hidden transition-colors">
            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              Ảnh đại diện (Thumbnail)
            </label>

            {{-- Preview --}}
            <div id="thumbPreviewWrap" class="{{ (isset($post) && $post->thumbnail) ? '' : 'hidden' }} mb-3 relative group">
              <img id="thumbPreview"
                   src="{{ (isset($post) && $post->thumbnail) ? asset('storage/' . $post->thumbnail) : '' }}"
                   class="w-full h-44 object-cover rounded-xl border border-gray-200 dark:border-zinc-700 block">
              <button type="button" id="btnRemoveThumb"
                      class="absolute top-2 right-2 bg-white/90 dark:bg-zinc-800/90 hover:bg-red-50 dark:hover:bg-red-500/20 text-red-500 rounded-full w-7 h-7 flex items-center justify-center shadow text-xs border border-red-100 dark:border-red-500/30 opacity-0 group-hover:opacity-100 transition">✕</button>
            </div>

            {{-- Drop zone --}}
            <label for="thumbnail" id="thumbDropzone"
                   class="{{ (isset($post) && $post->thumbnail) ? 'hidden' : '' }} flex flex-col items-center justify-center gap-2 h-36 border-2 border-dashed border-gray-200 dark:border-zinc-700 rounded-xl cursor-pointer hover:border-emerald-400 dark:hover:border-emerald-500/50 hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition">
              <svg class="w-8 h-8 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span class="text-xs text-gray-400 dark:text-zinc-500 text-center">Click, kéo thả, hoặc ấn Ctrl+V (Paste)<br><span class="text-gray-300 dark:text-zinc-600">để dán ảnh bìa vào đây</span></span>
            </label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="hidden">
            @error('thumbnail')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
          </div>

          {{-- Tên tác giả / Nguồn --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 overflow-hidden transition-colors">
            <label for="source_author" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              Tác giả / Nguồn
            </label>
            <input type="text" name="source_author" id="source_author"
                   value="{{ old('source_author', $post->source_author ?? '') }}"
                   placeholder="VD: Cẩm Thiều - TV"
                   class="w-full border border-gray-200 dark:border-zinc-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 dark:text-zinc-100 transition">
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1.5">Hiển thị in đậm cuối bài viết</p>
          </div>

          {{-- Người chụp ảnh --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 overflow-hidden transition-colors" x-data="{
              sameAsAuthor: {{ old('photographer_same', (!isset($post) || (isset($post) && $post->photographer === $post->source_author)) ? 'true' : 'false') }},
              photographerVal: '{{ old('photographer', $post->photographer ?? '') }}',
              authorVal: '{{ old('source_author', $post->source_author ?? '') }}',
              init() {
                  const authorInput = document.getElementById('source_author');
                  if (authorInput) {
                      authorInput.addEventListener('input', (e) => {
                          this.authorVal = e.target.value;
                          if (this.sameAsAuthor) this.photographerVal = e.target.value;
                      });
                  }
                  
                  this.$watch('sameAsAuthor', (val) => {
                      if (val) {
                          this.photographerVal = document.getElementById('source_author')?.value || '';
                      }
                  });
              }
          }">
            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              <i class="bi bi-camera"></i> Người chụp ảnh
            </label>
            <label class="flex items-center gap-2 mb-2.5 cursor-pointer select-none group">
              <div class="relative flex items-center justify-center w-5 h-5 shrink-0">
                  <input type="checkbox" x-model="sameAsAuthor"
                         class="w-5 h-5 rounded-md border-2 border-gray-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-zinc-950 transition cursor-pointer">
              </div>
              <span class="text-xs text-gray-600 dark:text-zinc-400 font-medium group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Cùng tác giả bài viết</span>
            </label>
            <input type="text" name="photographer" id="photographer"
                   x-model="photographerVal"
                   :readonly="sameAsAuthor"
                   :class="sameAsAuthor ? 'bg-gray-50 dark:bg-zinc-900/50 text-gray-400 dark:text-zinc-600 cursor-not-allowed border-gray-200 dark:border-zinc-800' : 'bg-white dark:bg-zinc-950/50 text-gray-900 dark:text-zinc-100 border-gray-200 dark:border-zinc-800'"
                   placeholder="VD: Nguyễn Văn A"
                   class="w-full border rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 transition">
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1.5">Dùng để tính nhuận bút ảnh riêng biệt</p>
          </div>

          {{-- Tips card --}}
          <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/10 rounded-2xl border border-amber-100 dark:border-amber-500/20 p-5 transition-colors">
            <p class="text-xs font-bold text-amber-700 dark:text-amber-500 mb-2">💡 Mẹo viết bài hay</p>
            <ul class="space-y-1.5 text-xs text-amber-600 dark:text-amber-500/80">
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
  <div class="absolute inset-0 bg-gray-900/60 dark:bg-zinc-900/90 backdrop-blur-sm transition-opacity" onclick="document.getElementById('confirmSubmitModal').classList.add('hidden')"></div>
  <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-100 opacity-100 border border-gray-100 dark:border-zinc-800">
    <div class="flex items-start gap-4 mb-2">
      <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
      </div>
      <div class="pt-1">
        <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100 leading-none">Xác nhận gửi bài?</h3>
        <p class="text-sm text-gray-500 dark:text-zinc-400 mt-2 leading-relaxed">Ban Biên tập sẽ nhận được bài viết này. <strong class="text-gray-700 dark:text-zinc-300">Bạn sẽ không thể tự chỉnh sửa</strong> trong lúc chờ duyệt.</p>
      </div>
    </div>
    
    <div class="flex items-center justify-end gap-3 mt-6">
      <button type="button" onclick="document.getElementById('confirmSubmitModal').classList.add('hidden')"
              class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition">
        Chưa, quay lại
      </button>
      <button type="button" onclick="processSubmit()"
              class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-green-600 dark:from-emerald-600 dark:to-green-700 hover:from-emerald-600 hover:to-green-700 rounded-xl shadow-lg shadow-emerald-200 dark:shadow-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 transition transform active:scale-95">
        Đồng ý gửi
      </button>
    </div>
  </div>
</div>

{{-- ══════ Media Library Modal ══════ --}}
<div id="mediaModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-black/50 dark:bg-zinc-900/80 backdrop-blur-sm" onclick="document.getElementById('mediaModal').classList.add('hidden')"></div>
  <div class="relative bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden transition-colors">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/60 dark:bg-zinc-950/50">
      <h3 class="font-bold text-gray-800 dark:text-zinc-100">🖼️ Media Library</h3>
      <button type="button" onclick="document.getElementById('mediaModal').classList.add('hidden')"
              class="text-gray-400 dark:text-zinc-500 hover:text-gray-700 dark:hover:text-zinc-300 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition text-lg">✕</button>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-100 dark:border-zinc-800 px-6">
      <button type="button" class="media-tab-btn active-tab px-4 py-3 text-sm font-semibold border-b-2 -mb-px transition dark:text-zinc-300" data-tab="upload">📤 Tải lên</button>
      <button type="button" class="media-tab-btn px-4 py-3 text-sm font-medium text-gray-500 dark:text-zinc-500 border-b-2 border-transparent -mb-px hover:text-gray-700 dark:hover:text-zinc-300 transition" data-tab="library" id="library-tab">📁 Của tôi</button>
      <button type="button" class="media-tab-btn px-4 py-3 text-sm font-medium text-gray-500 dark:text-zinc-500 border-b-2 border-transparent -mb-px hover:text-gray-700 dark:hover:text-zinc-300 transition" data-tab="shared" id="shared-tab">🌐 Shared</button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-zinc-900">
      {{-- Upload Tab --}}
      <div id="tab-upload" class="media-tab-pane">
        <label for="mediaUploadInput" id="mediaDropZone"
               class="flex flex-col items-center gap-3 h-40 border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-950/50 hover:bg-green-50/30 dark:hover:bg-green-500/5 hover:border-green-400 dark:hover:border-green-500/50 rounded-2xl cursor-pointer transition justify-center w-full relative">
          <svg class="w-10 h-10 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          <span class="text-sm text-gray-400 dark:text-zinc-500 text-center px-4" id="mediaUploadText">Click hoặc kéo thả file ảnh/video vào đây</span>
        </label>
        <input type="file" id="mediaUploadInput" accept="image/*,video/*" class="hidden">
        <button type="button" id="btnUploadMediaFile"
                class="mt-3 w-full py-2.5 bg-green-600 hover:bg-green-700 dark:bg-green-600 text-white text-sm font-bold rounded-xl transition">
          Tải lên
        </button>
        <div id="uploadResult" class="mt-3 text-sm"></div>
      </div>

      {{-- Library Tab --}}
      <div id="tab-library" class="media-tab-pane hidden">
        <div id="personalMediaList" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <p class="col-span-full text-center text-sm text-gray-400 dark:text-zinc-500 py-8">Đang tải...</p>
        </div>
      </div>

      {{-- Shared Tab --}}
      <div id="tab-shared" class="media-tab-pane hidden">
        <div id="sharedMediaList" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <p class="col-span-full text-center text-sm text-gray-400 dark:text-zinc-500 py-8">Đang tải...</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════ CUTE TOAST NOTIFICATION ══════ --}}
<div id="cuteToast" class="fixed top-24 right-5 z-[200] transform transition-all duration-500 translate-x-[150%] opacity-0 flex items-center gap-4 bg-white dark:bg-zinc-800 px-5 py-4 rounded-[1.25rem] shadow-2xl shadow-green-900/10 border-b-4 border-emerald-400">
  <div id="cuteToastIcon" class="text-3xl animate-bounce">✨📝</div>
  <div>
    <h4 id="cuteToastTitle" class="font-bold text-emerald-600 dark:text-emerald-400 text-sm mb-0.5">Xong rồi nè!</h4>
    <p id="cuteToastMsg" class="text-xs text-gray-500 dark:text-zinc-400 font-medium leading-relaxed">Đã copy nội dung vào trình soạn thảo.</p>
  </div>
</div>

<style>
  .ck-editor__editable { min-height: 420px; border: 0 !important; }
  .ck.ck-toolbar { border: 0 !important; border-bottom: 1px solid #f3f4f6 !important; background: #fafafa !important; }
  .ck.ck-editor { border: 0 !important; }
  .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) { border: 0 !important; }
  .media-tab-btn.active-tab { color: #16a34a; border-color: #16a34a; }
  
  html.dark .ck-editor__editable { background: #18181b !important; color: #f4f4f5 !important; }
  html.dark .ck.ck-toolbar { border-bottom: 1px solid #27272a !important; background: #09090b !important; }
  html.dark .ck.ck-button { color: #d4d4d8 !important; }
  html.dark .ck.ck-button:hover, html.dark .ck.ck-button.ck-on { background: #27272a !important; color: #fff !important; }
  html.dark .media-tab-btn.active-tab { color: #10b981; border-color: #10b981; }
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
}).then(e => { window.myEditor = e; }).catch(console.error);


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
        if(data.content && window.myEditor) window.myEditor.setData(window.myEditor.getData() + data.content);
        
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

// AI Generate — Đã chuyển sang onclick inline + Web-LLM script cuối file

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

// ─── PLAGIARISM CHECKER (PHP NATIVE) ─────────────────────────────────
function openPlagiarismModal() {
    document.getElementById('plagiarismModal').classList.remove('hidden');
}

function closePlagiarismModal() {
    document.getElementById('plagiarismModal').classList.add('hidden');
    document.getElementById('plagiarismResults').innerHTML = '';
    document.getElementById('plagiarismStartScreen').classList.remove('hidden');
    document.getElementById('plagiarismProgressScreen').classList.add('hidden');
    document.getElementById('plagiarismReportScreen').classList.add('hidden');
}

async function startPlagiarismCheck() {
    // 1. Get plain text from editor
    let htmlContent = '';
    if (typeof myEditor !== 'undefined') {
        htmlContent = myEditor.getData();
    } else {
        htmlContent = document.getElementById('editor').value;
    }
    
    // Tạo element tạm để gỡ HTML tag
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = htmlContent;
    const plainText = tempDiv.textContent || tempDiv.innerText || "";
    
    if (plainText.trim().length < 50) {
        alert("Bài viết quá ngắn. Cần ít nhất 50 ký tự để kiểm tra đạo văn.");
        return;
    }

    // 2. Chẻ câu (split by punctuation)
    // Tách bằng ".", "!", "?" nhưng loại trừ viết tắt kiểu "v.v.", "PGS.TS."
    let sentences = plainText.split(/(?<=[.?!])\s+/).map(s => s.trim()).filter(s => s.length > 20);
    
    if (sentences.length === 0) {
        alert("Không tìm thấy câu văn hợp lệ nào dài hơn 20 chữ.");
        return;
    }

    // Giới hạn check tối đa 20 câu để tránh bị server chặn / tốn thời gian
    if (sentences.length > 20) {
        sentences = sentences.slice(0, 20);
    }

    // 3. Chuẩn bị UI
    document.getElementById('plagiarismStartScreen').classList.add('hidden');
    document.getElementById('plagiarismProgressScreen').classList.remove('hidden');
    const progressBar = document.getElementById('plagProgressBar');
    const progressText = document.getElementById('plagProgressText');
    const currentSentenceEl = document.getElementById('plagCurrentSentence');
    
    let plagiarizedCount = 0;
    let totalScore = 0;
    const resultsHtml = [];

    // 4. Process từng câu qua AJAX
    for (let i = 0; i < sentences.length; i++) {
        const sentence = sentences[i];
        
        // Update Tiền trình UI
        const percent = Math.round((i / sentences.length) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = `Đang quét: ${i+1}/${sentences.length} câu (${percent}%)`;
        currentSentenceEl.textContent = sentence.substring(0, 60) + '...';

        try {
            const formData = new FormData();
            formData.append('sentence', sentence);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('/plagiarism-check', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('API Error');

            const result = await response.json();
            
            totalScore += result.similarity;
            if (result.isPlagiarized) plagiarizedCount++;

            // Lưu lại kết quả câu này để show Report
            if (result.similarity > 25) {
                let sourcesList = result.sources.map(src => `<a href="${src.url}" target="_blank" class="block text-blue-600 hover:underline truncate" title="${src.url}">🔹 ${src.similarity}% - ${src.url}</a>`).join('');
                
                resultsHtml.push(`
                    <div class="mb-4 p-4 rounded-xl ${result.isPlagiarized ? 'bg-red-50 border border-red-200' : 'bg-orange-50 border border-orange-200'}">
                        <p class="text-sm font-semibold text-gray-800 mb-2">"${result.sentence}"</p>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 rounded text-xs font-bold ${result.isPlagiarized ? 'bg-red-500 text-white' : 'bg-orange-400 text-white'}">Trùng khớp: ${result.similarity}%</span>
                        </div>
                        <div class="space-y-1 text-xs pl-2 border-l-2 ${result.isPlagiarized ? 'border-red-300' : 'border-orange-300'}">
                            ${sourcesList}
                        </div>
                    </div>
                `);
            }

        } catch (err) {
            console.error("Lỗi khi check câu:", sentence, err);
        }

        // Delay 1s giữa mỗi câu để tránh Rate Limit DuckDuckGo
        await new Promise(r => setTimeout(r, 1000));
    }

    // Hoàn tất!
    progressBar.style.width = '100%';
    progressText.textContent = `Hoàn tất! 100%`;

    // 5. Hiển thị trang kết quả (Report Screen)
    document.getElementById('plagiarismProgressScreen').classList.add('hidden');
    document.getElementById('plagiarismReportScreen').classList.remove('hidden');

    const avgScore = Math.round(totalScore / sentences.length);
    const plagPercent = Math.round((plagiarizedCount / sentences.length) * 100);

    // Xác định mức độ màu sắc
    let colorClass = 'text-green-600';
    let ringClass = 'ring-green-500';
    let statusText = 'An toàn';
    if (plagPercent > 10) { colorClass = 'text-yellow-600'; ringClass = 'ring-yellow-500'; statusText = 'Nguy cơ thấp'; }
    if (plagPercent > 25) { colorClass = 'text-red-600'; ringClass = 'ring-red-500'; statusText = 'Vi phạm bản quyền (>25%)'; }

    document.getElementById('plagScoreUi').className = `text-4xl font-extrabold ${colorClass}`;
    document.getElementById('plagScoreUi').textContent = `${plagPercent}%`;
    document.getElementById('plagStatusUi').textContent = statusText;
    document.getElementById('plagStatusUi').className = `text-sm font-bold mt-1 ${colorClass}`;

    const reportOverview = document.getElementById('plagReportOverview');
    reportOverview.innerHTML = `
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100">
                <p class="text-xs text-gray-500 uppercase">Tổng số câu quét</p>
                <p class="text-xl font-bold text-gray-800">${sentences.length}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100 dark:bg-zinc-800 dark:border-zinc-700">
                <p class="text-xs text-gray-500 dark:text-zinc-400 uppercase">Câu vi phạm (>25%)</p>
                <p class="text-xl font-bold text-red-600">${plagiarizedCount}</p>
            </div>
        </div>
    `;

    if (resultsHtml.length > 0) {
        document.getElementById('plagDetailedResults').innerHTML = resultsHtml.join('');
    } else {
        document.getElementById('plagDetailedResults').innerHTML = `
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-500 mb-3">
                    <i class="bi bi-shield-check text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Tuyệt vời! Không phát hiện câu nào có dấu hiệu sao chép (trùng khớp > 25%).</p>
            </div>
        `;
    }
}
</script>

{{-- ============================== --}}
{{-- PLAGIARISM CHECK MODAL --}}
{{-- ============================== --}}
<div id="plagiarismModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-gray-900/60 dark:bg-zinc-900/90 backdrop-blur-sm transition-opacity" onclick="closePlagiarismModal()"></div>
  
  <!-- Modal Content -->
  <div class="relative bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200 border border-gray-100 dark:border-zinc-800">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-950/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
          <i class="bi bi-shield-check text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100 leading-tight">Mắt Thần E-News</h3>
          <p class="text-xs text-gray-500 dark:text-zinc-400">Công cụ rà soát đạo văn bằng N-Gram & Cosine</p>
        </div>
      </div>
      <button type="button" onclick="closePlagiarismModal()" class="text-gray-400 dark:text-zinc-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 p-2 rounded-xl transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 overflow-y-auto flex-1 bg-white dark:bg-zinc-900">
      
      <!-- SCREEN 1: Bắt đầu -->
      <div id="plagiarismStartScreen" class="text-center py-10">
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/detective-searching-document-4438848-3718485.png" alt="Scan" class="w-48 mx-auto mb-6 opacity-80">
        <h4 class="text-xl font-bold text-gray-800 dark:text-zinc-100 mb-2">Chuẩn bị quét tài liệu</h4>
        <p class="text-gray-500 dark:text-zinc-400 text-sm max-w-md mx-auto mb-6">Hệ thống sẽ bẻ gãy bài viết của bạn thành từng mảnh nhỏ và đối chiếu với hơn 40 tỷ trang web trên Internet.</p>
        <button type="button" onclick="startPlagiarismCheck()" class="inline-flex items-center justify-center gap-2 px-8 py-3 text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-full shadow-lg shadow-indigo-600/30 dark:shadow-none transition transform hover:scale-105">
          <i class="bi bi-radar"></i> Khởi chạy Mắt Thần
        </button>
      </div>

      <!-- SCREEN 2: Tiến trình quét -->
      <div id="plagiarismProgressScreen" class="hidden py-16 text-center max-w-sm mx-auto">
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 rounded-full border-4 border-gray-100 dark:border-zinc-800"></div>
            <div class="absolute inset-0 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin"></div>
            <i class="bi bi-radar absolute inset-0 flex items-center justify-center text-3xl text-indigo-600 dark:text-indigo-400 animate-pulse"></i>
        </div>
        
        <h4 class="text-lg font-bold text-gray-800 dark:text-zinc-100 mb-4" id="plagProgressText">Đang khởi động thuật toán...</h4>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-3 mb-3 overflow-hidden">
          <div id="plagProgressBar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <p class="text-xs text-gray-400 dark:text-zinc-500 italic" id="plagCurrentSentence">Đang bóc tách cú pháp...</p>
      </div>

      <!-- SCREEN 3: Kết quả -->
      <div id="plagiarismReportScreen" class="hidden">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left: Overview Card -->
            <div class="md:w-1/3">
                <div class="bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-2xl p-6 shadow-sm sticky top-0 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-50 dark:bg-zinc-800 border-8 border-gray-100 dark:border-zinc-700 mb-4">
                        <span id="plagScoreUi" class="text-4xl font-extrabold text-gray-900 dark:text-zinc-100">0%</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-widest font-semibold">Tỷ lệ Trùng Lặp</p>
                    <p id="plagStatusUi" class="text-sm font-bold text-gray-800 dark:text-zinc-200 mt-1">An toàn</p>
                    <div id="plagReportOverview"></div>
                    
                    <div class="mt-6 pt-5 border-t dark:border-zinc-800">
                        <button type="button" onclick="openPlagiarismModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-zinc-300 bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 rounded-xl transition">
                            <i class="bi bi-arrow-clockwise"></i> Quét lại
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Detailed Results -->
            <div class="md:w-2/3">
                <h4 class="text-base font-bold text-gray-900 dark:text-zinc-100 border-b dark:border-zinc-800 pb-3 mb-4">Chi tiết nguồn vi phạm</h4>
                <div id="plagDetailedResults" class="space-y-4">
                    <!-- JS sẽ append kết quả vào đây -->
                </div>
            </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ============================== --}}
{{-- AI GENERATE SCRIPT (Server-side Gemini) --}}
{{-- ============================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const aiPromptTarget = document.getElementById('aiPromptTarget');
    const aiPromptInput = document.getElementById('aiPromptInput');
    const btnExecuteAI = document.getElementById('btnExecuteAI');

    if (!aiPromptTarget || !btnExecuteAI) return;

    btnExecuteAI.addEventListener('click', async () => {
        const promptText = aiPromptInput.value.trim();
        if (!promptText) {
            alert('Vui lòng nhập chủ đề viết bài cho AI!');
            aiPromptInput.focus();
            return;
        }

        const originalBtnHtml = btnExecuteAI.innerHTML;
        btnExecuteAI.disabled = true;
        aiPromptInput.disabled = true;

        // Tạo/lấy khung trạng thái
        let progressEl = document.getElementById('aiProgressState');
        if (!progressEl) {
            progressEl = document.createElement('div');
            progressEl.id = 'aiProgressState';
            progressEl.className = 'mt-3 text-xs text-indigo-700 bg-indigo-100/60 p-3 rounded-xl border border-indigo-200';
            aiPromptTarget.querySelector('.bg-gradient-to-r').appendChild(progressEl);
        }

        // Animation giả lập tiến trình
        let fakeProgress = 0;
        const steps = [
            { p: 15, text: 'Đang phân tích chủ đề...' },
            { p: 35, text: 'Đang nghiên cứu ngữ cảnh...' },
            { p: 55, text: 'Đang viết bản nháp bài viết...' },
            { p: 75, text: 'Đang hoàn thiện nội dung...' },
            { p: 90, text: 'Đang kiểm tra chất lượng...' },
        ];
        let stepIdx = 0;

        function renderProgress(percent, text) {
            progressEl.innerHTML = `
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[11px] font-semibold text-indigo-700 truncate">
                        <i class="bi bi-stars mr-1 text-purple-500 animate-pulse"></i> ${text}
                    </span>
                    <span class="text-xs font-bold text-indigo-800 ml-2 whitespace-nowrap shrink-0">${percent}%</span>
                </div>
                <div class="w-full bg-indigo-200/50 rounded-full h-2.5 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 h-2.5 rounded-full transition-all duration-700 ease-out" style="width: ${percent}%"></div>
                </div>
            `;
        }

        renderProgress(5, 'Đang kết nối đến AI Gemini...');
        btnExecuteAI.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Đang xử lý...';

        // Chạy animation giả cho đẹp
        const progressTimer = setInterval(() => {
            if (stepIdx < steps.length) {
                renderProgress(steps[stepIdx].p, steps[stepIdx].text);
                stepIdx++;
            }
        }, 2500);

        try {
            const fd = new FormData();
            fd.append('prompt', promptText);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch("{{ route('ai.generate-post') }}", {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            clearInterval(progressTimer);

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Lỗi không xác định từ server.');
            }

            if (data.content) {
                renderProgress(100, `Hoàn thành bằng ${data.provider || 'AI'}! Đang chèn vào trình soạn thảo...`);

                // 1. Gắn Tiêu đề
                const titleInput = document.getElementById('title');
                if (titleInput && data.title && (!titleInput.value || titleInput.value.trim() === '')) {
                    titleInput.value = data.title;
                }

                // 2. Insert Nội dung vào CKEditor
                let currentContent = window.myEditor ? window.myEditor.getData() : '';
                if (currentContent === '<p>&nbsp;</p>' || currentContent === '<p><br></p>') currentContent = '';

                const aiBlock = `<br/><h2>📝 Bản nháp AI (Nguồn: ${data.provider || 'AI'})</h2>` + data.content;

                if (window.myEditor) {
                    window.myEditor.setData(currentContent + aiBlock);
                } else {
                    const editorEl = document.getElementById('editor');
                    if (editorEl) editorEl.value = (editorEl.value || '') + '\n\n' + data.content;
                }

                // 3. Tự động mượn tạm Ảnh đại diện (nếu chưa có)
                const thumbDropzone = document.getElementById('thumbDropzone');
                const thumbnailInput = document.getElementById('thumbnail');
                const thumbPreview = document.getElementById('thumbPreview');
                const thumbPreviewWrap = document.getElementById('thumbPreviewWrap');

                if (data.cover_image_prompt && thumbnailInput && !thumbnailInput.files.length && thumbDropzone && (!thumbPreview.src || thumbPreview.src.includes('undefined'))) {
                    try {
                        renderProgress(100, `Hoàn thành bài viết (${data.provider || 'AI'}). Đang tự vẽ ảnh bìa...`);
                        
                        // Gọi Pollinations API để lấy ảnh AI miễn phí
                        const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(data.cover_image_prompt)}?width=800&height=500&nologo=true`;
                        const imgRes = await fetch(imageUrl);
                        const blob = await imgRes.blob();
                        const file = new File([blob], `ai-cover-${Date.now()}.jpg`, { type: 'image/jpeg' });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        thumbnailInput.files = dataTransfer.files;

                        // Hiển thị Preview
                        const reader = new FileReader();
                        reader.onload = ev => {
                            thumbPreview.src = ev.target.result;
                            thumbPreviewWrap.classList.remove('hidden');
                            thumbDropzone.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    } catch (e) {
                        console.warn("Không thể tự động tải ảnh bìa AI", e);
                    }
                }

                // Thông báo thành công
                setTimeout(() => {
                    progressEl.innerHTML = `
                        <div class="flex items-center gap-2 text-green-700">
                            <i class="bi bi-check-circle-fill text-green-500 text-base"></i>
                            <span class="font-bold">AI đã hoàn thành bài viết!</span>
                            <span class="text-green-600/70 text-[10px]">Vui lòng đọc lại và chỉnh sửa cho phù hợp.</span>
                        </div>
                    `;
                    progressEl.className = 'mt-3 text-xs p-3 rounded-xl border border-green-200 bg-green-50';
                }, 500);

                // Ẩn khung AI sau 4 giây
                setTimeout(() => {
                    aiPromptTarget.classList.add('hidden');
                    aiPromptInput.value = '';
                    progressEl.remove();
                }, 4000);
            }

        } catch (error) {
            clearInterval(progressTimer);
            console.error('AI Error:', error);
            progressEl.innerHTML = `
                <div class="flex items-center gap-2 text-red-700">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-base"></i>
                    <span class="font-bold">${error.message}</span>
                </div>
            `;
            progressEl.className = 'mt-3 text-xs p-3 rounded-xl border border-red-200 bg-red-50';
        } finally {
            btnExecuteAI.disabled = false;
            aiPromptInput.disabled = false;
            btnExecuteAI.innerHTML = originalBtnHtml;
        }
    });
});
</script>

@endpush
