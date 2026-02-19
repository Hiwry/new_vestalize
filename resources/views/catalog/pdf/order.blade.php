<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pedido #{{ $catalogOrder->order_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5; /* Indigo 600 */
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 30px;
            width: 100%;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            width: 30%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #475569;
        }
        .items-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals table {
            float: right;
            width: 40%;
        }
        .totals td {
            padding: 5px;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background-color: #94a3b8;
        }
        .badge-pending { background-color: #f59e0b; }
        .badge-approved { background-color: #10b981; }
        .badge-rejected { background-color: #ef4444; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $store->name }}</h1>
        <p>Comprovante de Pedido</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-label">Detalhes do Pedido</div>
                    <div><strong>Código:</strong> #{{ $catalogOrder->order_code }}</div>
                    <div><strong>Data:</strong> {{ $catalogOrder->created_at->format('d/m/Y H:i') }}</div>
                    <div style="margin-top: 5px;">
                        <strong>Status:</strong> 
                        <span class="badge badge-{{ $catalogOrder->status }}">
                            {{ strtoupper($catalogOrder->status_label) }}
                        </span>
                    </div>
                </td>
                <td>
                    <div class="info-label">Dados do Cliente</div>
                    <div><strong>Nome:</strong> {{ $catalogOrder->customer_name }}</div>
                    <div><strong>Telefone:</strong> {{ $catalogOrder->customer_phone }}</div>
                    @if($catalogOrder->customer_email)
                        <div><strong>E-mail:</strong> {{ $catalogOrder->customer_email }}</div>
                    @endif
                    @if($catalogOrder->customer_cpf)
                        <div><strong>CPF:</strong> {{ $catalogOrder->customer_cpf }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="40%">Produto</th>
                <th width="20%">Detalhes</th>
                <th width="15%" style="text-align: center;">Qtd</th>
                <th width="25%" style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($catalogOrder->items as $item)
                <tr>
                    <td>{{ $item['title'] }}</td>
                    <td>
                        @if(!empty($item['size']))
                            <div>Tam: {{ $item['size'] }}</div>
                        @endif
                        @if(!empty($item['color']))
                            <div>Cor: {{ $item['color'] }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item['quantity'] }}</td>
                    <td style="text-align: right;">R$ {{ number_format($item['total'] ?? 0, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">R$ {{ number_format($catalogOrder->subtotal, 2, ',', '.') }}</td>
            </tr>
            @if($catalogOrder->discount > 0)
            <tr>
                <td>Desconto:</td>
                <td style="text-align: right;">- R$ {{ number_format($catalogOrder->discount, 2, ',', '.') }}</td>
            </tr>
            @endif
            @if($catalogOrder->delivery_fee > 0)
            <tr>
                <td>Entrega:</td>
                <td style="text-align: right;">+ R$ {{ number_format($catalogOrder->delivery_fee, 2, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total:</td>
                <td style="text-align: right;">R$ {{ number_format($catalogOrder->total, 2, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    @if($catalogOrder->notes)
        <div style="margin-top: 30px; background-color: #f9fafb; padding: 15px; border-radius: 5px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Observações:</div>
            <div>{{ $catalogOrder->notes }}</div>
        </div>
    @endif

    <div class="footer">
        <p>Obrigado pela preferência!</p>
        <p>{{ $store->name }} - Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
