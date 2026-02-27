<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\MarketplaceCreditWallet;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\MarketplaceReview;
use App\Models\Marketplace\MarketplaceService;
use App\Models\Marketplace\MarketplaceTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Meus pedidos (como comprador ou designer)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'buying');

        // Pedidos como comprador
        $buyingOrders = MarketplaceOrder::with(['designer.user'])
            ->where('buyer_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'buying_page');

        // Pedidos como designer (se tiver perfil)
        $sellingOrders = collect();
        $designer = \App\Models\Marketplace\DesignerProfile::where('user_id', $user->id)->first();
        if ($designer) {
            $sellingOrders = MarketplaceOrder::with(['buyer'])
                ->where('designer_id', $designer->id)
                ->latest()
                ->paginate(10, ['*'], 'selling_page');
        }

        $wallet = MarketplaceCreditWallet::getOrCreate($user->id);

        return view('marketplace.orders.index', compact(
            'buyingOrders', 'sellingOrders', 'designer', 'wallet', 'tab'
        ));
    }

    /**
     * Detalhe do pedido
     */
    public function show(int $id)
    {
        $user  = Auth::user();
        $order = MarketplaceOrder::with(['buyer', 'designer.user', 'review'])
            ->findOrFail($id);

        // Apenas comprador ou designer podem ver
        $designer = \App\Models\Marketplace\DesignerProfile::where('user_id', $user->id)->first();
        $isDesigner = $designer && $order->designer_id === $designer->id;

        if ($order->buyer_id !== $user->id && !$isDesigner) {
            abort(403);
        }

        $orderableModel = $order->orderable_type === 'service'
            ? MarketplaceService::with('images')->find($order->orderable_id)
            : MarketplaceTool::with('images')->find($order->orderable_id);

        return view('marketplace.orders.show', compact('order', 'orderableModel', 'isDesigner'));
    }

    /**
     * Cria um pedido (compra serviço ou ferramenta)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'                => 'required|in:service,tool',
            'id'                  => 'required|integer',
            'buyer_instructions'  => 'nullable|string|max:1000',
        ]);

        $user   = Auth::user();
        $wallet = MarketplaceCreditWallet::getOrCreate($user->id);

        DB::beginTransaction();
        try {
            if ($validated['type'] === 'service') {
                $item = MarketplaceService::with('designer')->active()->findOrFail($validated['id']);
                $designerId = $item->designer_profile_id;
            } else {
                $item = MarketplaceTool::active()->findOrFail($validated['id']);
                $designerId = null; // ferramentas não têm designer
            }

            $priceCredits = $item->price_credits;

            // Verifica saldo
            if (!$wallet->hasBalance($priceCredits)) {
                return back()->with('error', "Saldo insuficiente. Você tem {$wallet->balance} créditos, mas o item custa {$priceCredits}.");
            }

            // Calcula créditos ao designer (menos comissão de 15%)
            $creditsToDesigner = $designerId ? (int)($priceCredits * 0.85) : null;

            // Cria o pedido
            $order = MarketplaceOrder::create([
                'order_number'       => MarketplaceOrder::generateOrderNumber(),
                'buyer_id'           => $user->id,
                'orderable_type'     => $validated['type'],
                'orderable_id'       => $item->id,
                'designer_id'        => $designerId,
                'price_credits'      => $priceCredits,
                'credits_to_designer' => $creditsToDesigner,
                'status'             => 'in_progress',
                'buyer_instructions' => $validated['buyer_instructions'] ?? null,
                'deadline_at'        => $validated['type'] === 'service'
                    ? now()->addWeekdays($item->delivery_days)
                    : null,
            ]);

            // Debita créditos do comprador
            $wallet->debit($priceCredits, "Pedido #{$order->order_number}", [
                'reference_type' => 'marketplace_order',
                'reference_id'   => $order->id,
            ]);

            // Atualiza contador de vendas
            if ($validated['type'] === 'service') {
                $item->increment('total_orders');
            } else {
                $item->increment('total_downloads');
            }

            DB::commit();

            return redirect()->route('marketplace.orders.show', $order->id)
                ->with('success', "✅ Pedido #{$order->order_number} criado com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Designer marca pedido como entregue
     */
    public function deliver(Request $request, int $id)
    {
        $validated = $request->validate([
            'delivery_message' => 'required|string|max:1000',
            'delivery_file'    => 'nullable|file|max:51200', // 50MB
        ]);

        $user     = Auth::user();
        $designer = \App\Models\Marketplace\DesignerProfile::where('user_id', $user->id)->firstOrFail();
        $order    = MarketplaceOrder::where('designer_id', $designer->id)->findOrFail($id);

        if (!in_array($order->status, ['in_progress', 'revision_requested'])) {
            return back()->with('error', 'Este pedido não pode ser marcado como entregue.');
        }

        $filePath = null;
        if ($request->hasFile('delivery_file')) {
            $filePath = $request->file('delivery_file')
                ->store('marketplace/deliveries', 'public');
        }

        $order->update([
            'status'           => 'delivered',
            'delivery_message' => $validated['delivery_message'],
            'delivery_file'    => $filePath,
            'delivered_at'     => now(),
        ]);

        return redirect()->route('marketplace.orders.show', $order->id)
            ->with('success', 'Entrega confirmada! Aguardando aprovação do comprador.');
    }

    /**
     * Comprador confirma recebimento
     */
    public function complete(int $id)
    {
        $user  = Auth::user();
        $order = MarketplaceOrder::where('buyer_id', $user->id)
            ->where('status', 'delivered')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $order->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            // Credita o designer (se for serviço)
            if ($order->designer_id && $order->credits_to_designer) {
                $designerProfile = \App\Models\Marketplace\DesignerProfile::find($order->designer_id);
                if ($designerProfile) {
                    $designerWallet = MarketplaceCreditWallet::getOrCreate($designerProfile->user_id);
                    $designerWallet->credit(
                        $order->credits_to_designer,
                        'earn',
                        "Pedido #{$order->order_number} concluído",
                        [
                            'reference_type' => 'marketplace_order',
                            'reference_id'   => $order->id,
                        ]
                    );
                    $designerProfile->increment('total_sales');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao confirmar pedido.');
        }

        return redirect()->route('marketplace.orders.show', $order->id)
            ->with('success', '✅ Pedido concluído! O designer recebeu seus créditos.');
    }

    /**
     * Comprador solicita revisão
     */
    public function requestRevision(Request $request, int $id)
    {
        $validated = $request->validate([
            'revision_note' => 'required|string|max:500',
        ]);

        $user  = Auth::user();
        $order = MarketplaceOrder::where('buyer_id', $user->id)
            ->where('status', 'delivered')
            ->findOrFail($id);

        $order->update([
            'status'           => 'revision_requested',
            'delivery_message' => $order->delivery_message . "\n\n📝 Revisão solicitada: " . $validated['revision_note'],
        ]);

        return redirect()->route('marketplace.orders.show', $order->id)
            ->with('success', 'Revisão solicitada ao designer.');
    }

    /**
     * Avalia o pedido
     */
    public function review(Request $request, int $id)
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user  = Auth::user();
        $order = MarketplaceOrder::where('buyer_id', $user->id)
            ->where('status', 'completed')
            ->findOrFail($id);

        if ($order->review) {
            return back()->with('error', 'Você já avaliou este pedido.');
        }

        if (!$order->designer_id) {
            return back()->with('error', 'Ferramentas não suportam avaliações.');
        }

        $designer = \App\Models\Marketplace\DesignerProfile::find($order->designer_id);

        MarketplaceReview::create([
            'marketplace_order_id' => $order->id,
            'reviewer_id'          => $user->id,
            'reviewee_id'          => $designer->user_id,
            'rating'               => $validated['rating'],
            'comment'              => $validated['comment'] ?? null,
        ]);

        // Recalcula rating do designer
        $designer?->recalculateRating();

        return redirect()->route('marketplace.orders.show', $order->id)
            ->with('success', '⭐ Avaliação enviada! Obrigado pelo feedback.');
    }
}
