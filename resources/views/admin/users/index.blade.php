@extends('layouts.admin')
@section('title', 'Kelola akun')
@section('content')
{{-- Hanya Pemilik (FR-08.4). Variabel: $users (semua pengguna dashboard). --}}
@php $isOwner = ($authUser ?? auth()->user())->role === 'owner'; @endphp
@if(!$isOwner)
  <div class="card"><b>Akses ditolak (403).</b> Hanya Pemilik yang dapat mengelola akun.</div>
@else
<div class="flex flex-wrap items-center gap-2"><h2>Kelola akun</h2><a class="btn ml-auto" href="{{ route('admin.users.create') }}">Tambah Staf</a></div>
<div class="overflow-x-auto card">
  <table>
    <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @foreach($users as $u)
      <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td><span class="chip{{ $u->role === 'owner' ? ' on' : '' }}">{{ $u->role === 'owner' ? 'Pemilik' : 'Staf' }}</span></td>
        <td>{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
          @if($u->role !== 'owner')
          <div class="flex flex-wrap items-center gap-2">
            <a class="btn s sm" href="{{ route('admin.users.edit', $u->id) }}">Ubah</a>
            <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}">@csrf @method('PATCH')
              <button class="btn s sm">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
          </div>
          @else
          <span class="text-sm text-mu">—</span>
          @endif
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
@endsection
