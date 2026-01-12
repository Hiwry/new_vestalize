<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Exibe a listagem de logs de auditoria
     */
    public function index(Request $request)
    {
        // Apenas Admin Geral ou Super Admin podem ver todos os logs
        // Admins de loja veem apenas os logs do seu tenant_id
        $user = Auth::user();
        
        $query = ActivityLog::with(['causer', 'subject'])
            ->latest();

        if ($user->tenant_id !== null) {
            $query->where('tenant_id', $user->tenant_id);
        }

        // Filtros
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(30)->withQueryString();
        
        // Dados para os filtros
        $users = [];
        if ($user->tenant_id !== null) {
            $users = User::where('tenant_id', $user->tenant_id)->orderBy('name')->get();
        } else {
            $users = User::orderBy('name')->limit(100)->get();
        }

        $events = ActivityLog::select('event')->distinct()->pluck('event');

        return view('admin.audit.index', compact('logs', 'users', 'events'));
    }

    /**
     * Exibe detalhes de um log específico (incluindo JSON properties)
     */
    public function show($id)
    {
        $user = Auth::user();
        $log = ActivityLog::with(['causer', 'subject'])->findOrFail($id);

        // Segurança: verificar se o log pertence ao tenant do usuário
        if ($user->tenant_id !== null && $log->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        return view('admin.audit.show', compact('log'));
    }
}
