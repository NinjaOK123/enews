@extends('layouts.admin')
@section('title', 'Chỉnh sửa thông báo')

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
</style>
@endsection

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Chỉnh sửa thông báo: <span class="text-emerald-600">#{{ $notification->id }}</span></h3>
            <p class="text-sm text-gray-500 mt-1">Cập nhật nội dung hoặc gửi lại thông báo đã tạo</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i> Trở về
        </a>
    </div>

    <!-- Warning if already sent -->
    @if($notification->sent_at)
        <div class="mb-6 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl flex items-center gap-3 animate-fade-in-up">
            <i class="bi bi-exclamation-triangle-fill text-xl text-amber-500 shrink-0"></i> 
            <span class="text-sm font-medium">Thông báo này đã được phát hành lúc <strong>{{ $notification->sent_at->format('d/m/Y H:i') }}</strong>. Bạn vẫn có thể chỉnh sửa nhưng sẽ không cập nhật nội dung cho phiên bản đã gửi tới email. Để cập nhật tới người nhận, bạn phải bấm "Gửi lại".</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        @if($errors->any())
            <div class="mx-6 mt-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl animate-fade-in-up">
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

        <form action="{{ route('admin.notifications.update', $notification) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="p-6 md:p-8 space-y-8">
                <!-- Tiêu đề -->
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-1.5">Tiêu đề thông báo <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $notification->title) }}" placeholder="Nhập tiêu đề thông báo..." required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-base focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all placeholder:text-gray-400 @error('title') border-red-300 ring-4 ring-red-100 focus:border-red-400 focus:ring-red-100 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-sm text-red-500 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Nội dung (CKEditor 5) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nội dung chi tiết <span class="text-red-500">*</span></label>
                    <div class="rounded-xl overflow-hidden border border-gray-200 @error('content') border-red-300 ring-4 ring-red-100 @enderror">
                        <textarea name="content" id="notifContent">{{ old('content', $notification->content) }}</textarea>
                    </div>
                    @error('content')
                        <p class="mt-1.5 text-sm text-red-500 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Đối tượng nhận -->
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Đối tượng nhận <span class="text-red-500">*</span></label>
                        <p class="text-sm text-gray-500">Chọn một hoặc nhiều nhóm tài khoản sẽ nhận được thông báo này trên hệ thống.</p>
                        @if($errors->has('recipients'))
                            <p class="mt-1.5 text-sm text-red-500 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $errors->first('recipients') }}</p>
                        @endif
                    </div>

                    @php
                        $currentRecipients = old('recipients', $notification->recipients ?? []);
                    @endphp

                    <!-- Grid Nhóm người dùng -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach([
                            'all'         => ['icon' => 'bi-people-fill text-emerald-500',  'label' => 'Tất cả',          'sub' => 'Mọi người dùng'],
                            'editor'      => ['icon' => 'bi-pencil-square text-blue-500',   'label' => 'Biên tập',        'sub' => 'Nhóm Editor'],
                            'contributor' => ['icon' => 'bi-pen text-indigo-500',           'label' => 'Cộng tác viên',   'sub' => 'Nhóm Contributor'],
                            'reader'      => ['icon' => 'bi-person text-amber-500',         'label' => 'Độc giả',         'sub' => 'Nhóm Reader'],
                            'admin'       => ['icon' => 'bi-shield-fill text-red-500',      'label' => 'Admin',           'sub' => 'Nhóm Admin'],
                        ] as $value => $info)
                        <label class="relative flex items-center gap-3 p-3 border-2 border-gray-100 rounded-xl cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 group">
                            <input type="checkbox" name="recipients[]" value="{{ $value }}" class="peer w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 pointer-events-none" {{ in_array($value, $currentRecipients) ? 'checked' : '' }}>
                            <div>
                                <div class="font-bold text-sm text-gray-900 group-has-[:checked]:text-emerald-700"><i class="bi {{ $info['icon'] }} me-1.5"></i>{{ $info['label'] }}</div>
                                <div class="text-[11px] text-gray-500">{{ $info['sub'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <!-- Gửi đích danh -->
                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Gửi đích danh (Tuỳ chọn)</label>
                        <p class="text-sm text-gray-500 mb-3">Bạn có thể gõ tên hoặc email để tìm và chọn thêm từng cá nhân nhận thông báo.</p>
                        <select name="recipients[]" id="specificUsers" multiple>
                            @foreach($groupedUsers as $role => $users)
                                <optgroup label="Nhóm {{ ucfirst($role) }}">
                                    @foreach($users as $user)
                                        <option value="user_{{ $user->id }}" {{ in_array('user_'.$user->id, $currentRecipients) ? 'selected' : '' }}>
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
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-wrap items-center justify-end gap-3 rounded-b-2xl">
                <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-colors shadow-sm order-3 md:order-1">
                    Hủy bỏ
                </a>
                <button type="button" onclick="window.confirmSubmit(event, this, 'draft', 'Bạn có muốn LƯU THAY ĐỔI thông báo này không?')" class="flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 font-bold rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-200 focus:outline-none order-2">
                    <i class="bi bi-save"></i> Lưu cập nhật
                </button>
                <button type="button" onclick="window.confirmSubmit(event, this, 'send', 'Bạn có chắc chắn muốn TẠO & GỬI NGAY thông báo này? Hệ thống sẽ phát hành thông báo và bắn Hàng loạt Email đến người nhận (việc này có thể mất vài giây).')" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-xl shadow-sm transition-all shadow-emerald-200 focus:ring-2 focus:ring-emerald-200 focus:outline-none order-1 md:order-3">
                    <i class="bi bi-send-fill text-sm"></i> Phát hành lại
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
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
