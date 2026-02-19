@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Solicitações de Antecipação</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gerencie solicitações de antecipação de data de entrega</p>
    </div>
</div>

        @if(session('success'))
<div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Tabs -->
<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <nav class="-mb-px flex space-x-8">
        <button onclick="showTab('pendente')" id="tab-pendente" class="tab-button border-b-2 border-indigo-500 dark:border-indigo-400 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    Pendentes ({{ $requests->where('status', 'pendente')->count() }})
                </button>
        <button onclick="showTab('aprovado')" id="tab-aprovado" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Aprovadas ({{ $requests->where('status', 'aprovado')->count() }})
                </button>
        <button onclick="showTab('rejeitado')" id="tab-rejeitado" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Rejeitadas ({{ $requests->where('status', 'rejeitado')->count() }})
                </button>
            </nav>
        </div>

        <!-- Solicitações Pendentes -->
        <div id="content-pendente" class="tab-content">
            @forelse($requests->where('status', 'pendente') as $request)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-4 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('orders.show', $request->order->id) }}" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            </h3>
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                Pendente
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Cliente:</strong> {{ $request->order->client->name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Solicitado por:</strong> {{ $request->requested_by_name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data da Solicitação:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data Atual:</strong> <span class="text-orange-600 dark:text-orange-400 font-semibold">{{ \Carbon\Carbon::parse($request->current_delivery_date)->format('d/m/Y') }}</span></p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data Solicitada:</strong> <span class="text-green-600 dark:text-green-400 font-semibold">{{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</span></p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Antecipação:</strong> {{ \Carbon\Carbon::parse($request->current_delivery_date)->diffInDays(\Carbon\Carbon::parse($request->requested_delivery_date)) }} dias</p>
                            </div>
                        </div>

<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4 border border-gray-200 dark:border-gray-600">
<p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Motivo:</p>
<p class="text-sm text-gray-600 dark:text-gray-400">{{ $request->reason }}</p>
                        </div>

                        <div class="flex gap-3">
                            <button onclick="openApproveModal({{ $request->id }})" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 text-sm transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
</svg>
                                Aprovar
                            </button>
                            <button onclick="openRejectModal({{ $request->id }})" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-600 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-700 text-sm transition-colors">
<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
</svg>
                                Rejeitar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-12 text-center border border-gray-200 dark:border-gray-700">
<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
</svg>
<p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma solicitação pendente.</p>
            </div>
            @endforelse
        </div>

        <!-- Solicitações Aprovadas -->
        <div id="content-aprovado" class="tab-content hidden">
            @forelse($requests->where('status', 'aprovado') as $request)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-4 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('orders.show', $request->order->id) }}" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            </h3>
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                Aprovada
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Cliente:</strong> {{ $request->order->client->name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Solicitado por:</strong> {{ $request->requested_by_name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Aprovado por:</strong> {{ $request->reviewed_by_name }}</p>
                            </div>
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data Original:</strong> {{ \Carbon\Carbon::parse($request->current_delivery_date)->format('d/m/Y') }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Nova Data:</strong> <span class="text-green-600 dark:text-green-400 font-semibold">{{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</span></p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Aprovado em:</strong> {{ $request->reviewed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($request->review_notes)
<div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
<p class="text-sm font-semibold text-green-700 dark:text-green-300 mb-1">Observações:</p>
<p class="text-sm text-green-600 dark:text-green-400">{{ $request->review_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-12 text-center border border-gray-200 dark:border-gray-700">
<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma solicitação aprovada.</p>
            </div>
            @endforelse
        </div>

        <!-- Solicitações Rejeitadas -->
        <div id="content-rejeitado" class="tab-content hidden">
            @forelse($requests->where('status', 'rejeitado') as $request)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-4 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">
                                <a href="{{ route('orders.show', $request->order->id) }}" 
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            </h3>
<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                Rejeitada
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Cliente:</strong> {{ $request->order->client->name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Solicitado por:</strong> {{ $request->requested_by_name }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Rejeitado por:</strong> {{ $request->reviewed_by_name }}</p>
                            </div>
                            <div>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Data Solicitada:</strong> {{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</p>
<p class="text-sm text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-gray-100">Rejeitado em:</strong> {{ $request->reviewed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($request->review_notes)
<div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 border border-red-200 dark:border-red-800">
<p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">Motivo da Rejeição:</p>
<p class="text-sm text-red-600 dark:text-red-400">{{ $request->review_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-12 text-center border border-gray-200 dark:border-gray-700">
<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
<p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma solicitação rejeitada.</p>
            </div>
            @endforelse
        </div>

    <!-- Modal de Aprovação -->
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-md w-full">
<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aprovar Solicitação</h3>
<button onclick="closeApproveModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form id="approve-form" method="POST">
                @csrf
                <div class="px-6 py-4">
<label for="approve-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observações (opcional)
                    </label>
                    <textarea id="approve-notes" 
                              name="review_notes" 
                              rows="3"
                              maxlength="500"
                              placeholder="Adicione observações sobre a aprovação..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-500"></textarea>
                </div>

<div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" 
                            onclick="closeApproveModal()"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 dark:bg-green-600 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-700 transition">
                        Aprovar Solicitação
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Modal de Rejeição -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl dark:shadow-gray-900/50 max-w-md w-full">
<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
<h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rejeitar Solicitação</h3>
<button onclick="closeRejectModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form id="reject-form" method="POST">
                @csrf
                <div class="px-6 py-4">
<label for="reject-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo da Rejeição *
                    </label>
                    <textarea id="reject-notes" 
                              name="review_notes" 
                              rows="3"
                              required
                              maxlength="500"
                              placeholder="Explique o motivo da rejeição..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-500"></textarea>
                </div>

<div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 dark:bg-red-600 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-700 transition">
                        Rejeitar Solicitação
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <script>
        function showTab(status) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'dark:border-indigo-400', 'text-indigo-600', 'dark:text-indigo-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            document.getElementById('content-' + status).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-' + status);
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('border-indigo-500', 'dark:border-indigo-400', 'text-indigo-600', 'dark:text-indigo-400');
        }

        function openApproveModal(requestId) {
            document.getElementById('approve-form').action = `/producao/delivery-requests/${requestId}/approve`;
            document.getElementById('approve-modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
            document.getElementById('approve-form').reset();
            document.body.classList.remove('overflow-hidden');
        }

        function openRejectModal(requestId) {
            document.getElementById('reject-form').action = `/producao/delivery-requests/${requestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-form').reset();
            document.body.classList.remove('overflow-hidden');
        }

        document.getElementById('approve-modal').addEventListener('click', function(e) {
            if (e.target === this) closeApproveModal();
        });

        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeApproveModal();
                closeRejectModal();
            }
        });
    </script>
@endsection
