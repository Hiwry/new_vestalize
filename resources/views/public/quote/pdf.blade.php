<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $settings->title }} - Orçamento</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid {{ $settings->primary_color }};
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: {{ $settings->primary_color }};
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .grid {
            width: 100%;
            border-collapse: collapse;
        }
        .grid td {
            vertical-align: top;
            padding: 10px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            font-size: 12px;
            text-transform: uppercase;
        }
        .value {
            font-size: 16px;
        }
        .product-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .logo-img {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">{{ $settings->title }}</h1>
            <p class="subtitle">Solicitação de Orçamento #{{ time() }}</p>
        </div>

        <div class="section">
            <h2 class="section-title">Dados do Cliente</h2>
            <table class="grid">
                <tr>
                    <td width="33%">
                        <span class="label">Nome</span>
                        <span class="value">{{ $data['contact']['name'] }}</span>
                    </td>
                    <td width="33%">
                        <span class="label">WhatsApp</span>
                        <span class="value">{{ $data['contact']['phone'] }}</span>
                    </td>
                    <td width="33%">
                        <span class="label">Empresa</span>
                        <span class="value">{{ $data['contact']['company'] ?? 'Não informado' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2 class="section-title">Detalhes do Pedido</h2>
            <div class="product-card">
                <table class="grid">
                    <tr>
                        <td width="50%">
                            <span class="label">Produto Selecionado</span>
                            <div class="value" style="color: {{ $settings->primary_color }}; font-weight: bold; margin-top: 5px;">
                                {{ $data['product']['name'] }}
                            </div>
                            <div style="font-size: 12px; color: #666;">{{ $data['product']['description'] ?? '' }}</div>
                        </td>
                        <td width="25%">
                            <span class="label">Quantidade</span>
                            <span class="value">{{ $data['quantity'] }}</span>
                        </td>
                        <td width="25%">
                            <span class="label">Data da Solicitação</span>
                            <span class="value">{{ $data['date'] }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($data['has_logo'] && $data['logo_path'])
        <div class="section">
            <h2 class="section-title">Logo Anexada</h2>
            <div style="text-align: center; padding: 20px; background: #fff; border: 1px dashed #ccc;">
                <!-- Display logo if it's an image. If it's PDF, show icon -->
                @php
                    $ext = pathinfo($data['logo_path'], PATHINFO_EXTENSION);
                @endphp
                
                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                    <img src="{{ public_path('storage/' . $data['logo_path']) }}" style="max-width: 300px; max-height: 200px;">
                @else
                    <p>Arquivo anexado: {{ basename($data['logo_path']) }}</p>
                @endif
            </div>
        </div>
        @endif

        <div class="footer">
            <p>Este documento é uma solicitação de orçamento e não garante reserva de estoque ou preço.</p>
            <p>{{ $settings->tenant->name ?? 'Vestalize' }} - Gerado em {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
