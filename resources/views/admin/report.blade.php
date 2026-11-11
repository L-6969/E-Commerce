@extends('layouts.admin')
@section('title', 'Laporan omzet')
@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
@media print {
  header, nav, form, .no-print { display: none !important; }
  body { background: #fff !important; }
  .card { border: 1px solid #ccc !important; box-shadow: none !important; background: #fff !important; }
}
</style>
@endpush
@section('content')
{{-- Hanya Pemilik (backend: 403 untuk Staf). Variabel: $mode (day|month), $date, $summary{count,total,online,offline}, $rows[name,qty,total], $max. Hanya pesanan "Selesai". --}}
@if(!($isOwner ?? (($authUser ?? auth()->user())->role === 'owner')))
  <div class="card"><b>Akses ditolak (403).</b> Laporan omzet hanya untuk peran Pemilik.</div>
@else
<div class="flex flex-wrap items-center justify-between gap-3 no-print">
  <form method="GET" class="flex flex-wrap items-center gap-2">
    <h2 style="margin:0">Laporan omzet</h2>
    <select name="mode" class="chip" aria-label="Periode" onchange="this.form.submit()">
      <option value="day" @selected($mode === 'day')>Harian</option>
      <option value="month" @selected($mode === 'month')>Bulanan</option>
    </select>
    <input type="date" name="date" class="chip" value="{{ $date }}" onchange="this.form.submit()">
  </form>
  <div class="flex flex-wrap items-center gap-2">
    <button type="button" class="btn s sm" onclick="window.print()">🖨️ Cetak / PDF</button>
  </div>
</div>

<div class="grid gap-3 grid-cols-[repeat(auto-fill,minmax(210px,1fr))] mt-3">
  <div class="card"><div class="text-sm text-mu">Omzet (pesanan Selesai)</div><div class="text-lg font-extrabold">{{ rupiah($summary->total) }}</div><div class="text-sm text-mu">{{ $summary->count }} pesanan</div></div>
  <div class="card"><div class="text-sm text-mu">Online</div><div class="text-lg font-extrabold">{{ rupiah($summary->online) }}</div></div>
  <div class="card"><div class="text-sm text-mu">Offline</div><div class="text-lg font-extrabold">{{ rupiah($summary->offline) }}</div></div>
</div>

<div class="card mt-3">
  <h3>Penjualan per produk</h3>
  @if($rows->isEmpty())
    <p class="text-sm text-mu">Belum ada penjualan selesai pada periode ini.</p>
  @else
    <div class="relative h-[260px] no-print"><canvas id="chartProduk" role="img" aria-label="Grafik omzet per produk"></canvas></div>
    <div class="mt-3 overflow-x-auto">
      <table>
        <thead><tr><th>Produk</th><th>Kuantitas Terjual</th><th>Total Omzet</th></tr></thead>
        <tbody>
          @foreach($rows as $r)
            <tr><td><b>{{ $r->name }}</b></td><td>{{ $r->qty }} porsi</td><td><b>{{ rupiah($r->total) }}</b></td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <script>
    window.addEventListener('load', () => {
      const el = document.getElementById('chartProduk'); if (!el || !window.Chart) return;
      new Chart(el, {
        type: 'bar',
        data: {
          labels: @json($rows->pluck('name')->values()),
          datasets: [{ label: 'Omzet (Rp)', data: @json($rows->pluck('total')->values()), backgroundColor: '#183c2f', borderRadius: 6 }]
        },
        options: {
          indexAxis: 'y',
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { x: { ticks: { callback: v => 'Rp' + Number(v).toLocaleString('id-ID') } } }
        }
      });
    });
    </script>
  @endif

  {{-- Ekspor Laporan (FR-15.1) --}}
  <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-bd/40 no-print" x-data="{
    downloadCsv() {
      const csv = @js("Produk,Kuantitas,Omzet\n" . $rows->map(fn($r) => "\"{$r->name}\",{$r->qty},{$r->total}")->implode("\n"));
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'laporan-omzet-' + @js($mode) + '-' + @js($date) + '.csv';
      a.click();
      URL.revokeObjectURL(url);
    }
  }">
    <span class="text-xs text-mu font-semibold mr-1">Ekspor (FR-15):</span>
    <button type="button" class="btn s sm" x-on:click="downloadCsv()">📥 Unduh Berkas CSV</button>
    <button type="button" class="btn s sm" x-on:click="navigator.clipboard.writeText(@js("Produk,Qty,Total\n" . $rows->map(fn($r) => "\"{$r->name}\",{$r->qty},{$r->total}")->implode("\n"))).then(() => alert('Data CSV disalin ke clipboard!'))">📋 Salin Teks CSV</button>
    <button type="button" class="btn s sm" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
  </div>
</div>
@endif
@endsection
