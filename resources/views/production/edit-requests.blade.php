@extends('layouts.admin')

@section('content')
@php
    $latestRequestAt = optional($editRequests->getCollection()->max('created_at'));
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

    .pf-topbar, .pf-brand, .pf-actions, .pf-kpis, .pf-request-meta, .pf-request-actions, .pf-modal-actions, .pf-status-row {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }
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
    .pf-action:hover, .pf-modal-button:hover, .pf-inline-button:hover { transform: translateY(-1px); filter: brightness(1.03); }
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
    .pf-banner { margin-bottom: 14px; border-radius: 14px; padding: 14px 16px; font-size: 13px; font-weight: 700; }
    .pf-banner-success { border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.1); color: #065f46; }
    .pf-banner-error { border: 1px solid rgba(239, 68, 68, 0.25); background: rgba(239, 68, 68, 0.1); color: #991b1b; }
    .dark .pf-banner-success { color: #a7f3d0; background: rgba(16, 185, 129, 0.14); }
    .dark .pf-banner-error { color: #fecaca; background: rgba(239, 68, 68, 0.14); }
    .pf-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .pf-panel-title { font-size: 17px; font-weight: 700; color: var(--pf-text-primary); }
    .pf-panel-subtitle { font-size: 12px; color: var(--pf-text-secondary); font-weight: 600; margin-top: 2px; }
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
    .pf-inline-button-view { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
    .pf-inline-button-success { background: rgba(16, 185, 129, 0.14); color: #047857; }
    .pf-inline-button-danger { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }
    .dark .pf-inline-button-view { color: #93c5fd; }
    .dark .pf-inline-button-success { color: #6ee7b7; }
    .dark .pf-inline-button-danger { color: #fca5a5; }
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
    .pf-pager { margin-top: 14px; }
    .pf-modal {
        --pf-text-primary: #0f172a;
        --pf-text-secondary: #64748b;
        --pf-input-bg: #ffffff;
        --pf-input-border: #d6d9e2;
        --pf-input-text: #334155;
        --pf-card-bg: #ffffff;
        --pf-card-border: #dde2ea;
        --pf-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
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
    .dark .pf-modal {
        --pf-text-primary: #e2e8f0;
        --pf-text-secondary: #94a3b8;
        --pf-input-bg: #0b1322;
        --pf-input-border: rgba(148, 163, 184, 0.3);
        --pf-input-text: #e2e8f0;
        --pf-card-bg: #111827;
        --pf-card-border: rgba(148, 163, 184, 0.22);
        --pf-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
    }
    .pf-modal.is-open { display: flex; }
    .pf-modal-card { width: min(100%, 1160px); max-height: calc(100vh - 32px); overflow: auto; border-radius: 30px; }
    .pf-modal-card-sm { width: min(100%, 560px); }
    .pf-modal-head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; margin-bottom: 18px; position: sticky; top: 0; z-index: 2; background: var(--pf-card-bg); padding-bottom: 8px; }
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
    .pf-modal-body { color: var(--pf-text-primary); padding-bottom: 6px; }

    @media (max-width: 760px) {
        .prod-ft { padding: 14px; border-radius: 16px; }
        .pf-actions, .pf-request-actions, .pf-modal-actions { width: 100%; }
        .pf-action, .pf-inline-button, .pf-modal-button { width: 100%; justify-content: center; }
        .pf-request-head, .pf-panel-head, .pf-topbar, .pf-modal-head { flex-direction: column; }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="prod-ft">
        <div class="pf-topbar">
            <div class="pf-brand">
                <span class="pf-logo"><i class="fa-solid fa-pen-to-square"></i></span>
                <div>
                    <div class="pf-title">Solicitacoes de Edicao</div>
                    <div class="pf-subtitle">Mesmo shell visual da central de producao, com foco em revisao rapida e decisao por pedido.</div>
                </div>
            </div>

            <div class="pf-actions">
                <a href="{{ route('production.dashboard') }}" class="pf-action pf-action-primary"><i class="fa-solid fa-chart-line"></i><span>Dashboard</span></a>
                <a href="{{ route('kanban.index') }}" class="pf-action pf-action-success"><i class="fa-solid fa-table-columns"></i><span>Abrir Kanban</span></a>
            </div>
        </div>

        @if(session('success'))
            <div class="pf-banner pf-banner-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="pf-banner pf-banner-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="pf-kpis">
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #d97706, #f59e0b);"><i class="fa-solid fa-hourglass-half"></i></span>
                <div class="pf-kpi-value">{{ $statusCounts['pending'] ?? 0 }}</div>
                <div class="pf-kpi-label">Pendentes</div>
                <div class="pf-kpi-note">Solicitacoes aguardando avaliacao da producao.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #059669, #10b981);"><i class="fa-solid fa-circle-check"></i></span>
                <div class="pf-kpi-value">{{ $statusCounts['approved'] ?? 0 }}</div>
                <div class="pf-kpi-label">Aprovadas</div>
                <div class="pf-kpi-note">Pedidos com alteracoes autorizadas e aplicadas.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);"><i class="fa-solid fa-ban"></i></span>
                <div class="pf-kpi-value">{{ $statusCounts['rejected'] ?? 0 }}</div>
                <div class="pf-kpi-label">Rejeitadas</div>
                <div class="pf-kpi-note">Solicitacoes encerradas com devolutiva para o solicitante.</div>
            </div>
            <div class="pf-card pf-kpi">
                <span class="pf-kpi-icon" style="background: linear-gradient(135deg, #2563eb, #3b82f6);"><i class="fa-solid fa-layer-group"></i></span>
                <div class="pf-kpi-value">{{ $editRequests->total() }}</div>
                <div class="pf-kpi-label">Total</div>
                <div class="pf-kpi-note">
                    @if($latestRequestAt)
                        Ultima solicitacao nesta pagina: {{ $latestRequestAt->format('d/m/Y H:i') }}
                    @else
                        Nenhuma solicitacao carregada nesta pagina.
                    @endif
                </div>
            </div>
        </div>

        @if($editRequests->isEmpty())
            <div class="pf-card pf-empty">
                <div class="pf-empty-icon"><i class="fa-solid fa-file-pen"></i></div>
                <h3>Nenhuma solicitacao de edicao</h3>
                <p>Nao ha pedidos aguardando revisao nesta tela. Quando uma alteracao for solicitada, ela aparecera aqui com o mesmo fluxo de aprovacao e rejeicao.</p>
            </div>
        @else
            <div class="pf-card">
                <div class="pf-panel-head">
                    <div>
                        <div class="pf-panel-title">Fila de revisao</div>
                        <div class="pf-panel-subtitle">Lista paginada de solicitacoes com status, motivo, historico e acoes de producao.</div>
                    </div>
                    <div class="pf-status-row">
                        <span class="pf-badge pf-badge-pending">Pendentes {{ $statusCounts['pending'] ?? 0 }}</span>
                        <span class="pf-badge pf-badge-approved">Aprovadas {{ $statusCounts['approved'] ?? 0 }}</span>
                        <span class="pf-badge pf-badge-rejected">Rejeitadas {{ $statusCounts['rejected'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="pf-request-list">
                    @foreach($editRequests as $editRequest)
                        @php
                            $badgeClass = match($editRequest->status) {
                                'approved' => 'pf-badge-approved',
                                'rejected' => 'pf-badge-rejected',
                                default => 'pf-badge-pending',
                            };
                            $badgeLabel = match($editRequest->status) {
                                'approved' => 'Aprovada',
                                'rejected' => 'Rejeitada',
                                default => 'Pendente',
                            };
                        @endphp
                        <article class="pf-card pf-request-card">
                            <div class="pf-request-head">
                                <div>
                                    <div class="pf-request-title">
                                        @if($editRequest->order)
                                            <a href="{{ route('orders.show', $editRequest->order->id) }}" class="pf-request-link">#{{ str_pad($editRequest->order->id, 6, '0', STR_PAD_LEFT) }}</a>
                                        @else
                                            <span>Pedido removido</span>
                                        @endif
                                    </div>
                                    <div class="pf-panel-subtitle">
                                        @if($editRequest->order)
                                            R$ {{ number_format($editRequest->order->total, 2, ',', '.') }}
                                        @else
                                            Pedido sem vinculo disponivel
                                        @endif
                                    </div>
                                </div>
                                <span class="pf-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </div>

                            <div class="pf-request-meta">
                                <div class="pf-metric">
                                    <span class="pf-metric-label">Cliente</span>
                                    <span class="pf-metric-value">{{ $editRequest->order?->client?->name ?? 'N/A' }}</span>
                                </div>
                                <div class="pf-metric">
                                    <span class="pf-metric-label">Solicitado por</span>
                                    <span class="pf-metric-value">{{ $editRequest->user->name ?? 'Removido' }}</span>
                                </div>
                                <div class="pf-metric">
                                    <span class="pf-metric-label">Data da solicitacao</span>
                                    <span class="pf-metric-value">{{ $editRequest->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="pf-metric">
                                    <span class="pf-metric-label">Revisado por</span>
                                    <span class="pf-metric-value">{{ $editRequest->approvedBy?->name ?? 'Aguardando analise' }}</span>
                                </div>
                            </div>

                            <div class="pf-note-box">
                                <div class="pf-note-label">Motivo da solicitacao</div>
                                <div class="pf-note-text">{{ $editRequest->reason }}</div>
                            </div>

                            @if($editRequest->admin_notes)
                                <div class="pf-note-box">
                                    <div class="pf-note-label">Observacoes da analise</div>
                                    <div class="pf-note-text">{{ $editRequest->admin_notes }}</div>
                                </div>
                            @endif

                            <div class="pf-request-actions">
                                <button type="button" class="pf-inline-button pf-inline-button-view" onclick="viewChanges({{ $editRequest->id }})">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Ver Alteracoes</span>
                                </button>

                                @if($editRequest->status === 'pending')
                                    <button type="button" class="pf-inline-button pf-inline-button-success" onclick="approveEdit({{ $editRequest->id }})">
                                        <i class="fa-solid fa-check"></i>
                                        <span>Aprovar</span>
                                    </button>
                                    <button type="button" class="pf-inline-button pf-inline-button-danger" onclick="rejectEdit({{ $editRequest->id }})">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>Rejeitar</span>
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($editRequests->hasPages())
                    <div class="pf-pager">
                        {{ $editRequests->links() }}
                    </div>
                @endif
            </div>
        @endif
    </section>
</div>

<div id="changes-modal" class="pf-modal" aria-hidden="true">
    <div class="pf-card pf-modal-card">
        <div class="pf-modal-head">
            <div>
                <div class="pf-modal-title">Alteracoes solicitadas</div>
                <div class="pf-modal-subtitle">Comparativo completo da requisicao de edicao.</div>
            </div>
            <button type="button" class="pf-modal-close" onclick="closeChangesModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="changes-content" class="pf-modal-body"></div>
    </div>
</div>

<div id="approve-modal" class="pf-modal" aria-hidden="true">
    <div class="pf-card pf-modal-card pf-modal-card-sm">
        <div class="pf-modal-head">
            <div>
                <div class="pf-modal-title">Aprovar edicao</div>
                <div class="pf-modal-subtitle">A alteracao sera aplicada ao pedido imediatamente.</div>
            </div>
            <button type="button" class="pf-modal-close" onclick="closeApproveModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="approve-form" method="POST">
            @csrf
            <label class="pf-field-label" for="approve-admin-notes">Observacoes (opcional)</label>
            <textarea id="approve-admin-notes" name="admin_notes" class="pf-textarea" placeholder="Adicione observacoes sobre a aprovacao..."></textarea>
            <div class="pf-modal-actions" style="margin-top:16px;">
                <button type="button" class="pf-modal-button pf-modal-button-muted" onclick="closeApproveModal()">Cancelar</button>
                <button type="submit" class="pf-modal-button pf-modal-button-success">Aprovar Edicao</button>
            </div>
        </form>
    </div>
</div>

<div id="reject-modal" class="pf-modal" aria-hidden="true">
    <div class="pf-card pf-modal-card pf-modal-card-sm">
        <div class="pf-modal-head">
            <div>
                <div class="pf-modal-title">Rejeitar edicao</div>
                <div class="pf-modal-subtitle">Informe o motivo para devolver a solicitacao ao time comercial.</div>
            </div>
            <button type="button" class="pf-modal-close" onclick="closeRejectModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="reject-form" method="POST">
            @csrf
            <label class="pf-field-label" for="reject-admin-notes">Motivo da rejeicao</label>
            <textarea id="reject-admin-notes" name="admin_notes" class="pf-textarea" required placeholder="Explique o motivo da rejeicao..."></textarea>
            <div class="pf-modal-actions" style="margin-top:16px;">
                <button type="button" class="pf-modal-button pf-modal-button-muted" onclick="closeRejectModal()">Cancelar</button>
                <button type="submit" class="pf-modal-button pf-modal-button-danger">Rejeitar Edicao</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.openModal = function (id) {
        document.getElementById(id).classList.add('is-open');
    };

    window.closeModal = function (id) {
        document.getElementById(id).classList.remove('is-open');
    };

    window.viewChanges = function (editRequestId) {
        fetch("{{ route('production.edit-requests.changes', ['editRequest' => '__ID__']) }}".replace('__ID__', editRequestId))
            .then(response => response.json())
            .then(data => {
                document.getElementById('changes-content').innerHTML = data.html;
                window.openModal('changes-modal');
            })
            .catch(error => {
                console.error('Erro ao carregar alteracoes:', error);
                alert('Erro ao carregar as alteracoes.');
            });
    };

    window.closeChangesModal = function () {
        window.closeModal('changes-modal');
    };

    window.approveEdit = function (editRequestId) {
        document.getElementById('approve-form').action = "{{ route('production.edit-requests.approve', ['editRequest' => '__ID__']) }}".replace('__ID__', editRequestId);
        window.openModal('approve-modal');
    };

    window.rejectEdit = function (editRequestId) {
        document.getElementById('reject-form').action = "{{ route('production.edit-requests.reject', ['editRequest' => '__ID__']) }}".replace('__ID__', editRequestId);
        window.openModal('reject-modal');
    };

    window.closeApproveModal = function () {
        window.closeModal('approve-modal');
    };

    window.closeRejectModal = function () {
        window.closeModal('reject-modal');
    };

    ['changes-modal', 'approve-modal', 'reject-modal'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function (event) {
            if (event.target === this) {
                window.closeModal(id);
            }
        });
    });
</script>
@endsection
