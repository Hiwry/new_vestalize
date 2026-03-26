@extends('layouts.admin')

@section('content')
<style>
    .qb-wrap {
        --qb-from: #f3f4f8;
        --qb-to: #eceff4;
        --qb-border: #d8dce6;
        --qb-text: #0f172a;
        --qb-muted: #64748b;
        --qb-card-bg: #ffffff;
        --qb-card-border: #dde2ea;
        --qb-card-shadow: 0 8px 20px rgba(15,23,42,.05);
        --qb-row-border: #eef1f6;
        --qb-head-border: #e5e9f1;
        --qb-tag-bg: #f8fafc;
        --qb-tag-border: #e2e8f0;
        --qb-tag-text: #475569;
        background: linear-gradient(180deg, var(--qb-from) 0%, var(--qb-to) 100%);
        border: 1px solid var(--qb-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--qb-text);
        box-shadow: 0 20px 50px rgba(15,23,42,.08);
    }
    .dark .qb-wrap {
        --qb-from: #0f172a;
        --qb-to: #0b1322;
        --qb-border: rgba(148,163,184,.25);
        --qb-text: #e2e8f0;
        --qb-muted: #94a3b8;
        --qb-card-bg: #111827;
        --qb-card-border: rgba(148,163,184,.22);
        --qb-card-shadow: 0 18px 38px rgba(2,6,23,.55);
        --qb-row-border: rgba(148,163,184,.16);
        --qb-head-border: rgba(148,163,184,.25);
        --qb-tag-bg: rgba(15,23,42,.55);
        --qb-tag-border: rgba(148,163,184,.2);
        --qb-tag-text: #cbd5e1;
    }
    /* Topbar */
    .qb-topbar { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:20px; }
    .qb-brand { display:flex; align-items:center; gap:12px; flex:1 1 280px; }
    .qb-logo { width:36px; height:36px; border-radius:11px; background:linear-gradient(135deg,#6d28d9,#7c3aed); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .qb-title { font-size:18px; font-weight:700; letter-spacing:-0.015em; color:var(--qb-text); }
    .qb-subtitle { color:var(--qb-muted); font-size:12px; font-weight:600; }
    .qb-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    /* Buttons */
    .qb-btn { height:38px; border-radius:12px; padding:0 14px; display:inline-flex; align-items:center; gap:8px; font-size:13px; font-weight:700; text-decoration:none; transition:transform .18s ease, filter .2s ease; white-space:nowrap; border:none; cursor:pointer; }
    .qb-btn:hover { transform:translateY(-1px); filter:brightness(1.04); }
    .qb-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; filter:none; }
    .qb-btn-ghost { color:var(--qb-muted) !important; background:var(--qb-card-bg); border:1px solid var(--qb-card-border); }
    .qb-btn-ghost:hover { color:var(--qb-text) !important; }
    .qb-btn-primary { color:#fff !important; background:linear-gradient(135deg,#6d28d9,#7c3aed); box-shadow:0 8px 18px rgba(109,40,217,.22); }
    .qb-btn-success { color:#fff !important; background:linear-gradient(135deg,#059669,#10b981); box-shadow:0 8px 18px rgba(5,150,105,.22); }
    .qb-btn-sky { color:#fff !important; background:linear-gradient(135deg,#0284c7,#0ea5e9); box-shadow:0 8px 18px rgba(2,132,199,.22); }
    .qb-btn-whatsapp { color:#0f1a11 !important; background:#25D366; box-shadow:0 8px 18px rgba(37,211,102,.22); }
    .qb-btn-full { width:100%; justify-content:center; height:42px; font-size:14px; }
    /* Stats strip */
    .qb-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:20px; }
    .qb-stat { background:var(--qb-card-bg); border:1px solid var(--qb-card-border); border-radius:14px; padding:14px 16px; box-shadow:var(--qb-card-shadow); }
    .qb-stat-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--qb-muted); font-weight:700; margin-bottom:8px; }
    .qb-stat-value { font-size:24px; font-weight:800; color:var(--qb-text); line-height:1; }
    .qb-stat-total .qb-stat-label { color:#6d28d9; }
    .dark .qb-stat-total .qb-stat-label { color:#a78bfa; }
    .qb-stat-total .qb-stat-value { color:#6d28d9; }
    .dark .qb-stat-total .qb-stat-value { color:#a78bfa; }
    /* Cards */
    .qb-card { background:var(--qb-card-bg); border:1px solid var(--qb-card-border); border-radius:14px; padding:16px; box-shadow:var(--qb-card-shadow); }
    .qb-card-title { font-size:15px; font-weight:700; color:var(--qb-text); }
    .qb-card-subtitle { font-size:12px; color:var(--qb-muted); font-weight:600; margin-top:2px; }
    .qb-card-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--qb-muted); font-weight:700; margin-bottom:6px; }
    .qb-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px; }
    /* Layouts */
    .qb-grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .qb-grid-items { display:grid; grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr); gap:14px; }
    .qb-main-aside { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:14px; align-items:start; }
    .qb-main { display:flex; flex-direction:column; gap:14px; }
    /* Form */
    .qb-label { display:block; font-size:13px; font-weight:600; color:var(--qb-text); margin-bottom:5px; }
    .qb-input { width:100%; border-radius:10px; border:1px solid var(--qb-card-border); background:var(--qb-tag-bg); color:var(--qb-text); padding:8px 12px; font-size:13px; outline:none; transition:border-color .15s, box-shadow .15s; }
    .qb-input:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.12); background:var(--qb-card-bg); }
    .qb-textarea { width:100%; border-radius:10px; border:1px solid var(--qb-card-border); background:var(--qb-tag-bg); color:var(--qb-text); padding:8px 12px; font-size:13px; outline:none; resize:vertical; transition:border-color .15s, box-shadow .15s; }
    .qb-textarea:focus { border-color:#d97706; box-shadow:0 0 0 3px rgba(217,119,6,.1); background:var(--qb-card-bg); }
    /* Pills */
    .qb-pill { display:inline-flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid var(--qb-tag-border); background:var(--qb-card-bg); color:var(--qb-tag-text); padding:5px 12px; font-size:12px; font-weight:600; cursor:pointer; transition:border-color .15s, background .15s; }
    .qb-pill:hover { border-color:#7c3aed; color:#6d28d9; }
    .dark .qb-pill:hover { color:#a78bfa; border-color:#7c3aed; }
    .qb-pill-active { border-color:#7c3aed; background:rgba(109,40,217,.1); color:#6d28d9; box-shadow:0 0 0 2px rgba(124,58,237,.1); }
    .dark .qb-pill-active { color:#a78bfa; background:rgba(109,40,217,.22); }
    .qb-pill-size-active { border-color:#d97706; background:rgba(245,158,11,.1); color:#92400e; }
    .dark .qb-pill-size-active { color:#fcd34d; background:rgba(217,119,6,.22); }
    .qb-pill-obs-active { border-color:#d97706; background:rgba(245,158,11,.08); color:#92400e; }
    .dark .qb-pill-obs-active { color:#fcd34d; background:rgba(217,119,6,.18); }
    .qb-pill-deadline-active { border-color:#6d28d9; background:rgba(109,40,217,.1); color:#6d28d9; box-shadow:0 0 0 2px rgba(124,58,237,.1); }
    .dark .qb-pill-deadline-active { color:#a78bfa; background:rgba(109,40,217,.22); }
    /* Tag (small badge) */
    .qb-tag { display:inline-flex; align-items:center; padding:5px 10px; border-radius:999px; font-size:11px; font-weight:700; background:var(--qb-tag-bg); border:1px solid var(--qb-tag-border); color:var(--qb-tag-text); }
    /* Item rows */
    .qb-item-row { background:var(--qb-card-bg); border:1px solid var(--qb-row-border); border-radius:12px; padding:14px; transition:transform .15s, border-color .15s; }
    .qb-item-row:hover { transform:translateY(-1px); border-color:var(--qb-card-border); }
    .qb-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; margin-top:12px; }
    .qb-metric { background:var(--qb-tag-bg); border-radius:10px; padding:10px 12px; }
    .qb-metric-purple { background:rgba(109,40,217,.07); border:1px solid rgba(109,40,217,.16); }
    .dark .qb-metric-purple { background:rgba(109,40,217,.18); border-color:rgba(109,40,217,.28); }
    .qb-metric-label { font-size:10px; text-transform:uppercase; letter-spacing:.08em; color:var(--qb-muted); font-weight:700; }
    .qb-metric-value { font-size:15px; font-weight:800; color:var(--qb-text); margin-top:4px; }
    .qb-metric-purple .qb-metric-label { color:#6d28d9; }
    .dark .qb-metric-purple .qb-metric-label { color:#a78bfa; }
    .qb-metric-purple .qb-metric-value { color:#6d28d9; }
    .dark .qb-metric-purple .qb-metric-value { color:#a78bfa; }
    /* Item form box */
    .qb-form-box { background:var(--qb-tag-bg); border:1px dashed var(--qb-card-border); border-radius:14px; padding:16px; }
    /* Summary panel */
    .qb-summary-head { background:linear-gradient(135deg,#6d28d9,#7c3aed); border-radius:12px 12px 0 0; padding:18px; color:#fff; margin:-16px -16px 0; }
    /* Total box */
    .qb-total-box { background:rgba(109,40,217,.07); border:1px solid rgba(109,40,217,.16); border-radius:12px; padding:16px; }
    .dark .qb-total-box { background:rgba(109,40,217,.16); border-color:rgba(109,40,217,.28); }
    .qb-total-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#6d28d9; font-weight:700; }
    .dark .qb-total-label { color:#a78bfa; }
    .qb-total-value { font-size:30px; font-weight:800; color:var(--qb-text); line-height:1; margin-top:6px; }
    .qb-total-note { font-size:12px; color:var(--qb-muted); margin-top:6px; font-weight:600; }
    /* Alerts */
    .qb-alert-error { border-radius:10px; border:1px solid #fecaca; background:#fef2f2; color:#991b1b; padding:10px 14px; font-size:13px; }
    .dark .qb-alert-error { border-color:rgba(239,68,68,.3); background:rgba(220,38,38,.12); color:#fca5a5; }
    .qb-alert-info { border-radius:10px; border:1px solid #d1fae5; background:#f0fdf4; color:#065f46; padding:8px 12px; font-size:12px; font-weight:600; }
    .dark .qb-alert-info { border-color:rgba(16,185,129,.25); background:rgba(5,150,105,.12); color:#6ee7b7; }
    /* Checklist */
    .qb-check-item { font-size:12px; color:var(--qb-muted); font-weight:600; display:flex; align-items:center; gap:7px; }
    .qb-check-item.done { color:#059669; }
    .dark .qb-check-item.done { color:#6ee7b7; }
    .qb-check-dot { width:16px; height:16px; border-radius:50%; border:1.5px solid currentColor; display:inline-flex; align-items:center; justify-content:center; font-size:8px; flex-shrink:0; }
    /* Empty state */
    .qb-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:220px; border:1px dashed var(--qb-head-border); border-radius:12px; text-align:center; padding:24px; }
    .qb-empty-icon { width:48px; height:48px; border-radius:50%; background:rgba(109,40,217,.1); color:#6d28d9; display:flex; align-items:center; justify-content:center; font-size:18px; margin:0 auto 12px; }
    .dark .qb-empty-icon { background:rgba(109,40,217,.2); color:#a78bfa; }
    /* Divider */
    .qb-hr { border:none; border-top:1px solid var(--qb-head-border); margin:0; }
    /* Responsive */
    @media (max-width:1200px) { .qb-main-aside { grid-template-columns:1fr; } }
    @media (max-width:900px) { .qb-grid-items { grid-template-columns:1fr; } .qb-stats { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:640px) { .qb-wrap { padding:14px; border-radius:16px; } .qb-grid-2 { grid-template-columns:1fr; } .qb-metrics { grid-template-columns:repeat(2,1fr); } .qb-actions, .qb-btn { width:100%; justify-content:center; } }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6" x-data="quickBudgetBuilder()">
    <div class="qb-wrap">

        {{-- Topbar --}}
        <div class="qb-topbar">
            <div class="qb-brand">
                <div class="qb-logo"><i class="fa-solid fa-bolt"></i></div>
                <div>
                    <p class="qb-title">Orçamento Rápido</p>
                    <p class="qb-subtitle">Contato + itens + fechamento — sem sair da tela.</p>
                </div>
            </div>
            <div class="qb-actions">
                <a href="{{ route('budget.index') }}" class="qb-btn qb-btn-ghost">
                    <i class="fa-solid fa-arrow-left" style="font-size:11px"></i>
                    <span>Voltar</span>
                </a>
                <button type="button" @click="submitForm('save')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-success">
                    <i class="fa-solid fa-floppy-disk" style="font-size:12px"></i>
                    <span x-text="loadingAction === 'save' ? 'Salvando...' : 'Salvar'"></span>
                </button>
                <button type="button" @click="submitForm('whatsapp')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-whatsapp">
                    <i class="fa-brands fa-whatsapp" style="font-size:14px"></i>
                    <span x-text="loadingAction === 'whatsapp' ? 'Abrindo...' : 'WhatsApp'"></span>
                </button>
            </div>
        </div>

        {{-- Stats strip --}}
        <div class="qb-stats">
            <div class="qb-stat">
                <p class="qb-stat-label">Itens</p>
                <p class="qb-stat-value" x-text="items.length"></p>
            </div>
            <div class="qb-stat">
                <p class="qb-stat-label">Peças</p>
                <p class="qb-stat-value" x-text="totalQuantity"></p>
            </div>
            <div class="qb-stat">
                <p class="qb-stat-label">Prazo</p>
                <p class="qb-stat-value"><span x-text="form.deadline_days"></span>d</p>
            </div>
            <div class="qb-stat qb-stat-total">
                <p class="qb-stat-label">Total</p>
                <p class="qb-stat-value" x-text="formatCurrency(grandTotal)"></p>
            </div>
        </div>

        {{-- Server error --}}
        <template x-if="serverError">
            <div class="qb-alert-error" style="margin-bottom:14px" x-text="serverError"></div>
        </template>

        {{-- Main layout --}}
        <div class="qb-main-aside">
            <div class="qb-main">

                {{-- Row 1: Contact + Observations --}}
                <div class="qb-grid-2">

                    {{-- 1. Contact --}}
                    <div class="qb-card">
                        <div class="qb-card-head">
                            <div>
                                <p class="qb-card-label">1. Contato</p>
                                <p class="qb-card-title">Dados essenciais</p>
                                <p class="qb-card-subtitle">Só o que precisa para sair rápido.</p>
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:12px">
                            <label class="block">
                                <span class="qb-label">Nome do contato</span>
                                <input type="text" x-model="form.contact_name" class="qb-input" placeholder="Ex: Maria Oliveira">
                            </label>
                            <label class="block">
                                <span class="qb-label">WhatsApp</span>
                                <input type="text" x-model="form.contact_phone" x-mask="(99) 99999-9999" class="qb-input" placeholder="(00) 00000-0000">
                            </label>
                            <div>
                                <span class="qb-label">Prazo estimado</span>
                                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:6px">
                                    <template x-for="day in quickDeadlines" :key="day">
                                        <button type="button" @click="form.deadline_days = day"
                                            :class="form.deadline_days === day ? 'qb-pill-deadline-active' : ''"
                                            class="qb-pill" style="border-radius:10px;padding:7px 0;width:100%">
                                            <span x-text="day + 'd'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Observations --}}
                    <div class="qb-card">
                        <div class="qb-card-head">
                            <div>
                                <p class="qb-card-label">2. Observações</p>
                                <p class="qb-card-title">Recados rápidos</p>
                                <p class="qb-card-subtitle">Sugestões prontas ou texto livre.</p>
                            </div>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
                            <template x-for="opt in observationOptions" :key="opt">
                                <button type="button" @click="toggleObservation(opt)"
                                    :class="form.observations.includes(opt) ? 'qb-pill-obs-active' : ''"
                                    class="qb-pill">
                                    <span x-text="opt"></span>
                                </button>
                            </template>
                        </div>
                        <label class="block">
                            <span class="qb-label">Observações gerais</span>
                            <textarea x-model="form.observations" rows="7" class="qb-textarea" placeholder="Ex: valores válidos para tamanhos padrão, prazo sujeito à aprovação..."></textarea>
                        </label>
                    </div>

                </div>

                {{-- Row 2: Items --}}
                <div class="qb-card">
                    <div class="qb-card-head">
                        <div>
                            <p class="qb-card-label">3. Itens</p>
                            <p class="qb-card-title">Mais de um item no mesmo orçamento</p>
                            <p class="qb-card-subtitle">Adicione um item, confirme e siga para o próximo.</p>
                        </div>
                        <div class="qb-alert-info">Resumo em tempo real: qtd e total consolidados.</div>
                    </div>

                    <div class="qb-grid-items">
                        {{-- Draft form --}}
                        <div class="qb-form-box">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                                <p class="qb-card-title" x-text="editingIndex === null ? 'Novo item' : 'Editar item'"></p>
                                <template x-if="editingIndex !== null">
                                    <button type="button" @click="cancelEdit()" class="qb-tag" style="cursor:pointer">Cancelar</button>
                                </template>
                            </div>

                            <div style="display:flex;flex-direction:column;gap:12px">
                                <label class="block">
                                    <span class="qb-label">Produto interno</span>
                                    <input type="text" x-model="draft.product_internal" class="qb-input" placeholder="Ex: Camiseta básica">
                                </label>

                                <div>
                                    <span class="qb-label">Técnica</span>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:5px">
                                        <template x-for="tech in techniques" :key="tech">
                                            <button type="button" @click="setTechnique(tech)"
                                                :class="draft.technique_type === tech ? 'qb-pill-active' : ''"
                                                class="qb-pill">
                                                <span x-text="tech"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="qb-label">Tamanho da aplicação</span>
                                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:5px">
                                        <template x-for="size in applicationSizes" :key="size">
                                            <button type="button" @click="setSize(size)"
                                                :class="draft.application_size === size ? 'qb-pill-size-active' : ''"
                                                class="qb-pill">
                                                <span x-text="size"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <label class="block">
                                    <span class="qb-label">Descrição da personalização</span>
                                    <input type="text" x-model="draft.technique" class="qb-input" placeholder="Ex: Serigrafia - A4">
                                </label>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                    <label class="block">
                                        <span class="qb-label">Quantidade</span>
                                        <input type="number" min="1" x-model.number="draft.quantity" class="qb-input">
                                    </label>
                                    <label class="block">
                                        <span class="qb-label">Valor unitário</span>
                                        <input type="number" min="0.01" step="0.01" x-model.number="draft.unit_price" class="qb-input">
                                    </label>
                                </div>

                                <label class="block">
                                    <span class="qb-label">Notas do item</span>
                                    <textarea x-model="draft.notes" rows="3" class="qb-textarea" placeholder="Ex: frente e costas, ajuste de arte..."></textarea>
                                </label>
                            </div>

                            <template x-if="draftError">
                                <div class="qb-alert-error" style="margin-top:12px" x-text="draftError"></div>
                            </template>

                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:14px">
                                <div>
                                    <p class="qb-metric-label">Total do item</p>
                                    <p style="font-size:20px;font-weight:800;color:var(--qb-text);line-height:1.2;margin-top:3px" x-text="formatCurrency(draftTotal)"></p>
                                </div>
                                <button type="button" @click="saveDraft()" class="qb-btn qb-btn-primary">
                                    <i class="fa-solid fa-plus" style="font-size:11px"></i>
                                    <span x-text="editingIndex === null ? 'Adicionar item' : 'Atualizar item'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Items list --}}
                        <div style="display:flex;flex-direction:column;gap:10px">
                            <template x-if="items.length === 0">
                                <div class="qb-empty">
                                    <div class="qb-empty-icon"><i class="fa-solid fa-cubes-stacked"></i></div>
                                    <p class="qb-card-title" style="font-size:14px">Nenhum item adicionado</p>
                                    <p style="font-size:12px;color:var(--qb-muted);margin-top:4px;max-width:240px">Comece por uma peça. Depois empilhe os demais itens aqui.</p>
                                </div>
                            </template>

                            <template x-for="(item, index) in items" :key="item.uid">
                                <article class="qb-item-row">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
                                        <div style="min-width:0">
                                            <div style="display:flex;align-items:center;gap:8px">
                                                <span style="width:22px;height:22px;border-radius:50%;background:rgba(109,40,217,.12);color:#6d28d9;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0" x-text="index+1"></span>
                                                <p class="qb-card-title" style="font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" x-text="item.product_internal || 'Item rápido'"></p>
                                            </div>
                                            <p style="font-size:12px;font-weight:600;color:#6d28d9;margin-top:3px" x-text="item.technique"></p>
                                            <template x-if="item.notes">
                                                <p style="font-size:12px;color:var(--qb-muted);margin-top:2px" x-text="item.notes"></p>
                                            </template>
                                        </div>
                                        <div style="display:flex;gap:6px;flex-shrink:0">
                                            <button type="button" @click="editItem(index)" class="qb-tag" style="cursor:pointer">Editar</button>
                                            <button type="button" @click="removeItem(index)" class="qb-tag" style="cursor:pointer;color:#991b1b;border-color:#fecaca">Remover</button>
                                        </div>
                                    </div>

                                    <div class="qb-metrics">
                                        <div class="qb-metric">
                                            <p class="qb-metric-label">Qtd</p>
                                            <p class="qb-metric-value" x-text="item.quantity"></p>
                                        </div>
                                        <div class="qb-metric">
                                            <p class="qb-metric-label">Unitário</p>
                                            <p class="qb-metric-value" x-text="formatCurrency(item.unit_price)"></p>
                                        </div>
                                        <div class="qb-metric">
                                            <p class="qb-metric-label">Aplicação</p>
                                            <p class="qb-metric-value" x-text="item.application_size || '—'"></p>
                                        </div>
                                        <div class="qb-metric qb-metric-purple">
                                            <p class="qb-metric-label">Total</p>
                                            <p class="qb-metric-value" x-text="formatCurrency(itemTotal(item))"></p>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>
                </div>

            </div>{{-- end .qb-main --}}

            {{-- Aside summary --}}
            <aside>
                <div class="qb-card" style="overflow:hidden">
                    <div class="qb-summary-head">
                        <p style="font-size:10px;text-transform:uppercase;letter-spacing:.1em;opacity:.75;font-weight:700">Resumo</p>
                        <p style="font-size:20px;font-weight:800;margin-top:4px">Fechamento rápido</p>
                        <p style="font-size:12px;opacity:.7;margin-top:2px">Pronto para salvar, enviar ou gerar PDF.</p>
                    </div>

                    <div style="padding-top:16px;display:flex;flex-direction:column;gap:14px">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                            <div class="qb-metric"><p class="qb-metric-label">Itens</p><p class="qb-metric-value" x-text="items.length"></p></div>
                            <div class="qb-metric"><p class="qb-metric-label">Peças</p><p class="qb-metric-value" x-text="totalQuantity"></p></div>
                        </div>

                        <div class="qb-total-box">
                            <p class="qb-total-label">Total do orçamento</p>
                            <p class="qb-total-value" x-text="formatCurrency(grandTotal)"></p>
                            <p class="qb-total-note">Prazo: <strong x-text="form.deadline_days + ' dias'"></strong></p>
                        </div>

                        <hr class="qb-hr">

                        <div style="display:flex;flex-direction:column;gap:8px">
                            <button type="button" @click="submitForm('save')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-success qb-btn-full">
                                <i class="fa-solid fa-floppy-disk" style="font-size:12px"></i>
                                <span x-text="loadingAction === 'save' ? 'Salvando...' : 'Salvar orçamento'"></span>
                            </button>
                            <button type="button" @click="submitForm('pdf')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-sky qb-btn-full">
                                <i class="fa-solid fa-file-pdf" style="font-size:12px"></i>
                                <span x-text="loadingAction === 'pdf' ? 'Gerando...' : 'Salvar e gerar PDF'"></span>
                            </button>
                            <button type="button" @click="submitForm('copy')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-ghost qb-btn-full">
                                <i class="fa-solid fa-copy" style="font-size:12px"></i>
                                <span x-text="loadingAction === 'copy' ? 'Copiando...' : 'Salvar e copiar texto'"></span>
                            </button>
                            <button type="button" @click="submitForm('whatsapp')" :disabled="!canSubmit || loading" class="qb-btn qb-btn-whatsapp qb-btn-full">
                                <i class="fa-brands fa-whatsapp" style="font-size:14px"></i>
                                <span x-text="loadingAction === 'whatsapp' ? 'Abrindo...' : 'Salvar e abrir WhatsApp'"></span>
                            </button>
                        </div>

                        <hr class="qb-hr">

                        <div class="qb-card" style="padding:12px;box-shadow:none">
                            <p style="font-size:12px;font-weight:700;color:var(--qb-text);margin-bottom:8px">Checklist</p>
                            <div style="display:flex;flex-direction:column;gap:7px">
                                <div class="qb-check-item" :class="form.contact_name.trim() ? 'done' : ''">
                                    <span class="qb-check-dot"><i class="fa-solid fa-check" x-show="form.contact_name.trim()" style="font-size:7px"></i></span>
                                    <span>Contato preenchido</span>
                                </div>
                                <div class="qb-check-item" :class="form.contact_phone.trim() ? 'done' : ''">
                                    <span class="qb-check-dot"><i class="fa-solid fa-check" x-show="form.contact_phone.trim()" style="font-size:7px"></i></span>
                                    <span>WhatsApp informado</span>
                                </div>
                                <div class="qb-check-item" :class="items.length ? 'done' : ''">
                                    <span class="qb-check-dot"><i class="fa-solid fa-check" x-show="items.length" style="font-size:7px"></i></span>
                                    <span>Ao menos um item adicionado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

        </div>{{-- end .qb-main-aside --}}

    </div>{{-- end .qb-wrap --}}
</div>

@push('scripts')
<script>
function quickBudgetBuilder() {
    return {
        form: { contact_name: '', contact_phone: '', deadline_days: 15, observations: '' },
        draft: { product_internal: '', technique: '', technique_type: '', application_size: '', quantity: 1, unit_price: null, notes: '' },
        items: [],
        editingIndex: null,
        loading: false,
        loadingAction: '',
        draftError: '',
        serverError: '',
        techniques: ['Serigrafia', 'Bordado', 'Sublimação', 'Sublimação Local', 'Sublimação Total', 'DTF'],
        applicationSizes: ['ESCUDO', 'A5', 'A4', 'A3', 'A2'],
        quickDeadlines: [7, 10, 15, 20],
        observationOptions: @json($observationOptions),

        get draftTotal() { return (this.draft.quantity || 0) * (this.draft.unit_price || 0); },
        get totalQuantity() { return this.items.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0); },
        get grandTotal() { return this.items.reduce((sum, item) => sum + this.itemTotal(item), 0); },
        get canSubmit() { return this.form.contact_name.trim() !== '' && this.form.contact_phone.trim() !== '' && this.items.length > 0; },

        formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value || 0));
        },

        itemTotal(item) {
            return (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
        },

        toggleObservation(opt) {
            const lines = this.form.observations ? this.form.observations.split('\n') : [];
            if (lines.includes(opt)) this.form.observations = lines.filter(line => line !== opt).join('\n');
            else this.form.observations = [...lines.filter(Boolean), opt].join('\n');
        },

        setTechnique(tech) {
            this.draft.technique_type = tech;
            this.syncDraftTechnique();
        },

        setSize(size) {
            this.draft.application_size = size;
            this.syncDraftTechnique();
        },

        syncDraftTechnique() {
            const parts = [];
            if (this.draft.technique_type) parts.push(this.draft.technique_type);
            if (this.draft.application_size) parts.push(this.draft.application_size);
            if (parts.length) this.draft.technique = parts.join(' - ');
        },

        validateDraft() {
            if (!this.draft.technique.trim()) return 'Informe a personalização do item.';
            if (!this.draft.quantity || Number(this.draft.quantity) < 1) return 'Defina uma quantidade válida.';
            if (!this.draft.unit_price || Number(this.draft.unit_price) <= 0) return 'Defina um valor unitário válido.';
            return '';
        },

        saveDraft() {
            this.draftError = this.validateDraft();
            if (this.draftError) return;

            const item = {
                uid: this.editingIndex === null ? `${Date.now()}-${Math.random()}` : this.items[this.editingIndex].uid,
                product_internal: this.draft.product_internal.trim(),
                technique: this.draft.technique.trim(),
                technique_type: this.draft.technique_type.trim(),
                application_size: this.draft.application_size.trim(),
                quantity: Number(this.draft.quantity),
                unit_price: Number(this.draft.unit_price),
                notes: this.draft.notes.trim()
            };

            if (this.editingIndex === null) this.items.unshift(item);
            else this.items.splice(this.editingIndex, 1, item);

            this.cancelEdit();
        },

        editItem(index) {
            const item = this.items[index];
            this.editingIndex = index;
            this.draft = {
                product_internal: item.product_internal || '',
                technique: item.technique || '',
                technique_type: item.technique_type || '',
                application_size: item.application_size || '',
                quantity: item.quantity || 1,
                unit_price: item.unit_price || null,
                notes: item.notes || ''
            };
            this.draftError = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        cancelEdit() {
            this.editingIndex = null;
            this.draftError = '';
            this.draft = { product_internal: '', technique: '', technique_type: '', application_size: '', quantity: 1, unit_price: null, notes: '' };
        },

        removeItem(index) {
            this.items.splice(index, 1);
            if (this.editingIndex === index) this.cancelEdit();
            else if (this.editingIndex !== null && this.editingIndex > index) this.editingIndex -= 1;
        },

        buildPayload() {
            return {
                contact_name: this.form.contact_name.trim(),
                contact_phone: this.form.contact_phone.trim(),
                deadline_days: Number(this.form.deadline_days) || 15,
                observations: this.form.observations.trim(),
                items: this.items.map(item => ({
                    product_internal: item.product_internal,
                    technique: item.technique,
                    technique_type: item.technique_type,
                    application_size: item.application_size,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    notes: item.notes
                }))
            };
        },

        async submitForm(action) {
            this.serverError = '';
            if (!this.canSubmit) {
                this.serverError = 'Preencha contato, WhatsApp e adicione pelo menos um item.';
                return;
            }

            this.loading = true;
            this.loadingAction = action;

            try {
                const response = await fetch('{{ route("budget.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.buildPayload())
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    if (data.errors) {
                        const firstField = Object.keys(data.errors)[0];
                        this.serverError = data.errors[firstField][0];
                    } else {
                        this.serverError = data.message || 'Erro ao salvar orçamento rápido.';
                    }
                    return;
                }

                if (action === 'pdf') {
                    window.open(data.pdf_url, '_blank');
                    window.location.href = data.redirect_url;
                    return;
                }

                if (action === 'copy') {
                    const msgResponse = await fetch(data.whatsapp_url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const msgData = await msgResponse.json();
                    if (msgData.message) await navigator.clipboard.writeText(msgData.message);
                    window.location.href = data.redirect_url;
                    return;
                }

                if (action === 'whatsapp') {
                    window.open(data.whatsapp_url, '_blank');
                    window.location.href = data.redirect_url;
                    return;
                }

                window.location.href = data.redirect_url;
            } catch (error) {
                console.error(error);
                this.serverError = 'Erro ao salvar orçamento rápido. Tente novamente.';
            } finally {
                this.loading = false;
                this.loadingAction = '';
            }
        }
    };
}
</script>
@endpush
@endsection
