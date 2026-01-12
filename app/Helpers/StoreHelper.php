<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Store;

class StoreHelper
{
    /**
     * Aplicar filtro de loja na query baseado no role do usuário
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

        $activeTenantId = $user->tenant_id;
        
        // Se for Super Admin, verifica se há um tenant selecionado na sessão
        if ($activeTenantId === null) {
            $activeTenantId = session('selected_tenant_id');
        }

        if ($activeTenantId !== null) {
            $tenantStoreIds = \App\Models\Store::where('tenant_id', $activeTenantId)->pluck('id')->toArray();

            if (empty($tenantStoreIds)) {
                return $query->whereRaw('1 = 0');
            }

            $query->whereIn($column, $tenantStoreIds);
        } else {
            // Se for Super Admin e NÃO houver tenant selecionado, mostra apenas itens sem loja (gerais do sistema)
            if ($user->tenant_id === null) {
                return $query->whereNull($column);
            }
        }

        if ($user->isAdminGeral() || $user->isEstoque()) {
            return $query;
        }

        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            
            if (!empty($storeIds)) {
                return $query->whereIn($column, $storeIds);
            }
        }

        return $query;
    }

    /**
     * Obter IDs das lojas que o usuário pode acessar
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
     * Verificar se usuário pode acessar uma loja
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
     * Obter todas as lojas disponíveis para o usuário
     */
    public static function getAvailableStores()
    {
        $user = Auth::user();
        
        if (!$user || $user->tenant_id === null) {
            return Store::active()->orderBy('name')->get();
        }

        return Store::active()
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();
    }
    /**
     * Obter IDs das lojas para filtragem
     * Se uma loja específica for selecionada, retorna apenas ela (se o usuário tiver acesso)
     * Caso contrário, retorna todas as lojas permitidas ao usuário
     */
    public static function getStoreIds($selectedStoreId = null): array
    {
        // Se uma loja específica foi selecionada
        if ($selectedStoreId) {
            if (self::canAccessStore($selectedStoreId)) {
                return [$selectedStoreId];
            }
            // Se não tiver acesso, retorna array vazio ou poderia lançar exceção
            // Por segurança, retorna vazio para não mostrar dados indevidos
            return [];
        }

        // Retorna todas as lojas permitidas
        return self::getUserStoreIds();
    }

    /**
     * Alias para getAvailableStores para manter compatibilidade
     */
    public static function getUserStores()
    {
        return self::getAvailableStores();
    }
}
