@extends('layouts.app')
@section('main_class', '')
@section('title', $settings->name . ' · Pesan Online')
@section('meta_description', 'Pesan ' . $settings->name . ' secara online: cilok sapi dari daging sapi pilihan. ' . $settings->address . '. Buka ' . $settings->hours . '.')
@push('head')
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Restaurant', 'name' => $settings->name, 'telephone' => '+' . $settings->wa, 'address' => $settings->address, 'servesCuisine' => 'Cilok', 'url' => url('/')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush
@section('content')
{{-- Variabel: $settings, $categories, $products (is_active saja). Tampilan mengikuti desain Figma "Website Design for Cilok Sapi". --}}
@php
  $heroImage = 'https://images.unsplash.com/photo-1774370793603-3c00fd215bb8?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&q=88&w=1400';
  $storyImage = 'https://images.unsplash.com/photo-1687426163461-1eeb49c83584?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&q=86&w=1100';
  $minPrice = $products->where('is_available', true)->min('price') ?? $products->min('price');
  $benefits = [
    ['01', 'Daging Sapi Pilihan', 'Dibuat dari daging sapi pilihan untuk rasa gurih yang lebih kaya.'],
    ['02', 'Higienis', 'Diolah dengan proses yang bersih dan higienis setiap hari.'],
    ['03', 'Resep Khas Ciamis', 'Racikan bumbu keluarga dengan cita rasa autentik Priangan Timur.'],
  ];
@endphp

{{-- HERO --}}
<section id="beranda" class="relative min-h-[760px] bg-forest pt-28 text-white lg:min-h-[820px]">
  <div class="hero-glow"></div>
  <div class="relative mx-auto grid max-w-[1240px] items-center gap-12 px-5 pb-24 pt-16 lg:grid-cols-[1.03fr_.97fr] lg:px-8 lg:pb-32 lg:pt-24">
    <div class="relative z-10">
      <div class="mb-7 inline-flex items-center gap-2.5 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.19em] text-leaf">
        <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
        @if($settings->halal)Halal No. {{ $settings->halal }}@else Cita rasa khas Ciamis @endif
      </div>
      <h1 class="mb-0 max-w-[650px] font-display text-[52px] font-semibold leading-[0.98] tracking-[-0.045em] sm:text-[66px] lg:text-[80px]">
        Lembutnya Cilok,
        <span class="block text-leaf">Gurihnya Sapi.</span>
      </h1>
      <p class="mt-7 max-w-[540px] text-base leading-7 text-white/70 sm:text-lg">
        Camilan khas Ciamis dari daging sapi pilihan, diracik dengan bumbu keluarga untuk rasa yang selalu bikin rindu.
      </p>
      <div class="mt-9 flex flex-wrap items-center gap-5">
        {{-- Pesan lewat website (bukan langsung ke WhatsApp) agar pesanan tersimpan dulu: P-01 / FR-05 --}}
        <a class="group inline-flex items-center gap-4 rounded-full bg-mist py-2.5 pl-6 pr-2.5 text-sm font-extrabold text-forest transition hover:bg-white" href="#produk">
          Pesan via WhatsApp
          <span class="grid h-9 w-9 place-items-center rounded-full bg-forest text-white"><span class="h-4 w-4 transition group-hover:translate-x-0.5">@include('partials.arrow')</span></span>
        </a>
        <a class="inline-flex items-center gap-2 border-b border-white/35 pb-1 text-sm font-bold text-white transition hover:border-white" href="#keunggulan">Lihat keunggulan kami</a>
      </div>
      <div class="mt-12 flex items-center gap-7 border-t border-white/10 pt-7 text-xs text-white/65 sm:gap-10">
        <div><strong class="block text-xl text-white">{{ $products->count() }} menu</strong>tersedia online</div>
        <span class="h-9 w-px bg-white/15"></span>
        <div><strong class="block text-xl text-white">Higienis</strong>daging sapi pilihan</div>
        <span class="hidden h-9 w-px bg-white/15 sm:block"></span>
        <div class="hidden sm:block"><strong class="block text-xl text-white">Online</strong>pesan lewat WhatsApp</div>
      </div>
    </div>

    <div class="relative mx-auto w-full max-w-[560px] lg:mx-0">
      <div class="hero-photo-frame">
        <img class="h-full w-full object-cover" src="{{ $heroImage }}" alt="Semangkuk cilok sapi dengan sambal dan pelengkap" fetchpriority="high">
        <div class="absolute inset-0 bg-gradient-to-t from-[#102c23]/35 via-transparent to-transparent"></div>
      </div>
      @if($minPrice)
      <div class="absolute -bottom-7 -left-3 rounded-2xl bg-gold px-5 py-4 text-forest shadow-xl sm:-left-10 sm:px-6">
        <span class="block text-[10px] font-extrabold uppercase tracking-[0.18em]">Mulai dari</span>
        <strong class="font-display text-3xl">{{ rupiah($minPrice) }}</strong>
      </div>
      @endif
      <div class="absolute -right-4 top-8 grid h-24 w-24 rotate-6 place-items-center rounded-full border border-white/20 bg-forest2/90 text-center text-[10px] font-bold uppercase leading-4 tracking-[0.12em] text-leaf backdrop-blur sm:-right-10">
        Hangat<br> &amp; Kenyal
      </div>
    </div>
  </div>
  <div class="absolute bottom-0 left-0 h-12 w-full rounded-t-[50%] bg-cream sm:h-16"></div>
</section>

{{-- KATALOG (FR-02) --}}
<section id="produk" class="px-5 py-20 sm:py-28 lg:px-8">
  <div class="mx-auto max-w-[1180px]" x-data="{ cat: 'Semua' }">
    <div class="mx-auto max-w-2xl text-center">
      <span class="eyebrow">Menu Kami</span>
      <h2 class="mt-4 font-display text-4xl font-semibold leading-tight tracking-[-0.035em] sm:text-5xl">Pilih menu favoritmu.</h2>
      <p class="mx-auto mt-5 max-w-lg text-sm leading-6 text-forest/60">Tambahkan ke keranjang, isi data pemesan, lalu kirim pesanan lewat WhatsApp.</p>
    </div>
    <div class="mt-10 flex justify-center gap-2 overflow-x-auto">
      <button type="button" class="chip" :class="cat === 'Semua' && 'on'" x-on:click="cat = 'Semua'">Semua</button>
      @foreach($categories as $c)<button type="button" class="chip" :class="cat === @js($c) && 'on'" x-on:click="cat = @js($c)">{{ $c }}</button>@endforeach
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($products as $p)
      <article class="menu-card" x-show="cat === 'Semua' || cat === @js($p->category)">
        @if($p->photo_url)<img src="{{ $p->photo_url }}" alt="{{ $p->name }}" loading="lazy" width="320" height="200" class="mb-4 h-40 w-full rounded-2xl object-cover">
        @else<div class="mb-4 text-5xl leading-none" aria-hidden="true">{{ $p->emoji }}</div>@endif
        <div class="flex items-start justify-between gap-3">
          <h3 class="mb-0 font-display text-xl font-semibold">{{ $p->name }}</h3>
          <span class="font-display text-lg font-semibold">{{ rupiah($p->price) }}</span>
        </div>
        @if($p->description)<p class="mt-2 text-sm leading-6 text-forest/60">{{ $p->description }}</p>@endif
        <div class="mt-2 flex flex-wrap items-center gap-2">
          <span class="text-sm {{ $p->is_available ? 'text-forest/60' : 'font-bold text-er' }}">{{ $p->is_available ? 'Tersedia' : 'Habis' }}</span>
          @if($settings->halal)<span class="chip h">Halal</span>@endif
        </div>
        <button type="button" class="btn mt-5 w-full" @disabled(!$p->is_available)
          x-on:click="$store.cart.add({{ $p->id }}); $store.cart.toast(@js($p->name) + ' masuk keranjang.')">Tambah ke keranjang</button>
      </article>
      @empty<p class="text-sm text-forest/60">Belum ada menu.</p>@endforelse
    </div>
  </div>
</section>

{{-- KEUNGGULAN (FR-01) --}}
<section id="keunggulan" class="px-5 pb-20 sm:pb-28 lg:px-8">
  <div class="mx-auto max-w-[1180px]">
    <div class="mx-auto max-w-2xl text-center">
      <span class="eyebrow">Kenapa Cilok Sapi SR?</span>
      <h2 class="mt-4 font-display text-4xl font-semibold leading-tight tracking-[-0.035em] sm:text-5xl">Sederhana bahannya,<br>istimewa rasanya.</h2>
      <p class="mx-auto mt-5 max-w-lg text-sm leading-6 text-forest/60">Kami percaya kelezatan terbaik selalu dimulai dari bahan yang baik dan proses yang jujur.</p>
    </div>
    <div class="mt-14 grid gap-4 md:grid-cols-3">
      @foreach($benefits as [$no, $title, $copy])
      <article class="benefit-card group">
        <div class="flex items-start justify-between">
          <span class="grid h-11 w-11 place-items-center rounded-full bg-leaf text-forest transition group-hover:bg-gold">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="h-6 w-6"><path d="m7 12 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <span class="font-display text-sm italic text-forest/35">{{ $no }}</span>
        </div>
        <h3 class="mb-0 mt-10 font-display text-2xl font-semibold">{{ $title }}</h3>
        <p class="mt-3 text-sm leading-6 text-forest/60">{{ $copy }}</p>
      </article>
      @endforeach
    </div>
  </div>
</section>

{{-- TENTANG / CERITA USAHA + ALAMAT, JAM BUKA, KONTAK (FR-01) --}}
<section id="tentang" class="bg-sand px-5 py-20 sm:py-28 lg:px-8">
  <div class="mx-auto grid max-w-[1180px] items-center gap-12 lg:grid-cols-2 lg:gap-20">
    <div class="relative">
      <div class="overflow-hidden rounded-[2rem]">
        <img class="aspect-[4/3] w-full object-cover transition duration-700 hover:scale-[1.03]" src="{{ $storyImage }}" alt="Sajian cilok sapi khas dengan saus pendamping" loading="lazy">
      </div>
      <div class="absolute -bottom-6 right-5 rounded-2xl bg-forest p-5 text-white shadow-xl sm:right-10">
        <span class="block text-[10px] font-extrabold uppercase tracking-[0.18em] text-white/60">Jam buka</span>
        <span class="block font-display text-xl text-leaf">{{ $settings->hours }}</span>
      </div>
    </div>
    <div>
      <span class="eyebrow">Cerita dari Ciamis</span>
      <h2 class="mt-4 font-display text-4xl font-semibold leading-[1.08] tracking-[-0.035em] sm:text-5xl">Dari resep keluarga,<br>untuk semua.</h2>
      <p class="mt-6 max-w-xl text-[15px] leading-7 text-forest/70">Bermula dari dapur kecil di Ciamis, Cilok Sapi SR hadir membawa camilan sederhana dengan standar rasa yang istimewa. Setiap butir dibuat teliti agar teksturnya kenyal, lembut, dan gurih sampai gigitan terakhir.</p>
      <p class="mt-4 max-w-xl text-[15px] leading-7 text-forest/70">Nikmati dengan saus kacang khas atau sambal pedas racikan kami. Cocok untuk teman santai, acara keluarga, maupun ide usaha.</p>
      <dl class="mt-6 grid max-w-xl gap-1 text-sm text-forest/70">
        <div><dt class="inline font-bold text-forest">Alamat: </dt><dd class="inline">{{ $settings->address }}</dd></div>
        <div><dt class="inline font-bold text-forest">Jam buka: </dt><dd class="inline">{{ $settings->hours }}</dd></div>
        <div><dt class="inline font-bold text-forest">WhatsApp: </dt><dd class="inline">+{{ $settings->wa }}</dd></div>
        @if($settings->halal)<div><dt class="inline font-bold text-forest">Sertifikat halal: </dt><dd class="inline">{{ $settings->halal }}</dd></div>@endif
      </dl>
      <a class="mt-8 inline-flex items-center gap-3 text-sm font-extrabold" href="https://wa.me/{{ $settings->wa }}?text={{ rawurlencode($settings->template) }}" target="_blank" rel="noopener">
        Kenal lebih dekat <span class="h-5 w-5">@include('partials.arrow')</span>
      </a>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="px-5 py-20 sm:py-24 lg:px-8">
  <div class="relative mx-auto max-w-[1180px] overflow-hidden rounded-[2rem] bg-gold px-6 py-14 text-center text-forest sm:px-12 sm:py-16">
    <div class="cta-ring cta-ring-left"></div><div class="cta-ring cta-ring-right"></div>
    <p class="relative text-xs font-extrabold uppercase tracking-[0.2em]">Sudah lapar?</p>
    <h2 class="relative mx-auto mt-4 max-w-2xl font-display text-4xl font-semibold leading-tight tracking-[-0.04em] sm:text-5xl">Yuk, rasakan cilok sapi<br class="hidden sm:block"> khas Ciamis hari ini.</h2>
    <a class="relative mt-8 inline-flex items-center gap-4 rounded-full bg-forest py-2.5 pl-6 pr-2.5 text-sm font-extrabold text-white transition hover:bg-forest2" href="#produk">
      Pesan Sekarang
      <span class="grid h-9 w-9 place-items-center rounded-full bg-leaf text-forest"><span class="h-4 w-4">@include('partials.arrow')</span></span>
    </a>
  </div>
</section>
@endsection
