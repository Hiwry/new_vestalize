@extends('layouts.admin')

@section('content')
<style>
    .vq-ft {
        --of-surface-from: #f3f4f8;
        --of-surface-to: #eceff4;
        --of-surface-border: #d8dce6;
        --of-text-primary: #0f172a;
        --of-text-secondary: #64748b;
        --of-card-bg: #ffffff;
        --of-card-border: #dde2ea;
        --of-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --of-input-bg: #ffffff;
        --of-input-border: #d6d9e2;
        --of-input-text: #334155;
        background: linear-gradient(180deg, var(--of-surface-from) 0%, var(--of-surface-to) 100%);
        border: 1px solid var(--of-surface-border);
        border-radius: 20px;
        padding: 20px;
        color: var(--of-text-primary);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .dark .vq-ft {
        --of-surface-from: #0f172a;
        --of-surface-to: #0b1322;
        --of-surface-border: rgba(148, 163, 184, 0.25);
        --of-text-primary: #e2e8f0;
        --of-text-secondary: #94a3b8;
        --of-card-bg: #111827;
        --of-card-border: rgba(148, 163, 184, 0.22);
        --of-card-shadow: 0 18px 38px rgba(2, 6, 23, 0.55);
        --of-input-bg: #0f172a;
        --of-input-border: rgba(148, 163, 184, 0.25);
        --of-input-text: #e2e8f0;
    }

    .vq-ft-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .vq-ft-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1 1 320px;
    }

    .vq-ft-logo {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .vq-ft-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--of-text-primary) !important;
    }

    .vq-ft-subtitle {
        margin-top: 3px;
        font-size: 13px;
        font-weight: 600;
        color: var(--of-text-secondary) !important;
    }

    .vq-ft-btn {
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

    .vq-ft-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .vq-ft-btn,
    .vq-ft-btn span,
    .vq-ft-btn svg,
    .vq-ft-btn svg * {
        color: #fff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    .vq-card {
        background: var(--of-card-bg) !important;
        border: 1px solid var(--of-card-border) !important;
        border-radius: 14px !important;
        box-shadow: var(--of-card-shadow) !important;
        padding: 20px;
    }

    .vq-input,
    input.vq-input,
    select.vq-input {
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
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .vq-input:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    select.vq-input {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 34px !important;
    }

    .vq-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--of-text-secondary);
        margin-bottom: 6px;
        display: block;
    }

    .vq-ft .text-gray-900,
    .vq-ft .text-gray-800,
    .vq-ft .text-gray-700 {
        color: var(--of-text-primary) !important;
    }

    .vq-ft .text-gray-600,
    .vq-ft .text-gray-500,
    .vq-ft .text-gray-400 {
        color: var(--of-text-secondary) !important;
    }

    /* Mic card */
    .vq-mic-card {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border-radius: 16px;
        padding: 40px 24px;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .vq-mic-card.is-listening {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .vq-mic-btn {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .3s ease;
        margin-bottom: 14px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .vq-mic-btn:hover { transform: scale(1.05); }
    .vq-mic-btn.listening {
        background: rgba(255,255,255,0.2);
        animation: pulse-mic 1.5s infinite;
        box-shadow: 0 0 0 0 rgba(255,255,255,0.3);
    }

    @keyframes pulse-mic {
        0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
        70% { box-shadow: 0 0 0 20px rgba(255,255,255,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
    }

    .vq-transcript-box {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 12px 16px;
        margin-top: 12px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Sound waves */
    .vq-waves { display: flex; align-items: center; justify-content: center; gap: 3px; height: 28px; margin-top: 8px; }
    .vq-waves span { width: 3px; background: #fff; border-radius: 99px; animation: wave-bounce 0.6s ease-in-out infinite; }
    .vq-waves span:nth-child(1) { animation-delay: 0s; }
    .vq-waves span:nth-child(2) { animation-delay: 0.1s; }
    .vq-waves span:nth-child(3) { animation-delay: 0.2s; }
    .vq-waves span:nth-child(4) { animation-delay: 0.3s; }
    .vq-waves span:nth-child(5) { animation-delay: 0.4s; }
    @keyframes wave-bounce {
        0%, 100% { height: 8px; }
        50% { height: 24px; }
    }

    /* Tips */
    .vq-tip {
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid var(--of-card-border);
    }

    .vq-tip-blue { background: rgba(59, 130, 246, 0.06); border-color: rgba(59, 130, 246, 0.15); }
    .vq-tip-green { background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.15); }
    .vq-tip-amber { background: rgba(245, 158, 11, 0.06); border-color: rgba(245, 158, 11, 0.15); }
    .vq-tip-rose { background: rgba(239, 68, 68, 0.06); border-color: rgba(239, 68, 68, 0.15); }

    .dark .vq-tip-blue { background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.2); }
    .dark .vq-tip-green { background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.2); }
    .dark .vq-tip-amber { background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.2); }
    .dark .vq-tip-rose { background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.2); }

    .vq-form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    /* Size inputs */
    .vq-size-groups {
        display: grid;
        gap: 12px;
    }
    .vq-size-group {
        border: 1px solid var(--of-card-border);
        border-radius: 14px;
        background: rgba(148, 163, 184, 0.05);
        padding: 14px;
    }
    .vq-size-group-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .vq-size-group-title {
        font-size: 12px;
        font-weight: 800;
        color: var(--of-text-primary);
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .vq-size-group-count {
        font-size: 11px;
        font-weight: 700;
        color: var(--of-text-secondary);
    }
    .vq-size-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(84px, 1fr));
        gap: 10px;
    }
    .vq-size-item {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        background: var(--of-input-bg);
        border: 1px solid var(--of-input-border);
        border-radius: 12px;
        padding: 10px;
    }
    .vq-size-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--of-text-secondary);
        text-align: center;
        min-height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1.1;
        letter-spacing: .04em;
    }
    .vq-size-input {
        width: 100%;
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid var(--of-input-border) !important;
        background: var(--of-card-bg) !important;
        color: var(--of-input-text) !important;
        text-align: center;
        font-size: 13px !important;
        font-weight: 700 !important;
    }
    .vq-size-note {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        color: var(--of-text-secondary);
    }

    /* Personalization row */
    .vq-pers-row {
        background: var(--of-input-bg);
        border: 1px solid var(--of-input-border);
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
    }
    .vq-pers-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }
    .vq-pers-note {
        margin-top: 10px;
        font-size: 12px;
        color: var(--of-text-secondary);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Actions */
    .vq-action-btn {
        height: 40px;
        border-radius: 10px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: transform .18s ease, filter .18s ease;
    }
    .vq-action-btn:hover { transform: translateY(-1px); filter: brightness(1.03); }

    @media (max-width: 768px) {
        .vq-ft { padding: 14px; border-radius: 16px; }
        .vq-ft-title { font-size: 20px; }
        .vq-mic-card { padding: 28px 16px; }
        .vq-mic-btn { width: 64px; height: 64px; }
        .vq-form-grid-3,
        .vq-pers-grid {
            grid-template-columns: 1fr;
        }
        .vq-size-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6" x-data="voiceQuote()" x-cloak>
<section class="vq-ft">

{{-- Header --}}
<div class="vq-ft-topbar">
    <div class="vq-ft-brand">
        <span class="vq-ft-logo">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
        </span>
        <div>
            <h1 class="vq-ft-title">Orçamento por Voz</h1>
            <p class="vq-ft-subtitle">Fale os detalhes ou envie um áudio e o Gemini sugere o preenchimento</p>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('budget.index') }}" class="vq-ft-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Orçamentos
        </a>
    </div>
</div>

{{-- Mic Card --}}
<div class="vq-mic-card" :class="{ 'is-listening': isListening }">
    <button @click="toggleListening()" :disabled="isProcessing" class="vq-mic-btn" :class="{ 'listening': isListening }">
        <template x-if="isProcessing">
            <svg class="w-8 h-8 animate-spin" style="color: #2563eb" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </template>
        <template x-if="!isProcessing">
            <svg class="w-8 h-8" :style="isListening ? 'color: #fff' : 'color: #2563eb'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
            </svg>
        </template>
    </button>
    <div style="color: #fff;">
        <p style="font-size: 16px; font-weight: 700;" x-text="isListening ? 'Ouvindo...' : (isProcessing ? 'Processando...' : 'Toque para falar')"></p>
        <p style="font-size: 13px; opacity: .7; margin-top: 4px;" x-show="!transcript && !isListening">
            Ex: "camisa básica 20 P 30 M serigrafia A4 peito e costas"
        </p>
    </div>

    <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-top: 16px;">
        <button
            type="button"
            @click="$refs.audioFile.click()"
            :disabled="isProcessing"
            class="vq-action-btn"
            style="background: rgba(255,255,255,0.16); color: #fff; border: 1px solid rgba(255,255,255,0.25);"
        >
            Enviar áudio
        </button>
        <input
            type="file"
            x-ref="audioFile"
            class="hidden"
            accept=".mp3,.wav,.aac,.ogg,.flac,audio/*"
            @change="handleAudioFileChange($event)"
        >
    </div>
    <p style="font-size: 12px; opacity: .75; color: #fff; margin-top: 8px;">
        MP3, WAV, AAC, OGG ou FLAC, até 10MB.
    </p>

    {{-- Transcript --}}
    <div x-show="transcript" x-transition class="vq-transcript-box">
        <p style="font-size: 11px; opacity: .6; color: #fff; margin-bottom: 4px; font-weight: 700;">Transcrição:</p>
        <p style="font-size: 15px; font-weight: 600; color: #fff;" x-text='"\"" + transcript + "\""'></p>
    </div>

    {{-- Sound waves --}}
    <div x-show="isListening" class="vq-waves">
        <span></span><span></span><span></span><span></span><span></span>
    </div>
</div>

{{-- Error --}}
<div x-show="error" x-transition class="vq-card" style="margin-bottom: 14px; border-left: 4px solid #ef4444 !important; display: flex; align-items: center; gap: 10px;">
    <svg class="w-5 h-5" style="color: #ef4444; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="font-size: 13px; font-weight: 600; color: var(--of-text-primary);" x-text="error"></span>
</div>

{{-- Success --}}
<div x-show="matchedData && !error" x-transition class="vq-card" style="margin-bottom: 14px; border-left: 4px solid #10b981 !important; display: flex; align-items: center; gap: 10px;">
    <svg class="w-5 h-5" style="color: #10b981; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <span style="font-size: 13px; font-weight: 600; color: var(--of-text-primary);" x-text="aiProvider === 'gemini' ? 'Dados encontrados com Gemini. Revise e ajuste se necessário.' : 'Dados encontrados! Revise e ajuste se necessário.'"></span>
        <p x-show="aiSummary" style="font-size: 12px; color: var(--of-text-secondary); margin-top: 3px;" x-text="aiSummary"></p>
    </div>
</div>

{{-- Quote Form --}}
<div x-show="matchedData" x-transition>

    {{-- Product --}}
    <div class="vq-card" style="margin-bottom: 14px;">
        <h3 style="font-size: 14px; font-weight: 800; color: var(--of-text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <svg class="w-5 h-5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Produto
        </h3>
        <div class="vq-form-grid-3">
            <div>
                <label class="vq-label">Tipo de Corte</label>
                <select x-model="form.cut_type_id" @change="onCutTypeChange()" class="vq-input" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <template x-for="ct in cutTypes" :key="ct.id">
                        <option :value="ct.id" x-text="ct.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="vq-label">Produto</label>
                <select x-model="form.product_id" @change="onProductChange()" class="vq-input" style="width: 100%;">
                    <option value="">-- Selecione --</option>
                    <template x-for="p in filteredProducts" :key="p.id">
                        <option :value="p.id" x-text="p.title + ' — R$ ' + parseFloat(p.price).toFixed(2).replace('.', ',')"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="vq-label">Preço Unitário</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--of-text-secondary); font-weight: 700;">R$</span>
                    <input type="number" step="0.01" x-model="form.unit_price" class="vq-input" style="width: 100%; padding-left: 36px !important; font-weight: 800 !important;">
                </div>
            </div>
        </div>
    </div>

    {{-- Sizes --}}
    <div class="vq-card" style="margin-bottom: 14px;">
        <h3 style="font-size: 14px; font-weight: 800; color: var(--of-text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <svg class="w-5 h-5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            Tamanhos
        </h3>
        <div class="vq-size-groups">
            <template x-for="group in visibleSizeGroups" :key="group.key">
                <div class="vq-size-group">
                    <div class="vq-size-group-head">
                        <span class="vq-size-group-title" x-text="group.label"></span>
                        <span class="vq-size-group-count" x-text="`${group.sizes.length} tamanhos`"></span>
                    </div>
                    <div class="vq-size-grid">
                        <template x-for="size in group.sizes" :key="`${group.key}-${size}`">
                            <label class="vq-size-item">
                                <span class="vq-size-label" x-text="size"></span>
                                <input type="number" min="0" x-model.number="form.sizes[size]" @change="handleSizeChange()" class="vq-size-input">
                            </label>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <p x-show="usesProductSizeTable" class="vq-size-note">Grade filtrada pela tabela padrão do produto selecionado.</p>
        <div style="margin-top: 12px; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            <span style="color: var(--of-text-secondary); font-weight: 600;">Total peças:</span>
            <span style="color: var(--of-text-primary); font-weight: 800;" x-text="totalPieces"></span>
        </div>
        <div x-show="detectedQuantity > 0 && !hasAnySizes" class="vq-tip vq-tip-amber" style="margin-top: 12px;">
            <p style="font-size: 12px; font-weight: 700; color: #b45309;">
                A IA identificou <span x-text="detectedQuantity"></span> peças, mas não distribuiu os tamanhos.
            </p>
            <p style="font-size: 12px; color: var(--of-text-secondary); margin-top: 4px;">
                Ajuste os tamanhos abaixo para fechar o orçamento.
            </p>
        </div>
        <div x-show="hasSizeMismatch" class="vq-tip vq-tip-blue" style="margin-top: 12px;">
            <p style="font-size: 12px; font-weight: 700; color: #2563eb;">
                A IA indicou <span x-text="detectedQuantity"></span> peças, mas a grade está somando <span x-text="totalPieces"></span>.
            </p>
            <p style="font-size: 12px; color: var(--of-text-secondary); margin-top: 4px;">
                Revise a distribuição antes de copiar o orçamento.
            </p>
        </div>
    </div>

    {{-- Personalizations --}}
    <div class="vq-card" style="margin-bottom: 14px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <h3 style="font-size: 14px; font-weight: 800; color: var(--of-text-primary); display: flex; align-items: center; gap: 8px;">
                <svg class="w-5 h-5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                Personalizações
            </h3>
            <button @click="addPersonalization()" class="vq-action-btn" style="height: 32px; background: rgba(59,130,246,0.1); color: #3b82f6; font-size: 12px;">
                + Adicionar
            </button>
        </div>

        <template x-for="(pers, idx) in form.personalizations" :key="idx">
            <div class="vq-pers-row">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 11px; font-weight: 800; color: #3b82f6;" x-text="'Personalização #' + (idx + 1)"></span>
                    <button @click="form.personalizations.splice(idx, 1)" style="background: none; border: none; cursor: pointer; color: var(--of-text-secondary); padding: 4px;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="vq-pers-grid">
                    <div>
                        <label class="vq-label">Tipo</label>
                        <select x-model="pers.type_id" @change="handlePersonalizationTypeChange(idx)" class="vq-input" style="width: 100%;">
                            <option value="">-- Tipo --</option>
                            @foreach($personalizationTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="vq-label">Tamanho</label>
                        <select x-model="pers.size_name" @change="syncPersonalizationPrice(idx)" class="vq-input" style="width: 100%;">
                            <option value="">-- Tam --</option>
                            @foreach($personalizationSizes as $ps)
                            <option value="{{ $ps->size_name }}">{{ $ps->size_name }} {{ $ps->size_dimensions ? '('.$ps->size_dimensions.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="vq-label">Local</label>
                        <select x-model="pers.location_id" class="vq-input" style="width: 100%;">
                            <option value="">-- Local --</option>
                            @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="personalizationUsesColorCount(pers)">
                        <label class="vq-label">Qtd. Cores</label>
                        <input type="number" min="1" x-model.number="pers.color_count" @change="syncPersonalizationPrice(idx)" class="vq-input" style="width: 100%;">
                    </div>
                    <div>
                        <label class="vq-label">Preço Unit.</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); font-size: 11px; color: var(--of-text-secondary); font-weight: 700;">R$</span>
                            <input type="number" step="0.01" x-model.number="pers.unit_price" class="vq-input" style="width: 100%; padding-left: 28px !important; font-weight: 800 !important;">
                        </div>
                    </div>
                </div>
                <div x-show="personalizationUsesColorCount(pers) && (getColorCountLabel(pers) || pers.color_details)" class="vq-pers-note">
                    <span x-show="getColorCountLabel(pers)" x-text="`Cores: ${getColorCountLabel(pers)}`"></span>
                    <span x-show="pers.color_details" x-text="`Detalhes: ${pers.color_details}`"></span>
                </div>
            </div>
        </template>

        <div x-show="form.personalizations.length === 0" style="text-align: center; padding: 20px 0; color: var(--of-text-secondary); font-size: 13px; font-weight: 600;">
            Nenhuma personalização adicionada
        </div>
    </div>

    {{-- Totals --}}
    <div class="vq-card" style="margin-bottom: 14px;">
        <h3 style="font-size: 14px; font-weight: 800; color: var(--of-text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Resumo
        </h3>
        <div style="font-size: 13px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: var(--of-text-secondary); font-weight: 600;">Produto (<span x-text="totalPieces"></span> pçs × R$ <span x-text="parseFloat(form.unit_price || 0).toFixed(2)"></span>)</span>
                <span style="color: var(--of-text-primary); font-weight: 800;" x-text="'R$ ' + subtotalProduct.toFixed(2)"></span>
            </div>
            <template x-for="(pers, idx) in form.personalizations" :key="'total-' + idx">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: var(--of-text-secondary); font-weight: 600;" x-text="buildPersonalizationLabel(pers)"></span>
                    <span style="color: var(--of-text-primary); font-weight: 800;" x-text="'R$ ' + (parseFloat(pers.unit_price || 0) * totalPieces).toFixed(2)"></span>
                </div>
            </template>
            <div style="border-top: 1px solid var(--of-card-border); padding-top: 12px; margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 16px; font-weight: 800; color: var(--of-text-primary);">Total</span>
                <span style="font-size: 22px; font-weight: 800; color: #10b981;" x-text="'R$ ' + grandTotal.toFixed(2)"></span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
        <button @click="resetForm()" class="vq-action-btn" style="background: var(--of-input-bg); border: 1px solid var(--of-input-border); color: var(--of-text-secondary);">
            Limpar
        </button>
        <button @click="copyToClipboard()" class="vq-action-btn" style="background: linear-gradient(135deg, #6d28d9, #7c3aed); color: #fff; box-shadow: 0 10px 20px rgba(109,40,217,0.25);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
            Copiar
        </button>
        <button @click="shareWhatsApp()" class="vq-action-btn" style="background: linear-gradient(135deg, #059669, #10b981); color: #fff; box-shadow: 0 10px 20px rgba(16,185,129,0.25);">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.616l4.528-1.463A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.234 0-4.308-.724-5.994-1.953l-.42-.312-2.686.868.896-2.632-.343-.452A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
            WhatsApp
        </button>
    </div>
</div>

{{-- Tips (when no data) --}}
<div x-show="!matchedData" class="vq-card">
    <h3 style="font-size: 14px; font-weight: 800; color: var(--of-text-primary); margin-bottom: 14px;">Dicas de Uso</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <div class="vq-tip vq-tip-blue">
            <p style="font-size: 12px; font-weight: 800; color: #3b82f6; margin-bottom: 4px;">Produto + Tamanho</p>
            <p style="font-size: 12px; font-weight: 600; color: var(--of-text-secondary); font-style: italic;">"camisa básica PP"</p>
        </div>
        <div class="vq-tip vq-tip-green">
            <p style="font-size: 12px; font-weight: 800; color: #10b981; margin-bottom: 4px;">Personalização + Local</p>
            <p style="font-size: 12px; font-weight: 600; color: var(--of-text-secondary); font-style: italic;">"serigrafia A4 peito e costas"</p>
        </div>
        <div class="vq-tip vq-tip-amber">
            <p style="font-size: 12px; font-weight: 800; color: #f59e0b; margin-bottom: 4px;">Quantidade</p>
            <p style="font-size: 12px; font-weight: 600; color: var(--of-text-secondary); font-style: italic;">"50 unidades"</p>
        </div>
        <div class="vq-tip vq-tip-rose">
            <p style="font-size: 12px; font-weight: 800; color: #ef4444; margin-bottom: 4px;">Tudo junto</p>
            <p style="font-size: 12px; font-weight: 600; color: var(--of-text-secondary); font-style: italic;">"camisa básica PP serigrafia A4 peito escudo costas"</p>
        </div>
    </div>
</div>

</section>
</div>

<script>
function voiceQuote() {
    return {
        isListening: false,
        isProcessing: false,
        transcript: '',
        error: '',
        matchedData: false,
        recognition: null,
        detectedQuantity: 0,
        aiSummary: '',
        aiProvider: '',
        defaultSizes: @json(array_fill_keys($knownSizes, 0)),
        sizeTable: @json($sizeTable),

        products: @json($products),
        cutTypes: @json($cutTypes),
        personalizationTypes: @json($personalizationTypes),
        personalizationSettings: @json($personalizationSettings),
        locations: @json($locations),
        personalizationSizes: @json($personalizationSizes),
        personalizationPriceUrl: @json(url('/api/personalization-prices/price')),

        form: {
            cut_type_id: '',
            product_id: '',
            unit_price: 0,
            sizes: @json(array_fill_keys($knownSizes, 0)),
            personalizations: [],
        },

        get hasAnySizes() {
            return Object.values(this.form.sizes).some(qty => (parseInt(qty) || 0) > 0);
        },

        get selectedProduct() {
            return this.products.find(product => String(product.id) === String(this.form.product_id)) || null;
        },

        get usesProductSizeTable() {
            return Array.isArray(this.selectedProduct?.available_sizes) && this.selectedProduct.available_sizes.length > 0;
        },

        get visibleSizeGroups() {
            const filledSizes = Object.entries(this.form.sizes)
                .filter(([, qty]) => (parseInt(qty) || 0) > 0)
                .map(([size]) => size);
            const productSizes = this.usesProductSizeTable
                ? this.selectedProduct.available_sizes.filter(size => Object.prototype.hasOwnProperty.call(this.defaultSizes, size))
                : [];
            const visibleSizes = new Set([...(productSizes.length ? productSizes : Object.keys(this.defaultSizes)), ...filledSizes]);

            return this.sizeTable
                .map(group => ({
                    ...group,
                    sizes: group.sizes.filter(size => visibleSizes.has(size)),
                }))
                .filter(group => group.sizes.length > 0);
        },

        get filteredProducts() {
            if (!this.form.cut_type_id) return this.products;
            return this.products.filter(p => String(p.cut_type_id) === String(this.form.cut_type_id));
        },

        get totalPieces() {
            const sizeTotal = Object.values(this.form.sizes).reduce((sum, qty) => sum + (parseInt(qty) || 0), 0);
            return sizeTotal || (parseInt(this.detectedQuantity) || 1);
        },

        get hasSizeMismatch() {
            return this.detectedQuantity > 0 && this.hasAnySizes && this.totalPieces !== this.detectedQuantity;
        },

        get subtotalProduct() {
            return this.totalPieces * parseFloat(this.form.unit_price || 0);
        },

        get totalPersonalizations() {
            return this.form.personalizations.reduce((sum, p) => sum + ((parseFloat(p.unit_price) || 0) * this.totalPieces), 0);
        },

        get grandTotal() {
            return this.subtotalProduct + this.totalPersonalizations;
        },

        freshForm() {
            return {
                cut_type_id: '',
                product_id: '',
                unit_price: 0,
                sizes: { ...this.defaultSizes },
                personalizations: [],
            };
        },

        normalizePersonalizationEntry(entry = {}) {
            const typeId = entry.type_id ? String(entry.type_id) : '';
            const typeName = entry.type_name || this.getTypeName(typeId);
            const usesColorCount = this.personalizationUsesColorCount({ type_id: typeId, type_name: typeName });
            const colorCount = parseInt(entry.color_count || 0) || 0;

            return {
                type_id: typeId,
                size_name: entry.size_name || '',
                location_id: entry.location_id ? String(entry.location_id) : '',
                unit_price: parseFloat(entry.unit_price || 0) || 0,
                color_count: usesColorCount ? Math.max(1, colorCount || 1) : null,
                color_details: entry.color_details || '',
                has_neon: Boolean(entry.has_neon),
                notes: entry.notes || '',
            };
        },

        init() {
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                this.recognition = new SpeechRecognition();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = 'pt-BR';

                this.recognition.onresult = (event) => {
                    let finalTranscript = '';
                    let interimTranscript = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) finalTranscript += event.results[i][0].transcript;
                        else interimTranscript += event.results[i][0].transcript;
                    }
                    if (interimTranscript) this.transcript = interimTranscript;
                    if (finalTranscript) { this.transcript = finalTranscript; this.processVoice(finalTranscript); }
                };

                this.recognition.onerror = (event) => {
                    this.isListening = false;
                    if (event.error === 'not-allowed') this.error = 'Permissão de microfone negada. Ative nas configurações do navegador.';
                    else this.error = 'Erro no reconhecimento de voz. Tente novamente.';
                };

                this.recognition.onend = () => { this.isListening = false; };
            }
        },

        async toggleListening() {
            if (!this.recognition) { this.error = 'Reconhecimento de voz não disponível neste navegador. Use Chrome ou Edge.'; return; }
            if (this.isListening) { this.recognition.stop(); this.isListening = false; return; }
            this.error = '';
            this.transcript = '';
            try {
                await navigator.mediaDevices.getUserMedia({ audio: true });
                this.recognition.start();
                this.isListening = true;
            } catch (err) {
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    this.error = 'Permissão de microfone negada. Ative nas configurações do navegador.';
                } else {
                    this.error = 'Erro ao acessar o microfone. Verifique se está conectado.';
                }
            }
        },

        async processVoice(text) {
            await this.sendForMatching({ text });
        },

        async handleAudioFileChange(event) {
            const [file] = event.target.files || [];
            event.target.value = '';

            if (!file) {
                return;
            }

            await this.sendForMatching({ audio: file });
        },

        async sendForMatching({ text = '', audio = null } = {}) {
            this.isProcessing = true;
            this.error = '';
            try {
                const formData = new FormData();
                if (text) formData.append('text', text);
                if (audio) formData.append('audio', audio);

                const response = await fetch('{{ route("voice-quote.match") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData,
                });
                const result = await response.json();

                if (!response.ok) {
                    const firstError = result?.errors ? Object.values(result.errors).flat()[0] : null;
                    this.error = firstError || result?.message || 'Não consegui processar o áudio.';
                    return;
                }

                if (result.success && result.data) {
                    this.aiSummary = result.data.summary || '';
                    this.aiProvider = result.meta?.provider || '';
                    this.transcript = result.data.raw_text || this.transcript;
                    this.applyMatchedData(result.data);
                    this.matchedData = true;
                } else {
                    this.error = 'Não consegui encontrar correspondências. Tente falar mais devagar.';
                }
            } catch (err) {
                this.error = 'Erro ao processar. Tente novamente.';
            } finally {
                this.isProcessing = false;
            }
        },

        applyMatchedData(data) {
            this.form = this.freshForm();
            this.detectedQuantity = parseInt(data.quantity || 0) || 0;

            if (data.product) {
                this.form.cut_type_id = data.product.cut_type_id ? String(data.product.cut_type_id) : '';
                this.form.product_id = String(data.product.id);
                this.form.unit_price = data.product.price;
            }

            if (data.sizes && Object.keys(data.sizes).length > 0) {
                for (const [size, qty] of Object.entries(data.sizes)) {
                    if (Object.prototype.hasOwnProperty.call(this.form.sizes, size)) this.form.sizes[size] = qty;
                }
            }
            if (data.personalizations && data.personalizations.length > 0) {
                this.form.personalizations = data.personalizations.map(entry => this.normalizePersonalizationEntry(entry));
            }
        },

        onCutTypeChange() {
            this.form.product_id = '';
            this.form.unit_price = 0;
        },

        onProductChange() {
            const option = this.products.find(p => String(p.id) === String(this.form.product_id));
            if (!option) {
                return;
            }

            const qty = parseInt(this.totalPieces || 1) || 1;
            const wholesaleMin = parseInt(option.wholesale_min_qty || 0) || 0;
            const wholesalePrice = parseFloat(option.wholesale_price || 0);

            if (wholesalePrice > 0 && wholesaleMin > 0 && qty >= wholesaleMin) {
                this.form.unit_price = wholesalePrice;
                return;
            }

            this.form.unit_price = parseFloat(option.price);
        },

        handleSizeChange() {
            this.form.personalizations.forEach((pers, index) => {
                if (pers.type_id && pers.size_name) {
                    this.syncPersonalizationPrice(index);
                }
            });
        },

        handlePersonalizationTypeChange(index) {
            const pers = this.form.personalizations[index];
            if (!pers) {
                return;
            }

            if (this.personalizationUsesColorCount(pers)) {
                pers.color_count = Math.max(1, parseInt(pers.color_count || 1) || 1);
            } else {
                pers.color_count = null;
                pers.color_details = '';
                pers.has_neon = false;
            }

            this.syncPersonalizationPrice(index);
        },

        getTypeName(typeId) {
            const type = this.personalizationTypes.find(item => String(item.id) === String(typeId));
            return type ? type.name : '';
        },

        getLocationName(locationId) {
            const location = this.locations.find(item => String(item.id) === String(locationId));
            return location ? location.name : '';
        },

        getTypeKey(typeReference) {
            if (typeReference && typeof typeReference === 'object') {
                if (typeReference.type_name) {
                    return String(typeReference.type_name).trim().toUpperCase();
                }

                if (typeReference.type_id) {
                    return String(this.getTypeName(typeReference.type_id) || '').trim().toUpperCase();
                }
            }

            const rawValue = String(typeReference || '').trim();
            if (!rawValue) {
                return '';
            }

            if (/^\d+$/.test(rawValue)) {
                return String(this.getTypeName(rawValue) || '').trim().toUpperCase();
            }

            return rawValue.toUpperCase();
        },

        getPersonalizationSetting(typeReference) {
            const typeKey = this.getTypeKey(typeReference);
            return typeKey ? this.personalizationSettings[typeKey] || null : null;
        },

        personalizationUsesColorCount(typeReference) {
            const setting = this.getPersonalizationSetting(typeReference);
            if (setting) {
                return Boolean(setting.charge_by_color);
            }

            return ['SERIGRAFIA', 'EMBORRACHADO'].includes(this.getTypeKey(typeReference));
        },

        getFallbackColorSurcharge(pers) {
            const setting = this.getPersonalizationSetting(pers);
            const colorCount = parseInt(pers.color_count || 0) || 0;
            if (!setting || !colorCount) {
                return 0;
            }

            const minColors = parseInt(setting.min_colors || 1) || 1;
            if (colorCount <= minColors) {
                return 0;
            }

            return (colorCount - minColors) * (parseFloat(setting.color_price_per_unit || 0) || 0);
        },

        async syncPersonalizationPrice(index) {
            const pers = this.form.personalizations[index];
            if (!pers) {
                return;
            }

            const typeKey = this.getTypeKey(pers);
            if (!typeKey || !pers.size_name) {
                return;
            }

            const quantity = Math.max(1, parseInt(this.totalPieces || 1) || 1);

            try {
                const baseResponse = await fetch(
                    `${this.personalizationPriceUrl}?type=${encodeURIComponent(typeKey)}&size=${encodeURIComponent(pers.size_name)}&quantity=${quantity}`,
                    { headers: { Accept: 'application/json' } }
                );

                if (!baseResponse.ok) {
                    return;
                }

                const baseData = await baseResponse.json();
                if (!baseData.success) {
                    return;
                }

                let unitPrice = parseFloat(baseData.price || 0) || 0;

                if (this.personalizationUsesColorCount(pers) && (parseInt(pers.color_count || 0) > 1)) {
                    let colorSurcharge = this.getFallbackColorSurcharge(pers);
                    const colorResponse = await fetch(
                        `${this.personalizationPriceUrl}?type=${encodeURIComponent(typeKey)}&size=COR&quantity=${quantity}`,
                        { headers: { Accept: 'application/json' } }
                    );

                    if (colorResponse.ok) {
                        const colorData = await colorResponse.json();
                        if (colorData.success) {
                            colorSurcharge = (parseFloat(colorData.price || 0) || 0) * ((parseInt(pers.color_count || 1) || 1) - 1);
                        }
                    }

                    unitPrice += colorSurcharge;
                }

                pers.unit_price = Number(unitPrice.toFixed(2));
            } catch (error) {
                console.error('Erro ao atualizar preço da personalização:', error);
            }
        },

        addPersonalization() {
            this.form.personalizations.push(this.normalizePersonalizationEntry({ color_count: 1 }));
            this.matchedData = true;
        },

        getColorCountLabel(pers) {
            const colorCount = parseInt(pers.color_count || 0) || 0;
            if (!this.personalizationUsesColorCount(pers) || colorCount < 1) {
                return '';
            }

            let label = `${colorCount} ${colorCount === 1 ? 'cor' : 'cores'}`;
            if (pers.has_neon) {
                label += ' + neon';
            }

            return label;
        },

        buildPersonalizationLabel(pers) {
            const parts = [
                this.getTypeName(pers.type_id) || 'Personalização',
                pers.size_name || null,
                this.getLocationName(pers.location_id) || null,
            ].filter(Boolean);
            const baseLabel = parts.join(' - ');
            const colorLabel = this.getColorCountLabel(pers);

            return colorLabel ? `${baseLabel} • ${colorLabel}` : baseLabel;
        },

        resetForm() {
            this.form = this.freshForm();
            this.detectedQuantity = 0;
            this.aiSummary = '';
            this.aiProvider = '';
            this.matchedData = false;
            this.transcript = '';
            this.error = '';
        },

        buildQuoteText() {
            const product = this.products.find(item => String(item.id) === String(this.form.product_id));
            const lines = ['*ORÇAMENTO*', ''];
            if (product) {
                lines.push(`*Produto:* ${product.title}`);
                lines.push(`*Preço unitário:* R$ ${parseFloat(this.form.unit_price || 0).toFixed(2)}`);
            }

            const sizes = Object.entries(this.form.sizes).filter(([, qty]) => qty > 0);
            if (sizes.length) {
                lines.push(`*Tamanhos:* ${sizes.map(([size, qty]) => `${size}(${qty})`).join(', ')}`);
                lines.push(`*Total peças:* ${this.totalPieces}`);
            } else if (this.detectedQuantity > 0) {
                lines.push(`*Quantidade detectada:* ${this.detectedQuantity}`);
                lines.push('*Tamanhos:* pendente de distribuição');
            }

            if (this.form.personalizations.length) {
                lines.push('');
                lines.push('*Personalizações:*');
                this.form.personalizations.forEach((pers, index) => {
                    const details = pers.color_details ? ` (${pers.color_details})` : '';
                    lines.push(`  ${index + 1}. ${this.buildPersonalizationLabel(pers)}${details} — R$ ${parseFloat(pers.unit_price || 0).toFixed(2)}/un`);
                });
            }

            lines.push('');
            lines.push(`*Subtotal Produto:* R$ ${this.subtotalProduct.toFixed(2)}`);
            if (this.totalPersonalizations > 0) {
                lines.push(`*Subtotal Personalizações:* R$ ${this.totalPersonalizations.toFixed(2)}`);
            }
            lines.push(`*TOTAL: R$ ${this.grandTotal.toFixed(2)}*`);
            return lines.join('\n');
        },

        copyToClipboard() { navigator.clipboard.writeText(this.buildQuoteText()).then(() => alert('Orçamento copiado!')); },
        shareWhatsApp() { window.open(`https://wa.me/?text=${encodeURIComponent(this.buildQuoteText())}`, '_blank'); },
    };
}
</script>
@endsection
