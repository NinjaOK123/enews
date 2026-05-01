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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-6 transition-all relative group focus-within:border-indigo-500/50 dark:focus-within:border-indigo-500/50">
            <div class="flex items-center justify-between mb-2">
              <label for="title" class="block text-[10px] font-black text-gray-400 dark:text-zinc-500 uppercase tracking-[0.2em]">
                Tiêu đề bài viết <span class="text-red-500">*</span>
              </label>
              <button type="button" onclick="suggestTitles(this)" class="group/btn relative overflow-hidden bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 dark:hover:text-white flex items-center gap-2">
                <i class="bi bi-stars"></i> Gợi ý tiêu đề
              </button>
            </div>
            <input type="text" id="title" name="title" required
                   value="{{ old('title', $post->title ?? '') }}"
                   placeholder="Nhập tiêu đề hấp dẫn..."
                   class="w-full text-2xl md:text-3xl font-black text-gray-900 dark:text-zinc-100 border-0 outline-none placeholder:text-gray-200 dark:placeholder:text-zinc-700 bg-transparent ring-0 focus:ring-0 px-0 py-1 m-0 leading-tight">
            @error('title')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm transition-colors overflow-hidden">
            <div class="flex flex-wrap items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/60 dark:bg-zinc-950/50">
              <span class="text-[10px] font-black text-gray-400 dark:text-zinc-500 uppercase tracking-widest">Nội dung chính</span>
              <div class="flex flex-wrap items-center gap-1.5 p-1 bg-white/50 dark:bg-zinc-900/50 rounded-xl border border-gray-200/50 dark:border-zinc-800/50">
                
                {{-- Word --}}
                <button type="button" id="btnImportWord" class="px-3 py-1.5 text-[10px] font-black uppercase text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-all flex items-center gap-1.5">
                    <i class="bi bi-file-earmark-word"></i> Word
                </button>
                <input type="file" id="wordFileInput" accept=".docx,.doc" class="hidden">
                <div class="w-px h-3 bg-gray-200 dark:bg-zinc-800 mx-0.5"></div>

                {{-- AI Quick Actions --}}
                <div class="flex items-center gap-1">
                    <button type="button" onclick="aiQuickAction('outline', this)" class="px-3 py-1.5 text-[10px] font-black uppercase text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/10 rounded-lg transition-all flex items-center gap-1.5" title="Lên sườn bài">
                        <i class="bi bi-list-task"></i> Sườn bài
                    </button>
                    <button type="button" onclick="aiQuickAction('expand', this)" class="px-3 py-1.5 text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-all flex items-center gap-1.5" title="Viết tiếp đoạn văn">
                        <i class="bi bi-lightning-charge"></i> Viết tiếp
                    </button>
                    <button type="button" onclick="aiQuickAction('summary', this)" class="px-3 py-1.5 text-[10px] font-black uppercase text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-all flex items-center gap-1.5" title="Tạo đoạn Sapo">
                        <i class="bi bi-card-text"></i> Tóm tắt
                    </button>
                    <button type="button" onclick="aiQuickAction('image', this)" class="px-3 py-1.5 text-[10px] font-black uppercase text-pink-600 dark:text-pink-400 hover:bg-pink-50 dark:hover:bg-pink-500/10 rounded-lg transition-all flex items-center gap-1.5" title="Tạo ảnh AI minh hoạ">
                        <i class="bi bi-image"></i> Tạo ảnh
                    </button>
                    <button type="button" onclick="document.getElementById('aiPromptTarget').classList.toggle('hidden')" class="w-7 h-7 flex items-center justify-center text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded-lg transition-all" title="Mở hộp chat AI">
                        <i class="bi bi-plus-circle-fill"></i>
                    </button>
                </div>

                <div class="w-px h-3 bg-gray-200 dark:bg-zinc-800 mx-0.5"></div>

                {{-- Media --}}
                <button type="button" onclick="document.getElementById('mediaModal').classList.remove('hidden')" class="px-3 py-1.5 text-[10px] font-black uppercase text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-all flex items-center gap-1.5">
                    <i class="bi bi-images text-indigo-400"></i> Media
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
        <div class="w-80 shrink-0 space-y-5" x-data="{
            sameAsAuthor: {{ old('photographer_same', (!isset($post) || (isset($post) && $post->photographer === $post->source_author)) ? 'true' : 'false') }},
            authorVal: @js(old('source_author', $post->source_author ?? '')),
            photographerVal: @js(old('photographer', $post->photographer ?? '')),
            init() {
                this.$watch('sameAsAuthor', (val) => {
                    if (val) this.photographerVal = this.authorVal;
                });
                this.$watch('authorVal', (val) => {
                    if (this.sameAsAuthor) this.photographerVal = val;
                });
            }
        }">

          {{-- Chuyên mục --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 transition-colors">
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
          <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-950/40 dark:to-blue-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-500/20 shadow-sm p-5 relative transition-colors">
            <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none">
              <div class="absolute -right-4 -bottom-4 text-indigo-200 dark:text-indigo-500/10 opacity-20 dark:opacity-100 pointer-events-none">
                <i class="bi bi-star-fill" style="font-size: 6rem;"></i>
              </div>
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
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 shadow-sm p-5 relative transition-colors">
            <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none">
              <div class="absolute -right-4 -top-4 text-emerald-200 dark:text-emerald-500/10 opacity-30 dark:opacity-100 pointer-events-none">
                <i class="bi bi-wallet2" style="font-size: 5rem;"></i>
              </div>
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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 transition-colors">
            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              Ảnh đại diện (Thumbnail)
            </label>

            {{-- Preview --}}
            <div id="thumbPreviewWrap" class="{{ (isset($post) && $post->thumbnail) ? '' : 'hidden' }} mb-3 relative group">
              <img id="thumbPreview"
                   src="{{ (isset($post) && $post->thumbnail) ? $post->thumbnail_url : '' }}"
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
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 transition-colors">
            <label for="source_author" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
              Tác giả / Nguồn
            </label>
            <div class="relative" x-data="{ 
                open: false, 
                activeIndex: -1,
                authors: {{ Js::from($suggestedAuthors ?? []) }},
                get currentTerm() {
                    return (authorVal || '').split(',').pop().trim();
                },
                get filteredAuthors() {
                    if (!this.currentTerm) return this.authors.slice(0, 6);
                    return this.authors.filter(a => a.toLowerCase().includes(this.currentTerm.toLowerCase()) && a !== this.currentTerm).slice(0, 6);
                },
                selectAuthor(author) {
                    let parts = (authorVal || '').split(',');
                    parts.pop();
                    if (parts.length > 0) {
                        authorVal = parts.map(p => p.trim()).join(', ') + ', ' + author;
                    } else {
                        authorVal = author;
                    }
                    this.open = false;
                    this.activeIndex = -1;
                    $refs.authorInput.focus();
                }
            }">
                <input type="text" name="source_author" id="source_author" x-ref="authorInput"
                       x-model="authorVal"
                       @input="open = true; activeIndex = -1"
                       @focus="open = true"
                       @click.outside="open = false"
                       @keydown.arrow-down.prevent="if (open) { activeIndex = activeIndex === filteredAuthors.length - 1 ? 0 : activeIndex + 1 }"
                       @keydown.arrow-up.prevent="if (open) { activeIndex = activeIndex <= 0 ? filteredAuthors.length - 1 : activeIndex - 1 }"
                       @keydown.enter.prevent="if (open && activeIndex >= 0 && filteredAuthors[activeIndex]) { selectAuthor(filteredAuthors[activeIndex]) }"
                       @keydown.escape="open = false"
                       autocomplete="off"
                       placeholder="VD: Cẩm Thiều - TV"
                       class="w-full border border-gray-200 dark:border-zinc-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 bg-white dark:bg-zinc-950/50 dark:text-zinc-100 transition relative z-20">
                
                <div x-show="open && filteredAuthors.length > 0" x-transition.opacity.duration.200ms x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden">
                    <template x-for="(author, index) in filteredAuthors" :key="author">
                        <div @click="selectAuthor(author)" 
                             @mouseenter="activeIndex = index"
                             :class="{'bg-emerald-50 dark:bg-emerald-500/20': activeIndex === index, 'bg-transparent': activeIndex !== index}"
                             class="px-4 py-2.5 cursor-pointer text-sm font-medium text-gray-700 dark:text-zinc-200 transition-colors border-b border-gray-100 dark:border-zinc-700/50 last:border-0 flex items-center gap-2">
                             <i class="bi bi-person-circle text-gray-400 dark:text-zinc-500" :class="{'text-emerald-600 dark:text-emerald-400': activeIndex === index}"></i>
                             <span x-text="author"></span>
                        </div>
                    </template>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1.5">Hiển thị in đậm cuối bài viết</p>
          </div>

          {{-- Người chụp ảnh --}}
          <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm p-5 transition-colors">
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
            <div class="relative" x-data="{ 
                pOpen: false, 
                pActiveIndex: -1,
                authors: {{ Js::from($suggestedAuthors ?? []) }},
                get currentTerm() {
                    return (photographerVal || '').split(',').pop().trim();
                },
                get filteredPhotographers() {
                    if (!this.currentTerm) return this.authors.slice(0, 6);
                    return this.authors.filter(a => a.toLowerCase().includes(this.currentTerm.toLowerCase()) && a !== this.currentTerm).slice(0, 6);
                },
                selectPhotographer(author) {
                    let parts = (photographerVal || '').split(',');
                    parts.pop();
                    if (parts.length > 0) {
                        photographerVal = parts.map(p => p.trim()).join(', ') + ', ' + author;
                    } else {
                        photographerVal = author;
                    }
                    this.pOpen = false;
                    this.pActiveIndex = -1;
                    $refs.photoInput.focus();
                }
            }">
                <input type="text" name="photographer" id="photographer" x-ref="photoInput"
                       x-model="photographerVal"
                       @input="if(!sameAsAuthor) { pOpen = true; pActiveIndex = -1; }"
                       @focus="if(!sameAsAuthor) pOpen = true"
                       @click.outside="pOpen = false"
                       @keydown.arrow-down.prevent="if (pOpen && !sameAsAuthor) { pActiveIndex = pActiveIndex === filteredPhotographers.length - 1 ? 0 : pActiveIndex + 1 }"
                       @keydown.arrow-up.prevent="if (pOpen && !sameAsAuthor) { pActiveIndex = pActiveIndex <= 0 ? filteredPhotographers.length - 1 : pActiveIndex - 1 }"
                       @keydown.enter.prevent="if (pOpen && !sameAsAuthor && pActiveIndex >= 0 && filteredPhotographers[pActiveIndex]) { selectPhotographer(filteredPhotographers[pActiveIndex]) }"
                       @keydown.escape="pOpen = false"
                       autocomplete="off"
                       :readonly="sameAsAuthor"
                       :class="sameAsAuthor ? 'bg-gray-50 dark:bg-zinc-900/50 text-gray-400 dark:text-zinc-600 cursor-not-allowed border-gray-200 dark:border-zinc-800' : 'bg-white dark:bg-zinc-950/50 text-gray-900 dark:text-zinc-100 border-gray-200 dark:border-zinc-800'"
                       placeholder="VD: Nguyễn Văn A"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 transition relative z-10">
                
                <div x-show="pOpen && !sameAsAuthor && filteredPhotographers.length > 0" x-transition.opacity.duration.200ms x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg overflow-hidden">
                    <template x-for="(author, index) in filteredPhotographers" :key="author">
                        <div @click="selectPhotographer(author)" 
                             @mouseenter="pActiveIndex = index"
                             :class="{'bg-emerald-50 dark:bg-emerald-500/20': pActiveIndex === index, 'bg-transparent': pActiveIndex !== index}"
                             class="px-4 py-2.5 cursor-pointer text-sm font-medium text-gray-700 dark:text-zinc-200 transition-colors border-b border-gray-100 dark:border-zinc-700/50 last:border-0 flex items-center gap-2">
                             <i class="bi bi-camera text-gray-400 dark:text-zinc-500" :class="{'text-emerald-600 dark:text-emerald-400': pActiveIndex === index}"></i>
                             <span x-text="author"></span>
                        </div>
                    </template>
                </div>
            </div>
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
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-zinc-800/60 bg-gradient-to-r from-emerald-50/50 to-teal-50/50 dark:from-emerald-950/20 dark:to-teal-950/20">
      <h3 class="text-lg font-extrabold text-gray-800 dark:text-zinc-100 flex items-center gap-2">
         <span class="p-2 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl text-emerald-600 dark:text-emerald-400 leading-none">🖼️</span> Media Library
      </h3>
      <button type="button" onclick="document.getElementById('mediaModal').classList.add('hidden')"
              class="text-gray-400 dark:text-zinc-500 hover:text-red-500 dark:hover:text-red-400 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 dark:hover:bg-red-500/10 transition text-lg">✕</button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-100 dark:border-zinc-800/60 bg-white dark:bg-zinc-900">
      <div class="flex space-x-2">
        <button type="button" class="media-tab-btn active-tab text-sm" data-tab="upload">📤 Tải lên</button>
        <button type="button" class="media-tab-btn text-sm" data-tab="library" id="library-tab">📁 Của tôi</button>
        <button type="button" class="media-tab-btn text-sm" data-tab="shared" id="shared-tab">🌐 Shared</button>
      </div>
      <div class="flex items-center gap-2" id="mediaWidthSelector">
        <label for="mediaInsertWidth" class="text-xs text-gray-500 dark:text-zinc-400 font-medium">Độ rộng video:</label>
        <select id="mediaInsertWidth" class="text-sm border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 rounded-lg py-1 px-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="100%">100%</option>
            <option value="75%">75%</option>
            <option value="50%">50%</option>
        </select>
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-zinc-900">
      {{-- Upload Tab --}}
      <div id="tab-upload" class="media-tab-pane">
        <label for="mediaUploadInput" id="mediaDropZone"
               class="flex flex-col items-center justify-center gap-4 py-12 border-2 border-dashed border-emerald-200 dark:border-zinc-700 bg-emerald-50/30 dark:bg-zinc-900/50 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:border-emerald-400 dark:hover:border-emerald-500/50 rounded-2xl cursor-pointer transition-all duration-300 group">
          <div class="w-16 h-16 bg-white dark:bg-zinc-800 shadow-sm border border-gray-100 dark:border-zinc-700 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
             <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
          </div>
          <div class="text-center px-4" id="mediaUploadText">
             <p class="text-sm font-bold text-gray-700 dark:text-zinc-300 mb-1">Click hoặc kéo thả file vào đây</p>
             <p class="text-xs text-gray-400 dark:text-zinc-500">Hỗ trợ JPG, PNG, WEBP, MP4 (Tối đa 20MB)</p>
          </div>
        </label>
        <input type="file" id="mediaUploadInput" accept="image/*,video/*" class="hidden">
        <div id="uploadResult" class="mt-3 text-sm text-center"></div>
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

  /* Allow playing video inside editor */
  .ck-content .media video, .ck-content .ck-media__wrapper video {
      pointer-events: auto !important;
      position: relative !important;
      z-index: 2 !important;
  }

  /* Media Tabs */
  .media-tab-btn {
      border-radius: 9999px;
      padding: 0.5rem 1rem;
      font-weight: 600;
      color: #6b7280;
      transition: all 0.2s;
  }
  html.dark .media-tab-btn { color: #a1a1aa; }
  .media-tab-btn:hover { background-color: #f3f4f6; color: #374151; }
  html.dark .media-tab-btn:hover { background-color: #27272a; color: #d4d4d8; }
  
  .media-tab-btn.active-tab { 
      background-color: #ecfdf5; 
      color: #059669; 
      border: none !important;
  }
  html.dark .media-tab-btn.active-tab { 
      background-color: rgba(16, 185, 129, 0.2); 
      color: #34d399; 
  }
  
  /* Dark mode CKEditor overrides */
  html.dark .ck-editor__editable { background: #18181b !important; color: #f4f4f5 !important; }
  html.dark .ck.ck-toolbar { border-bottom: 1px solid #27272a !important; background: #09090b !important; }
  html.dark .ck.ck-button { color: #d4d4d8 !important; }
  html.dark .ck.ck-button:hover, html.dark .ck.ck-button.ck-on { background: #27272a !important; color: #fff !important; }
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
    mediaEmbed: {
        previewsInData: true,
        toolbar: ['mediaEmbed'], // Add default toolbar if needed, though mediaEmbed doesn't have much natively
        extraProviders: [
            {
                name: 'custom-video',
                url: [
                    /^.*\.(mp4|webm|ogg)(\?.*)?$/i,
                    /\/media\/\d+\/view/i,
                    /\/storage\/.*(\?.*)?/i
                ],
                html: match => {
                    const fullUrl = match[0];
                    let width = '100%';
                    let align = 'center'; // Center by default
                    try {
                        const urlObj = new URL(fullUrl, window.location.origin);
                        if (urlObj.searchParams.has('w')) {
                            width = urlObj.searchParams.get('w') + '%';
                        }
                    } catch(e) {}
                    
                    return `<div style="position:relative; width:${width}; max-width:100%; margin: 1em auto; text-align: ${align};">
                        <video controls style="max-width:100%; border-radius: 8px; display: inline-block; width: 100%;" src="${fullUrl}"></video>
                    </div>`;
                }
            }
        ]
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
function showCuteToast(type, title, message) {
    const container = document.getElementById('cuteToastContainer');
    if (!container) {
        const div = document.createElement('div');
        div.id = 'cuteToastContainer';
        div.className = 'fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(div);
    }
    
    const toast = document.createElement('div');
    const colorClass = type === 'success' ? 'bg-emerald-500 shadow-emerald-200' : 'bg-rose-500 shadow-rose-200';
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    
    toast.className = `transform translate-x-full transition-all duration-500 ease-out flex items-center gap-3 p-4 rounded-2xl text-white shadow-xl ${colorClass} pointer-events-auto min-w-[300px] border border-white/20`;
    toast.innerHTML = `
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-lg">
            <i class="bi ${icon}"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold">${title}</p>
            <p class="text-[11px] opacity-90">${message}</p>
        </div>
    `;
    
    document.getElementById('cuteToastContainer').appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.remove('translate-x-full'), 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
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
        const file = this.files[0];
        const maxFileSize = 20 * 1024 * 1024; // 20MB
        
        if (file.size > maxFileSize) {
            uploadText.innerHTML = `
                <div class="text-rose-500 px-4 py-2">
                    <i class="bi bi-exclamation-octagon-fill text-3xl mb-2 block animate-bounce"></i>
                    <p class="text-sm font-bold mb-1">File quá lớn!</p>
                    <p class="text-xs">Video/Ảnh của bạn vượt quá giới hạn 20MB.</p>
                </div>
            `;
            const uploadResult = document.getElementById('uploadResult');
            if(uploadResult) uploadResult.innerHTML = '';
            setTimeout(() => { fi.value = ''; fi.dispatchEvent(new Event('change')); }, 3500);
            return;
        }

        uploadText.innerHTML = `
            <div class="text-emerald-600 dark:text-emerald-400 px-4">
                <i class="bi bi-cloud-arrow-up-fill text-3xl mb-2 block animate-pulse"></i>
                <p class="text-sm font-bold mb-1">Đang đẩy lên máy chủ...</p>
                <p class="text-xs break-all line-clamp-1">${file.name}</p>
            </div>
        `;
        const uploadResult = document.getElementById('uploadResult');
        if(uploadResult) uploadResult.innerHTML = '';
        
        const fd = new FormData(); 
        fd.append('upload', file); 
        fd.append('_token', '{{ csrf_token() }}');
        
        fetch(uploadMediaUrl, { method:'POST', body:fd })
        .then(async r => {
            if (!r.ok) {
                if (r.status === 413) throw new Error("File vượt quá 20MB (Lỗi máy chủ).");
                const errData = await r.json().catch(() => null);
                throw new Error(errData?.error?.message || errData?.message || "Lỗi máy chủ.");
            }
            return r.json();
        })
        .then(data => {
            if(data.url) {
                uploadText.innerHTML = `
                    <div class="text-emerald-500 px-4">
                        <i class="bi bi-check-circle-fill text-3xl mb-2 block"></i>
                        <p class="text-sm font-bold">Tải lên thành công!</p>
                    </div>
                `;
                insertMediaToEditor(data.url, data.type);
                setTimeout(() => {
                    fi.value = ''; // Reset input
                    fi.dispatchEvent(new Event('change'));
                }, 2000);
            } else { 
                throw new Error(data.error?.message || 'Không rõ lỗi');
            }
        }).catch((err) => {
            uploadText.innerHTML = `
                <div class="text-rose-500 px-4">
                    <i class="bi bi-x-circle-fill text-3xl mb-2 block"></i>
                    <p class="text-sm font-bold mb-1">Tải lên thất bại</p>
                    <p class="text-xs break-words">${err.message}</p>
                </div>
            `;
            setTimeout(() => { fi.value = ''; fi.dispatchEvent(new Event('change')); }, 4000);
        });
    } else {
        uploadText.innerHTML = `
             <p class="text-sm font-bold text-gray-700 dark:text-zinc-300 mb-1">Click hoặc kéo thả file vào đây</p>
             <p class="text-xs text-gray-400 dark:text-zinc-500">Hỗ trợ JPG, PNG, WEBP, MP4 (Tối đa 20MB)</p>
        `;
    }
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
                fi.files = dt.files;
                
                // Tự động kích hoạt upload
                fi.dispatchEvent(new Event('change'));
                break; // Chỉ xử lý file đầu tiên
            }
        }
    }
});

function insertMediaToEditor(url, type) {
    if(!window.myEditor) {
        alert("Lỗi: Không tìm thấy trình soạn thảo!");
        return;
    }
    
    try {
        if (type === 'image' || (type && type.startsWith('image/'))) {
            const html = `<figure class="image"><img src="${url}" alt="media"></figure>`;
            const view = window.myEditor.data.processor.toView(html);
            const modelFragment = window.myEditor.data.toModel(view);
            window.myEditor.model.insertContent(modelFragment);
        } else {
            let finalUrl = url;
            const wSelect = document.getElementById('mediaInsertWidth');
            if (wSelect && wSelect.value !== '100%') {
                const wVal = parseInt(wSelect.value);
                finalUrl = url + (url.includes('?') ? '&' : '?') + 'w=' + wVal;
            }
            window.myEditor.execute('mediaEmbed', finalUrl);
        }
    } catch (e) {
        console.error("Lỗi chèn media:", e);
        try {
            // Fallback for video if mediaEmbed fails
            let finalUrl = url;
            const wSelect = document.getElementById('mediaInsertWidth');
            let wStyle = '100%';
            if (wSelect && wSelect.value !== '100%') {
                wStyle = parseInt(wSelect.value) + '%';
                finalUrl = url + (url.includes('?') ? '&' : '?') + 'w=' + parseInt(wSelect.value);
            }
            const html = `<figure class="media" style="text-align: center;"><div style="width: ${wStyle}; margin: 0 auto;"><video controls style="max-width: 100%; width: 100%; display: block;" src="${finalUrl}"></video></div></figure>`;
            const view = window.myEditor.data.processor.toView(html);
            const modelFragment = window.myEditor.data.toModel(view);
            window.myEditor.model.insertContent(modelFragment);
        } catch (e2) {
            alert("Lỗi chèn media: " + e2.message);
        }
    }
    
    document.getElementById('mediaModal').classList.add('hidden');
}

function loadMedia(url, containerId) {
    const c = document.getElementById(containerId);
    c.innerHTML = '<p class="col-span-full text-center text-sm text-gray-400 py-8">Đang tải...</p>';
    fetch(url).then(r => r.json()).then(data => {
        if(!data || !data.length) { c.innerHTML = '<p class="col-span-full text-center text-sm text-gray-400 py-8">Không có file nào.</p>'; return; }
        c.innerHTML = data.map(m => `
            <div class="rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 hover:border-emerald-400 dark:hover:border-emerald-500 group transition-all shadow-sm hover:shadow-md bg-white dark:bg-zinc-800 relative">
                <div class="cursor-pointer" onclick="window.open('${m.url}', '_blank')">
                    ${m.file_type && m.file_type.startsWith('image')
                        ? `<img src="${m.url}" class="w-full h-24 object-cover transition-transform duration-300 group-hover:scale-105">`
                        : `<div class="w-full h-24 bg-zinc-800 dark:bg-zinc-900 flex items-center justify-center text-zinc-400 dark:text-zinc-500 transition-colors text-3xl"><i class="bi bi-play-circle-fill"></i></div>`}
                </div>
                
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-center items-center gap-2 backdrop-blur-[2px] pointer-events-none group-hover:pointer-events-auto">
                    <button type="button" onclick="event.stopPropagation(); insertMediaToEditor('${m.url}','${m.file_type}')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold shadow-sm flex items-center gap-1 transition-transform hover:scale-105 active:scale-95">
                        <i class="bi bi-box-arrow-in-down-right"></i> Chèn
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="event.stopPropagation(); window.open('${m.url}', '_blank')" class="w-7 h-7 flex items-center justify-center bg-zinc-700 hover:bg-zinc-600 text-white rounded-full text-xs shadow-sm transition-transform hover:scale-110 active:scale-95" title="Xem">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" onclick="event.stopPropagation(); deleteMedia(${m.id})" class="w-7 h-7 flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white rounded-full text-xs shadow-sm transition-transform hover:scale-110 active:scale-95" title="Xoá">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>

                <p class="text-[11px] text-zinc-600 dark:text-zinc-400 truncate px-2.5 py-1.5 border-t border-zinc-100 dark:border-zinc-700 font-medium relative z-10 bg-white dark:bg-zinc-800">${m.file_name}</p>
            </div>`).join('');
    }).catch(() => c.innerHTML = '<p class="col-span-full text-center text-sm text-red-400 py-8">Lỗi tải dữ liệu.</p>');
}

function deleteMedia(id) {
    if (!confirm('Bạn có chắc chắn muốn xoá file này? Thao tác này không thể hoàn tác.')) return;
    
    fetch('/contributor/media/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Tải lại danh sách
            loadMedia("{{ route('contributor.media.personal') }}", 'personalMediaList');
        } else {
            alert(data.message || 'Lỗi khi xoá file.');
        }
    })
    .catch(err => {
        alert('Lỗi kết nối. Vui lòng thử lại.');
    });
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
    resetPlagiarismScreens();
}

function resetPlagiarismScreens() {
    document.getElementById('plagiarismStartScreen').classList.remove('hidden');
    document.getElementById('plagiarismProgressScreen').classList.add('hidden');
    document.getElementById('plagiarismReportScreen').classList.add('hidden');
    document.getElementById('btnMinimizePlagiarism').classList.add('hidden'); // Hide minimize on start
}

function minimizePlagiarismModal() {
    document.getElementById('plagiarismModal').classList.add('hidden');
    document.getElementById('plagiarismWidget').classList.remove('hidden');
}

function restorePlagiarismModal() {
    document.getElementById('plagiarismWidget').classList.add('hidden');
    document.getElementById('plagiarismModal').classList.remove('hidden');
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
    document.getElementById('btnMinimizePlagiarism').classList.remove('hidden'); // Allow minimize now
    
    const progressBar = document.getElementById('plagProgressBar');
    const progressText = document.getElementById('plagProgressText');
    const currentSentenceEl = document.getElementById('plagCurrentSentence');
    const widgetProgress = document.getElementById('widgetProgress');
    const widgetTitle = document.getElementById('widgetTitle');
    const widgetSpin = document.getElementById('widgetSpin');
    const widgetIcon = document.getElementById('widgetIcon');
    
    // Reset widget state
    widgetTitle.textContent = 'Đang quét đạo văn...';
    widgetTitle.className = 'text-sm font-bold text-gray-900 dark:text-zinc-100 truncate';
    widgetProgress.classList.remove('bg-green-500');
    widgetProgress.classList.add('bg-indigo-500');
    widgetSpin.classList.remove('hidden');
    widgetIcon.innerHTML = '<i class="bi bi-shield-check text-lg"></i>';
    
    let plagiarizedCount = 0;
    let maxScore = 0;         // Điểm tương đồng cao nhất tìm thấy
    let totalScore = 0;       // Tổng điểm để tính trung bình
    const resultsHtml = [];

    // 4. Process từng câu qua AJAX
    for (let i = 0; i < sentences.length; i++) {
        const sentence = sentences[i];
        
        // Update Tiền trình UI
        const percent = Math.round(((i + 1) / sentences.length) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = `Đang quét: ${i+1}/${sentences.length} câu (${percent}%)`;
        currentSentenceEl.textContent = sentence.substring(0, 60) + '...';
        
        // Cập nhật Widget
        widgetProgress.style.width = percent + '%';

        try {
            const formData = new FormData();
            formData.append('sentence', sentence);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("admin.plagiarism.check") }}', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('API Error');

            const result = await response.json();
            
            totalScore += result.similarity;
            if (result.isPlagiarized) plagiarizedCount++;  // Tầng 3: Đếm các câu vi phạm thật sự (>20%)
            if (result.similarity > maxScore) maxScore = result.similarity; // Lưu điểm cao nhất

            // Cập nhật real-time: điểm đạo văn live trong vòng tròn Progress (element đúng)
            const livePercent = Math.round((plagiarizedCount / sentences.length) * 100);
            const plagLiveScoreEl = document.getElementById('plagLiveScore');
            if (plagLiveScoreEl) {
                let liveColor = 'text-green-500';
                if (livePercent > 5)  liveColor = 'text-yellow-400';
                if (livePercent > 20) liveColor = 'text-orange-400';
                if (livePercent > 35) liveColor = 'text-red-500';
                plagLiveScoreEl.className = `text-sm font-black ${liveColor} leading-none mt-1`;
                plagLiveScoreEl.textContent = `${livePercent}%`;
            }

            // Tầng 2: Lưu lại kết quả câu này để show Report Highlight (hiển thị mọi câu > 5%)
            if (result.similarity > 5) {
                let sourcesList = result.sources.map(src => {
                    if (src.is_internal) {
                        return `
                            <div class="flex justify-between items-center text-xs mb-1.5 p-1.5 rounded bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-500/30 transition">
                                <div class="flex items-center gap-2 truncate mr-4">
                                    <span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest shrink-0">Nội bộ</span>
                                    <a href="${src.url}" target="_blank" class="text-red-700 dark:text-red-400 font-bold hover:underline truncate" title="${src.title}">${src.title}</a>
                                </div>
                                <span class="text-red-600 dark:text-red-400 font-black ml-2 shrink-0">${src.similarity}%</span>
                            </div>
                        `;
                    } else {
                        return `
                            <div class="flex justify-between items-center text-xs mb-1.5 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition">
                                <a href="${src.url}" target="_blank" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 hover:underline truncate mr-4" title="${src.url}">${src.url}</a>
                                <span class="text-gray-500 dark:text-zinc-400 font-bold ml-2 shrink-0">${src.similarity}%</span>
                            </div>
                        `;
                    }
                }).join('');
                
                resultsHtml.push(`
                    <div class="bg-red-50/50 dark:bg-red-900/10 border border-red-200 dark:border-red-500/20 rounded-xl p-5 mb-4">
                        <div class="flex justify-between items-start gap-4 mb-4">
                            <p class="text-gray-800 dark:text-zinc-200 font-medium text-sm leading-relaxed">"${result.sentence}"</p>
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold shrink-0">${result.similarity}% trùng lập</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-zinc-400 font-bold uppercase tracking-wider mb-2">Nguồn phát hiện</p>
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-2 border border-gray-100 dark:border-zinc-800">
                                ${sourcesList}
                            </div>
                        </div>
                    </div>
                `);
            }

        } catch (err) {
            console.error("Lỗi khi check câu:", sentence, err);
        }

        // Không cần delay — Serper API không có rate limit strict như DuckDuckGo
    }

    // Hoàn tất!
    progressBar.style.width = '100%';
    progressText.textContent = `Hoàn tất! 100%`;

    // 5. Hiển thị trang kết quả (Report Screen)
    document.getElementById('plagiarismProgressScreen').classList.add('hidden');
    document.getElementById('plagiarismReportScreen').classList.remove('hidden');

    // Tỷ lệ độ phủ (Coverage) = % câu bị trùng lặp nặng (Tầng 3)
    const plagPercent = Math.round((plagiarizedCount / sentences.length) * 100);
    // Mức tương đồng = điểm cao nhất tìm thấy trong bài
    const topScore = maxScore;
    const displayScore = Math.max(plagPercent, topScore);

    // Xác định màu cảnh báo theo điểm hiển thị chính
    let colorClass = 'text-green-500';
    if (displayScore > 10) colorClass = 'text-yellow-500';
    if (displayScore > 25) colorClass = 'text-orange-500';
    if (displayScore > 45) colorClass = 'text-red-500';

    document.getElementById('plagScoreUi').className = `text-5xl font-bold ${colorClass}`;
    document.getElementById('plagScoreUi').textContent = `${displayScore}%`;
    // Hiển thị điểm tương đồng cao nhất (câu trùng nhất)
    document.getElementById('plagSimScoreUi').textContent = `${topScore}%`;

    const statusText = displayScore >= 45 ? 'Nguy cơ cao' : (displayScore >= 25 ? 'Cẩn thận' : 'An toàn');
    document.getElementById('plagStatusUi').textContent = statusText;
    
    document.getElementById('plagTotalSentences').textContent = sentences.length;
    document.getElementById('plagViolatedSentences').textContent = plagiarizedCount;

    if (resultsHtml.length > 0) {
        document.getElementById('plagDetailedResults').innerHTML = resultsHtml.join('');
    } else {
        document.getElementById('plagDetailedResults').innerHTML = `
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-500 mb-3">
                    <i class="bi bi-shield-check text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Tuyệt vời! Không phát hiện câu nào có dấu hiệu sao chép đáng kể (> 5%).</p>
            </div>
        `;
    }

    // Update Widget State
    widgetTitle.textContent = '✅ Đã quét xong. Bấm xem!';
    widgetTitle.className = 'text-sm font-bold text-green-600 dark:text-green-400 truncate animate-pulse';
    widgetProgress.classList.remove('bg-indigo-500');
    widgetProgress.classList.add('bg-green-500');
    widgetSpin.classList.add('hidden');
    widgetIcon.innerHTML = '<i class="bi bi-check2-all text-xl text-green-500"></i>';

    // Play Ting Audio
    try {
        const audio = new Audio("data:audio/wav;base64,UklGRtAAAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YagAAAAK/wEABQAF/wb/Bv8L/xT/IQAsAEMAVwBlAGoAZwBcAEkAJAAOAAH/8v7Q/qr+jf6G/oT+f/59/n3+e/5//of+mf62/tb+9f4LACH+Sv5z/pv+vv7X/vL+AwAOABgAHwAfABgADwAH/wP/7f7M/q7+lv6C/nL+Y/5b/lj+Wf5p/n3+mf63/tj+9f4IAAwADgAQABcAHwAkACQAIAAYAA8ACAAB//T+6v7i/tX+v/6o/pT+gP5t/mH+WP5Z/mP+cv6H/pz+tv7R/vP+AwAKABIAHgAoAC8ALgAoAB4AEAAD//H+0/66/pz+g/5q/lf+Sf5A/kH+Sv5g/nn+kv6x/tT+9/4LAAMK//D+4/7q/gAA9P7e/vb+///8/wcAAQAFAAAAAA==");
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Audio play error:', e));
    } catch(e) {}

    // Show Notification If Minimized
    if (document.getElementById('plagiarismModal').classList.contains('hidden')) {
        showCuteToast('success', 'Turnitin đã quét xong!', 'Bấm vào thông báo dưới góc phải để xem Báo cáo Trùng lặp.');
    }
}
</script>

{{-- ============================== --}}
{{-- PLAGIARISM CHECK MODAL --}}
{{-- ============================== --}}
<div id="plagiarismModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-gray-900/40 dark:bg-zinc-950/80 backdrop-blur-md transition-opacity" onclick="closePlagiarismModal()"></div>
  
  <!-- Modal Content -->
  <div class="relative bg-white dark:bg-zinc-950 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-300 border border-gray-100 dark:border-zinc-800 shadow-indigo-500/10 dark:shadow-indigo-500/5">
    
    <!-- Top Accent Line -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-blue-500 z-10"></div>

    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 relative z-0">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20">
          <i class="bi bi-shield-check text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100 leading-tight flex items-center gap-2">
            Turnitin <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full">Miễn Phí</span>
          </h3>
          <p class="text-xs text-gray-500 dark:text-zinc-400 font-mono tracking-tight mt-0.5">THUẬT TOÁN: N-GRAM & COSINE SIMILARITY</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
          <button type="button" id="btnMinimizePlagiarism" onclick="minimizePlagiarismModal()" class="text-gray-400 dark:text-zinc-500 hover:text-indigo-600 dark:hover:text-indigo-400 bg-gray-50 hover:bg-indigo-50 dark:bg-zinc-800 dark:hover:bg-indigo-500/20 px-3 py-2 rounded-xl transition flex items-center gap-2 text-sm font-semibold">
            <i class="bi bi-arrows-angle-contract"></i> Thu nhỏ
          </button>
          <button type="button" onclick="closePlagiarismModal()" class="text-gray-400 dark:text-zinc-500 hover:text-gray-600 dark:hover:text-zinc-300 bg-gray-50 hover:bg-gray-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 p-2 rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
      </div>
    </div>

    <!-- Body -->
    <div class="p-6 lg:p-10 overflow-y-auto flex-1 bg-gray-50/30 dark:bg-zinc-950 relative">
      
      <!-- SCREEN 1: Bắt đầu -->
      <div id="plagiarismStartScreen" class="flex flex-col items-center justify-center py-12 h-full min-h-[400px]">
        
        <div class="relative w-32 h-32 mb-8 flex flex-col items-center justify-center">
            <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-500/10 rounded-full animate-ping opacity-30"></div>
            <div class="w-24 h-24 bg-white dark:bg-zinc-900 border-2 border-indigo-100 dark:border-indigo-500/20 rounded-full flex items-center justify-center shadow-lg relative z-10">
                <i class="bi bi-file-earmark-text text-4xl text-indigo-600 dark:text-indigo-400"></i>
                <!-- Scanner line simulation -->
                <div class="absolute top-0 w-full h-1/2 bg-gradient-to-b from-transparent to-indigo-500/20 dark:to-indigo-500/40 rounded-t-full border-b border-indigo-400 hidden group-hover:block"></div>
            </div>
        </div>

        <h4 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-3 text-center tracking-tight">Phân tích Tính Nguyên gốc</h4>
        <p class="text-gray-500 dark:text-zinc-400 text-sm max-w-lg text-center mb-8 leading-relaxed">
            Hệ thống sẽ đối chiếu chuyên sâu từng mảnh dữ liệu của bài viết với <span class="font-semibold text-gray-700 dark:text-zinc-300">hàng tỷ trang mạng internet</span> và các cơ sở dữ liệu học thuật mở để đảm bảo tính độc bản của nội dung.
        </p>
        
        <button type="button" onclick="startPlagiarismCheck()" class="group inline-flex items-center justify-center gap-3 px-8 py-3.5 text-base font-bold text-white bg-gray-900 hover:bg-gray-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-gray-200 rounded-full shadow-xl transition-all hover:-translate-y-0.5">
          Khởi chạy Quá trình Quét
          <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </button>

        <div class="mt-8 flex items-center gap-6 text-xs text-gray-400 dark:text-zinc-500 font-medium">
            <span class="flex items-center gap-1.5"><i class="bi bi-lock-fill"></i> Bảo mật dữ liệu 100%</span>
            <span class="flex items-center gap-1.5"><i class="bi bi-lightning-charge-fill"></i> Tốc độ phân tích cực nhanh</span>
        </div>
      </div>

      <!-- SCREEN 2: Tiến trình quét -->
      <div id="plagiarismProgressScreen" class="hidden flex flex-col items-center justify-center py-16 h-full min-h-[400px]">
        <div class="relative w-28 h-28 mb-10">
            <!-- Ripple Effects -->
            <div class="absolute inset-0 rounded-full border border-indigo-200 dark:border-indigo-500/30 scale-150 animate-ping opacity-20"></div>
            <div class="absolute inset-0 rounded-full border border-indigo-300 dark:border-indigo-500/40 scale-110 animate-ping opacity-40" style="animation-delay: 300ms;"></div>
            
            <div class="absolute inset-0 rounded-full border-[3px] border-gray-100 dark:border-zinc-800/50"></div>
            <div class="absolute inset-0 rounded-full border-[3px] border-indigo-600 border-t-transparent animate-spin ring-4 ring-indigo-50 dark:ring-indigo-500/10"></div>
            
            <div class="absolute inset-0 flex flex-col items-center justify-center bg-white dark:bg-zinc-900 rounded-full m-1 shadow-sm">
                <span id="plagProgressPercent" class="text-base font-bold text-indigo-600 dark:text-indigo-400 leading-none">0%</span>
                <span class="text-[9px] text-gray-400 dark:text-zinc-500 mt-0.5">quét</span>
                <span id="plagLiveScore" class="text-sm font-black text-green-500 leading-none mt-1">0%</span>
                <span class="text-[8px] text-gray-400 dark:text-zinc-500">ĐV</span>
            </div>
        </div>
        
        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 text-center" id="plagProgressText">Hệ thống đang truy xuất dữ liệu</h4>
        
        <div class="w-full max-w-md bg-gray-100 dark:bg-zinc-800/50 rounded-full h-1.5 mb-6 overflow-hidden">
          <div id="plagProgressBar" class="bg-indigo-600 h-full rounded-full transition-all duration-300 relative overflow-hidden" style="width: 0%">
            <!-- Animated shimmer on progress bar -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full animate-[shimmer_1.5s_infinite]"></div>
          </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-xl px-5 py-4 w-full max-w-md shadow-sm">
            <p class="text-xs font-semibold text-gray-400 dark:text-zinc-500 uppercase tracking-widest mb-2 flex items-center justify-between">
                <span>Trạng thái máy quét</span>
                <i class="bi bi-activity animate-pulse text-indigo-500"></i>
            </p>
            <p class="text-sm text-gray-600 dark:text-zinc-300 italic truncate font-mono" id="plagCurrentSentence">Đang phân tích cấu trúc cú pháp...</p>
        </div>
      </div>

      <!-- SCREEN 3: Kết quả -->
      <div id="plagiarismReportScreen" class="hidden max-w-4xl mx-auto w-full py-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">Báo cáo Phân tích Trùng lặp</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="flex h-2 w-2 relative">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <p class="text-sm text-gray-500 dark:text-zinc-400">Turnitin Checker AI Engine</p>
                </div>
            </div>
            <button type="button" onclick="resetPlagiarismScreens()" class="inline-flex items-center gap-2 px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-700 dark:text-zinc-300 rounded-lg text-sm font-medium transition border border-gray-200 dark:border-zinc-700">
                <i class="bi bi-arrow-clockwise"></i> Quét lại báo cáo
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div class="bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700/50 rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mb-1">Tỷ lệ Đạo văn</p>
                <p id="plagScoreUi" class="text-5xl font-bold text-green-500">0%</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700/50 rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mb-1">Mức độ Tương đồng</p>
                <p id="plagSimScoreUi" class="text-5xl font-bold text-blue-500">0%</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5 mb-8">
            <div class="bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700/50 rounded-2xl p-4 text-center">
                <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wide">Tổng số câu quét</p>
                <p id="plagTotalSentences" class="text-xl font-bold text-gray-900 dark:text-zinc-100">0</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700/50 rounded-2xl p-4 text-center">
                <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wide">Câu văn vi phạm</p>
                <p id="plagViolatedSentences" class="text-xl font-bold text-red-500">0</p>
            </div>
            <div class="bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700/50 rounded-2xl p-4 text-center">
                <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1 uppercase tracking-wide">Đánh giá chung</p>
                <p id="plagStatusUi" class="text-sm font-bold text-green-600 dark:text-green-400 inline-block mt-1">An toàn</p>
            </div>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-zinc-100 flex items-center gap-2">
                Phân tích Chi tiết
            </h3>
            <span class="text-xs text-gray-500 dark:text-zinc-400 bg-gray-100 dark:bg-zinc-800 px-2 py-1 rounded">Mức cảnh báo >25%</span>
        </div>

        <!-- Detailed Breakdown -->
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-list-columns-reverse text-indigo-500"></i> Phân tích Chi tiết
            </h3>
            <span class="text-xs bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 px-2 py-1 rounded font-medium">Hiển thị các khối vi phạm >25%</span>
        </div>

        <div id="plagDetailedResults" class="space-y-4 pb-6">
            <!-- JS sẽ append kết quả vào đây -->
        </div>

      </div>

    </div>
  </div>
</div>

{{-- ============================== --}}
{{-- PLAGIARISM FLOATING WIDGET --}}
{{-- ============================== --}}
<div id="plagiarismWidget" onclick="restorePlagiarismModal()" class="hidden fixed bottom-6 right-6 z-[90] bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-2xl p-4 cursor-pointer hover:scale-105 transition-transform duration-300 flex items-center gap-4 w-72">
    <div class="relative w-10 h-10 shrink-0">
        <div id="widgetSpin" class="absolute inset-0 rounded-full border-[3px] border-indigo-600 border-t-transparent animate-spin"></div>
        <div id="widgetIcon" class="absolute inset-0 flex items-center justify-center text-indigo-500">
            <i class="bi bi-shield-check text-lg"></i>
        </div>
    </div>
    <div class="flex-1 min-w-0">
        <p id="widgetTitle" class="text-sm font-bold text-gray-900 dark:text-zinc-100 truncate">Đang quét đạo văn...</p>
        <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
            <div id="widgetProgress" class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
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

async function suggestTitles(btn) {
    const content = window.myEditor ? window.myEditor.getData() : '';
    const currentTitle = document.getElementById('title').value;
    
    if (!content || content.length < 100) {
        alert('Hãy viết một chút nội dung để AI có cơ sở gợi ý tiêu đề nhé!');
        return;
    }

    const originalBtn = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Đang nghĩ...';

    try {
        const fd = new FormData();
        fd.append('prompt', `Dựa trên bài viết sau, hãy gợi ý 3 tiêu đề hấp dẫn, đúng chất báo chí truyền thông. Trả về JSON { "suggestions": ["Title 1", "Title 2", "Title 3"] }. Nội dung bài: ${content.substring(0, 1000)}`);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const res = await fetch("{{ route('ai.generate-post') }}", { method: 'POST', body: fd });
        const data = await res.json();
        
        if (data.title) {
            if (confirm(`Gợi ý: "${data.title}"\nBạn có muốn sử dụng tiêu đề này không?`)) {
                document.getElementById('title').value = data.title;
            }
        }
    } catch (e) {
        console.error(e);
        alert('Lỗi: ' + e.message);
    }
    btn.disabled = false;
    btn.innerHTML = originalBtn;
}

async function aiQuickAction(action, btn) {
    if (!window.myEditor) {
        alert('Trình soạn thảo chưa được khởi tạo!');
        return;
    }
    const title = document.getElementById('title').value;
    const content = window.myEditor.getData();

    let prompt = "";
    if (action === 'outline') {
        if (!title) { alert('Vui lòng nhập tiêu đề trước!'); return; }
        prompt = `Tạo sườn bài (Outline) chi tiết với các thẻ <h2> và <p> cho chủ đề: ${title}. Đúng văn phong báo chí E-News.`;
    } else if (action === 'expand') {
        if (!content || content.length < 50) { alert('Hãy viết một đoạn ngắn để AI có thể viết tiếp!'); return; }
        prompt = `Đọc nội dung bài viết và viết tiếp khoảng 2-3 đoạn văn mạch lạc, hấp dẫn, giữ đúng văn phong. Nội dung hiện tại: ${content.substring(content.length - 1000)}`;
    } else if (action === 'summary') {
        if (!content || content.length < 100) { alert('Nội dung quá ngắn để tóm tắt!'); return; }
        prompt = `Hãy viết một đoạn Sapo (Tóm tắt) khoảng 2 câu cực kỳ thu hút cho bài báo sau. Nội dung: ${content.substring(0, 1500)}`;
    } else if (action === 'image') {
        if (!title && (!content || content.length < 50)) { alert('Vui lòng nhập tiêu đề hoặc nội dung để AI tạo ảnh minh hoạ!'); return; }
        prompt = `Bài báo có tiêu đề: "${title}" và nội dung: "${content.substring(0, 1000)}". Hãy trả về một câu prompt tiếng Anh (mô tả cực chi tiết, bắt mắt, phong cách chân thực digital art) để vẽ một bức ảnh minh họa. Điền câu prompt tĩnh tiếng Anh vào trường "cover_image_prompt" trong kết quả trả về. KHÔNG cần viết lại nội dung, dùng trường "content" để chứa dummy text cũng được.`;
    }

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Chờ...';

    try {
        const fd = new FormData();
        fd.append('prompt', prompt);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const res = await fetch("{{ route('ai.generate-post') }}", { method: 'POST', body: fd });
        const data = await res.json();
        
        if (!res.ok) throw new Error(data.error || 'Server error');
        
        if (action === 'image' && data.cover_image_prompt) {
            const seed = Math.floor(Math.random() * 1000000);
            const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(data.cover_image_prompt)}?width=800&height=500&nologo=true&seed=${seed}`;
            const imgHtml = `<p><br></p><figure class="image"><img src="${imageUrl}" alt="AI Generated Image" /><figcaption>Ảnh minh họa tạo bởi AI: ${data.cover_image_prompt}</figcaption></figure><p><br></p>`;
            window.myEditor.setData(content + imgHtml);
            if (typeof showCuteToast === 'function') {
                showCuteToast('success', 'Xong rồi! ✨', 'Ảnh AI đã chèn thành công!');
            }
        } else if (data.content) {
            if (action === 'summary') {
                window.myEditor.setData(`<strong>${data.content}</strong><br/>` + content);
            } else {
                window.myEditor.setData(content + `<br/>` + data.content);
            }
            if (typeof showCuteToast === 'function') {
                showCuteToast('success', 'Xong rồi! ✨', 'AI đã xử lý yêu cầu của bạn.');
            }
        }
    } catch (e) {
        alert('Lỗi: ' + e.message);
    }
    btn.disabled = false;
    btn.innerHTML = originalText;
}
</script>

@endpush
