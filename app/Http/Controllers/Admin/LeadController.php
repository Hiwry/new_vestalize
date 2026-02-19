<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::latest()->paginate(20);
        return view('admin.leads.index', compact('leads'));
    }

    /**
     * Excluir leads selecionados em lote
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:leads,id',
        ]);

        $count = Lead::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} lead(s) excluído(s) com sucesso."
        ]);
    }

    /**
     * Excluir um lead individual
     */
    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->back()->with('success', 'Lead excluído com sucesso.');
    }
}
