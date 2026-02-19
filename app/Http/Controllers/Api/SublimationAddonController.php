<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SublimationAddon;
use Illuminate\Http\JsonResponse;

class SublimationAddonController extends Controller
{
    /**
     * Buscar todos os adicionais de sublimação ativos
     */
    public function getAddons(): JsonResponse
    {
        $addons = SublimationAddon::getActiveAddons();
        
        return response()->json([
            'success' => true,
            'data' => $addons,
            'count' => $addons->count()
        ]);
    }
}