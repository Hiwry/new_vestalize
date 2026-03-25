@extends('layouts.admin')

@section('content')
@php
    $editPending   = $editRequests->where('status', 'pending');
    $editApproved  = $editRequests->whereIn('status', ['approved', 'completed']);
    $editRejected  = $editRequests->where('status', 'rejected');

    $delivPending  = $deliveryRequests->where('status', 'pendente');
    $delivApproved = $deliveryRequests->where('status', 'aprovado');
    $delivRejected = $deliveryRequests->where('status', 'rejeitado');

    $totalPending  = $editPending->count()  + $delivPending->count();
    $totalApproved = $editApproved->count() + $delivApproved->count();
    $totalRejected = $editRejected->count() + $delivRejected->count();
    $total         = $editRequests->count() + $deliveryRequests->count();
@endphp

<style>
    .mr-wrap {
        --mr-from:         #f3f4f8;
        --mr-to:           #eceff4;
        --mr-border:       #d8dce6;
        --mr-text:         #0f172a;
        --mr-sub:          #64748b;
        --mr-input-bg:     #ffffff;
        --mr-input-border: #d6d9e2;
        --mr-input-text:   #334155;
        --mr-card-bg:      #ffffff;
        --mr-card-border:  #dde2ea;
        --mr-card-shadow:  0 8px 20px rgba(15,23,42,0.05);
        --mr-row-border:   #eef1f6;
        --mr-tab-active-bg:   #ede9fe;
        --mr-tab-active-text: #5b21b6;
        background: linear-gradient(180deg, var(--mr-from), var(--mr-to));
        border: 1px solid var(--mr-border);
        border-radius: 20px;
        padding: 22px;
        color: var(--mr-text);
        box-shadow: 0 20px 50px rgba(15,23,42,0.08);
    }
    .dark .mr-wrap {
        --mr-from:         #0f172a;
        --mr-to:           #0b1322;
        --mr-border:       rgba(148,163,184,0.25);
        --mr-text:         #e2e8f0;
        --mr-sub:          #94a3b8;
        --mr-input-bg:     #0b1322;
        --mr-input-border: rgba(148,163,184,0.3);
        --mr-input-text:   #e2e8f0;
        --mr-card-bg:      #111827;
        --mr-card-border:  rgba(148,163,184,0.22);
        --mr-card-shadow:  0 18px 38px rgba(2,6,23,0.55);
        --mr-row-border:   rgba(148,163,184,0.16);
        --mr-tab-active-bg:   rgba(124,58,237,0.24);
        --mr-tab-active-text: #e9d5ff;
    }

    .mr-topbar  { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px; }
    .mr-brand   { display:flex; align-items:center; gap:12px; flex:1 1 260px; min-width:0; }
    .mr-logo    { width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,#6d28d9,#8b5cf6); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .mr-title   { font-size:18px; font-weight:700; letter-spacing:-.015em; color:var(--mr-text); }
    .mr-subtitle{ font-size:12px; font-weight:600; color:var(--mr-sub); margin-top:2px; }

    .mr-kpis  { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px; }
    @media (max-width:900px) { .mr-kpis { grid-template-columns:repeat(2,1fr); } }
    .mr-kpi { background:var(--mr-card-bg); border:1px solid var(--mr-card-border); border-radius:14px; padding:16px; box-shadow:var(--mr-card-shadow); }
    .mr-kpi-icon { width:36px; height:36px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; font-size:14px; margin-bottom:10px; }
    .mr-kpi-value { font-size:26px; font-weight:800; letter-spacing:-.03em; color:var(--mr-text); line-height:1; }
    .mr-kpi-label { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--mr-sub); font-weight:700; margin-top:5px; }
    .mr-kpi-pending  .mr-kpi-icon { background:rgba(245,158,11,.12); color:#d97706; }
    .mr-kpi-approved .mr-kpi-icon { background:rgba(16,185,129,.1);  color:#059669; }
    .mr-kpi-rejected .mr-kpi-icon { background:rgba(239,68,68,.1);   color:#dc2626; }
    .mr-kpi-total    .mr-kpi-icon { background:rgba(109,40,217,.1);  color:#6d28d9; }
    .dark .mr-kpi-pending  .mr-kpi-icon { color:#fbbf24; }
    .dark .mr-kpi-approved .mr-kpi-icon { color:#34d399; }
    .dark .mr-kpi-rejected .mr-kpi-icon { color:#f87171; }
    .dark .mr-kpi-total    .mr-kpi-icon { color:#a78bfa; }

    .mr-alert { border-radius:12px; padding:12px 16px; font-size:13px; font-weight:700; margin-bottom:14px; background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.22); color:#065f46; }
    .dark .mr-alert { color:#a7f3d0; background:rgba(16,185,129,.12); }

    .mr-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; border-bottom:1px solid var(--mr-card-border); padding-bottom:0; }
    .mr-tab {
        padding:10px 16px; font-size:13px; font-weight:700; color:var(--mr-sub);
        border-radius:8px 8px 0 0; cursor:pointer; border:none; background:transparent;
        border-bottom:2px solid transparent; margin-bottom:-1px;
        transition:color .15s ease, border-color .15s ease, background .15s ease;
    }
    .mr-tab:hover { color:var(--mr-text); background:rgba(109,40,217,.05); }
    .mr-tab.is-active { color:var(--mr-tab-active-text); border-bottom-color:#7c3aed; background:var(--mr-tab-active-bg); }

    .mr-subtabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
    .mr-subtab {
        padding:6px 14px; font-size:11px; font-weight:800; letter-spacing:.03em;
        color:var(--mr-sub); border:1px solid var(--mr-input-border); background:var(--mr-input-bg);
        border-radius:999px; cursor:pointer; transition:all .15s ease;
    }
    .mr-subtab.is-active { background:var(--mr-tab-active-bg); color:var(--mr-tab-active-text); border-color:transparent; }

    .mr-card-list { display:grid; gap:10px; }
    .mr-card { background:var(--mr-card-bg); border:1px solid var(--mr-card-border); border-radius:14px; box-shadow:var(--mr-card-shadow); overflow:hidden; }
    .mr-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:14px 16px 12px; border-bottom:1px solid var(--mr-row-border); flex-wrap:wrap; }
    .mr-order-id   { font-size:16px; font-weight:800; letter-spacing:-.01em; }
    .mr-order-link { color:#6d28d9; text-decoration:none; }
    .mr-order-link:hover { color:#7c3aed; }
    .mr-client-name { font-size:12px; font-weight:600; color:var(--mr-sub); margin-top:2px; }
    .mr-meta { display:flex; gap:16px; flex-wrap:wrap; padding:12px 16px; }
    .mr-metric { flex:1 1 140px; min-width:0; }
    .mr-metric-label { display:block; font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--mr-sub); font-weight:700; margin-bottom:4px; }
    .mr-metric-value { display:block; font-size:13px; font-weight:700; color:var(--mr-text); }
    .mr-metric-warn { color:#ea580c; }

    .mr-note { margin:0 16px 14px; padding:10px 13px; border-radius:10px; border:1px solid var(--mr-row-border); background:color-mix(in srgb,var(--mr-card-bg) 92%,#f8fafc); }
    .mr-note-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--mr-sub); font-weight:700; margin-bottom:5px; }
    .mr-note-text  { font-size:13px; font-weight:600; color:var(--mr-text); line-height:1.6; }
    .mr-note-admin { border-color:rgba(109,40,217,.18); background:rgba(109,40,217,.04); }
    .mr-note-admin .mr-note-label { color:#6d28d9; }
    .dark .mr-note-admin .mr-note-label { color:#a78bfa; }

    .mr-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }
    .mr-badge-pending   { background:rgba(245,158,11,.12);  color:#92400e; }
    .mr-badge-approved  { background:rgba(16,185,129,.12);  color:#065f46; }
    .mr-badge-rejected  { background:rgba(239,68,68,.12);   color:#991b1b; }
    .mr-badge-completed { background:rgba(109,40,217,.12);  color:#4c1d95; }
    .dark .mr-badge-pending   { color:#fcd34d; }
    .dark .mr-badge-approved  { color:#6ee7b7; }
    .dark .mr-badge-rejected  { color:#fca5a5; }
    .dark .mr-badge-completed { color:#c4b5fd; }

    .mr-btn { height:36px; padding:0 16px; display:inline-flex; align-items:center; gap:8px; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none; border:0; cursor:pointer; transition:transform .15s ease, filter .15s ease; }
    .mr-btn:hover { transform:translateY(-1px); filter:brightness(1.04); }
    .mr-btn-primary { background:linear-gradient(135deg,#6d28d9,#7c3aed); color:#fff; box-shadow:0 6px 16px rgba(109,40,217,.2); }

    .mr-empty { text-align:center; padding:40px 16px; }
    .mr-empty-icon { width:56px; height:56px; border-radius:16px; margin:0 auto 12px; display:inline-flex; align-items:center; justify-content:center; background:color-mix(in srgb,var(--mr-card-bg) 60%,#ede9fe); color:#7c3aed; font-size:22px; }
    .mr-empty h3 { font-size:17px; font-weight:800; color:var(--mr-text); margin-bottom:6px; }
    .mr-empty p  { font-size:13px; color:var(--mr-sub); font-weight:600; line-height:1.6; max-width:420px; margin:0 auto; }

    @media (max-width:640px) {
        .mr-wrap { padding:14px; border-radius:16px; }
        .mr-topbar, .mr-card-head { flex-direction:column; }
    }
</style>

<div class="max-w-[1400px] mx-auto pt-2 md:pt-3 pb-6">
<section class="mr-wrap">

    <div class="mr-topbar">
        <div class="mr-brand">
            <span class="mr-logo"><i class="fa-solid fa-file-signature"></i></span>
            <div>
                <div class="mr-title">Minhas Solicitacoes</div>
                <div class="mr-subtitle">Edicao de pedidos · Antecipacao de entrega</div>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="mr-btn mr-btn-primary">
            <i class="fa-solid fa-box-open"></i><span>Meus Pedidos</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mr-alert"><i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}</div>
    @endif

    <div class="mr-kpis">
        <div class="mr-kpi mr-kpi-pending">
            <div class="mr-kpi-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="mr-kpi-value">{{ $totalPending }}</div>
            <div class="mr-kpi-label">Aguardando</div>
        </div>
        <div class="mr-kpi mr-kpi-approved">
            <div class="mr-kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="mr-kpi-value">{{ $totalApproved }}</div>
            <div class="mr-kpi-label">Aprovadas</div>
        </div>
        <div class="mr-kpi mr-kpi-rejected">
            <div class="mr-kpi-icon"><i class="fa-solid fa-ban"></i></div>
            <div class="mr-kpi-value">{{ $totalRejected }}</div>
            <div class="mr-kpi-label">Recusadas</div>
        </div>
        <div class="mr-kpi mr-kpi-total">
            <div class="mr-kpi-icon"><i class="fa-solid fa-layer-group"></i></div>
            <div class="mr-kpi-value">{{ $total }}</div>
            <div class="mr-kpi-label">Total</div>
        </div>
    </div>

    <div class="mr-tabs">
        <button class="mr-tab is-active" data-main-tab="edit">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Edicao de Pedido
            @if($editRequests->count())<span class="opacity-60 ml-1 text-xs">({{ $editRequests->count() }})</span>@endif
        </button>
        <button class="mr-tab" data-main-tab="delivery">
            <i class="fa-solid fa-bolt mr-1"></i> Antecipacao de Entrega
            @if($deliveryRequests->count())<span class="opacity-60 ml-1 text-xs">({{ $deliveryRequests->count() }})</span>@endif
        </button>
    </div>

    <div id="panel-edit">
        @if($editRequests->isEmpty())
            <div class="mr-card">
                <div class="mr-empty">
                    <div class="mr-empty-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <h3>Nenhuma solicitacao de edicao</h3>
                    <p>Voce ainda nao enviou nenhuma solicitacao para editar um pedido. Acesse um pedido finalizado e clique em "Solicitar Edicao".</p>
                </div>
            </div>
        @else
            <div class="mr-subtabs">
                <button class="mr-subtab is-active" data-sub-section="edit" data-sub-tab="pending">Aguardando ({{ $editPending->count() }})</button>
                <button class="mr-subtab" data-sub-section="edit" data-sub-tab="approved">Aprovadas ({{ $editApproved->count() }})</button>
                <button class="mr-subtab" data-sub-section="edit" data-sub-tab="rejected">Recusadas ({{ $editRejected->count() }})</button>
            </div>
            <div id="edit-panel-pending" class="mr-card-list">
                @forelse($editPending as $req)
                    @include('vendor.requests.partials.edit-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-clock"></i></div><h3>Nenhuma pendente</h3></div></div>
                @endforelse
            </div>
            <div id="edit-panel-approved" class="mr-card-list" style="display:none;">
                @forelse($editApproved as $req)
                    @include('vendor.requests.partials.edit-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-circle-check"></i></div><h3>Nenhuma aprovada</h3></div></div>
                @endforelse
            </div>
            <div id="edit-panel-rejected" class="mr-card-list" style="display:none;">
                @forelse($editRejected as $req)
                    @include('vendor.requests.partials.edit-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-ban"></i></div><h3>Nenhuma recusada</h3></div></div>
                @endforelse
            </div>
        @endif
    </div>

    <div id="panel-delivery" style="display:none;">
        @if($deliveryRequests->isEmpty())
            <div class="mr-card">
                <div class="mr-empty">
                    <div class="mr-empty-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Nenhuma solicitacao de antecipacao</h3>
                    <p>Voce ainda nao enviou nenhum pedido de antecipacao de entrega.</p>
                </div>
            </div>
        @else
            <div class="mr-subtabs">
                <button class="mr-subtab is-active" data-sub-section="delivery" data-sub-tab="pending">Aguardando ({{ $delivPending->count() }})</button>
                <button class="mr-subtab" data-sub-section="delivery" data-sub-tab="approved">Aprovadas ({{ $delivApproved->count() }})</button>
                <button class="mr-subtab" data-sub-section="delivery" data-sub-tab="rejected">Recusadas ({{ $delivRejected->count() }})</button>
            </div>
            <div id="delivery-panel-pending" class="mr-card-list">
                @forelse($delivPending as $req)
                    @include('vendor.requests.partials.delivery-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-clock"></i></div><h3>Nenhuma pendente</h3></div></div>
                @endforelse
            </div>
            <div id="delivery-panel-approved" class="mr-card-list" style="display:none;">
                @forelse($delivApproved as $req)
                    @include('vendor.requests.partials.delivery-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-circle-check"></i></div><h3>Nenhuma aprovada</h3></div></div>
                @endforelse
            </div>
            <div id="delivery-panel-rejected" class="mr-card-list" style="display:none;">
                @forelse($delivRejected as $req)
                    @include('vendor.requests.partials.delivery-card', ['req' => $req])
                @empty
                    <div class="mr-card"><div class="mr-empty"><div class="mr-empty-icon"><i class="fa-solid fa-ban"></i></div><h3>Nenhuma recusada</h3></div></div>
                @endforelse
            </div>
        @endif
    </div>

</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mr-tab[data-main-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-main-tab');
            document.getElementById('panel-edit').style.display     = 'none';
            document.getElementById('panel-delivery').style.display = 'none';
            document.querySelectorAll('.mr-tab[data-main-tab]').forEach(function (t) { t.classList.remove('is-active'); });
            var panel = document.getElementById('panel-' + tab);
            if (panel) panel.style.display = 'block';
            btn.classList.add('is-active');
        });
    });

    document.querySelectorAll('.mr-subtab[data-sub-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var section = btn.getAttribute('data-sub-section');
            var status  = btn.getAttribute('data-sub-tab');
            var parentPanel = document.getElementById('panel-' + section);
            if (parentPanel) {
                parentPanel.querySelectorAll('.mr-subtab').forEach(function (t) { t.classList.remove('is-active'); });
            }
            btn.classList.add('is-active');
            ['pending', 'approved', 'rejected'].forEach(function (s) {
                var el = document.getElementById(section + '-panel-' + s);
                if (el) el.style.display = (s === status) ? 'block' : 'none';
            });
        });
    });
});
</script>
@endsection