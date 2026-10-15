{{-- Partial. Variabel: $categories, $product (null jika baru), $action, $method (POST|PUT) --}}
@php $p = $product ?? null; @endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card" x-data="{ photoErr: '' }">
  @csrf @if(($method ?? 'POST') !== 'POST') @method($method) @endif
  <div class="f"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $p->name ?? '') }}" required minlength="2"></div>
  <div class="f"><label for="description">Deskripsi</label><textarea id="description" name="description" rows="2">{{ old('description', $p->description ?? '') }}</textarea></div>
  <div class="f"><label for="category">Kategori</label><select id="category" name="category">@foreach($categories as $c)<option @selected(old('category', $p->category ?? '') === $c)>{{ $c }}</option>@endforeach</select></div>
  <div class="f"><label for="price">Harga (rupiah, bilangan bulat)</label><input id="price" type="number" name="price" min="1" step="1" value="{{ old('price', $p->price ?? '') }}" required></div>
  <div class="f"><label for="emoji">Ikon</label><input id="emoji" name="emoji" maxlength="4" value="{{ old('emoji', $p->emoji ?? '🍢') }}"></div>
  <div class="f"><label for="stock">Stok produk</label><input id="stock" type="number" name="stock" min="0" value="{{ old('stock', $p->stock ?? 0) }}"></div>
  <div class="f"><label for="photo">Foto (WebP, maksimal 200 KB)</label>
    <input id="photo" type="file" name="photo" accept="image/webp"
      x-on:change="const f = $event.target.files[0]; photoErr = ''; if (f && f.type !== 'image/webp') photoErr = 'Format harus WebP. Ubah dulu fotonya ke WebP.'; else if (f && f.size > 204800) photoErr = 'Foto melebihi 200 KB. Kompres ke WebP dulu.'; if (photoErr) $event.target.value = ''">
    <span class="text-[13px] text-er" x-text="photoErr"></span><span class="text-[13px] text-er">{{ $errors->first('photo') }}</span></div>
  <input type="hidden" name="is_available" value="0">
  <label class="flex flex-wrap items-center gap-2"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $p->is_available ?? true))> Tersedia</label>
  <div class="flex flex-wrap items-center gap-2 mt-3"><button class="btn">Simpan produk</button><a class="btn s" href="{{ route('admin.products') }}">Batal</a></div>
</form>
