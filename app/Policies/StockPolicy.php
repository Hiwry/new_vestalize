<?php

namespace App\Policies;

use App\Helpers\StoreHelper;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar se o usuario pode ver qualquer estoque.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque() || $user->isProducao();
    }

    /**
     * Verificar se o usuario pode ver um estoque especifico.
     */
    public function view(User $user, Stock $stock): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isEstoque() || $user->isAdminLoja()) {
            return StoreHelper::canAccessStore($stock->store_id);
        }

        if ($user->isProducao()) {
            return true;
        }

        return false;
    }

    /**
     * Verificar se o usuario pode criar estoque.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }

    /**
     * Verificar se o usuario pode atualizar estoque.
     */
    public function update(User $user, Stock $stock): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isEstoque() || $user->isAdminLoja()) {
            return StoreHelper::canAccessStore($stock->store_id);
        }

        return false;
    }

    /**
     * Verificar se o usuario pode excluir estoque.
     */
    public function delete(User $user, Stock $stock): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isEstoque()) {
            return StoreHelper::canAccessStore($stock->store_id);
        }

        return false;
    }

    /**
     * Verificar se o usuario pode transferir estoque.
     */
    public function transfer(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }

    /**
     * Verificar se o usuario pode aprovar solicitacoes de estoque.
     */
    public function approveRequests(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }
}
