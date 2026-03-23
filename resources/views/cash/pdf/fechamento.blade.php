<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Fechamento de Caixa</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; background: #fff; }

/* ---------- HELPERS ---------- */
.br { page-break-after: always; }
h2.page-title {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    background: #ddd;
    padding: 5px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.period-label {
    text-align: center;
    font-size: 10px;
    color: #555;
    margin-bottom: 10px;
}
.company-header {
    text-align: center;
    margin-bottom: 10px;
}
.company-header img { max-height: 40px; }
.company-header .company-name { font-size: 12px; font-weight: bold; }

/* ---------- PÁG 1 & 3: RESUMO ---------- */
.summary-wrapper {
    display: table;
    width: 100%;
}
.summary-left, .summary-mid, .summary-right {
    display: table-cell;
    vertical-align: top;
    padding: 0 4px;
}
.summary-left  { width: 45%; }
.summary-mid   { width: 18%; }
.summary-right { width: 37%; }

table.res {
    width: 100%;
    border-collapse: collapse;
    font-size: 9px;
}
table.res th {
    background: #bbb;
    font-weight: bold;
    text-align: center;
    padding: 3px 4px;
    border: 1px solid #888;
    font-size: 10px;
}
table.res td {
    border: 1px solid #ccc;
    padding: 2px 4px;
}
table.res td.label  { font-weight: normal; }
table.res td.value  { text-align: right; white-space: nowrap; }
table.res tr.total-row td {
    background: #FFD700;
    font-weight: bold;
}
table.res tr.section-header td {
    background: #ddd;
    font-weight: bold;
    font-style: italic;
    text-align: center;
    color: #900;
}
table.res tr.saldo-row td {
    background: #FF4444;
    color: #fff;
    font-weight: bold;
}
table.res tr.sangria-row td {
    background: #FF8C00;
    font-weight: bold;
}
table.res tr.suprimento-row td {
    background: #6699CC;
    color: #fff;
    font-weight: bold;
}

/* ---------- PÁG 2: DETALHAMENTO ---------- */
.detail-table-wrapper { overflow-x: auto; }
table.det {
    width: 100%;
    border-collapse: collapse;
    font-size: 8px;
}
table.det th {
    background: #333;
    color: #fff;
    padding: 3px 4px;
    border: 1px solid #555;
    text-align: center;
    white-space: nowrap;
    font-size: 7.5px;
}
table.det th.vend-col { background: #5c1a8c; }
table.det th.total-col { background: #1a5c1a; }
table.det td {
    border: 1px solid #ccc;
    padding: 2px 4px;
    text-align: right;
    white-space: nowrap;
}
table.det td.label-col { text-align: left; font-weight: bold; color: #333; }
table.det tr.tot-row td { background: #FFD700; font-weight: bold; }
table.det tr.card-row td { color: #c00; }
table.det tr.subtotais td { background: #eee; font-weight: bold; }

table.mov {
    width: 100%;
    border-collapse: collapse;
    font-size: 8px;
}
table.mov th {
    background: #2f4858;
    color: #fff;
    border: 1px solid #555;
    padding: 4px;
    text-align: left;
}
table.mov td {
    border: 1px solid #ccc;
    padding: 3px 4px;
    vertical-align: top;
}
table.mov td.amount {
    text-align: right;
    white-space: nowrap;
}
table.mov td.small {
    white-space: nowrap;
}
table.mov td.wrap {
    white-space: normal;
}
table.mov tr.total-row td {
    background: #FFD700;
    font-weight: bold;
}
.section-note {
    font-size: 9px;
    color: #555;
    margin-bottom: 8px;
}
</style>
</head>
<body>

@php
use Carbon\Carbon;

$fmt = fn($v) => 'R$ ' . number_format(floatval($v), 2, ',', '.');
$fmtOrDash = fn($v) => ($v == 0) ? '-' : 'R$ ' . number_format(floatval($v), 2, ',', '.');

$totalCartao = ($paymentTotals['visa_credito'] ?? 0)
             + ($paymentTotals['visa_debito'] ?? 0)
             + ($paymentTotals['master_credito'] ?? 0)
             + ($paymentTotals['master_debito'] ?? 0)
             + ($paymentTotals['elo_credito'] ?? 0)
             + ($paymentTotals['elo_debito'] ?? 0)
             + ($paymentTotals['hiper'] ?? 0)
             + ($paymentTotals['amex'] ?? 0)
             + ($paymentTotals['outros_credito'] ?? 0)
             + ($paymentTotals['outros_debito'] ?? 0);

$logoPath = public_path('vestalize.svg');
@endphp

{{-- ====================== PÁGINA 1: FECHAMENTO DE CAIXA ====================== --}}
<div class="company-header">
    @if(file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Vestalize">
    @endif
    @if(!empty($companySettings->company_name))
        <div class="company-name">{{ $companySettings->company_name }}</div>
    @endif
</div>

<h2 class="page-title">FECHAMENTO DE CAIXA</h2>
<div class="period-label">Período: {{ $periodLabel }}</div>

<div class="summary-wrapper">
    {{-- COLUNA ESQUERDA: Formas de pagamento --}}
    <div class="summary-left">
        <table class="res">
            <tr><th colspan="2">RESUMO</th></tr>
            <tr>
                <td class="label">Vendas</td>
                <td class="value">{{ $fmtOrDash($totalVendas) }}</td>
            </tr>
            <tr>
                <td class="label">Dinheiro</td>
                <td class="value">{{ $fmtOrDash($paymentTotals['dinheiro'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Cheque/Boleto</td>
                <td class="value">{{ $fmtOrDash($paymentTotals['cheque_boleto'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Entradas</td>
                <td class="value">{{ $fmtOrDash($paymentTotals['entradas'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Transferência</td>
                <td class="value">{{ $fmtOrDash($paymentTotals['transferencia'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Mov. CASHBACK</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['cashback'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Cashback Concedido</em></td>
                <td class="value">{{ $fmtOrDash($cashbackConcedido ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Cashback Utilizado</em></td>
                <td class="value">{{ $fmtOrDash($cashbackUtilizado ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Visa Crédito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['visa_credito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Visa Débito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['visa_debito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Master Crédito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['master_credito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Master Débito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['master_debito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Elo Credito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['elo_credito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Elo Debito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['elo_debito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Hiper C</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['hiper'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Amex C</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['amex'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Outros - Crédito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['outros_credito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Outros - Débito</em></td>
                <td class="value">{{ $fmtOrDash($paymentTotals['outros_debito'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label">Total Cartão</td>
                <td class="value">{{ $fmtOrDash($totalCartao) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total de Vendas</td>
                <td class="value">{{ $fmt($totalVendas) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Entradas Finalizadas</em></td>
                <td class="value">{{ $fmtOrDash($entradasFinalizadas ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Vendas Debitadas</em></td>
                <td class="value">{{ $fmtOrDash($vendasDebitadas ?? 0) }}</td>
            </tr>
            <tr>
                <td class="label"><em>Pagamento de Débito</em></td>
                <td class="value">{{ $fmtOrDash($pagamentoDebito ?? 0) }}</td>
            </tr>
        </table>
    </div>

    {{-- COLUNA CENTRAL: Sangria / Suprimentos / Saldo --}}
    <div class="summary-mid">
        <table class="res">
            <tr><th colspan="2">&nbsp;</th></tr>
            <tr class="sangria-row">
                <td class="label">Sangria</td>
                <td class="value">{{ $fmt($totalSangria) }}</td>
            </tr>
            <tr class="suprimento-row">
                <td class="label">Suprimentos</td>
                <td class="value">{{ $fmtOrDash($totalSuprimentos) }}</td>
            </tr>
            <tr class="saldo-row">
                <td class="label">Saldo de Caixa</td>
                <td class="value">{{ $fmt($saldoCaixa) }}</td>
            </tr>
        </table>
    </div>

    {{-- COLUNA DIREITA: Vendedores --}}
    <div class="summary-right">
        <table class="res">
            <tr><th colspan="2">VENDEDOR / TOTAL</th></tr>
            @forelse($vendasPorVendedor as $vId => $vData)
            <tr>
                <td class="label">{{ strtoupper($vData['nome']) }}</td>
                <td class="value">{{ $fmt($vData['total']) }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;color:#888">Sem vendas no período</td></tr>
            @endforelse
            <tr class="total-row">
                <td class="label">TOTAL</td>
                <td class="value">{{ $fmt($totalVendas) }}</td>
            </tr>
        </table>
    </div>
</div>

{{-- ====================== PÁGINA 2: DETALHAMENTO DAS ENTRADAS ====================== --}}
<div class="br"></div>

<h2 class="page-title">DETALHAMENTO DAS ENTRADAS</h2>
<div class="period-label">Período: {{ $periodLabel }}</div>
<div class="section-note">Cada linha abaixo identifica a origem da entrada, o pedido relacionado e a forma de pagamento efetiva.</div>

<table class="mov">
    <thead>
        <tr>
            <th style="width:58px">Data</th>
            <th style="width:42px">Hora</th>
            <th style="width:88px">Origem</th>
            <th style="width:65px">Pedido</th>
            <th style="width:130px">Cliente</th>
            <th style="width:110px">Vendedor</th>
            <th style="width:95px">Pagamento</th>
            <th>Descricao</th>
            <th style="width:82px">Valor</th>
        </tr>
    </thead>
    <tbody>
        @forelse($entryDetails as $entry)
        <tr>
            <td class="small">{{ $entry['date'] }}</td>
            <td class="small">{{ $entry['time'] }}</td>
            <td class="wrap">{{ $entry['origin'] }}</td>
            <td class="small">{{ $entry['order_number'] }}</td>
            <td class="wrap">{{ $entry['client'] }}</td>
            <td class="wrap">{{ $entry['seller'] }}</td>
            <td class="wrap">{{ $entry['payment_method'] }}</td>
            <td class="wrap">{{ $entry['description'] }}</td>
            <td class="amount">{{ $fmt($entry['amount']) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center;color:#777">Nenhuma entrada encontrada no periodo.</td>
        </tr>
        @endforelse
        @if(!empty($entryDetails))
        <tr class="total-row">
            <td colspan="8">TOTAL DAS ENTRADAS</td>
            <td class="amount">{{ $fmt($totalVendas) }}</td>
        </tr>
        @endif
    </tbody>
</table>

@if(!empty($cashMovementDetails))
    <br>
    <h2 class="page-title" style="font-size:12px">SAIDAS E AJUSTES DE CAIXA</h2>
    <div class="section-note">Sangrias e suprimentos permanecem separados para facilitar a conferencia do saldo.</div>
    <table class="mov">
        <thead>
            <tr>
                <th style="width:58px">Data</th>
                <th style="width:42px">Hora</th>
                <th style="width:90px">Movimento</th>
                <th style="width:65px">Pedido</th>
                <th style="width:110px">Responsavel</th>
                <th>Descricao</th>
                <th style="width:82px">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashMovementDetails as $movement)
            <tr>
                <td class="small">{{ $movement['date'] }}</td>
                <td class="small">{{ $movement['time'] }}</td>
                <td class="wrap">{{ $movement['origin'] }}</td>
                <td class="small">{{ $movement['order_number'] }}</td>
                <td class="wrap">{{ $movement['seller'] }}</td>
                <td class="wrap">{{ $movement['description'] }}</td>
                <td class="amount">{{ $fmt($movement['amount']) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6">TOTAL DE SAIDAS E AJUSTES</td>
                <td class="amount">{{ $fmt($totalSangria + $totalSuprimentos) }}</td>
            </tr>
        </tbody>
    </table>
@endif

{{-- ====================== PÁGINA 3: DETALHAMENTO POR VENDEDOR ====================== --}}
<div class="br"></div>

<h2 class="page-title">DETALHAMENTO DE VENDAS POR VENDEDOR</h2>
<div class="period-label">Período: {{ $periodLabel }}</div>

@php
$vendedores = array_values($vendasPorVendedor);
// Coletar todas as transações individuais de entrada (exceto sangria/suprimento)
$allVendas = $vendas->sortBy('transaction_date')->values();
@endphp

<table class="det">
    <thead>
        <tr>
            <th class="vend-col" style="text-align:left;width:90px">VEND</th>
            @foreach($vendedores as $v)
                <th>{{ strtoupper($v['nome']) }}</th>
            @endforeach
            <th class="total-col">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        {{-- Linha de Totais gerais --}}
        <tr class="tot-row">
            <td class="label-col">Total de V.</td>
            @foreach($vendedores as $v)
                <td>{{ $fmt($v['total']) }}</td>
            @endforeach
            <td>{{ $fmt($totalVendas) }}</td>
        </tr>
        {{-- Linha do total cartão --}}
        <tr class="card-row">
            <td class="label-col">Cartão</td>
            @foreach($vendedores as $v)
                @php
                    $cartaoVendedor = collect($v['transacoes'])->sum(function($t) use ($v) {
                        if (empty($t->payment_methods) || !is_array($t->payment_methods)) {
                            $m = strtolower($t->payment_method ?? '');
                            if (str_contains($m, 'visa') || str_contains($m, 'master') || str_contains($m, 'elo') ||
                                str_contains($m, 'hiper') || str_contains($m, 'amex') || str_contains($m, 'cartao') ||
                                str_contains($m, 'credito') || str_contains($m, 'debito')) {
                                return floatval($t->amount);
                            }
                            return 0;
                        }
                        $sum = 0;
                        foreach ($t->payment_methods as $pm) {
                            $m = strtolower($pm['method'] ?? '');
                            if (str_contains($m, 'visa') || str_contains($m, 'master') || str_contains($m, 'elo') ||
                                str_contains($m, 'hiper') || str_contains($m, 'amex') || str_contains($m, 'cartao') ||
                                str_contains($m, 'credito') || str_contains($m, 'debito')) {
                                $sum += floatval($pm['amount'] ?? 0);
                            }
                        }
                        return $sum;
                    });
                @endphp
                <td>{{ $fmtOrDash($cartaoVendedor) }}</td>
            @endforeach
            <td>{{ $fmtOrDash($totalCartao) }}</td>
        </tr>
        {{-- Linhas individuais de vendas --}}
        @foreach($allVendas as $t)
        @php
            $tVendedorId = $t->order?->user_id ?? $t->user_id ?? 0;
        @endphp
        <tr>
            <td class="label-col">{{ $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('d/m') : '-' }}</td>
            @foreach($vendedores as $v)
                @php
                    $vendKeys = array_keys($vendasPorVendedor);
                    $vIndex = array_search($v['nome'], array_column($vendasPorVendedor, 'nome'));
                    $thisVId = isset($vendKeys[$vIndex]) ? $vendKeys[$vIndex] : null;
                    $show = ($thisVId == $tVendedorId);
                @endphp
                <td>{{ $show ? $fmtOrDash($t->amount) : '' }}</td>
            @endforeach
            <td>{{ $fmtOrDash($t->amount) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ====================== PÁGINA 4: RESUMO CONSOLIDADO ====================== --}}
<div class="br"></div>

<h2 class="page-title">RESUMO FINANCEIRO</h2>
<div class="period-label">Período: {{ $periodLabel }} — Gerado em {{ now()->format('d/m/Y H:i') }}</div>

<div class="summary-wrapper">
    <div class="summary-left">
        <table class="res">
            <tr><th colspan="2">RESUMO CONSOLIDADO</th></tr>
            <tr><td class="label">Dinheiro</td><td class="value">{{ $fmtOrDash($paymentTotals['dinheiro'] ?? 0) }}</td></tr>
            <tr><td class="label">Cheque/Boleto</td><td class="value">{{ $fmtOrDash($paymentTotals['cheque_boleto'] ?? 0) }}</td></tr>
            <tr><td class="label">Transferência / PIX</td><td class="value">{{ $fmtOrDash(($paymentTotals['transferencia'] ?? 0) + ($paymentTotals['entradas'] ?? 0)) }}</td></tr>
            <tr><td class="label">Total Cartão</td><td class="value">{{ $fmtOrDash($totalCartao) }}</td></tr>
            <tr class="total-row"><td class="label">Total de Vendas</td><td class="value">{{ $fmt($totalVendas) }}</td></tr>
            <tr style="height:6px"><td colspan="2"></td></tr>
            <tr class="sangria-row"><td class="label">Sangria</td><td class="value">{{ $fmt($totalSangria) }}</td></tr>
            <tr class="suprimento-row"><td class="label">Suprimentos</td><td class="value">{{ $fmtOrDash($totalSuprimentos) }}</td></tr>
            <tr class="saldo-row"><td class="label">Saldo de Caixa</td><td class="value">{{ $fmt($saldoCaixa) }}</td></tr>
        </table>
    </div>
    <div class="summary-right" style="width:50%;padding-left:12px">
        <table class="res">
            <tr><th colspan="2">VENDAS POR VENDEDOR</th></tr>
            @forelse($vendasPorVendedor as $vData)
            <tr>
                <td class="label">{{ strtoupper($vData['nome']) }}</td>
                <td class="value">{{ $fmt($vData['total']) }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="text-align:center;color:#888">Sem vendas</td></tr>
            @endforelse
            <tr class="total-row">
                <td class="label">TOTAL GERAL</td>
                <td class="value">{{ $fmt($totalVendas) }}</td>
            </tr>
        </table>

        <br>
        <table class="res" style="margin-top:8px">
            <tr><th colspan="2">CARTÕES DETALHADOS</th></tr>
            <tr><td class="label"><em>Visa Crédito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['visa_credito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Visa Débito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['visa_debito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Master Crédito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['master_credito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Master Débito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['master_debito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Elo Crédito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['elo_credito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Elo Débito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['elo_debito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Hiper C</em></td><td class="value">{{ $fmtOrDash($paymentTotals['hiper'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Amex C</em></td><td class="value">{{ $fmtOrDash($paymentTotals['amex'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Outros Crédito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['outros_credito'] ?? 0) }}</td></tr>
            <tr><td class="label"><em>Outros Débito</em></td><td class="value">{{ $fmtOrDash($paymentTotals['outros_debito'] ?? 0) }}</td></tr>
            <tr class="total-row"><td class="label">Total Cartão</td><td class="value">{{ $fmtOrDash($totalCartao) }}</td></tr>
        </table>
    </div>
</div>

</body>
</html>
