<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Pedidos - Produção</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
            font-size: 9px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Cores para status */
        .row-yellow {
            background-color: #ffff99;
        }
        
        .row-orange {
            background-color: #ffcc99;
        }
        
        .row-red {
            background-color: #ff9999;
        }
        
        .evento-badge {
            background-color: #ff0000;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
            margin-left: 5px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            display: inline-block;
        }
        
        .atrasado {
            background-color: #ff9999;
            color: #8b0000;
            font-weight: bold;
        }
        
        .col-vendedor { width: 8%; }
        .col-descricao { width: 22%; }
        .col-os { width: 8%; }
        .col-servico { width: 15%; }
        .col-qt { width: 5%; }
        .col-data { width: 10%; }
        .col-classificacao { width: 12%; }
        .col-status { width: 12%; }
        .col-obs { width: 8%; }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>LISTA DE PEDIDOS 
        @if($startDate && $endDate)
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        @endif
        @if($selectedStore)
            - {{ strtoupper($selectedStore->name) }}
        @endif
    </h1>
    
    <table>
        <thead>
            <tr>
                <th class="col-vendedor">VENDEDOR</th>
                <th class="col-descricao">DESCRIÇÃO</th>
                <th class="col-os">OS</th>
                <th class="col-servico">SERVIÇO</th>
                <th class="col-qt">QT</th>
                <th class="col-data">DATA DE ENT.</th>
                <th class="col-status">STATUS</th>
                <th class="col-classificacao">LOJA</th>
            </tr>
        </thead>
        <tbody>
            @php
                $hoje = \Carbon\Carbon::now()->startOfDay();
            @endphp
            
            @foreach($orders as $order)
                @php
                    $firstItem = $order->items->first();
                    $artName = $firstItem?->art_name ?? ($order->client ? $order->client->name : 'Cliente não encontrado');
                    $deliveryDate = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date) : null;
                    $isDelayed = $deliveryDate && $deliveryDate->lt($hoje);
                    
                    // Definir cor da linha baseado no status ou atraso
                    $rowClass = '';
                    if ($order->is_event) {
                        $rowClass = 'row-red';
                    } elseif ($isDelayed) {
                        $rowClass = 'row-orange';
                    } else {
                        $rowClass = 'row-yellow';
                    }
                @endphp
                
                <tr class="{{ $rowClass }}">
                    <td>{{ strtoupper($order->seller ?? 'N/A') }}</td>
                    <td>
                        <strong>{{ $artName }}</strong>
                        @if($firstItem)
                            <br>
                            <small>
                                {{ $firstItem->fabric }}
                                @if($firstItem->color)
                                    / {{ $firstItem->color }}
                                @endif
                            </small>
                        @endif
                    </td>
                    <td class="text-center">{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-center">
                        @if($firstItem)
                            {{ strtoupper($firstItem->print_type) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $order->items->sum('quantity') }}</strong></td>
                    <td class="text-center">
                        @if($deliveryDate)
                            {{ $deliveryDate->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="status-badge" style="background-color: {{ $order->status->color }}; color: white;">
                            {{ $order->status->name }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($order->store)
                            {{ $order->store->name }}
                        @elseif($order->store_id)
                            {{ \App\Models\Store::find($order->store_id)?->name ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            
            @if($orders->isEmpty())
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">
                        Nenhum pedido encontrado para o período selecionado.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <div class="footer">
        Total de pedidos: <strong>{{ $orders->count() }}</strong> | 
        Total de peças: <strong>{{ $orders->sum(function($order) { return $order->items->sum('quantity'); }) }}</strong> |
        Gerado em: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

