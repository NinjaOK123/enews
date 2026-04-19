@extends('layouts.admin')
@section('title', 'Tạo thông báo mới')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

@section('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 250px;
        border-radius: 0 0 12px 12px !important;
        border-color: #f3f4f6 !important;
    }
    .ck-toolbar {
        border-radius: 12px 12px 0 0 !important;
        border-color: #f3f4f6 !important;
        background: #f9fafb !important;
    }
    .choices__inner {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.35rem 0.75rem;
    }
    .choices[data-type*="select-multiple"] .choices__button, .choices[data-type*="text"] .choices__button {
        border-left: 1px solid rgba(255,255,255,0.3);
    }
    .choices__list--multiple .choices__item {
        background-color: #10b981;
        border: 1px solid #059669;
        border-radius: 0.5rem;
    }

    /* Dark Mode Overrides */
    html.dark .ck-editor__editable_inline {
        background: #18181b !important; /* zinc-900 */
        border-color: #27272a !important; /* zinc-800 */
        color: #f4f4f5 !important; /* zinc-100 */
    }
    html.dark .ck-toolbar {
        background: #09090b !important; /* zinc-950 */
        border-color: #27272a !important; /* zinc-800 */
    }
    html.dark .ck.ck-button {
        color: #a1a1aa !important; /* zinc-400 */
    }
    html.dark .ck.ck-button:hover, html.dark .ck.ck-button.ck-on {
        background: #27272a !important; /* zinc-800 */
        color: #10b981 !important; /* emerald-500 */
    }
    
    html.dark .choices__inner {
        background-color: #18181b; /* zinc-900 */
        border-color: #27272a; /* zinc-800 */
        color: #f4f4f5; /* zinc-100 */
    }
    html.dark .choices__input {
        background-color: transparent !important;
        color: #f4f4f5 !important;
    }
    html.dark .choices__list--dropdown {
        background-color: #18181b; /* zinc-900 */
        border-color: #27272a; /* zinc-800 */
        color: #f4f4f5; /* zinc-100 */
    }
    html.dark .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #27272a; /* zinc-800 */
    }
</style>
@endsection

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 tracking-tight transition-colors">Tạo thông báo mới</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1 transition-colors">Soạn thảo và chọn nhóm đối tượng nhận thông báo</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Trở về
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-zinc-800 overflow-hidden transition-colors">
        
        @if($errors->any())
            <div class="mx-6 mt-6 px-4 py-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-800 dark:text-red-400 rounded-xl animate-fade-in-up transition-colors">
                <div class="flex gap-2">
                    <i class="bi bi-exclamation-octagon-fill text-lg shrink-0 mt-0.5"></i>
                    <ul class="text-sm font-medium list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf
            
            <div class="p-6 md:p-8 space-y-8">
                <!-- Tiêu đề -->
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-zinc-200 mb-1.5 transition-colors">Tiêu đề thông báo <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Nhập tiêu đề thông báo..." required
                           class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-950/50 border border-gray-200 dark:border-zinc-800/80 rounded-xl text-base text-gray-800 dark:text-zinc-200 focus:bg-white dark:focus:bg-zinc-950 focus:ring-2 focus:ring-emerald-100 dark:focus:ring-emerald-500/20 focus:border-emerald-400 dark:focus:border-emerald-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-zinc-600 @error('title') border-red-300 dark:border-red-500/50 ring-4 ring-red-100 dark:ring-red-500/20 focus:border-red-400 dark:focus:border-red-500 focus:ring-red-100 dark:focus:ring-red-500/20 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1 transition-colors"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Nội dung (CKEditor 5) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-zinc-200 mb-1.5 transition-colors">Nội dung chi tiết <span class="text-red-500">*</span></label>
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-zinc-800 @error('content') border-red-300 dark:border-red-500/50 ring-4 ring-red-100 dark:ring-red-500/20 @enderror transition-colors text-gray-900 dark:text-zinc-100">
                        <textarea name="content" id="notifContent">{{ old('content') }}</textarea>
                    </div>
                    @error('content')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1 transition-colors"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Đối tượng nhận -->
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-zinc-200 mb-1 transition-colors">Đối tượng nhận <span class="text-red-500">*</span></label>
                        <p class="text-sm text-gray-500 dark:text-zinc-400 transition-colors">Chọn một hoặc nhiều nhóm tài khoản sẽ nhận được thông báo này trên hệ thống.</p>
                        @if($errors->has('recipients'))
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1 transition-colors"><i class="bi bi-exclamation-circle"></i> {{ $errors->first('recipients') }}</p>
                        @endif
                    </div>

                    <!-- Grid Nhóm người dùng -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <!-- All -->
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 dark:border-zinc-800 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-zinc-800/50 has-[:checked]:border-emerald-500 dark:has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-500/10 group">
                            <input type="checkbox" name="recipients[]" value="all" class="peer w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-zinc-950 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 pointer-events-none transition-colors" {{ in_array('all', old('recipients', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-zinc-200 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-400 transition-colors"><i class="bi bi-people-fill text-emerald-500 me-1.5"></i>Tất cả</div>
                                <div class="text-[11px] text-gray-500 dark:text-zinc-500 transition-colors">Mọi người dùng</div>
                            </div>
                        </label>
                        <!-- Editor -->
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 dark:border-zinc-800 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-zinc-800/50 has-[:checked]:border-emerald-500 dark:has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-500/10 group">
                            <input type="checkbox" name="recipients[]" value="editor" class="peer w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-zinc-950 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 pointer-events-none transition-colors" {{ in_array('editor', old('recipients', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-zinc-200 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-400 transition-colors"><i class="bi bi-pencil-square text-blue-500 me-1.5"></i>Biên tập</div>
                                <div class="text-[11px] text-gray-500 dark:text-zinc-500 transition-colors">Nhóm Editor</div>
                            </div>
                        </label>
                        <!-- Contributor -->
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 dark:border-zinc-800 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-zinc-800/50 has-[:checked]:border-emerald-500 dark:has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-500/10 group">
                            <input type="checkbox" name="recipients[]" value="contributor" class="peer w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-zinc-950 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 pointer-events-none transition-colors" {{ in_array('contributor', old('recipients', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-zinc-200 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-400 transition-colors"><i class="bi bi-pen text-indigo-500 me-1.5"></i>Cộng tác viên</div>
                                <div class="text-[11px] text-gray-500 dark:text-zinc-500 transition-colors">Nhóm Contributor</div>
                            </div>
                        </label>
                        <!-- Reader -->
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 dark:border-zinc-800 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-zinc-800/50 has-[:checked]:border-emerald-500 dark:has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-500/10 group">
                            <input type="checkbox" name="recipients[]" value="reader" class="peer w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-zinc-950 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 pointer-events-none transition-colors" {{ in_array('reader', old('recipients', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-zinc-200 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-400 transition-colors"><i class="bi bi-person text-amber-500 me-1.5"></i>Độc giả</div>
                                <div class="text-[11px] text-gray-500 dark:text-zinc-500 transition-colors">Nhóm Reader</div>
                            </div>
                        </label>
                        <!-- Admin -->
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 dark:border-zinc-800 rounded-xl cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-zinc-800/50 has-[:checked]:border-emerald-500 dark:has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-500/10 group">
                            <input type="checkbox" name="recipients[]" value="admin" class="peer w-4 h-4 text-emerald-600 bg-gray-100 dark:bg-zinc-950 border-gray-300 dark:border-zinc-700 rounded focus:ring-emerald-500 dark:focus:ring-emerald-500/50 pointer-events-none transition-colors" {{ in_array('admin', old('recipients', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-zinc-200 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-400 transition-colors"><i class="bi bi-shield-lock-fill text-red-500 me-1.5"></i>Quản trị viên</div>
                                <div class="text-[11px] text-gray-500 dark:text-zinc-500 transition-colors">Nhóm Admin</div>
                            </div>
                        </label>
                    </div>

                    <!-- Gửi đích danh -->
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-zinc-200 mb-1 transition-colors">Gửi đích danh (Tuỳ chọn)</label>
                        <p class="text-sm text-gray-500 dark:text-zinc-400 mb-3 transition-colors">Bạn có thể gõ tên hoặc email để tìm và chọn thêm từng cá nhân nhận thông báo.</p>
                        <select name="recipients[]" id="specificUsers" multiple>
                            @foreach($groupedUsers as $role => $users)
                                <optgroup label="Nhóm {{ ucfirst($role) }}">
                                    @foreach($users as $user)
                                        <option value="user_{{ $user->id }}" {{ in_array('user_'.$user->id, old('recipients', [])) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 bg-gray-50/50 dark:bg-zinc-950/40 border-t border-gray-100 dark:border-zinc-800 flex flex-wrap items-center justify-end gap-3 rounded-b-2xl transition-colors">
                <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 rounded-xl transition-colors shadow-sm order-3 md:order-1">
                    Hủy bỏ
                </a>
                <button type="button" onclick="window.confirmSubmit(event, this, 'draft', 'Bạn có muốn LƯU NHÁP bản thông báo này để xem lại sau không?')" class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-zinc-800 border-2 border-emerald-500 dark:border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-700 dark:hover:text-emerald-500 font-bold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/30 focus:outline-none order-2">
                    <i class="bi bi-save"></i> Lưu nháp
                </button>
                <button type="button" onclick="window.confirmSubmit(event, this, 'send', 'Bạn có chắc chắn muốn TẠO & GỬI NGAY thông báo này? Hệ thống sẽ phát hành thông báo và bắn Hàng loạt Email đến người nhận (việc này có thể mất vài giây).')" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl shadow-sm transition-all shadow-[0_4px_14px_0_rgb(5,150,105,39%)] hover:shadow-[0_6px_20px_rgba(5,150,105,23%)] focus:ring-2 focus:ring-emerald-200 dark:focus:ring-emerald-500/30 focus:outline-none order-1 md:order-3">
                    <i class="bi bi-send-fill text-sm"></i> Phát hành ngay
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    setTimeout(function() {
        const element = document.getElementById('specificUsers');
        if (element) {
            new Choices(element, {
                removeItemButton: true,
                searchPlaceholderValue: 'Tìm tên hoặc email...',
                placeholderValue: 'Gõ để tìm người dùng cụ thể...',
                noResultsText: 'Không tìm thấy kết quả',
                noChoicesText: 'Không còn người dùng nào để chọn',
                itemSelectText: 'Nhấn để chọn'
            });
        }
    }, 100);
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
