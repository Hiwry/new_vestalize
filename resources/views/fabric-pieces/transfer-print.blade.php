<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Transferência #{{ $transfer->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #ccc;
            padding: 10px;
        }
        .info-box h3 {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-box p {
            font-size: 14px;
            font-weight: bold;
        }
        .transfer-number {
            text-align: center;
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }
        .transfer-number span {
            font-size: 20px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background: #f5f5f5;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            font-size: 12px;
        }
        .signature-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 40px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .notes {
            background: #fffde7;
            border: 1px solid #ffc107;
            padding: 10px;
            margin-bottom: 20px;
        }
        .notes h4 {
            font-size: 11px;
            margin-bottom: 5px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
        }
        .print-button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimir</button>

    <div class="header">
        <h1>NOTA DE TRANSFERÊNCIA</h1>
        <p class="subtitle">Peças de Tecido - Transferência Entre Lojas</p>
    </div>

    <div class="transfer-number">
        <span>Nº {{ str_pad($transfer->id, 6, '0', STR_PAD_LEFT) }}</span>
        <br>
        <small>{{ $transfer->created_at->format('d/m/Y H:i') }}</small>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Origem</h3>
            <p>{{ $transfer->fromStore->name ?? 'N/A' }}</p>
        </div>
        <div class="info-box">
            <h3>Destino</h3>
            <p>{{ $transfer->toStore->name ?? 'N/A' }}</p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 80px;">Código</th>
                <th>Tipo de Tecido</th>
                <th>Cor</th>
                <th>Fornecedor</th>
                <th>NF</th>
                <th style="width: 80px;">Peso (kg)</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>#{{ $transfer->fabricPiece->id }}</strong></td>
                <td>{{ $transfer->fabricPiece->fabricType->name ?? '-' }}</td>
                <td>{{ $transfer->fabricPiece->color->name ?? '-' }}</td>
                <td>{{ $transfer->fabricPiece->supplier ?? '-' }}</td>
                <td>{{ $transfer->fabricPiece->invoice_number ?? '-' }}</td>
                <td style="text-align: center;">{{ $transfer->fabricPiece->weight ? number_format($transfer->fabricPiece->weight, 2, ',', '.') : '-' }}</td>
                <td style="text-align: center;">{{ $transfer->fabricPiece->status_label }}</td>
            </tr>
        </tbody>
    </table>

    @if($transfer->request_notes)
    <div class="notes">
        <h4>Observações:</h4>
        <p>{{ $transfer->request_notes }}</p>
    </div>
    @endif

    <div class="info-grid">
        <div class="info-box">
            <h3>Transferido por</h3>
            <p>{{ $transfer->requestedBy->name ?? 'N/A' }}</p>
            <small>{{ $transfer->requested_at?->format('d/m/Y H:i') }}</small>
        </div>
        <div class="info-box">
            <h3>Recebido em</h3>
            <p>{{ $transfer->received_at?->format('d/m/Y H:i') ?? 'Pendente' }}</p>
        </div>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line">
                Assinatura Remetente
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Assinatura Destinatário
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Sistema de Gestão Vestalize</p>
    </div>

    <script>
        // Auto-print quando abrir (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
