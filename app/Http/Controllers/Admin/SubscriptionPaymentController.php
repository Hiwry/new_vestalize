<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = SubscriptionPayment::with(['tenant', 'plan'])
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.subscription-payments.index', compact('payments'));
    }
}
