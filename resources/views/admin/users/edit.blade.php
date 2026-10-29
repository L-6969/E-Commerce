@extends('layouts.admin')
@section('title', 'Ubah akun')
@section('content')
@php $isOwner = ($authUser ?? auth()->user())->role === 'owner'; @endphp
@if(!$isOwner)
  <div class="card"><b>Akses ditolak (403).</b> Hanya Pemilik yang dapat mengelola akun.</div>
@else
<h2>Ubah akun: {{ $user->name }}</h2>
@include('admin.users.form', ['action' => route('admin.users.update', $user->id), 'method' => 'PUT'])
@endif
@endsection
