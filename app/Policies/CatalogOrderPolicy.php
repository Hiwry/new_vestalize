<?php

namespace App\Policies;

use App\Helpers\StoreHelper;
use App\Models\CatalogOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEstoque();
    }

    public function view(User $user, CatalogOrder $catalogOrder): bool
    {
        if (!$this->viewAny($user)) {
            return false;
        }

        if ($user->isAdminGeral()) {
            return true;
        }

        if ($user->tenant_id !== null && (int) $catalogOrder->tenant_id !== (int) $user->tenant_id) {
            return false;
        }

        return $catalogOrder->store_id
            ? StoreHelper::canAccessStore($catalogOrder->store_id)
            : true;
    }

    public function update(User $user, CatalogOrder $catalogOrder): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        return $this->view($user, $catalogOrder);
    }
}

