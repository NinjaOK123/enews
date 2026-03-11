@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 text-center">
                    <h4 class="card-title fw-bold mb-4 text-success">Thông tin cá nhân</h4>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @error('avatar')
                        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror
                    
                    <div class="mb-4 d-flex flex-column align-items-center">
                        <div style="cursor: pointer; position: relative; display: inline-block;" onclick="document.getElementById('avatarInput').click();" title="Nhấp để thay đổi ảnh đại diện">
                            <div style="width: 120px; height: 120px;">
                                @if($user->avatar)
                                    <img id="avatarPreview" src="{{ filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle shadow-sm w-100 h-100" style="object-fit: cover; border: 4px solid #fff;">
                                @else
                                    <img id="avatarPreview" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=200" alt="Avatar" class="rounded-circle shadow-sm w-100 h-100" style="object-fit: cover; border: 4px solid #fff;">
                                @endif
                            </div>
                            <div class="mt-2 text-primary fw-medium text-center" style="font-size: 0.85rem;">
                                <i class="bi bi-camera-fill me-1"></i>Thay đổi ảnh
                            </div>
                        </div>
                    </div>

                    <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewAndSubmitAvatar(this);">
                    </form>
                    
                    <script>
                        function previewAndSubmitAvatar(input) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    document.getElementById('avatarPreview').src = e.target.result;
                                };
                                reader.readAsDataURL(input.files[0]);
                                
                                // Auto submit form after picking
                                document.getElementById('avatarForm').submit();
                            }
                        }
                    </script>

                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-4">{{ $user->email }}</p>

                    <div class="text-start bg-light p-3 rounded-3">
                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-secondary"><i class="bi bi-person-badge me-2"></i>Vai trò:</span>
                            <span class="fw-semibold text-primary">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary"><i class="bi bi-calendar-check me-2"></i>Ngày tạo tài khoản:</span>
                            <span class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'Đang cập nhật' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 rounded-pill"><i class="bi bi-arrow-left me-2"></i>Trở lại trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
