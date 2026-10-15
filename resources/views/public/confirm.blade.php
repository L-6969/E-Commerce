@extends('layouts.app')
@section('title', 'Pesanan ' . $order->code)
@section('robots', 'noindex,nofollow')
@section('content')
{{-- Variabel: $settings, $order (+items), $message (teks WA), $waUrl. Halaman diakses lewat token acak, bukan nomor urut. --}}
<div class="card" x-data x-init="$store.cart.clear(); window.open(@js($waUrl), '_blank')">
  <h2>Pesanan tersimpan</h2>
  <p>Kode pesanan: <b>{{ $order->code }}</b>. Pesanan Anda sudah tercatat. Tekan kirim di WhatsApp agar outlet segera memproses.</p>
  <ul>@foreach($order->items as $i)<li>{{ $i->product_name_snapshot }} × {{ $i->qty }} = {{ rupiah($i->line_total) }}</li>@endforeach</ul>
  <p><b>Total {{ rupiah($order->total) }}</b> · {{ $order->fulfillment === 'antar' ? 'Diantar' : 'Ambil di outlet' . ($order->pickup_time ? ' pukul ' . $order->pickup_time : ' (secepatnya)') }}</p>
  <div class="flex flex-wrap items-center gap-2">
    <a class="btn" href="{{ $waUrl }}" target="_blank" rel="noopener">Buka WhatsApp lagi</a>
    <button type="button" class="btn s" x-on:click="navigator.clipboard.writeText(@js($message)).then(() => $store.cart.toast('Tersalin.'), () => $store.cart.toast('Gagal menyalin. Salin manual.'))">Salin pesan</button>
    <span class="text-sm text-mu">Nomor WhatsApp outlet: <b>+{{ $settings->wa }}</b></span>
  </div>
  <pre class="text-sm text-mu" style="white-space:pre-wrap">{{ $message }}</pre>
  <a class="btn s" href="{{ route('home') }}">Kembali ke menu</a>
</div>
@endsection
