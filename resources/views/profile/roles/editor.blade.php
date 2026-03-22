<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>University Editor Profile Variant 2</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2a7c27",
                        "accent-gold": "#d4af37",
                        "background-light": "#f6f8f6",
                        "background-dark": "#141e14",
                    },
                    fontFamily: {
                        "display": ["Work Sans"]
                    },
                    borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Work Sans', sans-serif;
        }
        .glass-panel {
            background: rgba(42, 124, 39, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="relative flex min-h-screen w-full flex-col max-w-[430px] mx-auto bg-background-light dark:bg-background-dark shadow-2xl overflow-hidden">
<!-- Header / Navigation -->
<div class="flex items-center justify-between p-4 sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
<button onclick="window.location.href='{{ route('home') }}'" class="flex items-center justify-center size-10 rounded-full hover:bg-primary/10 transition-colors">
<span class="material-symbols-outlined text-primary">arrow_back_ios_new</span>
</button>
<h1 class="text-lg font-bold tracking-tight">Editor Profile</h1>
@if(auth()->id() === $user->id)
<form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
  @csrf
  <button type="submit" class="flex items-center justify-center size-10 rounded-full hover:bg-red-500/10 transition-colors" title="Đăng xuất">
    <span class="material-symbols-outlined text-red-500">logout</span>
  </button>
</form>
@endif
</div>
<!-- Alert Messages -->
@if(session('success'))
<div class="mx-4 mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mx-4 mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  {{ session('error') }}
</div>
@endif
@error('avatar')
<div class="mx-4 mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  {{ $message }}
</div>
@enderror
<!-- Hero Section: Split Layout with Glassmorphism -->
<div class="px-4 pt-2 pb-6">
<div class="glass-panel rounded-xl p-6 relative overflow-hidden">
<!-- Decorative background elements -->
<div class="absolute -top-10 -right-10 size-32 bg-primary/20 rounded-full blur-3xl"></div>
<div class="absolute -bottom-10 -left-10 size-32 bg-accent-gold/10 rounded-full blur-3xl"></div>
<div class="flex flex-col md:flex-row gap-6 relative z-10">
<div class="flex flex-col items-center md:items-start gap-4 flex-1">
<div class="relative">
<div class="size-24 rounded-full border-4 border-primary/30 p-1">
@php
    $avatarUrl = filter_var($user->avatar, FILTER_VALIDATE_URL)
        ? $user->avatar
        : ($user->avatar
            ? asset('storage/' . $user->avatar) . '?v=' . (@filemtime(storage_path('app/public/' . $user->avatar)) ?: time())
            : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2a7c27&color=fff&size=200');
@endphp
<div class="size-full rounded-full bg-cover bg-center" data-alt="Avatar" style="background-image: url('{{ $avatarUrl }}')"></div>
</div>
<div class="absolute bottom-0 right-0 size-6 bg-primary rounded-full flex items-center justify-center border-2 border-background-dark">
<span class="material-symbols-outlined text-[14px] text-white">verified</span>
</div>
</div>
<div class="text-center md:text-left">
<h2 class="text-2xl font-bold">{{ $user->name }}</h2>
<p class="text-primary font-medium text-sm">{{ ucfirst($user->role) }}</p>
<p class="text-slate-500 dark:text-slate-400 text-xs">{{ $user->email }}</p>
</div>
@if($isEditable)
<button onclick="document.getElementById('avatarInput').click()" class="flex items-center gap-2 px-6 py-2 bg-primary dark:bg-primary text-white rounded-full font-semibold text-sm hover:opacity-90 transition-opacity">
<span class="material-symbols-outlined text-sm">edit</span>
    Edit Profile
</button>
<form id="avatarForm" action="{{ route('profile.avatar', $user->id) }}" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
</form>
@endif
</div>
<!-- Split Layout Stats Dashboard -->
<div class="flex flex-col gap-3 justify-center min-w-[140px]">
<div class="bg-white/5 dark:bg-black/20 rounded-lg p-3 border border-white/10 flex items-center gap-3">
<div class="flex flex-col">
<span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">In Review</span>
<span class="text-xl font-bold text-accent-gold">{{ $toReviewCount }}</span>
</div>
<div class="ml-auto">
<span class="material-symbols-outlined text-accent-gold opacity-80">pending_actions</span>
</div>
</div>
<div class="bg-white/5 dark:bg-black/20 rounded-lg p-3 border border-white/10 flex items-center gap-3">
<div class="flex flex-col">
<span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Approved</span>
<span class="text-xl font-bold text-primary">{{ $approvedCount }}</span>
</div>
<div class="ml-auto">
<span class="material-symbols-outlined text-primary opacity-80">task_alt</span>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Quick Stats Pill Widgets -->
<div class="flex gap-3 px-4 pb-6 overflow-x-auto no-scrollbar">
<div class="flex-none flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800/50 rounded-full border border-slate-200 dark:border-slate-700">
<span class="material-symbols-outlined text-accent-gold text-lg">workspace_premium</span>
<span class="text-xs font-semibold">Gold Curator</span>
</div>
<div class="flex-none flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800/50 rounded-full border border-slate-200 dark:border-slate-700">
<span class="material-symbols-outlined text-primary text-lg">trending_up</span>
<span class="text-xs font-semibold">+12% Impact</span>
</div>
<div class="flex-none flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800/50 rounded-full border border-slate-200 dark:border-slate-700">
<span class="material-symbols-outlined text-blue-500 text-lg">history_edu</span>
<span class="text-xs font-semibold">1.2k Articles</span>
</div>
</div>
<!-- Recent Activity Feed -->
<div class="flex-1 bg-white dark:bg-slate-900/30 rounded-t-[2.5rem] p-6 shadow-inner">
<div class="flex items-center justify-between mb-6">
<h3 class="text-lg font-bold">Recent Activity</h3>
<button class="text-primary text-sm font-semibold">View All</button>
</div>
<div class="space-y-6">
@forelse($recentActivities as $activity)
<div class="flex gap-4">
<div class="flex flex-col items-center">
<div class="size-10 rounded-full {{ $activity->status == 'published' ? 'bg-primary/10' : ($activity->status == 'pending' ? 'bg-accent-gold/10' : 'bg-slate-500/10') }} flex items-center justify-center">
<span class="material-symbols-outlined {{ $activity->status == 'published' ? 'text-primary' : ($activity->status == 'pending' ? 'text-accent-gold' : 'text-slate-500') }}">{{ $activity->status == 'published' ? 'check_circle' : ($activity->status == 'pending' ? 'pending' : 'edit_document') }}</span>
</div>
@if(!$loop->last)
<div class="w-0.5 h-full bg-slate-200 dark:bg-slate-800 mt-2"></div>
@endif
</div>
<div class="flex-1 pb-6">
<div class="flex items-center justify-between mb-1">
<span class="text-[10px] px-2 py-0.5 bg-primary/20 text-primary font-bold rounded-md uppercase">{{ $activity->category ? $activity->category->name : 'Uncategorized' }}</span>
<span class="text-[10px] text-slate-400">{{ $activity->updated_at->diffForHumans() }}</span>
</div>
<p class="font-semibold text-sm leading-snug">{{ ucfirst($activity->status) }}: "{{ Str::limit($activity->title, 40) }}"</p>
<p class="text-xs text-slate-500 mt-1">Author: {{ $activity->author->name ?? '-' }}</p>
</div>
</div>
@empty
<div class="text-center text-slate-500 py-6 text-sm">No recent activity found.</div>
@endforelse
</div>
</div>
<!-- Bottom Navigation Bar -->
<div class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark px-6 py-3 pb-8">
<div class="flex justify-between items-center">
<a class="flex flex-col items-center gap-1 text-slate-400" href="#">
<span class="material-symbols-outlined">newspaper</span>
<span class="text-[10px] font-medium">Feed</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span class="text-[10px] font-medium">Panel</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400" href="#">
<div class="size-12 rounded-full bg-primary flex items-center justify-center -mt-8 border-4 border-background-dark shadow-lg shadow-primary/30">
<span class="material-symbols-outlined text-white">add</span>
</div>
<span class="text-[10px] font-medium">Draft</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400" href="#">
<span class="material-symbols-outlined">search</span>
<span class="text-[10px] font-medium">Search</span>
</a>
<a class="flex flex-col items-center gap-1 text-primary" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">person</span>
<span class="text-[10px] font-medium">Profile</span>
</a>
</div>
</div>
</div>
</body></html>