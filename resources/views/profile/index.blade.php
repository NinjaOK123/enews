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
                    
                    <div class="mb-4 position-relative d-inline-block">
                        <div style="cursor: pointer; position: relative" onclick="document.getElementById('avatarInput').click();" title="Nhấp để thay đổi ảnh đại diện">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover; border: 4px solid #fff;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=200" alt="Avatar" class="rounded-circle shadow-sm" width="120" height="120" style="border: 4px solid #fff;">
                            @endif
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm" style="transform: translate(-10%, -10%); border: 2px solid white;">
                                <i class="bi bi-camera-fill" style="font-size: 1rem;"></i>
                            </div>
                        </div>
                    </div>

                    <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                    </form>

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
