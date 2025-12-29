<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\Store;

class FixOrderStoreIds extends Command
{
    protected $signature = 'orders:fix-store-ids {order_id?}';
    protected $description = 'Corrige o store_id dos pedidos baseado no vendedor que os criou';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        if ($orderId) {
            $orders = Order::where('id', $orderId)->get();
        } else {
            $orders = Order::whereNull('store_id')
                ->orWhereHas('user', function($q) {
                    // Pedidos de vendedores que têm loja associada mas o pedido não tem store_id correto
                })
                ->get();
        }
        
        $fixed = 0;
        
        foreach ($orders as $order) {
            if (!$order->user) {
                $this->warn("Pedido #{$order->id} não tem usuário associado");
                continue;
            }
            
            $user = $order->user;
            $storeId = null;
            
            // Se for vendedor, buscar loja associada
            if ($user->isVendedor()) {
                $userStores = $user->stores()->get();
                if ($userStores->isNotEmpty()) {
                    $storeId = $userStores->first()->id;
                } elseif ($user->store) {
                    $store = Store::where('name', 'like', '%' . $user->store . '%')->first();
                    if ($store) {
                        $storeId = $store->id;
                    }
                }
            } elseif ($user->isAdminLoja()) {
                $storeIds = $user->getStoreIds();
                $storeId = !empty($storeIds) ? $storeIds[0] : null;
            }
            
            // Se não encontrou, usar loja principal
            if (!$storeId) {
                $mainStore = Store::where('is_main', true)->first();
                $storeId = $mainStore ? $mainStore->id : null;
            }
            
            if ($storeId && $order->store_id != $storeId) {
                $oldStoreId = $order->store_id;
                $order->update(['store_id' => $storeId]);
                $this->info("Pedido #{$order->id}: store_id atualizado de {$oldStoreId} para {$storeId} (Vendedor: {$user->name})");
                $fixed++;
            }
        }
        
        $this->info("Total de pedidos corrigidos: {$fixed}");
        
        return 0;
    }
}

