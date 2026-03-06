@extends('layouts.admin')

@section('content')
@php
    $pendingRequests = $requests->where('status', 'pendente')->values();
    $approvedRequests = $requests->where('status', 'aprovado')->values();
    $rejectedRequests = $requests->where('status', 'rejeitado')->values();

    $avgDaysSaved = (int) round($pendingRequests->map(function ($request) {
        return \Carbon\Carbon::parse($request->requested_delivery_date)
            ->diffInDays(\Carbon\Carbon::parse($request->current_delivery_date));
    })->avg() ?? 0);

    $latestRequestAt = $requests->max('created_at');
@endphp

<style>
    .prod-ft {
        --pf-surface-from: #f3f4f8;
        --pf-surface-to: #eceff4;
        --pf-surface-border: #d8dce6;
        --pf-text-primary: #0f172a;
        --pf-text-secondary: #64748b;
        --pf-input-bg: #ffffff;
        --pf-input-border: #d6d9e2;
        --pf-input-text: #334155;
        --pf-card-bg: #ffffff;
        --pf-card-border: #dde2ea;
        --pf-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --pf-table-head-border: #e5e9f1;
        --pf-table-row-border: #eef1f6;
        background: linear-gradient(180deg, var(--pf-surface-from) 0%, var(--pf-surface-to) 100%);
        border: 1px solid var(--pf-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--pf-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .prod-ft {
        --pf-surface-from: #0f172a;
        --pf-surface-to: #0b1322;
        --pf-surface-border: rgba(148, 163, 184, 0.25);
        --pf-text-primary: #e2e8f0;
        --pf-text-secondary: #94a3b8;
        --pf-input-bg: #0b1322;
        --pf-input-border: rgba(148, 163, 184, 0.3);
        --pf-input-text: #e2e8f0;
        --pf-card-bg: #111827;
        --pf-card-border: rgba(148, 163, 184, 0.22);
        --pf-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        --pf-table-head-border: rgba(148, 163, 184, 0.25);
        --pf-table-row-border: rgba(148, 163, 184, 0.16);
    }

    .pf-topbar, .pf-brand, .pf-actions, .pf-kpis, .pf-tabs, .pf-request-meta, .pf-request-actions, .pf-modal-actions { display: flex; gap: 14px; flex-wrap: wrap; }
    .pf-topbar { justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .pf-brand { align-items: center; flex: 1 1 320px; min-width: 0; }
    .pf-logo { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #6d28d9, #8b5cf6); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .pf-title { font-size: 18px; font-weight: 700; letter-spacing: -0.015em; }
    .pf-subtitle { color: var(--pf-text-secondary); font-size: 12px; font-weight: 600; margin-top: 2px; }
    .pf-actions { align-items: center; }
    .pf-action, .pf-modal-button {
        height: 38px;
        border-radius: 12px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        border: 0;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .2s ease, filter .2s ease;
    }
    .pf-action:hover, .pf-modal-button:hover { transform: translateY(-1px); filter: brightness(1.03); }
    .pf-action-primary { background: linear-gradient(135deg, #6d28d9, #7c3aed); color: #fff !important; box-shadow: 0 10px 20px rgba(109, 40, 217, 0.25); }
    .pf-action-success { background: linear-gradient(135deg, #059669, #10b981); color: #fff !important; box-shadow: 0 10px 20px rgba(5, 150, 105, 0.25); }
    .pf-action-muted, .pf-modal-button-muted { background: var(--pf-input-bg); color: var(--pf-input-text); border: 1px solid var(--pf-input-border); }
    .pf-modal-button-success { background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 10px 20px rgba(5, 150, 105, 0.22); }
    .pf-modal-button-danger { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; box-shadow: 0 10px 20px rgba(220, 38, 38, 0.22); }
    .pf-card { background: var(--pf-card-bg); border: 1px solid var(--pf-card-border); border-radius: 14px; padding: 16px; box-shadow: var(--pf-card-shadow); }
    .pf-kpis { margin-bottom: 14px; }
    .pf-kpi { flex: 1 1 220px; min-height: 132px; position: relative; overflow: hidden; }
    .pf-kpi-value { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; color: var(--pf-text-primary); margin-top: 10px; }
    .pf-kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--pf-text-secondary); font-weight: 700; }
    .pf-kpi-note { font-size: 12px; color: var(--pf-text-secondary); margin-top: 8px; font-weight: 600; }
    .pf-kpi-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
    .pf-banner { margin-bottom: 14px; border-radius: 14px; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.1); color: #065f46; padding: 14px 16px; font-size: 13px; font-weight: 700; }
    .dark .pf-banner { color: #a7f3d0; background: rgba(16, 185, 129, 0.14); }
    .pf-tabs { margin-bottom: 14px; }
    .pf-tab {
        border: 1px solid var(--pf-input-border);
        background: var(--pf-input-bg);
        color: var(--pf-text-secondary);
        border-radius: 999px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .02em;
        cursor: pointer;
        transition: all .18s ease;
    }
    .pf-tab.is-active { color: #fff; border-color: transparent; box-shadow: 0 10px 20px rgba(109, 40, 217, 0.18); }
    .pf-tab-pending.is-active { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .pf-tab-approved.is-active { background: linear-gradient(135deg, #059669, #10b981); }
    .pf-tab-rejected.is-active { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .pf-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .pf-panel-title { font-size: 17px; font-weight: 700; color: var(--pf-text-primary); }
    .pf-panel-subtitle { font-size: 12px; color: var(--pf-text-secondary); font-weight: 600; margin-top: 2px; }
    .pf-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }
    .pf-badge-pending { background: rgba(245, 158, 11, 0.16); color: #b45309; }
    .pf-badge-approved { background: rgba(16, 185, 129, 0.16); color: #047857; }
    .pf-badge-rejected { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }
    .dark .pf-badge-pending { color: #fcd34d; }
    .dark .pf-badge-approved { color: #6ee7b7; }
    .dark .pf-badge-rejected { color: #fca5a5; }
    .pf-request-list { display: grid; gap: 14px; }
    .pf-request-card { padding: 0; overflow: hidden; }
    .pf-request-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 18px 18px 14px; border-bottom: 1px solid var(--pf-table-row-border); }
    .pf-request-title { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
    .pf-request-link { color: #6d28d9; text-decoration: none; }
    .pf-request-link:hover { color: #7c3aed; }
    .pf-request-meta { padding: 0 18px 16px; }
    .pf-metric { flex: 1 1 180px; min-width: 0; }
    .pf-metric-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--pf-text-secondary); font-weight: 700; margin-bottom: 6px; }
    .pf-metric-value { display: block; font-size: 13px; font-weight: 700; color: var(--pf-text-primary); }
    .pf-metric-value.is-warning { color: #ea580c; }
    .pf-metric-value.is-success { color: #059669; }
    .pf-note-box { margin: 0 18px 16px; border-radius: 12px; border: 1px solid var(--pf-table-head-border); background: color-mix(in srgb, var(--pf-card-bg) 88%, #f8fafc); padding: 12px 14px; }
    .pf-note-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--pf-text-secondary); font-weight: 700; margin-bottom: 6px; }
    .pf-note-text { font-size: 13px; font-weight: 600; color: var(--pf-text-primary); line-height: 1.55; }
    .pf-request-actions { padding: 0 18px 18px; }
    .pf-inline-button {
        height: 36px;
        border-radius: 10px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        cursor: pointer;
        font-size: 12px;
        font-weight: 800;
        transition: transform .18s ease, filter .18s ease;
    }
    .pf-inline-button:hover { transform: translateY(-1px); filter: brightness(1.04); }
    .pf-inline-button-success { background: rgba(16, 185, 129, 0.14); color: #047857; }
    .pf-inline-button-danger { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }
    .dark .pf-inline-button-success { color: #6ee7b7; }
    .dark .pf-inline-button-danger { color: #fca5a5; }
    .pf-empty { text-align: center; padding: 48px 20px; }
    .pf-empty-icon {
        width: 74px;
        height: 74px;
        border-radius: 22px;
        margin: 0 auto 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(109, 40, 217, 0.12), rgba(59, 130, 246, 0.12));
        color: #6d28d9;
        font-size: 28px;
    }
    .pf-empty h3 { font-size: 22px; font-weight: 800; color: var(--pf-text-primary); letter-spacing: -0.02em; }
    .pf-empty p { max-width: 520px; margin: 10px auto 0; font-size: 14px; color: var(--pf-text-secondary); font-weight: 600; line-height: 1.6; }
    .pf-modal {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(6px);
    }
    .pf-modal.is-open { display: flex; }
    .pf-modal-card { width: min(100%, 560px); }
    .pf-modal-head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; margin-bottom: 16px; }
    .pf-modal-title { font-size: 18px; font-weight: 800; color: var(--pf-text-primary); }
    .pf-modal-subtitle { font-size: 12px; color: var(--pf-text-secondary); font-weight: 600; margin-top: 4px; }
    .pf-modal-close {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid var(--pf-input-border);
        background: var(--pf-input-bg);
        color: var(--pf-input-text);
        cursor: pointer;
    }
    .pf-textarea {
        width: 100%;
        min-height: 110px;
        border-radius: 12px;
        border: 1px solid var(--pf-input-border);
        background: var(--pf-input-bg);
        color: var(--pf-input-text);
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 600;
        resize: vertical;
    }
    .pf-field-label { display: block; margin-bottom: 8px; font-size: 12px; font-weight: 800; color: var(--pf-text-primary); }

    @media (max-width: 760px) {
        .prod-ft { padding: 14px; border-radius: 16px; }
        .pf-actions, .pf-tabs, .pf-request-actions, .pf-modal-actions { width: 100%; }
        .pf-action, .pf-tab, .pf-inline-button, .pf-modal-button { width: 100%; justify-content: center; }
        .pf-request-head, .pf-panel-head, .pf-topbar, .pf-modal-head { flex-direction: column; }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="prod-ft">
        <div class="pf-topbar">
            <div class="pf-brand">
                <span class="pf-logo"><i class="fa-solid fa-bolt"></i></span>
                <div>
                    <div class="pf-title">Solicitacoes de Antecipacao</div>
                    <div class="pf-subtitle">Mesmo shell visual do dashboard de producao, com foco em triagem, prazo e decisao rapida.</div>
                </div>
            </div>

            <div class="pf-actions">
                <a href="{{ route('production.dashboard') }}" class="pf-action pf-action-primary"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
                <a href="{{ route('kanban.index') }}" class="pf-action pf-action-success"><i class="fa-solid fa-table-columns"></i><span>Abrir Kanban</span></a>
            </div>
        </div>

        @if(session('success'))
            <div class="pf-banner">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="pf-kpis">
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #d97706, #f59e0b);"><i class="fa-solid fa-hourglass-half"></i></span>
                <div class="pf-kpi-value">{{ $pendingRequests->count() }}</div>
                <div class="pf-kpi-label">Pendentes</div>
                <div class="pf-kpi-note">Solicitacoes aguardando aprovacao ou rejeicao da producao.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #059669, #10b981);"><i class="fa-solid fa-circle-check"></i></span>
                <div class="pf-kpi-value">{{ $approvedRequests->count() }}</div>
                <div class="pf-kpi-label">Aprovadas</div>
                <div class="pf-kpi-note">Pedidos que ja tiveram a data de entrega ajustada.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);"><i class="fa-solid fa-ban"></i></span>
                <div class="pf-kpi-value">{{ $rejectedRequests->count() }}</div>
                <div class="pf-kpi-label">Rejeitadas</div>
                <div class="pf-kpi-note">Demandas recusadas e registradas para historico operacional.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #2563eb, #3b82f6);"><i class="fa-solid fa-calendar-day"></i></span>
                <div class="pf-kpi-value">{{ $avgDaysSaved }}</div>
                <div class="pf-kpi-label">Media de dias solicitados</div>
                <div class="pf-kpi-note">
                    @if($latestRequestAt)
                        Ultima entrada em {{ \Carbon\Carbon::parse($latestRequestAt)->format('d/m/Y H:i') }}.
                    @else
                        Nenhuma solicitacao registrada neste tenant.
                    @endif
                </div>
            </div>
        </div>

        <div class="pf-card">
            <div class="pf-panel-head">
                <div>
                    <div class="pf-panel-title">Fila de solicitacoes</div>
                    <div class="pf-panel-subtitle">Visual unico para analisar impacto no prazo, motivacao e decisao tomada.</div>
                </div>
                <a href="{{ route('production.index') }}" class="pf-action pf-action-muted">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Lista de Ordens</span>
                </a>
            </div>

            @if($requests->isEmpty())
                <div class="pf-empty">
                    <div class="pf-empty-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <h3>Nenhuma solicitacao no momento</h3>
                    <p>Quando um pedido pedir antecipacao de entrega, a analise aparece aqui com contexto suficiente para decidir rapido sem sair do fluxo de producao.</p>
                </div>
            @else
                <div class="pf-tabs" role="tablist" aria-label="Status das solicitacoes">
                    <button type="button" class="pf-tab pf-tab-pending is-active" id="tab-pendente" onclick="showTab('pendente')">Pendentes ({{ $pendingRequests->count() }})</button>
                    <button type="button" class="pf-tab pf-tab-approved" id="tab-aprovado" onclick="showTab('aprovado')">Aprovadas ({{ $approvedRequests->count() }})</button>
                    <button type="button" class="pf-tab pf-tab-rejected" id="tab-rejeitado" onclick="showTab('rejeitado')">Rejeitadas ({{ $rejectedRequests->count() }})</button>
                </div>

                <div id="content-pendente" class="tab-content">
                    @include('production.partials.delivery-request-list', ['items' => $pendingRequests, 'status' => 'pendente'])
                </div>
                <div id="content-aprovado" class="tab-content hidden">
                    @include('production.partials.delivery-request-list', ['items' => $approvedRequests, 'status' => 'aprovado'])
                </div>
                <div id="content-rejeitado" class="tab-content hidden">
                    @include('production.partials.delivery-request-list', ['items' => $rejectedRequests, 'status' => 'rejeitado'])
                </div>
            @endif
        </div>
    </section>
</div>

<div id="approve-modal" class="pf-modal" aria-hidden="true">
    <div class="pf-card pf-modal-card">
        <div class="pf-modal-head">
            <div>
                <div class="pf-modal-title">Aprovar solicitacao</div>
                <div class="pf-modal-subtitle">Use observacoes apenas quando a aprovacao precisar deixar contexto para o time.</div>
            </div>
            <button type="button" class="pf-modal-close" onclick="closeApproveModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="approve-form" method="POST">
            @csrf
            <label for="approve-notes" class="pf-field-label">Observacoes internas (opcional)</label>
            <textarea id="approve-notes" name="review_notes" maxlength="500" class="pf-textarea" placeholder="Ex.: encaixado na fila de urgencias por disponibilidade do setor."></textarea>

            <div class="pf-modal-actions mt-4">
                <button type="button" onclick="closeApproveModal()" class="pf-modal-button pf-modal-button-muted">Cancelar</button>
                <button type="submit" class="pf-modal-button pf-modal-button-success">Aprovar solicitacao</button>
            </div>
        </form>
    </div>
</div>

<div id="reject-modal" class="pf-modal" aria-hidden="true">
    <div class="pf-card pf-modal-card">
        <div class="pf-modal-head">
            <div>
                <div class="pf-modal-title">Rejeitar solicitacao</div>
                <div class="pf-modal-subtitle">Explique o bloqueio para que a equipe comercial tenha retorno claro.</div>
            </div>
            <button type="button" class="pf-modal-close" onclick="closeRejectModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="reject-form" method="POST">
            @csrf
            <label for="reject-notes" class="pf-field-label">Motivo da rejeicao</label>
            <textarea id="reject-notes" name="review_notes" maxlength="500" required class="pf-textarea" placeholder="Ex.: capacidade do setor ocupada e risco de atraso em pedidos ja prometidos."></textarea>

            <div class="pf-modal-actions mt-4">
                <button type="button" onclick="closeRejectModal()" class="pf-modal-button pf-modal-button-muted">Cancelar</button>
                <button type="submit" class="pf-modal-button pf-modal-button-danger">Rejeitar solicitacao</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showTab(status) {
        document.querySelectorAll('.tab-content').forEach((content) => content.classList.add('hidden'));
        document.querySelectorAll('.pf-tab').forEach((tab) => tab.classList.remove('is-active'));

        document.getElementById(`content-${status}`)?.classList.remove('hidden');
        document.getElementById(`tab-${status}`)?.classList.add('is-active');
    }

    function openApproveModal(requestId) {
        const action = "{{ route('production.delivery-requests.approve', ['deliveryRequest' => '__ID__']) }}".replace('__ID__', requestId);
        document.getElementById('approve-form').action = action;
        document.getElementById('approve-modal').classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    }

    function closeApproveModal() {
        document.getElementById('approve-modal').classList.remove('is-open');
        document.getElementById('approve-form').reset();
        document.body.classList.remove('overflow-hidden');
    }

    function openRejectModal(requestId) {
        const action = "{{ route('production.delivery-requests.reject', ['deliveryRequest' => '__ID__']) }}".replace('__ID__', requestId);
        document.getElementById('reject-form').action = action;
        document.getElementById('reject-modal').classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').classList.remove('is-open');
        document.getElementById('reject-form').reset();
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('approve-modal')?.addEventListener('click', function (event) {
        if (event.target === this) closeApproveModal();
    });

    document.getElementById('reject-modal')?.addEventListener('click', function (event) {
        if (event.target === this) closeRejectModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeApproveModal();
            closeRejectModal();
        }
    });
</script>
@endsection
