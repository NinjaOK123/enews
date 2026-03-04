@extends('layouts.app')
@section('title', 'Chuyên mục')
@section('content')
<div class="container py-5 text-center">
  <h1 style="color:var(--primary);">📂 Chuyên mục: {{ Str::title(str_replace('-', ' ', $slug)) }}</h1>
  <p class="text-muted mt-2">Đang tải danh sách bài viết...</p>
  <a href="{{ route('home') }}" class="btn btn-primary-agu mt-3">← Về trang chủ</a>
</div>
@endsection
