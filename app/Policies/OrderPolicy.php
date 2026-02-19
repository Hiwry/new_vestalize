<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Helpers\StoreHelper;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar se o usuário pode ver qualquer pedido
     */
    public function viewAny(User $user): bool
    {
        // Qualquer usuário autenticado pode listar pedidos (filtrado por loja)
        return true;
    }

    /**
     * Verificar se o usuário pode ver um pedido específico
     */
    public function view(User $user, Order $order): bool
    {
        // Admin geral vê todos
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja e estoque vêem pedidos de suas lojas
        if ($user->isAdminLoja() || $user->isEstoque()) {
            return StoreHelper::canAccessStore($order->store_id);
        }

        // Vendedor vê apenas seus próprios pedidos
        if ($user->isVendedor()) {
            return $order->user_id === $user->id;
        }

        // Caixa vê pedidos da loja
        if ($user->isCaixa()) {
            return StoreHelper::canAccessStore($order->store_id);
        }

        return false;
    }

    /**
     * Verificar se o usuário pode criar pedidos
     */
    public function create(User $user): bool
    {
        // Admin, vendedor e caixa podem criar
        return $user->isAdmin() || $user->isVendedor() || $user->isCaixa();
    }

    /**
     * Verificar se o usuário pode atualizar um pedido
     */
    public function update(User $user, Order $order): bool
    {
        // Admin geral pode editar qualquer pedido
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja pode editar pedidos de suas lojas
        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($order->store_id);
        }

        // Vendedor pode editar apenas seus próprios pedidos em rascunho
        if ($user->isVendedor()) {
            return $order->user_id === $user->id && $order->is_draft;
        }

        return false;
    }

    /**
     * Verificar se o usuário pode cancelar um pedido
     */
    public function cancel(User $user, Order $order): bool
    {
        // Apenas admins podem cancelar
        if (!$user->isAdmin()) {
            return false;
        }

        // Admin geral pode cancelar qualquer pedido
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja pode cancelar pedidos de suas lojas
        return StoreHelper::canAccessStore($order->store_id);
    }

    /**
     * Verificar se o usuário pode excluir um pedido
     */
    public function delete(User $user, Order $order): bool
    {
        // Apenas admin geral pode excluir pedidos
        if (!$user->isAdminGeral()) {
            return false;
        }

        // Não pode excluir pedidos finalizados
        return $order->is_draft || $order->is_cancelled;
    }

    /**
     * Verificar se o usuário pode alterar o status
     */
    public function updateStatus(User $user, Order $order): bool
    {
        // Admin e produção podem alterar status
        if ($user->isAdmin() || $user->isProducao()) {
            if ($user->isAdminGeral() || $user->isProducao()) {
                return true;
            }
            return StoreHelper::canAccessStore($order->store_id);
        }

        return false;
    }
}
