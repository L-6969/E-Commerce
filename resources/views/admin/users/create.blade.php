@extends('layouts.admin')
@section('title', 'Tambah Staf')
@section('content')
@php $isOwner = ($authUser ?? auth()->user())->role === 'owner'; @endphp
@if(!$isOwner)
  <div class="card"><b>Akses ditolak (403).</b> Hanya Pemilik yang dapat mengelola akun.</div>
@else
<h2>Tambah akun Staf</h2>
@include('admin.users.form', ['user' => null, 'action' => route('admin.users.store'), 'method' => 'POST'])
@endif
@endsection
