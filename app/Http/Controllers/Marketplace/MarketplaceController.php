<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\DesignerProfile;
use App\Models\Marketplace\MarketplaceCreditWallet;
use App\Models\Marketplace\MarketplaceService;
use App\Models\Marketplace\MarketplaceTool;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function index()
    {
        $featuredServices = MarketplaceService::with(['designer', 'images'])
            ->active()
            ->featured()
            ->latest()
            ->take(6)
            ->get();

        $latestServices = MarketplaceService::with(['designer', 'images'])
            ->active()
            ->latest()
            ->take(12)
            ->get();

        $featuredTools = MarketplaceTool::with('images')
            ->active()
            ->featured()
            ->latest()
            ->take(6)
            ->get();

        $topDesigners = DesignerProfile::with('user')
            ->active()
            ->orderByDesc('rating_average')
            ->orderByDesc('total_sales')
            ->take(6)
            ->get();

        $userWallet = null;
        if (Auth::check()) {
            $userWallet = MarketplaceCreditWallet::getOrCreate(Auth::id());
        }

        $serviceCategories = MarketplaceService::$categoryLabels;
        $toolCategories    = MarketplaceTool::$categoryLabels;

        return view('marketplace.home', compact(
            'featuredServices',
            'latestServices',
            'featuredTools',
            'topDesigners',
            'userWallet',
            'serviceCategories',
            'toolCategories'
        ));
    }

    public function designers()
    {
        $designers = DesignerProfile::with(['user', 'services'])
            ->active()
            ->orderByDesc('rating_average')
            ->paginate(16);

        return view('marketplace.designers.index', compact('designers'));
    }
}
