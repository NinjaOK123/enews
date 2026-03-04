@extends('layouts.frontend')

@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Đăng Nhập</h3>

                {{-- Hiển thị lỗi chung hoặc từ middleware --}}
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="login" class="form-label">Email hoặc Username <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('login') is-invalid @enderror" 
                               id="login" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               autofocus 
                               placeholder="nhapsv@student.agu.edu.vn">
                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                    </div>
                </form>

                <hr class="my-4">
                <div class="text-center">
                    <p class="text-muted">Hoặc đăng nhập bằng tài khoản trường</p>
                    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger w-100">
                        <i class="fab fa-google me-2"></i>Đăng nhập bằng Google AGU
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
