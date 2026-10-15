@extends('layouts.admin')
@section('title', 'Produk baru')
@section('content')<h2>Produk baru</h2>@include('admin.products.form', ['product' => null, 'action' => route('admin.products.store'), 'method' => 'POST'])@endsection
