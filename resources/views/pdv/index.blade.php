{{-- 
    PDV - Ponto de Venda
    Esta view é APENAS para vendas (PDV), NÃO deve conter:
    - Lista de estoque agrupado (@forelse($groupedStocks...))
    - Formulários de filtro de estoque
    - Tabelas de estoque
    - Qualquer código relacionado a gerenciamento de estoque
    
    O PDV apenas verifica estoque via API ao adicionar produtos ao carrinho.
--}}
@extends('layouts.admin')

@section('content')
<style>
    :root {
        --pdv-shell-bg: #f4f7fb;
        --pdv-shell-border: #d6deea;
        --pdv-text-primary: #10203b;
        --pdv-text-secondary: #5f7088;
        --pdv-tab-text: #5f7088;
        --pdv-card-bg: #ffffff;
        --pdv-card-border: #dce5ef;
        --pdv-card-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        --pdv-soft-bg: #f6f8fc;
        --pdv-soft-border: #e3ebf4;
        --pdv-input-bg: #f8fbff;
        --pdv-input-border: #d9e3ef;
        --pdv-input-text: #24344f;
        --pdv-accent: #7c3aed;
        --pdv-accent-hover: #6d28d9;
        --pdv-accent-soft: rgba(124, 58, 237, 0.14);
        --pdv-danger-bg: rgba(239, 68, 68, 0.08);
        --pdv-danger-border: rgba(239, 68, 68, 0.16);
    }

    .dark {
        --pdv-shell-bg: #0d1830;
        --pdv-shell-border: rgba(148, 163, 184, 0.16);
        --pdv-text-primary: #e5edf8;
        --pdv-text-secondary: #91a4c0;
        --pdv-tab-text: #9eb1cc;
        --pdv-card-bg: #10203a;
        --pdv-card-border: rgba(148, 163, 184, 0.12);
        --pdv-card-shadow: 0 18px 30px rgba(2, 6, 23, 0.14);
        --pdv-soft-bg: #142543;
        --pdv-soft-border: rgba(148, 163, 184, 0.1);
        --pdv-input-bg: #162847;
        --pdv-input-border: rgba(148, 163, 184, 0.18);
        --pdv-input-text: #e5edf8;
        --pdv-accent-soft: rgba(124, 58, 237, 0.16);
        --pdv-danger-bg: rgba(239, 68, 68, 0.12);
        --pdv-danger-border: rgba(239, 68, 68, 0.18);
    }

    .pdv-ft {
        background: var(--pdv-shell-bg);
        border: 1px solid var(--pdv-shell-border);
        border-radius: 24px;
        padding: 24px;
        color: var(--pdv-text-primary);
    }

    .pdv-ft-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .pdv-ft-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1 1 320px;
    }

    .pdv-ft-logo {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--pdv-accent);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .pdv-ft-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--pdv-text-primary);
    }

    .pdv-ft-subtitle {
        margin-top: 3px;
        font-size: 13px;
        font-weight: 600;
        color: var(--pdv-text-secondary);
    }

    .pdv-ft-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .pdv-ft-btn {
        height: 38px;
        border-radius: 12px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
        white-space: nowrap;
    }

    .pdv-ft-btn:hover,
    .pdv-primary-action:hover,
    .pdv-grid-action:hover,
    .pdv-fab:hover {
        transform: translateY(-1px);
    }

    .pdv-ft-btn-primary {
        background: var(--pdv-accent);
        border: 1px solid var(--pdv-accent);
        color: #fff !important;
    }

    .pdv-ft-btn-primary:hover {
        background: var(--pdv-accent-hover);
        border-color: var(--pdv-accent-hover);
    }

    .pdv-ft-btn-secondary {
        color: var(--pdv-text-primary) !important;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
    }

    .pdv-ft-btn-secondary:hover {
        color: var(--pdv-accent) !important;
        background: var(--pdv-card-bg);
        border-color: rgba(124, 58, 237, 0.3);
    }

    .pdv-card,
    .pdv-neo-card,
    .pdv-ft-shell-card {
        background: var(--pdv-card-bg) !important;
        border: 1px solid var(--pdv-card-border) !important;
        box-shadow: var(--pdv-card-shadow) !important;
        color: var(--pdv-text-primary) !important;
    }

    .pdv-muted-surface,
    .pdv-summary-box {
        background: var(--pdv-soft-bg) !important;
        border: 1px solid var(--pdv-soft-border) !important;
        box-shadow: none !important;
    }

    .pdv-search-results {
        background: var(--pdv-card-bg) !important;
        border: 1px solid var(--pdv-card-border) !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08) !important;
    }

    .pdv-panel-divider {
        border-color: var(--pdv-soft-border) !important;
    }

    .pdv-ft-tabs {
        margin-bottom: 18px;
        padding: 8px;
    }

    .pdv-tab-link {
        appearance: none;
        cursor: pointer;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--pdv-tab-text);
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .pdv-tab-link:hover {
        background: var(--pdv-soft-bg);
        border-color: var(--pdv-soft-border);
    }

    .pdv-tab-link.bg-indigo-600 {
        background: var(--pdv-accent) !important;
        border-color: var(--pdv-accent) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .pdv-neo-panel {
        background: transparent !important;
    }

    .pdv-neo-card-hover {
        transition: border-color .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .pdv-neo-card-hover:hover {
        border-color: rgba(124, 58, 237, 0.3) !important;
        box-shadow: var(--pdv-card-shadow) !important;
    }

    .pdv-neo-input,
    input.pdv-neo-input,
    select.pdv-neo-input,
    textarea.pdv-neo-input,
    input.pdv-card,
    select.pdv-card,
    textarea.pdv-card {
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        border: 1px solid var(--pdv-input-border) !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--pdv-input-bg) inset !important;
        box-shadow: none !important;
        caret-color: var(--pdv-input-text);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .pdv-neo-input:focus,
    input.pdv-neo-input:focus,
    select.pdv-neo-input:focus,
    textarea.pdv-neo-input:focus,
    input.pdv-card:focus,
    select.pdv-card:focus,
    textarea.pdv-card:focus {
        outline: none !important;
        border-color: var(--pdv-accent) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--pdv-input-bg) inset, 0 0 0 3px var(--pdv-accent-soft) !important;
        box-shadow: 0 0 0 3px var(--pdv-accent-soft) !important;
    }

    .pdv-neo-input::placeholder,
    input.pdv-card::placeholder,
    textarea.pdv-card::placeholder {
        color: var(--pdv-text-secondary) !important;
    }

    .pdv-primary-action,
    .pdv-grid-action,
    button.bg-gray-900,
    a.bg-gray-900,
    button.bg-indigo-600,
    a.bg-indigo-600,
    button.bg-purple-600,
    a.bg-purple-600 {
        background: var(--pdv-accent) !important;
        border: 1px solid var(--pdv-accent) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .pdv-primary-action,
    .pdv-primary-action span,
    .pdv-primary-action i,
    .pdv-primary-action svg,
    .pdv-primary-action svg *,
    .pdv-grid-action,
    .pdv-grid-action span,
    .pdv-grid-action i,
    .pdv-grid-action svg,
    .pdv-grid-action svg *,
    .pdv-ft-btn-primary,
    .pdv-ft-btn-primary span,
    .pdv-ft-btn-primary i,
    .pdv-ft-btn-primary svg,
    .pdv-ft-btn-primary svg *,
    button.bg-gray-900 *,
    a.bg-gray-900 *,
    button.bg-indigo-600 *,
    a.bg-indigo-600 *,
    button.bg-purple-600 *,
    a.bg-purple-600 * {
        color: #fff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    .pdv-primary-action:hover,
    .pdv-grid-action:hover,
    button.hover\:bg-black:hover,
    a.hover\:bg-black:hover,
    button.hover\:bg-indigo-700:hover,
    a.hover\:bg-indigo-700:hover,
    button.hover\:bg-purple-700:hover,
    a.hover\:bg-purple-700:hover {
        background: var(--pdv-accent-hover) !important;
        border-color: var(--pdv-accent-hover) !important;
    }

    .pdv-primary-action:disabled,
    button.bg-gray-900:disabled,
    button.bg-indigo-600:disabled,
    button.bg-purple-600:disabled {
        background: #94a3b8 !important;
        border-color: #94a3b8 !important;
        box-shadow: none !important;
        opacity: 0.55;
    }

    .pdv-secondary-action {
        background: var(--pdv-soft-bg) !important;
        color: var(--pdv-text-primary) !important;
        border: 1px solid var(--pdv-soft-border) !important;
        box-shadow: none !important;
    }

    .pdv-secondary-action:hover {
        color: var(--pdv-accent) !important;
        background: var(--pdv-card-bg) !important;
        border-color: rgba(124, 58, 237, 0.3) !important;
    }

    .pdv-danger-action {
        background: var(--pdv-danger-bg) !important;
        color: #ef4444 !important;
        border: 1px solid var(--pdv-danger-border) !important;
        box-shadow: none !important;
    }

    .pdv-danger-action:hover {
        background: rgba(239, 68, 68, 0.16) !important;
    }

    .pdv-total-preview {
        background: var(--pdv-card-bg) !important;
        color: var(--pdv-text-primary) !important;
        border: 1px solid var(--pdv-card-border) !important;
        box-shadow: var(--pdv-card-shadow) !important;
    }

    .pdv-fab {
        background: var(--pdv-accent) !important;
        border: 1px solid var(--pdv-accent) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .pdv-fab,
    .pdv-fab svg,
    .pdv-fab svg * {
        color: #fff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }

    .pdv-overlay,
    #add-product-modal,
    #clear-cart-modal,
    #sublocal-modal,
    #payment-modal {
        background: rgba(2, 6, 23, 0.64) !important;
        backdrop-filter: blur(6px);
    }

    #add-product-modal > div,
    #clear-cart-modal > div,
    #sublocal-modal > div,
    #payment-modal > div {
        background: var(--pdv-card-bg) !important;
        border: 1px solid var(--pdv-card-border) !important;
        box-shadow: 0 16px 40px rgba(2, 6, 23, 0.2) !important;
    }

    .pdv-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(320px, 380px);
        gap: 18px;
        align-items: start;
    }

    .pdv-main-stack,
    .pdv-sidebar-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .pdv-toolbar-card,
    .pdv-catalog-card,
    .pdv-section-card {
        background: var(--pdv-card-bg);
        border: 1px solid var(--pdv-card-border);
        border-radius: 20px;
        box-shadow: var(--pdv-card-shadow);
    }

    .pdv-toolbar-card {
        padding: 20px;
    }

    .pdv-toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .pdv-toolbar-copy h2 {
        font-size: 18px;
        line-height: 1.15;
        font-weight: 800;
        color: var(--pdv-text-primary);
    }

    .pdv-toolbar-copy p {
        margin-top: 4px;
        font-size: 13px;
        color: var(--pdv-text-secondary);
        font-weight: 500;
    }

    .pdv-filter-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 16px;
        position: relative;
    }

    .pdv-filter-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-tabs-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 6px;
        border-radius: 16px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
    }

    .pdv-search-field {
        display: grid;
        grid-template-columns: 20px minmax(0, 1fr);
        align-items: center;
        gap: 12px;
        min-height: 44px;
        padding: 0 16px;
        border-radius: 12px;
        border: 1px solid var(--pdv-input-border);
        background: var(--pdv-input-bg);
        background-color: var(--pdv-input-bg);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .pdv-search-field.pdv-search-field-with-action {
        grid-template-columns: 20px minmax(0, 1fr) auto;
    }

    .pdv-search-field-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        pointer-events: none;
        color: var(--pdv-text-secondary);
        transition: color .2s ease;
    }

    .pdv-search-field:focus-within .pdv-search-field-icon {
        color: var(--pdv-accent);
    }

    .pdv-search-field:focus-within {
        border-color: var(--pdv-accent) !important;
        box-shadow: 0 0 0 3px var(--pdv-accent-soft) !important;
    }

    .pdv-search-field-input {
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        height: 42px !important;
        min-height: 42px !important;
        border-radius: 0 !important;
        border: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        line-height: 1.2 !important;
        caret-color: var(--pdv-input-text);
        text-indent: 0 !important;
        appearance: none;
        -webkit-appearance: none;
    }

    .pdv-search-field-input:focus {
        outline: none !important;
        border-color: transparent !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
    }

    .pdv-search-field-input::placeholder {
        color: var(--pdv-text-secondary) !important;
    }

    .pdv-search-field-input.has-action {
        padding-right: 8px !important;
    }

    .pdv-search-field-action {
        position: relative;
        display: inline-flex;
        align-items: center;
        align-self: stretch;
        font-size: 12px;
        font-weight: 700;
        color: var(--pdv-accent);
        text-decoration: none;
        white-space: nowrap;
    }

    .pdv-search-field-action:hover {
        color: var(--pdv-accent-hover);
    }

    .pdv-ft input.pdv-search-field-input {
        background: transparent !important;
        background-color: transparent !important;
        border-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
    }

    .pdv-ft input.pdv-search-field-input:focus {
        border-color: transparent !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
    }

    .dark .pdv-ft input.pdv-search-field-input,
    .dark .pdv-ft input.pdv-search-field-input:-webkit-autofill,
    .dark .pdv-ft input.pdv-search-field-input:-webkit-autofill:hover,
    .dark .pdv-ft input.pdv-search-field-input:-webkit-autofill:focus {
        background: transparent !important;
        background-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px transparent inset !important;
    }

    .pdv-catalog-card {
        overflow: hidden;
    }

    .pdv-catalog-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--pdv-soft-border);
    }

    .pdv-catalog-head span {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-catalog-head h3 {
        margin-top: 4px;
        font-size: 20px;
        line-height: 1.15;
        font-weight: 800;
        color: var(--pdv-text-primary);
    }

    .pdv-catalog-note {
        font-size: 13px;
        font-weight: 600;
        color: var(--pdv-text-secondary);
        max-width: 260px;
        text-align: right;
    }

    .pdv-catalog-body {
        padding: 18px 20px 20px;
    }

    .pdv-section-card {
        padding: 20px;
    }

    .pdv-section-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .pdv-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
        color: var(--pdv-accent);
        flex-shrink: 0;
    }

    .pdv-section-head h3 {
        font-size: 16px;
        line-height: 1.15;
        font-weight: 800;
        color: var(--pdv-text-primary);
    }

    .pdv-section-head p {
        margin-top: 3px;
        font-size: 12px;
        line-height: 1.45;
        color: var(--pdv-text-secondary);
        font-weight: 500;
    }

    .pdv-client-display {
        margin-bottom: 12px;
    }

    .pdv-client-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px;
        border-radius: 14px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
    }

    .pdv-client-card button {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pdv-client-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(124, 58, 237, 0.12);
        color: var(--pdv-accent);
        flex-shrink: 0;
    }

    .pdv-client-actions {
        display: flex;
        gap: 10px;
    }

    .pdv-cart-shell {
        max-height: min(48vh, 520px);
        overflow-y: auto;
        padding-right: 4px;
    }

    .pdv-cart-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .pdv-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 220px;
        padding: 24px;
        text-align: center;
        color: var(--pdv-text-secondary);
    }

    .pdv-empty-state svg {
        opacity: .35;
    }

    .pdv-cart-item {
        padding: 14px;
        border-radius: 16px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
    }

    .pdv-cart-item-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .pdv-cart-item-title {
        font-size: 14px;
        line-height: 1.4;
        font-weight: 700;
        color: var(--pdv-text-primary);
    }

    .pdv-cart-item-meta {
        margin-top: 4px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .pdv-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 24px;
        padding: 0 10px;
        border-radius: 999px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
        font-size: 11px;
        font-weight: 700;
        color: var(--pdv-text-secondary);
    }

    .pdv-chip-accent {
        color: var(--pdv-accent);
        border-color: rgba(124, 58, 237, 0.2);
        background: rgba(124, 58, 237, 0.08);
    }

    .pdv-chip-success {
        color: #16a34a;
        border-color: rgba(34, 197, 94, 0.22);
        background: rgba(34, 197, 94, 0.08);
    }

    .pdv-chip-danger {
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.2);
        background: rgba(239, 68, 68, 0.08);
    }

    .pdv-cart-remove {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #ef4444;
        opacity: .72;
        transition: background .18s ease, opacity .18s ease;
    }

    .pdv-cart-remove:hover {
        background: rgba(239, 68, 68, 0.08);
        opacity: 1;
    }

    .pdv-cart-item-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .pdv-cart-controls {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 10px;
        border-radius: 12px;
        background: var(--pdv-card-bg);
        border: 1px solid var(--pdv-input-border);
    }

    .pdv-cart-controls input {
        background: transparent !important;
        border: none;
        box-shadow: none;
    }

    .pdv-cart-controls input:focus {
        box-shadow: none !important;
    }

    .pdv-cart-price {
        font-size: 16px;
        font-weight: 800;
        color: var(--pdv-text-primary);
        white-space: nowrap;
    }

    .pdv-cart-discount-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--pdv-soft-border);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pdv-cart-discount-row label {
        font-size: 11px;
        font-weight: 700;
        color: var(--pdv-text-secondary);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .pdv-summary-grid {
        display: grid;
        gap: 12px;
    }

    .pdv-summary-breakdown {
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: 16px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
        display: grid;
        gap: 10px;
    }

    .pdv-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--pdv-text-secondary);
    }

    .pdv-summary-row strong {
        font-size: 14px;
        font-weight: 800;
        color: var(--pdv-text-primary);
    }

    .pdv-summary-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-summary-inline {
        display: grid;
        grid-template-columns: 84px minmax(0, 1fr);
        gap: 10px;
    }

    .pdv-summary-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid var(--pdv-input-border);
        background: var(--pdv-input-bg);
        background-color: var(--pdv-input-bg);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .pdv-summary-input-wrap:focus-within {
        border-color: var(--pdv-accent) !important;
        box-shadow: 0 0 0 3px var(--pdv-accent-soft) !important;
    }

    .pdv-summary-select-wrap::after {
        content: '';
        position: absolute;
        right: 16px;
        top: 50%;
        width: 8px;
        height: 8px;
        border-right: 2px solid var(--pdv-text-secondary);
        border-bottom: 2px solid var(--pdv-text-secondary);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }

    .pdv-summary-prefix {
        padding-left: 14px;
        font-size: 13px;
        font-weight: 700;
        color: var(--pdv-text-secondary);
        white-space: nowrap;
    }

    .pdv-summary-input,
    .pdv-summary-select {
        width: 100%;
        min-width: 0;
        min-height: 46px;
        border: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
        appearance: none;
        -webkit-appearance: none;
        outline: none !important;
    }

    .pdv-ft .pdv-summary-input-wrap input.pdv-summary-input,
    .pdv-ft .pdv-summary-input-wrap select.pdv-summary-select,
    .dark .pdv-ft .pdv-summary-input-wrap input.pdv-summary-input,
    .dark .pdv-ft .pdv-summary-input-wrap select.pdv-summary-select {
        background: transparent !important;
        background-color: transparent !important;
        border: 0 !important;
        border-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
    }

    .pdv-ft .pdv-summary-input-wrap,
    .dark .pdv-ft .pdv-summary-input-wrap {
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        border-color: var(--pdv-input-border) !important;
    }

    .pdv-summary-select {
        cursor: pointer;
        padding-right: 34px;
    }

    .pdv-summary-input::placeholder {
        color: var(--pdv-text-secondary) !important;
    }

    .pdv-summary-input::-webkit-outer-spin-button,
    .pdv-summary-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .pdv-summary-input[type=number] {
        -moz-appearance: textfield;
    }

    .pdv-summary-notes {
        min-height: 52px;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .dark .pdv-summary-input:-webkit-autofill,
    .dark .pdv-summary-input:-webkit-autofill:hover,
    .dark .pdv-summary-input:-webkit-autofill:focus {
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px transparent inset !important;
    }

    .pdv-summary-total {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--pdv-soft-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .pdv-summary-total span {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-summary-total strong {
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--pdv-text-primary);
        text-align: right;
        white-space: nowrap;
    }

    .pdv-mobile-drawer-head {
        padding: 12px 16px 14px;
        border-bottom: 1px solid var(--pdv-soft-border);
    }

    .pdv-mobile-total {
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .pdv-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));
        gap: 16px;
    }

    .pdv-product-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        min-width: 0;
        gap: 14px;
        padding: 16px;
        border-radius: 18px;
        background: var(--pdv-card-bg);
        border: 1px solid var(--pdv-card-border);
        box-shadow: var(--pdv-card-shadow);
        transition: border-color .18s ease, transform .18s ease;
    }

    .pdv-product-card:hover {
        transform: translateY(-2px);
        border-color: rgba(124, 58, 237, 0.3);
    }

    .pdv-product-media {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .pdv-product-thumb {
        aspect-ratio: 1 / 1;
        width: 88px;
        overflow: hidden;
        border-radius: 16px;
        background: var(--pdv-soft-bg);
        border: 1px solid var(--pdv-soft-border);
        flex-shrink: 0;
    }

    .pdv-product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pdv-product-thumb-placeholder {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--pdv-text-secondary);
        font-size: 20px;
    }

    .pdv-product-summary {
        min-width: 0;
        flex: 1;
    }

    .pdv-product-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .pdv-product-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-product-title {
        margin-top: 4px;
        font-size: 18px;
        line-height: 1.25;
        font-weight: 800;
        color: var(--pdv-text-primary);
    }

    .pdv-product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }

    .pdv-product-footer {
        margin-top: auto;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
    }

    .pdv-product-footer > :first-child {
        min-width: 0;
        flex: 1 1 120px;
    }

    .pdv-product-footer .pdv-grid-action,
    .pdv-product-action {
        flex: 0 1 auto;
        width: auto !important;
        min-width: 132px !important;
        max-width: 100%;
        margin-left: auto;
        padding-left: 18px !important;
        padding-right: 18px !important;
        white-space: nowrap;
    }

    .pdv-product-price-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-product-price {
        margin-top: 4px;
        font-size: clamp(24px, 2vw, 28px);
        line-height: 1.02;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--pdv-text-primary);
        overflow-wrap: anywhere;
    }

    .pdv-modal-stack {
        display: grid;
        gap: 18px;
    }

    .pdv-modal-card {
        background: var(--pdv-soft-bg) !important;
        border: 1px solid var(--pdv-soft-border) !important;
        border-radius: 18px;
        padding: 16px;
    }

    .pdv-modal-header-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .pdv-modal-kpi-label,
    .pdv-modal-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-kpi-value {
        margin-top: 6px;
        font-size: 20px;
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--pdv-text-primary);
    }

    .pdv-modal-kpi-meta {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.45;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid rgba(34, 197, 94, 0.22);
        background: rgba(34, 197, 94, 0.08);
        color: #16a34a;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .pdv-modal-field {
        display: grid;
        gap: 8px;
    }

    .pdv-modal-label .required {
        color: #ef4444;
    }

    .pdv-modal-helper {
        font-size: 12px;
        line-height: 1.45;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid var(--pdv-input-border);
        background: var(--pdv-input-bg);
        background-color: var(--pdv-input-bg);
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .pdv-modal-input-wrap:focus-within {
        border-color: var(--pdv-accent) !important;
        box-shadow: 0 0 0 3px var(--pdv-accent-soft) !important;
    }

    .pdv-modal-input-wrap.is-disabled {
        opacity: .65;
    }

    .pdv-modal-prefix {
        padding-left: 14px;
        font-size: 13px;
        font-weight: 800;
        color: var(--pdv-text-secondary);
        white-space: nowrap;
    }

    .pdv-modal-select-wrap::after {
        content: '';
        position: absolute;
        right: 16px;
        top: 50%;
        width: 8px;
        height: 8px;
        border-right: 2px solid var(--pdv-text-secondary);
        border-bottom: 2px solid var(--pdv-text-secondary);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }

    .pdv-modal-input,
    .pdv-modal-select,
    .pdv-modal-textarea {
        width: 100%;
        min-width: 0;
        min-height: 48px;
        border: 0 !important;
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--pdv-input-bg) inset !important;
        box-shadow: none !important;
        caret-color: var(--pdv-input-text);
        padding: 0 14px;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        appearance: none;
        -webkit-appearance: none;
        outline: none !important;
    }

    .pdv-modal-select {
        cursor: pointer;
        padding-right: 36px;
    }

    .pdv-modal-textarea {
        min-height: 112px;
        padding-top: 12px;
        padding-bottom: 12px;
        resize: vertical;
    }

    .pdv-modal-input.with-prefix {
        padding-left: 10px;
    }

    .pdv-modal-input::placeholder {
        color: var(--pdv-text-secondary) !important;
        opacity: 1;
    }

    .pdv-modal-input::-webkit-outer-spin-button,
    .pdv-modal-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .pdv-modal-input[type=number] {
        -moz-appearance: textfield;
    }

    #add-product-modal .pdv-modal-input-wrap input.pdv-modal-input,
    #add-product-modal .pdv-modal-input-wrap select.pdv-modal-select,
    #add-product-modal .pdv-modal-input-wrap textarea.pdv-modal-textarea,
    .dark #add-product-modal .pdv-modal-input-wrap input.pdv-modal-input,
    .dark #add-product-modal .pdv-modal-input-wrap select.pdv-modal-select,
    .dark #add-product-modal .pdv-modal-input-wrap textarea.pdv-modal-textarea {
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        border: 0 !important;
        border-color: transparent !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--pdv-input-bg) inset !important;
        box-shadow: none !important;
    }

    #add-product-modal .pdv-modal-input-wrap,
    .dark #add-product-modal .pdv-modal-input-wrap {
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        border-color: var(--pdv-input-border) !important;
    }

    .pdv-modal-inline-metrics {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pdv-modal-mini-kpi {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: var(--pdv-card-bg);
        border: 1px solid var(--pdv-card-border);
        font-size: 12px;
        font-weight: 700;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-mini-kpi strong {
        color: var(--pdv-text-primary);
    }

    .pdv-modal-swatches {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pdv-modal-swatch {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 38px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid var(--pdv-soft-border);
        background: var(--pdv-card-bg);
        color: var(--pdv-text-primary);
        font-size: 12px;
        font-weight: 700;
        transition: border-color .18s ease, transform .18s ease, background .18s ease, color .18s ease;
    }

    .pdv-modal-swatch:hover {
        transform: translateY(-1px);
        border-color: rgba(124, 58, 237, 0.28);
    }

    .pdv-modal-swatch.is-selected {
        border-color: rgba(124, 58, 237, 0.35);
        background: rgba(124, 58, 237, 0.12);
        color: var(--pdv-accent);
    }

    .pdv-modal-swatch-dot {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.18);
        flex-shrink: 0;
    }

    .pdv-modal-swatch-stock {
        font-size: 10px;
        font-weight: 800;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .pdv-modal-section-title {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
    }

    .pdv-modal-size-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
    }

    .pdv-modal-size-card {
        position: relative;
        display: grid;
        gap: 8px;
    }

    .pdv-modal-size-card label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pdv-text-secondary);
        text-align: center;
    }

    .pdv-modal-size-input {
        width: 100%;
        min-width: 0;
        height: 44px;
        border-radius: 14px;
        border: 1px solid var(--pdv-input-border) !important;
        background: var(--pdv-input-bg) !important;
        background-color: var(--pdv-input-bg) !important;
        color: var(--pdv-input-text) !important;
        -webkit-text-fill-color: var(--pdv-input-text) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--pdv-input-bg) inset !important;
        box-shadow: none !important;
        text-align: center;
        font-size: 16px;
        font-weight: 800;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .pdv-modal-size-input:focus {
        outline: none;
        border-color: var(--pdv-accent);
        box-shadow: 0 0 0 3px var(--pdv-accent-soft);
    }

    .pdv-modal-stock-list {
        display: grid;
        gap: 10px;
    }

    .pdv-modal-stock-empty {
        padding: 14px;
        border-radius: 16px;
        background: var(--pdv-soft-bg);
        border: 1px dashed var(--pdv-soft-border);
        font-size: 13px;
        color: var(--pdv-text-secondary);
    }

    @media (max-width: 768px) {
        .pdv-modal-header-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .pdv-modal-size-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .pdv-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 22px;
    }

    .pdv-pagination-actions {
        display: flex;
        gap: 8px;
    }

    .pdv-pagination-btn {
        appearance: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid var(--pdv-soft-border);
        background: var(--pdv-soft-bg);
        font-size: 13px;
        font-weight: 700;
        color: var(--pdv-text-primary);
        transition: background .18s ease, border-color .18s ease, color .18s ease;
    }

    .pdv-pagination-btn:hover {
        border-color: rgba(124, 58, 237, 0.3);
        color: var(--pdv-accent);
    }

    @media (max-width: 1200px) {
        .pdv-layout {
            grid-template-columns: 1fr;
        }
    }

    .pdv-ft .text-gray-900,
    .pdv-ft .text-gray-800,
    .pdv-ft .text-gray-700 {
        color: var(--pdv-text-primary) !important;
    }

    .pdv-ft .text-gray-600,
    .pdv-ft .text-gray-500,
    .pdv-ft .text-gray-400 {
        color: var(--pdv-text-secondary) !important;
    }

    @media (max-width: 768px) {
        .pdv-ft {
            padding: 14px;
            border-radius: 16px;
        }

        .pdv-ft-title {
            font-size: 24px;
        }

        .pdv-ft-actions {
            width: 100%;
        }

        .pdv-ft-btn {
            flex: 1 1 0;
            justify-content: center;
        }

        .pdv-toolbar-row,
        .pdv-catalog-head,
        .pdv-summary-total {
            flex-direction: column;
            align-items: flex-start;
        }

        .pdv-tabs-wrap {
            justify-content: flex-start;
        }

        .pdv-section-card,
        .pdv-toolbar-card,
        .pdv-catalog-body,
        .pdv-catalog-head {
            padding-left: 14px;
            padding-right: 14px;
        }

        .pdv-summary-total strong {
            font-size: 32px;
            text-align: left;
        }

        .pdv-pagination {
            flex-direction: column;
            align-items: stretch;
        }

        .pdv-product-media,
        .pdv-product-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .pdv-product-footer .pdv-grid-action {
            width: 100% !important;
        }

        .pdv-product-thumb {
            width: 100%;
            max-width: 120px;
        }
    }
</style>

@php
    $cartSubtotal = !empty($cart) ? array_sum(array_column($cart, 'total_price')) : 0;
    $cartItemsCount = !empty($cart)
        ? array_sum(array_map(static fn ($item) => (float) ($item['quantity'] ?? 0), $cart))
        : 0;
@endphp

<div class="max-w-[1520px] mx-auto px-4 sm:px-6 lg:px-8 pt-2 md:pt-3 pb-20">
    <section class="pdv-ft">
        <div class="pdv-ft-topbar">
            <div class="pdv-ft-brand">
                <span class="pdv-ft-logo"><i class="fa-solid fa-cash-register"></i></span>
                <div>
                    <h1 class="pdv-ft-title">Ponto de Venda</h1>
                    <p class="pdv-ft-subtitle">Fluxo rapido de venda com a mesma linguagem visual de vendas e pedidos.</p>
                </div>
            </div>

            <div class="pdv-ft-actions">
                <a href="{{ route('sales.index') }}" class="pdv-ft-btn pdv-ft-btn-secondary">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Vendas</span>
                </a>
                <a href="{{ route('pdv.sales') }}" class="pdv-ft-btn pdv-ft-btn-primary">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Historico PDV</span>
                </a>
            </div>
        </div>

        <div class="pdv-layout">
            <div class="pdv-main-stack">
                <div class="pdv-toolbar-card">
                    <div class="pdv-toolbar-row">
                        <div class="pdv-toolbar-copy">
                            <h2>Catalogo PDV</h2>
                            <p>Escolha a categoria, pesquise pelo nome e abra a configuracao do item sem trocar de tela.</p>
                        </div>
                    </div>

                    <div class="pdv-tabs-wrap">
                            <button type="button"
                               data-url="{{ route('pdv.index', ['type' => 'products']) }}"
                               data-type="products"
                               class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'products' ? 'bg-indigo-600 !text-white' : '' }}">
                                Produtos
                            </button>
                            <button type="button"
                               data-url="{{ route('pdv.index', ['type' => 'fabric_pieces']) }}"
                               data-type="fabric_pieces"
                               class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'fabric_pieces' ? 'bg-indigo-600 !text-white' : '' }}">
                                Tecidos
                            </button>
                            <button type="button"
                               data-url="{{ route('pdv.index', ['type' => 'machines']) }}"
                               data-type="machines"
                               class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'machines' ? 'bg-indigo-600 !text-white' : '' }}">
                                Maquinas
                            </button>
                            <button type="button"
                               data-url="{{ route('pdv.index', ['type' => 'supplies']) }}"
                               data-type="supplies"
                               class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'supplies' ? 'bg-indigo-600 !text-white' : '' }}">
                                Suprimentos
                            </button>
                            <button type="button"
                               data-url="{{ route('pdv.index', ['type' => 'uniforms']) }}"
                               data-type="uniforms"
                               class="pdv-tab-link px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $type == 'uniforms' ? 'bg-indigo-600 !text-white' : '' }}">
                                Uniformes
                            </button>
                    </div>

                    <div class="pdv-filter-field">
                        <label for="product-search" class="pdv-filter-label">Buscar item</label>
                        <div class="pdv-search-field">
                            <div class="pdv-search-field-icon">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   id="product-search"
                                   value="{{ $search ?? '' }}"
                                   placeholder="Buscar produto, tecido, maquina ou suprimento..."
                                   autocomplete="off"
                                   spellcheck="false"
                                   class="pdv-search-field-input">
                        </div>
                        {{-- Dropdown autocomplete exclusivo para a aba de Tecidos --}}
                        <div id="fabric-search-dropdown"
                             class="absolute left-0 right-0 mt-1 z-50 hidden"
                             style="top: 100%;">
                            <div class="pdv-card bg-white rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden max-h-80 overflow-y-auto" id="fabric-search-results">
                                {{-- preenchido por JS --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pdv-catalog-card">
                    <div id="pdv-catalog-content">
                        @include('pdv.partials.catalog')
                    </div>
                </div>
            </div>

            <aside class="hidden lg:block">
                <div class="pdv-sidebar-stack sticky top-6">
                    <div class="pdv-section-card">
                        <div class="pdv-section-head">
                            <span class="pdv-section-icon"><i class="fa-solid fa-user"></i></span>
                            <div>
                                <h3>Cliente da venda</h3>
                                <p>Associe um cliente para historico, relacionamento e recorrencia. Opcional.</p>
                            </div>
                        </div>

                        <div id="selected-client-display" class="pdv-client-display hidden animate-fade-in-down">
                            <div class="pdv-client-card">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="pdv-client-avatar">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm leading-tight truncate" id="selected-client-name"></p>
                                        <p class="text-xs text-indigo-600 dark:text-indigo-400 truncate" id="selected-client-info"></p>
                                    </div>
                                </div>
                                <button onclick="clearSelectedClient()" class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="client-search-container" class="relative">
                            <div class="pdv-filter-field">
                                <label for="search-client" class="pdv-filter-label">Buscar cliente</label>
                                <div class="pdv-search-field pdv-search-field-with-action">
                                    <div class="pdv-search-field-icon">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="search-client" placeholder="Buscar cliente (opcional)..."
                                           onkeydown="if(event.key === 'Enter') window.searchClient()"
                                           autocomplete="off"
                                           spellcheck="false"
                                           class="pdv-search-field-input has-action">
                                    <a href="{{ route('clients.create') }}" target="_blank" class="pdv-search-field-action">
                                        + Novo
                                    </a>
                                </div>
                            </div>
                            <div id="search-results" class="absolute w-full mt-2 pdv-card pdv-search-results rounded-xl z-50 max-h-60 overflow-y-auto hidden"></div>
                            <input type="hidden" id="client_id" name="client_id" value="">
                        </div>
                    </div>

                    <div class="pdv-section-card">
                        <div class="pdv-section-head">
                            <span class="pdv-section-icon"><i class="fa-solid fa-cart-shopping"></i></span>
                            <div class="flex-1 min-w-0">
                                <h3>Carrinho da venda</h3>
                                <p>Itens selecionados com quantidade, preco e desconto individual.</p>
                            </div>
                            <span id="cart-total-items-badge" class="pdv-chip">{{ $cartItemsCount }} itens</span>
                        </div>

                        <div class="pdv-cart-shell" id="cart-items-container">
                            <div id="cart-items" class="pdv-cart-list">
                                @if(empty($cart))
                                    <div class="pdv-empty-state">
                                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-sm">Seu carrinho esta vazio</p>
                                            <p class="text-xs mt-1">Adicione itens do catalogo para iniciar a venda.</p>
                                        </div>
                                    </div>
                                @else
                                    @foreach($cart as $item)
                                        <div class="cart-item pdv-cart-item" data-item-id="{{ $item['id'] }}">
                                            <div class="pdv-cart-item-head">
                                                <div class="min-w-0 flex-1">
                                                    <p class="pdv-cart-item-title">{{ $item['product_title'] }}</p>
                                                    <div class="pdv-cart-item-meta">
                                                        @if(!empty($item['color_name']))
                                                            <span class="pdv-chip">{{ $item['color_name'] }}</span>
                                                        @endif
                                                        @if(!empty($item['size']))
                                                            <span class="pdv-chip">{{ $item['size'] }}</span>
                                                        @endif
                                                        @if(isset($item['sale_type']) && $item['sale_type'] != 'unidade')
                                                            <span class="pdv-chip pdv-chip-accent">{{ $item['sale_type'] == 'kg' ? 'Venda por Kg' : 'Venda por Metro' }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button onclick="removeCartItem('{{ $item['id'] }}')" class="pdv-cart-remove">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="pdv-cart-item-foot">
                                                <div class="pdv-cart-controls">
                                                    <input type="number"
                                                           value="{{ $item['quantity'] }}"
                                                           step="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                                           min="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                                           onchange="updateCartItem('{{ $item['id'] }}', this.value, null)"
                                                           class="w-16 p-0 text-center text-xs bg-transparent dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-0" style="background-color: transparent !important;">
                                                    <span class="text-xs text-gray-400">x</span>
                                                    <input type="number"
                                                           value="{{ number_format($item['unit_price'], 2, '.', '') }}"
                                                           step="0.01" min="0"
                                                           onchange="updateCartItem('{{ $item['id'] }}', null, this.value)"
                                                           class="w-20 p-0 text-right text-xs bg-transparent dark:bg-transparent text-gray-900 dark:text-gray-100 font-medium focus:ring-0" style="background-color: transparent !important;">
                                                </div>
                                                <p class="pdv-cart-price">R$ {{ number_format($item['total_price'], 2, ',', '.') }}</p>
                                            </div>

                                            <div class="pdv-cart-discount-row mt-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                                                <label class="text-xs font-semibold text-gray-500">Desconto</label>
                                                <select id="item-discount-type-{{ $item['id'] }}"
                                                        class="px-2 py-1 text-[11px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500"
                                                        style="background-color: transparent !important;"
                                                        onchange="updateItemDiscount('{{ $item['id'] }}')">
                                                    <option value="fixed" {{ ($item['discount_type'] ?? 'fixed') === 'fixed' ? 'selected' : '' }} class="dark:bg-gray-800">R$</option>
                                                    <option value="percent" {{ ($item['discount_type'] ?? '') === 'percent' ? 'selected' : '' }} class="dark:bg-gray-800">%</option>
                                                </select>
                                                <input type="number"
                                                       id="item-discount-value-{{ $item['id'] }}"
                                                       step="0.01"
                                                       min="0"
                                                       value="{{ $item['discount_value'] ?? 0 }}"
                                                       onchange="updateItemDiscount('{{ $item['id'] }}')"
                                                       class="w-20 px-2 py-1 text-right text-[11px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500"
                                                       style="background-color: transparent !important;">
                                                @if(($item['item_discount'] ?? 0) > 0)
                                                    <span class="pdv-chip pdv-chip-danger">-R$ {{ number_format($item['item_discount'], 2, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pdv-section-card">
                        <div class="pdv-section-head">
                            <span class="pdv-section-icon"><i class="fa-solid fa-credit-card"></i></span>
                            <div>
                                <h3>Resumo e finalizacao</h3>
                                <p>Configure desconto, entrega e observacoes antes de concluir.</p>
                            </div>
                        </div>

                        <div class="pdv-summary-grid">
                            <div class="pdv-summary-field">
                                <label>Desconto</label>
                                <div class="pdv-summary-inline">
                                    <div class="pdv-summary-input-wrap pdv-summary-select-wrap">
                                        <select id="discount-type" class="pdv-summary-select dark:text-gray-100">
                                            <option value="fixed" class="dark:bg-[#10203a] dark:text-gray-100">R$</option>
                                            <option value="percent" class="dark:bg-[#10203a] dark:text-gray-100">%</option>
                                        </select>
                                    </div>
                                    <div class="pdv-summary-input-wrap">
                                        <input type="number" id="discount-input" placeholder="0,00" step="0.01" min="0" inputmode="decimal" class="pdv-summary-input text-right">
                                    </div>
                                </div>
                            </div>

                            <div class="pdv-summary-field">
                                <label>Entrega</label>
                                <div class="pdv-summary-input-wrap">
                                    <span class="pdv-summary-prefix">R$</span>
                                    <input type="number" id="delivery-fee-input" placeholder="0,00" inputmode="decimal" class="pdv-summary-input text-right">
                                </div>
                            </div>

                            <div class="pdv-summary-field">
                                <label>Observacoes</label>
                                <div class="pdv-summary-input-wrap">
                                    <input type="text" id="notes-input" placeholder="Adicionar observacoes..." class="pdv-summary-input pdv-summary-notes">
                                </div>
                            </div>

                            @if(Auth::user()->isAdmin() || Auth::user()->isCaixa())
                            <div class="pdv-summary-field">
                                <label>Vendedor</label>
                                <div class="pdv-summary-input-wrap">
                                    <select id="seller_id" class="pdv-summary-input" style="background: transparent;">
                                        <option value="">Selecionar vendedor...</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="pdv-summary-breakdown">
                            <div class="pdv-summary-row">
                                <span>Subtotal</span>
                                <strong id="cart-subtotal">R$ {{ number_format($cartSubtotal, 2, ',', '.') }}</strong>
                            </div>
                            <div class="pdv-summary-row">
                                <span>Desconto aplicado</span>
                                <strong id="cart-discount-total">R$ 0,00</strong>
                            </div>
                            <div class="pdv-summary-row">
                                <span>Entrega</span>
                                <strong id="cart-delivery-fee">R$ 0,00</strong>
                            </div>
                        </div>

                        <div class="pdv-summary-total">
                            <div>
                                <span>Total a pagar</span>
                                <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">Checkout rapido com cliente opcional.</p>
                            </div>
                            <strong id="cart-total">R$ {{ number_format($cartSubtotal, 2, ',', '.') }}</strong>
                        </div>

                        <div class="space-y-3 mt-5">
                            <button onclick="window.checkout()" id="checkout-btn" class="pdv-primary-action group w-full py-4 rounded-xl font-bold transition-all active:scale-[0.99] flex justify-center items-center gap-3 text-base disabled:cursor-not-allowed" disabled style="color: white !important;">
                                <span style="color: white !important;">Finalizar venda</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: white !important;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </button>

                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="window.checkoutWithoutClient()" id="checkout-without-client-btn" class="pdv-secondary-action w-full py-2.5 rounded-xl font-semibold text-xs transition-all flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Sem cliente
                                </button>
                                <button onclick="window.clearCart()" class="pdv-danger-action w-full py-2.5 text-xs font-semibold rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <div id="mobile-cart-fab" class="lg:hidden fixed bottom-20 right-4 z-40">
            <button onclick="toggleMobileCart()" class="pdv-fab relative w-14 h-14 rounded-full flex items-center justify-center active:scale-95 transition-all">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span id="mobile-cart-count" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center {{ empty($cart) ? 'hidden' : '' }}">
                    {{ count($cart ?? []) }}
                </span>
            </button>
            <div class="pdv-total-preview absolute -left-24 top-1/2 -translate-y-1/2 text-xs px-3 py-1.5 rounded-full font-bold whitespace-nowrap {{ empty($cart) ? 'hidden' : '' }}" id="mobile-cart-total-preview">
                R$ {{ number_format($cartSubtotal, 2, ',', '.') }}
            </div>
        </div>

        <div id="mobile-cart-drawer" class="lg:hidden fixed inset-0 z-50 hidden">
            <div onclick="toggleMobileCart()" class="pdv-overlay absolute inset-0"></div>

            <div class="absolute bottom-0 left-0 right-0 pdv-card rounded-t-[28px] max-h-[88vh] overflow-hidden transform transition-transform duration-300 translate-y-full" id="mobile-cart-content">
                <div class="flex justify-center pt-3">
                    <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                </div>

                <div class="pdv-mobile-drawer-head">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Carrinho</p>
                            <p class="pdv-mobile-total text-gray-900 dark:text-white" id="mobile-cart-total">R$ {{ number_format($cartSubtotal, 2, ',', '.') }}</p>
                        </div>
                        <button onclick="toggleMobileCart()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="max-h-[42vh] overflow-y-auto p-4" id="mobile-cart-items">
                    @if(empty($cart))
                        <div class="pdv-empty-state min-h-[180px]">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-sm">Carrinho vazio</p>
                                <p class="text-xs mt-1">Adicione itens para visualizar o resumo.</p>
                            </div>
                        </div>
                    @else
                        <div class="pdv-cart-list">
                            @foreach($cart as $item)
                                <div class="pdv-cart-item">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="pdv-cart-item-title">{{ $item['product_title'] }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item['quantity'] }} x R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</p>
                                        </div>
                                        <p class="pdv-cart-price">R$ {{ number_format($item['total_price'], 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t pdv-panel-divider pdv-muted-surface space-y-3">
                    <button onclick="window.checkout(); toggleMobileCart();" class="pdv-primary-action w-full py-3 rounded-xl font-bold text-sm transition-colors">
                        Finalizar venda
                    </button>
                    <button onclick="window.clearCart(); toggleMobileCart();" class="pdv-danger-action w-full py-2.5 text-xs rounded-xl">
                        Limpar carrinho
                    </button>
                </div>
            </div>
        </div>
    </section>
</div> <!-- End Min-H-Screen -->

<script>
// Fabric group modal toggle
window.openFabricGroupModal = async function(fabricId, fabricName) {
    const modal = document.getElementById('fabric-group-modal');
    const title = document.getElementById('fabric-group-modal-title');
    const list = document.getElementById('fabric-pieces-list');
    
    title.textContent = `Selecionar Cor: ${fabricName}`;
    list.innerHTML = `
        <div class="p-12 text-center text-gray-500">
            <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-3 text-indigo-500"></i>
            <p>Buscando peças para ${fabricName}...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    try {
        const response = await fetch(`/pdv/fabric-pieces/${fabricId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const pieces = await response.json();
        
        if (!pieces || pieces.length === 0) {
            list.innerHTML = `
                <div class="p-12 text-center text-gray-500">
                    <i class="fa-solid fa-exclamation-triangle text-2xl mb-3 text-orange-400"></i>
                    <p>Nenhuma peça disponível para este tecido no momento.</p>
                </div>
            `;
            return;
        }
        
        list.innerHTML = pieces.map(piece => {
            // Register item in pageItems so openAddProductModal can find it
            if (window.pageItems && !window.pageItems.find(p => p.id == piece.id && p.type === 'fabric_piece')) {
                window.pageItems.push(piece);
            }

            const colorSwatch = piece.color_hex
                ? `<span class="w-8 h-8 rounded-full border border-gray-200 shadow-sm" style="background:${piece.color_hex}"></span>`
                : `<span class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center"><i class="fa-solid fa-palette text-xs text-gray-400"></i></span>`;
            
            const unitLabel = piece.control_unit === 'metros' ? 'm' : 'kg';
            
            return `
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors flex items-center justify-between gap-4" 
                     onclick="closeFabricGroupModal(); openAddProductModal(${piece.id}, 'fabric_piece')">
                    <div class="flex items-center gap-4">
                        ${colorSwatch}
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">${piece.color_name}</p>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                                <span class="pdv-modal-badge !bg-gray-100 !text-gray-600 !border-gray-200">Ref: ${piece.reference || 'N/A'}</span>
                                <span>Estoque: <strong>${piece.available_label}</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-extrabold text-indigo-600 dark:text-indigo-400">R$ ${parseFloat(piece.price).toLocaleString('pt-BR', {minimumFractionDigits:2,maximumFractionDigits:2})}/${unitLabel}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mt-1">Clique para Adicionar</p>
                    </div>
                </div>
            `;
        }).join('');
        
    } catch (error) {
        console.error('Erro ao buscar peças:', error);
        list.innerHTML = `
            <div class="p-12 text-center text-red-500">
                <i class="fa-solid fa-circle-xmark text-2xl mb-3"></i>
                <p>Erro ao carregar peças. Tente novamente.</p>
            </div>
        `;
    }
};

window.closeFabricGroupModal = function() {
    document.getElementById('fabric-group-modal').classList.add('hidden');
};

// Mobile cart toggle
function toggleMobileCart() {
    const drawer = document.getElementById('mobile-cart-drawer');
    const content = document.getElementById('mobile-cart-content');
    
    if (drawer.classList.contains('hidden')) {
        drawer.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('translate-y-full');
        }, 10);
    } else {
        content.classList.add('translate-y-full');
        setTimeout(() => {
            drawer.classList.add('hidden');
        }, 300);
    }
}
</script>

<!-- Modal Adicionar Produto -->
<div id="add-product-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="pdv-card bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center pdv-card bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title-dynamic">Adicionar Produto</h3>
            <button onclick="closeAddProductModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="product-modal-content" class="p-6 max-h-[calc(80vh-4rem)] overflow-y-auto">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

<!-- Modal Selecionar Peça de Tecido -->
<div id="fabric-group-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="pdv-card bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center pdv-card bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="fabric-group-modal-title">Selecionar Peça</h3>
            <button onclick="closeFabricGroupModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="fabric-group-modal-content" class="p-0 max-h-[calc(80vh-4rem)] overflow-y-auto">
            <div id="fabric-pieces-list" class="divide-y divide-gray-100 dark:divide-gray-800">
                <!-- Preenchido via JS -->
                <div class="p-12 text-center text-gray-500">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-3"></i>
                    <p>Carregando peças disponíveis...</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Confirmação para Limpar Carrinho -->
<div id="clear-cart-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="if(event.target === this) closeClearCartModal()">
    <div class="pdv-card bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Limpar Carrinho</h3>
            <button onclick="closeClearCartModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700 dark:text-gray-300">
                Deseja realmente limpar o carrinho? Esta ação não pode ser desfeita.
            </p>
        </div>
        
        <div class="flex gap-3 justify-end">
            <button onclick="closeClearCartModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button onclick="confirmClearCart()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Limpar Carrinho
            </button>
        </div>
    </div>
</div>

<!-- Modal de Personalização SUB.LOCAL -->
<div id="sublocal-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeSublocalModal()">
    <div class="pdv-card bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-800" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between sticky top-0 pdv-card bg-white z-10">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Adicionar Personalização SUB.LOCAL</h3>
            <button type="button" onclick="closeSublocalModal()" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Localização -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Localização *</label>
                <select id="sublocal-modal-location" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tamanho -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tamanho *</label>
                <select id="sublocal-modal-size" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                </select>
            </div>

            <!-- Quantidade -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantidade *</label>
                <input type="number" id="sublocal-modal-quantity" min="1" value="1"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Quantidade de peças para esta aplicação</p>
            </div>

            <!-- Preço Calculado -->
            <div id="sublocal-modal-price-display" class="hidden">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Preço por Aplicação:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400" id="sublocal-modal-unit-price">R$ 0,00</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-600 dark:text-slate-400">Total desta Aplicação:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="sublocal-modal-total-price">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="sublocal-modal-unit-price-value" value="0">
            <input type="hidden" id="sublocal-modal-final-price-value" value="0">

            <!-- Botões -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-slate-800">
                <button type="button" onclick="closeSublocalModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmSublocalPersonalization()" 
                        class="flex-1 px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors font-semibold">
                    Adicionar
                </button>
            </div>
        </div>
</div>
</div>

@php
    // $jsItems agora vem do Controller
@endphp

@push('scripts')
<!-- Modal Formas de Pagamento (OUTSIDE main content) -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center" style="display: none;">
    <div class="pdv-card bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Formas de Pagamento</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Total da Venda: <span class="font-semibold text-lg text-gray-900 dark:text-gray-100" id="payment-total">R$ 0,00</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Adicione uma ou mais formas de pagamento para finalizar a venda
            </p>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar Forma de Pagamento:</label>
            <div class="flex flex-wrap gap-2">
                <select id="new-payment-method" class="flex-1 min-w-[150px] px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg pdv-card bg-white text-gray-900 dark:text-gray-100">
                    <option value="">Selecione...</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="transferencia">Transferência</option>
                    <option value="boleto">Boleto</option>
                </select>
                <input type="number" 
                       id="new-payment-amount" 
                       step="0.01"
                       min="0.01"
                       placeholder="Valor"
                       class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg pdv-card bg-white text-gray-900 dark:text-gray-100">
                <button type="button" onclick="addPaymentMethod()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" style="color: white !important;">
                    Adicionar
                </button>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comprovante de Pagamento (Opcional):</label>
            <div class="relative">
                <input type="file" id="pdv-payment-receipt" accept="image/*,.pdf" 
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                       onchange="document.getElementById('pdv-receipt-filename').textContent = this.files[0] ? this.files[0].name : 'Clique para anexar comprovante'">
                <div class="w-full px-4 py-2.5 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 text-sm flex items-center gap-2 overflow-hidden">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-5-8l-5-5m0 0l-5 5m5-5v12"/></svg>
                    <span class="truncate" id="pdv-receipt-filename">Clique para anexar comprovante</span>
                </div>
            </div>
            <p class="mt-1 text-[10px] text-gray-500">O comprovante será vinculado ao pagamento atual ou ao primeiro método adicionado.</p>
        </div>
        
        <div id="payment-methods-list" class="space-y-2 mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Pago:</span>
                <span class="text-lg font-semibold text-green-600 dark:text-green-400" id="payment-total-paid">R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Restante:</span>
                <span class="text-lg font-semibold" id="payment-remaining">R$ 0,00</span>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closePaymentModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button onclick="confirmPayment()" 
                    id="confirm-payment-btn"
                    class="flex-1 px-4 py-2 bg-gray-900 hover:bg-black text-white rounded-lg transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled
                    style="color: white !important;">
                <span style="color: white !important;">Finalizar Venda</span>
            </button>
        </div>
    </div>
</div>
@endpush

<script>
// CRÍTICO: Definir funções no window IMEDIATAMENTE, antes de qualquer outro código
// Isso garante que as funções estejam disponíveis quando a página é carregada via AJAX
// e os elementos com onclick/onchange tentam chamá-las

// Função stub para openAddProductModal
// Queue calls when stub is executed, then retry when real function loads
window._pendingModalCalls = [];
window.openAddProductModal = window.openAddProductModal || function(itemId, type = 'product') {
    // Queue the call
    window._pendingModalCalls.push({itemId, type});
    // Retry after short delay (allows real function to load)
    setTimeout(() => {
        if (window._pendingModalCalls.length > 0 && typeof window._openAddProductModalReal === 'function') {
            const call = window._pendingModalCalls.shift();
            window._openAddProductModalReal(call.itemId, call.type);
        }
    }, 50);
};

// Função stub para checkStockForSizes
window.checkStockForSizes = window.checkStockForSizes || async function() {
    // Função stub silenciosa - apenas retorna sem fazer nada
    return;
};

// Função stub para updateTotalQuantity
window.updateTotalQuantity = window.updateTotalQuantity || function() {
    // Função stub silenciosa
    return;
};

// Função stub para calculateSizeSurcharges
window.calculateSizeSurcharges = window.calculateSizeSurcharges || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmAddProduct
window.confirmAddProduct = window.confirmAddProduct || async function() {
    console.warn('confirmAddProduct ainda não foi totalmente inicializada. Aguarde...');
    return false;
};

// Função stub para closeAddProductModal
window.closeAddProductModal = window.closeAddProductModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearSelectedClient
window.clearSelectedClient = window.clearSelectedClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para searchClient
window.searchClient = window.searchClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearCart
window.clearCart = window.clearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para removeCartItem
window.removeCartItem = window.removeCartItem || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkout
window.checkout = window.checkout || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkoutWithoutClient
window.checkoutWithoutClient = window.checkoutWithoutClient || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeClearCartModal
window.closeClearCartModal = window.closeClearCartModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmClearCart
window.confirmClearCart = window.confirmClearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeSublocalModal
window.closeSublocalModal = window.closeSublocalModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmSublocalPersonalization
window.confirmSublocalPersonalization = window.confirmSublocalPersonalization || function() {
    // Função stub silenciosa
    return;
};

(function() {
    // Evitar redeclaração ao carregar via AJAX
    if (typeof window.productsData !== 'undefined') {
        return; // Já foi declarado, não redeclarar
    }
    
    // Dados dos produtos
    // Dados dos produtos
    // Dados dos produtos
    window.pageItems = @json($jsItems);
    window.allItemsData = window.pageItems; // Compatibilidade
    window.locationsData = @json($locations);
    window.fabricsData = @json($fabrics);
    window.colorsData = @json($colors);
    window.currentStoreId = {{ $currentStoreId ?? 'null' }};
    window.sizesList = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
    
    // Função para atualizar itens via AJAX
    window.updatePageItems = function(newItems) {
        window.pageItems = newItems;
        window.allItemsData = window.pageItems;
    };
})();

// Aliases para compatibilidade (usar window.* para evitar redeclaração)
const currentStoreId = window.currentStoreId;
const sizesList = window.sizesList;

let currentProductId = null;
let currentProductType = 'product';

function getFabricPieceControlUnit(product) {
    return product?.control_unit === 'metros' ? 'metros' : 'kg';
}

function getFabricPieceUnitLabel(product) {
    return getFabricPieceControlUnit(product) === 'metros' ? 'Metro' : 'Kg';
}

function getFabricPieceUnitSuffix(product) {
    return getFabricPieceControlUnit(product) === 'metros' ? 'm' : 'kg';
}

function getFabricPieceStep(product) {
    return getFabricPieceControlUnit(product) === 'metros' ? '0.01' : '0.001';
}

function formatFabricPieceQuantity(value, product) {
    const decimals = getFabricPieceControlUnit(product) === 'metros' ? 2 : 3;
    return Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function normalizeConfiguredColors(rawColors) {
    if (!Array.isArray(rawColors)) {
        return [];
    }

    return rawColors
        .map((item) => {
            const name = String(item?.name ?? '').trim();
            if (!name) {
                return null;
            }

            const matchedColor = (window.colorsData || []).find((color) =>
                String(color.name ?? '').trim().toLowerCase() === name.toLowerCase()
            );

            return {
                id: matchedColor?.id ?? null,
                name,
                hex: item?.hex ?? item?.color_hex ?? matchedColor?.color_hex ?? '#666666',
                available: item?.available ?? null,
            };
        })
        .filter((item) => item && item.id);
}

function getFallbackVariantColors(product) {
    const configuredColors = normalizeConfiguredColors(product?.available_colors ?? []);
    return configuredColors;
}

function renderVariantColorOptions(colors, selectedColorId = null) {
    const select = document.getElementById('modal-color-select');
    const swatches = document.getElementById('modal-color-swatches');

    if (!select) {
        return;
    }

    const normalizedColors = (colors || [])
        .filter((color) => color && color.id)
        .sort((left, right) => String(left.name || '').localeCompare(String(right.name || ''), 'pt-BR'));

    window.currentVariantColors = normalizedColors;

    if (normalizedColors.length === 0) {
        select.innerHTML = '<option value="">Nenhuma cor em estoque</option>';
        select.disabled = true;
        if (swatches) {
            swatches.className = 'pdv-modal-swatches';
            swatches.innerHTML = '<p class="pdv-modal-stock-empty">Nenhuma cor disponível para este corte no estoque.</p>';
        }
        return;
    }

    select.disabled = false;
    select.innerHTML = `
        <option value="">Selecione a cor...</option>
        ${normalizedColors.map((color) => `
            <option value="${color.id}" ${String(selectedColorId ?? '') === String(color.id) ? 'selected' : ''}>
                ${color.name}${color.available !== null ? ` (${color.available})` : ''}
            </option>
        `).join('')}
    `;

    if (swatches) {
        swatches.className = 'pdv-modal-swatches';
        swatches.innerHTML = normalizedColors.map((color) => {
            const isSelected = String(selectedColorId ?? '') === String(color.id);
            return `
                <button type="button"
                        onclick="selectModalColor('${color.id}')"
                        class="pdv-modal-swatch ${isSelected ? 'is-selected' : ''}">
                    <span class="pdv-modal-swatch-dot" style="background-color: ${color.hex || '#666666'}"></span>
                    <span>${color.name}</span>
                    ${color.available !== null ? `<span class="pdv-modal-swatch-stock">${color.available}</span>` : ''}
                </button>
            `;
        }).join('');
    }
}

window.selectModalColor = function selectModalColor(colorId) {
    const select = document.getElementById('modal-color-select');
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;

    if (!select) {
        return;
    }

    select.value = String(colorId);
    renderVariantColorOptions(window.currentVariantColors || [], colorId);

    if (cutTypeId) {
        loadStockByCutType(cutTypeId);
    }

    if (typeof window.checkStockForSizes === 'function') {
        window.checkStockForSizes();
    }
};

async function loadColorOptionsForCutType(cutTypeId, product, selectedColorId = null) {
    const select = document.getElementById('modal-color-select');

    if (!select || !cutTypeId) {
        return;
    }

    select.disabled = true;
    select.innerHTML = '<option value="">Carregando cores...</option>';

    try {
        const params = new URLSearchParams({ cut_type_id: cutTypeId });
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        const data = await response.json();

        const colorsFromStock = Array.isArray(data?.available_colors)
            ? data.available_colors.map((color) => ({
                id: color.id,
                name: color.name,
                hex: color.hex || color.color_hex || '#666666',
                available: color.available ?? null,
            }))
            : [];

        const availableColors = colorsFromStock.length > 0 ? colorsFromStock : getFallbackVariantColors(product);

        let nextSelectedColorId = selectedColorId;
        if (!nextSelectedColorId && availableColors.length === 1) {
            nextSelectedColorId = availableColors[0].id;
        }

        renderVariantColorOptions(availableColors, nextSelectedColorId);

        if (nextSelectedColorId) {
            select.value = String(nextSelectedColorId);
        }

        await loadStockByCutType(cutTypeId);

        if (select.value && typeof window.checkStockForSizes === 'function') {
            await window.checkStockForSizes();
        }
    } catch (error) {
        console.error('Erro ao carregar cores disponíveis:', error);
        renderVariantColorOptions(getFallbackVariantColors(product), selectedColorId);
    }
}

// Preencher quantidade com o total da peça (peça fechada)
window.sellClosedPiece = function() {
    const product = window.pageItems.find(p => p.id == currentProductId && p.type == currentProductType);
    if (!product || currentProductType !== 'fabric_piece') return;

    const maxQty = parseFloat(product.available_quantity) || 0;
    const qtyInput = document.getElementById('modal-quantity');
    if (qtyInput) {
        qtyInput.value = maxQty.toFixed(product.control_unit === 'metros' ? 2 : 3);
        calculateFabricPiecePrice();
    }
};

// Calcular preço de peça de tecido respeitando a unidade de controle
window.calculateFabricPiecePrice = function() {
    const product = window.pageItems.find(p => p.id == currentProductId && p.type == currentProductType);
    if (!product || currentProductType !== 'fabric_piece') return;
    
    const quantityInput = document.getElementById('modal-quantity');
    const priceInput = document.getElementById('modal-unit-price');
    const totalPreview = document.getElementById('fabric-piece-total-preview');
    
    if (quantityInput && priceInput) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const pricePerUnit = parseFloat(priceInput.value) || parseFloat(product.price_per_unit || product.price || 0);
        const maxQuantity = parseFloat(product.available_quantity) || 0;
        
        // Validar que não excede o máximo disponível
        if (maxQuantity > 0 && quantity > maxQuantity) {
            quantityInput.value = maxQuantity;
            alert(`Quantidade máxima disponível: ${formatFabricPieceQuantity(maxQuantity, product)} ${getFabricPieceUnitSuffix(product)}`);
        }
        
        const totalPrice = (parseFloat(quantityInput.value) || 0) * pricePerUnit;

        if (totalPreview) {
            totalPreview.textContent = `R$ ${totalPrice.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
    }
};

function upgradeModalControl(control, options = {}) {
    if (!control || control.type === 'hidden') {
        return null;
    }

    const {
        isSelect = false,
        prefixText = '',
        disabled = false,
        inputClass = '',
    } = options;
    const tagName = control.tagName.toLowerCase();
    const isTextarea = tagName === 'textarea';

    let wrapper = control.parentElement;
    if (!wrapper || !wrapper.classList.contains('pdv-modal-input-wrap')) {
        wrapper = document.createElement('div');
        wrapper.className = `pdv-modal-input-wrap${isSelect ? ' pdv-modal-select-wrap' : ''}`;
        control.parentNode.insertBefore(wrapper, control);
        wrapper.appendChild(control);
    } else {
        wrapper.className = `pdv-modal-input-wrap${isSelect ? ' pdv-modal-select-wrap' : ''}`;
    }

    let prefix = wrapper.querySelector('.pdv-modal-prefix');
    if (prefixText) {
        if (!prefix) {
            prefix = wrapper.querySelector('span') || document.createElement('span');
            if (!prefix.parentElement) {
                wrapper.insertBefore(prefix, control);
            }
        }
        prefix.className = 'pdv-modal-prefix';
        prefix.textContent = prefixText;
    } else if (prefix) {
        prefix.remove();
    }

    wrapper.style.setProperty('background', 'var(--pdv-input-bg)', 'important');
    wrapper.style.setProperty('background-color', 'var(--pdv-input-bg)', 'important');
    wrapper.style.setProperty('border-color', 'var(--pdv-input-border)', 'important');
    wrapper.style.setProperty('box-shadow', 'none', 'important');

    control.className = `${isSelect ? 'pdv-modal-select' : (isTextarea ? 'pdv-modal-textarea' : 'pdv-modal-input')}${prefixText ? ' with-prefix' : ''}${inputClass ? ` ${inputClass}` : ''}`;
    control.style.setProperty('background', 'var(--pdv-input-bg)', 'important');
    control.style.setProperty('background-color', 'var(--pdv-input-bg)', 'important');
    control.style.setProperty('border', '0', 'important');
    control.style.setProperty('border-color', 'transparent', 'important');
    control.style.setProperty('color', 'var(--pdv-input-text)', 'important');
    control.style.setProperty('caret-color', 'var(--pdv-input-text)', 'important');
    control.style.setProperty('-webkit-text-fill-color', 'var(--pdv-input-text)', 'important');
    control.style.setProperty('-webkit-box-shadow', '0 0 0 1000px var(--pdv-input-bg) inset', 'important');
    control.style.setProperty('box-shadow', 'none', 'important');
    if (isSelect) {
        control.style.setProperty('background-image', 'none', 'important');
    }

    if (disabled || control.disabled || control.readOnly) {
        wrapper.classList.add('is-disabled');
    }

    return wrapper;
}

function upgradeModalField(control, options = {}) {
    if (!control) {
        return;
    }

    const field = control.closest('.mb-4, .mb-5');
    if (field) {
        field.classList.add('pdv-modal-field');
        const label = field.querySelector('label');
        if (label) {
            label.className = 'pdv-modal-label';
        }
        const helperText = field.querySelector('p');
        if (helperText) {
            helperText.classList.add('pdv-modal-helper');
        }
    }

    upgradeModalControl(control, options);
}

function enhanceAddProductModalUI() {
    const content = document.getElementById('product-modal-content');
    const root = content?.firstElementChild;

    if (!root) {
        return;
    }

    root.classList.add('pdv-modal-stack');

    const headerCard = root.firstElementChild;
    if (headerCard) {
        headerCard.className = 'pdv-modal-card pdv-modal-header-card';
        const label = headerCard.querySelector('span');
        if (label) {
            label.className = 'pdv-modal-kpi-label';
        }
        const price = headerCard.querySelector('.text-2xl');
        if (price) {
            price.className = 'pdv-modal-kpi-value';
        }
        const meta = headerCard.querySelector('.text-xs.text-gray-500');
        if (meta) {
            meta.className = 'pdv-modal-kpi-meta';
        }
        const badge = headerCard.querySelector('.bg-green-100');
        if (badge) {
            badge.className = 'pdv-modal-badge';
        }
    }

    upgradeModalField(document.getElementById('modal-quantity'), {
        inputClass: 'text-right',
        disabled: document.getElementById('modal-quantity')?.disabled || document.getElementById('modal-quantity')?.readOnly,
    });

    upgradeModalField(document.getElementById('modal-unit-price'), {
        prefixText: 'R$',
        inputClass: 'text-right',
    });

    upgradeModalField(document.getElementById('modal-color-select'), {
        isSelect: true,
    });

    const sizeHeader = document.getElementById('total-quantity-display')?.closest('.flex.items-center.justify-between');
    if (sizeHeader) {
        sizeHeader.className = 'pdv-modal-card pdv-modal-section-head';
        const title = sizeHeader.querySelector('label');
        if (title) {
            title.className = 'pdv-modal-section-title';
        }
        sizeHeader.querySelectorAll('.text-gray-500').forEach((metric) => {
            metric.className = 'pdv-modal-mini-kpi';
        });
    }

    document.querySelectorAll('[id^="modal-size-"]').forEach((input) => {
        input.className = 'pdv-modal-size-input';
        input.style.setProperty('background', 'var(--pdv-input-bg)', 'important');
        input.style.setProperty('background-color', 'var(--pdv-input-bg)', 'important');
        input.style.setProperty('border-color', 'var(--pdv-input-border)', 'important');
        input.style.setProperty('color', 'var(--pdv-input-text)', 'important');
        input.style.setProperty('caret-color', 'var(--pdv-input-text)', 'important');
        input.style.setProperty('-webkit-text-fill-color', 'var(--pdv-input-text)', 'important');
        input.style.setProperty('-webkit-box-shadow', '0 0 0 1000px var(--pdv-input-bg) inset', 'important');
        input.style.setProperty('box-shadow', 'none', 'important');
        const card = input.closest('.relative.group');
        if (card) {
            card.className = 'pdv-modal-size-card';
        }
    });

    const sizeGrids = Array.from(root.querySelectorAll('.grid.grid-cols-5'));
    sizeGrids.forEach((grid) => {
        grid.className = 'pdv-modal-size-grid';
    });

    const stockList = document.getElementById('stock-by-size-list');
    if (stockList) {
        stockList.className = 'pdv-modal-stock-list';
        const emptyBox = stockList.querySelector('.text-sm.text-gray-500');
        if (emptyBox) {
            emptyBox.className = 'pdv-modal-stock-empty';
        }
    }
}

// Abrir modal de adicionar produto
// IMPORTANTE: Definir no window imediatamente para estar disponível quando carregado via AJAX
window.openAddProductModal = function openAddProductModal(itemId, type = 'product') {
    // Register this as the real function for the stub to use
    window._openAddProductModalReal = window.openAddProductModal;
    
    currentProductId = itemId;
    currentProductType = type;
    
    // Encontrar o item na lista genérica pageItems
    const product = window.pageItems.find(p => p.id == itemId && p.type == type);
    
    if (!product) {
        console.error('Item não encontrado:', itemId, type);
        return;
    }
    
    const modal = document.getElementById('add-product-modal');
    const content = document.getElementById('product-modal-content');
    
    // Flags de Tipo
    const isProduct = type === 'product';
    const isProductOption = type === 'product_option';
    const isFabricPiece = type === 'fabric_piece';
    const isStockItem = !isProduct && !isProductOption; // fabric_piece, machine, supply, uniform
    const variantCutTypeId = isProductOption ? product.id : (product.cut_type_id || null);
    
    // Configurações de Quantidade
    let quantityLabel = 'Quantidade';
    let quantityStep = '1';
    let quantityMin = '1';
    let quantityValue = '1';
    let isQuantityReadonly = false;
    
    if (isProduct && (product.sale_type === 'kg' || product.sale_type === 'metro')) {
        quantityLabel = `Quantidade (${product.sale_type === 'kg' ? 'Kg' : 'Metros'})`;
        quantityStep = '0.01';
        quantityMin = '0.01';
    }
    
    if (isFabricPiece) {
        const availableQuantity = parseFloat(product.available_quantity || 0);
        quantityLabel = `Quantidade (${getFabricPieceUnitLabel(product)})`;
        // CORREÇÃO: Sugerir a peça inteira por padrão para garantir que o peso/metro real seja enviado
        quantityValue = availableQuantity; 
        quantityStep = getFabricPieceStep(product);
        quantityMin = getFabricPieceStep(product);
        isQuantityReadonly = false;
    }
    
    // Configurações de Exibição
    // Mostrar Grade (Tamanhos) APENAS para ProductOption 
    // (Produtos normais usam input simples ou grade SE tiver category - mas vamos simplificar: Produtos normais -> Input simples. Options -> Grade)
    // A lógica original mostrava tamanhos para produtos também se não fosse tecido e nao fosse quick.
    // Vamos manter compatibilidade:
    const isFabric = isProduct && (product.sale_type === 'kg' || product.sale_type === 'metro');
    const shouldShowStockFields = Boolean(variantCutTypeId) && !isStockItem && !isFabric;

    // HTML SubLocal
    let sublocalHtml = '';
    if (isProductOption && product.allows_sublocal) {
        sublocalHtml = `
            <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personalizações SUB.LOCAL:</label>
                    <button type="button" onclick="openSublocalModal()" class="px-3 py-1 text-xs bg-gray-900 text-white rounded hover:bg-black transition-colors">
                        + Adicionar
                    </button>
                </div>
                <div id="sublocal-personalizations-list" class="space-y-3"></div>
            </div>
        `;
    }
    
    // Input Unitário HTML
    // Mostrado para TODOS exceto ProductOption (que tem preço fixo por tamanho/tipo)
    const showUnitPrice = !isProductOption;
    
    // Atualizar título do modal dinamicamente
    const modalTitle = document.getElementById('modal-title-dynamic');
    if(modalTitle) modalTitle.textContent = product.title;

    // HTML Principal
    content.innerHTML = `
        <div>
            <!-- Header Info (Price & Type) -->
            <div class="mb-5 flex items-center justify-between pdv-card bg-gray-50/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col">
                    ${isFabricPiece ? `
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Preço por ${getFabricPieceUnitLabel(product)}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                R$ ${parseFloat(product.price_per_unit || product.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Saldo disponível: <span class="font-semibold text-gray-700 dark:text-gray-300">${formatFabricPieceQuantity(product.available_quantity || 0, product)} ${getFabricPieceUnitSuffix(product)}</span>
                            | Valor estimado: <span class="font-semibold text-gray-700 dark:text-gray-300">R$ ${parseFloat(product.sale_price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </span>
                    ` : `
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Preço Unitário</span>
                        <div class="flex items-center gap-2">
                             <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                R$ ${parseFloat(product.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </span>
                            ${isProduct && product.sale_type !== 'unidade' ? `<span class="text-xs font-medium text-gray-400">/ ${product.sale_type === 'kg' ? 'Kg' : 'Mt'}</span>` : ''}
                        </div>
                    `}
                </div>
                 ${isFabricPiece ? `<span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">Venda por ${getFabricPieceUnitLabel(product)} (máx: ${formatFabricPieceQuantity(product.available_quantity || 0, product)} ${getFabricPieceUnitSuffix(product)})</span>` : ''}
            </div>
            
            ${!shouldShowStockFields ? `
            ${isFabricPiece ? `
            <!-- Cor da peça -->
            <div class="mb-4 flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50">
                <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-500"
                     style="${product.color_hex ? `background:${product.color_hex}` : 'background:#e5e7eb'}"></div>
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cor</span>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">${product.color_name || '—'}</p>
                </div>
            </div>
            ` : ''}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">${quantityLabel}</label>
                <input type="number" 
                       id="modal-quantity" 
                       step="${quantityStep}"
                       min="${quantityMin}"
                       value="${quantityValue}"
                       ${isFabricPiece ? `max="${product.available_quantity || 999}" oninput="calculateFabricPiecePrice()"` : ''}
                       ${isQuantityReadonly ? 'readonly disabled' : ''}
                       class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg pdv-card bg-gray-50 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500 transition-shadow ${isQuantityReadonly ? 'cursor-not-allowed opacity-75' : ''}">
                ${isFabricPiece ? `
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">Máx: <span class="font-semibold">${formatFabricPieceQuantity(product.available_quantity || 0, product)} ${getFabricPieceUnitSuffix(product)}</span></p>
                    <button type="button"
                            onclick="sellClosedPiece()"
                            class="text-xs font-bold px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Peça Fechada (${formatFabricPieceQuantity(product.available_quantity || 0, product)} ${getFabricPieceUnitSuffix(product)})
                    </button>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            ${showUnitPrice ? `
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    ${isFabricPiece ? `Preço por ${getFabricPieceUnitLabel(product)}` : (isProduct && product.sale_type === 'kg' ? 'Preço por Kg (Venda)' : 'Valor Unitário')} 
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                    <input type="number" 
                           id="modal-unit-price" 
                           required
                           step="0.01"
                           min="0.00"
                           value="${isFabricPiece ? (product.price_per_unit || product.price || 0) : (product.price || 0)}"
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg pdv-card bg-gray-50 text-gray-900 dark:text-gray-100 font-medium focus:ring-1 focus:ring-indigo-500 transition-shadow">
                </div>
                ${isFabricPiece ? `<p class="text-xs text-gray-500 mt-1">Total calculado: <span id="fabric-piece-total-preview" class="font-semibold text-gray-700 dark:text-gray-300">R$ 0,00</span></p>` : ''}
            </div>
            ` : `<input type="hidden" id="modal-unit-price" value="${product.price || 0}">`}
            
            ${shouldShowStockFields ? `
            <!-- Hidden field para o ID do tipo de corte -->
            <input type="hidden" id="modal-cut-type-id" value="${variantCutTypeId}">
            <input type="hidden" id="modal-fabric-id" value="${product.fabric_id || ''}">
            
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Cor <span class="text-red-500">*</span></label>
                <select id="modal-color-select" required
                        class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg pdv-card bg-gray-50 text-gray-700 dark:text-gray-200 focus:ring-1 focus:ring-indigo-500 text-sm transition-shadow">
                    <option value="">Carregando cores...</option>
                </select>
                <div id="modal-color-swatches" class="mt-3 flex flex-wrap gap-2"></div>
            </div>
            
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3 pdv-card bg-gray-50/50 p-2 rounded-lg border border-gray-100 dark:border-gray-700">
                    <label class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wide ml-2">Tamanhos</label>
                    <div class="flex items-center gap-4 text-xs pr-2">
                        <div class="text-gray-500 dark:text-gray-400 pdv-card bg-white px-2 py-1 rounded border border-gray-200 dark:border-gray-600 shadow-sm">
                            Qtd: <span id="total-quantity-display" class="font-bold text-gray-900 dark:text-gray-100 text-sm ml-1">0</span>
                        </div>
                        <div class="text-gray-500 dark:text-gray-400 pdv-card bg-white px-2 py-1 rounded border border-gray-200 dark:border-gray-600 shadow-sm">
                            Total: <span id="total-surcharges-modal" class="font-bold text-gray-900 dark:text-gray-100 text-sm ml-1">R$ 0,00</span>
                        </div>
                    </div>
                </div>
                
                <!-- Grid 1: PP, P, M, G, GG -->
                <div class="grid grid-cols-5 gap-3 mb-4 mt-4">
                    ${['PP','P','M','G','GG'].map(s => `
                    <div class="flex flex-col items-center justify-start h-32">
                        <!-- Label Topo -->
                        <div class="h-6 flex items-end mb-2">
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">${s}</label>
                        </div>
                        
                        <!-- Tooltip da loja (agora no fluxo normal, acima do input) -->
                        <div class="h-6 w-full flex items-end justify-center mb-1">
                            <div id="stock-badge-${s.toLowerCase()}"></div>
                        </div>

                        <!-- Input -->
                        <div class="w-full relative">
                            <input type="number" id="modal-size-${s.toLowerCase()}" min="0" value="" placeholder="0"
                                   onchange="checkStockForSizes(); ${s === 'GG' ? 'calculateSizeSurcharges();' : ''} updateTotalQuantity();" 
                                   class="w-full h-11 border border-gray-200 dark:border-gray-600 rounded-lg text-center text-base font-bold pdv-card bg-white text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-400 focus:border-gray-900 placeholder-gray-300 dark:placeholder-gray-600 transition-all hover:border-gray-400">
                        </div>
                        
                        <!-- Valor Adicional GG -->
                        <div class="h-6 w-full mt-1.5 flex justify-center">
                            ${s === 'GG' ? `<p class="text-[10px] font-bold text-orange-500 text-center w-full" id="surcharge-gg"></p>` : ''}
                        </div>
                        
                        <!-- Tooltip antigo/oculto -->
                        <div id="stock-${s.toLowerCase()}" class="hidden absolute z-50 top-full left-1/2 -translate-x-1/2 mt-1 w-max min-w-[120px] bg-white dark:bg-slate-800 shadow-xl rounded-lg p-3 text-xs border border-gray-100 dark:border-slate-700"></div>
                    </div>`).join('')}
                </div>

                <!-- Grid 2: EXG, G1, G2, G3, Especial -->
                <div class="grid grid-cols-5 gap-3 mb-6">
                     ${['EXG','G1','G2','G3','Especial'].map(s => `
                    <div class="flex flex-col items-center justify-start h-32">
                        <!-- Label Topo -->
                        <div class="h-6 flex items-end mb-2">
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">${s === 'Especial' ? 'ESP' : s}</label>
                        </div>
                        
                        <!-- Tooltip da loja (agora no fluxo normal, acima do input) -->
                        <div class="h-6 w-full flex items-end justify-center mb-1">
                            <div id="stock-badge-${s.toLowerCase()}"></div>
                        </div>

                        <!-- Input -->
                        <div class="w-full relative">
                            <input type="number" id="modal-size-${s.toLowerCase()}" min="0" value="" placeholder="0"
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full h-11 border border-gray-200 dark:border-gray-600 rounded-lg text-center text-base font-bold pdv-card bg-white text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-400 focus:border-gray-900 placeholder-gray-300 dark:placeholder-gray-600 transition-all hover:border-gray-400">
                        </div>
                        
                        <!-- Valores adicionais Plus Size -->
                        <div class="h-6 w-full mt-1.5 flex justify-center">
                            <p class="text-[10px] font-bold text-orange-500 dark:text-orange-400 text-center w-full" id="surcharge-${s.toLowerCase()}"></p>
                        </div>
                    </div>`).join('')}
                </div>
            </div>

            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Estoque por tamanho</label>
                    <span class="text-[11px] text-gray-400">Selecione a cor para priorizar a disponibilidade</span>
                </div>
                <div id="stock-by-size-list" class="space-y-3">
                    <div class="text-sm text-gray-500 dark:text-gray-400 pdv-card bg-gray-50/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-600 p-4">
                        Carregando disponibilidade por tamanho...
                    </div>
                </div>
            </div>
            ` : ''}
            
            ${sublocalHtml}
            
            <div class="pt-4 mt-2 flex gap-3">
                <button onclick="closeAddProductModal()" class="flex-1 py-3 text-sm font-bold pdv-secondary-action rounded-xl transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmAddProduct()" class="flex-1 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Adicionar</span>
                </button>
            </div>
        </div>
    `;

    enhanceAddProductModalUI();
    modal.classList.remove('hidden');

    if (isFabricPiece) {
        setTimeout(() => {
            calculateFabricPiecePrice();
            document.getElementById('modal-unit-price')?.addEventListener('input', calculateFabricPiecePrice);
            document.getElementById('modal-quantity')?.addEventListener('input', calculateFabricPiecePrice);
        }, 50);
    }
    
    if (shouldShowStockFields) {
        setTimeout(() => calculateSizeSurcharges(), 100);
        document.getElementById('modal-quantity')?.addEventListener('input', () => { calculateSizeSurcharges(); checkStockAvailability(); });
        const colorSelect = document.getElementById('modal-color-select');
        if (colorSelect) {
            colorSelect.addEventListener('change', function() {
                renderVariantColorOptions(window.currentVariantColors || [], this.value);
                loadStockByCutType(variantCutTypeId);
                checkStockForSizes();
            });
        }

        loadColorOptionsForCutType(variantCutTypeId, product);
    }
}

// Atualizar total de quantidade
window.updateTotalQuantity = function updateTotalQuantity() {
    const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'];
    let total = 0;
    
    sizes.forEach(size => {
        const input = document.getElementById(`modal-size-${size.toLowerCase()}`);
        if (input) {
            total += parseInt(input.value || 0);
        }
    });
    
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) {
        totalQuantityDisplay.textContent = total;
        totalQuantityDisplay.className = total > 0 ? 'font-semibold text-indigo-600 dark:text-indigo-400' : 'font-semibold text-gray-400';
    }
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) {
        totalItemsDisplay.textContent = total;
    }
}

// Encontrar loja prioritária que tem todos os tamanhos selecionados
function findPriorityStore(selectedSizes, stockBySizeData) {
    if (!stockBySizeData || !stockBySizeData.stock_by_size) {
        return null;
    }
    
    // Mapear lojas e verificar quais têm todos os tamanhos selecionados
    const storeScores = {};
    
    selectedSizes.forEach(size => {
        const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
        if (sizeData && sizeData.stores) {
            sizeData.stores.forEach(store => {
                if (!storeScores[store.store_id]) {
                    storeScores[store.store_id] = {
                        store_id: store.store_id,
                        store_name: store.store_name,
                        hasAllSizes: true,
                        totalAvailable: 0,
                        sizesCount: 0
                    };
                }
                storeScores[store.store_id].totalAvailable += store.available || 0;
                storeScores[store.store_id].sizesCount++;
            });
        }
    });
    
    // Verificar quais lojas têm todos os tamanhos
    const selectedSizesCount = selectedSizes.length;
    let priorityStore = null;
    let maxTotalAvailable = 0;
    
    Object.values(storeScores).forEach(store => {
        if (store.sizesCount === selectedSizesCount && store.totalAvailable > maxTotalAvailable) {
            priorityStore = store;
            maxTotalAvailable = store.totalAvailable;
        }
    });
    
    return priorityStore;
}

// Verificar estoque para cada tamanho com informações por loja
// Verificar estoque para cada tamanho com informações por loja
window.checkStockForSizes = async function checkStockForSizes() {
    const colorSelect = document.getElementById('modal-color-select');
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    
    // Limpar badges se não houver seleção
    if (!colorSelect || !cutTypeId || !colorSelect.value) {
        sizesList.forEach(size => {
            const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
            if (stockBadge) stockBadge.innerHTML = '';
        });
        return;
    }
    
    const colorId = colorSelect.value;
    // const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3']; // Usar global sizesList
    
    // Buscar dados de estoque (cachear se possível ou buscar sempre? buscar sempre é mais seguro no PDV)
    let stockBySizeData = null;
    try {
        const params = new URLSearchParams({ cut_type_id: cutTypeId, color_id: colorId });
        console.log('Buscando estoque:', `/api/stocks/by-cut-type?${params}`);
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        stockBySizeData = await response.json();
        console.log('Resposta do estoque:', stockBySizeData);
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
        return;
    }
    
    if (!stockBySizeData || !stockBySizeData.success || !stockBySizeData.stock_by_size) {
        console.warn('Sem dados de estoque ou erro:', stockBySizeData?.message);
        return;
    }

    // Iterar pelos tamanhos
    sizesList.forEach(size => {
        const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
        if (!stockBadge) return;
        
        const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
        
        if (sizeData && sizeData.available > 0) {
            // Lógica de Prioridade de Loja
            // 1. Tentar encontrar na loja atual
            let targetStore = null;
            let neededTransfer = false;
            
            // Tenta encontrar na currentStoreId
            if (window.currentStoreId) {
                targetStore = sizeData.stores.find(s => s.store_id == window.currentStoreId && s.available > 0);
            }
            
            // Se não tem na loja atual, pega a com maior estoque
            if (!targetStore) {
                // Ordena por quantidade descrescente
                const sortedStores = sizeData.stores.sort((a, b) => b.available - a.available);
                if (sortedStores.length > 0) {
                    targetStore = sortedStores[0];
                    neededTransfer = true; // Indica que vai precisar de transferência (fundo amarelo/laranja)
                }
            }
            
            if (targetStore) {
                // Formatar exibição: "100 - NomeLoja"
                // Se for da loja atual: Verde. Se for de outra: Laranja/Azul.
                const badgeColorClass = neededTransfer 
                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800' 
                    : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 border-green-200 dark:border-green-800';
                
                // Abrevia o nome da loja se for muito longo para caber
                let storeName = targetStore.store_name;
                if (storeName.length > 8) storeName = storeName.substring(0, 8) + '..';
                
                // Agora o tooltip está em um container estático 'h-6' (sem absolute overlap)
                stockBadge.className = `w-max max-w-full px-1.5 py-0.5 rounded text-[9.5px] font-bold shadow-sm whitespace-nowrap flex items-center justify-center gap-1 border border-b-0 rounded-b-none ${badgeColorClass}`;
                stockBadge.innerHTML = `<span>${targetStore.available}</span> <span class="opacity-75 font-medium border-l border-current pl-1 truncate">${storeName}</span>`;
                
                // Tooltip simples (title nativo)
                stockBadge.title = `Loja: ${targetStore.store_name} (${targetStore.available} un.)`;
            } else {
                 // Tem available > 0 no total, mas stores array vazio? (Caso raro de inconsistência)
                 stockBadge.innerHTML = '';
            }
            
        } else {
            // Sem estoque
            // Opcional: Mostrar "0" ou nada. Usuário pediu pra focar no "Se tem".
            // Se não tem, gera solicitação, mas visualmente no grid pode ficar vazio ou traço.
            stockBadge.className = 'text-[9.5px] text-gray-400 font-bold px-1 whitespace-nowrap';
            stockBadge.innerHTML = '0';
        }
    });
} // Fim de checkStockForSizes

// Buscar estoque por tipo de corte
async function loadStockByCutType(cutTypeId) {
    if (!cutTypeId) {
        return;
    }
    
    const stockList = document.getElementById('stock-by-size-list');
    const colorSelect = document.getElementById('modal-color-select');
    if (!stockList) return;
    
    const colorId = colorSelect?.value;
    
    if (!colorId) {
        stockList.innerHTML = `
            <div class="text-center py-6 pdv-card bg-gray-50/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Selecione uma cor para verificar o estoque disponível.
                </p>
            </div>
        `;
        return;
    }
    
    try {
        // Buscar de todas as lojas (não filtrar por loja específica)
        const params = new URLSearchParams({
            cut_type_id: cutTypeId
        });
        
        if (colorId) {
            params.append('color_id', colorId);
        }
        
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        const data = await response.json();
        
        if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
            
            data.stock_by_size.forEach(item => {
                const hasStock = item.available > 0;
                const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'pdv-card bg-gray-50/50 border-gray-200 dark:border-gray-700';
                const textColor = hasStock ? 'text-green-800 dark:text-green-200' : 'text-gray-500 dark:text-gray-400';
                
                html += `
                    <div class="p-4 rounded-lg border-2 ${bgColor} transition-all hover:shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-base font-bold text-gray-900 dark:text-gray-100">Tamanho ${item.size}</span>
                            </div>
                            ${hasStock ? `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Em estoque
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                     Sem estoque
                                </span>
                            `}
                        </div>
                        
                        <div class="mb-2">
                            <div class="text-sm ${textColor}">
                                <span class="font-semibold">${item.available}</span> disponível
                                ${item.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${item.reserved} reservado)</span>` : ''}
                            </div>
                        </div>
                        
                        ${item.stores && item.stores.length > 0 ? `
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Por Loja:</div>
                                <div class="space-y-1.5">
                                    ${(() => {
                                        // Priorizar a loja atual
                                        let myStore = item.stores.find(s => s.store_id == window.currentStoreId);
                                        let displayStores = item.stores;
                                        
                                        if (myStore && myStore.available > 0) {
                                            displayStores = [myStore];
                                        } else {
                                            displayStores = item.stores.filter(s => s.available > 0);
                                            if (displayStores.length === 0 && myStore) {
                                                displayStores = [myStore];
                                            } else if (displayStores.length === 0) {
                                                displayStores = item.stores.slice(0, 1);
                                            }
                                        }
                                        
                                        return displayStores.map(store => `
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600 dark:text-gray-400 font-medium">${store.store_name}:</span>
                                                <span class="font-semibold ${store.available > 0 ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-500'}">
                                                    ${store.available} disp.
                                                    ${store.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${store.reserved} res.)</span>` : ''}
                                                </span>
                                            </div>
                                        `).join('');
                                    })()}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            stockList.innerHTML = html;
        } else {
            stockList.innerHTML = `
                <div class="text-center py-8 pdv-card bg-gray-50/50 rounded-xl border-2 border-dashed border-yellow-300 dark:border-yellow-700/50">
                    <svg class="w-16 h-16 mx-auto mb-4 text-yellow-500 dark:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-base font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                        Nenhum estoque cadastrado
                    </p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-500/80">
                        Para esta cor selecionada
                    </p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
        stockList.innerHTML = `
            <div class="text-sm text-red-600 dark:text-red-400 text-center py-2">
                Erro ao carregar estoque
            </div>
        `;
    }
}

// Verificar disponibilidade de estoque em tempo real
async function checkStockAvailability() {
    if (!currentStoreId) {
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    const stockInfo = document.getElementById('stock-info');
    const stockQuantity = document.getElementById('stock-quantity');
    const stockWarning = document.getElementById('stock-warning');
    const stockSuccess = document.getElementById('stock-success');
    
    // Ocultar mensagens anteriores
    if (stockWarning) stockWarning.classList.add('hidden');
    if (stockSuccess) stockSuccess.classList.add('hidden');
    
    // Verificar se todos os campos estão preenchidos
    if (!fabricId || !colorId || !cutTypeId || !size) {
        if (stockInfo) stockInfo.classList.add('hidden');
        return;
    }
    
    try {
        const params = new URLSearchParams({
            store_id: currentStoreId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId,
            size: size,
            quantity: quantity
        });
        
        const response = await fetch(`/api/stocks/check?${params}`);
        const data = await response.json();
        
        if (data.success && stockInfo) {
            stockInfo.classList.remove('hidden');
            const available = data.available_quantity || 0;
            const hasStock = data.has_stock || false;
            
            if (stockQuantity) {
                stockQuantity.textContent = `${available} unidade(s)`;
            }
            
            if (hasStock) {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-green-600 dark:text-green-400';
                if (stockSuccess) {
                    stockSuccess.classList.remove('hidden');
                    stockSuccess.textContent = ` Estoque suficiente para ${quantity} unidade(s)`;
                }
            } else {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-red-600 dark:text-red-400';
                if (stockWarning) {
                    stockWarning.classList.remove('hidden');
                    stockWarning.innerHTML = ` Estoque insuficiente! Disponível: ${available} unidade(s). <button type="button" onclick="createStockRequest()" class="text-blue-600 dark:text-blue-400 underline ml-1">Solicitar estoque</button>`;
                }
            }
        } else {
            if (stockInfo) stockInfo.classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao verificar estoque:', error);
        if (stockInfo) stockInfo.classList.add('hidden');
    }
}

// Criar solicitação de estoque
async function createStockRequest() {
    if (!currentStoreId) {
        alert('Loja não identificada');
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    if (!fabricId || !colorId || !cutTypeId || !size) {
        alert('Preencha todos os campos de especificação');
        return;
    }
    
    const fabricName = window.fabricsData.find(f => f.id == fabricId)?.name || 'Tecido';
    const colorName = window.colorsData.find(c => c.id == colorId)?.name || 'Cor';
    const cutTypeName = document.getElementById('modal-cut-type')?.value || 'Tipo de Corte';
    
    if (!confirm(`Deseja criar uma solicitação de estoque para:\n\nTecido: ${fabricName}\nCor: ${colorName}\nTipo de Corte: ${cutTypeName}\nTamanho: ${size}\nQuantidade: ${quantity} unidade(s)?`)) {
        return;
    }
    
    try {
        const response = await fetch('/stock-requests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                requesting_store_id: currentStoreId,
                fabric_id: fabricId,
                color_id: colorId,
                cut_type_id: cutTypeId,
                size: size,
                requested_quantity: quantity,
                request_notes: `Solicitação criada automaticamente do PDV - Quantidade necessária: ${quantity}`
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Solicitação de estoque criada com sucesso!');
            // Atualizar informações de estoque
            checkStockAvailability();
        } else {
            alert('Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao criar solicitação:', error);
        alert('Erro ao criar solicitação de estoque');
    }
}

// Calcular acréscimos de tamanhos especiais
// IMPORTANTE: Usar preço unitário, não o total, para determinar a faixa de acréscimo
async function calculateSizeSurcharges() {
    const unitPrice = parseFloat(document.getElementById('modal-unit-price')?.value || 0);
    const quantity = parseFloat(document.getElementById('modal-quantity')?.value || 1);
    // Usar preço unitário para buscar a faixa de acréscimo, não o total
    const priceForSurcharge = unitPrice;
    
    // Verificar quais tamanhos estão disponíveis (GG e EXG só para produtos não-tecido)
    const hasGG = document.getElementById('modal-size-gg') !== null;
    const hasEXG = document.getElementById('modal-size-exg') !== null;
    
    let sizes = ['G1', 'G2', 'G3', 'Especial'];
    if (hasGG) sizes.unshift('GG');
    if (hasEXG) sizes.unshift('EXG');
    
    let totalSurcharges = 0;
    
    for (const size of sizes) {
        const quantityInput = document.getElementById(`modal-size-${size.toLowerCase()}`);
        const surchargeDisplay = document.getElementById(`surcharge-${size.toLowerCase()}`);
        
        if (!quantityInput || !surchargeDisplay) continue;
        
        const qty = parseInt(quantityInput.value || 0);
        
        if (qty > 0 && priceForSurcharge > 0) {
            try {
                // Usar preço unitário para buscar a faixa de acréscimo
                const response = await fetch(`{{ url('/api/size-surcharge') }}/${size}?price=${priceForSurcharge}`);
                const data = await response.json();
                
                if (data.surcharge) {
                    const surchargePerUnit = parseFloat(data.surcharge);
                    const totalSurcharge = surchargePerUnit * qty;
                    totalSurcharges += totalSurcharge;
                    
                    surchargeDisplay.textContent = `R$ ${totalSurcharge.toFixed(2).replace('.', ',')}`;
                    surchargeDisplay.className = 'text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium';
                } else {
                    surchargeDisplay.textContent = 'R$ 0,00';
                    surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                }
            } catch (error) {
                console.error(`Erro ao calcular acréscimo ${size}:`, error);
                surchargeDisplay.textContent = 'R$ 0,00';
                surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            }
        } else {
            surchargeDisplay.textContent = 'R$ 0,00';
            surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
        }
    }
    
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = `R$ ${totalSurcharges.toFixed(2).replace('.', ',')}`;
    }
}

// Variável para controlar personalizações sub.local
let sublocalPersonalizations = [];
let sublocalCounter = 0;
let sublocalSizes = [];

// Carregar tamanhos disponíveis para SUB.LOCAL
async function loadSublocalSizes() {
    try {
        const response = await fetch('/api/personalization-prices/sizes?type=SUB. LOCAL');
        const data = await response.json();
        
        if (data.success && data.sizes) {
            sublocalSizes = data.sizes;
            const sizeSelect = document.getElementById('sublocal-modal-size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            data.sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size.size_name;
                const dimensions = size.size_dimensions || '';
                option.textContent = dimensions ? `${size.size_name} (${dimensions})` : size.size_name;
                sizeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar tamanhos:', error);
    }
}

// Abrir modal de sub.local
function openSublocalModal() {
    // Resetar campos
    document.getElementById('sublocal-modal-location').value = '';
    document.getElementById('sublocal-modal-size').value = '';
    document.getElementById('sublocal-modal-quantity').value = '1';
    document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    
    // Carregar tamanhos
    loadSublocalSizes();
    
    // Mostrar modal
    document.getElementById('sublocal-modal').classList.remove('hidden');
    
    // Adicionar event listeners
    document.getElementById('sublocal-modal-location').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-size').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-quantity').addEventListener('input', calculateSublocalModalPrice);
}

// Fechar modal de sub.local
window.closeSublocalModal = function closeSublocalModal() {
    document.getElementById('sublocal-modal').classList.add('hidden');
}

// Calcular preço no modal de sub.local
async function calculateSublocalModalPrice() {
    const location = document.getElementById('sublocal-modal-location').value;
    const size = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    
    if (!location || !size || quantity < 1) {
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/api/personalization-prices/price?type=SUB. LOCAL&size=${encodeURIComponent(size)}&quantity=${quantity}`);
        const data = await response.json();
        
        if (data.success && data.price) {
            const unitPrice = parseFloat(data.price);
            const totalPrice = unitPrice * quantity;
            
            document.getElementById('sublocal-modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-total-price').textContent = `R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-unit-price-value').value = unitPrice;
            document.getElementById('sublocal-modal-final-price-value').value = totalPrice;
            document.getElementById('sublocal-modal-price-display').classList.remove('hidden');
        } else {
            document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao calcular preço:', error);
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    }
}

// Confirmar e adicionar personalização sub.local
window.confirmSublocalPersonalization = function confirmSublocalPersonalization() {
    const locationId = document.getElementById('sublocal-modal-location').value;
    const locationName = document.getElementById('sublocal-modal-location').selectedOptions[0]?.text || '';
    const sizeName = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    const unitPrice = parseFloat(document.getElementById('sublocal-modal-unit-price-value').value || 0);
    const finalPrice = parseFloat(document.getElementById('sublocal-modal-final-price-value').value || 0);
    
    if (!locationId || !sizeName || quantity < 1 || unitPrice <= 0) {
        showNotification('Preencha todos os campos obrigatórios e verifique o preço', 'error');
        return;
    }
    
    const container = document.getElementById('sublocal-personalizations-list');
    if (!container) return;
    
    const id = sublocalCounter++;
    const personalizationHtml = `
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 pdv-card bg-gray-50" data-sublocal-id="${id}">
            <div class="flex justify-between items-center mb-2">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">${locationName}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tamanho: ${sizeName} | Qtd: ${quantity}</p>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400">Total: R$ ${finalPrice.toFixed(2).replace('.', ',')}</p>
                </div>
                <button type="button" onclick="removeSublocalPersonalization(${id})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', personalizationHtml);
    
    // Adicionar aos dados
    sublocalPersonalizations.push({
        id: id,
        location_id: locationId,
        location_name: locationName,
        size_name: sizeName,
        quantity: quantity,
        unit_price: unitPrice,
        final_price: finalPrice
    });
    
    // Fechar modal
    closeSublocalModal();
}

// Remover personalização sub.local
function removeSublocalPersonalization(id) {
    const element = document.querySelector(`[data-sublocal-id="${id}"]`);
    if (element) {
        element.remove();
    }
    sublocalPersonalizations = sublocalPersonalizations.filter(p => p.id !== id);
}

// Fechar modal
window.closeAddProductModal = function closeAddProductModal() {
    document.getElementById('add-product-modal').classList.add('hidden');
    currentProductId = null;
    currentProductType = 'product';
    
    // Resetar campos de tamanho (apenas os que existem)
    ['pp', 'p', 'm', 'g', 'gg', 'exg', 'g1', 'g2', 'g3'].forEach(size => {
        const input = document.getElementById(`modal-size-${size}`);
        if (input) input.value = 0;
        const stockDiv = document.getElementById(`stock-${size}`);
        if (stockDiv) stockDiv.innerHTML = '';
        const stockBadge = document.getElementById(`stock-badge-${size}`);
        if (stockBadge) {
            stockBadge.innerHTML = '';
            stockBadge.className = '';
        }
        const display = document.getElementById(`surcharge-${size}`);
        if (display) {
            display.textContent = '+ R$ 0,00';
            display.className = 'text-xs font-semibold text-gray-900 dark:text-gray-300 mt-1 text-center';
        }
    });
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = 'R$ 0,00';
    }
    
    // Limpar total de quantidade
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) totalQuantityDisplay.textContent = '0';
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) totalItemsDisplay.textContent = '0';
    
    // Limpar seleção de cor
    const colorSelect = document.getElementById('modal-color-select');
    if (colorSelect) colorSelect.value = '';
    const colorSwatches = document.getElementById('modal-color-swatches');
    if (colorSwatches) colorSwatches.innerHTML = '';
    window.currentVariantColors = [];
    
    // Limpar informações de estoque
    const stockList = document.getElementById('stock-by-size-list');
    if (stockList) {
        stockList.innerHTML = `
            <div class="text-sm text-gray-600 dark:text-gray-400 text-center py-4 pdv-card bg-white rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium">Selecione a cor acima para visualizar o estoque disponível</p>
                <p class="text-xs mt-1">O estoque será exibido por tamanho e por loja</p>
            </div>
        `;
    }
    
    // Limpar personalizações sub.local
    const sublocalContainer = document.getElementById('sublocal-personalizations-list');
    if (sublocalContainer) {
        sublocalContainer.innerHTML = '';
    }
    sublocalPersonalizations = [];
    sublocalCounter = 0;
    
    // Fechar modal de sub.local se estiver aberto
    const sublocalModal = document.getElementById('sublocal-modal');
    if (sublocalModal) {
        sublocalModal.classList.add('hidden');
    }
}

// Confirmar adicionar produto
window.confirmAddProduct = async function confirmAddProduct() {
    if (!currentProductId) return;
    
    const product = window.pageItems.find(p => p.id == currentProductId && p.type == currentProductType);
    
    if (!product) return;
    
    // Para product_option (tipo de corte), usar o preço fixo do produto
    // Para produtos normais, usar o preço do input (se existir)
    const unitPriceInput = document.getElementById('modal-unit-price');
    let unitPrice;
    
    if (currentProductType === 'product_option') {
        // Usar preço fixo do tipo de corte
        unitPrice = parseFloat(product.price || 0);
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Preço do produto não configurado', 'error');
            return;
        }
    } else {
        // Para produtos normais, validar o preço do input
        unitPrice = unitPriceInput ? parseFloat(unitPriceInput.value) : null;
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Informe um preço unitário válido', 'error');
            if (unitPriceInput) {
                unitPriceInput.focus();
                unitPriceInput.classList.add('border-red-500');
            }
            return;
        }
    }
    
    // Coletar cor/tipo para itens com grade de estoque
    const colorSelect = document.getElementById('modal-color-select');
    const selectedColorId = colorSelect?.value || null;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value || null;
    const fabricId = document.getElementById('modal-fabric-id')?.value || null;
    
    const hasVariantSelection = Boolean(cutTypeId);

    if (hasVariantSelection) {
        if (!selectedColorId) {
            showNotification('Selecione a cor', 'error');
            return;
        }
    }
    
    // Verificar se existem inputs de tamanho (se não existirem, é um produto simples/tecido/quick-product)
    const hasSizeInputs = document.getElementById('modal-size-p') !== null;
    
    let totalQuantity = 0;
    let sizeQuantities = {};

    if (hasSizeInputs) {
        // Coletar quantidades de todos os tamanhos
        sizeQuantities = {
            'PP': parseInt(document.getElementById('modal-size-pp')?.value || 0),
            'P': parseInt(document.getElementById('modal-size-p')?.value || 0),
            'M': parseInt(document.getElementById('modal-size-m')?.value || 0),
            'G': parseInt(document.getElementById('modal-size-g')?.value || 0),
            'GG': parseInt(document.getElementById('modal-size-gg')?.value || 0),
            'EXG': parseInt(document.getElementById('modal-size-exg')?.value || 0),
            'G1': parseInt(document.getElementById('modal-size-g1')?.value || 0),
            'G2': parseInt(document.getElementById('modal-size-g2')?.value || 0),
            'G3': parseInt(document.getElementById('modal-size-g3')?.value || 0),
        };
        // Calcular quantidade total dos tamanhos
        totalQuantity = Object.values(sizeQuantities).reduce((sum, qty) => sum + qty, 0);
    } else {
        // Produto simples: pegar do input único
        totalQuantity = parseFloat(document.getElementById('modal-quantity')?.value || 0);
    }
    
    if (totalQuantity <= 0) {
        showNotification(hasSizeInputs ? 'Informe pelo menos uma quantidade para algum tamanho' : 'Informe uma quantidade válida maior que 0', 'error');
        return;
    }
    
    // Coletar personalizações sub.local (já estão no array sublocalPersonalizations)
    const sublocalPersonalizationsToSend = sublocalPersonalizations.map(p => ({
        location_id: p.location_id,
        location_name: p.location_name,
        size_name: p.size_name,
        quantity: p.quantity,
        unit_price: p.unit_price,
        final_price: p.final_price
    }));
    
    // Para itens com grade de tamanhos/estoque, adicionar cada tamanho como item separado
    if (hasSizeInputs && hasVariantSelection) {
        // Adicionar cada tamanho que tiver quantidade > 0 (sequencialmente para evitar problemas de sincronização)
        let itemsAdded = 0;
        let lastError = null;
        
        // Usar for...of com await para garantir que cada item seja adicionado antes do próximo
        const sizes = Object.entries(sizeQuantities).filter(([size, qty]) => qty > 0);
        
        for (const [size, qty] of sizes) {
            try {
                // Para tamanhos especiais (GG, EXG, G1, G2, G3), enviar size_quantities
                // para que o servidor calcule o acréscimo corretamente
                const sizeQuantitiesForSurcharge = {};
                if (['GG', 'EXG', 'G1', 'G2', 'G3'].includes(size)) {
                    sizeQuantitiesForSurcharge[size] = qty;
                }
                
                const result = await addProductToCart(
                    currentProductId, 
                    currentProductType, 
                    null, 
                    unitPrice, 
                    qty, 
                    sizeQuantitiesForSurcharge, // Enviar size_quantities para calcular acréscimo
                    sublocalPersonalizationsToSend,
                    size, // tamanho específico
                    selectedColorId,
                    cutTypeId,
                    fabricId
                );
                if (result && result.success) {
                    itemsAdded++;
                    if (result.stock_request_created) {
                        // Marcar que houve solicitação de estoque (mostrar aviso no final)
                        lastError = { type: 'stock_request' };
                    }
                } else {
                    lastError = result || { type: 'unknown' };
                }
            } catch (error) {
                console.error(`Erro ao adicionar tamanho ${size}:`, error);
                lastError = error;
            }
        }
        
        if (itemsAdded > 0) {
            closeAddProductModal();
            // Buscar carrinho atualizado do servidor para garantir que temos todos os itens
            fetch('{{ route("pdv.cart.get") }}', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart) {
                    updateCartDisplay(data.cart, data.cart_total);
                }
            })
            .catch(err => console.error('Erro ao atualizar carrinho:', err));
            
            // Mostrar notificação apropriada
            if (lastError && lastError.type === 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho. Algumas solicitações de estoque foram criadas.`, 'warning');
            } else if (lastError && lastError.type !== 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s), mas houve erros`, 'warning');
            } else {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho`, 'success');
            }
        } else if (lastError) {
            showNotification('Erro ao adicionar itens ao carrinho', 'error');
        }
    } else {
        // Para produtos normais, usar a lógica antiga
        const quantity = parseFloat(document.getElementById('modal-quantity')?.value || totalQuantity);
        try {
            const result = await addProductToCart(
                currentProductId, 
                currentProductType, 
                null, 
                unitPrice, 
                quantity, 
                sizeQuantities, 
                sublocalPersonalizationsToSend
            );
            closeAddProductModal();
            if (result && result.success) {
                if (result.stock_request_created) {
                    showNotification('Item adicionado ao carrinho. Solicitação de estoque criada automaticamente.', 'warning');
                } else {
                    showNotification('Item adicionado ao carrinho', 'success');
                }
            } else {
                showNotification(result?.message || 'Erro ao adicionar item ao carrinho', 'error');
            }
        } catch (error) {
            console.error('Erro ao adicionar produto:', error);
            showNotification('Erro ao adicionar item ao carrinho', 'error');
        }
    }
    
    // Limpar personalizações após adicionar
    sublocalPersonalizations = [];
    sublocalCounter = 0;
}

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Atualizar total quando desconto ou taxa mudar
document.getElementById('discount-input')?.addEventListener('input', updateTotal);
document.getElementById('discount-type')?.addEventListener('change', updateTotal);
document.getElementById('delivery-fee-input')?.addEventListener('input', updateTotal);

// Busca de produtos
// Busca de produtos - AGORA FEITA VIA BACKEND (PAGINAÇÃO)
// Listener removido pois a busca agora é via formulário GET
/*
document.getElementById('product-search')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const title = card.getAttribute('data-product-title');
        const category = card.getAttribute('data-product-category');
        
        if (title.includes(search) || category.includes(search)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
*/

// Listener removido - não há mais select de cliente, apenas campo de busca
// O botão de checkout sempre está habilitado agora (cliente é opcional)
// Não há mais validação de cliente obrigatório no frontend

// Função para adicionar produto ao carrinho
// Adicionar produto ao carrinho (Frontend)
window.addProductToCart = async function addProductToCart(itemId, type, productTitle, unitPrice, quantity = 1, sizeQuantities = {}, sublocalPersonalizations = [], selectedSize = null, selectedColorId = null, cutTypeId = null, fabricId = null) {
    try {
        const body = {
            quantity: quantity,
            unit_price: unitPrice,
            size_quantities: sizeQuantities,
            item_type: type // Corrigido: era 'type', mas controller espera 'item_type'
        };
        
        if (type === 'product') {
            body.product_id = itemId;
            if (selectedSize) {
                body.size = selectedSize;
                body.color_id = selectedColorId;
                body.cut_type_id = cutTypeId;
                body.fabric_id = fabricId;
            }
        } else if (type === 'product_option') {
            body.product_option_id = itemId;
            body.size = selectedSize;
            body.color_id = selectedColorId;
            body.cut_type_id = cutTypeId;
            body.fabric_id = fabricId;
            if (sublocalPersonalizations && sublocalPersonalizations.length > 0) {
                body.sublocal_personalizations = sublocalPersonalizations;
            }
        } else {
            // Generic types (fabric_piece, machine, supply, uniform)
            body.item_id = itemId;
        }
        
        const response = await fetch('{{ route("pdv.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            // Retornar resultado para a função chamadora decidir quando mostrar notificação
            return { success: true, stock_request_created: data.stock_request_created || false };
        } else {
            return { success: false, message: data.message || 'Erro ao adicionar item' };
        }
    } catch (error) {
        console.error('Erro:', error);
        return { success: false, message: 'Erro ao adicionar item ao carrinho' };
    }
}

// Função para atualizar item do carrinho
window.updateCartItem = async function updateCartItem(itemId, quantity, unitPrice) {
    try {
        const body = {
            item_id: itemId
        };

        if (quantity !== null) {
            body.quantity = Math.max(0.01, parseFloat(quantity));
        }

        if (unitPrice !== null) {
            body.unit_price = Math.max(0, parseFloat(unitPrice));
        }

        const response = await fetch('{{ route("pdv.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
        } else {
            showNotification(data.message || 'Erro ao atualizar item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar item', 'error');
    }
}

// Função para atualizar desconto do item
window.updateItemDiscount = async function updateItemDiscount(itemId) {
    try {
        const discountType = document.getElementById(`item-discount-type-${itemId}`)?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById(`item-discount-value-${itemId}`)?.value || 0);
        
        const response = await fetch('{{ route("pdv.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId,
                discount_type: discountType,
                discount_value: discountValue
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
        } else {
            showNotification(data.message || 'Erro ao atualizar desconto', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar desconto', 'error');
    }
}

// Função para remover item do carrinho
async function removeCartItem(itemId) {
    try {
        const response = await fetch('{{ route("pdv.cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            showNotification('Item removido do carrinho', 'success');
        } else {
            showNotification(data.message || 'Erro ao remover item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao remover item', 'error');
    }
}

// Função para limpar carrinho
// Abrir modal de confirmação para limpar carrinho
window.clearCart = function clearCart() {
    document.getElementById('clear-cart-modal').classList.remove('hidden');
}

// Fechar modal de confirmação
window.closeClearCartModal = function closeClearCartModal() {
    document.getElementById('clear-cart-modal').classList.add('hidden');
}

// Confirmar limpeza do carrinho
window.confirmClearCart = async function confirmClearCart() {
    closeClearCartModal();
    
    try {
        const response = await fetch('{{ route("pdv.cart.clear") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay([], 0);
            showNotification('Carrinho limpo', 'success');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao limpar carrinho', 'error');
    }
}

// Fun??o para atualizar exibi??o do carrinho
const formatPdvCurrency = (value) => `R$ ${Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
const formatPdvQuantity = (value) => Number(value || 0).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 3 });

function getCartAdjustments(subtotal) {
    const discountType = document.getElementById('discount-type')?.value || 'fixed';
    const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
    const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);

    let discount = 0;
    if (discountType === 'percent') {
        discount = subtotal * (discountValue / 100);
    } else {
        discount = discountValue;
    }

    return {
        discount,
        deliveryFee,
        total: subtotal - discount + deliveryFee,
    };
}

function syncTotalsUI(subtotal, discount, deliveryFee, total) {
    const totalText = formatPdvCurrency(total);

    const subtotalEl = document.getElementById('cart-subtotal');
    if (subtotalEl) subtotalEl.textContent = formatPdvCurrency(subtotal);

    const discountEl = document.getElementById('cart-discount-total');
    if (discountEl) discountEl.textContent = formatPdvCurrency(discount);

    const deliveryEl = document.getElementById('cart-delivery-fee');
    if (deliveryEl) deliveryEl.textContent = formatPdvCurrency(deliveryFee);

    const totalEl = document.getElementById('cart-total');
    if (totalEl) totalEl.textContent = totalText;

    const overviewTotalEl = document.getElementById('cart-overview-total');
    if (overviewTotalEl) overviewTotalEl.textContent = totalText;

    const mobileTotalEl = document.getElementById('mobile-cart-total');
    if (mobileTotalEl) mobileTotalEl.textContent = totalText;

    const mobileTotalPreview = document.getElementById('mobile-cart-total-preview');
    if (mobileTotalPreview) mobileTotalPreview.textContent = totalText;
}

function updateCartDisplay(cart, cartTotal) {
    const cartItemsContainer = document.getElementById('cart-items');
    const mobileCartItemsContainer = document.getElementById('mobile-cart-items');

    const renderEmptyState = () => `
        <div class="pdv-empty-state">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <div>
                <p class="font-semibold text-sm">Seu carrinho esta vazio</p>
                <p class="text-xs mt-1">Adicione itens do catalogo para iniciar a venda.</p>
            </div>
        </div>`;

    const renderMobileEmptyState = () => `
        <div class="pdv-empty-state min-h-[180px]">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <div>
                <p class="font-semibold text-sm">Carrinho vazio</p>
                <p class="text-xs mt-1">Adicione itens para visualizar o resumo.</p>
            </div>
        </div>`;

    const renderMetaChips = (item) => {
        const chips = [];

        if (item.color_name && !item.type?.includes('fabric_piece')) chips.push(`<span class="pdv-chip">${item.color_name}</span>`);
        if (item.size) chips.push(`<span class="pdv-chip">${item.size}</span>`);

        if (item.sale_type && item.sale_type !== 'unidade') {
            chips.push(`<span class="pdv-chip pdv-chip-accent">${item.sale_type === 'kg' ? 'Venda por Kg' : 'Venda por Metro'}</span>`);
        }

        return chips.join('');
    };

    const renderExtras = (item) => {
        let extras = '';

        if (item.size_surcharges && Object.keys(item.size_surcharges).length > 0) {
            const surchargeLines = Object.entries(item.size_surcharges)
                .filter(([, data]) => Number(data.quantity || 0) > 0)
                .map(([size, data]) => `<span class="pdv-chip">+ ${size} (${data.quantity}x): ${formatPdvCurrency(data.total)}</span>`)
                .join('');
            if (surchargeLines) extras += `<div class="pdv-cart-item-meta mt-2">${surchargeLines}</div>`;
        }

        if (item.sublocal_personalizations && item.sublocal_personalizations.length > 0) {
            const sublocalLines = item.sublocal_personalizations
                .map((personalization) => {
                    const locationName = personalization.location_name || 'Local';
                    const sizeName = personalization.size_name ? ` - ${personalization.size_name}` : '';
                    return `<span class="pdv-chip pdv-chip-success">${locationName}${sizeName} (${personalization.quantity}x): ${formatPdvCurrency(personalization.final_price || 0)}</span>`;
                })
                .join('');
            if (sublocalLines) extras += `<div class="pdv-cart-item-meta mt-2">${sublocalLines}</div>`;
        }

        return extras;
    };

    const renderCartItem = (item) => `
        <div class="cart-item pdv-cart-item" data-item-id="${item.id}">
            <div class="pdv-cart-item-head">
                <div class="min-w-0 flex-1">
                    <p class="pdv-cart-item-title">${item.product_title}${item.size && item.type === 'fabric_piece' ? ` - ${item.size}` : ''}</p>
                    <div class="pdv-cart-item-meta">${renderMetaChips(item)}</div>
                    ${renderExtras(item)}
                </div>
                <button onclick="removeCartItem('${item.id}')" class="pdv-cart-remove">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="pdv-cart-item-foot">
                <div class="pdv-cart-controls">
                    <input type="number"
                           value="${item.quantity}"
                           step="${item.type === 'fabric_piece' ? (item.control_unit === 'metros' ? '0.01' : '0.001') : (item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1')}"
                           min="${item.type === 'fabric_piece' ? (item.control_unit === 'metros' ? '0.01' : '0.001') : (item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1')}"
                           onchange="updateCartItem('${item.id}', this.value, null)"
                           class="w-16 p-0 text-center text-xs bg-transparent dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-0" style="background-color: transparent !important;">
                    <span class="text-xs text-gray-400">x</span>
                    <input type="number"
                           step="0.01"
                           value="${parseFloat(item.unit_price || 0).toFixed(2)}"
                           min="0"
                           onchange="updateCartItem('${item.id}', null, this.value)"
                           class="w-20 p-0 text-right text-xs bg-transparent dark:bg-transparent text-gray-900 dark:text-gray-100 font-medium focus:ring-0" style="background-color: transparent !important;">
                </div>
                <p class="pdv-cart-price">${formatPdvCurrency((item.total_price || 0) - (item.item_discount || 0))}</p>
            </div>

            <div class="pdv-cart-discount-row mt-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                <label class="text-xs font-semibold text-gray-500">Desconto</label>
                <select id="item-discount-type-${item.id}" class="px-2 py-1 text-[11px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500" style="background-color: transparent !important;" onchange="updateItemDiscount('${item.id}')">
                    <option value="fixed" ${(item.discount_type || 'fixed') === 'fixed' ? 'selected' : ''} class="dark:bg-gray-800">R$</option>
                    <option value="percent" ${item.discount_type === 'percent' ? 'selected' : ''} class="dark:bg-gray-800">%</option>
                </select>
                <input type="number" id="item-discount-value-${item.id}" step="0.01" min="0" value="${item.discount_value || 0}" onchange="updateItemDiscount('${item.id}')" class="w-20 px-2 py-1 text-right text-[11px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-transparent text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-indigo-500" style="background-color: transparent !important;">
                ${(item.item_discount || 0) > 0 ? `<span class="pdv-chip pdv-chip-danger">- ${formatPdvCurrency(item.item_discount || 0)}</span>` : ''}
            </div>
        </div>`;

    const renderMobileCartItem = (item) => `
        <div class="pdv-cart-item">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="pdv-cart-item-title">${item.product_title}</p>
                    <p class="text-xs text-gray-500 mt-1">${formatPdvQuantity(item.quantity)} x ${formatPdvCurrency(item.unit_price || 0)}</p>
                </div>
                <p class="pdv-cart-price">${formatPdvCurrency((item.total_price || 0) - (item.item_discount || 0))}</p>
            </div>
        </div>`;

    if (!cart || cart.length === 0) {
        cartItemsContainer.innerHTML = renderEmptyState();
        if (mobileCartItemsContainer) mobileCartItemsContainer.innerHTML = renderMobileEmptyState();
        syncTotalsUI(0, 0, 0, 0);
    } else {
        cartItemsContainer.innerHTML = cart.map(renderCartItem).join('');
        if (mobileCartItemsContainer) {
            mobileCartItemsContainer.innerHTML = `<div class="pdv-cart-list">${cart.map(renderMobileCartItem).join('')}</div>`;
        }

        let subtotal = 0;
        let totalQuantity = 0;

        cart.forEach(item => {
            subtotal += parseFloat(item.total_price || 0);
            totalQuantity += parseFloat(item.quantity || 0);
        });

        const { discount, deliveryFee, total } = getCartAdjustments(subtotal);
        syncTotalsUI(subtotal, discount, deliveryFee, total);

        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) cartTotalItems.textContent = formatPdvQuantity(totalQuantity);

        const cartItemsBadge = document.getElementById('cart-total-items-badge');
        if (cartItemsBadge) cartItemsBadge.textContent = `${formatPdvQuantity(totalQuantity)} itens`;

        const mobileCartCount = document.getElementById('mobile-cart-count');
        if (mobileCartCount) {
            mobileCartCount.textContent = cart.length;
            mobileCartCount.classList.remove('hidden');
        }

        const mobileTotalPreview = document.getElementById('mobile-cart-total-preview');
        if (mobileTotalPreview) mobileTotalPreview.classList.remove('hidden');
    }

    if (!cart || cart.length === 0) {
        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) cartTotalItems.textContent = '0';

        const cartItemsBadge = document.getElementById('cart-total-items-badge');
        if (cartItemsBadge) cartItemsBadge.textContent = '0 itens';

        const mobileCartCount = document.getElementById('mobile-cart-count');
        if (mobileCartCount) {
            mobileCartCount.textContent = '0';
            mobileCartCount.classList.add('hidden');
        }

        const mobileTotalPreview = document.getElementById('mobile-cart-total-preview');
        if (mobileTotalPreview) {
            mobileTotalPreview.textContent = 'R$ 0,00';
            mobileTotalPreview.classList.add('hidden');
        }

        const mobileTotalEl = document.getElementById('mobile-cart-total');
        if (mobileTotalEl) mobileTotalEl.textContent = 'R$ 0,00';
    }

    updateCheckoutButtonState();
}

function updateCheckoutButtonState() {
    const cartItems = document.querySelectorAll('.cart-item');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    if (checkoutBtn) {
        // Habilitar apenas se tiver itens no carrinho (cliente é opcional)
        if (cartItems.length > 0) {
            checkoutBtn.disabled = false;
        } else {
            checkoutBtn.disabled = true;
        }
    }
}

// Fun??o para atualizar total
function updateTotal() {
    fetch('{{ route("pdv.cart.get") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const cart = data.cart || [];
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const { discount, deliveryFee, total } = getCartAdjustments(subtotal);
        syncTotalsUI(subtotal, discount, deliveryFee, total);
    })
    .catch(error => {
        console.error('Erro ao buscar carrinho:', error);

        const cartItems = document.querySelectorAll('.cart-item .pdv-cart-price');
        let subtotal = 0;

        cartItems.forEach(item => {
            const match = item.textContent.match(/R\$\s*([\d.,]+)/);
            if (!match) return;

            const priceStr = match[1].replace(/\./g, '').replace(',', '.');
            const price = parseFloat(priceStr);
            if (!isNaN(price)) subtotal += price;
        });

        const { discount, deliveryFee, total } = getCartAdjustments(subtotal);
        syncTotalsUI(subtotal, discount, deliveryFee, total);
    });
}

// Fun??o para finalizar venda
// Vari?veis globais para pagamento
let paymentMethods = [];
let checkoutData = null;

// Fun??o para finalizar venda - abre modal de pagamento
// Fun??o para finalizar venda checkout normal
window.checkout = async function checkout() {
    // Buscar valor do client_id - pode ser vazio, null ou um ID
    const clientIdElement = document.getElementById('client_id');
    let clientId = clientIdElement ? clientIdElement.value : null;
    
    // Normalizar: se for string vazia, undefined, null ou 'null', converter para null
    if (!clientId || clientId === '' || clientId === 'null' || clientId === 'undefined') {
        clientId = null;
    }
    
    console.log('Checkout - client_id:', clientId);
    
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        // Salvar dados do checkout
        checkoutData = {
            client_id: clientId,
            seller_id: document.getElementById('seller_id')?.value || null,
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            total: total,
            subtotal: subtotal // Adicionar subtotal para referência
        };
        
        console.log('Checkout data:', checkoutData);
        
        // Resetar métodos de pagamento
        paymentMethods = [];
        
        // Atualizar modal de pagamento
        document.getElementById('payment-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        renderPaymentMethods();
        updatePaymentTotals();
        
        // Abrir modal de pagamento
        const modal = document.getElementById('payment-modal');
        if (modal) {
            modal.style.display = 'flex';
            // Auto-preencher o valor total no campo de pagamento
            const amountInput = document.getElementById('new-payment-amount');
            if (amountInput) {
                amountInput.value = total.toFixed(2);
                amountInput.focus();
                amountInput.select();
            }
        }
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Função para finalizar venda sem cliente - agora abre modal de pagamento
window.checkoutWithoutClient = async function checkoutWithoutClient() {
    console.log('checkoutWithoutClient: Iniciando...');
    
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('checkoutWithoutClient: Carrinho recebido:', data);
        
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discountType = document.getElementById('discount-type')?.value || 'fixed';
        const discountValue = parseFloat(document.getElementById('discount-input')?.value || 0);
        let discount = 0;
        if (discountType === 'percent') {
            discount = subtotal * (discountValue / 100);
        } else {
            discount = discountValue;
        }
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        console.log('checkoutWithoutClient: Total calculado:', total);
        
        // Salvar dados do checkout SEM CLIENTE (client_id = null)
        checkoutData = {
            client_id: null,
            seller_id: document.getElementById('seller_id')?.value || null,
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            total: total,
            subtotal: subtotal
        };
        
        console.log('Checkout sem cliente, abrindo modal de pagamento:', checkoutData);
        
        // Resetar métodos de pagamento
        paymentMethods = [];
        
        // Atualizar modal de pagamento
        const paymentTotalEl = document.getElementById('payment-total');
        if (paymentTotalEl) {
            paymentTotalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        console.log('checkoutWithoutClient: Chamando renderPaymentMethods...');
        if (typeof renderPaymentMethods === 'function') {
            renderPaymentMethods();
        } else {
            console.error('renderPaymentMethods não é uma função!');
        }
        
        console.log('checkoutWithoutClient: Chamando updatePaymentTotals...');
        if (typeof updatePaymentTotals === 'function') {
            updatePaymentTotals();
        } else {
            console.error('updatePaymentTotals não é uma função!');
        }
        
        // Abrir modal de pagamento (igual ao checkout normal)
        console.log('checkoutWithoutClient: Abrindo modal...');
        const modal = document.getElementById('payment-modal');
        if (modal) {
            modal.style.display = 'flex';
            // Auto-preencher o valor total no campo de pagamento
            const amountInput = document.getElementById('new-payment-amount');
            if (amountInput) {
                amountInput.value = total.toFixed(2);
                amountInput.focus();
                amountInput.select();
            }
            console.log('checkoutWithoutClient: Modal aberto com sucesso!');
        } else {
            console.error('Elemento payment-modal não encontrado!');
        }
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Adicionar método de pagamento
window.addPaymentMethod = function addPaymentMethod() {
    const method = document.getElementById('new-payment-method').value;
    const amount = parseFloat(document.getElementById('new-payment-amount').value);
    const receiptInput = document.getElementById('pdv-payment-receipt');
    const file = receiptInput?.files[0];
    
    console.log('Adding payment method:', method, amount);

    if (!method) {
        showNotification('Selecione uma forma de pagamento', 'error');
        return;
    }
    
    if (!amount || amount <= 0) {
        showNotification('Informe um valor válido', 'error');
        return;
    }
    
    const paymentId = Date.now() + Math.random();
    
    paymentMethods.push({
        id: paymentId,
        method: method,
        amount: amount,
        fileName: file ? file.name : null
    });

    if (file) {
        if (!window.pdvPaymentFiles) window.pdvPaymentFiles = {};
        window.pdvPaymentFiles[paymentId] = file;
        
        // Reset file input after use
        receiptInput.value = '';
        document.getElementById('pdv-receipt-filename').textContent = 'Clique para anexar comprovante';
    }
    
    document.getElementById('new-payment-method').value = '';
    document.getElementById('new-payment-amount').value = '';
    
    // Focar no dropdown de método para a próxima inserção
    document.getElementById('new-payment-method').focus();
    
    renderPaymentMethods();
    updatePaymentTotals();
}

// Inicializar listeners do modal de pagamento usando delegação
document.addEventListener('keypress', function(e) {
    if (e.target && e.target.id === 'new-payment-amount' && e.key === 'Enter') {
        e.preventDefault();
        addPaymentMethod();
    }
});

document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'new-payment-method') {
        const amountInput = document.getElementById('new-payment-amount');
        if (amountInput && (!amountInput.value || amountInput.value === '0' || amountInput.value === '')) {
            // Calcular restante
            const total = checkoutData?.total || 0;
            const totalPaid = paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
            const remaining = Math.max(0, total - totalPaid);
            amountInput.value = remaining.toFixed(2);
        }
    }
});

// Remover método de pagamento
window.removePaymentMethod = function removePaymentMethod(id) {
    paymentMethods = paymentMethods.filter(pm => pm.id !== id);
    if (window.pdvPaymentFiles && window.pdvPaymentFiles[id]) {
        delete window.pdvPaymentFiles[id];
    }
    renderPaymentMethods();
    updatePaymentTotals();
}

// Renderizar lista de métodos de pagamento
function renderPaymentMethods() {
    const container = document.getElementById('payment-methods-list');
    
    if (paymentMethods.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>';
        return;
    }
    
    container.innerHTML = paymentMethods.map(pm => `
        <div class="flex items-center justify-between p-3 pdv-card bg-gray-50 rounded-lg">
            <div>
                <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">${pm.method}</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">R$ ${pm.amount.toFixed(2).replace('.', ',')}</span>
                ${pm.fileName ? `<div class="text-[10px] text-green-600 font-bold mt-0.5 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>${pm.fileName}</div>` : ''}
            </div>
            <button onclick="removePaymentMethod(${pm.id})" 
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
}

// Atualizar totais do pagamento
function updatePaymentTotals() {
    const total = checkoutData?.total || 0;
    const totalPaid = paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
    const remaining = total - totalPaid;
    
    document.getElementById('payment-total-paid').textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
    
    const remainingElement = document.getElementById('payment-remaining');
    remainingElement.textContent = `R$ ${Math.abs(remaining).toFixed(2).replace('.', ',')}`;
    remainingElement.className = remaining >= 0 
        ? 'text-lg font-semibold text-gray-900 dark:text-gray-100' 
        : 'text-lg font-semibold text-orange-600 dark:text-orange-400';
    
    // Habilitar botão apenas se houver pelo menos um método de pagamento
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = paymentMethods.length === 0;
}

// Fechar modal de pagamento
window.closePaymentModal = function closePaymentModal() {
    document.getElementById('payment-modal').style.display = 'none';
    paymentMethods = [];
    window.pdvPaymentFiles = {};
    const receiptInput = document.getElementById('pdv-payment-receipt');
    if (receiptInput) receiptInput.value = '';
    const receiptFileName = document.getElementById('pdv-receipt-filename');
    if (receiptFileName) receiptFileName.textContent = 'Clique para anexar comprovante';
    checkoutData = null;
}

// Confirmar pagamento e finalizar venda
window.confirmPayment = async function confirmPayment() {
    console.log('Confirm payment clicked. Methods:', paymentMethods.length);
    
    if (paymentMethods.length === 0) {
        showNotification('Adicione pelo menos uma forma de pagamento', 'error');
        return;
    }
    
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Processando...';
    
    try {
        // Preparar dados do checkout, garantindo que client_id seja null se vazio
        const checkoutPayload = {
            ...checkoutData,
            payment_methods: paymentMethods
        };
        
        // Garantir que client_id seja null se vazio, undefined ou string vazia
        if (!checkoutPayload.client_id || 
            checkoutPayload.client_id === '' || 
            checkoutPayload.client_id === 'null' || 
            checkoutPayload.client_id === 'undefined') {
            checkoutPayload.client_id = null;
        }
        
        console.log('Enviando checkout payload:', JSON.stringify(checkoutPayload, null, 2));
        
        const checkoutUrl = '{{ route("pdv.checkout") }}';
        console.log('Sending checkout POST to:', checkoutUrl);

        // Files need FormData
        const formData = new FormData();
        
        // Add checkout data
        Object.entries(checkoutData).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                formData.append(key, value);
            }
        });

        // Add payment methods as JSON string (or handle differently)
        formData.append('payment_methods', JSON.stringify(paymentMethods));

        // Add CSRF token
        formData.append('_token', csrfToken);

        // Add payment receipt files
        if (window.pdvPaymentFiles) {
            Object.entries(window.pdvPaymentFiles).forEach(([id, file]) => {
                formData.append(`receipt_attachments[${id}]`, file);
            });
        }
        
        const response = await fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                // Content-Type is set automatically for FormData
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
             const text = await response.text();
             console.error('Checkout failed. Status:', response.status, 'Response:', text);
             throw new Error(`Erro do servidor: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Verificar se há solicitações de estoque criadas
            let message = `Pedido #${data.order_number} criado com sucesso!`;
            let notificationType = 'success';
            
            if (data.stock_requests_created && data.stock_requests_created.length > 0) {
                const requestsCount = data.stock_requests_created.length;
                const requestsInfo = data.stock_requests_created.map(r => 
                    `${r.size}: ${r.quantity}`
                ).join(', ');
                message += ` ${requestsCount} solicitação(ões) de estoque criada(s): ${requestsInfo}`;
                notificationType = 'warning';
            }
            
            showNotification(message, notificationType);
            
            // Fechar modal
            closePaymentModal();
            
            // Gerar nota da venda (abrir em nova aba)
            if (data.receipt_url) {
                window.open(data.receipt_url, '_blank');
            }
            
            // Limpar carrinho e redirecionar para o pedido
            sessionStorage.removeItem('pdv_cart');
            setTimeout(() => {
                window.location.href = '{{ route("orders.show", ":id") }}'.replace(':id', data.order_id);
            }, 1500);
        } else {
            const errorMessage = data.message || (data.errors ? JSON.stringify(data.errors) : 'Erro ao finalizar venda');
            console.error('Erro no checkout:', data);
            showNotification(errorMessage, 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Finalizar Venda';
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao finalizar venda: ' + error.message, 'error');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Finalizar Venda';
    }
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-gray-900 text-white border-l-4 border-green-500' :
        type === 'error' ? 'bg-gray-900 text-white border-l-4 border-red-500' :
        'bg-gray-900 text-white border-l-4 border-indigo-500'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remover após 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

const clientSearchUrl = "{{ url('/api/clients/search') }}";

// Função para buscar clientes
window.searchClient = function searchClient() {
    const query = document.getElementById('search-client').value;
    const resultsDiv = document.getElementById('search-results');
    
    // Show results container
    resultsDiv.classList.remove('hidden');
    
    if (query.length < 3) {
        resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Digite ao menos 3 caracteres para buscar</p>';
        return;
    }

    fetch(`${clientSearchUrl}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Nenhum cliente encontrado</p>';
                return;
            }

            resultsDiv.innerHTML = data.map(client => `
                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md cursor-pointer transition-all"
                     onclick='selectClient(${JSON.stringify(client)})'>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">${client.name || 'Sem nome'}</p>
                            ${client.phone_primary ? `<p class="text-sm text-gray-600 dark:text-gray-400">${client.phone_primary}</p>` : ''}
                            ${client.cpf_cnpj ? `<p class="text-xs text-gray-500 dark:text-gray-500">${client.cpf_cnpj}</p>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Erro ao buscar clientes:', error);
            resultsDiv.innerHTML = '<p class="text-sm text-red-500 dark:text-red-400 p-2">Erro ao buscar clientes. Tente novamente.</p>';
        });
}

// Função para selecionar cliente
window.selectClient = function selectClient(client) {
    document.getElementById('client_id').value = client.id;
    
    // Mostrar cliente selecionado
    const displayDiv = document.getElementById('selected-client-display');
    const nameDiv = document.getElementById('selected-client-name');
    const infoDiv = document.getElementById('selected-client-info');
    
    nameDiv.textContent = client.name || 'Sem nome';
    
    let info = [];
    if (client.phone_primary) info.push(client.phone_primary);
    if (client.cpf_cnpj) info.push(client.cpf_cnpj);
    infoDiv.textContent = info.join(' • ') || '';
    
    displayDiv.classList.remove('hidden');
    
    // Limpar busca
    document.getElementById('search-client').value = '';
    // Corrigido linha duplicada
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Função para limpar cliente selecionado
window.clearSelectedClient = function clearSelectedClient() {
    const clientIdElement = document.getElementById('client_id');
    if (clientIdElement) {
        clientIdElement.value = '';
        clientIdElement.removeAttribute('value'); // Garantir que não tenha valor
    }
    document.getElementById('selected-client-display').classList.add('hidden');
    document.getElementById('search-client').value = '';
    document.getElementById('search-client').value = '';
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Permitir buscar ao pressionar Enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-client');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchClient();
            }
        });
    }
});

// Calcular acréscimos de tamanho automaticamente quando GG, EXG, G1, G2, G3 são selecionados
// Usa os valores fixos do modelo SizeSurcharge


// Função para toggle do painel de estoque
window.toggleStockDetails = function() {
    const panel = document.getElementById('stock-details-panel');
    const icon = document.getElementById('stock-toggle-icon');
    if (panel && icon) {
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            panel.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
};

// --- AJAX Logic for Instant Search & Navigation ---

// Debounce wrapper
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Current state
let currentSearch = '{{ $search ?? "" }}';
let currentType = '{{ $type ?? "products" }}';
let catalogRequestController = null;

function getCatalogContent() {
    return document.getElementById('pdv-catalog-content');
}

function getProductSearchInput() {
    return document.getElementById('product-search');
}

function buildCatalogUrl(baseUrl, type, search) {
    const url = new URL(baseUrl || `{{ route('pdv.index') }}`, window.location.origin);
    url.searchParams.set('type', type);

    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }

    return url.toString();
}

function setActiveTab(type) {
    document.querySelectorAll('.pdv-tab-link').forEach((link) => {
        const isActive = link.dataset.type === type;
        link.classList.toggle('bg-indigo-600', isActive);
        link.classList.toggle('text-white', isActive);
        link.setAttribute('aria-current', isActive ? 'page' : 'false');
        link.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    // Atualizar placeholder da busca por aba
    const si = getProductSearchInput();
    if (si) {
        const placeholders = {
            products: 'Buscar camisa, tipo de corte...',
            fabric_pieces: 'Digite o nome da malha ou tipo de tecido...',
            machines: 'Buscar máquina por nome ou código...',
            supplies: 'Buscar suprimento...',
            uniforms: 'Buscar uniforme...',
        };
        si.placeholder = placeholders[type] || 'Buscar item...';
    }

    // Limpar dropdown de tecido quando sai da aba Tecidos
    if (type !== 'fabric_pieces') {
        hideFabricDropdown();
    }
}

// Fetch function
async function fetchProducts(type, search, options = {}) {
    const catalogContent = getCatalogContent();
    if (!catalogContent) return;

    const {
        url = null,
        historyMode = 'replace',
    } = options;

    const requestUrl = url ?? `{{ route('pdv.index') }}?type=${encodeURIComponent(type)}&search=${encodeURIComponent(search)}`;

    if (catalogRequestController) {
        catalogRequestController.abort();
    }

    const requestController = new AbortController();
    catalogRequestController = requestController;
    catalogContent.style.opacity = '0.5';
    catalogContent.style.pointerEvents = 'none';
    catalogContent.style.transition = 'opacity 0.2s';

    try {
        const response = await fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            signal: requestController.signal
        });

        if (!response.ok) throw new Error(`Erro HTTP ${response.status}`);

        const data = await response.json();
        
        // Update Grid HTML
        if (data.html) {
            catalogContent.innerHTML = data.html;
        }
        
        currentType = data.type ?? type;
        currentSearch = data.search ?? search ?? '';

        setActiveTab(currentType);

        const searchInput = getProductSearchInput();
        if (searchInput && searchInput.value !== currentSearch) {
            searchInput.value = currentSearch;
        }

        if (data.jsItems && typeof window.updatePageItems === 'function') {
            window.updatePageItems(data.jsItems);
        }

        const nextUrl = new URL(requestUrl, window.location.origin);
        if (historyMode === 'push') {
            window.history.pushState({ path: nextUrl.toString() }, '', nextUrl);
        } else {
            window.history.replaceState({ path: nextUrl.toString() }, '', nextUrl);
        }

    } catch (error) {
        if (error.name === 'AbortError') {
            return;
        }

        console.error('Erro ao buscar produtos:', error);
        showNotification('Erro ao carregar produtos', 'error');
    } finally {
        if (catalogRequestController === requestController) {
            catalogRequestController = null;
            catalogContent.style.opacity = '1';
            catalogContent.style.pointerEvents = '';
        }
    }
}

// Event Listener: Instant Search
const searchInput = getProductSearchInput();
if (searchInput) {
    searchInput.addEventListener('input', debounce(function(e) {
        const val = e.target.value;
        if (currentType === 'fabric_pieces') {
            fetchFabricAutocomplete(val);
        }
        fetchProducts(currentType, val, { historyMode: 'replace' });
    }, 350));

    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function(ev) {
        if (!ev.target.closest('#fabric-search-dropdown') && ev.target.id !== 'product-search') {
            hideFabricDropdown();
        }
    });
}

// ---- Fabric Piece Autocomplete ----
const fabricSearchUrl = '{{ route("pdv.fabric-pieces.search") }}';

function hideFabricDropdown() {
    const dd = document.getElementById('fabric-search-dropdown');
    if (dd) dd.classList.add('hidden');
}

function showFabricDropdown() {
    const dd = document.getElementById('fabric-search-dropdown');
    if (dd) dd.classList.remove('hidden');
}

async function fetchFabricAutocomplete(q) {
    const dd = document.getElementById('fabric-search-dropdown');
    const results = document.getElementById('fabric-search-results');
    if (!dd || !results) return;

    if (!q || q.trim().length === 0) {
        hideFabricDropdown();
        return;
    }

    try {
        const res = await fetch(`${fabricSearchUrl}?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const pieces = await res.json();

        if (!pieces || pieces.length === 0) {
            results.innerHTML = `<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">Nenhuma peça encontrada para "<strong>${q}</strong>"</div>`;
            showFabricDropdown();
            return;
        }

        results.innerHTML = pieces.map(piece => {
            const colorSwatch = piece.color_hex
                ? `<span class="inline-block w-4 h-4 rounded-full border border-gray-300 flex-shrink-0" style="background:${piece.color_hex}"></span>`
                : `<span class="inline-block w-4 h-4 rounded-full bg-gray-200 border border-gray-300 flex-shrink-0"></span>`;
            const statusBadge = piece.status === 'fechada'
                ? `<span class="text-[10px] font-bold px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded">Fechada</span>`
                : `<span class="text-[10px] font-bold px-1.5 py-0.5 bg-green-100 text-green-700 rounded">Aberta</span>`;
            const unitLabel = piece.control_unit === 'metros' ? '/m' : '/kg';
            const priceText = piece.price > 0
                ? `<span class="font-bold text-indigo-600 dark:text-indigo-400">R$ ${parseFloat(piece.price).toLocaleString('pt-BR', {minimumFractionDigits:2,maximumFractionDigits:2})}${unitLabel}</span>`
                : `<span class="text-gray-400 text-xs">Preço não definido</span>`;

            // Register item in pageItems so openAddProductModal can find it
            if (window.pageItems && !window.pageItems.find(p => p.id == piece.id && p.type === 'fabric_piece')) {
                window.pageItems.push(piece);
            }

            return `
                <div class="px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                     onclick="hideFabricDropdown(); openAddProductModal(${piece.id}, 'fabric_piece')">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">${piece.fabric_type_name}</span>
                                ${statusBadge}
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                ${colorSwatch}
                                <span class="text-xs text-gray-500 dark:text-gray-400">${piece.color_name}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${piece.available_label} disponível</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            ${priceText}
                        </div>
                    </div>
                </div>`;
        }).join('');

        showFabricDropdown();
    } catch (err) {
        console.error('Erro no autocomplete de tecido:', err);
        hideFabricDropdown();
    }
}

// Limpar dropdown ao trocar de aba
document.addEventListener('click', function(e) {
    const tabLink = e.target.closest('.pdv-tab-link[data-type]');
    if (tabLink) {
        hideFabricDropdown();
        e.preventDefault();
        setActiveTab(tabLink.dataset.type);
        const searchValue = getProductSearchInput()?.value || '';
        fetchProducts(tabLink.dataset.type, searchValue, {
            url: buildCatalogUrl(tabLink.dataset.url, tabLink.dataset.type, searchValue),
            historyMode: 'push',
        });
        return;
    }

    const catalogContent = getCatalogContent();
    const paginationLink = e.target.closest('.pdv-pagination-btn[data-url]');
    if (paginationLink && catalogContent && catalogContent.contains(paginationLink)) {
        e.preventDefault();
        const url = paginationLink.dataset.url;
        const urlObj = new URL(url, window.location.origin);
        const params = new URLSearchParams(urlObj.search);
        const type = params.get('type') || currentType;
        const search = params.get('search') || currentSearch;

        fetchProducts(type, search, {
            url,
            historyMode: 'push',
        });
    }
});

window.addEventListener('popstate', function() {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    fetchProducts(
        params.get('type') || 'products',
        params.get('search') || '',
        {
            url: url.toString(),
            historyMode: 'replace',
        }
    );
});

</script>
@endsection

