<?php

namespace App\Http\Controllers;

use App\Helpers\StoreHelper;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class CashController extends Controller
{
    public function __construct()
    {
        // Middleware será aplicado via rotas
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->tenant_id === null;
        
        // Verificar se o usuário é administrador ou caixa
        if (!$user->isAdmin() && !$user->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        // Se não houver filtro, mostrar TODAS as transações
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'all');

        // Base sem filtro de período para calcular saldos gerais respeitando tenant/loja
        $baseScopedQuery = CashTransaction::query();
        StoreHelper::applyStoreFilter($baseScopedQuery);
        $pendentesVendaTotal = (clone $baseScopedQuery)
            ->where('status', 'pendente')
            ->whereRaw("LOWER(COALESCE(category, '')) <> 'sangria'")
            ->count();
        $confirmadasTotal = (clone $baseScopedQuery)
            ->where('status', 'confirmado')
            ->whereRaw("LOWER(COALESCE(category, '')) <> 'sangria'")
            ->count();
        $latestCashReferenceDateRaw = (clone $baseScopedQuery)
            ->where('status', 'confirmado')
            ->max('transaction_date');
        $latestCashReferenceDate = $latestCashReferenceDateRaw
            ? Carbon::parse($latestCashReferenceDateRaw)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        $query = CashTransaction::with(['order', 'user']);
        StoreHelper::applyStoreFilter($query);

        // Aplicar filtro de data (incluindo o dia final completo)
        if ($startDate && $endDate) {
            try {
                $startFilter = Carbon::parse($startDate)->startOfDay();
                $endFilter = Carbon::parse($endDate)->endOfDay();

                // Se vier invertido por engano, corrige sem quebrar a listagem
                if ($startFilter->gt($endFilter)) {
                    [$startFilter, $endFilter] = [$endFilter, $startFilter];
                }

                $query->whereBetween('transaction_date', [$startFilter, $endFilter]);

                // Normaliza para manter os inputs da tela no formato esperado
                $startDate = $startFilter->format('Y-m-d');
                $endDate = $endFilter->format('Y-m-d');
            } catch (\Throwable $e) {
                \Log::warning('Filtro de data inválido no caixa', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $query->orderBy('transaction_date', 'desc')
              ->orderBy('created_at', 'desc');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->get();
        
        // Se não houver filtro, definir datas padrão para exibição
        if (!$startDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Separar transações por colunas
        $pendentes = $transactions->where('status', 'pendente');
        $confirmadas = $transactions->where('status', 'confirmado')->filter(function($t) {
            return strtolower($t->category) !== 'sangria';
        });
        $canceladas = $transactions->where('status', 'cancelado');
        $sangrias = $transactions->where('status', 'confirmado')->filter(function($t) {
            return strtolower($t->category) === 'sangria';
        });
        $pendentesVenda = $transactions->where('status', 'pendente')->filter(function($t) {
            return strtolower($t->category) !== 'sangria';
        });

        // Calcular totais por coluna
        $totalPendentes = $pendentes->where('type', 'entrada')->sum('amount');
        $totalConfirmadas = $confirmadas->where('type', 'entrada')->sum('amount') - $confirmadas->where('type', 'saida')->sum('amount');
        $totalCanceladas = $canceladas->sum('amount');
        $totalSangrias = $sangrias->sum('amount');

        // Calcular totais do período
        $totalEntradas = $transactions->where('type', 'entrada')->sum('amount');
        $totalSaidas = $transactions->where('type', 'saida')->sum('amount');
        $saldoPeriodo = $totalEntradas - $totalSaidas;
        
        // Calcular saldos gerais respeitando escopo de loja/tenant do usuário
        $entradasConfirmadas = (clone $baseScopedQuery)
            ->where('type', 'entrada')
            ->where('status', 'confirmado')
            ->sum('amount');
        $saidasConfirmadas = (clone $baseScopedQuery)
            ->where('type', 'saida')
            ->where('status', 'confirmado')
            ->sum('amount');

        $saldoAtual = $entradasConfirmadas - $saidasConfirmadas; // Apenas confirmadas
        $saldoGeral = $saldoAtual; // Mantido por compatibilidade visual
        $saldoPendente = (clone $baseScopedQuery)
            ->where('type', 'entrada')
            ->where('status', 'pendente')
            ->sum('amount');
        $totalSaidasGeral = (clone $baseScopedQuery)
            ->where('type', 'saida')
            ->sum('amount');

        return view('cash.index', compact(
            'transactions',
            'pendentes',
            'confirmadas',
            'canceladas',
            'sangrias',
            'totalPendentes',
            'totalConfirmadas',
            'totalCanceladas',
            'totalSangrias',
            'totalEntradas',
            'totalSaidas',
            'saldoPeriodo',
            'saldoAtual',
            'saldoGeral',
            'saldoPendente',
            'totalSaidasGeral',
            'startDate',
            'endDate',
            'type',
            'isSuperAdmin',
            'pendentesVenda',
            'pendentesVendaTotal',
            'confirmadasTotal',
            'latestCashReferenceDate'
        ));
    }

    public function create()
    {
        // Verificar se o usuário é administrador ou caixa
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        return view('cash.create');
    }

    public function store(Request $request)
    {
        // Verificar se o usuário é administrador ou caixa
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        try {
            // Log para debug
            \Log::info('Tentando criar transação', $request->all());
            
            $validated = $request->validate([
                'type' => 'required|in:entrada,saida',
                'status' => 'required|in:pendente,confirmado,cancelado',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'nullable|in:dinheiro,pix,cartao,transferencia,boleto,entrada_dinheiro,debito_conta,credito_conta,multiplo',
                'payment_methods' => 'nullable|json',
                'transaction_date' => 'required|date',
                'order_id' => 'nullable|exists:orders,id',
                'notes' => 'nullable|string',
            ]);

            $user = Auth::user();
            
            if (!$user) {
                \Log::error('Usuário não autenticado');
                return redirect()->route('login')
                    ->with('error', 'Você precisa estar autenticado para registrar transações.');
            }

            $validated['user_id'] = $user->id;
            $validated['user_name'] = $user->name;

            // Processar múltiplos meios de pagamento
            if ($request->has('payment_methods') && $request->payment_methods) {
                $paymentMethods = is_string($request->payment_methods) 
                    ? json_decode($request->payment_methods, true) 
                    : $request->payment_methods;
                
                if (is_array($paymentMethods) && count($paymentMethods) > 0) {
                    $validated['payment_methods'] = $paymentMethods;
                    $validated['payment_method'] = 'multiplo';
                    
                    // Calcular total dos métodos de pagamento
                    $totalMethods = array_sum(array_column($paymentMethods, 'amount'));
                    if (abs($totalMethods - $validated['amount']) > 0.01) {
                        $validated['amount'] = $totalMethods; // Usar total dos métodos
                    }
                }
            } elseif ($request->has('payment_method')) {
                // Se não houver payment_methods, criar array com método único
                $validated['payment_methods'] = [[
                    'method' => $validated['payment_method'],
                    'amount' => $validated['amount']
                ]];
            }

            // Garantir que transaction_date seja datetime completo (com hora)
            if (isset($validated['transaction_date'])) {
                $transactionDate = Carbon::parse($validated['transaction_date']);
                // Se não tiver hora definida, usar a hora atual
                if ($transactionDate->format('H:i:s') === '00:00:00' && !str_contains($validated['transaction_date'], ':')) {
                    $transactionDate = Carbon::now();
                }
                $validated['transaction_date'] = $transactionDate;
            }

            \Log::info('Dados validados', $validated);

            $transaction = CashTransaction::create($validated);
            
            \Log::info('Transação criada com ID: ' . $transaction->id);

            return redirect()->route('cash.index')
                ->with('success', 'Transação registrada com sucesso! ID: ' . $transaction->id);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Erro de validação. Verifique os campos.');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar transação: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao registrar transação: ' . $e->getMessage());
        }
    }

    public function edit(CashTransaction $cash)
    {
        // Verificar se o usuário é administrador ou caixa
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        return view('cash.edit', compact('cash'));
    }

    public function update(Request $request, CashTransaction $cash)
    {
        // Verificar se o usuário é administrador ou caixa
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        $validated = $request->validate([
            'type' => 'required|in:entrada,saida',
            'status' => 'required|in:pendente,confirmado,cancelado',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|in:dinheiro,pix,cartao,transferencia,boleto,entrada_dinheiro,debito_conta,credito_conta,multiplo',
            'payment_methods' => 'nullable|json',
            'transaction_date' => 'required|date',
            'order_id' => 'nullable|exists:orders,id',
            'notes' => 'nullable|string',
        ]);

        // Processar múltiplos meios de pagamento
        if ($request->has('payment_methods') && $request->payment_methods) {
            $paymentMethods = is_string($request->payment_methods) 
                ? json_decode($request->payment_methods, true) 
                : $request->payment_methods;
            
            if (is_array($paymentMethods) && count($paymentMethods) > 0) {
                $validated['payment_methods'] = $paymentMethods;
                $validated['payment_method'] = 'multiplo';
                
                // Calcular total dos métodos de pagamento
                $totalMethods = array_sum(array_column($paymentMethods, 'amount'));
                if (abs($totalMethods - $validated['amount']) > 0.01) {
                    $validated['amount'] = $totalMethods; // Usar total dos métodos
                }
            }
        } elseif ($request->has('payment_method')) {
            // Se não houver payment_methods, criar array com método único
            $validated['payment_methods'] = [[
                'method' => $validated['payment_method'],
                'amount' => $validated['amount']
            ]];
        }

        // Garantir que transaction_date seja datetime completo (com hora)
        if (isset($validated['transaction_date'])) {
            $transactionDate = Carbon::parse($validated['transaction_date']);
            // Se não tiver hora definida, manter a hora atual da transação ou usar a hora atual
            if ($transactionDate->format('H:i:s') === '00:00:00' && !str_contains($validated['transaction_date'], ':')) {
                // Se a transação existente já tem hora, tentar manter o horário ou usar hora atual
                if ($cash->transaction_date && $cash->transaction_date->format('H:i:s') !== '00:00:00') {
                    $transactionDate->setTime(
                        $cash->transaction_date->hour,
                        $cash->transaction_date->minute,
                        $cash->transaction_date->second
                    );
                } else {
                    $transactionDate = Carbon::now();
                }
            }
            $validated['transaction_date'] = $transactionDate;
        }

        $cash->update($validated);

        return redirect()->route('cash.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    /**
     * Atualização rápida de status (usado no Kanban drag-and-drop).
     */
    public function updateStatus(Request $request, $cash)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pendente,confirmado,cancelado',
        ]);

        $query = CashTransaction::query();
        StoreHelper::applyStoreFilter($query);
        $transaction = $query->find($cash);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada.',
            ], 404);
        }

        $oldStatus = $transaction->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return response()->json([
                'success' => true,
                'message' => 'Status já estava atualizado.',
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                ],
            ]);
        }

        $transaction->status = $newStatus;

        $statusNote = sprintf(
            'Status alterado de %s para %s por %s em %s',
            $oldStatus,
            $newStatus,
            Auth::user()->name ?? 'Sistema',
            now()->format('d/m/Y H:i')
        );

        $transaction->notes = trim(($transaction->notes ? ($transaction->notes . PHP_EOL) : '') . $statusNote);
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso.',
            'transaction' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
            ],
        ]);
    }

    public function destroy(CashTransaction $cash)
    {
        // Verificar se o usuário é administrador ou caixa
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        $cash->delete();

        return redirect()->route('cash.index')
            ->with('success', 'Transação excluída com sucesso!');
    }

    /**
     * Relatório simplificado (resumo por período)
     */
    public function reportSimplified(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado.'
                ], 403);
            }

            $period = strtolower((string) $request->get('period', 'month'));
            $allowedPeriods = ['day', 'week', 'month', 'year', 'custom'];
            if (!in_array($period, $allowedPeriods, true)) {
                $period = 'month';
            }

            $referenceDate = Carbon::parse($request->get('date', Carbon::now()->format('Y-m-d')));
            $startFilter = null;
            $endFilter = null;

            switch ($period) {
                case 'day':
                    $startFilter = $referenceDate->copy()->startOfDay();
                    $endFilter = $referenceDate->copy()->endOfDay();
                    break;
                case 'week':
                    $startFilter = $referenceDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                    $endFilter = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                    break;
                case 'custom':
                    $startFilter = Carbon::parse($request->get('start_date', $referenceDate->format('Y-m-d')))->startOfDay();
                    $endFilter = Carbon::parse($request->get('end_date', $referenceDate->format('Y-m-d')))->endOfDay();
                    if ($startFilter->gt($endFilter)) {
                        [$startFilter, $endFilter] = [$endFilter, $startFilter];
                    }
                    break;
                case 'month':
                    $startFilter = $referenceDate->copy()->startOfMonth()->startOfDay();
                    $endFilter = $referenceDate->copy()->endOfMonth()->endOfDay();
                    break;
                case 'year':
                    $startFilter = $referenceDate->copy()->startOfYear()->startOfDay();
                    $endFilter = $referenceDate->copy()->endOfYear()->endOfDay();
                    break;
                default:
                    $startFilter = $referenceDate->copy()->startOfMonth()->startOfDay();
                    $endFilter = $referenceDate->copy()->endOfMonth()->endOfDay();
                    break;
            }

            // Carregar relacionamentos necessários, incluindo order.user
            $transactionsQuery = CashTransaction::with(['order.user', 'order.items', 'user'])
                ->whereBetween('transaction_date', [$startFilter, $endFilter])
                ->where('status', 'confirmado');
            StoreHelper::applyStoreFilter($transactionsQuery);
            $transactions = $transactionsQuery->get();
            $latestConfirmedDateQuery = CashTransaction::query()
                ->where('status', 'confirmado');
            StoreHelper::applyStoreFilter($latestConfirmedDateQuery);
            $latestConfirmedDateRaw = $latestConfirmedDateQuery->max('transaction_date');
            $latestConfirmedDate = $latestConfirmedDateRaw
                ? Carbon::parse($latestConfirmedDateRaw)->format('Y-m-d')
                : null;

            // Resumo por meio de pagamento
            $byPaymentMethod = [];
            $totalEntradas = 0;
            $totalSaidas = 0;
            $comissoesPorVendedor = [];
            $totalProdutos = 0;
            $totalDescontos = 0;

            foreach ($transactions as $transaction) {
                // Processar payment_methods de forma mais robusta
                $paymentMethods = [];
                
                // Se payment_methods existe e é um array válido
                if (!empty($transaction->payment_methods) && is_array($transaction->payment_methods)) {
                    $paymentMethods = $transaction->payment_methods;
                } 
                // Se payment_methods é uma string JSON, tentar decodificar
                elseif (!empty($transaction->payment_methods) && is_string($transaction->payment_methods)) {
                    $decoded = json_decode($transaction->payment_methods, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $paymentMethods = $decoded;
                    }
                }
                
                // Se não há payment_methods mas há payment_method, criar array
                if (empty($paymentMethods) && !empty($transaction->payment_method)) {
                    $paymentMethods = [[
                        'method' => $transaction->payment_method,
                        'amount' => floatval($transaction->amount ?? 0)
                    ]];
                }
                
                // Se ainda não há métodos, usar o valor total da transação
                if (empty($paymentMethods)) {
                    $paymentMethods = [[
                        'method' => $transaction->payment_method ?? 'outros',
                        'amount' => floatval($transaction->amount ?? 0)
                    ]];
                }

                // Processar cada método de pagamento
                foreach ($paymentMethods as $method) {
                    // Garantir que method é um array
                    if (!is_array($method)) {
                        continue;
                    }
                    
                    $methodName = $method['method'] ?? $transaction->payment_method ?? 'outros';
                    $methodAmount = floatval($method['amount'] ?? $transaction->amount ?? 0);

                    if (empty($methodName)) {
                        $methodName = 'outros';
                    }

                    if (!isset($byPaymentMethod[$methodName])) {
                        $byPaymentMethod[$methodName] = [
                            'entradas' => 0,
                            'saidas' => 0
                        ];
                    }

                    if ($transaction->type === 'entrada') {
                        $byPaymentMethod[$methodName]['entradas'] += $methodAmount;
                        $totalEntradas += $methodAmount;
                    } else {
                        $byPaymentMethod[$methodName]['saidas'] += $methodAmount;
                        $totalSaidas += $methodAmount;
                    }
                }

                // Comissão por vendedor (se houver pedido)
                if ($transaction->order && $transaction->type === 'entrada') {
                    $vendedorId = $transaction->order->user_id ?? null;
                    
                    // Tentar obter o nome do vendedor de forma segura
                    $vendedorNome = 'Sem vendedor';
                    if ($vendedorId) {
                        if ($transaction->order->user) {
                            $vendedorNome = $transaction->order->user->name ?? 'Sem vendedor';
                        } elseif ($transaction->user) {
                            $vendedorNome = $transaction->user->name ?? 'Sem vendedor';
                        } elseif ($transaction->user_name) {
                            $vendedorNome = $transaction->user_name;
                        }
                    }
                    
                    if ($vendedorId) {
                        if (!isset($comissoesPorVendedor[$vendedorId])) {
                            $comissoesPorVendedor[$vendedorId] = [
                                'nome' => $vendedorNome,
                                'total' => 0,
                                'transacoes' => 0
                            ];
                        }
                        
                        $comissoesPorVendedor[$vendedorId]['total'] += floatval($transaction->amount ?? 0);
                        $comissoesPorVendedor[$vendedorId]['transacoes']++;
                    }
                }

                // Total de produtos e descontos (se houver pedido)
                if ($transaction->order && $transaction->order->items) {
                    $totalProdutos += $transaction->order->items->sum('quantity') ?? 0;
                    
                    // Calcular desconto (diferença entre subtotal e total)
                    try {
                        $subtotal = $transaction->order->items->sum(function($item) {
                            return floatval($item->quantity ?? 0) * floatval($item->unit_price ?? 0);
                        });
                        $desconto = $subtotal - floatval($transaction->order->total ?? 0);
                        if ($desconto > 0) {
                            $totalDescontos += $desconto;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao calcular desconto na transação', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'date' => $referenceDate->format('Y-m-d'),
                'periodo' => [
                    'tipo' => $period,
                    'inicio' => $startFilter->format('Y-m-d'),
                    'fim' => $endFilter->format('Y-m-d'),
                ],
                'ultima_data_com_movimento' => $latestConfirmedDate,
                'resumo' => [
                    'por_meio_pagamento' => $byPaymentMethod,
                    'total_entradas' => round($totalEntradas, 2),
                    'total_saidas' => round($totalSaidas, 2),
                    'saldo' => round($totalEntradas - $totalSaidas, 2),
                    'comissoes_por_vendedor' => array_values($comissoesPorVendedor),
                    'total_produtos' => $totalProdutos,
                    'total_descontos' => round($totalDescontos, 2),
                    'total_transacoes' => $transactions->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar relatório simplificado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'period' => $request->get('period'),
                'date' => $request->get('date'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Relatório detalhado (descrição completa de cada transação)
     */
    public function reportDetailed(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado.'
                ], 403);
            }

            $startDate = $request->get('start_date', Carbon::now()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $startFilter = Carbon::parse($startDate)->startOfDay();
            $endFilter = Carbon::parse($endDate)->endOfDay();
            if ($startFilter->gt($endFilter)) {
                [$startFilter, $endFilter] = [$endFilter, $startFilter];
            }

            $transactionsQuery = CashTransaction::with(['order.client', 'order.items', 'order.user', 'user'])
                ->whereBetween('transaction_date', [$startFilter, $endFilter])
                ->where('status', 'confirmado')
                ->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc');
            StoreHelper::applyStoreFilter($transactionsQuery);
            $transactions = $transactionsQuery->get();

            $detalhes = $transactions->map(function($transaction) {
                // Processar payment_methods de forma mais robusta
                $paymentMethods = [];
                
                if (!empty($transaction->payment_methods) && is_array($transaction->payment_methods)) {
                    $paymentMethods = $transaction->payment_methods;
                } elseif (!empty($transaction->payment_methods) && is_string($transaction->payment_methods)) {
                    $decoded = json_decode($transaction->payment_methods, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $paymentMethods = $decoded;
                    }
                }
                
                if (empty($paymentMethods) && !empty($transaction->payment_method)) {
                    $paymentMethods = [[
                        'method' => $transaction->payment_method,
                        'amount' => floatval($transaction->amount ?? 0)
                    ]];
                }

                $detalhe = [
                    'id' => $transaction->id,
                    'data' => $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '',
                    'hora' => $transaction->created_at ? $transaction->created_at->format('H:i') : '',
                    'tipo' => $transaction->type ?? '',
                    'categoria' => $transaction->category ?? '',
                    'descricao' => $transaction->description ?? '',
                    'valor' => floatval($transaction->amount ?? 0),
                    'meios_pagamento' => $paymentMethods,
                    'vendedor' => $transaction->user_name ?? ($transaction->user->name ?? 'Sistema'),
                    'status' => $transaction->status ?? '',
                    'observacoes' => $transaction->notes ?? '',
                ];

                // Detalhes do pedido se houver
                if ($transaction->order) {
                    try {
                        $itens = [];
                        if ($transaction->order->items) {
                            $itens = $transaction->order->items->map(function($item) {
                                return [
                                    'nome' => $item->art_name ?? 'Sem nome',
                                    'quantidade' => intval($item->quantity ?? 0),
                                    'preco_unitario' => floatval($item->unit_price ?? 0),
                                    'subtotal' => floatval($item->quantity ?? 0) * floatval($item->unit_price ?? 0)
                                ];
                            })->toArray();
                        }
                        
                        $subtotal = 0;
                        if ($transaction->order->items) {
                            $subtotal = $transaction->order->items->sum(function($item) {
                                return floatval($item->quantity ?? 0) * floatval($item->unit_price ?? 0);
                            });
                        }
                        
                        $total = floatval($transaction->order->total ?? 0);
                        $desconto = max(0, $subtotal - $total);
                        
                        $detalhe['pedido'] = [
                            'id' => $transaction->order->id,
                            'numero' => str_pad($transaction->order->id, 6, '0', STR_PAD_LEFT),
                            'cliente' => $transaction->order->client->name ?? 'Sem cliente',
                            'itens' => $itens,
                            'subtotal' => round($subtotal, 2),
                            'desconto' => round($desconto, 2),
                            'total' => round($total, 2)
                        ];
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao processar detalhes do pedido', [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order->id ?? null,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                return $detalhe;
            });

            return response()->json([
                'success' => true,
                'periodo' => [
                    'inicio' => $startFilter->format('Y-m-d'),
                    'fim' => $endFilter->format('Y-m-d')
                ],
                'total_transacoes' => $transactions->count(),
                'detalhes' => $detalhes
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar relatório detalhado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ], 500);
        }
    }

    /* =====================================================================
     * FECHAMENTO DE CAIXA — PDF (3 páginas)
     * GET /cash/fechamento/pdf?period=day|week|month&date=YYYY-MM-DD
     * ===================================================================== */
    public function fechamentoCaixaPdf(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isCaixa()) {
            abort(403);
        }

        $period = in_array($request->get('period'), ['day','week','month']) ? $request->get('period') : 'day';
        $referenceDate = Carbon::parse($request->get('date', Carbon::now()->format('Y-m-d')));

        switch ($period) {
            case 'week':
                $startFilter  = $referenceDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $endFilter    = $referenceDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $periodLabel  = 'Semana: ' . $startFilter->format('d/m/Y') . ' a ' . $endFilter->format('d/m/Y');
                break;
            case 'month':
                $startFilter  = $referenceDate->copy()->startOfMonth()->startOfDay();
                $endFilter    = $referenceDate->copy()->endOfMonth()->endOfDay();
                $periodLabel  = $referenceDate->locale('pt_BR')->isoFormat('MMMM [de] YYYY');
                break;
            default: // day
                $startFilter  = $referenceDate->copy()->startOfDay();
                $endFilter    = $referenceDate->copy()->endOfDay();
                $periodLabel  = $referenceDate->format('d/m/Y');
                break;
        }

        $q = CashTransaction::with(['order.client', 'order.user', 'order.items', 'user'])
            ->whereBetween('transaction_date', [$startFilter, $endFilter])
            ->where('status', 'confirmado')
            ->orderBy('transaction_date');
        StoreHelper::applyStoreFilter($q);
        $transactions = $q->get();

        // Separar categorias
        $sangrias      = $transactions->filter(fn($t) => strtolower($t->category ?? '') === 'sangria');
        $suprimentosTx = $transactions->filter(fn($t) => in_array(strtolower($t->category ?? ''), ['suprimento', 'suprimentos']));
        $vendas        = $transactions->filter(fn($t) =>
            $t->type === 'entrada' &&
            !in_array(strtolower($t->category ?? ''), ['sangria', 'suprimento', 'suprimentos'])
        );

        // Totais de cashback
        $cashbackConcedido = $transactions->filter(fn($t) => str_contains(strtolower($t->category ?? ''), 'cashback_concedido'))->sum('amount');
        $cashbackUtilizado = $transactions->filter(fn($t) => str_contains(strtolower($t->category ?? ''), 'cashback_utilizado'))->sum('amount');

        $paymentTotals  = $this->normalizePaymentMethodTotals($vendas);
        $totalVendas    = $vendas->sum('amount');
        $totalSangria   = $sangrias->sum('amount');
        $totalSuprimentos = $suprimentosTx->sum('amount');
        $saldoCaixa     = $totalVendas - $totalSangria + $totalSuprimentos;

        // Entradas finalizadas / vendas debitadas / pagamento débito (categorias auxiliares)
        $entradasFinalizadas = $transactions->filter(fn($t) => str_contains(strtolower($t->category ?? ''), 'entrada_finalizada'))->sum('amount');
        $vendasDebitadas     = $transactions->filter(fn($t) => str_contains(strtolower($t->category ?? ''), 'venda_debitada'))->sum('amount');
        $pagamentoDebito     = $transactions->filter(fn($t) => str_contains(strtolower($t->category ?? ''), 'pagamento_debito'))->sum('amount');

        // Agrupamento por vendedor — preservando a ordem de inserção
        $vendasPorVendedor = [];
        foreach ($vendas as $t) {
            $vid   = $t->order?->user_id ?? $t->user_id ?? 0;
            $vnome = $t->order?->user?->name ?? $t->user?->name ?? $t->user_name ?? 'Sem vendedor';
            if (!isset($vendasPorVendedor[$vid])) {
                $vendasPorVendedor[$vid] = ['nome' => $vnome, 'total' => 0, 'transacoes' => []];
            }
            $vendasPorVendedor[$vid]['total'] += floatval($t->amount);
            $vendasPorVendedor[$vid]['transacoes'][] = $t;
        }

        $entryDetails = [];
        foreach ($vendas as $transaction) {
            $paymentMethods = $this->getTransactionPaymentMethods($transaction);
            $baseDate = $transaction->transaction_date
                ? Carbon::parse($transaction->transaction_date)->format('d/m/Y')
                : '-';
            $baseTime = $transaction->created_at
                ? Carbon::parse($transaction->created_at)->format('H:i')
                : '-';
            $orderNumber = $transaction->order_id
                ? str_pad((string) $transaction->order_id, 6, '0', STR_PAD_LEFT)
                : '-';
            $clientName = $transaction->order?->client?->name ?? '-';
            $sellerName = $transaction->order?->user?->name
                ?? $transaction->user?->name
                ?? $transaction->user_name
                ?? 'Sem vendedor';
            $origin = $this->buildCashTransactionOrigin($transaction);
            $description = $this->buildCashTransactionDescription($transaction);

            foreach ($paymentMethods as $method) {
                $entryDetails[] = [
                    'date' => $baseDate,
                    'time' => $baseTime,
                    'origin' => $origin,
                    'order_number' => $orderNumber,
                    'client' => $clientName,
                    'seller' => $sellerName,
                    'payment_method' => $this->formatCashPaymentMethodLabel($method['method'] ?? null),
                    'description' => $description,
                    'amount' => floatval($method['amount'] ?? 0),
                ];
            }
        }

        $cashMovementDetails = $transactions
            ->filter(fn($t) => in_array(strtolower($t->category ?? ''), ['sangria', 'suprimento', 'suprimentos']))
            ->values()
            ->map(function ($transaction) {
                return [
                    'date' => $transaction->transaction_date
                        ? Carbon::parse($transaction->transaction_date)->format('d/m/Y')
                        : '-',
                    'time' => $transaction->created_at
                        ? Carbon::parse($transaction->created_at)->format('H:i')
                        : '-',
                    'origin' => $this->buildCashTransactionOrigin($transaction),
                    'order_number' => $transaction->order_id
                        ? str_pad((string) $transaction->order_id, 6, '0', STR_PAD_LEFT)
                        : '-',
                    'seller' => $transaction->order?->user?->name
                        ?? $transaction->user?->name
                        ?? $transaction->user_name
                        ?? 'Sistema',
                    'description' => $this->buildCashTransactionDescription($transaction),
                    'amount' => floatval($transaction->amount ?? 0),
                ];
            })
            ->all();

        // Configurações da empresa
        $storeId = null;
        if (Auth::user()->isAdminLoja()) {
            $storeId = Auth::user()->getStoreIds()[0] ?? null;
        }
        $companySettings = \App\Models\CompanySetting::getSettings($storeId);

        $html = view('cash.pdf.fechamento', compact(
            'paymentTotals',
            'totalVendas',
            'totalSangria',
            'totalSuprimentos',
            'saldoCaixa',
            'vendasPorVendedor',
            'vendas',
            'entryDetails',
            'cashMovementDetails',
            'periodLabel',
            'period',
            'startFilter',
            'endFilter',
            'companySettings',
            'cashbackConcedido',
            'cashbackUtilizado',
            'entradasFinalizadas',
            'vendasDebitadas',
            'pagamentoDebito'
        ))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'fechamento-caixa-' . $period . '-' . $referenceDate->format('Y-m-d') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Normaliza payment_methods de uma coleção de transações em totais por linha da tabela.
     */
    private function normalizePaymentMethodTotals($transactions): array
    {
        $map = [
            'dinheiro'             => 'dinheiro',
            'cheque'               => 'cheque_boleto',
            'boleto'               => 'cheque_boleto',
            'cheque_boleto'        => 'cheque_boleto',
            'pix'                  => 'entradas',
            'entrada_dinheiro'     => 'entradas',
            'transferencia'        => 'transferencia',
            'transferencia_bancaria' => 'transferencia',
            'visa_credito'         => 'visa_credito',
            'credito_visa'         => 'visa_credito',
            'visa_debito'          => 'visa_debito',
            'debito_visa'          => 'visa_debito',
            'master_credito'       => 'master_credito',
            'mastercard_credito'   => 'master_credito',
            'credito_master'       => 'master_credito',
            'master_debito'        => 'master_debito',
            'mastercard_debito'    => 'master_debito',
            'debito_master'        => 'master_debito',
            'elo_credito'          => 'elo_credito',
            'credito_elo'          => 'elo_credito',
            'elo_debito'           => 'elo_debito',
            'debito_elo'           => 'elo_debito',
            'hiper'                => 'hiper',
            'hiper_credito'        => 'hiper',
            'hipercard'            => 'hiper',
            'amex'                 => 'amex',
            'amex_credito'         => 'amex',
            'american_express'     => 'amex',
            'credito_conta'        => 'outros_credito',
            'outros_credito'       => 'outros_credito',
            'cartao'               => 'outros_credito',
            'cartao_credito'       => 'outros_credito',
            'multiplo'             => 'outros_credito',
            'debito_conta'         => 'outros_debito',
            'outros_debito'        => 'outros_debito',
            'cartao_debito'        => 'outros_debito',
        ];

        $totals = array_fill_keys([
            'dinheiro','cheque_boleto','entradas','transferencia',
            'visa_credito','visa_debito','master_credito','master_debito',
            'elo_credito','elo_debito','hiper','amex',
            'outros_credito','outros_debito','cashback',
        ], 0.0);

        foreach ($transactions as $t) {
            $methods = $this->getTransactionPaymentMethods($t);

            foreach ($methods as $m) {
                $key    = strtolower($m['method'] ?? 'outros_credito');
                $amount = floatval($m['amount'] ?? 0);
                $group  = $map[$key] ?? 'outros_credito';
                if (array_key_exists($group, $totals)) {
                    $totals[$group] += $amount;
                }
            }
        }

        return $totals;
    }

    private function getTransactionPaymentMethods(CashTransaction $transaction): array
    {
        $methods = [];

        if (!empty($transaction->payment_methods) && is_array($transaction->payment_methods)) {
            $methods = $transaction->payment_methods;
        } elseif (!empty($transaction->payment_methods) && is_string($transaction->payment_methods)) {
            $decoded = json_decode($transaction->payment_methods, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $methods = $decoded;
            }
        } elseif (!empty($transaction->payment_method)) {
            $methods = [[
                'method' => $transaction->payment_method,
                'amount' => floatval($transaction->amount ?? 0),
            ]];
        }

        if (empty($methods)) {
            $methods = [[
                'method' => 'outros_credito',
                'amount' => floatval($transaction->amount ?? 0),
            ]];
        }

        return array_values(array_map(function ($method) use ($transaction) {
            if (!is_array($method)) {
                return [
                    'method' => $transaction->payment_method ?? 'outros_credito',
                    'amount' => floatval($transaction->amount ?? 0),
                ];
            }

            return [
                'method' => $method['method'] ?? ($transaction->payment_method ?? 'outros_credito'),
                'amount' => floatval($method['amount'] ?? $transaction->amount ?? 0),
            ];
        }, $methods));
    }

    private function formatCashPaymentMethodLabel(?string $method): string
    {
        $labels = [
            'dinheiro' => 'Dinheiro',
            'entrada_dinheiro' => 'Entrada em dinheiro',
            'pix' => 'PIX',
            'transferencia' => 'Transferencia',
            'transferencia_bancaria' => 'Transferencia bancaria',
            'boleto' => 'Boleto',
            'cheque' => 'Cheque',
            'cheque_boleto' => 'Cheque/Boleto',
            'visa_credito' => 'Visa credito',
            'visa_debito' => 'Visa debito',
            'master_credito' => 'Master credito',
            'master_debito' => 'Master debito',
            'mastercard_credito' => 'Master credito',
            'mastercard_debito' => 'Master debito',
            'elo_credito' => 'Elo credito',
            'elo_debito' => 'Elo debito',
            'amex' => 'Amex',
            'hiper' => 'Hipercard',
            'credito_conta' => 'Credito em conta',
            'debito_conta' => 'Debito em conta',
            'cartao' => 'Cartao',
            'cartao_credito' => 'Cartao credito',
            'cartao_debito' => 'Cartao debito',
            'multiplo' => 'Multiplos meios',
            'outros_credito' => 'Outros credito',
            'outros_debito' => 'Outros debito',
        ];

        $key = strtolower(trim((string) $method));

        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key ?: 'Nao informado'));
    }

    private function buildCashTransactionOrigin(CashTransaction $transaction): string
    {
        $category = strtolower(trim((string) $transaction->category));

        if ($transaction->order_id) {
            return 'Venda';
        }

        if (str_contains($category, 'sangria')) {
            return 'Sangria';
        }

        if (str_contains($category, 'suprimento')) {
            return 'Suprimento';
        }

        if (str_contains($category, 'cashback_concedido')) {
            return 'Cashback concedido';
        }

        if (str_contains($category, 'cashback_utilizado')) {
            return 'Cashback utilizado';
        }

        if (str_contains($category, 'entrada_finalizada')) {
            return 'Entrada finalizada';
        }

        if (str_contains($category, 'venda_debitada')) {
            return 'Venda debitada';
        }

        if (str_contains($category, 'pagamento_debito')) {
            return 'Pagamento de debito';
        }

        return $transaction->type === 'entrada' ? 'Entrada manual' : 'Saida manual';
    }

    private function buildCashTransactionDescription(CashTransaction $transaction): string
    {
        $parts = [];

        if (!empty($transaction->description)) {
            $parts[] = trim((string) $transaction->description);
        }

        if ($transaction->order && $transaction->order->items && $transaction->order->items->isNotEmpty()) {
            $itemNames = $transaction->order->items
                ->pluck('art_name')
                ->filter()
                ->take(2)
                ->values()
                ->all();

            if (!empty($itemNames)) {
                $remaining = max(0, $transaction->order->items->count() - count($itemNames));
                $parts[] = 'Itens: ' . implode(', ', $itemNames) . ($remaining > 0 ? ' +' . $remaining : '');
            }
        }

        if (!empty($transaction->notes)) {
            $notes = trim((string) $transaction->notes);
            if ($notes !== '' && !in_array($notes, $parts, true)) {
                $parts[] = 'Obs: ' . $notes;
            }
        }

        if (empty($parts) && !empty($transaction->category)) {
            $parts[] = ucfirst(str_replace('_', ' ', strtolower((string) $transaction->category)));
        }

        return implode(' | ', array_filter($parts)) ?: '-';
    }
}
