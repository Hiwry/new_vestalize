<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // Added this line

class ClientController extends Controller
{
    use \App\Traits\ChecksSuperAdmin;

    /**
     * Listar todos os clientes
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver clientes de outros tenants sem selecionar contexto
        if ($this->isSuperAdmin() && !$this->hasSelectedTenant()) {
            return $this->emptySuperAdminResponse('clients.index', [
                'clients' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'stores' => collect([]),
                'search' => null,
                'storeId' => null,
                'categories' => collect([]), // Added to match original view data
                'category' => null, // Added to match original view data
            ]);
        }

        $search = $request->get('search');
        $category = $request->get('category');

        $query = Client::withCount(['orders' => function($q) {
            $q->where('is_draft', false);
        }])
        ->withSum(['orders as total_spent' => function($q) {
            $q->where('is_draft', false);
        }], 'total');

        // Aplicar filtro de loja
        StoreHelper::applyStoreFilter($query);

        // Busca por nome, telefone, email ou CPF/CNPJ
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_primary', 'like', "%{$search}%")
                  ->orWhere('phone_secondary', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$search}%");
            });
        }

        // Filtro por categoria
        if ($category) {
            $query->where('category', $category);
        }

        $clients = $query->orderBy('name')->paginate(20);

        // Buscar categorias únicas para o filtro
        $categories = Client::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('clients.index', compact('clients', 'categories', 'search', 'category'));
    }

    /**
     * Exibir detalhes de um cliente
     */
    public function show($id): View
    {
        $client = Client::with([
            'orders' => function($query) {
                $query->where('is_draft', false)
                      ->with(['status', 'items', 'payments'])
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Estatísticas do cliente
        $totalSpent = $client->orders->sum('total');
        $totalPaid = $client->orders->sum(function($order) {
            return $order->payments->sum('amount');
        });
        
        $stats = [
            'total_orders' => $client->orders->count(),
            'total_spent' => $totalSpent,
            'average_order' => $client->orders->count() > 0 
                ? $totalSpent / $client->orders->count() 
                : 0,
            'pending_balance' => $totalSpent - $totalPaid,
            'last_order_date' => $client->orders->first()?->created_at,
        ];

        // Pedidos agrupados por status
        $ordersByStatus = $client->orders->groupBy('status.name');

        return view('clients.show', compact('client', 'stats', 'ordersByStatus'));
    }

    /**
     * Formulário de criação de cliente
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Salvar novo cliente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:12',
            'category' => 'nullable|string|max:50',
        ]);

        // Obter store_id do usuário
        $user = \Illuminate\Support\Facades\Auth::user();
        $storeId = null;
        
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            $storeId = !empty($storeIds) ? $storeIds[0] : null;
        } else {
            $mainStore = \App\Models\Store::where('is_main', true)->first();
            $storeId = $mainStore ? $mainStore->id : null;
        }
        
        $validated['store_id'] = $storeId;
        $client = Client::create($validated);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Formulário de edição de cliente
     */
    public function edit($id): View
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Atualizar cliente
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:12',
            'category' => 'nullable|string|max:50',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Deletar cliente
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        // Verificar se o cliente tem pedidos
        if ($client->orders()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Não é possível excluir um cliente com pedidos cadastrados.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    /**
     * Formulário de edição rápida para Side Panel (AJAX)
     */
    public function quickEdit($id)
    {
        $client = Client::findOrFail($id);
        
        // Se for requisição AJAX, retornar apenas o HTML do formulário
        if (request()->ajax() || request()->wantsJson()) {
            return view('clients.partials.quick-edit-form', compact('client'));
        }

        // Fallback para edição normal
        return view('clients.edit', compact('client'));
    }

    /**
     * Atualizar cliente via AJAX (Side Panel)
     */
    public function quickUpdate(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:50',
        ]);

        $client->update($validated);

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cliente atualizado com sucesso!',
                'client' => $client
            ]);
        }

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Cliente atualizado com sucesso!');
    }
}

