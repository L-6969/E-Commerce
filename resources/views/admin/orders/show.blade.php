@extends('layouts.admin')
@section('title', $order->code)
@section('content')
@php $isOwner = ($authUser ?? auth()->user())->role === 'owner'; $next = next_status($order->status); $canCancel = in_array($order->status, ['Baru', 'Dikonfirmasi', 'Diproses'], true); @endphp
{{-- Variabel: $order (+items, +statusLogs[from_status,to_status,user,reason,created_at]) --}}
<a href="{{ route('admin.orders') }}" class="text-sm text-mu">← Semua pesanan</a>
<div class="card mt-3">
  <div class="flex flex-wrap items-center gap-2"><h2 style="margin:0">{{ $order->code }}</h2><span class="chip">{{ $order->status === 'Batal' ? 'Batal' : status_label($order->status, $order->fulfillment) }}</span></div>
  <p class="text-sm text-mu">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->channel }} · {{ $order->fulfillment === 'antar' ? 'Antar: ' . $order->delivery_address : 'Ambil ' . ($order->pickup_time ? 'pukul ' . $order->pickup_time : 'secepatnya') }} · Bayar: {{ $order->payment_method }} ({{ $order->payment_status === 'paid' ? 'lunas' : 'belum bayar' }}) · Telp: {{ $order->customer_phone ?: '-' }}</p>
  <ul>@foreach($order->items as $i)<li>{{ $i->product_name_snapshot }} × {{ $i->qty }} = {{ rupiah($i->line_total) }}</li>@endforeach</ul>
  @if($order->note)<p>Catatan: {{ $order->note }}</p>@endif
  <b>Total {{ rupiah($order->total) }}</b>

  <div class="flex flex-wrap items-center gap-2 mt-3">
    @if($next)<form method="POST" action="{{ route('admin.orders.status', $order->id) }}">@csrf<input type="hidden" name="status" value="{{ $next }}"><button class="btn">Ubah ke {{ status_label($next, $order->fulfillment) }}</button></form>@endif
    @if(!in_array($order->status, ['Selesai', 'Batal'], true))
      <form method="POST" action="{{ route('admin.orders.payment', $order->id) }}">@csrf<input type="hidden" name="payment_status" value="{{ $order->payment_status === 'paid' ? 'unpaid' : 'paid' }}"><button class="btn s">{{ $order->payment_status === 'paid' ? 'Tandai belum dibayar' : 'Tandai sudah dibayar' }}</button></form>
    @endif
    @if($order->customer_phone)<a class="btn s sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer_phone) }}">Hubungi pembeli</a>@endif
    @if($isOwner && $order->channel === 'online')<form method="POST" action="{{ route('admin.orders.anonymize', $order->id) }}" onsubmit="return confirm('Hapus data pribadi pesanan ini? Tindakan tidak bisa dibatalkan.')">@csrf<button class="btn s sm">Hapus data pribadi</button></form>@endif
  </div>

  @if($canCancel || ($isOwner && $order->status === 'Selesai'))
  <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="mt-3" x-data="{ reason: '' }">
    @csrf
    <div class="f"><label for="reason">Alasan (wajib untuk batal atau koreksi)</label><input id="reason" name="reason" x-model="reason"></div>
    <div class="flex flex-wrap items-center gap-2">
      @if($canCancel)<button class="btn d sm" name="status" value="Batal" :disabled="!reason.trim()">Batalkan pesanan</button>@endif
      @if($isOwner && $order->status === 'Selesai')<button class="btn s sm" name="status" value="Diproses" :disabled="!reason.trim()">Koreksi: buka kembali</button>@endif
    </div>
  </form>
  @elseif($order->status === 'Selesai')<p class="text-sm text-mu">Koreksi pesanan selesai hanya oleh Pemilik.</p>@endif

  <h3 class="mt-3">Riwayat status</h3>
  @forelse($order->statusLogs as $l)<div class="text-sm text-mu">{{ $l->created_at->format('d/m/Y H:i') }} · {{ $l->from_status }} → {{ $l->to_status }} oleh {{ $l->user }}{{ $l->reason ? ' · ' . $l->reason : '' }}</div>
  @empty<p class="text-sm text-mu">Belum ada perubahan.</p>@endforelse
</div>
@endsection
