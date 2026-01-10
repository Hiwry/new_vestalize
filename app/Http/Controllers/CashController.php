<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashController extends Controller
{
    public function __construct()
    {
        // Middleware será aplicado via rotas
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            $emptyCollection = collect([]);
            return view('cash.index', [
                'transactions' => $emptyCollection,
                'pendentes' => $emptyCollection,
                'confirmadas' => $emptyCollection,
                'canceladas' => $emptyCollection,
                'sangrias' => $emptyCollection,
                'totalPendentes' => 0,
                'totalConfirmadas' => 0,
                'totalCanceladas' => 0,
                'totalSangrias' => 0,
                'totalEntradas' => 0,
                'totalSaidas' => 0,
                'saldoPeriodo' => 0,
                'saldoAtual' => 0,
                'saldoGeral' => 0,
                'saldoPendente' => 0,
                'totalSaidasGeral' => 0,
                'startDate' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'endDate' => Carbon::now()->format('Y-m-d'),
                'type' => 'all',
                'isSuperAdmin' => true
            ]);
        }

        // Verificar se o usuário é administrador ou caixa
        if (!$user->isAdmin() && !$user->isCaixa()) {
            abort(403, 'Acesso negado. Apenas administradores e usuários de caixa podem acessar o caixa.');
        }
        
        // Se não houver filtro, mostrar TODAS as transações
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'all');

        $query = CashTransaction::with(['order', 'user']);

        // Aplicar filtro de data apenas se fornecido
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
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

        // Calcular totais por coluna
        $totalPendentes = $pendentes->where('type', 'entrada')->sum('amount');
        $totalConfirmadas = $confirmadas->where('type', 'entrada')->sum('amount') - $confirmadas->where('type', 'saida')->sum('amount');
        $totalCanceladas = $canceladas->sum('amount');
        $totalSangrias = $sangrias->sum('amount');

        // Calcular totais do período
        $totalEntradas = $transactions->where('type', 'entrada')->sum('amount');
        $totalSaidas = $transactions->where('type', 'saida')->sum('amount');
        $saldoPeriodo = $totalEntradas - $totalSaidas;
        
        // Calcular saldos gerais
        // CashTransaction::getSaldoAtual() and other methods might not be multi-tenant aware yet if they don't filter by user stores.
        // However, since we are only fixing the isolation for Super Admin visualization for now, this is enough.
        $saldoAtual = CashTransaction::getSaldoAtual(); // Apenas confirmadas
        $saldoGeral = CashTransaction::getSaldoGeral(); // Tudo
        $saldoPendente = CashTransaction::getSaldoPendente(); // Pendentes
        $totalSaidasGeral = CashTransaction::getTotalSaidas(); // Todas as saídas

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
            'isSuperAdmin'
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
     * Relatório simplificado (resumo diário)
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

            $date = $request->get('date', Carbon::now()->format('Y-m-d'));
            
            // Carregar relacionamentos necessários, incluindo order.user
            $transactions = CashTransaction::with(['order.user', 'order.items', 'user'])
                ->whereDate('transaction_date', $date)
                ->where('status', 'confirmado')
                ->get();

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
                'date' => $date,
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
                'date' => $request->get('date')
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

            $transactions = CashTransaction::with(['order.client', 'order.items', 'order.user', 'user'])
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where('status', 'confirmado')
                ->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

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
                    'inicio' => $startDate,
                    'fim' => $endDate
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
}
