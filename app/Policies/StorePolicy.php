<?php

namespace App\Policies;

use App\Helpers\StoreHelper;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Verificar se o usuario pode ver qualquer loja.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Verificar se o usuario pode ver uma loja especifica.
     */
    public function view(User $user, Store $store): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isAdminLoja() || $user->isEstoque() || $user->isCaixa()) {
            return StoreHelper::canAccessStore($store->id);
        }

        if ($user->isVendedor()) {
            return $user->stores->contains('id', $store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuario pode criar lojas.
     */
    public function create(User $user): bool
    {
        return $user->isAdminGeral();
    }

    /**
     * Verificar se o usuario pode atualizar uma loja.
     */
    public function update(User $user, Store $store): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuario pode excluir uma loja.
     */
    public function delete(User $user, Store $store): bool
    {
        return $user->isAdminGeral() && !$store->is_main;
    }

    /**
     * Verificar se o usuario pode gerenciar usuarios da loja.
     */
    public function manageUsers(User $user, Store $store): bool
    {
        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuario pode ver relatorios da loja.
     */
    public function viewReports(User $user, Store $store): bool
    {
        if ($user->isAdmin()) {
            if ($user->isAdminGeral()) {
                return true;
            }

            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }
}
