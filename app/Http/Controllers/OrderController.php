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
    use \App\Traits\ChecksSuperAdmin;

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // DEBUG: Log para rastrear problema de filtro por tenant
        \Log::info('OrderController::index DEBUG', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_tenant_id' => $user->tenant_id,
            'user_role' => $user->role,
            'isAdminGeral' => $user->isAdminGeral(),
            'isAdmin' => $user->isAdmin(),
        ]);
        
        // Super Admin (tenant_id === null) agora verá pedidos do sistema ou do tenant selecionado via StoreHelper
        // O bloco redundante foi removido para permitir a execução normal da query.

        $search = $request->get('search');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateType = $request->get('date_type', 'created'); // padrão: data de criação

        $query = Order::with([
            'client', 
            'status',
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
        
        // DEBUG: Log para ver resultados da query
        \Log::info('OrderController::index RESULTADOS', [
            'total_orders' => $orders->total(),
            'orders_tenant_ids' => $orders->pluck('tenant_id')->unique()->values()->toArray(),
            'orders_store_ids' => $orders->pluck('store_id')->unique()->values()->toArray(),
            'orders_ids' => $orders->pluck('id')->toArray(),
        ]);
        
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
            \App\Services\OrderService::addPayment($order, $validated, Auth::id());
            return redirect()->back()->with('success', 'Pagamento adicionado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar pagamento', ['error' => $e->getMessage(), 'order_id' => $id]);
            return redirect()->back()->with('error', 'Erro ao adicionar pagamento: ' . $e->getMessage());
        }
    }

    public function updatePayment(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'method' => 'required|in:pix,dinheiro,cartao,boleto,transferencia',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            \App\Services\OrderService::updatePayment($order, $validated, $request->input('method_id'));
            return redirect()->back()->with('success', 'Pagamento atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar pagamento', ['error' => $e->getMessage(), 'order_id' => $id]);
            return redirect()->back()->with('error', 'Erro ao atualizar pagamento: ' . $e->getMessage());
        }
    }

    public function deletePayment(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        try {
            \App\Services\OrderService::deletePayment($order, $request->input('method_id'));
            return redirect()->back()->with('success', 'Pagamento removido com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao remover pagamento', ['error' => $e->getMessage(), 'order_id' => $id]);
            return redirect()->back()->with('error', 'Erro ao remover pagamento: ' . $e->getMessage());
        }
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
            'changes' => \App\Services\OrderService::prepareChanges($order, $validated),
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
            $newOrder = \App\Services\OrderService::duplicate($originalOrder, Auth::id());

            return redirect()->route('orders.show', $newOrder->id)
                ->with('success', 'Pedido duplicado com sucesso! Novo pedido #' . str_pad($newOrder->id, 6, '0', STR_PAD_LEFT));

        } catch (\Exception $e) {
            \Log::error('Erro ao duplicar pedido:', [
                'order_id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()->with('error', 'Erro ao duplicar pedido: ' . $e->getMessage());
        }
    }
}
