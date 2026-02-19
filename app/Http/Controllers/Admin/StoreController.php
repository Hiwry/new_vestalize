<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        if ($user->isAdminGeral()) {
            $stores = Store::with(['parent', 'subStores', 'users'])->orderBy('is_main', 'desc')->orderBy('name')->get();
        } else {
            // Admin loja vê apenas suas lojas
            $storeIds = $user->getStoreIds();
            $stores = Store::whereIn('id', $storeIds)->with(['parent', 'subStores', 'users'])->orderBy('name')->get();
        }
        
        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Apenas admin geral pode criar lojas
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem criar lojas.');
        }
        
        $mainStore = Store::where('is_main', true)->first();
        $stores = Store::active()->orderBy('name')->get();
        
        return view('admin.stores.create', compact('mainStore', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Apenas admin geral pode criar lojas
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem criar lojas.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stores,id',
            'active' => 'boolean',
        ]);
        
        // Validar que apenas loja principal pode ser pai
        if (isset($validated['parent_id']) && $validated['parent_id']) {
            $parent = Store::findOrFail($validated['parent_id']);
            if (!$parent->isMain()) {
                return redirect()->back()->withErrors(['parent_id' => 'Apenas a loja principal pode ter sub-lojas.']);
            }
        }
        
        Store::create($validated);
        
        return redirect()->route('admin.stores.index')
            ->with('success', 'Loja criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store): View
    {
        // Verificar acesso
        if (!StoreHelper::canAccessStore($store->id)) {
            abort(403, 'Você não tem permissão para acessar esta loja.');
        }
        
        $store->load(['parent', 'subStores', 'users', 'orders', 'budgets', 'clients']);

        // Listas separadas
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
        // Apenas admin geral pode editar lojas
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem editar lojas.');
        }
        
        $mainStore = Store::where('is_main', true)->first();
        $stores = Store::where('id', '!=', $store->id)->active()->orderBy('name')->get();
        
        // Buscar configurações da empresa desta loja
        $settings = \App\Models\CompanySetting::getSettings($store->id);
        
        return view('admin.stores.edit', compact('store', 'mainStore', 'stores', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        // Apenas admin geral pode editar lojas
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem editar lojas.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:stores,id',
            'active' => 'boolean',
            // Configurações da empresa
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
        
        // Não permitir alterar is_main
        unset($validated['is_main']);
        
        // Validar que apenas loja principal pode ser pai
        if (isset($validated['parent_id']) && $validated['parent_id']) {
            $parent = Store::findOrFail($validated['parent_id']);
            if (!$parent->isMain()) {
                return redirect()->back()->withErrors(['parent_id' => 'Apenas a loja principal pode ter sub-lojas.']);
            }
        }
        
        // Separar dados da loja e das configurações
        $storeData = [
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'active' => $validated['active'] ?? false,
        ];
        
        $store->update($storeData);
        
        // Atualizar ou criar configurações da empresa
        $settings = \App\Models\CompanySetting::where('store_id', $store->id)->first();
        if (!$settings) {
            $settings = new \App\Models\CompanySetting();
            $settings->store_id = $store->id;
        }
        
        // Upload do logo
        if ($request->hasFile('logo')) {
            // Deletar logo antigo
            if ($settings->logo_path) {
                $oldPath = public_path($settings->logo_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                
                // No servidor Hostinger, também deletar de public_html/images/
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
            
            // Salvar novo logo
            $file = $request->file('logo');
            $filename = 'logo_store_' . $store->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $logoPath = 'images/' . $filename;
            
            // Criar diretório se não existir
            $imagesDir = public_path('images');
            if (!file_exists($imagesDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($imagesDir, 0755, true);
            }
            
            // Salvar em laravel/public/images/
            $file->move($imagesDir, $filename);
            
            // No servidor Hostinger, também salvar em public_html/images/ para acesso direto
            $publicPath = public_path();
            if (str_contains($publicPath, '/home2/') || str_contains($publicPath, '/home/')) {
                if (preg_match('#(/home2?/[^/]+)#', $publicPath, $matches)) {
                    $homePath = $matches[1];
                    $publicHtmlImagesDir = $homePath . '/public_html/images';
                    
                    // Criar diretório se não existir
                    if (!file_exists($publicHtmlImagesDir)) {
                        \Illuminate\Support\Facades\File::makeDirectory($publicHtmlImagesDir, 0755, true);
                    }
                    
                    // Copiar arquivo para public_html/images/
                    $publicHtmlPath = $publicHtmlImagesDir . '/' . $filename;
                    copy($imagesDir . '/' . $filename, $publicHtmlPath);
                    
                    \Log::info('StoreController: Logo também salvo em public_html/images', [
                        'path' => $publicHtmlPath
                    ]);
                }
            }
            
            $settings->logo_path = $logoPath;
            
            \Log::info('StoreController: Logo salvo com sucesso', [
                'store_id' => $store->id,
                'logo_path' => $logoPath
            ]);
        }
        
        // Atualizar configurações
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
            ->with('success', 'Loja e configurações atualizadas com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        // Apenas admin geral pode deletar lojas
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem deletar lojas.');
        }
        
        // Não permitir deletar loja principal
        if ($store->isMain()) {
            return redirect()->back()->withErrors(['error' => 'Não é possível deletar a loja principal.']);
        }
        
        // Verificar se há dados associados
        if ($store->orders()->count() > 0 || $store->budgets()->count() > 0 || $store->clients()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Não é possível deletar a loja pois há dados associados.']);
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
        // Apenas admin geral pode atribuir admins
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem atribuir admins.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $user = User::findOrFail($validated['user_id']);
        
        // Verificar se já está atribuído
        if ($store->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->withErrors(['user_id' => 'Este usuário já é admin desta loja.']);
        }
        
        // Determinar o papel na loja baseado no papel do usuário
        $pivotRole = $user->role === 'vendedor' ? 'vendedor' : 'admin_loja';
        
        $store->users()->attach($user->id, ['role' => $pivotRole]);
        
        return redirect()->back()->with('success', 'Admin atribuído com sucesso!');
    }

    /**
     * Remover admin de uma loja
     */
    public function removeAdmin(Store $store, User $user): RedirectResponse
    {
        // Apenas admin geral pode remover admins
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem remover admins.');
        }
        
        $store->users()->detach($user->id);
        
        return redirect()->back()->with('success', 'Admin removido com sucesso!');
    }

    /**
     * Deletar o logo de uma loja
     */
    public function deleteLogo(Store $store)
    {
        // Apenas admin geral pode deletar logo
        if (!Auth::user()->isAdminGeral()) {
            abort(403, 'Apenas administradores gerais podem deletar logos.');
        }
        
        $settings = \App\Models\CompanySetting::where('store_id', $store->id)->first();
        
        if ($settings && $settings->logo_path) {
            // Deletar de laravel/public/images/
            $logoPath = public_path($settings->logo_path);
            if (file_exists($logoPath)) {
                @unlink($logoPath);
            }
            
            // No servidor Hostinger, também deletar de public_html/images/
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
                'store_id' => $store->id
            ]);
            
            return response()->json(['success' => true, 'message' => 'Logo removido com sucesso!']);
        }
        
        return response()->json(['success' => false, 'message' => 'Nenhum logo encontrado.'], 404);
    }
}
