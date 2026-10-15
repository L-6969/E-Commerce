@extends('layouts.admin')
@section('title', 'Laporan omzet')
@push('head')<script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>@endpush
@section('content')
{{-- Hanya Pemilik (backend: 403 untuk Staf). Variabel: $mode (day|month), $date, $summary{count,total,online,offline}, $rows[name,qty,total], $max. Hanya pesanan "Selesai". --}}
@if(($authUser ?? auth()->user())->role !== 'owner')
  <div class="card"><b>Akses ditolak (403).</b> Laporan omzet hanya untuk Pemilik.</div>
@else
<form method="GET" class="flex flex-wrap items-center gap-2"><h2 style="margin:0">Laporan omzet</h2>
  <select name="mode" class="chip" aria-label="Periode" onchange="this.form.submit()"><option value="day" @selected($mode === 'day')>Harian</option><option value="month" @selected($mode === 'month')>Bulanan</option></select>
  <input type="date" name="date" class="chip" value="{{ $date }}" onchange="this.form.submit()"></form>
<div class="grid gap-3 grid-cols-[repeat(auto-fill,minmax(210px,1fr))] mt-3">
  <div class="card"><div class="text-sm text-mu">Omzet (pesanan Selesai)</div><div class="text-lg font-extrabold">{{ rupiah($summary->total) }}</div><div class="text-sm text-mu">{{ $summary->count }} pesanan</div></div>
  <div class="card"><div class="text-sm text-mu">Online</div><div class="text-lg font-extrabold">{{ rupiah($summary->online) }}</div></div>
  <div class="card"><div class="text-sm text-mu">Offline</div><div class="text-lg font-extrabold">{{ rupiah($summary->offline) }}</div></div>
</div>
<div class="card mt-3"><h3>Per produk</h3>
  @if($rows->isEmpty())<p class="text-sm text-mu">Belum ada penjualan selesai pada periode ini.</p>
  @else
    <div class="relative h-[260px]"><canvas id="chartProduk" role="img" aria-label="Grafik omzet per produk"></canvas></div>
    <div class="mt-3 overflow-x-auto"><table><thead><tr><th>Produk</th><th>Qty</th><th>Omzet</th></tr></thead><tbody>
      @foreach($rows as $r)<tr><td>{{ $r->name }}</td><td>{{ $r->qty }}</td><td>{{ rupiah($r->total) }}</td></tr>@endforeach
    </tbody></table></div>
    <script>
    window.addEventListener('load', () => {
      const el = document.getElementById('chartProduk'); if (!el || !window.Chart) return;
      new Chart(el, { type: 'bar', data: { labels: @json($rows->pluck('name')->values()), datasets: [{ label: 'Omzet (Rp)', data: @json($rows->pluck('total')->values()), backgroundColor: '#183c2f', borderRadius: 6 }] },
        options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { callback: v => 'Rp' + Number(v).toLocaleString('id-ID') } } } } });
    });
    </script>
  @endif
  <button type="button" class="btn s sm" x-data x-on:click="navigator.clipboard.writeText(@js("Produk,Qty,Total\n" . $rows->map(fn($r) => "\"{$r->name}\",{$r->qty},{$r->total}")->implode("\n")))">Salin sebagai CSV</button>
</div>
@endif
@endsection
