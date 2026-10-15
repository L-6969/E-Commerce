@extends('layouts.admin')
@section('title', 'Ubah produk')
@section('content')<h2>Ubah produk</h2>@include('admin.products.form', ['action' => route('admin.products.update', $product->id), 'method' => 'PUT'])@endsection
