@extends('layouts.app')
@section('title', 'Bài viết')
@section('content')
<div class="container py-5 text-center">
  <h1 style="color:var(--primary);">📄 Chi tiết bài viết</h1>
  <p class="text-muted mt-2">Đang tải bài viết...</p>
  <a href="{{ route('home') }}" class="btn btn-primary-agu mt-3">← Về trang chủ</a>
</div>
@endsection
