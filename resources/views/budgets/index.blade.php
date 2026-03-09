@extends('layouts.admin')

@section('content')
<style>
    .budgets-ft {
        --of-surface-from: #f3f4f8;
        --of-surface-to: #eceff4;
        --of-surface-border: #d8dce6;
        --of-text-primary: #0f172a;
        --of-text-secondary: #64748b;
        --of-tab-text: #4b5563;
        --of-card-bg: #ffffff;
        --of-card-border: #dde2ea;
        --of-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --of-input-bg: #ffffff;
        --of-input-border: #d6d9e2;
        --of-input-text: #334155;
        --of-table-head: #e5e9f1;
        --of-table-row: #eef1f6;
        background: linear-gradient(180deg, var(--of-surface-from) 0%, var(--of-surface-to) 100%);
        border: 1px solid var(--of-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--of-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .budgets-ft {
        --of-surface-from: #1e293b;
        --of-surface-to: #0f172a;
        --of-surface-border: rgba(148, 163, 184, 0.25);
        --of-text-primary: #f8fafc;
        --of-text-secondary: #cbd5e1;
        --of-tab-text: #94a3b8;
        --of-card-bg: #334155;
        --of-card-border: rgba(148, 163, 184, 0.2);
        --of-card-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        --of-input-bg: #475569;
        --of-input-border: rgba(148, 163, 184, 0.25);
        --of-input-text: #f8fafc;
        --of-table-head: rgba(255, 255, 255, 0.1);
        --of-table-row: rgba(255, 255, 255, 0.03);
    }

    .budgets-ft-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .budgets-ft-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1 1 320px;
    }

    .budgets-ft-logo {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .budgets-ft-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--of-text-primary) !important;
    }

    .budgets-ft-subtitle {
        margin-top: 3px;
        font-size: 13px;
        font-weight: 600;
        color: var(--of-text-secondary) !important;
    }

    .budgets-ft-btn {
        height: 38px;
        border-radius: 12px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        color: #fff !important;
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25);
        transition: transform .18s ease, filter .18s ease, box-shadow .2s ease;
    }

    .budgets-ft-btn-emerald {
        background: linear-gradient(135deg, #059669, #10b981);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
    }

    .budgets-ft-btn,
    .budgets-ft-btn span,
    .budgets-ft-btn i,
    .budgets-ft-btn svg,
    .budgets-ft-btn svg * {
        color: #fff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    .budgets-ft-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .budgets-ft .landing-card {
        background: var(--of-card-bg) !important;
        border: 1px solid var(--of-card-border) !important;
        border-radius: 14px !important;
        box-shadow: var(--of-card-shadow) !important;
    }

    .budgets-ft .budgets-ft-filter-card {
        margin-bottom: 14px !important;
        border-radius: 14px !important;
        overflow: hidden;
    }

    .budgets-ft .budgets-ft-filter-head {
        background: var(--of-card-bg) !important;
        border-bottom: 1px solid var(--of-card-border) !important;
        padding: 14px 16px !important;
    }

    .budgets-ft .budgets-ft-filter-head h3 {
        color: var(--of-text-primary) !important;
        font-size: 20px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .budgets-ft .budgets-ft-filter-head .w-8.h-8 {
        width: 34px;
        height: 34px;
        border-radius: 10px;
    }

    .budgets-ft .budgets-ft-filter-body {
        background: var(--of-card-bg) !important;
        padding: 16px !important;
    }

    .budgets-ft .budgets-ft-filter-body form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .budgets-ft .budgets-ft-filter-body .grid {
        gap: 12px !important;
    }

    .budgets-ft .budgets-ft-filter-body .border-t {
        border-color: var(--of-table-row) !important;
    }

    .budgets-ft .budgets-ft-filter-body label {
        color: var(--of-text-secondary) !important;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
    }

    .budgets-ft .budgets-ft-input,
    .budgets-ft input.budgets-ft-input,
    .budgets-ft select.budgets-ft-input,
    .budgets-ft textarea.budgets-ft-input {
        box-sizing: border-box;
        height: 40px !important;
        min-height: 40px !important;
        border-radius: 10px !important;
        border: 1px solid var(--of-input-border) !important;
        background: var(--of-input-bg) !important;
        color: var(--of-input-text) !important;
        padding: 0 14px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        line-height: 1.2 !important;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .budgets-ft select.budgets-ft-input {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 34px !important;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .budgets-ft .budgets-ft-input:focus {
        outline: none !important;
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15) !important;
    }

    .budgets-ft .budgets-ft-input::placeholder {
        color: var(--of-text-secondary) !important;
    }

    .budgets-ft .budgets-ft-filter-btn {
        height: 44px;
        border-radius: 10px !important;
        padding: 0 14px !important;
        border: 0 !important;
        background: linear-gradient(135deg, #6d28d9, #7c3aed) !important;
        color: #fff !important;
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25) !important;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: transform .18s ease, box-shadow .2s ease, filter .2s ease;
    }

    .budgets-ft .budgets-ft-filter-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .budgets-ft .budgets-ft-filter-btn,
    .budgets-ft .budgets-ft-filter-btn span,
    .budgets-ft .budgets-ft-filter-btn i,
    .budgets-ft .budgets-ft-filter-btn svg,
    .budgets-ft .budgets-ft-filter-btn svg * {
        color: #ffffff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    .budgets-ft .budgets-ft-clear-btn {
        width: 44px;
        min-width: 44px;
        height: 44px;
        border-radius: 10px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--of-input-bg) !important;
        border: 1px solid var(--of-input-border) !important;
        color: var(--of-text-secondary) !important;
    }

    .budgets-ft .budgets-ft-clear-btn:hover {
        border-color: #7c3aed !important;
        color: #7c3aed !important;
        background: rgba(124, 58, 237, 0.06) !important;
    }

    .budgets-ft .budgets-ft-table-card {
        border-radius: 14px !important;
        overflow: hidden;
    }

    .budgets-ft .table-sticky-wrapper {
        border-radius: 12px;
        border: 1px solid var(--of-table-head);
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        background: var(--of-card-bg);
    }

    .budgets-ft .budgets-ft-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .budgets-ft .budgets-ft-table thead {
        background: transparent !important;
    }

    .budgets-ft .budgets-ft-table thead th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        color: var(--of-text-secondary) !important;
        border-bottom: 1px solid var(--of-table-head);
        background: transparent !important;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .budgets-ft .budgets-ft-table tbody td {
        border-color: var(--of-table-row) !important;
        color: var(--of-text-primary) !important;
    }

    .budgets-ft .budgets-ft-table tbody tr {
        background: var(--of-card-bg) !important;
        border-color: var(--of-table-row) !important;
    }

    .budgets-ft .budgets-ft-table tbody tr:hover {
        background: var(--of-card-bg) !important;
    }

    .dark .budgets-ft .budgets-ft-table {
        background-color: var(--of-card-bg) !important;
    }

    .dark .budgets-ft .budgets-ft-table thead th {
        background-color: var(--of-table-head) !important;
        border-color: var(--of-table-head) !important;
    }

    .dark .budgets-ft .budgets-ft-table tbody td {
        background-color: var(--of-card-bg) !important;
        border-color: var(--of-table-row) !important;
    }

    .dark .budgets-ft .budgets-ft-table tbody tr:hover td {
        background-color: var(--of-card-bg) !important;
    }

    /* Colunas Sticky Budgets */
    .dark .budgets-ft .sticky-column {
        background-color: var(--of-card-bg) !important;
    }

    .dark .budgets-ft .sticky-table thead .sticky-column {
        background-color: var(--of-table-head) !important;
    }

    .dark .budgets-ft .sticky-table tbody tr:hover .sticky-column {
        background-color: var(--of-card-bg) !important;
    }

    /* Remover sombra/quebra da coluna fixa */
    .budgets-ft .sticky-column[data-sticky-last]::after,
    .dark .budgets-ft .sticky-column[data-sticky-last]::after {
        display: none !important;
        opacity: 0 !important;
    }

    .budgets-ft .table-sticky-wrapper::after,
    .dark .budgets-ft .table-sticky-wrapper::after {
        display: none !important;
        opacity: 0 !important;
    }

    .budgets-ft .budgets-ft-mobile-card {
        border-radius: 14px !important;
    }

    .budgets-ft .budgets-ft-mini-btn {
        height: 40px;
        border-radius: 10px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .budgets-ft .text-gray-900,
    .budgets-ft .text-gray-800,
    .budgets-ft .text-gray-700 {
        color: var(--of-text-primary) !important;
    }

    .budgets-ft .text-gray-600,
    .budgets-ft .text-gray-500,
    .budgets-ft .text-gray-400 {
        color: var(--of-text-secondary) !important;
    }

    /* Metric cards */
    .budgets-ft .budgets-metric-card {
        background: var(--of-card-bg);
        border: 1px solid var(--of-card-border);
        border-radius: 14px;
        box-shadow: var(--of-card-shadow);
        padding: 16px 18px;
        position: relative;
        overflow: hidden;
    }

    .budgets-ft .budgets-metric-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .budgets-ft .budgets-metric-card.metric-yellow::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .budgets-ft .budgets-metric-card.metric-blue::after { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .budgets-ft .budgets-metric-card.metric-red::after { background: linear-gradient(90deg, #ef4444, #f87171); }
    .budgets-ft .budgets-metric-card.metric-purple::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

    .budgets-ft .budgets-metric-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--of-text-secondary);
    }

    .budgets-ft .budgets-metric-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--of-text-primary);
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-top: 4px;
    }

    .budgets-ft .budgets-metric-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Proportion bar */
    .budgets-ft .proportion-bar {
        width: 100%;
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        display: flex;
        background: var(--of-table-row);
        margin-top: 8px;
    }

    .budgets-ft .proportion-legend {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
    }

    .budgets-ft .proportion-legend span {
        font-size: 10px;
        font-weight: 700;
        color: var(--of-text-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Notification */
    .budgets-ft .budgets-notification {
        background: var(--of-card-bg);
        border: 1px solid var(--of-card-border);
        border-left: 4px solid #7c3aed;
        border-radius: 0 14px 14px 0;
        box-shadow: var(--of-card-shadow);
        padding: 14px 18px;
        margin-bottom: 14px;
    }

    @media (max-width: 768px) {
        .budgets-ft {
            padding: 14px;
            border-radius: 16px;
        }

        .budgets-ft-title {
            font-size: 24px;
        }

        .budgets-ft .budgets-ft-filter-head h3 {
            font-size: 18px;
        }

        .budgets-ft .budgets-ft-filter-body {
            padding: 12px !important;
        }

        .budgets-ft-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

@php
    $statusColors = [
        'pending'  => '#f59e0b',
        'approved' => '#10b981',
        'rejected' => '#ef4444',
        'expired'  => '#6b7280',
    ];
    $statusLabels = [
        'pending'  => 'Pendente',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'expired'  => 'Expirado',
    ];
@endphp

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
<section class="budgets-ft">

<!-- Header -->
<div class="budgets-ft-topbar animate-fade-in-blur">
    <div class="budgets-ft-brand">
        <span class="budgets-ft-logo">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </span>
        <div>
            <h1 class="budgets-ft-title">Orçamentos</h1>
            <p class="budgets-ft-subtitle">Gerencie todos os orçamentos</p>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('budget.quick-create') }}" class="budgets-ft-btn budgets-ft-btn-emerald">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Orçamento Rápido
        </a>
        <a href="{{ route('budget.start') }}" class="budgets-ft-btn">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Novo Orçamento
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600/30 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
    {{ session('success') }}
</div>
@endif

<!-- Follow-up Notification -->
@if($followUpCount > 0)
<div class="budgets-notification animate-fade-in-up">
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 flex-shrink-0 mt-0.5">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold" style="color: var(--of-text-primary)">Rechamada Necessária</h3>
            <p class="mt-1 text-sm" style="color: var(--of-text-secondary)">
                Você tem <span class="font-bold" style="color: #7c3aed">{{ $followUpCount }}</span> orçamentos pendentes criados há mais de uma semana. Entre em contato com o cliente novamente!
            </p>
        </div>
    </div>
</div>
@endif

<!-- Dashboard Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 animate-fade-in-up">
    <!-- Pendentes -->
    <div class="budgets-metric-card metric-yellow">
        <div class="flex items-center justify-between">
            <div>
                <p class="budgets-metric-label">Pendentes</p>
                <p class="budgets-metric-value">{{ $pendingCount }}</p>
            </div>
            <div class="budgets-metric-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Valor em Aberto -->
    <div class="budgets-metric-card metric-blue">
        <div class="flex items-center justify-between">
            <div>
                <p class="budgets-metric-label">Valor em Aberto</p>
                <p class="budgets-metric-value">R$ {{ number_format($openValue, 2, ',', '.') }}</p>
            </div>
            <div class="budgets-metric-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Vencendo em Breve -->
    <div class="budgets-metric-card metric-red">
        <div class="flex items-center justify-between">
            <div>
                <p class="budgets-metric-label">Vencendo em 3 dias</p>
                <p class="budgets-metric-value">{{ $expiringSoonCount }}</p>
            </div>
            <div class="budgets-metric-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Proporção -->
    <div class="budgets-metric-card metric-purple">
        <p class="budgets-metric-label">Proporção</p>
        @php
            $totalStats = array_sum($statusStats);
            $approvedPct = $totalStats > 0 ? (($statusStats['approved'] ?? 0) / $totalStats) * 100 : 0;
            $pendingPct = $totalStats > 0 ? (($statusStats['pending'] ?? 0) / $totalStats) * 100 : 0;
            $rejectedPct = $totalStats > 0 ? (($statusStats['rejected'] ?? 0) / $totalStats) * 100 : 0;
        @endphp
        <div class="proportion-bar">
            <div style="width: {{ $approvedPct }}%; background: #10b981;" title="Aprovados: {{ $statusStats['approved'] ?? 0 }}"></div>
            <div style="width: {{ $pendingPct }}%; background: #f59e0b;" title="Pendentes: {{ $statusStats['pending'] ?? 0 }}"></div>
            <div style="width: {{ $rejectedPct }}%; background: #ef4444;" title="Rejeitados: {{ $statusStats['rejected'] ?? 0 }}"></div>
        </div>
        <div class="proportion-legend">
            <span><span class="w-2 h-2 rounded-full inline-block" style="background:#10b981"></span> Apr</span>
            <span><span class="w-2 h-2 rounded-full inline-block" style="background:#f59e0b"></span> Pend</span>
            <span><span class="w-2 h-2 rounded-full inline-block" style="background:#ef4444"></span> Rej</span>
        </div>
    </div>
</div>

<!-- Filtros Premium -->
<div class="w-full landing-card budgets-ft-filter-card mb-8 p-0 overflow-hidden animate-fade-in-up" x-data="{ filtersOpen: window.innerWidth >= 768 }">
    <div class="p-4 md:p-6 border-b border-gray-100 dark:border-white/5 flex justify-between items-center cursor-pointer md:cursor-default budgets-ft-filter-head" @click="filtersOpen = window.innerWidth < 768 ? !filtersOpen : true">
        <h3 class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
            </div>
            Filtros de Busca
        </h3>
        <button class="md:hidden p-2 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
            <svg class="w-5 h-5 transform transition-transform duration-300" :class="filtersOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>

    <div x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 budgets-ft-filter-body">
        <form method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-8">
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Buscar Orçamento</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-purple-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Número, cliente..."
                               style="padding-left: 3.25rem !important;"
                               class="w-full pr-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium budgets-ft-input">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium budgets-ft-input">
                        <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Todos os Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirado</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end border-t border-gray-100 dark:border-white/5 pt-6">
                <div class="md:col-span-3"></div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-br from-[#7c3aed] to-[#6d28d9] text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 budgets-ft-filter-btn">
                        <svg class="w-4 h-4 text-white" style="color: white !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        <span style="color: white !important;">Filtrar</span>
                    </button>
                    <a href="{{ route('budget.index') }}" class="p-3 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 rounded-xl hover:bg-gray-200 dark:hover:bg-white/10 transition-colors budgets-ft-clear-btn" title="Limpar Filtros">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista Mobile -->
<div class="space-y-4 md:hidden animate-fade-in-up">
    @forelse($budgets as $budget)
    @php $sColor = $statusColors[$budget->status] ?? '#6b7280'; @endphp
    <div class="landing-card budgets-ft-mobile-card p-5 relative overflow-hidden group">
        <!-- Barra Lateral de Status -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $sColor }}"></div>

        <div class="flex justify-between items-start mb-4">
            <div>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">#{{ str_pad($budget->id, 6, '0', STR_PAD_LEFT) }}</span>
                <h3 class="font-bold text-gray-900 dark:text-white leading-tight mt-1">
                    @if($budget->is_quick)
                        {{ $budget->contact_name ?? 'Cliente' }}
                        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded uppercase tracking-tighter" style="background: rgba(16,185,129,0.1); color: #10b981;">Rápido</span>
                    @else
                        {{ $budget->client->name ?? 'Cliente' }}
                    @endif
                </h3>
            </div>
            <div class="text-right">
                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-md uppercase tracking-wider"
                      style="background-color: {{ $sColor }}20; color: {{ $sColor }}">
                    {{ $statusLabels[$budget->status] ?? ucfirst($budget->status) }}
                </span>
                <div class="text-[10px] text-gray-400 mt-1 font-medium">{{ $budget->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-100 dark:border-white/5">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Validade</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $budget->valid_until->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Total</p>
                <p class="text-sm font-bold text-purple-600 dark:text-purple-400 mt-0.5">R$ {{ number_format($budget->total, 2, ',', '.') }}</p>
            </div>
        </div>

        @if($budget->order_id || $budget->order_number)
        <div class="flex items-center text-sm text-purple-600 dark:text-purple-400 mb-3">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" /></svg>
            @if($budget->order_id)
                Pedido #{{ str_pad($budget->order_id, 6, '0', STR_PAD_LEFT) }}
            @else
                Pedido #{{ $budget->order_number }}
            @endif
        </div>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ route('budget.show', $budget->id) }}" class="flex-1 py-2.5 bg-[#7c3aed] text-white text-xs font-bold rounded-xl text-center shadow-lg shadow-purple-500/20 transition-all budgets-ft-filter-btn budgets-ft-mini-btn">
                Ver Detalhes
            </a>
            <a href="{{ route('budget.pdf', $budget->id) }}" class="p-2.5 bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 rounded-xl transition-colors" title="PDF">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
            </a>
            @if($budget->status === 'approved')
            <a href="{{ route('budget.convert-to-order', $budget->id) }}" class="p-2.5 bg-green-50 dark:bg-green-500/10 text-green-500 rounded-xl transition-colors" title="Converter em Pedido">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="landing-card p-10 text-center">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum orçamento encontrado.</p>
    </div>
    @endforelse
</div>

<!-- Lista Desktop -->
<div class="hidden md:block landing-card budgets-ft-table-card p-0 overflow-hidden animate-fade-in-up delay-100">
    <div class="table-sticky-wrapper overflow-x-auto">
        <table class="min-w-full budgets-ft-table">
            <thead>
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Número</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Cliente</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Data</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Validade</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Total</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($budgets as $budget)
                @php $sColor = $statusColors[$budget->status] ?? '#6b7280'; @endphp
                <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-all duration-200">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $sColor }}"></div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $budget->budget_number }}
                            </div>
                        </div>
                        @if($budget->order_id)
                        <a href="{{ route('orders.show', $budget->order_id) }}"
                           class="block text-[10px] font-bold text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 hover:underline mt-1 pl-5 transition-colors uppercase tracking-tighter">
                            Pedido: #{{ str_pad($budget->order_id, 6, '0', STR_PAD_LEFT) }}
                        </a>
                        @elseif($budget->order_number)
                        <span class="block text-[10px] text-gray-400 mt-1 pl-5 font-bold uppercase tracking-tighter">
                            Pedido: #{{ $budget->order_number }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        @if($budget->is_quick)
                            <div class="text-sm font-semibold text-gray-900 dark:text-white max-w-[180px] truncate" title="{{ $budget->contact_name ?? 'Contato' }}">
                                {{ $budget->contact_name ?? 'Contato' }}
                            </div>
                            <span class="mt-0.5 inline-block px-1.5 py-0.5 text-[10px] font-bold rounded uppercase tracking-tighter" style="background: rgba(16,185,129,0.1); color: #10b981;">Rápido</span>
                        @else
                            <div class="text-sm font-semibold text-gray-900 dark:text-white max-w-[180px] truncate" title="{{ $budget->client->name ?? 'Cliente' }}">
                                {{ $budget->client->name ?? 'Cliente' }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $budget->created_at->format('d/m') }} <span class="text-[10px] font-normal opacity-50">{{ $budget->created_at->format('/y') }}</span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $budget->valid_until->format('d/m') }} <span class="text-[10px] font-normal opacity-50">{{ $budget->valid_until->format('/y') }}</span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">
                        R$ {{ number_format($budget->total, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-[11px] leading-4 font-bold rounded-lg uppercase tracking-wider"
                              style="background-color: {{ $sColor }}15; color: {{ $sColor }}">
                            {{ $statusLabels[$budget->status] ?? ucfirst($budget->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            {{-- Editar --}}
                            <a href="{{ route('budget.edit', $budget->id) }}" 
                               class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-500/10 rounded-lg transition-colors"
                               title="Editar Orçamento">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>

                            {{-- Ver --}}
                            <a href="{{ route('budget.show', $budget->id) }}"
                               class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-500/10 rounded-lg transition-colors"
                               title="Ver Detalhes">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>

                            {{-- PDF --}}
                            <a href="{{ route('budget.pdf', $budget->id) }}"
                               class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-500/10 rounded-lg transition-colors"
                               title="Gerar PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            </a>

                            {{-- Converter --}}
                            @if($budget->status === 'approved')
                            <a href="{{ route('budget.convert-to-order', $budget->id) }}"
                               class="p-2 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl transition-all shadow-md shadow-green-200/50 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-300/50 flex items-center justify-center"
                               title="Converter em Pedido">
                                <svg class="w-5 h-5" style="color: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-600 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium tracking-tight">Nenhum orçamento encontrado.</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Tente ajustar seus filtros ou crie um novo orçamento.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Paginação -->
@if($budgets->hasPages())
<div class="mt-6">
    {{ $budgets->links() }}
</div>
@endif

</section>
</div>
@endsection
