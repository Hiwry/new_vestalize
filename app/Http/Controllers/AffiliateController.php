<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AffiliateController extends Controller
{
    /**
     * Lista todos os afiliados (admin)
     */
    public function index()
    {
        $affiliates = Affiliate::withCount('tenants')
            ->withSum('commissions as total_commissions', 'amount')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.affiliates.index', compact('affiliates'));
    }

    /**
     * Formulário de criação de afiliado
     */
    public function create()
    {
        return view('admin.affiliates.create');
    }

    /**
     * Salva novo afiliado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliates,email',
            'phone' => 'nullable|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'bank_info' => 'nullable|array',
            'bank_info.bank' => 'nullable|string|max:100',
            'bank_info.agency' => 'nullable|string|max:20',
            'bank_info.account' => 'nullable|string|max:30',
            'bank_info.pix' => 'nullable|string|max:100',
        ]);

        $affiliate = Affiliate::create($validated);

        return redirect()
            ->route('admin.affiliates.show', $affiliate)
            ->with('success', 'Afiliado criado com sucesso! Código: ' . $affiliate->code);
    }

    /**
     * Exibe detalhes do afiliado
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->load(['tenants.currentPlan', 'commissions' => function ($query) {
            $query->latest()->limit(20);
        }]);

        $stats = [
            'total_referrals' => $affiliate->tenants()->count(),
            'active_referrals' => $affiliate->tenants()->where('status', 'active')->count(),
            'total_earnings' => $affiliate->total_earnings,
            'pending_balance' => $affiliate->pending_balance,
            'withdrawn_balance' => $affiliate->withdrawn_balance,
        ];

        return view('admin.affiliates.show', compact('affiliate', 'stats'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Affiliate $affiliate)
    {
        return view('admin.affiliates.edit', compact('affiliate'));
    }

    /**
     * Atualiza afiliado
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('affiliates')->ignore($affiliate->id)],
            'phone' => 'nullable|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'bank_info' => 'nullable|array',
        ]);

        $affiliate->update($validated);

        return redirect()
            ->route('admin.affiliates.show', $affiliate)
            ->with('success', 'Afiliado atualizado com sucesso!');
    }

    /**
     * Lista comissões de um afiliado
     */
    public function commissions(Affiliate $affiliate, Request $request)
    {
        $query = $affiliate->commissions()->with(['tenant', 'subscriptionPayment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.affiliates.commissions', compact('affiliate', 'commissions'));
    }

    /**
     * Deleta afiliado
     */
    public function destroy(Affiliate $affiliate)
    {
        // Verificar se tem tenants vinculados
        if ($affiliate->tenants()->count() > 0) {
            return back()->with('error', 'Não é possível deletar afiliado com clientes vinculados.');
        }

        // Deletar comissões primeiro
        $affiliate->commissions()->delete();
        
        $affiliate->delete();

        return redirect()
            ->route('admin.affiliates.index')
            ->with('success', 'Afiliado deletado com sucesso!');
    }

    /**
     * Aprova uma comissão
     */
    public function approveCommission(AffiliateCommission $commission)
    {
        if ($commission->approve()) {
            return back()->with('success', 'Comissão aprovada com sucesso!');
        }

        return back()->with('error', 'Não foi possível aprovar esta comissão.');
    }

    /**
     * Marca comissão como paga
     */
    public function payCommission(AffiliateCommission $commission)
    {
        if ($commission->markAsPaid()) {
            return back()->with('success', 'Comissão marcada como paga!');
        }

        return back()->with('error', 'Não foi possível marcar esta comissão como paga.');
    }

    /**
     * Cancela uma comissão
     */
    public function cancelCommission(Request $request, AffiliateCommission $commission)
    {
        $reason = $request->input('reason', 'Cancelado pelo administrador');

        if ($commission->cancel($reason)) {
            return back()->with('success', 'Comissão cancelada.');
        }

        return back()->with('error', 'Não foi possível cancelar esta comissão.');
    }

    /**
     * Validar código de afiliado (API para cadastro)
     */
    public function validateCode(Request $request)
    {
        $code = strtoupper($request->input('code', ''));
        
        $affiliate = Affiliate::active()->byCode($code)->first();

        if ($affiliate) {
            return response()->json([
                'valid' => true,
                'affiliate_name' => $affiliate->name,
                'message' => 'Código válido! Você será indicado por ' . $affiliate->name,
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Código de afiliado inválido.',
        ], 404);
    }
}
