<html class="dark"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
            "accent": "#D4AF37",
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
    .glass {
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
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
<!-- Header / Navigation Top -->
<div class="sticky top-0 z-50 glass px-4 py-3 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-accent" onclick="window.location.href='{{ route('home') }}'" style="cursor: pointer;">chevron_left</span>
<h1 class="text-lg font-bold">Reader Profile</h1>
</div>
@if(auth()->id() === $user->id)
<form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
  @csrf
  <button type="submit" class="p-2 rounded-full hover:bg-white/10" title="Đăng xuất">
    <span class="material-symbols-outlined text-red-500">logout</span>
  </button>
</form>
@endif
</div>
<main class="flex-1 overflow-y-auto pb-24">
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

<!-- Profile Header Section -->
<div class="relative pt-8 pb-12 px-6 flex flex-col items-center bg-gradient-to-b from-primary/20 to-transparent">
<div class="relative">
<div class="w-32 h-32 rounded-full border-4 border-accent/30 p-1 shadow-2xl">
<div class="w-full h-full rounded-full bg-cover bg-center" data-alt="Avatar of {{ $user->name }}" style="background-image: url('{{ filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : ($user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=2a7c27&color=fff&size=200') }}')"></div>
</div>
@if($isEditable)
<button onclick="document.getElementById('avatarInput').click()" class="absolute bottom-1 right-1 bg-accent text-background-dark p-2 rounded-full shadow-lg border-2 border-background-dark flex items-center justify-center hover:bg-yellow-500 transition-colors">
<span class="material-symbols-outlined text-sm font-bold">photo_camera</span>
</button>
<form id="avatarForm" action="{{ route('profile.avatar', $user->id) }}" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
</form>
@endif
</div>
<div class="mt-4 text-center">
<h2 class="text-2xl font-bold tracking-tight">{{ $user->name }}</h2>
<p class="text-slate-400 text-sm mb-1">{{ $user->email }}</p>
<div class="flex items-center justify-center gap-2 mt-2">
<span class="bg-accent/20 text-accent text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">{{ ucfirst($user->role) }}</span>
<span class="text-slate-500 text-xs">Joined {{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</span>
</div>
</div>
<div class="mt-8 w-full max-w-sm flex gap-3">
<button class="flex-1 bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-lg">edit</span>
          Edit Profile
        </button>
<button class="w-14 glass flex items-center justify-center rounded-xl">
<span class="material-symbols-outlined text-accent">share</span>
</button>
</div>
</div>
<!-- Stats Grid -->
<div class="px-6 grid grid-cols-3 gap-3 -mt-6">
<div class="glass p-4 rounded-2xl flex flex-col items-center text-center">
<span class="text-xl font-bold text-accent">128</span>
<span class="text-[10px] text-slate-400 uppercase font-bold mt-1">Articles</span>
</div>
<div class="glass p-4 rounded-2xl flex flex-col items-center text-center border-accent/20">
<span class="text-xl font-bold text-accent">45</span>
<span class="text-[10px] text-slate-400 uppercase font-bold mt-1">Saved</span>
</div>
<div class="glass p-4 rounded-2xl flex flex-col items-center text-center">
<span class="text-xl font-bold text-accent">12</span>
<span class="text-[10px] text-slate-400 uppercase font-bold mt-1">Comments</span>
</div>
</div>
<!-- Saved Articles Section -->
<div class="mt-10 px-6">
<div class="flex items-center justify-between mb-6">
<h3 class="text-xl font-bold">Saved Articles</h3>
<button class="text-accent text-sm font-bold">View All</button>
</div>
<div class="space-y-4">
<!-- Article Card 1 -->
<div class="glass rounded-2xl overflow-hidden flex gap-4 p-3 border-l-4 border-l-accent">
<div class="w-24 h-24 rounded-xl bg-cover bg-center shrink-0" data-alt="University campus building architecture at sunset" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBYor6_IuyswqZT4DF4oANybMsbUYxeNbLjhN-EPNUuVIM4OhHN7d02m0uAXkKUJIlIy0Ns_a-uT7PB5n5AlVpZ1Q6nU5aDedSDerRHfZJV7zRU_UukCrgKmKhYl_EovUnmj9AGkh2loGWSuVApWfWirTtRAnuaLXAf_-7OHR-OL2FhjNW6WN-3eH6SaBMkRKI9330h11p5rytZlygU-A8UAAmDrURO96LfD7zG9NRJdvp_490zRNMMA6ZN2kC4Wjwhx77ZBzY9ATM')"></div>
<div class="flex flex-col justify-between py-1">
<div>
<span class="text-[10px] font-bold text-accent uppercase tracking-widest">Research</span>
<h4 class="text-sm font-bold leading-tight mt-1 line-clamp-2">The Future of AI in Campus Management: A Comprehensive Study</h4>
</div>
<div class="flex items-center gap-2 text-slate-500 text-[10px]">
<span class="material-symbols-outlined text-xs">schedule</span>
<span>2 hours ago</span>
</div>
</div>
</div>
<!-- Article Card 2 -->
<div class="glass rounded-2xl overflow-hidden flex gap-4 p-3">
<div class="w-24 h-24 rounded-xl bg-cover bg-center shrink-0" data-alt="University library with rows of books and studying students" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDLCrmnx0YRxdv5N7HG39ksRi_qjEPnUqNndM74LgYbm0CsIGFlj-m_D_5sgDnD5-wBu-QqNgpihb2oNKaCNbsqGuImXUqGLrihBNjAE61UpDd2Veci1biugLisF2SQXExJMEO1WzvOYZmnkRwnxzalETJZbuyaO_kzeXqbvUj69UusJ1D6-ziIiAGwFOWCasAIaPeBWGTkGeyvG0GefClLT77SW1mpHa_hVr2EIYwZViJxRYwAKjbfnBZg4DgJcJzFpC2QYtUbl5M')"></div>
<div class="flex flex-col justify-between py-1">
<div>
<span class="text-[10px] font-bold text-accent uppercase tracking-widest">Campus Life</span>
<h4 class="text-sm font-bold leading-tight mt-1 line-clamp-2">New Library Wing Opening Ceremony Announced for October</h4>
</div>
<div class="flex items-center gap-2 text-slate-500 text-[10px]">
<span class="material-symbols-outlined text-xs">schedule</span>
<span>Yesterday</span>
</div>
</div>
</div>
<!-- Article Card 3 -->
<div class="glass rounded-2xl overflow-hidden flex gap-4 p-3">
<div class="w-24 h-24 rounded-xl bg-cover bg-center shrink-0" data-alt="Group of students collaborating in a modern workspace" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCgQdXiwY6C6F7UtUaWRtYBU7TaK3fQ8X7pown2JhMNbdcnisQkImsd5u8m1a1I8O-TFtS3wrnnpp2NckJcntKxRRU0Fr0YAVloxeIs6_Mo9EY8KmGfC0Me-PTaMYoEafD5qIg6cQ-0oDOPeJJpVguXZhsXjW_uIkMyhluT59YX7_Mg47BVkA6d8PWIxRItuDX0UxrBVNLWbf9NAwCCKjDWYTSKEFRDUC8nB2FEv6pkO-saOC_JrqDdrAQjd0PoDM_zmcITdWz2vVM')"></div>
<div class="flex flex-col justify-between py-1">
<div>
<span class="text-[10px] font-bold text-accent uppercase tracking-widest">Admissions</span>
<h4 class="text-sm font-bold leading-tight mt-1 line-clamp-2">Scholarship Applications Open for International Students 2024</h4>
</div>
<div class="flex items-center gap-2 text-slate-500 text-[10px]">
<span class="material-symbols-outlined text-xs">schedule</span>
<span>3 days ago</span>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- Bottom Navigation Bar -->
<div class="fixed bottom-0 left-0 right-0 glass border-t border-white/10 px-6 pb-6 pt-3 flex items-center justify-between z-50">
<a class="flex flex-col items-center gap-1 group" href="#">
<span class="material-symbols-outlined text-slate-500 group-hover:text-primary transition-colors">home</span>
<span class="text-[10px] font-medium text-slate-500">Home</span>
</a>
<a class="flex flex-col items-center gap-1 group" href="#">
<span class="material-symbols-outlined text-slate-500 group-hover:text-primary transition-colors">search</span>
<span class="text-[10px] font-medium text-slate-500">Explore</span>
</a>
<a class="flex flex-col items-center gap-1 group" href="#">
<span class="material-symbols-outlined text-slate-500 group-hover:text-primary transition-colors">bookmark</span>
<span class="text-[10px] font-medium text-slate-500">Saved</span>
</a>
<a class="flex flex-col items-center gap-1" href="#">
<div class="flex flex-col items-center gap-1">
<span class="material-symbols-outlined text-accent fill-1" style="font-variation-settings: 'FILL' 1;">person</span>
<span class="text-[10px] font-bold text-accent">Profile</span>
</div>
<div class="w-1 h-1 bg-accent rounded-full mt-0.5"></div>
</a>
</div>
</body></html>