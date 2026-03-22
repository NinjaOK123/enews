<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>University Contributor Profile</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,200..800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2a7c27",
                        "accent-gold": "#D4AF37",
                        "background-light": "#f6f8f6",
                        "background-dark": "#141e14",
                    },
                    fontFamily: {
                        "display": ["Newsreader", "serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-header {
            background: rgba(20, 30, 20, 0.8);
            backdrop-filter: blur(20px);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
<header class="sticky top-0 z-50 glass-header border-b border-primary/20 px-4 py-3 flex items-center justify-between">
<button onclick="window.location.href='{{ route('home') }}'" class="flex items-center justify-center p-2 text-primary dark:text-slate-100">
<span class="material-symbols-outlined">arrow_back_ios</span>
</button>
<h1 class="text-lg font-bold tracking-tight">Contributor Profile</h1>
@if(auth()->id() === $user->id)
<form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
  @csrf
  <button type="submit" class="flex items-center justify-center p-2 text-red-500 dark:text-red-400" title="Đăng xuất">
    <span class="material-symbols-outlined">logout</span>
  </button>
</form>
@endif
</header>
<main class="flex-1 pb-24">
<!-- Alert Messages -->
@if(session('success'))
<div class="m-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="m-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  {{ session('error') }}
</div>
@endif
@error('avatar')
<div class="m-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  {{ $message }}
</div>
@enderror
<section class="relative px-6 pt-8 pb-6 flex flex-col items-center text-center">
<div class="absolute inset-0 bg-gradient-to-b from-primary/20 to-transparent -z-10 h-64"></div>
<div class="relative mb-4 group">
<div class="size-32 rounded-full border-2 border-accent-gold p-1">
@php
    $avatarUrl = filter_var($user->avatar, FILTER_VALIDATE_URL)
        ? $user->avatar
        : ($user->avatar
            ? asset('storage/' . $user->avatar) . '?v=' . (@filemtime(storage_path('app/public/' . $user->avatar)) ?: time())
            : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2a7c27&color=fff&size=200');
@endphp
<div class="size-full rounded-full bg-cover bg-center" data-alt="Avatar" style="background-image: url('{{ $avatarUrl }}')"></div>
</div>
@if($isEditable)
<button onclick="document.getElementById('avatarInput').click()" class="absolute bottom-1 right-1 bg-accent-gold text-background-dark size-8 rounded-full flex items-center justify-center shadow-lg hover:bg-yellow-500 transition-colors">
<span class="material-symbols-outlined text-sm">edit</span>
</button>
<form id="avatarForm" action="{{ route('profile.avatar', $user->id) }}" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="
        var f = this.files[0];
        if (f && f.size > 5*1024*1024) { alert('Ảnh quá lớn! Vui lòng chọn ảnh nhỏ hơn 5MB.'); this.value=''; return; }
        document.getElementById('avatarForm').submit();
    ">
</form>
</form>
@endif
</div>
<div class="space-y-1">
<h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">{{ $user->name }}</h2>
<p class="text-primary dark:text-primary font-medium">{{ ucfirst($user->role) }} • Joined {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</p>
<p class="text-slate-500 dark:text-slate-400 text-sm">{{ $user->email }}</p>
</div>
<div class="mt-6 w-full max-w-sm">
@if($isEditable)
<button onclick="document.getElementById('avatarInput').click()" class="w-full bg-primary text-white py-3 rounded-xl font-bold tracking-wide hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
    Edit Profile
</button>
@endif
</div>
</section>
<section class="px-4 py-6 grid grid-cols-2 gap-4">
<div class="glass-card rounded-2xl p-5 flex flex-col items-start gap-3">
<span class="material-symbols-outlined text-accent-gold text-3xl">auto_stories</span>
<div>
<p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($articlesCount) }}</p>
<p class="text-xs uppercase tracking-widest font-semibold text-slate-500 dark:text-slate-400">Articles</p>
</div>
</div>
<div class="glass-card rounded-2xl p-5 flex flex-col items-start gap-3">
<span class="material-symbols-outlined text-accent-gold text-3xl">visibility</span>
<div>
<p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($totalViews) }}</p>
<p class="text-xs uppercase tracking-widest font-semibold text-slate-500 dark:text-slate-400">Total Views</p>
</div>
</div>
</section>
<section class="px-4 py-4">
<div class="flex items-center justify-between mb-6">
<h3 class="text-xl font-bold tracking-tight">My Articles</h3>
<a href="{{ route('contributor.dashboard') }}" class="text-primary text-sm font-bold flex items-center gap-1">
    View All <span class="material-symbols-outlined text-xs">arrow_forward</span>
</a>
</div>
<div class="space-y-4">
@forelse($posts as $post)
<div class="glass-card rounded-xl p-4 flex gap-4">
<div class="size-20 rounded-lg bg-cover bg-center shrink-0" data-alt="Cover" style="background-image: url('{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://ui-avatars.com/api/?name=Article&background=2a7c27&color=fff' }}')"></div>
<div class="flex flex-col justify-between py-1 flex-1">
<div>
<div class="flex items-center justify-between mb-1">
@if($post->status == 'published')
<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-accent-gold/20 text-accent-gold border border-accent-gold/30">Published</span>
@else
<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary/20 text-primary border border-primary/30">{{ ucfirst($post->status) }}</span>
@endif
<span class="text-[10px] text-slate-500 font-medium">{{ $post->created_at->format('M d, Y') }}</span>
</div>
<a href="{{ route('post.show', $post->slug) }}" class="font-bold text-sm leading-tight line-clamp-2 hover:text-primary transition-colors">{{ $post->title }}</a>
</div>
</div>
</div>
@empty
<div class="text-center text-slate-500 py-6 text-sm">No articles yet.</div>
@endforelse
</div>
</section>
</main>
<nav class="fixed bottom-0 w-full glass-header border-t border-primary/20 px-4 pb-6 pt-2">
<div class="flex items-center justify-around">
<a class="flex flex-col items-center gap-1 text-slate-500" href="#">
<span class="material-symbols-outlined">home</span>
<span class="text-[10px] font-bold uppercase tracking-tighter">Home</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-500" href="#">
<span class="material-symbols-outlined">search</span>
<span class="text-[10px] font-bold uppercase tracking-tighter">Search</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-500" href="#">
<span class="material-symbols-outlined">newspaper</span>
<span class="text-[10px] font-bold uppercase tracking-tighter">My News</span>
</a>
<a class="flex flex-col items-center gap-1 text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">person</span>
<span class="text-[10px] font-bold uppercase tracking-tighter">Profile</span>
</a>
</div>
</nav>
</div>
</body></html>