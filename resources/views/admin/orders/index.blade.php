@extends('layouts.admin')
@section('title', 'Pesanan')
@push('head')<meta http-equiv="refresh" content="30">@endpush {{-- FR-14: penyegaran otomatis tiap 30 detik --}}
@section('content')
{{-- Variabel: $orders (kolom: id, code, channel, customer_name, customer_phone, fulfillment, status, total), $filters (status,date,channel) --}}
@if(($newOrdersCount ?? 0) > 0)<div class="card" style="border-color:var(--ac);margin-bottom:12px"><b>{{ $newOrdersCount }} pesanan baru menunggu konfirmasi.</b></div>@endif
<form method="GET" class="flex flex-wrap items-center gap-2" style="margin-bottom:10px">
  <select name="status" class="chip" aria-label="Filter status" onchange="this.form.submit()"><option value="">Semua status</option>@foreach([...order_statuses(), 'Batal'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
  <input type="date" name="date" class="chip" value="{{ $filters['date'] ?? '' }}" aria-label="Filter tanggal" onchange="this.form.submit()">
  <select name="channel" class="chip" aria-label="Filter kanal" onchange="this.form.submit()"><option value="">Semua kanal</option><option value="online" @selected(($filters['channel'] ?? '') === 'online')>Online</option><option value="offline" @selected(($filters['channel'] ?? '') === 'offline')>Offline</option></select>
  <a class="chip" href="{{ route('admin.orders') }}">Reset</a>
</form>
<div class="overflow-x-auto card"><table>
  <thead><tr><th>Kode</th><th>Kanal</th><th>Pembeli</th><th>Total</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse($orders as $o)
    <tr @if($o->status === 'Baru') style="background:#fff8e6" @endif>
      <td>{{ $o->code }}<div class="text-[11px] text-mu">{{ $o->created_at->format('d/m H:i') }}</div></td><td>{{ $o->channel }}</td><td>{{ $o->customer_name ?: '-' }}<div class="text-sm text-mu">{{ mask_phone($o->customer_phone) }}</div></td>
      <td>{{ rupiah($o->total) }}</td>
      <td><span class="chip">{{ $o->status === 'Batal' ? 'Batal' : status_label($o->status, $o->fulfillment) }}</span>
        @if($o->status === 'Baru')<div class="text-[11px] text-er mt-1">⏱ {{ $o->created_at->diffForHumans(null, true) }} yang lalu</div>@endif
      </td>
      <td><a class="btn s sm" href="{{ route('admin.orders.show', $o->id) }}">Detail</a></td></tr>
  @empty<tr><td colspan="6" class="text-sm text-mu">Tidak ada pesanan untuk filter ini.</td></tr>@endforelse
  </tbody></table></div>
{{-- Pagination backend: {{ $orders->links() }} jika memakai paginator --}}
@endsection
