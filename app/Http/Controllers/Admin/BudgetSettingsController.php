<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\BudgetObservationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetSettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $tenant = $user?->tenant;
        $tenantId = $user?->tenant_id;

        $options = BudgetObservationHelper::getOptions($tenantId);
        $optionsText = implode("\n", $options);

        return view('admin.budget-settings', [
            'options' => $options,
            'optionsText' => $optionsText,
            'tenant' => $tenant,
            'tenantId' => $tenantId,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'options' => 'nullable|string|max:4000',
        ]);

        $options = BudgetObservationHelper::parseOptionsFromText($validated['options'] ?? '');
        BudgetObservationHelper::saveOptions($options, Auth::user()->tenant_id);

        return redirect()
            ->route('admin.budget-settings.edit')
            ->with('success', 'Observações atualizadas com sucesso!');
    }
}
