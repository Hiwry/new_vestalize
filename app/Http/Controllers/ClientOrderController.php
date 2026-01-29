<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientOrderController extends Controller
{
    public function show($token): View
    {
        $order = Order::with([
            'client',
            'status',
            'user',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.files',
            'payments'
        ])->where('client_token', $token)->firstOrFail();

        // Calcular totais de pagamento
        // NUNCA usar $p->amount pois esse é o total do pedido, não o valor pago
        $totalPago = 0;
        foreach($order->payments as $p) {
            if($p->payment_methods && is_array($p->payment_methods) && count($p->payment_methods) > 0) {
                $sumFromMethods = 0;
                foreach($p->payment_methods as $method) {
                    $sumFromMethods += floatval($method['amount'] ?? 0);
                }
                // Se a soma dos payment_methods for igual ao total do pedido, pode ser um erro
                // Nesse caso, usar entry_amount se disponível
                if(abs($sumFromMethods - $order->total) < 0.01 && $p->entry_amount > 0) {
                    $totalPago += floatval($p->entry_amount);
                } else {
                    $totalPago += $sumFromMethods;
                }
            } else {
                // Fallback para pagamentos antigos sem payment_methods
                // Usar entry_amount, nunca amount (que é o total do pedido)
                $totalPago += floatval($p->entry_amount ?? 0);
            }
        }
        $restante = $order->total - $totalPago;
        
        $payment = (object) [
            'entry_amount' => $totalPago,
            'remaining_amount' => $restante
        ];
        
        // Buscar configurações da empresa da loja do pedido
        $storeId = $order->store_id;
        if (!$storeId) {
            $mainStore = \App\Models\Store::where('is_main', true)->first();
            $storeId = $mainStore ? $mainStore->id : null;
        }
        $companySettings = \App\Models\CompanySetting::getSettings($storeId);

        return view('client.order-show', compact('order', 'payment', 'companySettings'));
    }

    public function confirm(Request $request, $token): RedirectResponse
    {
        $order = Order::where('client_token', $token)->firstOrFail();
        
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'confirmation_notes' => 'nullable|string|max:500',
            'accept_terms' => 'required|accepted',
        ], [
            'accept_terms.required' => 'Você deve aceitar os termos e condições para confirmar o pedido.',
            'accept_terms.accepted' => 'Você deve aceitar os termos e condições para confirmar o pedido.',
        ]);

        // Atualizar dados do cliente se necessário
        $order->client->update([
            'name' => $validated['client_name'],
            'phone_primary' => $validated['client_phone'],
        ]);

        // Buscar status "Fila de Impressão" ou próximo status disponível
        $tenantId = $order->tenant_id;
        $filaImpressaoStatus = Status::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('name', 'Fila de Impressão')
                  ->orWhere('name', 'Fila Impressão')
                  ->orWhere('name', 'Impressão')
                  ->orWhere('name', 'Confirmado');
            })
            ->where('name', '!=', 'Pendente')
            ->orderByRaw("CASE 
                WHEN name = 'Fila de Impressão' THEN 1
                WHEN name = 'Impressão' THEN 2
                WHEN name = 'Confirmado' THEN 3
                ELSE 4 
            END")
            ->first();
        
        // Se não encontrar nenhum desses, buscar o primeiro status que NÃO seja Pendente ou Quando não assina
        if (!$filaImpressaoStatus) {
            $filaImpressaoStatus = Status::where('tenant_id', $tenantId)
                ->whereNotIn('name', ['Pendente', 'Quando não assina'])
                ->orderBy('position')
                ->first();
        }
        
        $oldStatus = $order->status;
        $newStatusId = $filaImpressaoStatus ? $filaImpressaoStatus->id : $order->status_id;

        // Marcar pedido como confirmado pelo cliente e mover para o próximo status
        $order->update([
            'client_confirmed' => true,
            // Forçar o timezone para São Paulo ao salvar
            'client_confirmed_at' => now('America/Sao_Paulo'),
            'client_confirmation_notes' => $validated['confirmation_notes'],
            'is_draft' => false, // Se o cliente confirmou, o pedido sai do rascunho
            'status_id' => $newStatusId,
        ]);

        // Registrar log de confirmação
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => null, // Cliente não tem user_id
            'user_name' => $validated['client_name'],
            'action' => 'CLIENT_CONFIRMED',
            'description' => 'Cliente confirmou o pedido via link de compartilhamento',
        ]);

        // Registrar log de mudança de status se aplicável
        if ($filaImpressaoStatus && $order->status_id != $oldStatus->id) {
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => null,
                'user_name' => 'Sistema',
                'action' => 'status_changed',
                'description' => "Status alterado de '{$oldStatus->name}' para 'Fila de Impressão' após confirmação do cliente",
                'old_value' => ['status' => $oldStatus->name],
                'new_value' => ['status' => 'Fila de Impressão'],
            ]);
        }

        return redirect()->back()->with('success', 'Pedido confirmado com sucesso! Obrigado pela confirmação.');
    }

    public function generateToken($id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        // Gerar token único se não existir
        if (!$order->client_token) {
            $order->update([
                'client_token' => \Str::random(32)
            ]);
        }

        $shareUrl = route('client.order.show', $order->client_token);
        
        return redirect()->back()->with('success', 'Link gerado com sucesso!')->with('share_url', $shareUrl);
    }
}
