@extends('layouts.app')
@section('title', 'Keranjang')
@section('robots', 'noindex,nofollow')
@section('content')
{{-- Variabel: $settings, $products. Keranjang disimpan di browser (localStorage), server menghitung ulang saat checkout. --}}
<script>window.CATALOG = @json($products->mapWithKeys(fn($p) => [$p->id => ['name' => $p->name, 'price' => $p->price, 'emoji' => $p->emoji, 'available' => (bool) $p->is_available && (bool) $p->is_active]]));</script>
<h2>Keranjang</h2>
<div x-data="{ gone: [] }" x-init="gone = $store.cart.prune()">
  <div class="card mb-3 text-er" x-show="gone.length" style="display:none" role="alert">Dihapus dari keranjang karena habis atau tidak tersedia: <b x-text="gone.join(', ')"></b>.</div>
  <div class="card" x-show="!$store.cart.lines().length"><p>Keranjang masih kosong.</p><a class="btn" href="{{ route('home') }}">Pilih menu</a></div>
  <div class="grid gap-3" x-show="$store.cart.lines().length" style="display:none">
    <template x-for="l in $store.cart.lines()" :key="l.id">
      <div class="card flex flex-wrap items-center gap-2">
        <span class="text-5xl leading-none" style="font-size:30px" x-text="l.emoji" aria-hidden="true"></span>
        <div><b x-text="l.name"></b><div class="text-sm text-mu" x-text="$store.cart.rp(l.price)"></div><div class="text-[13px] text-er" x-show="!l.available">Produk habis. Hapus dari keranjang.</div></div>
        <div class="flex flex-wrap items-center gap-2 ml-auto">
          <button type="button" class="btn s sm" aria-label="Kurangi" x-on:click="$store.cart.add(l.id, -1)">−</button><b x-text="l.q"></b>
          <button type="button" class="btn s sm" aria-label="Tambah" x-on:click="$store.cart.add(l.id, 1)">+</button>
          <b style="min-width:90px;text-align:right" x-text="$store.cart.rp(l.price * l.q)"></b>
          <button type="button" class="btn d sm" x-on:click="$store.cart.add(l.id, -l.q)">Hapus</button>
        </div>
      </div>
    </template>
    <div class="card flex flex-wrap items-center gap-2"><b>Total</b><b class="text-lg font-extrabold ml-auto" x-text="$store.cart.rp($store.cart.subtotal)"></b></div>
    <a class="btn" href="{{ route('checkout') }}">Lanjut ke checkout</a>
  </div>
</div>
@endsection
