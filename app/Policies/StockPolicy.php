<?php

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;
use App\Helpers\StoreHelper;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar se o usuário pode ver qualquer estoque
     */
    public function viewAny(User $user): bool
    {
        // Admin, estoque e produção podem ver estoque
        return $user->isAdmin() || $user->isEstoque() || $user->isProducao();
    }

    /**
     * Verificar se o usuário pode ver um estoque específico
     */
    public function view(User $user, Stock $stock): bool
    {
        // Admin geral e estoque vêem todos
        if ($user->isAdminGeral() || $user->isEstoque()) {
            return true;
        }

        // Admin de loja vê estoque de suas lojas
        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($stock->store_id);
        }

        // Produção vê estoque para consulta
        if ($user->isProducao()) {
            return true;
        }

        return false;
    }

    /**
     * Verificar se o usuário pode criar estoque
     */
    public function create(User $user): bool
    {
        // Apenas admin e estoque podem criar
        return $user->isAdmin() || $user->isEstoque();
    }

    /**
     * Verificar se o usuário pode atualizar estoque
     */
    public function update(User $user, Stock $stock): bool
    {
        // Admin geral e estoque podem atualizar qualquer estoque
        if ($user->isAdminGeral() || $user->isEstoque()) {
            return true;
        }

        // Admin de loja pode atualizar estoque de suas lojas
        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($stock->store_id);
        }

        return false;
    }

    /**
     * Verificar se o usuário pode excluir estoque
     */
    public function delete(User $user, Stock $stock): bool
    {
        // Apenas admin geral e estoque podem excluir
        return $user->isAdminGeral() || $user->isEstoque();
    }

    /**
     * Verificar se o usuário pode transferir estoque
     */
    public function transfer(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }

    /**
     * Verificar se o usuário pode aprovar solicitações de estoque
     */
    public function approveRequests(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }
}
