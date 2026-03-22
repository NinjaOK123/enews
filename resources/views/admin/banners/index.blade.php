@extends('layouts.admin')

@section('title', 'Quản lý Banner Cuộc thi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">🖼️ Banner Cuộc thi</h1>
      <p class="text-sm text-gray-500 mt-1">Quản lý banner chạy trên trang chủ</p>
    </div>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
  <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
  </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ══ Form Thêm Banner ══ --}}
    <div class="lg:col-span-1">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-base font-semibold text-gray-700 mb-4">➕ Thêm Banner Mới</h2>
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên banner <span class="text-red-500">*</span></label>
            <input type="text" name="title" required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="Cuộc thi Sáng tác 2026...">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh banner <span class="text-red-500">*</span></label>
            <input type="file" name="image" required accept="image/*"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP. Tối đa 5MB. Tỷ lệ ngang (landscape) đẹp hơn.</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Link khi click</label>
            <input type="url" name="link"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
              placeholder="https://...">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
              <input type="number" name="order" value="0" min="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-end pb-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-green-600">
                <span class="text-sm text-gray-700">Hiển thị</span>
              </label>
            </div>
          </div>
          <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm">
            ✅ Thêm Banner
          </button>
        </form>
      </div>
    </div>

    {{-- ══ Danh sách Banner ══ --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
          <h2 class="text-base font-semibold text-gray-700">📋 Danh sách Banner</h2>
          <span class="text-sm text-gray-400">{{ $banners->count() }} banner</span>
        </div>

        @if($banners->isEmpty())
        <div class="text-center py-12 text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <p class="text-sm">Chưa có banner nào. Thêm banner đầu tiên!</p>
        </div>
        @else
        <div class="divide-y divide-gray-100">
          @foreach($banners as $banner)
          <div class="p-4 flex items-start gap-4 hover:bg-gray-50 transition" id="banner-{{ $banner->id }}">

            {{-- Ảnh preview --}}
            <div class="flex-shrink-0">
              @if($banner->link)
              <a href="{{ $banner->link }}" target="_blank">
                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                     class="w-28 h-16 object-cover rounded-lg border border-gray-200">
              </a>
              @else
              <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                   class="w-28 h-16 object-cover rounded-lg border border-gray-200">
              @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ $banner->title }}</p>
              @if($banner->link)
              <a href="{{ $banner->link }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate block">{{ Str::limit($banner->link, 40) }}</a>
              @endif
              <div class="flex items-center gap-2 mt-1">
                <span class="text-xs text-gray-400">Thứ tự: {{ $banner->order }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                  {{ $banner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                  {{ $banner->is_active ? '● Hiển thị' : '○ Ẩn' }}
                </span>
              </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
              {{-- Toggle active --}}
              <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                @csrf
                <button type="submit" title="{{ $banner->is_active ? 'Ẩn banner' : 'Hiện banner' }}"
                  class="p-1.5 rounded-lg {{ $banner->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }} transition">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="{{ $banner->is_active ? 'M10 12a2 2 0 100-4 2 2 0 000 4z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' : 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21' }}"/>
                  </svg>
                </button>
              </form>

              {{-- Edit (inline modal) --}}
              <button onclick="openEdit({{ $banner->id }}, '{{ addslashes($banner->title) }}', '{{ $banner->link }}', {{ $banner->order }}, {{ $banner->is_active ? 1 : 0 }})"
                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>

              {{-- Delete --}}
              <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                    onsubmit="return confirm('Xóa banner này?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/50" onclick="closeEdit()"></div>
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="text-base font-semibold text-gray-800 mb-4">✏️ Sửa Banner</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tên banner</label>
        <input type="text" name="title" id="editTitle" required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh mới (để trống nếu giữ ảnh cũ)</label>
        <input type="file" name="image" accept="image/*"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Link khi click</label>
        <input type="url" name="link" id="editLink"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
          <input type="number" name="order" id="editOrder" min="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end pb-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" id="editActive" value="1" class="w-4 h-4 text-green-600">
            <span class="text-sm text-gray-700">Hiển thị</span>
          </label>
        </div>
      </div>
      <div class="flex gap-3">
        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm">
          💾 Lưu thay đổi
        </button>
        <button type="button" onclick="closeEdit()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition text-sm">
          Hủy
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id, title, link, order, isActive) {
  document.getElementById('editTitle').value = title;
  document.getElementById('editLink').value = link || '';
  document.getElementById('editOrder').value = order;
  document.getElementById('editActive').checked = isActive === 1;
  document.getElementById('editForm').action = '/admin/banners/' + id;
  document.getElementById('editModal').classList.remove('hidden');
}
function closeEdit() {
  document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection
