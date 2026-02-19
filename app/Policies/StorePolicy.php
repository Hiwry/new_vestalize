<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use App\Helpers\StoreHelper;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Verificar se o usuário pode ver qualquer loja
     */
    public function viewAny(User $user): bool
    {
        // Qualquer usuário autenticado pode listar lojas (filtrado por permissões)
        return true;
    }

    /**
     * Verificar se o usuário pode ver uma loja específica
     */
    public function view(User $user, Store $store): bool
    {
        // Admin geral vê todas
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja e estoque vêem lojas que têm acesso
        if ($user->isAdminLoja() || $user->isEstoque()) {
            return StoreHelper::canAccessStore($store->id);
        }

        // Vendedor e caixa vêem suas lojas
        if ($user->isVendedor() || $user->isCaixa()) {
            return $user->stores->contains('id', $store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuário pode criar lojas
     */
    public function create(User $user): bool
    {
        // Apenas admin geral pode criar lojas
        return $user->isAdminGeral();
    }

    /**
     * Verificar se o usuário pode atualizar uma loja
     */
    public function update(User $user, Store $store): bool
    {
        // Admin geral pode atualizar qualquer loja
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja pode atualizar suas lojas
        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuário pode excluir uma loja
     */
    public function delete(User $user, Store $store): bool
    {
        // Apenas admin geral pode excluir lojas
        // E não pode excluir a loja principal
        return $user->isAdminGeral() && !$store->is_main;
    }

    /**
     * Verificar se o usuário pode gerenciar usuários da loja
     */
    public function manageUsers(User $user, Store $store): bool
    {
        // Admin geral pode gerenciar usuários de qualquer loja
        if ($user->isAdminGeral()) {
            return true;
        }

        // Admin de loja pode gerenciar usuários de suas lojas
        if ($user->isAdminLoja()) {
            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }

    /**
     * Verificar se o usuário pode ver relatórios da loja
     */
    public function viewReports(User $user, Store $store): bool
    {
        // Admin pode ver relatórios
        if ($user->isAdmin()) {
            if ($user->isAdminGeral()) {
                return true;
            }
            return StoreHelper::canAccessStore($store->id);
        }

        return false;
    }
}
