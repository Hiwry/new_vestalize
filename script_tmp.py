from pathlib import Path
p=Path('app/Http/Controllers/BudgetController.php')
t=p.read_text(encoding='utf-8', errors='replace')
old="""public function confirm()
    {
         = Session::get('budget_data', []);
         = Session::get('budget_items', []);
        
        if (empty() || empty()) {
            return redirect()->route('budget.start')->with('error', 'Nenhum dado de orçamento encontrado.');
        }

        return view('budgets.wizard.confirm');
    }
"""
new="""public function confirm()
    {
         = Session::get('budget_data', []);
         = Session::get('budget_items', []);
        
        if (empty() || empty()) {
            return redirect()->route('budget.start')->with('error', 'Nenhum dado de orçamento encontrado.');
        }

        // Texto padrão de observações do admin (editável em Configurações > Empresa > Termos)
         = \App\Models\CompanySetting::getSettings(auth()->user()->store_id ?? null);
         = ->terms_conditions;
        // Fallback simples garantindo prazo / acréscimo de tamanhos / pagamento
        if (empty()) {
             = "PRAZO: informar quantidade de dias úteis.\nACRÉSCIMO DE TAMANHOS: aplicar valor adicional em GG / EXG / Especial conforme tabela.\nFORMA DE PAGAMENTO: entrada + restante na entrega (ou conforme combinado).\nOBS: edite este texto em Configurações > Empresa > Termos.";
        }

        return view('budgets.wizard.confirm', compact('defaultAdminNotes'));
    }
"""
if old not in t:
    raise SystemExit('pattern not found')
p.write_text(t.replace(old,new), encoding='utf-8')
