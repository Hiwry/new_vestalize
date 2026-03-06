@extends('layouts.admin')

@section('content')
<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    @include('dashboard.partials.fintrack-style', [
        'dashboardTitle' => 'Painel Financeiro - Meu Painel',
        'showQuickActions' => true,
        'quickActions' => [
            [
                'label' => 'Abrir PDV',
                'href' => route('pdv.index'),
                'icon' => 'fa-cash-register',
                'variant' => 'success',
            ],
            [
                'label' => 'Novo Pedido',
                'href' => route('orders.wizard.start'),
                'icon' => 'fa-plus',
                'variant' => 'primary',
            ],
        ],
    ])
</div>
@endsection
