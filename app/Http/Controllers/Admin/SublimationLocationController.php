<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SublimationLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SublimationLocationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $nextOrder = (SublimationLocation::max('order') ?? 0) + 1;

        SublimationLocation::create([
            'name' => $data['name'],
            'order' => $nextOrder,
            'active' => true,
        ]);

        return redirect()->back()->with('success', 'LocalizaÇõÇœo adicionada com sucesso.');
    }

    public function destroy(SublimationLocation $location): RedirectResponse
    {
        $location->delete();

        return redirect()->back()->with('success', 'LocalizaÇõÇœo removida com sucesso.');
    }

    public function toggle(Request $request, SublimationLocation $location): RedirectResponse
    {
        $location->update([
            'active' => $request->boolean('active'),
        ]);

        return redirect()->back()->with('success', 'Status da localização atualizado.');
    }

    public function togglePdf(Request $request, SublimationLocation $location): RedirectResponse
    {
        $location->update([
            'show_in_pdf' => !$location->show_in_pdf,
        ]);

        return redirect()->back()->with('success', 'Visibilidade no PDF atualizada.');
    }
}
