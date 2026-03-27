<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->tenant_id === null) {
            return view('admin.users.index', [
                'users' => collect([]),
                'isSuperAdmin' => true,
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

        $validated['password'] = Hash::make($validated['password']);

        $role = $validated['role'];
        $tenantId = Auth::user()->tenant_id;
        unset($validated['role']);

        $user = User::create($validated);
        $user->role = $role;
        $user->tenant_id = $tenantId;
        $user->save();

        if (!empty($validated['store_id'])) {
            $user->stores()->attach($validated['store_id'], ['role' => $role]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario criado com sucesso!');
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

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $role = $validated['role'];
        unset($validated['role']);

        $user->update($validated);
        $user->role = $role;
        $user->save();

        if (isset($validated['store_id']) && !empty($validated['store_id'])) {
            $user->stores()->sync([$validated['store_id'] => ['role' => $role]]);
        } elseif ($role !== 'admin') {
            $user->stores()->detach();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario excluido com sucesso!');
    }
}
