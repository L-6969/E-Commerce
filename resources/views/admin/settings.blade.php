@extends('layouts.admin')
@section('title', 'Pengaturan & Akun Staf')
@section('content')
{{-- Hanya Pemilik (FR-08.4, FR-16, Tabel 2.3). Variabel: $settings{name,wa,hours,address,halal,template}, $staffList --}}
@php $isOwner = $isOwner ?? (($authUser ?? auth()->user())->role === 'owner'); @endphp
@if(!$isOwner)
  <div class="card"><b>Akses ditolak (403).</b> Hanya Pemilik yang dapat mengubah pengaturan dan mengelola akun staf.</div>
@else
<div class="grid gap-6">
  <div>
    <h2>Pengaturan toko</h2>
    <p class="text-sm text-mu mb-3">Kelola informasi outlet, nomor WhatsApp pemesanan, jam operasional, dan sertifikat halal (FR-16).</p>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="card">
      @csrf @method('PUT')
      <div class="f"><label for="name">Nama usaha</label><input id="name" name="name" value="{{ old('name', $settings->name) }}" required></div>
      <div class="f"><label for="wa">Nomor WhatsApp pemesanan (format 62…)</label><input id="wa" name="wa" inputmode="numeric" pattern="62[0-9]{8,13}" value="{{ old('wa', $settings->wa) }}" required><span class="text-xs text-mu">Nomor ini menjadi tujuan saat pembeli menekan tombol WhatsApp.</span></div>
      <div class="f"><label for="hours">Jam buka</label><input id="hours" name="hours" value="{{ old('hours', $settings->hours) }}" required></div>
      <div class="f"><label for="address">Alamat outlet</label><input id="address" name="address" value="{{ old('address', $settings->address) }}" required></div>
      <div class="f"><label for="halal">Nomor sertifikat halal (kosongkan jika belum diverifikasi)</label><input id="halal" name="halal" value="{{ old('halal', $settings->halal) }}"><span class="text-xs text-mu">Lencana Halal di katalog hanya tampil jika nomor sertifikat terisi (FR-16.2 / P-08).</span></div>
      <div class="f"><label for="template">Templat pesan WhatsApp (kalimat pembuka)</label><textarea id="template" name="template" rows="2">{{ old('template', $settings->template) }}</textarea></div>
      <button class="btn mt-2">Simpan pengaturan</button>
    </form>
  </div>

  {{-- Kelola Akun Staf (FR-08.4 / Tabel 2.3) --}}
  <div x-data="{ showForm: false }">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
      <div>
        <h2 class="mb-0">Kelola akun Staf</h2>
        <p class="text-sm text-mu">Pemilik dapat membuat dan menonaktifkan akun staf outlet untuk operasional kasir (FR-08.4).</p>
      </div>
      <button type="button" class="btn s sm" x-on:click="showForm = !showForm" x-text="showForm ? 'Batal' : '+ Tambah Staf'"></button>
    </div>

    {{-- Form Tambah Akun Staf --}}
    <div class="card mb-3" x-show="showForm" style="display:none">
      <h3 class="mb-2">Tambah Akun Staf Baru</h3>
      <form method="POST" action="{{ route('admin.settings.staff.store') }}">
        @csrf
        <div class="f"><label for="staff_name">Nama Staf</label><input id="staff_name" name="name" required placeholder="Contoh: Rian (Kasir Shift Pagi)"></div>
        <div class="f"><label for="staff_email">Email Login</label><input id="staff_email" type="email" name="email" required placeholder="kasir1@ciloksr.com"></div>
        <div class="f"><label for="staff_password">Kata Sandi Awal</label><input id="staff_password" type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"></div>
        <div class="flex gap-2 mt-3">
          <button class="btn sm">Simpan Akun Staf</button>
          <button type="button" class="btn s sm" x-on:click="showForm = false">Batal</button>
        </div>
      </form>
    </div>

    {{-- Daftar Akun Staf --}}
    <div class="overflow-x-auto card">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Peran</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($staffList ?? [] as $st)
            <tr>
              <td><b>{{ $st->name }}</b></td>
              <td>{{ $st->email }}</td>
              <td><span class="chip">{{ $st->role === 'staff' ? 'Staf Outlet' : $st->role }}</span></td>
              <td>
                <span class="{{ $st->is_active ? 'text-forest font-semibold' : 'text-er font-semibold' }}">
                  {{ $st->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.settings.staff.toggle', $st->id) }}">
                  @csrf @method('PATCH')
                  <button class="btn s sm {{ $st->is_active ? 'hover:!bg-er/10 hover:!text-er' : '' }}">
                    {{ $st->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-sm text-mu">Belum ada akun Staf. Klik "+ Tambah Staf" untuk membuat akun baru.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
@endsection
