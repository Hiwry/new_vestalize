<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequestSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PublicQuoteController extends Controller
{
    public function show($slug)
    {
        $settings = QuoteRequestSetting::where('slug', $slug)
            ->where('is_active', true)
            ->with('tenant')
            ->firstOrFail();

        // Check if tenant has access (double check)
        if (!$settings->tenant->canAccess('external_quote')) {
            abort(404);
        }

        return view('public.quote.wizard', compact('settings'));
    }

    public function submit(Request $request, $slug)
    {
        $settings = QuoteRequestSetting::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'step' => 'required', // Just to confirm submission type
            'product_index' => 'required|integer',
            'quantity' => 'required|string',
            'quantity_other' => 'nullable|string',
            'has_logo' => 'boolean',
            'logo_upload' => 'nullable|file|image|max:10240', // 10MB max
            'contact_name' => 'required|string',
            'contact_phone' => 'required|string',
            'contact_company' => 'nullable|string',
        ]);

        // Handle file upload if present
        $logoPath = null;
        if ($request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $filename = 'quote_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            // Store in public disk to be accessible by PDF generator if needed
            // Or usually temporary storage. For simplicity let's store in public/quotes
            $logoPath = $file->storeAs('quotes/logos', $filename, 'public');
        }

        $data = [
            'product' => $settings->products_json[$validated['product_index']] ?? ['name' => 'Desconhecido', 'icon' => 't-shirt'],
            'quantity' => $validated['quantity'] === 'other' ? $validated['quantity_other'] . ' peças (Outra quantidade)' : $validated['quantity'] . ' peças',
            'has_logo' => $request->has('logo_upload'), // Use uploaded file presence for logic
            'logo_path' => $logoPath,
            'contact' => [
                'name' => $validated['contact_name'],
                'phone' => $validated['contact_phone'],
                'company' => $validated['contact_company'],
            ],
            'date' => now()->format('d/m/Y H:i'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('public.quote.pdf', compact('settings', 'data'));
        
        // Save PDF temporarily to provide download link
        $pdfFilename = 'orcamento_' . time() . '.pdf';
        $pdfPath = 'quotes/pdfs/' . $pdfFilename;
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $pdfUrl = Storage::disk('public')->url($pdfPath);

        // Prepare WhatsApp Link
        $phone = preg_replace('/[^0-9]/', '', $settings->whatsapp_number);
        $message = "*Solicitação de Orçamento - {$settings->title}*\n\n" .
                   "*Dados do Pedido:*\n" .
                   "• Produto: {$data['product']['name']}\n" .
                   "• Quantidade: {$data['quantity']}\n" .
                   "• Empresa: " . ($data['contact']['company'] ?? 'N/A') . "\n" .
                   "• WhatsApp: {$data['contact']['phone']}\n" .
                   "• Logo: " . ($data['has_logo'] ? 'Anexado' : 'Não possui') . "\n\n" .
                   "📄 *Baixe o PDF do Orçamento aqui:* " . $pdfUrl . "\n\n" .
                   "Gostaria de receber um orçamento detalhado para este pedido. Obrigado!";
        
        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'pdf_url' => $pdfUrl,
            'whatsapp_url' => $whatsappUrl
        ]);
    }
}
