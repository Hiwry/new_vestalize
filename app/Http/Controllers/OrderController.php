<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Models\Client;
use App\Models\Payment;
use App\Models\CashTransaction;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateType = $request->get('date_type', 'created'); // padrão: data de criação

        $query = Order::with([
            'client', 
            'items.sublimations.location', 
            'items.sublimations', 
            'user', 
            'editRequests'
        ])
            ->notDrafts() // Usar scope otimizado
            ->where('is_pdv', false); // Excluir vendas do PDV

        // Aplicar filtro de loja
        StoreHelper::applyStoreFilter($query);

        // Se for vendedor, mostrar apenas os pedidos que ele criou
        if (Auth::user()->isVendedor()) {
            $query->byUser(Auth::id());
        }

        // Busca usando scope otimizado
        if ($search) {
            $query->search($search);
        }

        // Filtro por status usando scope
        if ($status) {
            $query->byStatus($status);
        }

        // Filtro por data - escolher entre data de criação ou entrega
        if ($startDate && $endDate) {
            if ($dateType === 'delivery') {
                // Filtrar por data de entrega
                $query->whereBetween('delivery_date', [$startDate, $endDate]);
            } else {
                // Filtrar por data de criação (padrão)
                $query->dateRange($startDate, $endDate);
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = Status::orderBy('position')->get();

        return view('orders.index', compact('orders', 'statuses', 'search', 'status', 'startDate', 'endDate', 'dateType'));
    }

    public function show($id)
    {
        $order = Order::with([
            'client',
            'user',
            'store',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.sublimations.files',
            'items.files',
            'comments.user',
            'logs.user',
            'deliveryRequests',
            'payments',
            'cashTransactions.user',
            'editHistory',
            'editApprovedBy',
            'cancellations',
            'editRequests'
        ])->findOrFail($id);

        // Pagamento já está carregado via relacionamento
        $payment = $order->payments->first();
        
        // Transações já carregadas via relacionamento
        $cashTransactions = $order->cashTransactions;

        return view('orders.show', compact('order', 'payment', 'cashTransactions'));
    }

    public function addPayment(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'method' => 'required|in:pix,dinheiro,cartao,boleto,transferencia',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            \DB::transaction(function () use ($order, $validated, $id) {
                // Buscar pagamento existente ou criar novo
                $payment = Payment::where('order_id', $id)->first();
                
                if ($payment) {
                    // Atualizar pagamento existente
                    $newEntryAmount = $payment->entry_amount + $validated['amount'];
                    $newRemainingAmount = $payment->remaining_amount - $validated['amount'];
                    
                    // Adicionar novo método de pagamento ao array
                    $paymentMethods = $payment->payment_methods ?? [];
                    $paymentMethods[] = [
                        'id' => time() . rand(1000, 9999),
                        'method' => $validated['method'],
                        'amount' => $validated['amount'],
                        'date' => now()->format('Y-m-d H:i:s'),
                    ];
                    
                    $payment->update([
                        'entry_amount' => $newEntryAmount,
                        'remaining_amount' => $newRemainingAmount,
                        'payment_methods' => $paymentMethods,
                        'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                    ]);
                } else {
                    // Criar novo pagamento
                    $payment = Payment::create([
                        'order_id' => $id,
                        'method' => $validated['method'],
                        'payment_method' => $validated['method'],
                        'payment_methods' => [[
                            'id' => time() . rand(1000, 9999),
                            'method' => $validated['method'],
                            'amount' => $validated['amount'],
                            'date' => now()->format('Y-m-d H:i:s'),
                        ]],
                        'amount' => $order->total,
                        'entry_amount' => $validated['amount'],
                        'remaining_amount' => $order->total - $validated['amount'],
                        'status' => $validated['amount'] >= $order->total ? 'pago' : 'pendente',
                        'entry_date' => now(),
                        'payment_date' => now(),
                    ]);
                }

                // Criar transação no caixa como "pendente" até o pedido ser entregue
                CashTransaction::create([
                    'type' => 'entrada',
                    'category' => 'Venda',
                    'description' => "Pagamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - " . $order->client->name,
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['method'],
                    'status' => 'pendente',
                    'transaction_date' => now(),
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name,
                    'notes' => $validated['notes'],
                ]);
            });

            return redirect()->back()->with('success', 'Pagamento adicionado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar pagamento', ['error' => $e->getMessage(), 'order_id' => $id]);
            return redirect()->back()->with('error', 'Erro ao adicionar pagamento: ' . $e->getMessage());
        }
    }

    public function updatePayment(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $payment = Payment::where('order_id', $id)->firstOrFail();
        
        $validated = $request->validate([
            'method' => 'required|in:pix,dinheiro,cartao,boleto,transferencia',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $methodId = $request->input('method_id');

        // Se tiver method_id, atualizar apenas esse método específico
        if ($methodId && $payment->payment_methods && is_array($payment->payment_methods)) {
            $paymentMethods = $payment->payment_methods;
            $oldAmount = 0;
            
            // Encontrar e atualizar o método específico
            foreach ($paymentMethods as $key => $method) {
                if ($method['id'] == $methodId) {
                    $oldAmount = $method['amount'];
                    $paymentMethods[$key]['method'] = $validated['method'];
                    $paymentMethods[$key]['amount'] = $validated['amount'];
                    break;
                }
            }
            
            // Recalcular totais
            $amountDifference = $validated['amount'] - $oldAmount;
            $newEntryAmount = $payment->entry_amount + $amountDifference;
            $newRemainingAmount = $payment->remaining_amount - $amountDifference;
            
            $payment->update([
                'entry_amount' => $newEntryAmount,
                'remaining_amount' => $newRemainingAmount,
                'payment_methods' => $paymentMethods,
                'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                'notes' => $validated['notes'],
            ]);
            
            // Atualizar transação no caixa correspondente
            $cashTransaction = CashTransaction::where('order_id', $order->id)
                ->where('type', 'entrada')
                ->where('amount', $oldAmount)
                ->first();

            if ($cashTransaction) {
                $cashTransaction->update([
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['method'],
                    'notes' => $validated['notes'],
                ]);
            }
        } else {
            // Fallback para pagamentos antigos sem payment_methods
            $payment->update([
                'method' => $validated['method'],
                'entry_amount' => $validated['amount'],
                'remaining_amount' => $order->total - $validated['amount'],
                'status' => $validated['amount'] >= $order->total ? 'pago' : 'parcial',
                'notes' => $validated['notes'],
            ]);

            // Atualizar transação no caixa (buscar a mais recente)
            $cashTransaction = CashTransaction::where('order_id', $order->id)
                ->where('type', 'entrada')
                ->latest()
                ->first();

            if ($cashTransaction) {
                $cashTransaction->update([
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['method'],
                    'notes' => $validated['notes'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function deletePayment(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $payment = Payment::where('order_id', $id)->firstOrFail();
        
        $paymentId = $request->input('payment_id');
        $methodId = $request->input('method_id');
        
        // Se tiver method_id, remover apenas esse método específico
        if ($methodId && $payment->payment_methods && is_array($payment->payment_methods)) {
            $paymentMethods = $payment->payment_methods;
            $removedAmount = 0;
            
            // Encontrar e remover o método específico
            foreach ($paymentMethods as $key => $method) {
                if ($method['id'] == $methodId) {
                    $removedAmount = $method['amount'];
                    unset($paymentMethods[$key]);
                    break;
                }
            }
            
            // Reindexar array
            $paymentMethods = array_values($paymentMethods);
            
            // Se ainda houver métodos, atualizar o pagamento
            if (!empty($paymentMethods)) {
                $newEntryAmount = $payment->entry_amount - $removedAmount;
                $newRemainingAmount = $payment->remaining_amount + $removedAmount;
                
                $payment->update([
                    'entry_amount' => $newEntryAmount,
                    'remaining_amount' => $newRemainingAmount,
                    'payment_methods' => $paymentMethods,
                    'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
                ]);
                
                // Atualizar ou deletar transação de caixa correspondente
                $cashTransaction = CashTransaction::where('order_id', $order->id)
                    ->where('type', 'entrada')
                    ->where('amount', $removedAmount)
                    ->first();
                
                if ($cashTransaction) {
                    $cashTransaction->delete();
                }
                
                return redirect()->back()->with('success', 'Pagamento removido com sucesso!');
            }
        }
        
        // Se não houver mais métodos ou não tiver method_id, deletar tudo
        CashTransaction::where('order_id', $order->id)->where('type', 'entrada')->delete();
        $payment->delete();

        return redirect()->back()->with('success', 'Todos os pagamentos removidos com sucesso!');
    }

    public function downloadClientReceipt($id)
    {
        $order = Order::findOrFail($id);
        
        // Se for venda do PDV, redirecionar para a nota de venda
        if ($order->is_pdv) {
            return redirect()->route('pdv.sale-receipt', $order->id);
        }
        
        $order = Order::with([
            'client',
            'status',
            'store',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.files',
            'payments',
            'editHistory',
            'editApprovedBy'
        ])->findOrFail($id);

        $payment = Payment::where('order_id', $id)->first();
        
        // Log para debug
        \Log::info("Gerando PDF da nota do pedido", [
            'order_id' => $order->id,
            'store_id' => $order->store_id,
            'store_name' => $order->store ? $order->store->name : 'N/A'
        ]);
        
        // Se o pedido não tem store_id, tentar buscar da loja principal
        $storeId = $order->store_id;
        if (!$storeId) {
            $mainStore = \App\Models\Store::where('is_main', true)->first();
            $storeId = $mainStore ? $mainStore->id : null;
            \Log::warning("Pedido #{$order->id} não tem store_id, usando loja principal", [
                'order_id' => $order->id,
                'main_store_id' => $storeId
            ]);
        }
        
        // Buscar configurações da loja do pedido
        $companySettings = \App\Models\CompanySetting::getSettings($storeId);
        
        \Log::info("Configurações da empresa carregadas", [
            'store_id' => $order->store_id,
            'company_name' => $companySettings->company_name,
            'logo_path' => $companySettings->logo_path,
            'company_phone' => $companySettings->company_phone,
            'company_email' => $companySettings->company_email
        ]);

        try {
            // Gerar HTML da view
            $html = view('orders.pdf.client-receipt', compact('order', 'payment', 'companySettings'))->render();
            
            // Configurar DomPDF
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isImageEnabled', true); // HABILITAR imagens
            $options->set('chroot', public_path()); // Permitir acesso a imagens locais
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $filename = 'Nota_Pedido_' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '_' . now()->format('Y-m-d') . '.pdf';
            
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateShareLink($id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        // Gerar token único se não existir
        if (!$order->client_token) {
            $order->update([
                'client_token' => \Str::random(32)
            ]);
        }

        $shareUrl = route('client.order.show', $order->client_token);
        
        return redirect()->back()->with('success', 'Link de compartilhamento gerado com sucesso!')->with('share_url', $shareUrl);
    }

    public function getPayment($orderId, $paymentId)
    {
        $payment = Payment::where('order_id', $orderId)->findOrFail($paymentId);
        
        return response()->json([
            'id' => $payment->id,
            'method' => $payment->method,
            'entry_amount' => $payment->entry_amount,
            'notes' => $payment->notes,
            'payment_methods' => $payment->payment_methods,
        ]);
    }

    public function requestEdit(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->update([
            'is_editing' => true,
            'edit_requested_at' => now(),
            'edit_notes' => $validated['reason'],
            'edit_status' => 'requested',
        ]);

        // Registrar no histórico
        \App\Models\OrderEditHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'edit_requested',
            'description' => 'Solicitação de edição do pedido',
            'changes' => ['reason' => $validated['reason']],
        ]);

        return response()->json(['success' => true]);
    }

    public function approveEdit(Request $request, $id)
    {
        // Verificar se o usuário é administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem aprovar edições.'
            ], 403);
        }

        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $order->update([
            'edit_status' => 'approved',
            'edit_approved_at' => now(),
            'edit_approved_by' => auth()->id(),
        ]);

        // Registrar no histórico
        \App\Models\OrderEditHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'edit_approved',
            'description' => 'Edição aprovada pela produção',
            'changes' => ['notes' => $validated['notes'] ?? ''],
        ]);

        return response()->json(['success' => true]);
    }

    public function rejectEdit(Request $request, $id)
    {
        // Verificar se o usuário é administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem rejeitar edições.'
            ], 403);
        }

        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->update([
            'edit_status' => 'rejected',
            'edit_rejected_at' => now(),
            'edit_rejection_reason' => $validated['reason'],
            'is_editing' => false,
        ]);

        // Registrar no histórico
        \App\Models\OrderEditHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'edit_rejected',
            'description' => 'Edição rejeitada pela produção',
            'changes' => ['reason' => $validated['reason']],
        ]);

        return response()->json(['success' => true]);
    }

    public function editOrder($id)
    {
        $order = Order::with([
            'client',
            'status',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.files',
            'editHistory',
            'editApprovedBy'
        ])->findOrFail($id);

        if ($order->edit_status !== 'approved') {
            return redirect()->back()->with('error', 'Este pedido não está aprovado para edição.');
        }

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Verificar se o pedido pode ser editado
        if ($order->is_cancelled) {
            return redirect()->back()->with('error', 'Pedidos cancelados não podem ser editados.');
        }

        $validated = $request->validate([
            'selected_steps' => 'required|array|min:1',
            'selected_steps.*' => 'in:client,items,personalization,payment',
            'edit_reason' => 'required|string|max:1000',
            'client_name' => 'required_if:selected_steps,client|string|max:255',
            'client_phone_primary' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'client_cpf_cnpj' => 'nullable|string|max:20',
            'client_address' => 'nullable|string|max:500',
            'items' => 'required_if:selected_steps,items|array',
            'items.*.id' => 'required_with:items|exists:order_items,id',
            'items.*.print_type' => 'required_with:items|string|max:255',
            'items.*.art_name' => 'nullable|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.fabric' => 'required_with:items|string|max:255',
            'items.*.color' => 'required_with:items|string|max:100',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'contract_type' => 'required_if:selected_steps,personalization|in:costura,personalizacao,ambos',
            'seller' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|date',
            'subtotal' => 'required_if:selected_steps,payment|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'total' => 'required_if:selected_steps,payment|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Criar solicitação de edição
        $editRequest = \App\Models\OrderEditRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $validated['edit_reason'],
            'changes' => $this->prepareChanges($order, $validated),
            'status' => 'pending'
        ]);

        // Atualizar pedido
        $order->update([
            'has_pending_edit' => true
        ]);

        // Criar log
        \App\Models\OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'edit_requested',
            'description' => 'Solicitação de edição enviada',
            'details' => json_encode([
                'reason' => $validated['edit_reason'],
                'steps' => $validated['selected_steps'],
                'edit_request_id' => $editRequest->id
            ])
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Solicitação de edição enviada com sucesso! Aguarde a aprovação do administrador.');
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->edit_status !== 'approved') {
            return redirect()->back()->with('error', 'Este pedido não está aprovado para edição.');
        }

        $validated = $request->validate([
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'total' => 'required|numeric|min:0',
        ]);

        // Capturar valores antigos para o log
        $oldValues = [
            'delivery_date' => $order->delivery_date,
            'notes' => $order->notes,
            'total' => $order->total,
        ];

        $newValues = [
            'delivery_date' => $validated['delivery_date'],
            'notes' => $validated['notes'],
            'total' => $validated['total'],
        ];

        // Calcular mudanças
        $changes = [];
        foreach ($oldValues as $field => $oldValue) {
            if ($oldValue != $newValues[$field]) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValues[$field]
                ];
            }
        }

        // Atualizar pedido
        $order->update([
            'delivery_date' => $validated['delivery_date'],
            'notes' => $validated['notes'],
            'total' => $validated['total'],
            'is_modified' => true,
            'last_modified_at' => now(),
            'edit_status' => 'completed',
            'edit_completed_at' => now(),
        ]);

        // Registrar mudanças no histórico
        if (!empty($changes)) {
            \App\Models\OrderEditHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'order_modified',
                'description' => 'Pedido modificado após aprovação',
                'changes' => $changes,
            ]);
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Pedido atualizado com sucesso!');
    }

    private function prepareChanges($order, $validated)
    {
        $changes = [];
        $selectedSteps = $validated['selected_steps'];

        // Dados do Cliente
        if (in_array('client', $selectedSteps)) {
            $clientChanges = [];
            $client = $order->client;
            
            if ($client->name !== $validated['client_name']) {
                $clientChanges['name'] = ['old' => $client->name, 'new' => $validated['client_name']];
            }
            if ($client->phone_primary !== $validated['client_phone_primary']) {
                $clientChanges['phone_primary'] = ['old' => $client->phone_primary, 'new' => $validated['client_phone_primary']];
            }
            if ($client->email !== $validated['client_email']) {
                $clientChanges['email'] = ['old' => $client->email, 'new' => $validated['client_email']];
            }
            if ($client->cpf_cnpj !== $validated['client_cpf_cnpj']) {
                $clientChanges['cpf_cnpj'] = ['old' => $client->cpf_cnpj, 'new' => $validated['client_cpf_cnpj']];
            }
            if ($client->address !== $validated['client_address']) {
                $clientChanges['address'] = ['old' => $client->address, 'new' => $validated['client_address']];
            }
            
            if (!empty($clientChanges)) {
                $changes['client'] = $clientChanges;
            }
        }

        // Itens do Pedido
        if (in_array('items', $selectedSteps) && isset($validated['items'])) {
            $itemsChanges = [];
            foreach ($validated['items'] as $index => $itemData) {
                $item = $order->items->find($itemData['id']);
                if ($item) {
                    $itemChanges = [];
                    if ($item->print_type !== $itemData['print_type']) {
                        $itemChanges['print_type'] = ['old' => $item->print_type, 'new' => $itemData['print_type']];
                    }
                    if ($item->art_name !== $itemData['art_name']) {
                        $itemChanges['art_name'] = ['old' => $item->art_name, 'new' => $itemData['art_name']];
                    }
                    if ($item->quantity != $itemData['quantity']) {
                        $itemChanges['quantity'] = ['old' => $item->quantity, 'new' => $itemData['quantity']];
                    }
                    if ($item->fabric !== $itemData['fabric']) {
                        $itemChanges['fabric'] = ['old' => $item->fabric, 'new' => $itemData['fabric']];
                    }
                    if ($item->color !== $itemData['color']) {
                        $itemChanges['color'] = ['old' => $item->color, 'new' => $itemData['color']];
                    }
                    if ($item->unit_price != $itemData['unit_price']) {
                        $itemChanges['unit_price'] = ['old' => $item->unit_price, 'new' => $itemData['unit_price']];
                    }
                    
                    if (!empty($itemChanges)) {
                        $itemsChanges[$item->id] = $itemChanges;
                    }
                }
            }
            if (!empty($itemsChanges)) {
                $changes['items'] = $itemsChanges;
            }
        }

        // Personalização
        if (in_array('personalization', $selectedSteps)) {
            $personalizationChanges = [];
            if ($order->contract_type !== $validated['contract_type']) {
                $personalizationChanges['contract_type'] = ['old' => $order->contract_type, 'new' => $validated['contract_type']];
            }
            if ($order->seller !== $validated['seller']) {
                $personalizationChanges['seller'] = ['old' => $order->seller, 'new' => $validated['seller']];
            }
            if (!empty($personalizationChanges)) {
                $changes['personalization'] = $personalizationChanges;
            }
        }

        // Pagamento e Valores
        if (in_array('payment', $selectedSteps)) {
            $paymentChanges = [];
            if ($order->delivery_date !== $validated['delivery_date']) {
                $paymentChanges['delivery_date'] = ['old' => $order->delivery_date, 'new' => $validated['delivery_date']];
            }
            if ($order->subtotal != $validated['subtotal']) {
                $paymentChanges['subtotal'] = ['old' => $order->subtotal, 'new' => $validated['subtotal']];
            }
            if ($order->discount != $validated['discount']) {
                $paymentChanges['discount'] = ['old' => $order->discount, 'new' => $validated['discount']];
            }
            if ($order->delivery_fee != $validated['delivery_fee']) {
                $paymentChanges['delivery_fee'] = ['old' => $order->delivery_fee, 'new' => $validated['delivery_fee']];
            }
            if ($order->total != $validated['total']) {
                $paymentChanges['total'] = ['old' => $order->total, 'new' => $validated['total']];
            }
            if (!empty($paymentChanges)) {
                $changes['payment'] = $paymentChanges;
            }
        }

        // Observações
        if ($order->notes !== $validated['notes']) {
            $changes['notes'] = ['old' => $order->notes, 'new' => $validated['notes']];
        }

        return $changes;
    }

    public function updateDeliveryDate(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'delivery_date' => 'required|date',
        ]);
        
        $oldDate = $order->delivery_date;
        $order->update([
            'delivery_date' => $validated['delivery_date']
        ]);
        
        // Criar log
        \App\Models\OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => 'delivery_date_updated',
            'description' => 'Data de entrega alterada de ' . 
                ($oldDate ? \Carbon\Carbon::parse($oldDate)->format('d/m/Y') : 'não definida') . 
                ' para ' . \Carbon\Carbon::parse($validated['delivery_date'])->format('d/m/Y'),
        ]);
        
        return redirect()->back()->with('success', 'Data de entrega atualizada com sucesso!');
    }

    /**
     * Duplicar pedido existente
     */
    public function duplicate($id): RedirectResponse
    {
        $originalOrder = Order::with([
            'client',
            'items.sublimations',
            'items.files',
        ])->findOrFail($id);

        // Verificar permissão
        $user = Auth::user();
        if ($user->isVendedor() && $originalOrder->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para duplicar este pedido.');
        }

        try {
            \DB::beginTransaction();

            // Buscar status inicial (primeiro status)
            $initialStatus = Status::orderBy('position')->first();

            // Criar cópia do pedido
            $newOrder = Order::create([
                'client_id' => $originalOrder->client_id,
                'user_id' => Auth::id(),
                'status_id' => $initialStatus?->id ?? $originalOrder->status_id,
                'store_id' => $originalOrder->store_id,
                'seller' => $originalOrder->seller,
                'contract_type' => $originalOrder->contract_type,
                'delivery_date' => now()->addDays(15), // 15 dias a partir de hoje
                'subtotal' => $originalOrder->subtotal,
                'discount' => $originalOrder->discount,
                'delivery_fee' => $originalOrder->delivery_fee,
                'total' => $originalOrder->total,
                'notes' => $originalOrder->notes,
                'is_draft' => true, // Criar como rascunho
            ]);

            // Duplicar itens
            foreach ($originalOrder->items as $item) {
                $newItem = $newOrder->items()->create([
                    'print_type' => $item->print_type,
                    'art_name' => $item->art_name,
                    'quantity' => $item->quantity,
                    'fabric' => $item->fabric,
                    'color' => $item->color,
                    'collar' => $item->collar,
                    'model' => $item->model,
                    'detail' => $item->detail,
                    'sizes' => $item->sizes,
                    'unit_price' => $item->unit_price,
                    'cover_image' => $item->cover_image,
                ]);

                // Duplicar personalizações/sublimações
                foreach ($item->sublimations as $sub) {
                    $newItem->sublimations()->create([
                        'size_id' => $sub->size_id,
                        'size_name' => $sub->size_name,
                        'location_id' => $sub->location_id,
                        'location_name' => $sub->location_name,
                        'quantity' => $sub->quantity,
                        'unit_price' => $sub->unit_price,
                        'discount_percent' => $sub->discount_percent,
                        'final_price' => $sub->final_price,
                        'application_type' => $sub->application_type,
                        'color_count' => $sub->color_count,
                        'has_neon' => $sub->has_neon,
                    ]);
                }
            }

            // Criar log
            \App\Models\OrderLog::create([
                'order_id' => $newOrder->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'order_created',
                'description' => 'Pedido criado por duplicação do pedido #' . str_pad($originalOrder->id, 6, '0', STR_PAD_LEFT),
            ]);

            \DB::commit();

            return redirect()->route('orders.show', $newOrder->id)
                ->with('success', 'Pedido duplicado com sucesso! Novo pedido #' . str_pad($newOrder->id, 6, '0', STR_PAD_LEFT));

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erro ao duplicar pedido:', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()->with('error', 'Erro ao duplicar pedido: ' . $e->getMessage());
        }
    }
}
