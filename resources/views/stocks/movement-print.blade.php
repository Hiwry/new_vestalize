<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Movimentação #{{ $movement->id }}</title>
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
        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .type-transferencia { background: #FFA500; color: white; }
        .type-pedido { background: #4CAF50; color: white; }
        .type-remocao { background: #f44336; color: white; }
        .type-entrada { background: #2196F3; color: white; }
        .type-devolucao { background: #9C27B0; color: white; }
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
        .movement-number {
            text-align: center;
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }
        .movement-number span {
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
        .items-table tfoot td {
            font-weight: bold;
            background: #f9f9f9;
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
        .order-info {
            background: #e3f2fd;
            border: 1px solid #2196F3;
            padding: 10px;
            margin-bottom: 20px;
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
    <button class="print-button no-print" onclick="window.print()"> Imprimir</button>

    <div class="header">
        <h1>NOTA DE MOVIMENTAÇÃO DE ESTOQUE</h1>
        <p class="subtitle">{{ $movement->type_label }}</p>
        <span class="type-badge type-{{ $movement->type }}">{{ strtoupper($movement->type_label) }}</span>
    </div>

    <div class="movement-number">
        <span>Nº {{ str_pad($movement->id, 6, '0', STR_PAD_LEFT) }}</span>
        <br>
        <small>{{ $movement->created_at->format('d/m/Y H:i') }}</small>
    </div>

    <div class="info-grid">
        @if($movement->from_store_id)
        <div class="info-box">
            <h3>{{ $movement->type === 'transferencia' ? 'Origem' : 'Loja' }}</h3>
            <p>{{ $movement->fromStore->name ?? 'N/A' }}</p>
        </div>
        @endif
        @if($movement->to_store_id)
        <div class="info-box">
            <h3>Destino</h3>
            <p>{{ $movement->toStore->name ?? 'N/A' }}</p>
        </div>
        @endif
    </div>

    @if($movement->order_id)
    <div class="order-info">
        <strong>Pedido:</strong> #{{ $movement->order_id }}
        @if($movement->order)
            - {{ $movement->order->client_name ?? 'Cliente não informado' }}
        @endif
    </div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Descrição</th>
                <th style="width: 80px;">Tamanho</th>
                <th style="width: 80px; text-align: center;">Qtd</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movement->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->size ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td style="text-align: center;">{{ $movement->total_quantity }}</td>
            </tr>
        </tfoot>
    </table>

    @if($movement->notes)
    <div class="notes">
        <h4>Observações:</h4>
        <p>{{ $movement->notes }}</p>
    </div>
    @endif

    <div class="info-grid">
        <div class="info-box">
            <h3>Responsável</h3>
            <p>{{ $movement->user->name ?? 'N/A' }}</p>
            <small>{{ $movement->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="info-box">
            <h3>Total de Itens</h3>
            <p>{{ $movement->items->count() }} tipo(s) / {{ $movement->total_quantity }} unid.</p>
        </div>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line">
                Assinatura {{ $movement->type === 'transferencia' ? 'Remetente' : 'Responsável' }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Assinatura {{ $movement->type === 'transferencia' ? 'Destinatário' : 'Conferente' }}
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
