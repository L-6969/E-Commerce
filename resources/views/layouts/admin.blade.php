@php $u = auth()->user(); $isOwner = $u && $u->role === 'owner'; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') · {{ $settings->name ?? 'Cilok Sapi SR' }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Franklin:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
@stack('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body>
<header class="sticky top-0 z-20 bg-forest text-white">
<div class="mx-auto flex max-w-[1000px] flex-wrap items-center gap-3 p-4">
  <a class="mr-auto text-lg font-extrabold no-underline" href="{{ route('admin.orders') }}">Admin · {{ $settings->name ?? '' }}</a>
  <nav class="nav" aria-label="Admin">
    <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'on' : '' }}">Pesanan @if(($newOrdersCount ?? 0) > 0)<span class="dot">{{ $newOrdersCount }}</span>@endif</a>
    <a href="{{ route('admin.offline') }}" class="{{ request()->routeIs('admin.offline*') ? 'on' : '' }}">Input offline</a>
    <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'on' : '' }}">Produk</a>
    @if($isOwner)<a href="{{ route('admin.report') }}" class="{{ request()->routeIs('admin.report') ? 'on' : '' }}">Laporan</a>@endif
    <a href="{{ route('admin.stock') }}" class="{{ request()->routeIs('admin.stock*') ? 'on' : '' }}">Stok bahan</a>
    @if($isOwner)<a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'on' : '' }}">Pengaturan</a>@endif
    <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'on' : '' }}">Log</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn s sm">Keluar ({{ $u->name ?? '' }})</button></form>
  </nav>
</div></header>
<main class="mx-auto max-w-[1000px] p-4">
  @if (session('ok'))<div class="ok" role="status">{{ session('ok') }}</div>@endif
  @if ($errors->any())<div class="card text-[13px] text-er" role="alert">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  @yield('content')
</main>
</body>
</html>
