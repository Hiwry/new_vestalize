<?php

namespace App\Helpers;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreHelper
{
    /**
     * Aplicar filtro de loja na query baseado no perfil do usuario.
     */
    public static function applyStoreFilter($query, $storeColumn = 'store_id')
    {
        $user = Auth::user();

        if (!$user) {
            return $query;
        }

        $column = $storeColumn;
        if (!str_contains($storeColumn, '.')) {
            $column = $storeColumn === 'store_id'
                ? 'store_id'
                : $storeColumn . '.store_id';
        }

        $activeTenantId = $user->getActiveTenantId();

        if ($activeTenantId !== null) {
            $tenantStoreIds = Store::withoutGlobalScopes()
                ->where('tenant_id', $activeTenantId)
                ->pluck('id')
                ->toArray();

            if (empty($tenantStoreIds)) {
                return $query->whereRaw('1 = 0');
            }

            $query->whereIn($column, $tenantStoreIds);
        } elseif ($user->tenant_id === null && $user->isAdminGeral()) {
            return $query;
        }

        if ($user->hasGeneralStoreAccess()) {
            return $query;
        }

        if ($user->isAdminLoja() || $user->isCaixa() || $user->isEstoque()) {
            $storeIds = $user->getStoreIds();

            return !empty($storeIds)
                ? $query->whereIn($column, $storeIds)
                : $query->whereRaw('1 = 0');
        }

        return $query;
    }

    /**
     * Obter IDs das lojas que o usuario pode acessar.
     */
    public static function getUserStoreIds(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return $user->getStoreIds();
    }

    /**
     * Verificar se usuario pode acessar uma loja.
     */
    public static function canAccessStore($storeId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->canAccessStore($storeId);
    }

    /**
     * Obter todas as lojas disponiveis para o usuario.
     */
    public static function getAvailableStores()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        $query = Store::active()->orderBy('name');
        $activeTenantId = $user->getActiveTenantId();

        if ($activeTenantId !== null) {
            $query->where('tenant_id', $activeTenantId);
        }

        if ($user->isAdminGeral()) {
            return $query->get();
        }

        if ($user->isAdminLoja() || $user->isCaixa() || $user->isEstoque()) {
            $storeIds = $user->getStoreIds();

            if (empty($storeIds)) {
                return collect();
            }

            return $query->whereIn('id', $storeIds)->get();
        }

        if ($user->isVendedor()) {
            $storeIds = $user->stores()->pluck('stores.id')->toArray();

            if (empty($storeIds)) {
                return collect();
            }

            return $query->whereIn('id', $storeIds)->get();
        }

        return $query->get();
    }

    /**
     * Obter IDs das lojas para filtragem.
     * Se uma loja especifica for selecionada, retorna apenas ela.
     * Caso contrario, retorna todas as lojas permitidas ao usuario.
     */
    public static function getStoreIds($selectedStoreId = null): array
    {
        if ($selectedStoreId) {
            return self::canAccessStore($selectedStoreId)
                ? [$selectedStoreId]
                : [];
        }

        return self::getUserStoreIds();
    }

    /**
     * Alias para getAvailableStores para manter compatibilidade.
     */
    public static function getUserStores()
    {
        return self::getAvailableStores();
    }
}
