<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Transferência</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
        }
        .urgent-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 12px;
        }
        .content {
            padding: 24px;
        }
        .info-box {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            border-left: 4px solid #f97316;
        }
        .info-box h3 {
            margin: 0 0 8px;
            color: #111827;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-size: 14px;
        }
        .info-value {
            color: #111827;
            font-weight: 600;
            font-size: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        .items-table th {
            background: #f3f4f6;
            padding: 12px 8px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }
        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .size-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }
        .quantity {
            font-weight: 700;
            color: #f97316;
        }
        .total-row {
            background: #fef3c7;
        }
        .total-row td {
            font-weight: 700;
            color: #92400e;
        }
        .action-button {
            display: block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 24px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        .arrow-icon {
            display: inline-block;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Solicitação de Transferência</h1>
            <p>Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            <span class="urgent-badge">⚡ URGENTE</span>
        </div>
        
        <div class="content">
            <div class="info-box">
                <h3>📦 Detalhes da Transferência</h3>
                <div class="info-row">
                    <span class="info-label">De:</span>
                    <span class="info-value">{{ $sourceStore->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Para:</span>
                    <span class="info-value">{{ $destinationStore->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total de Peças:</span>
                    <span class="info-value" style="color: #f97316;">{{ $totalQuantity }}</span>
                </div>
            </div>

            <h3 style="color: #111827; margin-bottom: 12px;">📋 Itens Solicitados</h3>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Tamanho</th>
                        <th>Quantidade</th>
                        <th>Produto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><span class="size-badge">{{ $item['size'] }}</span></td>
                        <td class="quantity">{{ $item['quantity'] }}</td>
                        <td>{{ $item['product'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td><strong>{{ $totalQuantity }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <a href="{{ route('stock-requests.index') }}" class="action-button">
                Ver Solicitações de Estoque →
            </a>

            <p style="font-size: 14px; color: #6b7280; text-align: center;">
                Por favor, separe os itens acima e confirme a transferência no sistema.
            </p>
        </div>
        
        <div class="footer">
            <p>Este é um e-mail automático. Por favor, não responda.</p>
            <p>{{ config('app.name') }} - Sistema de Gestão</p>
        </div>
    </div>
</body>
</html>
