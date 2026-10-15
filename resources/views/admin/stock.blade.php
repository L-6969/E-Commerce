@extends('layouts.admin')
@section('title', 'Stok bahan')
@section('content')
{{-- Variabel: $items[id,name,qty]. POST route('admin.stock.adjust', id): delta (+1 / -1) --}}
<h2>Stok bahan baku</h2>
<div class="grid gap-3">@foreach($items as $s)
  <div class="card flex flex-wrap items-center gap-2"><b>{{ $s->name }}</b>
    <form method="POST" action="{{ route('admin.stock.adjust', $s->id) }}" class="flex flex-wrap items-center gap-2 ml-auto">@csrf
      <button class="btn s sm" name="delta" value="-1">−1</button><b>{{ $s->qty }}</b><button class="btn s sm" name="delta" value="1">+1</button></form></div>
@endforeach</div>
@endsection
