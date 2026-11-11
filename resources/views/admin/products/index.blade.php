@extends('layouts.admin')
@section('title', 'Produk')
@section('content')
@php $isOwner = $isOwner ?? (($authUser ?? auth()->user())->role === 'owner'); @endphp
{{-- Variabel: $products (semua, termasuk nonaktif). Produk tidak dihapus permanen, hanya dinonaktifkan. --}}
<div class="flex flex-wrap items-center gap-2 mb-3">
  <div>
    <h2 class="mb-0">Produk</h2>
    <p class="text-xs text-mu mt-0.5">{{ $isOwner ? 'Pemilik memiliki akses penuh kelola produk & harga.' : 'Staf berwenang memperbarui ketersediaan dan stok produk (FR-09.3).' }}</p>
  </div>
  @if($isOwner)
    <a class="btn ml-auto" href="{{ route('admin.products.create') }}">+ Tambah produk</a>
  @endif
</div>

<div class="overflow-x-auto card"><table>
  <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  @foreach($products as $p)
    <tr>
      <td><span class="mr-1 text-xl" aria-hidden="true">{{ $p->emoji }}</span> <b>{{ $p->name }}</b></td>
      <td><span class="chip">{{ $p->category }}</span></td>
      <td><b>{{ rupiah($p->price) }}</b></td>
      <td>
        <form method="POST" action="{{ route('admin.products.stock', $p->id) }}" class="inline-flex items-center gap-1">
          @csrf
          <button type="submit" name="delta" value="-1" class="btn s sm !min-h-7 !px-2 !py-0.5 text-xs" aria-label="Kurangi stok">−</button>
          <span class="inline-block min-w-[28px] text-center font-bold">{{ $p->stock }}</span>
          <button type="submit" name="delta" value="1" class="btn s sm !min-h-7 !px-2 !py-0.5 text-xs" aria-label="Tambah stok">+</button>
        </form>
      </td>
      <td>
        <div class="flex flex-col gap-1 text-xs">
          <span class="{{ $p->is_active ? 'text-forest font-semibold' : 'text-er font-semibold' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
          <span class="{{ $p->is_available ? 'text-forest/70' : 'text-er font-bold' }}">{{ $p->is_available ? 'Tersedia' : 'Habis' }}</span>
        </div>
      </td>
      <td>
        <div class="flex flex-wrap items-center gap-1.5">
          {{-- Ketersediaan dapat diubah oleh Staf dan Pemilik (FR-09.3) --}}
          <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="field" value="is_available">
            <button class="btn s sm">{{ $p->is_available ? 'Tandai habis' : 'Tandai tersedia' }}</button>
          </form>

          @if($isOwner)
            {{-- Tambah, ubah, nonaktifkan hanya oleh Pemilik (FR-09.1, FR-09.2, Tabel 2.3) --}}
            <a class="btn s sm" href="{{ route('admin.products.edit', $p->id) }}">Ubah</a>
            <form method="POST" action="{{ route('admin.products.toggle', $p->id) }}">
              @csrf @method('PATCH')
              <input type="hidden" name="field" value="is_active">
              <button class="btn s sm {{ $p->is_active ? 'hover:!bg-er/10 hover:!text-er' : '' }}">{{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
          @endif
        </div>
      </td>
    </tr>
  @endforeach
  </tbody></table></div>
@endsection
