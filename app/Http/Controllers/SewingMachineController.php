<?php

namespace App\Http\Controllers;

use App\Models\SewingMachine;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SewingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = SewingMachine::with('store');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('worker_name', 'like', "%{$search}%")
                  ->orWhere('internal_code', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $machines = $query->latest()->paginate(10)->withQueryString();
        $stores = Store::all();

        return view('sewing-machines.index', compact('machines', 'stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::all();
        return view('sewing-machines.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'worker_name' => 'nullable|string|max:255',
            'internal_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:sewing_machines,serial_number',
            'status' => 'required|in:active,maintenance,broken,disposed',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        SewingMachine::create($validated);

        return redirect()->route('sewing-machines.index')
            ->with('success', 'Máquina cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SewingMachine $sewingMachine): View
    {
        $stores = Store::all();
        return view('sewing-machines.edit', compact('sewingMachine', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SewingMachine $sewingMachine): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'worker_name' => 'nullable|string|max:255',
            'internal_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:sewing_machines,serial_number,' . $sewingMachine->id,
            'status' => 'required|in:active,maintenance,broken,disposed',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $sewingMachine->update($validated);

        return redirect()->route('sewing-machines.index')
            ->with('success', 'Máquina atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SewingMachine $sewingMachine): RedirectResponse
    {
        $sewingMachine->delete();

        return redirect()->route('sewing-machines.index')
            ->with('success', 'Máquina removida com sucesso.');
    }
}
