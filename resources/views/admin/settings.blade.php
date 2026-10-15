@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')
{{-- Hanya Pemilik. Variabel: $settings{name,wa,hours,address,halal,template} --}}
<h2>Pengaturan toko</h2>
@if(($authUser ?? auth()->user())->role !== 'owner')<div class="card">Hanya Pemilik yang dapat mengubah pengaturan.</div>
@else
<form method="POST" action="{{ route('admin.settings.update') }}" class="card">
  @csrf @method('PUT')
  <div class="f"><label for="name">Nama usaha</label><input id="name" name="name" value="{{ old('name', $settings->name) }}"></div>
  <div class="f"><label for="wa">Nomor WhatsApp (format 62…)</label><input id="wa" name="wa" inputmode="numeric" pattern="62[0-9]{8,13}" value="{{ old('wa', $settings->wa) }}"></div>
  <div class="f"><label for="hours">Jam buka</label><input id="hours" name="hours" value="{{ old('hours', $settings->hours) }}"></div>
  <div class="f"><label for="address">Alamat outlet</label><input id="address" name="address" value="{{ old('address', $settings->address) }}"></div>
  <div class="f"><label for="halal">Nomor sertifikat halal (kosongkan jika belum ada)</label><input id="halal" name="halal" value="{{ old('halal', $settings->halal) }}"></div>
  <div class="f"><label for="template">Templat pesan WhatsApp (kalimat pembuka)</label><textarea id="template" name="template" rows="2">{{ old('template', $settings->template) }}</textarea></div>
  <button class="btn">Simpan pengaturan</button>
</form>
@endif
@endsection
