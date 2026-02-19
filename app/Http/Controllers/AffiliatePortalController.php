<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliatePortalController extends Controller
{
    public function dashboard()
    {
        $affiliate = $this->getAffiliateForUser();

        $affiliate->load(['tenants.currentPlan']);
        $clicksCount = $affiliate->clicks()->count();
        $totalReferrals = $affiliate->tenants()->count();
        $activeReferrals = $affiliate->paidReferralsCount();
        $conversionRate = $clicksCount > 0
            ? round(($totalReferrals / $clicksCount) * 100, 2)
            : 0;

        $stats = [
            'total_referrals' => $totalReferrals,
            'active_referrals' => $activeReferrals,
            'total_earnings' => $affiliate->total_earnings,
            'pending_balance' => $affiliate->pending_balance,
            'withdrawn_balance' => $affiliate->withdrawn_balance,
            'clicks' => $clicksCount,
            'conversion_rate' => $conversionRate,
        ];

        $referralLink = route('register.public', ['ref' => $affiliate->code]);

        return view('affiliate.dashboard', compact('affiliate', 'stats', 'referralLink'));
    }

    public function referrals()
    {
        $affiliate = $this->getAffiliateForUser();

        $referrals = $affiliate->tenants()
            ->with('currentPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('affiliate.referrals', compact('affiliate', 'referrals'));
    }

    public function redirect(Request $request, string $code)
    {
        $affiliate = Affiliate::active()->byCode($code)->first();

        if ($affiliate) {
            $this->logClick($request, $affiliate);
            return redirect()->route('register.public', ['ref' => $affiliate->code]);
        }

        return redirect()->route('register.public');
    }

    private function getAffiliateForUser(): Affiliate
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->first();
        if (!$affiliate) {
            abort(403, 'Acesso negado.');
        }

        return $affiliate;
    }

    private function logClick(Request $request, Affiliate $affiliate): void
    {
        $sessionKey = 'affiliate_click_' . $affiliate->code;
        if ($request->session()->has($sessionKey)) {
            return;
        }

        AffiliateClick::create([
            'affiliate_id' => $affiliate->id,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'landing_url' => $request->fullUrl(),
        ]);

        $request->session()->put($sessionKey, true);
    }
}
