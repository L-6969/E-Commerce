@extends('layouts.admin')
@section('title', 'Input offline')
@section('content')
{{-- Variabel: $products (aktif). POST route('admin.offline.store'): items[produk_id]=qty, payment_method, customer_name (opsional). Server simpan channel=offline, status=Selesai. --}}
<h2>Input penjualan outlet</h2>
<form method="POST" action="{{ route('admin.offline.store') }}"
  x-data="{ q: {}, P: @js($products->mapWithKeys(fn($p) => [$p->id => ['n' => $p->name, 'p' => $p->price]])),
    add(id, n) { const v = (this.q[id] || 0) + n; if (v <= 0) delete this.q[id]; else this.q[id] = v; this.q = { ...this.q } },
    get total() { return Object.entries(this.q).reduce((s, [id, v]) => s + this.P[id].p * v, 0) },
    rp(n) { return 'Rp' + Number(n).toLocaleString('id-ID') } }">
  @csrf
  <div class="grid gap-3 grid-cols-[repeat(auto-fill,minmax(150px,1fr))]">
    @foreach($products as $p)
    <button type="button" class="big" :class="q[{{ $p->id }}] && 'on'" @disabled(!$p->is_available) x-on:click="add({{ $p->id }}, 1)">
      <span class="text-5xl leading-none" style="font-size:28px">{{ $p->emoji }}</span><div><b>{{ $p->name }}</b></div>
      <div class="text-sm text-mu">{{ $p->is_available ? rupiah($p->price) : 'Habis' }}</div><b x-show="q[{{ $p->id }}]" x-text="'× ' + q[{{ $p->id }}]"></b>
    </button>
    @endforeach
  </div>
  <template x-for="(v, id) in q" :key="id"><input type="hidden" :name="'items[' + id + ']'" :value="v"></template>
  <div class="card mt-3">
    <template x-for="(v, id) in q" :key="id"><div class="flex flex-wrap items-center gap-2"><span x-text="P[id].n"></span>
      <button type="button" class="btn s sm" x-on:click="add(id, -1)">−</button><b x-text="v"></b><button type="button" class="btn s sm" x-on:click="add(id, 1)">+</button>
      <span class="ml-auto" x-text="rp(P[id].p * v)"></span></div></template>
    <p class="text-sm text-mu" x-show="!Object.keys(q).length">Ketuk menu untuk menambah item.</p>
    <div class="flex flex-wrap items-center gap-2 mt-3">
      <select name="payment_method" class="chip" aria-label="Metode bayar"><option>Tunai</option><option>Transfer</option><option>QRIS</option></select>
      <input name="customer_name" class="chip" placeholder="Nama pembeli (opsional)">
      <b class="ml-auto" x-text="'Total ' + rp(total)"></b>
    </div>
    <button class="btn mt-3" style="width:100%;min-height:56px" :disabled="!Object.keys(q).length">Simpan penjualan</button>
  </div>
</form>
@endsection
