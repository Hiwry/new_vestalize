@extends('layouts.admin')

@section('content')
@php
    $storeName = (isset($store) && $store ? $store->name : null) ?? (auth()->user()->store ?? 'Minha Loja');
@endphp
<div class="max-w-[1520px] mx-auto py-4 md:py-6">
    @include('dashboard.partials.fintrack-style', [
        'dashboardTitle' => 'Painel Financeiro - ' . $storeName,
    ])
</div>
@endsection
