@extends('layouts.admin')

@section('content')
<style>
    .stock-requests-page {
        --stock-bg: #f8fafc;
        --stock-surface: #ffffff;
        --stock-surface-soft: #f1f5f9;
        --stock-border: #dbe2ea;
        --stock-border-strong: #cbd5e1;
        --stock-text: #0f172a;
        --stock-muted: #64748b;
        --stock-accent: #2563eb;
        --stock-accent-strong: #1d4ed8;
        --stock-accent-soft: #dbeafe;
    }

    .dark .stock-requests-page {
        --stock-bg: #111827;
        --stock-surface: #1f2937;
        --stock-surface-soft: #111827;
        --stock-border: #334155;
        --stock-border-strong: #475569;
        --stock-text: #f3f4f6;
        --stock-muted: #94a3b8;
        --stock-accent-soft: rgba(59, 130, 246, 0.2);
    }

    .stock-toolbar {
        border: 1px solid var(--stock-border);
        background: linear-gradient(115deg, #f8fbff 0%, #eff6ff 52%, #eef2ff 100%);
    }

    .dark .stock-toolbar {
        background: linear-gradient(115deg, #111827 0%, #172033 50%, #1e1b3a 100%);
    }

    .stock-toolbar-label {
        display: inline-flex;
        align-items: center;
        border: 1px solid #bfdbfe;
        background-color: #eff6ff;
        color: #1d4ed8;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.35rem 0.75rem;
    }

    .dark .stock-toolbar-label {
        border-color: #3b82f6;
        background-color: rgba(37, 99, 235, 0.16);
        color: #93c5fd;
    }

    .stock-summary-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        border: 1px solid var(--stock-border-strong);
        background-color: var(--stock-surface);
        border-radius: 999px;
        color: var(--stock-text);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.7rem;
    }

    .stock-summary-chip .value {
        color: var(--stock-accent);
    }

    .stock-toolbar-button {
        border-radius: 0.8rem;
        border: 1px solid transparent;
        color: #ffffff !important;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        padding: 0.62rem 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .stock-toolbar-button:hover {
        transform: translateY(-1px);
    }

    .stock-toolbar-button svg {
        width: 0.95rem;
        height: 0.95rem;
        flex: 0 0 auto;
    }

    .stock-toolbar-button.transfer {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.28);
    }

    .stock-toolbar-button.order {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        box-shadow: 0 10px 24px rgba(124, 58, 237, 0.28);
    }

    .stock-toolbar-button.decrement {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 10px 24px rgba(220, 38, 38, 0.24);
    }

    .stock-toolbar-button.stock {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        box-shadow: 0 10px 24px rgba(13, 148, 136, 0.24);
    }

    .stock-filter-panel {
        border: 1px solid var(--stock-border);
        border-radius: 1rem;
        background: var(--stock-surface);
        box-shadow: 0 10px 28px -16px rgba(15, 23, 42, 0.3);
        padding: 1rem;
    }

    .stock-filter-label {
        display: block;
        color: var(--stock-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
    }

    .stock-filter-input {
        width: 100%;
        border: 1px solid var(--stock-border-strong);
        border-radius: 0.85rem;
        background: var(--stock-surface);
        color: var(--stock-text);
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.65rem 0.85rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .stock-filter-input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .stock-filter-cta {
        border: 1px solid #1d4ed8;
        border-radius: 0.8rem;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: #ffffff !important;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 0.64rem 1rem;
        min-width: 102px;
    }

    .stock-filter-cta:hover {
        filter: brightness(1.05);
    }

    .stock-filter-clear {
        border: 1px solid var(--stock-border-strong);
        border-radius: 0.8rem;
        background: var(--stock-surface-soft);
        color: var(--stock-muted);
        font-size: 0.86rem;
        font-weight: 700;
        padding: 0.64rem 1rem;
        min-width: 102px;
        text-align: center;
    }

    .stock-table-shell {
        border: 1px solid var(--stock-border);
        border-radius: 1rem;
        background: var(--stock-surface);
        box-shadow: 0 18px 30px -24px rgba(15, 23, 42, 0.46);
        overflow: hidden;
    }

    .stock-table-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        background: linear-gradient(180deg, var(--stock-surface-soft) 0%, rgba(248, 250, 252, 0) 100%);
        border-bottom: 1px solid var(--stock-border);
    }

    .stock-scroll-hint {
        color: var(--stock-muted);
        font-size: 0.74rem;
        font-weight: 600;
    }

    .stock-table-wrapper {
        overflow-x: auto;
    }

    .stock-requests-table {
        width: 100%;
        min-width: 1360px;
        font-size: 0.835rem;
        line-height: 1.45;
    }

    .stock-requests-table thead th {
        background-color: #f8fafc;
        border-bottom: 1px solid var(--stock-border);
        color: #475569;
        font-size: 0.71rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 0.78rem 0.8rem;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dark .stock-requests-table thead th {
        background-color: #111827;
        color: #94a3b8;
    }

    .stock-requests-table tbody td {
        border-bottom: 1px solid var(--stock-border);
        color: var(--stock-text);
        padding: 0.85rem 0.8rem;
        vertical-align: top;
    }

    .stock-requests-table tbody tr {
        transition: background-color 0.18s ease;
    }

    .stock-requests-table tbody tr:nth-child(even) {
        background-color: #fcfdff;
    }

    .dark .stock-requests-table tbody tr:nth-child(even) {
        background-color: #182132;
    }

    .stock-requests-table tbody tr:hover {
        background-color: #eff6ff !important;
    }

    .dark .stock-requests-table tbody tr:hover {
        background-color: #1e293b !important;
    }

    .sticky-left,
    .sticky-right {
        position: sticky;
        z-index: 6;
        backdrop-filter: blur(3px);
    }

    .sticky-left {
        left: 0;
        box-shadow: 1px 0 0 0 var(--stock-border);
        background-color: rgba(255, 255, 255, 0.94);
    }

    .sticky-right {
        right: 0;
        box-shadow: -1px 0 0 0 var(--stock-border);
        background-color: rgba(255, 255, 255, 0.94);
    }

    .dark .sticky-left,
    .dark .sticky-right {
        background-color: rgba(31, 41, 55, 0.92);
    }

    .stock-id-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background-color: #e2e8f0;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.75rem;
        line-height: 1;
        padding: 0.36rem 0.62rem;
    }

    .dark .stock-id-badge {
        background-color: #334155;
        color: #e2e8f0;
    }

    .stock-tag-pdv {
        border-radius: 999px;
        background-color: #ede9fe;
        color: #6d28d9;
        font-size: 0.68rem;
        font-weight: 800;
        line-height: 1;
        padding: 0.26rem 0.52rem;
    }

    .dark .stock-tag-pdv {
        background-color: rgba(124, 58, 237, 0.22);
        color: #c4b5fd;
    }

    .stock-store-secondary {
        color: var(--stock-muted);
        font-size: 0.74rem;
        font-weight: 600;
    }

    .stock-broadcast {
        color: #7c3aed;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.02em;
    }

    .stock-product-main {
        display: block;
        font-weight: 700;
        color: var(--stock-text);
    }

    .stock-product-sub {
        display: block;
        color: var(--stock-muted);
        font-size: 0.78rem;
        font-weight: 500;
    }

    .stock-size-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background-color: var(--stock-accent-soft);
        color: #1d4ed8;
        font-size: 0.72rem;
        font-weight: 800;
        line-height: 1;
        padding: 0.33rem 0.52rem;
    }

    .dark .stock-size-chip {
        color: #bfdbfe;
    }

    .stock-number-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.1rem;
        border-radius: 999px;
        background-color: #e2e8f0;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 0.2rem 0.55rem;
    }

    .stock-number-pill.approved {
        background-color: #dcfce7;
        color: #166534;
    }

    .dark .stock-number-pill {
        background-color: #334155;
        color: #e2e8f0;
    }

    .dark .stock-number-pill.approved {
        background-color: rgba(34, 197, 94, 0.2);
        color: #86efac;
    }

    .stock-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.01em;
        line-height: 1;
        padding: 0.35rem 0.6rem;
        text-transform: capitalize;
    }

    .stock-status-pendente {
        background-color: #fef9c3;
        color: #854d0e;
    }

    .stock-status-aprovado {
        background-color: #dcfce7;
        color: #166534;
    }

    .stock-status-rejeitado {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .stock-status-em_transferencia {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .stock-status-concluido,
    .stock-status-cancelado {
        background-color: #e2e8f0;
        color: #334155;
    }

    .dark .stock-status-pendente {
        background-color: rgba(250, 204, 21, 0.24);
        color: #fde047;
    }

    .dark .stock-status-aprovado {
        background-color: rgba(34, 197, 94, 0.2);
        color: #86efac;
    }

    .dark .stock-status-rejeitado {
        background-color: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    .dark .stock-status-em_transferencia {
        background-color: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }

    .dark .stock-status-concluido,
    .dark .stock-status-cancelado {
        background-color: #334155;
        color: #cbd5e1;
    }

    .stock-mini-flag {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        line-height: 1;
        padding: 0.28rem 0.5rem;
    }

    .stock-mini-flag.reserved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .stock-mini-flag.transfer {
        background-color: #ffedd5;
        color: #9a3412;
    }

    .dark .stock-mini-flag.reserved {
        background-color: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
    }

    .dark .stock-mini-flag.transfer {
        background-color: rgba(249, 115, 22, 0.2);
        color: #fdba74;
    }

    .stock-date-cell {
        color: var(--stock-muted);
        font-size: 0.78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .stock-notes {
        max-width: 16rem;
        color: var(--stock-muted);
        font-size: 0.79rem;
        font-weight: 500;
    }

    .stock-row-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.42rem;
    }

    .stock-row-action {
        border-radius: 0.65rem;
        border: 1px solid transparent;
        color: #ffffff !important;
        font-size: 0.72rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: 0.01em;
        padding: 0.44rem 0.62rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: filter 0.18s ease;
        white-space: nowrap;
    }

    .stock-row-action svg {
        width: 0.78rem;
        height: 0.78rem;
    }

    .stock-row-action:hover {
        filter: brightness(1.06);
    }

    .stock-row-action.approve {
        background-color: #16a34a;
    }

    .stock-row-action.reject {
        background-color: #dc2626;
    }

    .stock-row-action.receipt {
        background-color: #2563eb;
    }

    .stock-empty-state {
        color: var(--stock-muted);
    }

    .transfer-modal-overlay {
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(3px);
        overflow-y: auto;
    }

    .transfer-modal-panel {
        border: 1px solid #dbe2ea;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 22px 48px -24px rgba(15, 23, 42, 0.52);
        max-height: calc(100vh - 1.5rem);
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dark .transfer-modal-panel {
        border-color: #334155;
        background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
        box-shadow: 0 18px 38px -22px rgba(2, 6, 23, 0.8);
    }

    .transfer-modal-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%);
        border-bottom: 1px solid #dbeafe;
        padding: 1.1rem 1.25rem;
    }

    .dark .transfer-modal-head {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.16) 0%, rgba(67, 56, 202, 0.2) 100%);
        border-bottom-color: #334155;
    }

    .transfer-modal-title {
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0;
    }

    .dark .transfer-modal-title {
        color: #e2e8f0;
    }

    .transfer-modal-subtitle {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        margin-top: 0.2rem;
    }

    .dark .transfer-modal-subtitle {
        color: #94a3b8;
    }

    .transfer-close-button {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        background: #ffffff;
        transition: all 0.18s ease;
    }

    .transfer-close-button:hover {
        border-color: #94a3b8;
        color: #334155;
        background: #f8fafc;
    }

    .dark .transfer-close-button {
        border-color: #475569;
        color: #94a3b8;
        background: #1f2937;
    }

    .dark .transfer-close-button:hover {
        color: #e2e8f0;
        background: #0f172a;
    }

    .transfer-form {
        padding: 1.1rem 1.25rem calc(1.2rem + env(safe-area-inset-bottom));
    }

    .transfer-field-label {
        display: block;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 0.42rem;
    }

    .dark .transfer-field-label {
        color: #94a3b8;
    }

    .transfer-field {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 0.85rem;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.96rem;
        font-weight: 500;
        min-height: 2.9rem;
        padding: 0.62rem 0.85rem;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .transfer-field:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .dark .transfer-field {
        border-color: #475569;
        background: #1f2937;
        color: #e2e8f0;
    }

    .transfer-size-card {
        border: 1px solid #dbe2ea;
        border-radius: 1rem;
        background: #f8fafc;
        padding: 0.85rem;
    }

    .dark .transfer-size-card {
        border-color: #334155;
        background: #111827;
    }

    .transfer-size-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.7rem;
    }

    .transfer-size-title {
        color: #334155;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .dark .transfer-size-title {
        color: #cbd5e1;
    }

    .transfer-summary-badges {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .transfer-summary-badge {
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.7rem;
        font-weight: 800;
        line-height: 1;
        padding: 0.33rem 0.56rem;
    }

    .dark .transfer-summary-badge {
        border-color: #475569;
        background: #1f2937;
        color: #cbd5e1;
    }

    .transfer-size-item {
        border: 1px solid #dbe2ea;
        border-radius: 0.85rem;
        background: #ffffff;
        padding: 0.55rem 0.5rem 0.62rem;
    }

    .dark .transfer-size-item {
        border-color: #334155;
        background: #1f2937;
    }

    .transfer-size-name {
        display: block;
        text-align: center;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 800;
        margin-bottom: 0.16rem;
    }

    .dark .transfer-size-name {
        color: #cbd5e1;
    }

    .transfer-size-availability {
        display: block;
        text-align: center;
        color: #94a3b8;
        font-size: 0.69rem;
        font-weight: 700;
        margin-bottom: 0.45rem;
    }

    .transfer-size-input {
        width: 100%;
        border: 1px solid #dbe2ea;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        text-align: center;
        font-size: 1.02rem;
        font-weight: 700;
        min-height: 2.45rem;
        padding: 0.5rem 0.35rem;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .transfer-size-input:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    .transfer-size-input.has-error {
        border-color: #ef4444;
        color: #b91c1c;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14);
    }

    .dark .transfer-size-input {
        border-color: #475569;
        background: #111827;
        color: #e2e8f0;
    }

    .dark .transfer-size-input.has-error {
        border-color: #f87171;
        color: #fecaca;
        box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.2);
    }

    .transfer-help-text {
        color: #64748b;
        font-size: 0.76rem;
        margin-top: 0.55rem;
    }

    .dark .transfer-help-text {
        color: #94a3b8;
    }

    .transfer-feedback {
        min-height: 1.15rem;
        font-size: 0.75rem;
        font-weight: 700;
        margin-top: -0.2rem;
    }

    .transfer-feedback.muted {
        color: #64748b;
    }

    .transfer-feedback.success {
        color: #059669;
    }

    .transfer-feedback.error {
        color: #dc2626;
    }

    .dark .transfer-feedback.muted {
        color: #94a3b8;
    }

    .dark .transfer-feedback.success {
        color: #34d399;
    }

    .dark .transfer-feedback.error {
        color: #f87171;
    }

    .transfer-footer {
        display: flex;
        flex-direction: column-reverse;
        gap: 0.7rem;
    }

    .transfer-cancel-button,
    .transfer-submit-button {
        min-height: 2.7rem;
        border-radius: 0.85rem;
        font-size: 1rem;
        font-weight: 800;
        transition: all 0.18s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .transfer-cancel-button {
        border: 1px solid #cbd5e1;
        color: #475569;
        background: #ffffff;
    }

    .transfer-cancel-button:hover {
        background: #f8fafc;
        color: #334155;
    }

    .dark .transfer-cancel-button {
        border-color: #475569;
        color: #cbd5e1;
        background: #1f2937;
    }

    .dark .transfer-cancel-button:hover {
        background: #0f172a;
    }

    .transfer-submit-button {
        border: 1px solid #6d28d9;
        color: #ffffff;
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        box-shadow: 0 10px 20px -14px rgba(109, 40, 217, 0.75);
    }

    .transfer-submit-button:hover {
        filter: brightness(1.05);
    }

    .transfer-submit-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        filter: grayscale(0.1);
    }

    .transfer-submit-button.decrement {
        border-color: #dc2626;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 10px 20px -14px rgba(220, 38, 38, 0.75);
    }

    @media (max-width: 1024px) {
        .stock-toolbar-button {
            font-size: 0.78rem;
            padding: 0.58rem 0.85rem;
        }

        .stock-summary-chip {
            font-size: 0.7rem;
            padding: 0.35rem 0.62rem;
        }

        .transfer-modal-head,
        .transfer-form {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    @media (max-width: 768px) {
        .stock-table-topbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .stock-scroll-hint {
            font-size: 0.7rem;
        }

        .stock-requests-table {
            min-width: 1200px;
        }

        .transfer-footer {
            display: flex;
            flex-direction: column-reverse;
        }
    }
</style>
<script>
    // Função stub para evitar erros
    (function() {
        window.openAddProductModal = function() {
            return false;
        };
    })();
    
    // Definir funções globais imediatamente
    // Definir funções globais imediatamente
    window.approveRequest = function(requestsJson, isBroadcast) {
        let requests;
        try {
            requests = typeof requestsJson === 'string' ? JSON.parse(requestsJson) : requestsJson;
        } catch (e) {
            console.error('Erro ao processar solicitações:', e);
            return;
        }

        const modal = document.getElementById('approve-request-id');
        const approveModal = document.getElementById('approve-modal');
        const storeSelectContainer = document.getElementById('approve-fulfilling-store-container');
        const storeSelect = document.getElementById('approve-fulfilling-store');
        const sizesContainer = document.getElementById('approve-sizes-container');

        if (requests && requests.length > 0 && approveModal) {
            const firstRequest = requests[0];
            modal.value = firstRequest.id;
            
            if (isBroadcast && storeSelectContainer) {
                storeSelectContainer.style.display = 'block';
                if(storeSelect) storeSelect.required = true;
            } else if (storeSelectContainer) {
                storeSelectContainer.style.display = 'none';
                if(storeSelect) storeSelect.required = false;
            }

            // Limpar e preencher tamanhos
            if (sizesContainer) {
                sizesContainer.innerHTML = '';
                requests.forEach(req => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-600';
                    div.innerHTML = `
                        <div class="flex flex-col">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">${req.size}</span>
                            <span class="text-[10px] text-gray-500">Solicitado: ${req.requested_quantity}</span>
                        </div>
                        <input type="number" name="items[${req.id}]" value="${req.requested_quantity}" min="0" 
                               class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-2 focus:ring-green-500 approve-qty-input"
                               data-requested="${req.requested_quantity}">
                    `;
                    sizesContainer.appendChild(div);
                });
            }

            // Update form action
            const form = document.getElementById('approve-form');
            if (form) {
                form.action = `/stock-requests/${firstRequest.id}/approve`;
            }

            approveModal.classList.remove('hidden');
        }
    };

    window.approveAllInModal = function() {
        const inputs = document.querySelectorAll('.approve-qty-input');
        inputs.forEach(input => {
            input.value = input.getAttribute('data-requested');
        });
    };
    
    window.approveAllGroup = function(requests) {
        const requestsInput = document.getElementById('approve-group-requests');
        const modal = document.getElementById('approve-group-modal');
        if (requestsInput && modal) {
            requestsInput.value = JSON.stringify(requests);
            modal.classList.remove('hidden');
            const useRequestedCheckbox = document.getElementById('approve-group-use-requested');
            const customQuantityContainer = document.getElementById('approve-group-custom-quantity-container');
            if (useRequestedCheckbox && customQuantityContainer) {
                useRequestedCheckbox.checked = true;
                customQuantityContainer.style.display = 'none';
            }
        }
    };
    
    window.rejectRequest = function(id) {
        const modal = document.getElementById('reject-request-id');
        const rejectModal = document.getElementById('reject-modal');
        if (modal && rejectModal) {
            modal.value = id;
            rejectModal.classList.remove('hidden');
        }
    };
    
    window.rejectRequestGroup = function(ids) {
        const idsInput = document.getElementById('reject-group-ids');
        const modal = document.getElementById('reject-group-modal');
        if (idsInput && modal) {
            idsInput.value = JSON.stringify(ids);
            modal.classList.remove('hidden');
        }
    };
    
    window.completeRequest = function(id) {
        const modal = document.getElementById('complete-request-id');
        const completeModal = document.getElementById('complete-modal');
        if (modal && completeModal) {
            modal.value = id;
            completeModal.classList.remove('hidden');
        }
    };
</script>

@php
    $pageRequests = $requests->getCollection();
    $pendingCount = $pageRequests->where('status', 'pendente')->count();
    $approvedCount = $pageRequests->whereIn('status', ['aprovado', 'em_transferencia', 'concluido'])->count();
    $rejectedCount = $pageRequests->where('status', 'rejeitado')->count();
@endphp

<div class="stock-requests-page space-y-5">
    <!-- Header -->
    <div class="stock-toolbar rounded-2xl p-4 sm:p-5 lg:p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-3">
                <span class="stock-toolbar-label">Fluxo de Separação</span>
                <div>
                    <h1 class="text-2xl sm:text-[1.65rem] font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Solicitações de Estoque</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Acompanhe aprovações, pendências e transferências em um único painel.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="stock-summary-chip">
                        Exibidas
                        <span class="value">{{ $pageRequests->count() }}</span>
                    </span>
                    <span class="stock-summary-chip">
                        Pendentes
                        <span class="value">{{ $pendingCount }}</span>
                    </span>
                    <span class="stock-summary-chip">
                        Aprovadas
                        <span class="value">{{ $approvedCount }}</span>
                    </span>
                    <span class="stock-summary-chip">
                        Rejeitadas
                        <span class="value">{{ $rejectedCount }}</span>
                    </span>
                    <span class="stock-summary-chip">
                        Total geral
                        <span class="value">{{ number_format($requests->total(), 0, ',', '.') }}</span>
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(!Auth::user()->isVendedor())
                <button onclick="openRequestTransferModal()" class="stock-toolbar-button transfer">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Transferência
                </button>
                <button onclick="openRequestOrderModal()" class="stock-toolbar-button order">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Para Pedido
                </button>
                <button onclick="openRequestDecrementModal()" class="stock-toolbar-button decrement">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Retirada
                </button>
                @endif
                <a href="{{ Auth::user()->isVendedor() ? route('stocks.view') : route('stocks.index') }}" class="stock-toolbar-button stock">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Ver Estoque
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-100 dark:border-green-400 rounded-lg shadow" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-100 dark:border-red-400 rounded-lg shadow" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filtros -->
    <div class="stock-filter-panel">
        <form method="GET" action="{{ route('stock-requests.index') }}" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
            <div>
                <label class="stock-filter-label">Status</label>
                <select name="status" class="stock-filter-input">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="stock-filter-label">Loja</label>
                <select name="store_id" class="stock-filter-input">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 lg:justify-end">
                <button type="submit" class="stock-filter-cta">
                    Filtrar
                </button>
                @if(request('status') || request('store_id'))
                    <a href="{{ route('stock-requests.index') }}" class="stock-filter-clear">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="stock-table-shell">
        <div class="stock-table-topbar">
            <div>
                <h2 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100">Lista de solicitações</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visualização consolidada por pedido, produto e status.</p>
            </div>
            <span class="stock-scroll-hint">Role na horizontal para ver todas as colunas</span>
        </div>
        <div class="stock-table-wrapper">
        <table class="stock-requests-table">
            <thead>
                <tr>
                    <th class="sticky-left text-left">ID</th>
                    <th class="text-left">Origem <span class="text-gray-400">→</span> Destino</th>
                    <th class="text-left">Produto</th>
                    <th class="text-left">Tamanhos</th>
                    <th class="text-center">Solic.</th>
                    <th class="text-center">Aprov.</th>
                    <th class="text-center">Status</th>
                    <th class="text-left">Aprovado por</th>
                    <th class="text-left">Data aprov.</th>
                    <th class="text-left">Data solic.</th>
                    <th class="text-left">Observações</th>
                    <th class="sticky-right text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $group)
                <tr>
                    <td class="sticky-left">
                        @if($group['order_id'])
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="stock-id-badge">
                                    @php
                                        $order = $group['order'] ?? null;
                                        $isPdv = $order && $order->is_pdv;
                                    @endphp
                                    @if($isPdv)
                                        Venda #{{ str_pad($group['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    @else
                                        Pedido #{{ str_pad($group['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    @endif
                                </span>
                                @if($isPdv)
                                <span class="stock-tag-pdv">
                                    PDV
                                </span>
                                @endif
                            </div>
                        @else
                            <span class="stock-id-badge">#{{ $group['requests'][0]->id }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex flex-col gap-1">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $group['requesting_store']->name }}</span>
                            
                            @if($group['target_store'])
                                <div class="stock-store-secondary inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    {{ $group['target_store']->name }}
                                </div>
                            @else
                                <div class="stock-broadcast inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                    TODAS (GERAL)
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="stock-product-main">{{ $group['fabric']->name ?? '-' }}</span>
                        <span class="stock-product-sub">{{ $group['color']->name ?? '-' }} • {{ $group['cut_type']->name ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-1.5">
                            @php
                                $sizeOrder = ['PP' => 1, 'P' => 2, 'M' => 3, 'G' => 4, 'GG' => 5, 'EXG' => 6, 'G1' => 7, 'G2' => 8, 'G3' => 9];
                                uksort($group['sizes_summary'], function($a, $b) use ($sizeOrder) {
                                    return ($sizeOrder[$a] ?? 99) <=> ($sizeOrder[$b] ?? 99);
                                });
                            @endphp
                            @foreach($group['sizes_summary'] as $size => $quantity)
                                <span class="stock-size-chip">{{ $quantity }}{{ $size }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="text-center">
                        @php
                            $totalRequested = array_sum($group['sizes_summary']);
                        @endphp
                        <span class="stock-number-pill">{{ $totalRequested }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $totalApproved = 0;
                            foreach ($group['requests'] as $req) {
                                $totalApproved += $req->approved_quantity ?? 0;
                            }
                        @endphp
                        @if($totalApproved > 0)
                            <span class="stock-number-pill approved">{{ $totalApproved }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-semibold">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $status = $group['status'] ?? '';
                            $statusClasses = [
                                'pendente' => 'stock-status-pendente',
                                'aprovado' => 'stock-status-aprovado',
                                'rejeitado' => 'stock-status-rejeitado',
                                'em_transferencia' => 'stock-status-em_transferencia',
                                'concluido' => 'stock-status-concluido',
                                'cancelado' => 'stock-status-cancelado',
                            ];
                            $statusClass = $statusClasses[$status] ?? 'stock-status-concluido';
                            
                            // Verificar se alguma request do grupo tem estoque reservado
                            $hasReservedStock = false;
                            $isTransfer = false;
                            foreach ($group['requests'] as $req) {
                                if ($req->is_stock_reserved) {
                                    $hasReservedStock = true;
                                }
                                if ($req->is_transfer) {
                                    $isTransfer = true;
                                }
                            }
                        @endphp
                        <div class="flex flex-col gap-1 items-center">
                            <span class="stock-status-chip {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                            @if($hasReservedStock && $status === 'pendente')
                                <span class="stock-mini-flag reserved" title="Estoque já reservado automaticamente">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Reservado
                                </span>
                            @endif
                            @if($isTransfer)
                                <span class="stock-mini-flag transfer" title="Transferência entre lojas">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    Transf.
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($group['approved_by'] ?? null)
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $group['approved_by']->name ?? '-' }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-semibold">-</span>
                        @endif
                    </td>
                    <td class="stock-date-cell">
                        @php
                            $approvedAt = $group['approved_at'] ?? null;
                        @endphp
                        @if($approvedAt && ($approvedAt instanceof \Carbon\Carbon || $approvedAt instanceof \DateTime))
                            <span>{{ \Carbon\Carbon::parse($approvedAt)->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="stock-date-cell">
                        {{ $group['created_at']->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @php
                            $notes = $group['request_notes'] ?? null;
                            if (!$notes) {
                                foreach ($group['requests'] as $req) {
                                    if ($req->request_notes) {
                                        $notes = $req->request_notes;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if($notes)
                            <span class="stock-notes truncate block" title="{{ $notes }}">{{ Str::limit($notes, 42) }}</span>
                        @elseif($group['status'] === 'rejeitado' && ($group['rejection_reason'] ?? null))
                            <span class="stock-notes truncate block text-red-500 font-semibold" title="Motivo da Rejeição: {{ $group['rejection_reason'] }}">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ Str::limit($group['rejection_reason'], 42) }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 font-semibold">-</span>
                        @endif
                    </td>
                    <td class="sticky-right">
                        <div class="stock-row-actions">
                            @if($group['status'] === 'pendente')
                                @if(!Auth::user()->isVendedor())
                                <button onclick="approveRequest({{ json_encode($group['requests']) }}, {{ $group['target_store'] ? 'false' : 'true' }})" 
                                        class="stock-row-action approve"
                                        title="Aprovar">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Aprovar
                                </button>
                                <button onclick="rejectRequestGroup({{ json_encode(array_map(fn($r) => $r->id, $group['requests'])) }})" 
                                        class="stock-row-action reject"
                                        title="Rejeitar">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Rejeitar
                                </button>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold">Aguardando aprovação</span>
                                @endif
                            @elseif(in_array($group['status'], ['aprovado', 'em_transferencia', 'concluido']))
                                {{-- Botão para baixar comprovante de conferência --}}
                                <a href="{{ route('stock-requests.receipt', $group['requests'][0]->id) }}" 
                                   target="_blank"
                                   class="stock-row-action receipt"
                                   title="Baixar Termo de Conferência">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </a>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 font-semibold">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="px-4 py-14 text-center">
                        <div class="stock-empty-state">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm font-semibold">Nenhuma solicitação encontrada</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <!-- Paginação -->
    @if($requests->hasPages())
    <div class="pt-2 flex justify-center">
        {{ $requests->links() }}
    </div>
    @endif
</div>

<!-- Modais -->
<!-- Modal de Aprovação -->
<div id="approve-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aprovar Solicitação</h3>
        <form id="approve-form" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" id="approve-request-id" name="id">
            
            <div class="mb-4" id="approve-fulfilling-store-container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja de Origem (Atendimento):</label>
                <select id="approve-fulfilling-store" name="fulfilling_store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja...</option>
                    @foreach($stores as $store)
                        @if(\App\Helpers\StoreHelper::canAccessStore($store->id))
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Selecione de qual loja sairá o estoque.</p>
            </div>
            
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tamanhos e Quantidades:</label>
                    <button type="button" onclick="approveAllInModal()" class="text-xs text-indigo-600 hover:underline">Preencher Tudo</button>
                </div>
                <div id="approve-sizes-container" class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto pr-1">
                    <!-- Dinamicamente preenchido -->
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="approve-notes" name="approval_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="approve-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Aprovar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Rejeição -->
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rejeitar Solicitação</h3>
        <form id="reject-form">
            <input type="hidden" id="reject-request-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo:</label>
                <textarea id="reject-reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="reject-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Rejeitar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Transferência -->
<div id="request-transfer-modal" class="transfer-modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="transfer-modal-panel w-full max-w-3xl my-auto">
        <div class="transfer-modal-head">
            <div>
                <h3 class="transfer-modal-title">Solicitar Transferência</h3>
                <p class="transfer-modal-subtitle">Defina origem, destino e quantidades por tamanho.</p>
            </div>
            <button type="button" onclick="closeRequestTransferModal()" class="transfer-close-button" aria-label="Fechar modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="request-transfer-form" onsubmit="submitRequestTransfer(event)" class="transfer-form space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="transfer-field-label">Loja Solicitante</label>
                    <select id="transfer-requesting-store" name="requesting_store_id" required class="transfer-field">
                        <option value="">Selecione a loja</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Loja de Destino</label>
                    <select id="transfer-target-store" name="target_store_id" required class="transfer-field">
                        <option value="">Selecione a loja</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="transfer-field-label">Tecido</label>
                    <select id="transfer-fabric" name="fabric_id" class="transfer-field">
                        <option value="">Selecione o tecido</option>
                        @foreach($fabrics as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Cor</label>
                    <select id="transfer-color" name="color_id" class="transfer-field">
                        <option value="">Selecione a cor</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Tipo de Corte</label>
                    <select id="transfer-cut-type" name="cut_type_id" class="transfer-field">
                        <option value="">Selecione o tipo de corte</option>
                        @foreach($cutTypes as $cutType)
                            <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="transfer-size-card">
                <div class="transfer-size-head">
                    <span class="transfer-size-title">Quantidades por tamanho</span>
                    <div class="transfer-summary-badges">
                        <span class="transfer-summary-badge" id="transfer-selected-sizes">0 tamanhos</span>
                        <span class="transfer-summary-badge" id="transfer-selected-total">0 peças</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($sizes as $size)
                    <div class="transfer-size-item">
                        <label class="transfer-size-name">{{ $size }}</label>
                        <div id="stock-available-{{ $size }}" class="transfer-size-availability">Disp: --</div>
                        <input type="number"
                               id="transfer-size-{{ $size }}"
                               name="sizes[{{ $size }}]"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="transfer-size-input">
                    </div>
                    @endforeach
                </div>
                <p class="transfer-help-text">
                    Informe a quantidade para cada tamanho que deseja transferir. Deixe em 0 para não incluir.
                </p>
            </div>

            <p id="transfer-feedback" class="transfer-feedback muted">Selecione os campos para consultar disponibilidade em estoque.</p>

            <div>
                <label class="transfer-field-label">Observações</label>
                <textarea id="transfer-notes"
                          name="request_notes"
                          rows="3"
                          class="transfer-field"></textarea>
            </div>

            <div class="transfer-footer sm:flex-row sm:justify-end">
                <button type="button" onclick="closeRequestTransferModal()" class="transfer-cancel-button sm:min-w-[190px]">
                    Cancelar
                </button>
                <button id="transfer-submit-btn" type="submit" class="transfer-submit-button sm:min-w-[220px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span id="transfer-submit-label">Solicitar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar para Pedido -->
<div id="request-order-modal" class="transfer-modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="transfer-modal-panel w-full max-w-3xl my-auto max-h-[92vh] overflow-y-auto">
        <div class="transfer-modal-head">
            <div>
                <h3 class="transfer-modal-title">Solicitar para Pedido</h3>
                <p class="transfer-modal-subtitle">Vincule a solicitação a um pedido e informe os tamanhos.</p>
            </div>
            <button type="button" onclick="closeRequestOrderModal()" class="transfer-close-button" aria-label="Fechar modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-order-form" onsubmit="submitRequestOrder(event)" class="transfer-form space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="transfer-field-label">Pedido</label>
                    <select id="order-id" name="order_id" required class="transfer-field">
                        <option value="">Selecione o pedido</option>
                        @foreach($recentOrders as $order)
                            <option value="{{ $order->id }}"
                                    data-default-store="{{ $order->default_store_id ?? '' }}"
                                    data-default-fabric="{{ e($order->default_fabric_id ?? $order->default_fabric ?? '') }}"
                                    data-default-color="{{ e($order->default_color_id ?? $order->default_color ?? '') }}"
                                    data-default-cut-type="{{ e($order->default_cut_type_id ?? $order->default_cut_type ?? '') }}">
                                Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Loja Solicitante</label>
                    <select id="order-requesting-store" name="requesting_store_id" required class="transfer-field">
                        <option value="">Selecione a loja</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="transfer-field-label">Tecido</label>
                    <select id="order-fabric" name="fabric_id" class="transfer-field">
                        <option value="">Selecione o tecido</option>
                        @foreach($fabrics as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Cor</label>
                    <select id="order-color" name="color_id" class="transfer-field">
                        <option value="">Selecione a cor</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Tipo de Corte</label>
                    <select id="order-cut-type" name="cut_type_id" class="transfer-field">
                        <option value="">Selecione o tipo de corte</option>
                        @foreach($cutTypes as $cutType)
                            <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="transfer-size-card">
                <div class="transfer-size-head">
                    <span class="transfer-size-title">Quantidades por tamanho</span>
                    <div class="transfer-summary-badges">
                        <span class="transfer-summary-badge" id="order-selected-sizes">0 tamanhos</span>
                        <span class="transfer-summary-badge" id="order-selected-total">0 peças</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($sizes as $size)
                    <div class="transfer-size-item">
                        <label class="transfer-size-name">{{ $size }}</label>
                        <span id="order-stock-available-{{ $size }}" class="transfer-size-availability">Disp: --</span>
                        <input type="number"
                               id="order-size-{{ $size }}"
                               name="sizes[{{ $size }}]"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="transfer-size-input">
                    </div>
                    @endforeach
                </div>
                <p class="transfer-help-text">
                    Informe a quantidade para cada tamanho que deseja solicitar. Deixe em 0 para não incluir.
                </p>
            </div>

            <p id="order-feedback" class="transfer-feedback muted">Preencha pedido, loja e quantidades para enviar a solicitação.</p>
            
            <div>
                <label class="transfer-field-label">Observações</label>
                <textarea id="order-notes"
                          name="request_notes"
                          rows="3"
                          class="transfer-field"></textarea>
            </div>
            
            <div class="transfer-footer sm:flex-row sm:justify-end">
                <button type="button" onclick="closeRequestOrderModal()" class="transfer-cancel-button sm:min-w-[190px]">
                    Cancelar
                </button>
                <button id="order-submit-btn" type="submit" class="transfer-submit-button sm:min-w-[220px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span id="order-submit-label">Solicitar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Retirada -->
<div id="request-decrement-modal" class="transfer-modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="transfer-modal-panel w-full max-w-3xl my-auto max-h-[92vh] overflow-y-auto">
        <div class="transfer-modal-head">
            <div>
                <h3 class="transfer-modal-title">Solicitar Retirada</h3>
                <p class="transfer-modal-subtitle">Consulte o estoque atual e informe as quantidades para retirada.</p>
            </div>
            <button type="button" onclick="closeRequestDecrementModal()" class="transfer-close-button" aria-label="Fechar modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="request-decrement-form" onsubmit="submitRequestDecrement(event)" class="transfer-form space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="transfer-field-label">Loja Solicitante</label>
                    <select id="decrement-store" name="requesting_store_id" required class="transfer-field">
                        <option value="">Selecione a loja</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Tecido</label>
                    <select id="decrement-fabric" name="fabric_id" required class="transfer-field">
                        <option value="">Selecione o tecido</option>
                        @foreach($fabrics as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Cor</label>
                    <select id="decrement-color" name="color_id" required class="transfer-field">
                        <option value="">Selecione a cor</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="transfer-field-label">Tipo de Corte</label>
                    <select id="decrement-cut-type" name="cut_type_id" required class="transfer-field">
                        <option value="">Selecione o tipo de corte</option>
                        @foreach($cutTypes as $cutType)
                            <option value="{{ $cutType->id }}">{{ $cutType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="transfer-size-card">
                <div class="transfer-size-head">
                    <span class="transfer-size-title">Quantidades por tamanho</span>
                    <div class="transfer-summary-badges">
                        <span id="decrement-selected-sizes" class="transfer-summary-badge">0 tamanhos</span>
                        <span id="decrement-selected-total" class="transfer-summary-badge">0 peças</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($sizes as $size)
                    <div class="transfer-size-item">
                        <label class="transfer-size-name">{{ $size }}</label>
                        <span id="decrement-stock-available-{{ $size }}" class="transfer-size-availability">Disp: --</span>
                        <input type="number"
                               id="decrement-size-{{ $size }}"
                               name="sizes[{{ $size }}]"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="transfer-size-input">
                    </div>
                    @endforeach
                </div>
                <p class="transfer-help-text">
                    Informe a quantidade para cada tamanho que deseja retirar. Deixe em 0 para não incluir.
                </p>
            </div>

            <p id="decrement-feedback" class="transfer-feedback muted">Selecione loja, tecido, cor e tipo de corte para consultar disponibilidade.</p>

            <div>
                <label class="transfer-field-label">Observações</label>
                <textarea id="decrement-notes"
                          name="request_notes"
                          rows="3"
                          class="transfer-field"></textarea>
            </div>

            <div class="transfer-footer sm:flex-row sm:justify-end">
                <button type="button" onclick="closeRequestDecrementModal()" class="transfer-cancel-button sm:min-w-[190px]">
                    Cancelar
                </button>
                <button id="decrement-submit-btn" type="submit" class="transfer-submit-button decrement sm:min-w-[220px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m0 0l4-4m-4 4l4 4"></path>
                    </svg>
                    <span id="decrement-submit-label">Solicitar Retirada</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Conclusão -->
<div id="complete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Concluir Transferência</h3>
        <form id="complete-form">
            <input type="hidden" id="complete-request-id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade Transferida:</label>
                <input type="number" id="complete-quantity" min="1" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="complete-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Concluir</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Aprovação em Grupo -->
<div id="approve-group-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Aprovar Grupo</h3>
        <form id="approve-group-form">
            <input type="hidden" id="approve-group-requests">
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <input type="checkbox" id="approve-group-use-requested" checked class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label class="text-sm text-gray-700 dark:text-gray-300">Usar quantidade solicitada</label>
                </div>
            </div>
            <div class="mb-4" id="approve-group-custom-quantity-container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade Personalizada:</label>
                <input type="number" id="approve-group-quantity" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="approve-group-notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="approve-group-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Aprovar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Rejeição em Grupo -->
<div id="reject-group-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rejeitar Grupo</h3>
        <form id="reject-group-form">
            <input type="hidden" id="reject-group-ids">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo:</label>
                <textarea id="reject-group-reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" data-modal-close="reject-group-modal" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Rejeitar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation para botões de ação
        document.addEventListener('click', function(e) {
            const button = e.target.closest('[data-action]');
            if (button) {
                const action = button.getAttribute('data-action');
                const id = button.getAttribute('data-id');
                const ids = button.getAttribute('data-ids');
                
                switch(action) {
                    case 'approve':
                        if (id && window.approveRequest) {
                            window.approveRequest(parseInt(id));
                        }
                        break;
                    case 'approve-group':
                        if (button.getAttribute('data-requests') && window.approveAllGroup) {
                            try {
                                const requests = JSON.parse(button.getAttribute('data-requests'));
                                window.approveAllGroup(requests);
                            } catch (e) {
                                console.error('Erro ao parsear requests:', e);
                            }
                        }
                        break;
                    case 'reject':
                        if (id && window.rejectRequest) {
                            window.rejectRequest(parseInt(id));
                        }
                        break;
                    case 'reject-group':
                        if (ids && window.rejectRequestGroup) {
                            try {
                                const idsArray = JSON.parse(ids);
                                window.rejectRequestGroup(idsArray);
                            } catch (e) {
                                console.error('Erro ao parsear IDs:', e);
                            }
                        }
                        break;
                    case 'complete':
                        if (id && window.completeRequest) {
                            window.completeRequest(parseInt(id));
                        }
                        break;
                }
            }
            
            const closeButton = e.target.closest('[data-modal-close]');
            if (closeButton) {
                const modalId = closeButton.getAttribute('data-modal-close');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            }
        });
        
        // Event listeners para formulários
        const approveGroupForm = document.getElementById('approve-group-form');
        if (approveGroupForm) {
            approveGroupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitApproveGroup(e);
            });
        }
        
        const rejectGroupForm = document.getElementById('reject-group-form');
        if (rejectGroupForm) {
            rejectGroupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitRejectGroup(e);
            });
        }
        
        const approveForm = document.getElementById('approve-form');
        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitApprove(e);
            });
        }
        
        const rejectForm = document.getElementById('reject-form');
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitReject(e);
            });
        }
        
        const completeForm = document.getElementById('complete-form');
        if (completeForm) {
            completeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitComplete(e);
            });
        }
        
        // Checkbox de usar quantidade solicitada
        const useRequestedCheckbox = document.getElementById('approve-group-use-requested');
        const customQuantityContainer = document.getElementById('approve-group-custom-quantity-container');
        
        if (useRequestedCheckbox && customQuantityContainer) {
            useRequestedCheckbox.addEventListener('change', function() {
                customQuantityContainer.style.display = this.checked ? 'none' : 'block';
            });
        }
    });
    
    function submitApproveGroup(event) {
        event.preventDefault();
        const requestIds = JSON.parse(document.getElementById('approve-group-requests').value);
        const useRequested = document.getElementById('approve-group-use-requested').checked;
        const customQuantityInput = document.getElementById('approve-group-quantity').value;
        const customQuantity = customQuantityInput ? parseInt(customQuantityInput) : null;
        const notes = document.getElementById('approve-group-notes').value;
        
        if (!useRequested && (!customQuantity || customQuantity <= 0)) {
            showNotification('Informe uma quantidade válida', 'error');
            return;
        }
        
        let successCount = 0;
        let errorCount = 0;
        const promises = requestIds.map(requestId => {
            // Se useRequested é true, não enviamos quantidade customizada
            // O backend vai usar a quantidade solicitada
            const approvalData = {
                approval_notes: notes || 'Aprovação em grupo'
            };
            
            if (!useRequested && customQuantity) {
                approvalData.approved_quantity = customQuantity;
            }
            
            return fetch(`/stock-requests/${requestId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(approvalData)
            })
            .then(async res => {
                const data = await res.json();
                if (res.ok && data.success) {
                    successCount++;
                    // Guardar URL do PDF para abrir ao final (basta um para o lote)
                    if (data.pdf_url) {
                        window.latestPdfUrl = data.pdf_url;
                    }
                } else {
                    errorCount++;
                    console.error('Erro ao aprovar solicitação:', data.message || data.errors);
                }
            })
            .catch(error => {
                errorCount++;
                console.error('Erro ao aprovar solicitação:', error);
            });
        });
        
        Promise.all(promises).then(() => {
            document.getElementById('approve-group-modal').classList.add('hidden');
            if (successCount > 0) {
                const msg = `${successCount} solicitação(ões) aprovada(s) com sucesso!${errorCount > 0 ? ' ' + errorCount + ' erro(s).' : ''}`;
                showNotification(msg, 'success');
                
                if (window.latestPdfUrl) {
                    window.open(window.latestPdfUrl, '_blank');
                }
                
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Nenhuma solicitação foi aprovada. Verifique os erros no console.', 'error');
            }
        });
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg max-w-md ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
    
    function submitApprove(event) {
        event.preventDefault();
        const id = document.getElementById('approve-request-id').value;
        const form = document.getElementById('approve-form');
        const formData = new FormData(form);
        
        const body = {};
        let hasQuantity = false;
        
        formData.forEach((value, key) => {
            if (key.includes('items[')) {
                if (!body.items) body.items = {};
                const itemId = key.match(/\[(\d+)\]/)[1];
                const qty = parseInt(value) || 0;
                body.items[itemId] = qty;
                if (qty > 0) hasQuantity = true;
            } else {
                body[key] = value;
            }
        });

        if (!hasQuantity) {
            showNotification('Informe pelo menos uma quantidade maior que zero', 'error');
            return;
        }

        fetch(`/stock-requests/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                document.getElementById('approve-modal').classList.add('hidden');
                showNotification('Solicitação aprovada com sucesso!', 'success');
                
                if (data.pdf_url) {
                    window.open(data.pdf_url, '_blank');
                }
                
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorMsg = data.message || data.errors || 'Erro desconhecido';
                showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao aprovar solicitação. Verifique o console para mais detalhes.', 'error');
        });
    }
    
    function submitReject(event) {
        event.preventDefault();
        const id = document.getElementById('reject-request-id').value;
        const reason = document.getElementById('reject-reason').value;
        
        if (!reason || reason.trim() === '') {
            showNotification('Informe o motivo da rejeição', 'error');
            return;
        }
        
        fetch(`/stock-requests/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rejection_reason: reason
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok && data.success) {
                document.getElementById('reject-modal').classList.add('hidden');
                showNotification('Solicitação rejeitada.', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorMsg = data.message || data.errors || 'Erro desconhecido';
                showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao rejeitar solicitação. Verifique o console para mais detalhes.', 'error');
        });
    }
    
    function submitRejectGroup(event) {
        event.preventDefault();
        const ids = JSON.parse(document.getElementById('reject-group-ids').value);
        const reason = document.getElementById('reject-group-reason').value;
        
        if (!reason || reason.trim() === '') {
            showNotification('Informe o motivo da rejeição', 'error');
            return;
        }
        
        let successCount = 0;
        let errorCount = 0;
        const promises = ids.map(id => 
            fetch(`/stock-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejection_reason: reason
                })
            })
            .then(async res => {
                const data = await res.json();
                if (res.ok && data.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            })
            .catch(() => errorCount++)
        );
        
        Promise.all(promises).then(() => {
            document.getElementById('reject-group-modal').classList.add('hidden');
            if (successCount > 0) {
                showNotification(`${successCount} solicitação(ões) rejeitada(s) com sucesso!${errorCount > 0 ? ' ' + errorCount + ' erro(s).' : ''}`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Nenhuma solicitação foi rejeitada. Verifique os erros.', 'error');
            }
        });
    }
    
    // Funções para modais de criação de solicitações
    const transferSizes = @json($sizes);

    function notifyTransfer(message, type = 'success') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function setTransferFeedback(message, type = 'muted') {
        const feedback = document.getElementById('transfer-feedback');
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.className = `transfer-feedback ${type}`;
    }

    function setTransferSubmitting(isSubmitting) {
        const submitButton = document.getElementById('transfer-submit-btn');
        const submitLabel = document.getElementById('transfer-submit-label');
        if (!submitButton || !submitLabel) return;

        submitButton.disabled = isSubmitting;
        submitLabel.textContent = isSubmitting ? 'Enviando...' : 'Solicitar';
    }

    function updateTargetStoreOptions() {
        const requestingStore = document.getElementById('transfer-requesting-store');
        const targetStore = document.getElementById('transfer-target-store');
        if (!requestingStore || !targetStore) return;

        const selectedStoreId = requestingStore.value;

        Array.from(targetStore.options).forEach(option => {
            if (!option.value) return;
            const hideOption = selectedStoreId && option.value === selectedStoreId;
            option.hidden = hideOption;
            option.disabled = hideOption;
        });

        if (selectedStoreId && targetStore.value === selectedStoreId) {
            targetStore.value = '';
        }
    }

    function resetTransferStockDisplays() {
        transferSizes.forEach(size => {
            const display = document.getElementById(`stock-available-${size}`);
            const input = document.getElementById(`transfer-size-${size}`);

            if (display) {
                display.innerText = 'Disp: --';
                display.className = 'transfer-size-availability';
            }

            if (input) {
                input.removeAttribute('max');
                input.dataset.available = '';
                input.classList.remove('has-error');
            }
        });
    }

    function syncTransferSizeSummary() {
        let selectedSizes = 0;
        let totalPieces = 0;

        transferSizes.forEach(size => {
            const input = document.getElementById(`transfer-size-${size}`);
            if (!input) return;

            const quantity = parseInt(input.value, 10) || 0;
            const available = parseInt(input.dataset.available, 10);
            const hasAvailableLimit = !Number.isNaN(available);

            if (quantity > 0) {
                selectedSizes++;
                totalPieces += quantity;
            }

            if (hasAvailableLimit && quantity > available) {
                input.classList.add('has-error');
            } else {
                input.classList.remove('has-error');
            }
        });

        const selectedSizesEl = document.getElementById('transfer-selected-sizes');
        const selectedTotalEl = document.getElementById('transfer-selected-total');
        if (selectedSizesEl) {
            selectedSizesEl.textContent = `${selectedSizes} tamanho${selectedSizes === 1 ? '' : 's'}`;
        }
        if (selectedTotalEl) {
            selectedTotalEl.textContent = `${totalPieces} peça${totalPieces === 1 ? '' : 's'}`;
        }
    }

    function bindTransferModalListeners() {
        const modal = document.getElementById('request-transfer-modal');
        if (!modal || modal.dataset.bound === 'true') {
            return;
        }

        modal.dataset.bound = 'true';

        const requestingStore = document.getElementById('transfer-requesting-store');
        if (requestingStore) {
            requestingStore.addEventListener('change', () => {
                updateTargetStoreOptions();
                fetchStockDetails();
            });
        }

        ['transfer-target-store', 'transfer-fabric', 'transfer-color', 'transfer-cut-type'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', fetchStockDetails);
            }
        });

        transferSizes.forEach(size => {
            const input = document.getElementById(`transfer-size-${size}`);
            if (input) {
                input.addEventListener('input', syncTransferSizeSummary);
            }
        });

        modal.addEventListener('click', event => {
            if (event.target === modal) {
                closeRequestTransferModal();
            }
        });
    }

    function openRequestTransferModal() {
        const modal = document.getElementById('request-transfer-modal');
        if (!modal) return;

        bindTransferModalListeners();
        updateTargetStoreOptions();
        resetTransferStockDisplays();
        syncTransferSizeSummary();
        setTransferFeedback('Selecione os filtros para consultar disponibilidade em estoque.', 'muted');
        setTransferSubmitting(false);

        modal.classList.remove('hidden');

        const requestingStore = document.getElementById('transfer-requesting-store');
        if (requestingStore) {
            requestingStore.focus();
        }
    }

    function fetchStockDetails() {
        const storeElement = document.getElementById('transfer-target-store');
        const fabricElement = document.getElementById('transfer-fabric');
        const colorElement = document.getElementById('transfer-color');
        const cutTypeElement = document.getElementById('transfer-cut-type');
        if (!storeElement || !fabricElement || !colorElement || !cutTypeElement) return;

        const storeId = storeElement.value;
        const fabricId = fabricElement.value;
        const colorId = colorElement.value;
        const cutTypeId = cutTypeElement.value;

        resetTransferStockDisplays();
        syncTransferSizeSummary();

        if (!storeId || !fabricId || !colorId || !cutTypeId) {
            setTransferFeedback('Selecione loja de destino, tecido, cor e tipo de corte para ver disponibilidade.', 'muted');
            return;
        }

        setTransferFeedback('Buscando disponibilidade em estoque...', 'muted');

        const params = new URLSearchParams({
            store_id: storeId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId
        });

        fetch(`/stocks/details?${params.toString()}`)
            .then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    throw new Error(`Resposta inválida: ${text.slice(0, 150)}`);
                }
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data?.message || 'Falha ao buscar disponibilidade');
                }
                return data;
            })
            .then(data => {
                if (data.success && data.stocks) {
                    let totalAvailable = 0;
                    data.stocks.forEach(stock => {
                        const display = document.getElementById(`stock-available-${stock.size}`);
                        const input = document.getElementById(`transfer-size-${stock.size}`);
                        const available = Number(stock.available_quantity) || 0;
                        totalAvailable += available;
                        if (display) {
                            display.innerText = `Disp: ${available}`;
                            display.className = `transfer-size-availability ${available > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'}`;
                        }
                        if (input) {
                            input.dataset.available = available.toString();
                            input.setAttribute('max', available.toString());
                        }
                    });

                    syncTransferSizeSummary();
                    setTransferFeedback(`Disponibilidade carregada: ${totalAvailable} peça(s) no total.`, 'success');
                } else {
                    setTransferFeedback('Nenhuma disponibilidade encontrada para os filtros selecionados.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro ao buscar estoque:', err);
                setTransferFeedback('Erro ao buscar disponibilidade. Tente novamente.', 'error');
            });
    }
    
    function closeRequestTransferModal() {
        const modal = document.getElementById('request-transfer-modal');
        const form = document.getElementById('request-transfer-form');

        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }

        setTransferSubmitting(false);
        resetTransferStockDisplays();
        syncTransferSizeSummary();
        setTransferFeedback('Selecione os filtros para consultar disponibilidade em estoque.', 'muted');
        updateTargetStoreOptions();
    }
    
    function submitRequestTransfer(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const requestingStoreId = formData.get('requesting_store_id');
        const targetStoreId = formData.get('target_store_id');

        if (!requestingStoreId) {
            setTransferFeedback('Selecione a loja solicitante.', 'error');
            notifyTransfer('Selecione a loja solicitante.', 'error');
            return;
        }

        if (!targetStoreId) {
            setTransferFeedback('Selecione a loja de destino.', 'error');
            notifyTransfer('Selecione a loja de destino.', 'error');
            return;
        }

        if (requestingStoreId === targetStoreId) {
            setTransferFeedback('A loja de destino deve ser diferente da loja solicitante.', 'error');
            notifyTransfer('A loja de destino deve ser diferente da loja solicitante.', 'error');
            return;
        }

        const sizes = transferSizes;
        
        // Coletar quantidades de todos os tamanhos
        const sizeQuantities = {};
        let hasQuantities = false;
        let exceedsAvailability = false;
        
        sizes.forEach(size => {
            const input = document.getElementById(`transfer-size-${size}`);
            const quantity = input ? parseInt(input.value) || 0 : 0;
            const available = input ? parseInt(input.dataset.available, 10) : NaN;

            if (input && !Number.isNaN(available) && quantity > available) {
                input.classList.add('has-error');
                exceedsAvailability = true;
            }

            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            setTransferFeedback('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            notifyTransfer('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            return;
        }

        if (exceedsAvailability) {
            setTransferFeedback('Existem quantidades acima do disponível para alguns tamanhos.', 'error');
            notifyTransfer('Existem quantidades acima do disponível para alguns tamanhos.', 'error');
            return;
        }
        
        // Criar solicitações para cada tamanho com quantidade > 0
        const requests = [];
        sizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                requests.push({
                    requesting_store_id: parseInt(requestingStoreId),
                    target_store_id: parseInt(targetStoreId),
                    fabric_id: formData.get('fabric_id') ? parseInt(formData.get('fabric_id')) : null,
                    color_id: formData.get('color_id') ? parseInt(formData.get('color_id')) : null,
                    cut_type_id: formData.get('cut_type_id') ? parseInt(formData.get('cut_type_id')) : null,
                    size: size,
                    requested_quantity: sizeQuantities[size],
                    request_notes: formData.get('request_notes') || null,
                });
            }
        });

        setTransferSubmitting(true);
        setTransferFeedback('Enviando solicitações de transferência...', 'muted');
        
        // Enviar todas as solicitações
        Promise.all(requests.map(request => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            }).then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    return { success: false, message: text ? 'Servidor retornou uma resposta inválida.' : 'Falha ao processar resposta.' };
                }
                const data = await res.json();
                if (!res.ok) {
                    return { success: false, message: data.message || 'Erro ao criar solicitação.' };
                }
                return data;
            }).catch(error => {
                return { success: false, message: error.message || 'Erro de conexão ao enviar.' };
            })
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            
            if (successCount > 0) {
                if (failCount > 0) {
                    setTransferFeedback(`${successCount} criada(s), ${failCount} falharam.`, 'error');
                    notifyTransfer(`${successCount} solicitação(ões) criada(s), ${failCount} falharam.`, 'error');
                } else {
                    setTransferFeedback(`${successCount} solicitação(ões) criada(s) com sucesso.`, 'success');
                    notifyTransfer(`${successCount} solicitação(ões) de transferência criada(s) com sucesso!`, 'success');
                }
                closeRequestTransferModal();
                location.reload();
            } else {
                setTransferFeedback(results[0]?.message || 'Erro ao criar solicitações.', 'error');
                notifyTransfer('Erro ao criar solicitações: ' + (results[0]?.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitações:', error);
            setTransferFeedback('Erro inesperado ao criar solicitações de transferência.', 'error');
            notifyTransfer('Erro ao criar solicitações de transferência.', 'error');
        })
        .finally(() => {
            setTransferSubmitting(false);
            syncTransferSizeSummary();
        });
    }
    
    const orderSizes = transferSizes;

    function notifyOrder(message, type = 'success') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function setOrderFeedback(message, type = 'muted') {
        const feedback = document.getElementById('order-feedback');
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.className = `transfer-feedback ${type}`;
    }

    function setOrderSubmitting(isSubmitting) {
        const submitButton = document.getElementById('order-submit-btn');
        const submitLabel = document.getElementById('order-submit-label');
        if (!submitButton || !submitLabel) return;

        submitButton.disabled = isSubmitting;
        submitLabel.textContent = isSubmitting ? 'Enviando...' : 'Solicitar';
    }

    function normalizeOrderOptionText(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-zA-Z0-9]+/g, ' ')
            .trim()
            .toLowerCase();
    }

    function getOrderValueCandidates(rawValue) {
        const original = (rawValue || '').toString().trim();
        if (!original) {
            return [];
        }

        const candidates = [original];
        if (original.includes('-')) {
            const parts = original.split('-').map(part => part.trim()).filter(Boolean);
            candidates.push(...parts);
            if (parts.length > 1) {
                candidates.push(parts.slice(1).join(' '));
            }
        }

        return [...new Set(candidates)];
    }

    function selectOrderOptionByValueOrText(selectId, rawValue) {
        const select = document.getElementById(selectId);
        if (!select) {
            return false;
        }

        const candidates = getOrderValueCandidates(rawValue);
        if (!candidates.length) {
            select.value = '';
            return false;
        }

        const numericCandidate = candidates.find(candidate => /^\d+$/.test(candidate));
        if (numericCandidate && select.querySelector(`option[value="${numericCandidate}"]`)) {
            select.value = numericCandidate;
            return true;
        }

        const normalizedCandidates = candidates.map(normalizeOrderOptionText).filter(Boolean);
        if (!normalizedCandidates.length) {
            return false;
        }

        let bestOption = null;
        let bestScore = -1;

        Array.from(select.options).forEach(option => {
            if (!option.value) return;

            const optionTextNorm = normalizeOrderOptionText(option.textContent || '');
            const optionValueNorm = normalizeOrderOptionText(option.value);
            if (!optionTextNorm && !optionValueNorm) return;

            normalizedCandidates.forEach(candidateNorm => {
                if (!candidateNorm) return;

                let score = -1;
                if (optionTextNorm === candidateNorm || optionValueNorm === candidateNorm) {
                    score = 1000;
                } else if (optionTextNorm.includes(candidateNorm)) {
                    score = candidateNorm.length + 100;
                } else if (candidateNorm.includes(optionTextNorm) && optionTextNorm.length >= 3) {
                    score = optionTextNorm.length + 40;
                }

                if (score > bestScore) {
                    bestScore = score;
                    bestOption = option;
                }
            });
        });

        if (bestOption && bestScore >= 0) {
            select.value = bestOption.value;
            return true;
        }

        return false;
    }

    function applyOrderDefaultsFromSelection() {
        const orderSelect = document.getElementById('order-id');
        const storeSelect = document.getElementById('order-requesting-store');
        const selectedOption = orderSelect ? orderSelect.options[orderSelect.selectedIndex] : null;
        if (!orderSelect || !selectedOption) {
            return;
        }

        if (!orderSelect.value) {
            const fabricSelect = document.getElementById('order-fabric');
            const colorSelect = document.getElementById('order-color');
            const cutTypeSelect = document.getElementById('order-cut-type');
            if (fabricSelect) fabricSelect.value = '';
            if (colorSelect) colorSelect.value = '';
            if (cutTypeSelect) cutTypeSelect.value = '';
            resetOrderStockDisplays();
            setOrderFeedback('Selecione um pedido para preencher os dados automaticamente.', 'muted');
            return;
        }

        const defaultStore = (selectedOption.dataset.defaultStore || '').trim();
        const defaultFabric = (selectedOption.dataset.defaultFabric || '').trim();
        const defaultColor = (selectedOption.dataset.defaultColor || '').trim();
        const defaultCutType = (selectedOption.dataset.defaultCutType || '').trim();

        const fabricSelect = document.getElementById('order-fabric');
        const colorSelect = document.getElementById('order-color');
        const cutTypeSelect = document.getElementById('order-cut-type');

        if (defaultStore && storeSelect?.querySelector(`option[value="${defaultStore}"]`)) {
            storeSelect.value = defaultStore;
        } else if (storeSelect && !storeSelect.value && storeSelect.options.length === 2) {
            // Quando o usuario tem acesso a apenas uma loja, seleciona automaticamente.
            storeSelect.value = storeSelect.options[1].value;
        }

        if (fabricSelect) fabricSelect.value = '';
        if (colorSelect) colorSelect.value = '';
        if (cutTypeSelect) cutTypeSelect.value = '';

        const fabricMatched = selectOrderOptionByValueOrText('order-fabric', defaultFabric);
        const colorMatched = selectOrderOptionByValueOrText('order-color', defaultColor);
        const cutTypeMatched = selectOrderOptionByValueOrText('order-cut-type', defaultCutType);

        const matchedCount = [fabricMatched, colorMatched, cutTypeMatched].filter(Boolean).length;
        if (matchedCount === 3) {
            setOrderFeedback('Pedido preenchido automaticamente. Consultando estoque...', 'success');
        } else if (matchedCount > 0) {
            setOrderFeedback('Parte dos dados foi preenchida automaticamente. Revise os campos restantes.', 'muted');
        } else {
            setOrderFeedback('Nao foi possivel identificar tecido/cor/tipo de corte automaticamente para este pedido.', 'muted');
        }

        fetchOrderStockDetails();
    }

    function resetOrderStockDisplays() {
        orderSizes.forEach(size => {
            const display = document.getElementById(`order-stock-available-${size}`);
            if (display) {
                display.textContent = 'Disp: --';
                display.className = 'transfer-size-availability';
            }
        });
    }

    function fetchOrderStockDetails() {
        const storeElement = document.getElementById('order-requesting-store');
        const fabricElement = document.getElementById('order-fabric');
        const colorElement = document.getElementById('order-color');
        const cutTypeElement = document.getElementById('order-cut-type');
        if (!storeElement || !fabricElement || !colorElement || !cutTypeElement) return;

        const storeId = storeElement.value;
        const fabricId = fabricElement.value;
        const colorId = colorElement.value;
        const cutTypeId = cutTypeElement.value;

        resetOrderStockDisplays();

        if (!storeId || !fabricId || !colorId || !cutTypeId) {
            setOrderFeedback('Selecione loja, tecido, cor e tipo de corte para consultar o estoque atual.', 'muted');
            return;
        }

        setOrderFeedback('Buscando disponibilidade atual no estoque...', 'muted');

        const params = new URLSearchParams({
            store_id: storeId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId
        });

        fetch(`/stocks/details?${params.toString()}`)
            .then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    throw new Error(`Resposta inválida: ${text.slice(0, 150)}`);
                }
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data?.message || 'Falha ao buscar disponibilidade');
                }
                return data;
            })
            .then(data => {
                const stockMap = {};
                if (data.success && Array.isArray(data.stocks)) {
                    data.stocks.forEach(stock => {
                        stockMap[stock.size] = Number(stock.available_quantity) || 0;
                    });
                }

                let inStockCount = 0;
                let noStockCount = 0;

                orderSizes.forEach(size => {
                    const available = stockMap[size] ?? 0;
                    const display = document.getElementById(`order-stock-available-${size}`);
                    if (!display) return;

                    if (available > 0) {
                        inStockCount++;
                        display.textContent = `Disp: ${available} (Em estoque)`;
                        display.className = 'transfer-size-availability text-emerald-600 dark:text-emerald-400';
                    } else {
                        noStockCount++;
                        display.textContent = 'Sem estoque';
                        display.className = 'transfer-size-availability text-red-500 dark:text-red-400';
                    }
                });

                if (inStockCount > 0) {
                    setOrderFeedback(`${inStockCount} tamanho(s) com estoque e ${noStockCount} sem estoque.`, 'success');
                } else {
                    setOrderFeedback('Nenhum tamanho com estoque disponível para os filtros selecionados.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro ao buscar estoque do pedido:', err);
                setOrderFeedback('Erro ao consultar estoque atual. Tente novamente.', 'error');
            });
    }

    function syncOrderSizeSummary() {
        let selectedSizes = 0;
        let totalPieces = 0;

        orderSizes.forEach(size => {
            const input = document.getElementById(`order-size-${size}`);
            if (!input) return;
            const quantity = parseInt(input.value, 10) || 0;

            if (quantity > 0) {
                selectedSizes++;
                totalPieces += quantity;
            }
        });

        const selectedSizesEl = document.getElementById('order-selected-sizes');
        const selectedTotalEl = document.getElementById('order-selected-total');
        if (selectedSizesEl) {
            selectedSizesEl.textContent = `${selectedSizes} tamanho${selectedSizes === 1 ? '' : 's'}`;
        }
        if (selectedTotalEl) {
            selectedTotalEl.textContent = `${totalPieces} peça${totalPieces === 1 ? '' : 's'}`;
        }
    }

    function bindOrderModalListeners() {
        const modal = document.getElementById('request-order-modal');
        if (!modal || modal.dataset.bound === 'true') {
            return;
        }

        modal.dataset.bound = 'true';

        orderSizes.forEach(size => {
            const input = document.getElementById(`order-size-${size}`);
            if (input) {
                input.addEventListener('input', syncOrderSizeSummary);
            }
        });

        ['order-requesting-store', 'order-fabric', 'order-color', 'order-cut-type'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', fetchOrderStockDetails);
            }
        });

        const orderSelect = document.getElementById('order-id');
        if (orderSelect) {
            orderSelect.addEventListener('change', applyOrderDefaultsFromSelection);
        }

        modal.addEventListener('click', event => {
            if (event.target === modal) {
                closeRequestOrderModal();
            }
        });
    }

    function openRequestOrderModal() {
        const modal = document.getElementById('request-order-modal');
        if (!modal) return;

        bindOrderModalListeners();
        syncOrderSizeSummary();
        resetOrderStockDisplays();
        setOrderFeedback('Preencha pedido, loja e quantidades para enviar a solicitação.', 'muted');
        setOrderSubmitting(false);

        modal.classList.remove('hidden');

        const orderInput = document.getElementById('order-id');
        if (orderInput) {
            orderInput.focus();
        }
    }
    
    function closeRequestOrderModal() {
        const modal = document.getElementById('request-order-modal');
        const form = document.getElementById('request-order-form');

        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }

        syncOrderSizeSummary();
        resetOrderStockDisplays();
        setOrderFeedback('Preencha pedido, loja e quantidades para enviar a solicitação.', 'muted');
        setOrderSubmitting(false);
    }
    
    function submitRequestOrder(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const orderId = formData.get('order_id');
        const requestingStoreId = formData.get('requesting_store_id');
        
        if (!orderId || orderId === '') {
            setOrderFeedback('Selecione o pedido para criar a solicitação.', 'error');
            notifyOrder('Selecione o pedido para criar a solicitação.', 'error');
            return;
        }
        
        if (!requestingStoreId || requestingStoreId === '') {
            setOrderFeedback('Selecione a loja solicitante.', 'error');
            notifyOrder('Selecione a loja solicitante.', 'error');
            return;
        }
        
        const sizeQuantities = {};
        let hasQuantities = false;
        
        orderSizes.forEach(size => {
            const input = document.getElementById(`order-size-${size}`);
            const quantity = input ? parseInt(input.value, 10) || 0 : 0;
            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            setOrderFeedback('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            notifyOrder('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            return;
        }
        
        const requests = [];
        orderSizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                const request = {
                    order_id: parseInt(orderId, 10),
                    requesting_store_id: parseInt(requestingStoreId, 10),
                    target_store_id: null,
                    size: size,
                    requested_quantity: sizeQuantities[size],
                };
                
                const fabricId = formData.get('fabric_id');
                request.fabric_id = fabricId && fabricId !== '' ? parseInt(fabricId, 10) : null;
                
                const colorId = formData.get('color_id');
                request.color_id = colorId && colorId !== '' ? parseInt(colorId, 10) : null;
                
                const cutTypeId = formData.get('cut_type_id');
                request.cut_type_id = cutTypeId && cutTypeId !== '' ? parseInt(cutTypeId, 10) : null;
                
                const notes = formData.get('request_notes');
                request.request_notes = notes && notes.trim() !== '' ? notes.trim() : null;
                
                requests.push(request);
            }
        });

        setOrderSubmitting(true);
        setOrderFeedback('Enviando solicitações para o pedido...', 'muted');
        
        Promise.all(requests.map(request => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            })
            .then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    return { success: false, message: text ? 'Servidor retornou uma resposta inválida.' : 'Falha ao processar resposta.' };
                }

                const data = await res.json();
                if (!res.ok) {
                    return { success: false, message: data.message || 'Erro desconhecido', errors: data.errors };
                }

                return data;
            })
            .catch(error => {
                return { success: false, message: error.message || 'Erro ao enviar requisição' };
            })
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            const failedRequests = results.filter(r => !r.success);
            
            if (successCount > 0) {
                if (failCount > 0) {
                    const errorMessages = failedRequests.map(r => r.message).join(', ');
                    setOrderFeedback(`${successCount} criada(s), ${failCount} falharam.`, 'error');
                    notifyOrder(`${successCount} solicitação(ões) criada(s)! ${failCount} falharam. ${errorMessages}`, 'error');
                } else {
                    setOrderFeedback(`${successCount} solicitação(ões) criada(s) com sucesso.`, 'success');
                    notifyOrder(`${successCount} solicitação(ões) para pedido criada(s) com sucesso!`, 'success');
                }
                closeRequestOrderModal();
                location.reload();
            } else {
                const errorMessages = failedRequests.map(r => r.message || 'Erro desconhecido').join('\n');
                setOrderFeedback('Erro ao criar solicitações para o pedido.', 'error');
                notifyOrder('Erro ao criar solicitações:\n' + errorMessages, 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao processar solicitações:', error);
            setOrderFeedback('Erro inesperado ao criar solicitações para o pedido.', 'error');
            notifyOrder('Erro ao criar solicitações para pedido: ' + error.message, 'error');
        })
        .finally(() => {
            setOrderSubmitting(false);
            syncOrderSizeSummary();
        });
    }
    
    const decrementSizes = transferSizes;

    function notifyDecrement(message, type = 'success') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function setDecrementFeedback(message, type = 'muted') {
        const feedback = document.getElementById('decrement-feedback');
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.className = `transfer-feedback ${type}`;
    }

    function setDecrementSubmitting(isSubmitting) {
        const submitButton = document.getElementById('decrement-submit-btn');
        const submitLabel = document.getElementById('decrement-submit-label');
        if (!submitButton || !submitLabel) return;

        submitButton.disabled = isSubmitting;
        submitLabel.textContent = isSubmitting ? 'Enviando...' : 'Solicitar Retirada';
    }

    function resetDecrementStockDisplays() {
        decrementSizes.forEach(size => {
            const display = document.getElementById(`decrement-stock-available-${size}`);
            const input = document.getElementById(`decrement-size-${size}`);

            if (display) {
                display.textContent = 'Disp: --';
                display.className = 'transfer-size-availability';
            }

            if (input) {
                input.removeAttribute('max');
                input.dataset.available = '';
                input.classList.remove('has-error');
            }
        });
    }

    function syncDecrementSizeSummary() {
        let selectedSizes = 0;
        let totalPieces = 0;
        let exceedsAvailability = false;

        decrementSizes.forEach(size => {
            const input = document.getElementById(`decrement-size-${size}`);
            if (!input) return;

            const quantity = parseInt(input.value, 10) || 0;
            const available = parseInt(input.dataset.available, 10);
            const hasAvailableLimit = !Number.isNaN(available);

            if (quantity > 0) {
                selectedSizes++;
                totalPieces += quantity;
            }

            if (hasAvailableLimit && quantity > available) {
                exceedsAvailability = true;
                input.classList.add('has-error');
            } else {
                input.classList.remove('has-error');
            }
        });

        const selectedSizesEl = document.getElementById('decrement-selected-sizes');
        const selectedTotalEl = document.getElementById('decrement-selected-total');
        if (selectedSizesEl) {
            selectedSizesEl.textContent = `${selectedSizes} tamanho${selectedSizes === 1 ? '' : 's'}`;
        }
        if (selectedTotalEl) {
            selectedTotalEl.textContent = `${totalPieces} peça${totalPieces === 1 ? '' : 's'}`;
        }

        if (exceedsAvailability) {
            setDecrementFeedback('Existem quantidades acima do disponível para alguns tamanhos.', 'error');
        } else {
            const feedback = document.getElementById('decrement-feedback');
            if (
                feedback
                && feedback.classList.contains('error')
                && feedback.textContent.includes('acima do disponível')
            ) {
                setDecrementFeedback('Ajuste as quantidades e envie a solicitação.', 'muted');
            }
        }
    }

    function fetchDecrementStockDetails() {
        const storeElement = document.getElementById('decrement-store');
        const fabricElement = document.getElementById('decrement-fabric');
        const colorElement = document.getElementById('decrement-color');
        const cutTypeElement = document.getElementById('decrement-cut-type');
        if (!storeElement || !fabricElement || !colorElement || !cutTypeElement) return;

        const storeId = storeElement.value;
        const fabricId = fabricElement.value;
        const colorId = colorElement.value;
        const cutTypeId = cutTypeElement.value;

        resetDecrementStockDisplays();
        syncDecrementSizeSummary();

        if (!storeId || !fabricId || !colorId || !cutTypeId) {
            setDecrementFeedback('Selecione loja, tecido, cor e tipo de corte para consultar disponibilidade.', 'muted');
            return;
        }

        setDecrementFeedback('Buscando disponibilidade atual no estoque...', 'muted');

        const params = new URLSearchParams({
            store_id: storeId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId
        });

        fetch(`/stocks/details?${params.toString()}`)
            .then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    throw new Error(`Resposta inválida: ${text.slice(0, 150)}`);
                }

                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data?.message || 'Falha ao buscar disponibilidade');
                }

                return data;
            })
            .then(data => {
                const stockMap = {};
                if (data.success && Array.isArray(data.stocks)) {
                    data.stocks.forEach(stock => {
                        stockMap[stock.size] = Number(stock.available_quantity) || 0;
                    });
                }

                let inStockCount = 0;
                let noStockCount = 0;
                let totalAvailable = 0;

                decrementSizes.forEach(size => {
                    const available = stockMap[size] ?? 0;
                    totalAvailable += available;

                    const display = document.getElementById(`decrement-stock-available-${size}`);
                    const input = document.getElementById(`decrement-size-${size}`);
                    if (!display || !input) return;

                    if (available > 0) {
                        inStockCount++;
                        display.textContent = `Disp: ${available} (Em estoque)`;
                        display.className = 'transfer-size-availability text-emerald-600 dark:text-emerald-400';
                    } else {
                        noStockCount++;
                        display.textContent = 'Sem estoque';
                        display.className = 'transfer-size-availability text-red-500 dark:text-red-400';
                    }

                    input.dataset.available = available.toString();
                    input.setAttribute('max', available.toString());
                });

                syncDecrementSizeSummary();

                if (inStockCount > 0) {
                    setDecrementFeedback(`${inStockCount} tamanho(s) com estoque e ${noStockCount} sem estoque. Total: ${totalAvailable} peça(s).`, 'success');
                } else {
                    setDecrementFeedback('Nenhum tamanho com estoque disponível para os filtros selecionados.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro ao buscar estoque da retirada:', err);
                setDecrementFeedback('Erro ao consultar estoque atual. Tente novamente.', 'error');
            });
    }

    function bindDecrementModalListeners() {
        const modal = document.getElementById('request-decrement-modal');
        if (!modal || modal.dataset.bound === 'true') {
            return;
        }

        modal.dataset.bound = 'true';

        decrementSizes.forEach(size => {
            const input = document.getElementById(`decrement-size-${size}`);
            if (input) {
                input.addEventListener('input', syncDecrementSizeSummary);
            }
        });

        ['decrement-store', 'decrement-fabric', 'decrement-color', 'decrement-cut-type'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', fetchDecrementStockDetails);
            }
        });

        modal.addEventListener('click', event => {
            if (event.target === modal) {
                closeRequestDecrementModal();
            }
        });
    }

    function openRequestDecrementModal() {
        const modal = document.getElementById('request-decrement-modal');
        if (!modal) return;

        bindDecrementModalListeners();
        syncDecrementSizeSummary();
        resetDecrementStockDisplays();
        setDecrementFeedback('Selecione loja, tecido, cor e tipo de corte para consultar disponibilidade.', 'muted');
        setDecrementSubmitting(false);

        modal.classList.remove('hidden');

        const storeSelect = document.getElementById('decrement-store');
        if (storeSelect) {
            if (!storeSelect.value && storeSelect.options.length === 2) {
                storeSelect.value = storeSelect.options[1].value;
            }
            storeSelect.focus();
        }

        fetchDecrementStockDetails();
    }
    
    function closeRequestDecrementModal() {
        const modal = document.getElementById('request-decrement-modal');
        const form = document.getElementById('request-decrement-form');

        if (modal) {
            modal.classList.add('hidden');
        }
        if (form) {
            form.reset();
        }

        syncDecrementSizeSummary();
        resetDecrementStockDisplays();
        setDecrementFeedback('Selecione loja, tecido, cor e tipo de corte para consultar disponibilidade.', 'muted');
        setDecrementSubmitting(false);
    }
    
    function submitRequestDecrement(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const requestingStoreId = formData.get('requesting_store_id');
        const fabricId = formData.get('fabric_id');
        const colorId = formData.get('color_id');
        const cutTypeId = formData.get('cut_type_id');

        if (!requestingStoreId || !fabricId || !colorId || !cutTypeId) {
            setDecrementFeedback('Preencha loja, tecido, cor e tipo de corte antes de solicitar retirada.', 'error');
            notifyDecrement('Preencha loja, tecido, cor e tipo de corte antes de solicitar retirada.', 'error');
            return;
        }

        const sizeQuantities = {};
        let hasQuantities = false;
        let exceedsAvailability = false;
        
        decrementSizes.forEach(size => {
            const input = document.getElementById(`decrement-size-${size}`);
            const quantity = input ? parseInt(input.value, 10) || 0 : 0;
            const available = input ? parseInt(input.dataset.available, 10) : NaN;

            if (input && !Number.isNaN(available) && quantity > available) {
                input.classList.add('has-error');
                exceedsAvailability = true;
            }

            if (quantity > 0) {
                sizeQuantities[size] = quantity;
                hasQuantities = true;
            }
        });
        
        if (!hasQuantities) {
            setDecrementFeedback('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            notifyDecrement('Informe pelo menos uma quantidade para algum tamanho.', 'error');
            return;
        }

        if (exceedsAvailability) {
            setDecrementFeedback('Existem quantidades acima do disponível para alguns tamanhos.', 'error');
            notifyDecrement('Existem quantidades acima do disponível para alguns tamanhos.', 'error');
            return;
        }
        
        const requests = [];
        const noteText = (formData.get('request_notes') || '').trim();
        decrementSizes.forEach(size => {
            if (sizeQuantities[size] > 0) {
                requests.push({
                    requesting_store_id: parseInt(requestingStoreId, 10),
                    target_store_id: null,
                    order_id: null,
                    fabric_id: parseInt(fabricId, 10),
                    color_id: parseInt(colorId, 10),
                    cut_type_id: parseInt(cutTypeId, 10),
                    size: size,
                    requested_quantity: sizeQuantities[size],
                    request_notes: noteText ? `[RETIRADA] ${noteText}` : '[RETIRADA]',
                });
            }
        });

        setDecrementSubmitting(true);
        setDecrementFeedback('Enviando solicitações de retirada...', 'muted');
        
        Promise.all(requests.map(request => 
            fetch('{{ route("stock-requests.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(request)
            })
            .then(async res => {
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await res.text();
                    return { success: false, message: text ? 'Servidor retornou uma resposta inválida.' : 'Falha ao processar resposta.' };
                }

                const data = await res.json();
                if (!res.ok) {
                    return { success: false, message: data.message || 'Erro ao criar solicitação.' };
                }

                return data;
            })
            .catch(error => {
                return { success: false, message: error.message || 'Erro de conexão ao enviar.' };
            })
        ))
        .then(results => {
            const successCount = results.filter(r => r.success).length;
            const failCount = results.length - successCount;
            
            if (successCount > 0) {
                if (failCount > 0) {
                    setDecrementFeedback(`${successCount} criada(s), ${failCount} falharam.`, 'error');
                    notifyDecrement(`${successCount} solicitação(ões) criada(s), ${failCount} falharam.`, 'error');
                } else {
                    setDecrementFeedback(`${successCount} solicitação(ões) criada(s) com sucesso.`, 'success');
                    notifyDecrement(`${successCount} solicitação(ões) de retirada criada(s) com sucesso!`, 'success');
                }
                closeRequestDecrementModal();
                location.reload();
            } else {
                setDecrementFeedback(results[0]?.message || 'Erro ao criar solicitações.', 'error');
                notifyDecrement('Erro ao criar solicitações: ' + (results[0]?.message || 'Erro desconhecido'), 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitações de retirada:', error);
            setDecrementFeedback('Erro inesperado ao criar solicitações de retirada.', 'error');
            notifyDecrement('Erro ao criar solicitações de retirada.', 'error');
        })
        .finally(() => {
            setDecrementSubmitting(false);
            syncDecrementSizeSummary();
        });
    }
    
    // Garantir que as funções estão disponíveis globalmente
    window.openRequestTransferModal = openRequestTransferModal;
    window.openRequestOrderModal = openRequestOrderModal;
    window.openRequestDecrementModal = openRequestDecrementModal;
    
    function submitComplete(event) {
        event.preventDefault();
        const id = document.getElementById('complete-request-id').value;
        const quantity = parseInt(document.getElementById('complete-quantity').value);
        
        fetch(`/stock-requests/${id}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                transferred_quantity: quantity
            })
        })
        .then(async res => {
            if (res.ok) {
                // Se for redirecionamento ou sucesso 200, recarregar a página
                // Como o backend agora retorna redirect()->back(), o fetch seguirá o redirect e retornará o HTML da página
                document.getElementById('complete-modal').classList.add('hidden');
                showNotification('Transferência concluída com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                // Tenta ler como JSON se der erro, caso contrário texto
                try {
                    const data = await res.json();
                    const errorMsg = data.message || data.errors || 'Erro desconhecido';
                    showNotification('Erro: ' + (typeof errorMsg === 'string' ? errorMsg : JSON.stringify(errorMsg)), 'error');
                } catch (e) {
                    showNotification('Erro ao concluir transferência.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro ao concluir transferência. Verifique o console.', 'error');
        });
    }
</script>
@endpush
@endsection
