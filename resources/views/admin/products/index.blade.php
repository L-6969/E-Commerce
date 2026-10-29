@extends('layouts.admin')
@section('title', 'Produk')
@section('content')
{{-- Variabel: $products (semua, termasuk nonaktif). Produk tidak dihapus permanen, hanya dinonaktifkan. --}}
@php $isOwner = ($authUser ?? auth()->user())->role === 'owner'; @endphp
<div class="flex flex-wrap items-center gap-2"><h2>Produk</h2>@if($isOwner)<a class="btn ml-auto" href="{{ route('admin.products.create') }}">Tambah produk</a>@endif</div>
<div class="overflow-x-auto card"><table>
  <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @foreach($products as $p)
    <tr><td>{{ $p->emoji }} {{ $p->name }}</td><td>{{ $p->category }}</td><td>{{ rupiah($p->price) }}</td><td>{{ $p->stock }}</td>
      <td>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }} · {{ $p->is_available ? 'Tersedia' : 'Habis' }}</td>
      <td><div class="flex flex-wrap items-center gap-2">
        @if($isOwner)<a class="btn s sm" href="{{ route('admin.products.edit', $p->id) }}">Ubah</a>@endif
        <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}">@csrf @method('PATCH')<input type="hidden" name="field" value="is_available"><button class="btn s sm">{{ $p->is_available ? 'Tandai habis' : 'Tandai tersedia' }}</button></form>
        @if($isOwner)<form method="POST" action="{{ route('admin.products.toggle', $p->id) }}">@csrf @method('PATCH')<input type="hidden" name="field" value="is_active"><button class="btn s sm">{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>@endif
      </div></td></tr>
  @endforeach
  </tbody></table></div>
@endsection
