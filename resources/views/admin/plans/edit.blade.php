@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Editar Plano') }}: {{ $plan->name }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                    @method('PUT')
                    @include('admin.plans._form', ['plan' => $plan])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
