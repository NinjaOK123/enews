@extends('layouts.admin')
@section('title', 'Quản lý Cộng tác viên')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">🤝 Yêu cầu Cộng tác viên</h1>
      <p class="text-sm text-gray-500 mt-1">Duyệt hoặc từ chối yêu cầu trở thành cộng tác viên</p>
    </div>
    <div class="flex gap-2 text-sm">
      <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-semibold">⏳ Chờ: {{ $counts['pending'] }}</span>
      <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold">✅ Đã duyệt: {{ $counts['approved'] }}</span>
      <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold">❌ Từ chối: {{ $counts['rejected'] }}</span>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
  <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-4 text-sm">{{ session('success') }}</div>
  @endif

  {{-- Tab filter --}}
  <div class="flex gap-2 mb-5">
    @foreach(['pending' => '⏳ Chờ duyệt', 'approved' => '✅ Đã duyệt', 'rejected' => '❌ Từ chối', 'all' => 'Tất cả'] as $s => $label)
    <a href="{{ route('admin.contributor.index', ['status' => $s]) }}"
       class="px-4 py-2 rounded-lg text-sm font-semibold transition
              {{ $status === $s ? 'bg-[#2a7a27] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-[#2a7a27]' }}">
      {{ $label }}
    </a>
    @endforeach
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
          <tr>
            <th class="px-5 py-3 text-left">Người dùng</th>
            <th class="px-5 py-3 text-left">Họ tên</th>
            <th class="px-5 py-3 text-left">Ngân hàng</th>
            <th class="px-5 py-3 text-left">Số TK</th>
            <th class="px-5 py-3 text-left">Chủ TK</th>
            <th class="px-5 py-3 text-left">Ghi chú</th>
            <th class="px-5 py-3 text-left">Ngày gửi</th>
            <th class="px-5 py-3 text-left">Trạng thái</th>
            <th class="px-5 py-3 text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($requests as $req)
          <tr class="hover:bg-gray-50 transition" x-data="{ rejectOpen: false }">
            <td class="px-5 py-3">
              <div class="font-semibold text-gray-800">{{ $req->user->name }}</div>
              <div class="text-gray-400 text-xs">{{ $req->user->email }}</div>
            </td>
            <td class="px-5 py-3 font-medium">{{ $req->full_name }}</td>
            <td class="px-5 py-3">{{ $req->bank_name }}</td>
            <td class="px-5 py-3 font-mono">{{ $req->bank_account }}</td>
            <td class="px-5 py-3">{{ $req->account_holder }}</td>
            <td class="px-5 py-3 text-gray-500 max-w-xs truncate">{{ $req->note ?? '—' }}</td>
            <td class="px-5 py-3 text-gray-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
            <td class="px-5 py-3">
              @if($req->status === 'pending')
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-bold">⏳ Chờ</span>
              @elseif($req->status === 'approved')
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-bold">✅ Duyệt</span>
              @else
                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-bold">❌ Từ chối</span>
                @if($req->admin_note)
                  <div class="text-xs text-red-500 mt-1">{{ $req->admin_note }}</div>
                @endif
              @endif
            </td>
            <td class="px-5 py-3">
              @if($req->status === 'pending')
              <div class="flex items-center gap-2 justify-center">
                {{-- Approve --}}
                <form action="{{ route('admin.contributor.approve', $req) }}" method="POST">
                  @csrf
                  <button type="submit"
                          onclick="return confirm('Duyệt và cấp quyền Cộng tác viên cho {{ $req->full_name }}?')"
                          class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                    ✅ Duyệt
                  </button>
                </form>

                {{-- Reject --}}
                <button @click="rejectOpen = true"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                  ❌ Từ chối
                </button>

                {{-- Reject modal --}}
                <div x-show="rejectOpen" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                  <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                    <h3 class="font-bold text-gray-900 mb-3">Lý do từ chối</h3>
                    <form action="{{ route('admin.contributor.reject', $req) }}" method="POST">
                      @csrf
                      <textarea name="admin_note" rows="3"
                                placeholder="Ví dụ: Thông tin ngân hàng không hợp lệ..."
                                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 resize-none mb-3"></textarea>
                      <div class="flex gap-2">
                        <button type="button" @click="rejectOpen = false"
                                class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-xl text-sm font-semibold">Hủy</button>
                        <button type="submit"
                                class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-bold">Xác nhận từ chối</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @else
                <span class="text-gray-400 text-xs">{{ $req->reviewed_at ? $req->reviewed_at->format('d/m/Y') : '—' }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-12 text-gray-400">
              Không có yêu cầu nào.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
      {{ $requests->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
