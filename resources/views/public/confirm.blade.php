@extends('layouts.app')
@section('title', 'Pesanan ' . $order->code)
@section('robots', 'noindex,nofollow')
@section('content')
{{-- Variabel: $settings, $order (+items), $message (teks WA), $waUrl. Halaman diakses lewat token acak (FR-07.1). --}}
<div class="card" x-data="{
  init() {
    $store.cart.clear();
    try {
      window.open(@js($waUrl), '_blank');
    } catch(e) {}
  }
}">
  <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-forest/10 px-3 py-1 text-xs font-bold text-forest">
    <span class="h-2 w-2 rounded-full bg-leaf"></span>
    Pesanan Tersimpan di Sistem (FR-05)
  </div>
  <h2>Pesanan Berhasil Disimpan!</h2>
  <p class="text-base leading-relaxed">
    Kode pesanan Anda: <b class="rounded bg-forest/10 px-2 py-0.5 font-mono text-lg text-forest">{{ $order->code }}</b>.
  </p>
  <div class="my-4 rounded-2xl border border-gold/40 bg-gold/15 p-4 text-sm text-forest">
    <b>Pemberitahuan:</b> Pesanan sudah tersimpan di basis data outlet kami. Tekan tombol <b>Kirim ke WhatsApp</b> di bawah agar admin dapat mengonfirmasi pesanan Anda (FR-07.3).
  </div>

  <h3 class="mt-4 mb-2">Rincian Menu</h3>
  <ul class="mb-3 space-y-1 text-sm">
    @foreach($order->items as $i)
      <li><b>{{ $i->product_name_snapshot }}</b> × {{ $i->qty }} = {{ rupiah($i->line_total) }}</li>
    @endforeach
  </ul>

  <div class="my-3 border-t border-b border-bd/60 py-3 text-sm">
    <div class="flex justify-between font-bold text-base">
      <span>Total Pesanan:</span>
      <span class="text-forest">{{ rupiah($order->total) }}</span>
    </div>
    <div class="mt-1 text-xs text-mu">
      Layanan: <b>{{ $order->fulfillment === 'antar' ? 'Diantar ke alamat pemesan' : 'Ambil langsung di outlet' . ($order->pickup_time ? ' pukul ' . $order->pickup_time : ' (secepatnya)') }}</b>
      @if($order->delivery_address) · Alamat: {{ $order->delivery_address }} @endif
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-3 my-4">
    <a class="btn !bg-[#25D366] !text-white hover:!bg-[#1ea952] flex items-center gap-2" href="{{ $waUrl }}" target="_blank" rel="noopener">
      <span>💬 Buka WhatsApp Sekarang</span>
    </a>
    <button type="button" class="btn s" x-on:click="navigator.clipboard.writeText(@js($message)).then(() => $store.cart.toast('Teks pesanan tersalin!'), () => $store.cart.toast('Gagal menyalin otomatis. Silakan salin manual.'))">
      📋 Salin Pesan
    </button>
  </div>
  <p class="text-xs text-mu">Nomor WhatsApp resmi outlet: <b>+{{ $settings->wa }}</b> (Buka {{ $settings->hours }}).</p>

  <details class="my-3 text-xs text-mu bg-white/50 rounded-xl p-3 border border-forest/10">
    <summary class="cursor-pointer font-semibold">Tampilkan teks format WhatsApp</summary>
    <pre class="mt-2 text-xs font-mono whitespace-pre-wrap">{{ $message }}</pre>
  </details>

  <div class="mt-5">
    <a class="btn s sm" href="{{ route('home') }}">← Kembali ke Beranda</a>
  </div>
</div>
@endsection
