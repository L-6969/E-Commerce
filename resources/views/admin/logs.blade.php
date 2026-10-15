@extends('layouts.admin')
@section('title', 'Log aktivitas')
@section('content')
{{-- Variabel: $logs[created_at,user,action] --}}
<h2>Log aktivitas</h2>
<div class="card">@forelse($logs as $l)<div class="text-sm text-mu">{{ $l->created_at->format('d/m/Y H:i') }} · {{ $l->user }} · {{ $l->action }}</div>@empty<p class="text-sm text-mu">Belum ada aktivitas.</p>@endforelse</div>
@endsection
