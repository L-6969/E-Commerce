@extends('layouts.app')
@section('title', 'Checkout')
@section('robots', 'noindex,nofollow')
@section('content')
{{--
 Variabel: $settings, $products
 POST route('checkout.store') mengirim: name, phone, fulfillment (ambil|antar), delivery_address, payment_method, note,
 pickup_time (HH:MM, opsional, hanya jika ambil; wajib dalam jam buka), consent (1), items (JSON {"produk_id": qty}), idempotency_token. Server WAJIB validasi + hitung ulang total, simpan status "Baru", lalu redirect ke route('order.show', $token).
--}}
<script>window.CATALOG = @json($products->mapWithKeys(fn($p) => [$p->id => ['name' => $p->name, 'price' => $p->price, 'emoji' => $p->emoji, 'available' => (bool) $p->is_available && (bool) $p->is_active]]));</script>
<h2>Checkout</h2>
<form method="POST" action="{{ route('checkout.store') }}" class="card" x-data="{ ful: @js(old('fulfillment', 'ambil')), busy: false, gone: [], pk: @js(old('pickup_time') ? 'jam' : 'asap') }" x-init="gone = $store.cart.prune()"
      x-on:submit="if (!$store.cart.lines().length) { $event.preventDefault(); return } busy = true">
  @csrf
  <input type="hidden" name="idempotency_token" value="{{ \Illuminate\Support\Str::uuid() }}">
  <input type="hidden" name="items" :value="JSON.stringify($store.cart.items)">

  <div class="card mb-3 text-er" x-show="gone.length" style="display:none" role="alert">Dihapus dari keranjang karena habis atau tidak tersedia: <b x-text="gone.join(', ')"></b>.</div>
  <div class="card text-sm text-mu" style="margin-bottom:12px">
    <template x-for="l in $store.cart.lines()" :key="l.id"><div class="flex flex-wrap items-center gap-2"><span x-text="l.name + ' × ' + l.q"></span><span class="ml-auto" x-text="$store.cart.rp(l.price * l.q)"></span></div></template>
    <div class="flex flex-wrap items-center gap-2"><b>Total</b><b class="ml-auto" x-text="$store.cart.rp($store.cart.subtotal)"></b></div>
    <div class="text-[13px] text-er" x-show="!$store.cart.lines().length">Keranjang kosong. <a href="{{ route('home') }}">Pilih menu</a>.</div>
  </div>

  <div class="f"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required minlength="2"><span class="text-[13px] text-er">{{ $errors->first('name') }}</span></div>
  <div class="f"><label for="phone">Nomor HP</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" placeholder="081234567890" required><span class="text-[13px] text-er">{{ $errors->first('phone') }}</span></div>
  <div class="f"><label for="ful">Ambil atau antar</label>
    <select id="ful" name="fulfillment" x-model="ful"><option value="ambil">Ambil di outlet</option><option value="antar">Diantar (ongkir dibicarakan lewat WhatsApp)</option></select></div>
  <div class="f" x-show="ful === 'ambil'">
    <label for="pk">Waktu ambil</label>
    <select id="pk" x-model="pk"><option value="asap">Secepatnya</option><option value="jam">Pilih jam</option></select>
    <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" x-show="pk === 'jam'" x-bind:disabled="ful !== 'ambil' || pk !== 'jam'" aria-label="Jam ambil">
    <span class="text-sm text-mu">Jam ambil harus dalam jam buka: {{ $settings->hours }}.</span>
    <span class="text-[13px] text-er">{{ $errors->first('pickup_time') }}</span>
  </div>
  <div class="f" x-show="ful === 'antar'"><label for="addr">Alamat antar</label><textarea id="addr" name="delivery_address" rows="2" :required="ful === 'antar'">{{ old('delivery_address') }}</textarea><span class="text-[13px] text-er">{{ $errors->first('delivery_address') }}</span></div>
  <div class="f"><label for="pay">Rencana pembayaran</label>
    <select id="pay" name="payment_method">@foreach(['Tunai', 'Transfer', 'QRIS'] as $m)<option @selected(old('payment_method') === $m)>{{ $m }}</option>@endforeach</select>
    <span class="text-sm text-mu">Pembayaran dilakukan di luar website.</span></div>
  <div class="f"><label for="note">Catatan</label><textarea id="note" name="note" rows="2" placeholder="Contoh: kuah dipisah">{{ old('note') }}</textarea></div>

  <p class="text-sm text-mu">Nama, nomor HP, dan alamat dipakai hanya untuk memproses pesanan ini. Anda bisa meminta penghapusan data lewat WhatsApp outlet. Pesan dikirim lewat WhatsApp, layanan pihak ketiga.</p>
  <label class="flex flex-wrap items-center gap-2"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))> Saya setuju data saya dipakai untuk memproses pesanan.</label>
  <div class="text-[13px] text-er">{{ $errors->first('consent') }}{{ $errors->first('items') }}</div>
  <div class="flex flex-wrap items-center gap-2 mt-3"><button class="btn ml-auto" x-bind:disabled="busy || !$store.cart.lines().length">Pesan lewat WhatsApp</button></div>
</form>
@endsection
