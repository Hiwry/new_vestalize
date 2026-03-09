<style>
    .bw-shell {
        --bw-surface-from: #f3f4f8;
        --bw-surface-to: #eceff4;
        --bw-surface-border: #d8dce6;
        --bw-text-primary: #0f172a;
        --bw-text-secondary: #64748b;
        --bw-card-bg: #ffffff;
        --bw-card-border: #dde2ea;
        --bw-card-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        --bw-input-bg: #f8fafc;
        --bw-accent: #7c3aed;
        --bw-accent-strong: #6d28d9;
        --bw-success: #16a34a;
        background: linear-gradient(180deg, var(--bw-surface-from) 0%, var(--bw-surface-to) 100%);
        border: 1px solid var(--bw-surface-border);
        border-radius: 24px;
        padding: 20px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        color: var(--bw-text-primary);
    }

    .dark .bw-shell {
        --bw-surface-from: #0f172a;
        --bw-surface-to: #0b1322;
        --bw-surface-border: rgba(148, 163, 184, 0.22);
        --bw-text-primary: #e2e8f0;
        --bw-text-secondary: #94a3b8;
        --bw-card-bg: #0f172a;
        --bw-card-border: rgba(148, 163, 184, 0.2);
        --bw-card-shadow: 0 18px 40px rgba(2, 6, 23, 0.55);
        --bw-input-bg: #111827;
    }

    .bw-progress {
        background: var(--bw-card-bg);
        border: 1px solid var(--bw-card-border);
        border-radius: 18px;
        padding: 16px;
        box-shadow: var(--bw-card-shadow);
    }

    .bw-progress-track {
        background: color-mix(in srgb, var(--bw-card-border) 78%, #ffffff);
        border-radius: 999px;
        overflow: hidden;
    }

    .bw-progress-fill {
        background: linear-gradient(90deg, var(--bw-accent-strong), var(--bw-accent));
        box-shadow: 0 10px 24px rgba(124, 58, 237, 0.28);
    }

    .bw-step-badge {
        background: linear-gradient(135deg, var(--bw-accent-strong), var(--bw-accent));
        color: #ffffff;
        border: 1px solid rgba(124, 58, 237, 0.22);
        box-shadow: 0 12px 22px rgba(124, 58, 237, 0.22);
    }

    .bw-card {
        background: var(--bw-card-bg) !important;
        border: 1px solid var(--bw-card-border) !important;
        box-shadow: var(--bw-card-shadow) !important;
        border-radius: 20px !important;
    }

    .bw-card-header {
        background: color-mix(in srgb, var(--bw-card-bg) 94%, var(--bw-accent) 6%) !important;
        border-bottom: 1px solid var(--bw-card-border) !important;
    }

    .bw-panel {
        background: color-mix(in srgb, var(--bw-card-bg) 92%, var(--bw-accent) 8%);
        border: 1px solid color-mix(in srgb, var(--bw-accent) 18%, var(--bw-card-border));
        border-radius: 18px;
        box-shadow: none;
    }

    .bw-muted-panel {
        background: var(--bw-input-bg);
        border: 1px solid var(--bw-card-border);
        border-radius: 16px;
    }

    .bw-stat-card {
        background: var(--bw-card-bg);
        border: 1px solid var(--bw-card-border);
        border-radius: 18px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }

    .bw-shell input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="color"]),
    .bw-shell select,
    .bw-shell textarea {
        background: var(--bw-input-bg) !important;
        border-color: color-mix(in srgb, var(--bw-card-border) 85%, var(--bw-accent) 15%) !important;
        color: var(--bw-text-primary) !important;
    }

    .bw-shell input:focus,
    .bw-shell select:focus,
    .bw-shell textarea:focus {
        border-color: var(--bw-accent) !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.14) !important;
    }

    .bw-shell .text-gray-900,
    .bw-shell .dark\:text-white {
        color: var(--bw-text-primary) !important;
    }

    .bw-shell .text-gray-600,
    .bw-shell .text-gray-500,
    .bw-shell .dark\:text-slate-400,
    .bw-shell .dark\:text-gray-400 {
        color: var(--bw-text-secondary) !important;
    }

    .bw-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: rgba(124, 58, 237, 0.1);
        color: var(--bw-accent);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .bw-ghost-btn {
        background: var(--bw-input-bg);
        border: 1px solid var(--bw-card-border);
        color: var(--bw-text-secondary);
        border-radius: 14px;
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    }

    .bw-ghost-btn:hover {
        color: var(--bw-text-primary);
        border-color: rgba(124, 58, 237, 0.25);
    }

    .bw-primary-btn {
        background: linear-gradient(135deg, var(--bw-accent-strong), var(--bw-accent));
        color: #ffffff !important;
        border-radius: 14px;
        box-shadow: 0 12px 24px rgba(124, 58, 237, 0.22);
        transition: transform 0.18s ease, filter 0.18s ease, box-shadow 0.18s ease;
    }

    .bw-primary-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
        box-shadow: 0 16px 28px rgba(124, 58, 237, 0.26);
    }

    .bw-success-btn {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: #ffffff !important;
        border-radius: 14px;
        box-shadow: 0 12px 24px rgba(34, 197, 94, 0.2);
        transition: transform 0.18s ease, filter 0.18s ease, box-shadow 0.18s ease;
    }

    .bw-success-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
        box-shadow: 0 16px 28px rgba(34, 197, 94, 0.24);
    }

    .bw-empty-state {
        background:
            radial-gradient(circle at top, rgba(124, 58, 237, 0.12), transparent 42%),
            var(--bw-card-bg);
        border: 1px dashed color-mix(in srgb, var(--bw-accent) 28%, var(--bw-card-border));
        border-radius: 20px;
    }

    .bw-item-card {
        background: var(--bw-card-bg);
        border: 1px solid var(--bw-card-border);
        border-radius: 18px;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .bw-item-card:hover {
        transform: translateY(-1px);
        border-color: rgba(124, 58, 237, 0.26);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    }

    .bw-sidebar-card {
        position: sticky;
        top: 1.5rem;
    }

    @media (max-width: 760px) {
        .bw-shell {
            padding: 14px;
            border-radius: 18px;
        }

        .bw-progress {
            padding: 14px;
        }
    }
</style>
