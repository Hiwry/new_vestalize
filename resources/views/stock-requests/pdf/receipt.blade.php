<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovante de Separação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .signature-box {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = null;
        if (isset($companySettings->logo_path) && $companySettings->logo_path && file_exists(public_path($companySettings->logo_path))) {
            $logoPath = public_path($companySettings->logo_path);
        } else {
            $logoPath = public_path('vestalize.svg');
        }

        $referenceRequest = isset($items) && $items instanceof \Illuminate\Support\Collection && $items->isNotEmpty()
            ? $items->first()
            : null;

        $catalogCode = null;
        $referenceNotes = $referenceRequest->request_notes ?? null;
        if ($referenceNotes && preg_match('/(CAT-[A-Za-z0-9]+)/i', $referenceNotes, $matches)) {
            $catalogCode = strtoupper($matches[1]);
        }

        $isManualWithdrawal = $referenceNotes && str_contains(strtoupper($referenceNotes), '[RETIRADA]');
    @endphp

    <div class="header">
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; vertical-align: middle; width: 30%; text-align: left;">
                    @if($logoPath && file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo" style="max-height: 60px; max-width: 150px;">
                    @endif
                </div>
                <div style="display: table-cell; vertical-align: middle; width: 70%; text-align: right;">
                    <div class="company-name">{{ $companySettings->company_name ?? 'Vestalize' }}</div>
                    <div class="document-title">Comprovante de Separação de Estoque</div>
                    <div>Data: {{ date('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Pedido:</span> 
            @if($order)
                #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} 
                @if($order->is_pdv) (PDV) @endif
            @elseif($catalogCode)
                Pedido Catálogo: {{ $catalogCode }}
            @elseif($isManualWithdrawal)
                Venda/Retirada Avulsa
            @else
                Transferência Avulsa
            @endif
        </div>
        
        @if($order && $order->client)
        <div class="info-row">
            <span class="label">Cliente:</span> {{ $order->client->name }}
        </div>
        @endif

        <div class="info-row">
            <span class="label">Separado por:</span> {{ $approver->name ?? 'N/A' }}
        </div>
        
        <div class="info-row">
            <span class="label">Loja de Saída (Origem):</span> {{ $store->name ?? 'N/A' }}
        </div>
        
        @if($targetStore)
        <div class="info-row">
            <span class="label">Loja de Destino:</span> {{ $targetStore->name }}
        </div>
        @endif
    </div>

    <h3>Itens Separados</h3>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Cor</th>
                <th>Corte</th>
                <th>Tamanho</th>
                <th style="text-align: center;">Qtd. Aprovada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->fabric->name ?? '-' }}</td>
                <td>{{ $item->color->name ?? '-' }}</td>
                <td>{{ $item->cutType->name ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item->size }}</td>
                <td style="text-align: center;">{{ $item->approved_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total de Peças:</td>
                <td style="text-align: center; font-weight: bold;">{{ $totalQuantity }}</td>
            </tr>
        </tfoot>
    </table>

    @if($notes)
    <div class="info-section">
        <span class="label">Observações:</span>
        <p>{{ $notes }}</p>
    </div>
    @endif

    <div class="signature-box">
        <div class="signature-line">
            Responsável pela Conferência
        </div>
    </div>

    <div class="footer">
        Gerado automaticamente pelo sistema em {{ date('d/m/Y H:i:s') }}
        <br>
        {{ $companySettings->company_website ?? '' }}
    </div>
</body>
</html>
