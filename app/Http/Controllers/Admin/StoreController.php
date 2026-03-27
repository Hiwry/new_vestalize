<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StoreHelper;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreController extends Controller
{
    private function getTenantContext(): array
    {
        $tenantId = Auth::user()?->tenant_id ?? session('selected_tenant_id');
        $tenant = $tenantId ? Tenant::with('currentPlan')->find($tenantId) : null;
        $storeCount = $tenant
            ? Store::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()
            : null;
        $storeLimit = $tenant ? ($tenant->getPlanLimits()['stores'] ?? 1) : null;
        $remainingStores = ($storeCount !== null && $storeLimit !== null)
            ? max($storeLimit - $storeCount, 0)
            : null;

        return [
            'tenantId' => $tenant?->id,
            'tenant' => $tenant,
            'storeCount' => $storeCount,
            'storeLimit' => $storeLimit,
            'remainingStores' => $remainingStores,
            'canCreateStore' => $tenant ? $storeCount < $storeLimit : false,
        ];
    }

    private function requireTenantContext(): array
    {
        $context = $this->getTenantContext();

        if (!$context['tenant']) {
            abort(403, 'Selecione um tenant antes de gerenciar lojas.');
        }

        return $context;
    }

    private function ensureStoreInTenantContext(Store $store): void
    {
        $context = $this->getTenantContext();

        if ($context['tenantId'] && (int) $store->tenant_id !== (int) $context['tenantId']) {
            abort(403, 'Esta loja nao pertence ao tenant ativo.');
        }
    }

    private function getMainStoreForTenant(int $tenantId): ?Store
    {
        return Store::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('is_main', true)
            ->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $context = $this->getTenantContext();

        if ($user->isAdminGeral()) {
            $stores = Store::with(['parent', 'subStores', 'users'])
                ->when($context['tenantId'], fn ($query, $tenantId) => $query->where('tenant_id', $tenantId))
                ->orderBy('is_main', 'desc')
                ->orderBy('name')
                ->get();
        } else {
            $storeIds = $user->getStoreIds();
            $stores = Store::whereIn('id', $storeIds)
                ->with(['parent', 'subStores', 'users'])
                ->orderBy('name')
                ->get();
        }

        return view('admin.stores.index', array_merge(compact('stores'), $context));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem criar lojas.');
        }

        $context = $this->requireTenantContext();

        if (!$context['canCreateStore']) {
            return redirect()->route('admin.stores.index')
                ->withErrors([
                    'error' => "O plano atual permite ate {$context['storeLimit']} lojas. Faca upgrade para cadastrar outra loja.",
                ]);
        }

        $mainStore = $this->getMainStoreForTenant($context['tenantId']);
        $stores = Store::withoutGlobalScopes()
            ->where('tenant_id', $context['tenantId'])
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.stores.create', array_merge(compact('mainStore', 'stores'), $context));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem criar lojas.');
        }

        $context = $this->requireTenantContext();

        if (!$context['canCreateStore']) {
            return redirect()->route('admin.stores.index')
                ->withErrors([
                    'error' => "O plano atual permite ate {$context['storeLimit']} lojas. Faca upgrade para cadastrar outra loja.",
                ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stores,id',
            'active' => 'boolean',
        ]);

        $mainStore = $this->getMainStoreForTenant($context['tenantId']);

        if ($mainStore && empty($validated['parent_id'])) {
            $validated['parent_id'] = $mainStore->id;
        }

        if (!empty($validated['parent_id'])) {
            $parent = Store::withoutGlobalScopes()
                ->where('tenant_id', $context['tenantId'])
                ->findOrFail($validated['parent_id']);

            if (!$parent->isMain()) {
                return redirect()->back()->withErrors([
                    'parent_id' => 'Apenas a loja principal pode ter sub-lojas.',
                ]);
            }
        }

        Store::create([
            'tenant_id' => $context['tenantId'],
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'active' => (bool) ($validated['active'] ?? false),
            'is_main' => !$mainStore,
        ]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Loja criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store): View
    {
        $this->ensureStoreInTenantContext($store);

        if (!StoreHelper::canAccessStore($store->id)) {
            abort(403, 'Voce nao tem permissao para acessar esta loja.');
        }

        $store->load(['parent', 'subStores', 'users', 'orders', 'budgets', 'clients']);

        $admins = $store->users()->wherePivot('role', 'admin_loja')->get();
        $sellers = $store->users()->where('users.role', 'vendedor')->orderBy('name')->get();
        $allUsers = $store->users()->orderBy('name')->get();

        return view('admin.stores.show', compact('store', 'admins', 'sellers', 'allUsers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store): View
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem editar lojas.');
        }

        $context = $this->requireTenantContext();
        $this->ensureStoreInTenantContext($store);

        $mainStore = $this->getMainStoreForTenant($context['tenantId']);
        $stores = Store::withoutGlobalScopes()
            ->where('tenant_id', $context['tenantId'])
            ->where('id', '!=', $store->id)
            ->active()
            ->orderBy('name')
            ->get();

        $settings = \App\Models\CompanySetting::getSettings($store->id);

        return view('admin.stores.edit', array_merge(compact('store', 'mainStore', 'stores', 'settings'), $context));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem editar lojas.');
        }

        $context = $this->requireTenantContext();
        $this->ensureStoreInTenantContext($store);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stores,id',
            'active' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_city' => 'nullable|string|max:255',
            'company_state' => 'nullable|string|max:2',
            'company_zip' => 'nullable|string|max:10',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_cnpj' => 'nullable|string|max:18',
            'bank_name' => 'nullable|string|max:255',
            'bank_agency' => 'nullable|string|max:10',
            'bank_account' => 'nullable|string|max:20',
            'pix_key' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        unset($validated['is_main']);

        if (!empty($validated['parent_id'])) {
            $parent = Store::withoutGlobalScopes()
                ->where('tenant_id', $context['tenantId'])
                ->findOrFail($validated['parent_id']);

            if (!$parent->isMain()) {
                return redirect()->back()->withErrors([
                    'parent_id' => 'Apenas a loja principal pode ter sub-lojas.',
                ]);
            }
        }

        $storeData = [
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'active' => $validated['active'] ?? false,
        ];

        $store->update($storeData);

        $settings = \App\Models\CompanySetting::where('store_id', $store->id)->first();
        if (!$settings) {
            $settings = new \App\Models\CompanySetting();
            $settings->store_id = $store->id;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                $oldPath = public_path($settings->logo_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }

                $publicPath = public_path();
                if (str_contains($publicPath, '/home2/') || str_contains($publicPath, '/home/')) {
                    if (preg_match('#(/home2?/[^/]+)#', $publicPath, $matches)) {
                        $homePath = $matches[1];
                        $publicHtmlPath = $homePath . '/public_html/' . $settings->logo_path;
                        if (file_exists($publicHtmlPath)) {
                            @unlink($publicHtmlPath);
                        }
                    }
                }
            }

            $file = $request->file('logo');
            $filename = 'logo_store_' . $store->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $logoPath = 'images/' . $filename;

            $imagesDir = public_path('images');
            if (!file_exists($imagesDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($imagesDir, 0755, true);
            }

            $file->move($imagesDir, $filename);

            $publicPath = public_path();
            if (str_contains($publicPath, '/home2/') || str_contains($publicPath, '/home/')) {
                if (preg_match('#(/home2?/[^/]+)#', $publicPath, $matches)) {
                    $homePath = $matches[1];
                    $publicHtmlImagesDir = $homePath . '/public_html/images';

                    if (!file_exists($publicHtmlImagesDir)) {
                        \Illuminate\Support\Facades\File::makeDirectory($publicHtmlImagesDir, 0755, true);
                    }

                    $publicHtmlPath = $publicHtmlImagesDir . '/' . $filename;
                    copy($imagesDir . '/' . $filename, $publicHtmlPath);

                    \Log::info('StoreController: Logo tambem salvo em public_html/images', [
                        'path' => $publicHtmlPath,
                    ]);
                }
            }

            $settings->logo_path = $logoPath;

            \Log::info('StoreController: Logo salvo com sucesso', [
                'store_id' => $store->id,
                'logo_path' => $logoPath,
            ]);
        }

        $settings->fill([
            'company_name' => $validated['company_name'] ?? null,
            'company_address' => $validated['company_address'] ?? null,
            'company_city' => $validated['company_city'] ?? null,
            'company_state' => $validated['company_state'] ?? null,
            'company_zip' => $validated['company_zip'] ?? null,
            'company_phone' => $validated['company_phone'] ?? null,
            'company_email' => $validated['company_email'] ?? null,
            'company_website' => $validated['company_website'] ?? null,
            'company_cnpj' => $validated['company_cnpj'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_agency' => $validated['bank_agency'] ?? null,
            'bank_account' => $validated['bank_account'] ?? null,
            'pix_key' => $validated['pix_key'] ?? null,
        ]);

        $settings->save();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Loja e configuracoes atualizadas com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem deletar lojas.');
        }

        $this->ensureStoreInTenantContext($store);

        if ($store->isMain()) {
            return redirect()->back()->withErrors([
                'error' => 'Nao e possivel deletar a loja principal.',
            ]);
        }

        if ($store->orders()->count() > 0 || $store->budgets()->count() > 0 || $store->clients()->count() > 0) {
            return redirect()->back()->withErrors([
                'error' => 'Nao e possivel deletar a loja pois ha dados associados.',
            ]);
        }

        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Loja removida com sucesso!');
    }

    /**
     * Atribuir admin a uma loja
     */
    public function assignAdmin(Request $request, Store $store): RedirectResponse
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem atribuir admins.');
        }

        $context = $this->requireTenantContext();
        $this->ensureStoreInTenantContext($store);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ((int) $user->tenant_id !== (int) $context['tenantId']) {
            return redirect()->back()->withErrors([
                'user_id' => 'Este usuario nao pertence ao tenant atual.',
            ]);
        }

        if ($store->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->withErrors([
                'user_id' => 'Este usuario ja esta vinculado a esta loja.',
            ]);
        }

        $pivotRole = $user->role === 'vendedor' ? 'vendedor' : 'admin_loja';

        $store->users()->attach($user->id, ['role' => $pivotRole]);

        return redirect()->back()->with('success', 'Usuario vinculado com sucesso!');
    }

    /**
     * Remover admin de uma loja
     */
    public function removeAdmin(Store $store, User $user): RedirectResponse
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem remover admins.');
        }

        $this->ensureStoreInTenantContext($store);

        $store->users()->detach($user->id);

        return redirect()->back()->with('success', 'Usuario removido da loja com sucesso!');
    }

    /**
     * Deletar o logo de uma loja
     */
    public function deleteLogo(Store $store)
    {
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem deletar logos.');
        }

        $this->ensureStoreInTenantContext($store);

        $settings = \App\Models\CompanySetting::where('store_id', $store->id)->first();

        if ($settings && $settings->logo_path) {
            $logoPath = public_path($settings->logo_path);
            if (file_exists($logoPath)) {
                @unlink($logoPath);
            }

            $publicPath = public_path();
            if (str_contains($publicPath, '/home2/') || str_contains($publicPath, '/home/')) {
                if (preg_match('#(/home2?/[^/]+)#', $publicPath, $matches)) {
                    $homePath = $matches[1];
                    $publicHtmlPath = $homePath . '/public_html/' . $settings->logo_path;
                    if (file_exists($publicHtmlPath)) {
                        @unlink($publicHtmlPath);
                    }
                }
            }

            $settings->logo_path = null;
            $settings->save();

            \Log::info('StoreController: Logo removido com sucesso', [
                'store_id' => $store->id,
            ]);

            return response()->json(['success' => true, 'message' => 'Logo removido com sucesso!']);
        }

        return response()->json(['success' => false, 'message' => 'Nenhum logo encontrado.'], 404);
    }
}
