<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\TenantInvoiceConfig;
use App\Services\FocusNfeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Listar todas as invoices do tenant
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Super Admin (tenant_id === null) não deve ver dados de outros tenants
        if ($user->tenant_id === null) {
            return view('admin.invoices.index', [
                'invoices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'isSuperAdmin' => true
            ]);
        }

        $invoices = Invoice::with(['order.client'])
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Emitir NF-e para um pedido
     */
    public function emit(Request $request, $orderId)
    {
        $order = Order::with(['client', 'items', 'payments'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($orderId);

        // Verificar se já existe invoice para este pedido
        $existingInvoice = Invoice::where('order_id', $order->id)
            ->whereIn('status', [Invoice::STATUS_AUTHORIZED, Invoice::STATUS_PROCESSING])
            ->first();

        if ($existingInvoice) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido já possui uma NF-e: ' . $existingInvoice->numero,
                ]);
            }
            return back()->with('error', 'Este pedido já possui uma NF-e emitida.');
        }

        // Obter configuração do tenant
        $config = TenantInvoiceConfig::where('tenant_id', Auth::user()->tenant_id)->first();

        if (!$config) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configure suas credenciais de NF-e antes de emitir.',
                ]);
            }
            return redirect()->route('admin.invoice-config.edit')
                ->with('error', 'Configure suas credenciais de NF-e antes de emitir.');
        }

        // Emitir NF-e
        $service = new FocusNfeService($config);
        $result = $service->emitirNfe($order);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Listar invoices de um pedido
     */
    public function show($orderId)
    {
        $order = Order::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($orderId);

        $invoices = Invoice::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'invoices' => $invoices,
            'order' => $order,
        ]);
    }

    /**
     * Consultar status de uma NF-e
     */
    public function checkStatus($invoiceId)
    {
        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($invoiceId);

        $config = TenantInvoiceConfig::where('tenant_id', Auth::user()->tenant_id)->first();
        
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Configuração não encontrada.']);
        }

        $service = new FocusNfeService($config);
        $result = $service->consultarNfe($invoice);

        return response()->json($result);
    }

    /**
     * Cancelar NF-e
     */
    public function cancel(Request $request, $invoiceId)
    {
        $request->validate([
            'justificativa' => 'required|string|min:15|max:255',
        ]);

        $invoice = Invoice::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($invoiceId);

        $config = TenantInvoiceConfig::where('tenant_id', Auth::user()->tenant_id)->first();
        
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Configuração não encontrada.']);
        }

        $service = new FocusNfeService($config);
        $result = $service->cancelarNfe($invoice, $request->justificativa);

        return response()->json($result);
    }
}
