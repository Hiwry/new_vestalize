@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">

        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Lista de Espera VIP</h1>
        </div>

        <!-- Right: Actions  -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Filter button could go here -->
        </div>

    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
        <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Total de Leads <span class="text-gray-400 dark:text-gray-500 font-medium">({{ $leads->total() }})</span></h2>
        </header>
        <div class="p-3">

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <!-- Table header -->
                    <thead class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Nome</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Email</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Telefone</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Data Cadastro</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Status</div>
                            </th>
                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($leads as $lead)
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-gray-800 dark:text-gray-100 font-medium">{{ $lead->name }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">{{ $lead->email }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left font-medium">{{ $lead->phone }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left">{{ $lead->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="p-2 whitespace-nowrap">
                                    <div class="text-left text-green-500">{{ $lead->status ?? 'Novo' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-2 py-8 text-center text-gray-500">
                                    Nenhum lead cadastrado ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
