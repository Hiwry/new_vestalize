<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Added this line

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Super Admin (tenant_id === null) não deve ver dados de outros tenants sem selecionar um context
        if ($user->tenant_id === null) {
            return view('admin.users.index', [
                'users' => collect([]),
                'isSuperAdmin' => true
            ]);
        }

        $users = User::with('stores')->orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $stores = Store::active()->orderBy('name')->get();
        return view('admin.users.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,admin_loja,vendedor,producao,caixa,estoque',
            'store_id' => ['nullable', 'exists:stores,id'],
        ]);

        // Se for admin de loja, a loja é obrigatória
        if ($validated['role'] === 'admin_loja') {
            $request->validate([
                'store_id' => ['required', 'exists:stores,id'],
            ]);
        }

        $validated['password'] = Hash::make($validated['password']);

        // SEGURANÇA: role e tenant_id atribuídos explicitamente (não via mass assignment)
        $role = $validated['role'];
        $tenantId = Auth::user()->tenant_id;
        unset($validated['role']); // Remover do array para não tentar via $fillable

        $user = User::create($validated);
        $user->role = $role;
        $user->tenant_id = $tenantId;
        $user->save();

        // Atribuir loja ao usuário (se fornecido)
        if (!empty($validated['store_id'])) {
            $user->stores()->attach($validated['store_id'], ['role' => $role]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        $stores = Store::active()->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'stores'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,admin_loja,vendedor,producao,caixa,estoque',
            'store_id' => ['nullable', 'exists:stores,id'],
        ]);

        // Se for admin de loja, a loja é obrigatória
        if ($validated['role'] === 'admin_loja') {
            $request->validate([
                'store_id' => ['required', 'exists:stores,id'],
            ]);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // SEGURANÇA: role atribuído explicitamente
        $role = $validated['role'];
        unset($validated['role']);
        $user->update($validated);
        $user->role = $role;
        $user->save();

        // Sincronizar atribuição de loja
        if (isset($validated['store_id']) && !empty($validated['store_id'])) {
            // Atribui a loja ao usuário com a role apropriada
            $user->stores()->sync([$validated['store_id'] => ['role' => $role]]);
        } else {
            // Se não tem store_id, remove vínculos apenas se não for admin geral
            if ($role !== 'admin') {
                $user->stores()->detach();
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
