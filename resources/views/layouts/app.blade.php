@php
  $home = request()->routeIs('home');
  $menuUrl = $home ? '#produk' : route('home') . '#produk';
  $aboutUrl = $home ? '#tentang' : route('home') . '#tentang';
  $wa = 'https://wa.me/' . $settings->wa . '?text=' . rawurlencode($settings->template);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', $settings->name ?? 'Cilok Sapi SR')</title>
<meta name="description" content="@yield('meta_description', 'Pesan cilok sapi khas Ciamis secara online. Buka ' . $settings->hours . '.')">
<meta name="robots" content="@yield('robots', 'index,follow')">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $settings->name }}">
<meta property="og:title" content="@yield('title', $settings->name)">
<meta property="og:description" content="@yield('meta_description', 'Pesan cilok sapi khas Ciamis secara online. Buka ' . $settings->hours . '.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="theme-color" content="#183c2f">
@stack('head')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Franklin:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('cart', {
    items: (() => { try { return JSON.parse(localStorage.getItem('cart') || '{}') } catch (e) { return {} } })(),
    msg: '',
    save() { try { localStorage.setItem('cart', JSON.stringify(this.items)) } catch (e) {} },
    get count() { return Object.values(this.items).reduce((a, b) => a + b, 0) },
    add(id, n = 1) {
      let q = (this.items[id] || 0) + n;
      if (q <= 0) {
        delete this.items[id];
      } else {
        if (q > 99) q = 99; // FR-03.3 batas atas kuantitas
        this.items[id] = q;
      }
      this.items = { ...this.items };
      this.save();
    },
    clear() { this.items = {}; this.save() },
    prune() { const C = window.CATALOG || {}; const gone = []; for (const id of Object.keys(this.items)) { if (!C[id]) { gone.push('produk yang sudah tidak dijual'); delete this.items[id] } else if (!C[id].available) { gone.push(C[id].name); delete this.items[id] } } this.items = { ...this.items }; this.save(); return gone },
    lines() { const C = window.CATALOG || {}; return Object.entries(this.items).filter(([id]) => C[id]).map(([id, q]) => ({ id, q, ...C[id] })) },
    get subtotal() { return this.lines().reduce((s, l) => s + l.price * l.q, 0) },
    rp(n) { return 'Rp' + Number(n).toLocaleString('id-ID') },
    toast(m) { this.msg = m; setTimeout(() => this.msg = '', 2400) },
  })
})
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen overflow-x-hidden">
<header class="{{ $home ? 'absolute inset-x-0 top-0' : 'sticky top-0 bg-forest' }} z-20 text-white" x-data="{ mobileOpen: false }">
  <div class="mx-auto flex max-w-[1240px] items-center justify-between gap-3 px-4 py-4 sm:px-5 sm:py-5 lg:px-8 lg:py-7">
    <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Cilok Sapi SR">
      @include('partials.leaf')
      <div class="leading-none text-white">
        <span class="block font-display text-lg sm:text-[22px] font-bold tracking-[-0.03em]">Cilok Sapi SR</span>
        <span class="mt-1 block text-[9px] font-bold uppercase tracking-[0.28em] text-[#d8e49c]">Khas Ciamis</span>
      </div>
    </a>
    <nav class="hidden items-center gap-9 text-sm font-semibold text-white/85 md:flex" aria-label="Navigasi utama">
      <a class="nav-link" href="{{ route('home') }}">Beranda</a>
      <a class="nav-link" href="{{ $menuUrl }}">Produk</a>
      <a class="nav-link" href="{{ $aboutUrl }}">Tentang Kami</a>
    </nav>
    <div class="flex items-center gap-2">
      <a href="{{ route('cart') }}" class="rounded-full border border-white/30 px-3 py-2.5 text-xs sm:px-4 sm:py-3 font-extrabold text-white transition hover:bg-white/10">Keranjang <span class="dot" x-data x-show="$store.cart.count" x-text="$store.cart.count"></span></a>
      <a class="hidden rounded-full bg-mist px-5 py-3 text-xs font-extrabold text-forest transition hover:bg-white sm:inline-block md:px-6" href="{{ $menuUrl }}">Pesan Sekarang</a>
      {{-- Tombol Hamburger Menu Mobile (NFR-03) --}}
      <button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-white/30 text-white hover:bg-white/10 md:hidden" aria-label="Buka menu navigasi" x-on:click="mobileOpen = !mobileOpen">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
      </button>
    </div>
  </div>

  {{-- Drawer / Dropdown Navigasi Mobile --}}
  <div class="border-b border-white/10 bg-forest/95 px-5 py-4 backdrop-blur md:hidden" x-show="mobileOpen" x-transition style="display:none">
    <nav class="flex flex-col gap-3 text-sm font-semibold text-white" aria-label="Navigasi mobile">
      <a href="{{ route('home') }}" class="py-1 hover:text-leaf" x-on:click="mobileOpen = false">Beranda</a>
      <a href="{{ $menuUrl }}" class="py-1 hover:text-leaf" x-on:click="mobileOpen = false">Daftar Produk</a>
      <a href="{{ $aboutUrl }}" class="py-1 hover:text-leaf" x-on:click="mobileOpen = false">Tentang Cilok Sapi SR</a>
      <a href="{{ $wa }}" target="_blank" rel="noopener" class="py-1 text-leaf hover:underline">Chat WhatsApp Outlet</a>
      <a href="{{ route('login') }}" class="py-1 text-xs text-white/50 hover:text-white">Akses Admin / Kasir</a>
    </nav>
  </div>
</header>

@if (session('ok'))<div class="mx-auto max-w-[1000px] px-4 pt-4"><div class="ok" role="status">{{ session('ok') }}</div></div>@endif
<main class="@yield('main_class', 'mx-auto max-w-[1000px] p-4')">
  @yield('content')
</main>

<footer class="bg-forest px-5 py-9 text-white lg:px-8">
  <div class="mx-auto flex max-w-[1180px] flex-col items-center justify-between gap-5 sm:flex-row">
    <div class="flex items-center gap-3">@include('partials.leaf')<div class="font-display text-lg font-semibold">Cilok Sapi SR <span class="text-leaf">Ciamis</span></div></div>
    <p class="text-center text-xs text-white/60">{{ $settings->address }} · Buka {{ $settings->hours }}</p>
    <div class="flex gap-5 text-xs font-bold text-white/70">
      <a href="{{ $wa }}" target="_blank" rel="noopener" class="hover:text-white">WhatsApp</a>
      <a href="{{ route('login') }}" class="hover:text-white">Admin</a>
    </div>
  </div>
</footer>
<div class="toast" x-data x-show="$store.cart.msg" x-text="$store.cart.msg" role="status" style="display:none"></div>
</body>
</html>
