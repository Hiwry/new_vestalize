<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ChecksSuperAdmin
{
    /**
     * Verifica se o usuário é Super Admin (tenant_id === null)
     */
    protected function isSuperAdmin(): bool
    {
        $user = Auth::user();
        return $user && $user->tenant_id === null;
    }

    /**
     * Obtém o ID do tenant selecionado na sessão para o Super Admin
     */
    protected function getSelectedTenantId(): ?int
    {
        if (!$this->isSuperAdmin()) {
            return Auth::user()->tenant_id;
        }

        return session('selected_tenant_id');
    }

    /**
     * Verifica se o Super Admin selecionou um tenant
     */
    protected function hasSelectedTenant(): bool
    {
        return !empty($this->getSelectedTenantId());
    }

    /**
     * Retorna uma view vazia para Super Admin
     */
    protected function emptySuperAdminResponse(string $view, array $emptyData = []): \Illuminate\View\View
    {
        $defaultData = [
            'isSuperAdmin' => true,
        ];

        return view($view, array_merge($defaultData, $emptyData));
    }
}
