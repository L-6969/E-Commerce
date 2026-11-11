@extends('layouts.admin')
@section('title', 'Log aktivitas')
@section('content')
{{-- Hanya Pemilik (Tabel 2.3 Matriks Hak Akses / FR-17). --}}
@if(!($isOwner ?? (($authUser ?? auth()->user())->role === 'owner')))
  <div class="card"><b>Akses ditolak (403).</b> Log aktivitas lengkap hanya untuk peran Pemilik.</div>
@else
<h2>Log aktivitas</h2>
<div class="card">
  @forelse($logs as $l)
    <div class="border-b border-bd/40 py-2 text-sm text-mu last:border-0">
      <span class="font-bold text-forest">{{ $l->created_at->format('d/m/Y H:i') }}</span> · <span class="rounded bg-forest/10 px-1.5 py-0.5 text-xs font-semibold text-forest">{{ $l->user }}</span> · {{ $l->action }}
    </div>
  @empty
    <p class="text-sm text-mu">Belum ada aktivitas.</p>
  @endforelse
</div>
@endif
@endsection
