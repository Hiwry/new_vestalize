@extends('layouts.admin')

@section('content')
<script>
(function() {
    function initSewingPage() {
        console.log('Initializing Sewing Page...');
        if (typeof window.loadOptions === 'function') window.loadOptions();
        if (typeof window.initSublimationForm === 'function') window.initSublimationForm();
        
        // Form submit handler
        const form = document.getElementById('sewing-form');
        if (form && !form.dataset.listenerAttached) {
            form.addEventListener('submit', function(e) {
                if (typeof window.handleSewingFormSubmit === 'function') {
                    window.handleSewingFormSubmit(e);
                }
            });
            form.dataset.listenerAttached = 'true';
        }

        // Size input listeners
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            input.addEventListener('change', function() {
                if (typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }
            });
        });

        // Wizard size input listeners
        document.querySelectorAll('.wizard-size-input').forEach(input => {
            input.addEventListener('input', function() {
                if (typeof window.calculateWizardTotal === 'function') {
                    window.calculateWizardTotal();
                }
            });
        });

        if (typeof window.updateFabricPieceSelection === 'function') {
            window.updateFabricPieceSelection();
        }
    }

    // Expose initialization for AJAX loading
    window._sewingInitSetup = function() {
        document.removeEventListener('ajax-content-loaded', initSewingPage);
        document.addEventListener('ajax-content-loaded', initSewingPage);
    };
    window._sewingInitSetup();

    // Also run on DOMContentLoaded for initial load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSewingPage);
    } else {
        initSewingPage();
    }
})();
</script>
<style>
    .ow-shell, #sewing-wizard-modal {
        --sh-surface-from: #f3f4f8;
        --sh-surface-to: #eceff4;
        --sh-surface-border: #d8dce6;
        --sh-text-primary: #0f172a;
        --sh-text-secondary: #64748b;
        --sh-card-bg: #ffffff;
        --sh-card-border: #dde2ea;
        --sh-card-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        --sh-accent: #7c3aed;
        --sh-accent-strong: #6d28d9;
    }
    
    .ow-shell {
        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%);
        border: 1px solid var(--sh-surface-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        color: var(--sh-text-primary);
    }

    .dark .ow-shell, .dark #sewing-wizard-modal {
        --sh-surface-from: #0d1830;
        --sh-surface-to: #0b1322;
        --sh-surface-border: rgba(148, 163, 184, 0.16);
        --sh-text-primary: #e5edf8;
        --sh-text-secondary: #91a4c0;
        --sh-card-bg: #10203a;
        --sh-card-border: rgba(148, 163, 184, 0.12);
        --sh-card-shadow: none;
        --sh-input-bg: #162847;
    }

    .dark .ow-shell {
        background: linear-gradient(180deg, var(--sh-surface-from) 0%, var(--sh-surface-to) 100%) !important;
        box-shadow: none !important;
        border-color: var(--sh-surface-border) !important;
    }


    .dark.avento-theme .ow-card, .dark.avento-theme .ow-progress, .dark.avento-theme .sewing-ui-surface, .dark.avento-theme .glass-card, .dark.avento-theme #sewing-wizard-modal .wizard-option-card {
        background-color: var(--sh-card-bg) !important;
        box-shadow: none !important;
    }

    .dark.avento-theme .ow-shell input:not([type="color"]),
    .dark.avento-theme .ow-shell select,
    .dark.avento-theme .ow-shell textarea,
    .dark.avento-theme .ow-btn-ghost,
    .dark.avento-theme .ow-search-toggle,
    .dark.avento-theme .ow-search-panel div[class*="dark:bg-slate-800"],
    .dark.avento-theme #sewing-wizard-modal input:not([type="color"]),
    .dark.avento-theme #sewing-wizard-modal select,
    .dark.avento-theme #sewing-wizard-modal textarea {
        background-color: var(--sh-input-bg) !important;
        background: var(--sh-input-bg) !important;
    }

    .ow-card, .ow-progress, .ow-field-panel {
        background: var(--sh-card-bg) !important;
        border: 1px solid var(--sh-card-border) !important;
        border-radius: 16px !important;
        box-shadow: var(--sh-card-shadow) !important;
    }

    .ow-sidebar-item {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.92)) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        box-shadow: none !important;
    }

    .dark .ow-sidebar-item {
        background: linear-gradient(180deg, rgba(16, 32, 58, 0.96), rgba(22, 40, 71, 0.92)) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 88%, transparent) !important;
    }

    .dark.avento-theme .ow-sidebar-item {
        background: linear-gradient(180deg, rgba(16, 32, 58, 0.96), rgba(22, 40, 71, 0.92)) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 88%, transparent) !important;
    }

    .ow-sidebar-item:hover {
        border-color: var(--sh-accent) !important;
    }

    .ow-step-badge {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--sh-accent);
        color: #fff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        box-shadow: none !important;
    }

    .sh-title {
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: var(--sh-text-primary);
    }

    .sh-subtitle {
        margin-top: 3px;
        font-size: 13px;
        font-weight: 600;
        color: var(--sh-text-secondary);
    }

    .ow-progress-track {
        background: color-mix(in srgb, var(--sh-input-bg, #e5e7eb) 78%, var(--sh-card-bg) 22%) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        overflow: hidden;
    }

    .ow-progress-fill {
        display: block;
        min-width: 0.75rem;
        background: var(--sh-accent);
        box-shadow: none !important;
    }

    #sublimation-fullpage-form .fullpage-sub-shell {
        background: color-mix(in srgb, var(--sh-card-bg) 94%, var(--sh-accent) 6%) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 72%, var(--sh-accent) 28%) !important;
        box-shadow: var(--sh-card-shadow) !important;
        overflow: hidden;
    }

    #sublimation-fullpage-form .fullpage-sub-header,
    #sublimation-fullpage-form .fullpage-sub-footer {
        background: color-mix(in srgb, var(--sh-card-bg) 88%, var(--sh-accent) 12%) !important;
        border-color: var(--sh-card-border) !important;
    }

    #sublimation-fullpage-form .fullpage-sub-header {
        margin: -1rem -1rem 1.25rem;
        padding: 1rem 1rem 0.95rem;
    }

    #sublimation-fullpage-form .fullpage-sub-footer {
        margin: 1.5rem -1rem -1rem;
        padding: 1rem;
    }

    @media (min-width: 640px) {
        #sublimation-fullpage-form .fullpage-sub-header {
            margin: -1.25rem -1.25rem 1.5rem;
            padding: 1.15rem 1.25rem 1.05rem;
        }

        #sublimation-fullpage-form .fullpage-sub-footer {
            margin: 1.75rem -1.25rem -1.25rem;
            padding: 1rem 1.25rem;
        }
    }

    #sublimation-fullpage-form .fullpage-step-chip {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 78%, var(--sh-card-bg) 22%);
        border-color: var(--sh-card-border) !important;
        color: var(--sh-text-secondary) !important;
        transition: all 0.2s ease-in-out;
    }

    #sublimation-fullpage-form .fullpage-step-index {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 62%, var(--sh-card-bg) 38%);
        color: var(--sh-text-secondary);
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 78%, transparent);
    }

    #sublimation-fullpage-form .fullpage-step-chip.is-active {
        background: rgba(124, 58, 237, 0.12) !important;
        border-color: rgba(124, 58, 237, 0.45) !important;
        color: #7c3aed !important;
    }

    #sublimation-fullpage-form .fullpage-step-chip.is-active .fullpage-step-index {
        background: #7c3aed !important;
        color: #ffffff !important;
        border-color: transparent !important;
    }

    #sublimation-fullpage-form .fullpage-step-chip.is-complete {
        background: rgba(124, 58, 237, 0.08) !important;
        border-color: rgba(124, 58, 237, 0.22) !important;
        color: #8b5cf6 !important;
    }

    #sublimation-fullpage-form .fullpage-step-chip.is-complete .fullpage-step-index {
        background: rgba(124, 58, 237, 0.14) !important;
        color: #7c3aed !important;
        border-color: rgba(124, 58, 237, 0.15) !important;
    }

    #sublimation-fullpage-form select,
    #sublimation-fullpage-form input:not([type="checkbox"]):not([type="file"]),
    #sublimation-fullpage-form textarea {
        background: var(--sh-input-bg, #f8fafc) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        color: var(--sh-text-primary) !important;
        border-radius: 1rem !important;
        min-height: 3rem;
    }

    #sublimation-fullpage-form input[readonly] {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 74%, var(--sh-card-bg) 26%) !important;
        font-weight: 700;
    }

    #sublimation-fullpage-form select:focus,
    #sublimation-fullpage-form input:not([type="checkbox"]):not([type="file"]):focus,
    #sublimation-fullpage-form textarea:focus {
        border-color: rgba(124, 58, 237, 0.55) !important;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12) !important;
        outline: none;
    }

    #sublimation-fullpage-form .fullpage-sub-card {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 62%, var(--sh-card-bg) 38%) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        border-radius: 1rem;
    }

    #sublimation-fullpage-form .fullpage-sub-metric {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 68%, var(--sh-card-bg) 32%) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        border-radius: 1rem;
    }

    #sublimation-fullpage-form .fullpage-sub-metric.is-highlight {
        background: rgba(124, 58, 237, 0.12) !important;
        border-color: rgba(124, 58, 237, 0.24) !important;
    }

    #sublimation-fullpage-form .fullpage-sub-upload {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 58%, var(--sh-card-bg) 42%) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 74%, var(--sh-accent) 26%) !important;
    }

    #sublimation-fullpage-form .fullpage-sub-upload:hover {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 48%, var(--sh-accent) 12%) !important;
    }

    #sublimation-fullpage-form .fullpage-sub-summary {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 64%, var(--sh-card-bg) 36%) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        border-radius: 1rem;
    }

    #sublimation-fullpage-form .fullpage-sub-addon-item {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 70%, var(--sh-card-bg) 30%) !important;
        border: 1px solid color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        border-radius: 0.9rem;
        transition: all 0.2s ease-in-out;
    }

    #sublimation-fullpage-form .fullpage-sub-addon-item:hover {
        border-color: rgba(124, 58, 237, 0.35) !important;
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 62%, var(--sh-accent) 8%) !important;
    }

    #sublimation-fullpage-form .fullpage-sub-addon-item:has(input:checked) {
        border-color: rgba(124, 58, 237, 0.45) !important;
        background: rgba(124, 58, 237, 0.12) !important;
    }

    #fullpage-special-fabric-fields {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 64%, var(--sh-card-bg) 36%) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
    }

    #fullpage-special-fabric-fields label {
        color: var(--sh-text-secondary) !important;
    }

    #fullpage-special-fabric-hint {
        color: var(--sh-text-secondary) !important;
    }

    #sublimation-fullpage-form #fullpage-step-prev,
    #sublimation-fullpage-form #fullpage-step-submit {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 78%, var(--sh-card-bg) 22%) !important;
        border-color: color-mix(in srgb, var(--sh-card-border) 82%, transparent) !important;
        color: var(--sh-text-primary) !important;
    }

    #sublimation-fullpage-form #fullpage-step-prev:hover,
    #sublimation-fullpage-form #fullpage-step-submit:hover {
        background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 68%, var(--sh-accent) 12%) !important;
        border-color: rgba(124, 58, 237, 0.32) !important;
    }

    #sublimation-fullpage-form #fullpage-step-next {
        background: #7c3aed !important;
        border-color: #7c3aed !important;
    }

/* Animações Premium */
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
@keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-3px) rotate(1deg); } }

.animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
.animate-slide-right { animation: slideInRight 0.4s ease-out forwards; }
.animate-float { animation: float 3s ease-in-out infinite; }

.delay-100 { animation-delay: 0.1s; opacity: 0; }
.delay-200 { animation-delay: 0.2s; opacity: 0; }

.glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
.dark .glass-card { background: #10203a !important; }

.hover-lift { transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.hover-lift:hover { transform: translateY(-2px); }
.dark .hover-lift:hover { box-shadow: none !important; }

/* Dashboard visual parity */
.ow-shell {
    max-width: 1600px;
    margin: 0 auto;
    padding: 1rem 1rem 1.5rem;
}

.ow-shell .glass-card {
    background: var(--sh-card-bg) !important;
    border: 1px solid var(--sh-card-border) !important;
    box-shadow: var(--sh-card-shadow) !important;
}

/* Host card must not trap fixed wizard modal */
.wizard-host-card {
    overflow: visible !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.ow-shell .sewing-ui-surface {
    background: var(--sh-card-bg) !important;
    border: 1px solid var(--sh-card-border) !important;
    box-shadow: var(--sh-card-shadow) !important;
}

.ow-shell .sewing-ui-muted {
    background: var(--sh-input-bg, #f8fafc) !important;
    border: 1px solid var(--sh-card-border) !important;
}

.ow-shell .text-ui-primary { color: var(--sh-text-primary) !important; }
.ow-shell .text-ui-muted { color: var(--sh-text-secondary) !important; }

@media (min-width: 640px) {
    .ow-shell {
        padding: 1.25rem 1.5rem 1.75rem;
    }
}

@media (min-width: 1024px) {
    .ow-shell {
        padding: 1.25rem 2rem 2rem;
    }
}

/* Mobile responsiveness */
@media (max-width: 640px) {
    .size-grid-mobile { grid-template-columns: repeat(5, 1fr) !important; gap: 0.375rem !important; }
    .size-grid-mobile input { padding: 0.375rem !important; font-size: 12px !important; }
    .size-grid-mobile label { font-size: 10px !important; }
}

/* Wizard modal comfort */
.sewing-wizard-panel {
    width: 100%;
    height: 100%;
    max-height: 100%;
    min-height: 0;
    border-radius: 1.25rem !important;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

#wizard-content {
    overscroll-behavior: contain;
    min-height: 0;
    background:
        radial-gradient(circle at top right, rgba(124, 58, 237, 0.08), transparent 28%),
        linear-gradient(180deg,
            color-mix(in srgb, var(--sh-card-bg) 97%, var(--sh-accent) 3%) 0%,
            var(--sh-card-bg) 100%) !important;
}

#sewing-wizard-modal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 10000 !important;
    overflow: hidden !important;
}

#sewing-wizard-modal .wizard-overlay {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at top right, rgba(124, 58, 237, 0.18), transparent 30%),
        linear-gradient(180deg, rgba(3, 10, 24, 0.56), rgba(3, 10, 24, 0.78)) !important;
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
}

#sewing-wizard-modal .wizard-frame {
    position: absolute;
    z-index: 1;
    top: 10vh;
    bottom: 10vh;
    left: calc(var(--sidebar-width, 0px) + ((100vw - var(--sidebar-width, 0px)) * 0.1));
    right: calc((100vw - var(--sidebar-width, 0px)) * 0.1);
    min-width: 0;
}

@media (max-width: 1023px) {
    #sewing-wizard-modal .wizard-frame {
        top: 6vh;
        bottom: 6vh;
        left: 1rem;
        right: 1rem;
    }
}

#sewing-wizard-modal .sewing-wizard-panel {
    background: linear-gradient(180deg,
        color-mix(in srgb, var(--sh-card-bg) 97%, var(--sh-accent) 3%) 0%,
        var(--sh-card-bg) 100%) !important;
    border: 1px solid color-mix(in srgb, var(--sh-card-border) 70%, var(--sh-accent) 30%) !important;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22) !important;
}

#sewing-wizard-modal .wizard-head,
#sewing-wizard-modal .wizard-foot {
    background: linear-gradient(180deg,
        color-mix(in srgb, var(--sh-card-bg) 90%, var(--sh-accent) 10%) 0%,
        color-mix(in srgb, var(--sh-card-bg) 97%, transparent) 100%) !important;
    border-color: var(--sh-card-border) !important;
}

#sewing-wizard-modal .wizard-head {
    position: relative;
}

#sewing-wizard-modal .wizard-head::after {
    content: "";
    position: absolute;
    left: 1.5rem;
    right: 1.5rem;
    bottom: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(124, 58, 237, 0.45), transparent);
}

#sewing-wizard-modal #wizard-step-title {
    color: var(--sh-text-secondary) !important;
}

#sewing-wizard-modal .wizard-close-btn {
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.85rem;
    border: 1px solid var(--sh-card-border);
    background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 88%, var(--sh-card-bg) 12%) !important;
    color: var(--sh-text-secondary) !important;
}

#sewing-wizard-modal .wizard-close-btn:hover {
    color: var(--sh-text-primary) !important;
    border-color: rgba(124, 58, 237, 0.35) !important;
    background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 78%, var(--sh-accent) 22%) !important;
}

#sewing-wizard-modal .wizard-bar-track {
    background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 90%, var(--sh-card-bg) 10%) !important;
}

#wizard-options-personalizacao {
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem;
}

#sewing-wizard-modal .wizard-step-copy {
    margin-bottom: 1.25rem;
    padding: 1rem 1.1rem;
    border-radius: 1rem;
    border: 1px solid color-mix(in srgb, var(--sh-card-border) 72%, var(--sh-accent) 28%);
    background: var(--sh-card-bg);
}

#sewing-wizard-modal .wizard-soft-surface {
    background: linear-gradient(180deg,
        color-mix(in srgb, var(--sh-input-bg, #f8fafc) 82%, var(--sh-accent) 6%) 0%,
        color-mix(in srgb, var(--sh-card-bg) 92%, var(--sh-input-bg, #f8fafc) 8%) 100%) !important;
    border: 1px solid color-mix(in srgb, var(--sh-card-border) 78%, transparent) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}

#sewing-wizard-modal .wizard-soft-surface:hover {
    background: linear-gradient(180deg,
        color-mix(in srgb, var(--sh-input-bg, #f8fafc) 74%, var(--sh-accent) 12%) 0%,
        color-mix(in srgb, var(--sh-card-bg) 88%, var(--sh-input-bg, #f8fafc) 12%) 100%) !important;
}

#sewing-wizard-modal .wizard-step-kicker {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: rgba(124, 58, 237, 0.12);
    color: #7c3aed;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

#sewing-wizard-modal .wizard-step-heading {
    margin-top: 0.85rem;
    font-size: 1.1rem;
    line-height: 1.2;
    font-weight: 800;
    color: var(--sh-text-primary) !important;
}

#sewing-wizard-modal .wizard-step-text {
    margin-top: 0.35rem;
    max-width: 40rem;
    font-size: 0.9rem;
    color: var(--sh-text-secondary) !important;
}

#sewing-wizard-modal .wizard-option-card {
    background: var(--sh-card-bg) !important;
    border-color: var(--sh-card-border) !important;
}

#sewing-wizard-modal .wizard-option-card:hover {
    transform: translateY(-2px);
    border-color: rgba(124, 58, 237, 0.55) !important;
}

#sewing-wizard-modal .wizard-option-card.ring-2 {
    background: rgba(124, 58, 237, 0.08) !important;
    border-color: rgba(124, 58, 237, 0.55) !important;
}

#sewing-wizard-modal .wizard-personalization-card span {
    color: var(--sh-text-primary) !important;
}

#sewing-wizard-modal #wizard-prev-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    min-width: 7rem;
    padding: 0.75rem 1rem;
    border-radius: 0.9rem;
    border: 1px solid var(--sh-card-border);
    background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 88%, var(--sh-card-bg) 12%) !important;
    color: var(--sh-text-secondary) !important;
}

#sewing-wizard-modal #wizard-prev-btn:hover {
    color: var(--sh-text-primary) !important;
    border-color: rgba(124, 58, 237, 0.35) !important;
    background: color-mix(in srgb, var(--sh-input-bg, #f8fafc) 78%, var(--sh-accent) 22%) !important;
}

#sewing-wizard-modal #wizard-next-btn {
    min-width: 8.25rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 24px rgba(124, 58, 237, 0.22);
}

/* Absolute Zero Shadow Kill - FINAL OVERRIDE */
html.dark.avento-theme .ow-shell,
html.dark.avento-theme .ow-shell *,
html.dark.avento-theme .ow-shell *::before,
html.dark.avento-theme .ow-shell *::after,
html.dark.avento-theme #sewing-wizard-modal,
html.dark.avento-theme #sewing-wizard-modal *,
html.dark.avento-theme #sewing-wizard-modal *::before,
html.dark.avento-theme #sewing-wizard-modal *::after {
    box-shadow: none !important;
    text-shadow: none !important;
    filter: none !important;
    -webkit-filter: none !important;
    transition: none !important;
}

.wizard-personalization-card {
    position: relative;
    min-height: 110px;
}

#sewing-wizard-modal .wizard-personalization-card {
    overflow: hidden;
}

#sewing-wizard-modal .wizard-personalization-card::after {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0;
    pointer-events: none;
    background: rgba(255, 255, 255, 0.08);
    transition: opacity 0.2s ease-in-out;
}

#sewing-wizard-modal .wizard-personalization-card.is-selected {
    background: #7c3aed !important;
    border-color: rgba(196, 181, 253, 0.95) !important;
    box-shadow: 0 18px 34px rgba(124, 58, 237, 0.28) !important;
    transform: translateY(-2px);
}

#sewing-wizard-modal .wizard-personalization-card.is-selected::before {
    content: "Selecionado";
    position: absolute;
    top: 0.65rem;
    right: 0.65rem;
    z-index: 1;
    padding: 0.2rem 0.45rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.96);
    color: #5b21b6;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

#sewing-wizard-modal .wizard-personalization-card.is-selected::after {
    opacity: 1;
}

#sewing-wizard-modal .wizard-personalization-card.is-selected span {
    color: #ffffff !important;
}

#sewing-wizard-modal .wizard-personalization-card.is-selected .wizard-personalization-icon {
    background: rgba(255, 255, 255, 0.18) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff !important;
}

#sewing-wizard-modal .wizard-personalization-card.is-selected .wizard-personalization-icon i {
    color: #ffffff !important;
}

@media (max-width: 640px) {
    .wizard-personalization-card {
        min-height: 98px;
    }

    #sewing-wizard-modal .wizard-personalization-card.is-selected::before {
        top: 0.5rem;
        right: 0.5rem;
        font-size: 0.58rem;
    }
}
</style>

<div class="max-w-[1520px] mx-auto pt-2 md:pt-3 pb-4 md:pb-6">
    <section class="ow-shell">
        <!-- Top Bar (Estilo Sales Hub) -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <span class="ow-step-badge">2</span>
                <div>
                    <h1 class="sh-title">Costura e Personalização</h1>
                    <p class="sh-subtitle">Etapa 2 de 5 • Configure os detalhes do item</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Passo Atual</div>
                <div class="text-2xl font-black text-[#7c3aed]">40%</div>
            </div>
        </div>

        <!-- Progress Widget -->
        <div class="ow-progress p-4 mb-8">
            <div class="ow-progress-track w-full rounded-full h-2">
                <div class="ow-progress-fill h-2 rounded-full transition-all duration-700" style="width: 40%"></div>
            </div>
        </div>

    <!-- Messages Premium -->


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- FormulÃ¡rio de Adicionar Item -->
        <div class="lg:col-span-2">
            <div class="glass-card wizard-host-card sewing-ui-surface rounded-2xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <!-- Header Premium -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 dark:border-slate-800 sewing-ui-surface">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#7c3aed] rounded-xl sm:rounded-2xl flex items-center justify-center border border-[#7c3aed]">
                            <i class="fa-solid fa-plus text-white stay-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-black text-ui-primary" id="form-title">Adicionar Novo Item</h1>
                            <p class="text-[10px] sm:text-sm text-ui-muted mt-0.5 font-medium">Configure os detalhes do item de costura</p>
                        </div>
                    </div>
                </div>

                    <div class="p-6">
                        <form method="POST" action="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" data-action-url="{{ isset($editData) ? route('orders.edit.sewing') : route('orders.wizard.sewing') }}" id="sewing-form" class="space-y-5" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                <input type="hidden" name="existing_cover_image" id="fullpage_existing_cover_image" value="">
                <input type="hidden" name="existing_corel_file" id="fullpage_existing_corel_file" value="">
                <input type="hidden" name="tecido_id" id="fullpage_tecido_id" value="">
                <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- PersonalizaÃ§Ã£o agora Ã© selecionada dentro do modal -->
                            <div id="hidden-personalizacao-container"></div>
                            <!-- Campos escondidos para envio ao backend -->
                            <input type="hidden" name="quantity" id="quantity" value="0">
                            <input type="hidden" name="unit_price" id="unit_price" value="0">
                            <input type="hidden" name="unit_cost" id="unit_cost" value="0">
                            <input type="hidden" name="art_notes" id="art_notes" value="">
                            <!-- PersonalizaÃ§Ã£o movida para o Wizard (Etapa 1) -->

                            <!-- Wizard Trigger / Main Configuration Card -->
                            <div id="normal-wizard-trigger" class="p-5 sewing-ui-surface rounded-lg border border-gray-200 dark:border-slate-700 space-y-3">

                                <label class="block text-sm font-semibold text-ui-primary">Configuração do Item</label>
                                
                                <div class="sewing-ui-muted rounded-xl border border-gray-200 dark:border-slate-700 p-6 flex flex-col items-center justify-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-[#7c3aed] dark:text-purple-400 mb-2">
                                        <i class="fa-solid fa-layer-group text-3xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-ui-primary" id="summary-title">Configurar Novo Item</h4>
                                        <p class="text-sm text-ui-muted mt-1 max-w-md mx-auto" id="summary-desc">Clique abaixo para iniciar a configura&ccedil;&atilde;o completa do item (Tecido, Modelo, Tamanhos, etc).</p>
                                    </div>
                                    <button type="button" onclick="openSewingWizard()" class="px-6 py-3 bg-[#7c3aed] hover:bg-[#6d28d9] text-white stay-white border border-[#7c3aed] font-bold rounded-xl shadow-md shadow-purple-500/20 transition-all transform hover:scale-105">
                                        Iniciar Configuração
                                    </button>
                                     
                                    <!-- Selected Options Summary (Hidden initially) -->
                                    <div id="main-summary-tags" class="hidden mt-4 flex flex-wrap gap-2 justify-center">
                                        <!-- Populated JS -->
                                    </div>
                                    
                                    <!-- Price Preview (Hidden initially) -->
                                    <div id="main-price-preview" class="hidden mt-2">
                                         <span class="text-lg font-bold text-[#7c3aed] dark:text-purple-400">Total: <span id="main-price-value">R$ 0,00</span></span>
                                    </div>
                                </div>

                                <!-- Hidden Inputs to store ALL wizard values -->
                                <input type="hidden" name="tecido" id="tecido_hidden">
                                <input type="hidden" name="tipo_tecido" id="tipo_tecido_hidden">
                                <input type="hidden" name="cor" id="cor_hidden">
                                <input type="hidden" name="tipo_corte" id="tipo_corte_hidden">
                                <input type="hidden" id="detalhe_hidden">
                                <input type="hidden" name="detail_color" id="detail_color_hidden">
                                <input type="hidden" name="gola" id="gola_hidden">
                                <input type="hidden" name="collar_color" id="collar_color_hidden">
                                <!-- Sizes hidden inputs will be dynamically managed/appended or we can keep the container hidden -->
                                <div id="hidden-sizes-container" class="hidden">
                                     <!-- JS will map wizard inputs to here before submit if needed, or we just rely on the form inside modal to be the 'real' inputs if we move the form tag? 
                                          The form tag wraps the whole content. So inputs inside the modal ARE inside the form.
                                          We just need to ensure unique IDs if we duplicate.
                                          Actually, if we move the inputs TO the modal, we don't need hidden copies if the modal is inside the form.
                                          Let's check: The modal is inside <form id="sewing-form"> ?
                                          Line 215 is the modal div.
                                          Line 454 was the end of the form.
                                          So yes, existing modal IS inside the form. 
                                          We can just place the actual inputs inside the modal steps!
                                      -->
                                </div>
                            </div>

                            <div class="p-5 sewing-ui-surface rounded-lg border border-gray-200 dark:border-slate-700 space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-ui-primary">Peça de Tecido do Estoque</label>
                                        <p class="text-xs text-ui-muted mt-1">Opcional. Vincule uma peça para consumir saldo automaticamente ao confirmar o pedido.</p>
                                    </div>
                                    <span id="fabric-piece-current-badge" class="hidden px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        Peça vinculada
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Selecionar peça</label>
                                        <select name="fabric_piece_id" id="fabric_piece_id"
                                                onchange="updateFabricPieceSelection()"
                                                class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                            <option value="">Não usar peça de tecido</option>
                                            @foreach(($fabricPieces ?? []) as $piece)
                                                <option value="{{ $piece->id }}">
                                                    {{ $piece->display_name }} | Saldo {{ number_format($piece->available_quantity, $piece->control_unit === 'metros' ? 2 : 3, ',', '.') }} {{ $piece->control_unit === 'metros' ? 'm' : 'kg' }} | {{ $piece->store?->name ?? 'Loja' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Quantidade da peça</label>
                                        <input type="number"
                                               name="fabric_piece_quantity"
                                               id="fabric_piece_quantity"
                                               min="0.001"
                                               step="0.001"
                                               placeholder="0,000"
                                               class="w-full px-3 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                    </div>
                                </div>

                                <input type="hidden" name="fabric_piece_unit" id="fabric_piece_unit">

                                <div id="fabric-piece-selection-info" class="hidden rounded-xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-900/10 px-4 py-3">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Unidade</span>
                                            <span id="fabric-piece-unit-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Saldo disponível</span>
                                            <span id="fabric-piece-available-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                        <div>
                                            <span class="block text-emerald-700 dark:text-emerald-300 font-semibold uppercase tracking-wide">Loja</span>
                                            <span id="fabric-piece-store-label" class="text-gray-900 dark:text-white font-bold">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUBLIMAÃ‡ÃƒO FULLPAGE FORM (Hidden - Shows when SUB.TOTAL is selected) -->
                            <div id="sublimation-fullpage-form" class="hidden">
                                <div class="fullpage-sub-shell p-4 sm:p-5 rounded-xl border border-gray-200 dark:border-slate-700">
                                    <!-- Header -->
                                    <div class="fullpage-sub-header flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-slate-800">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sublimação Total</h3>
                                            <p id="fullpage-step-subtitle" class="text-sm text-gray-500 dark:text-slate-400">Etapa 1 de 3 · Configuração</p>
                                        </div>
                                        <button type="button" onclick="hideSubFullpageForm()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- Stepper -->
                                    <div id="fullpage-step-indicator" class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
                                        <div data-step="1" class="fullpage-step-chip is-active flex items-center gap-2 px-3 py-2 rounded-lg border">
                                            <span class="fullpage-step-index w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center">1</span>
                                            <span class="text-xs sm:text-sm font-semibold">Configuração</span>
                                        </div>
                                        <div data-step="2" class="fullpage-step-chip is-pending flex items-center gap-2 px-3 py-2 rounded-lg border">
                                            <span class="fullpage-step-index w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center">2</span>
                                            <span class="text-xs sm:text-sm font-semibold">Produção</span>
                                        </div>
                                        <div data-step="3" class="fullpage-step-chip is-pending flex items-center gap-2 px-3 py-2 rounded-lg border">
                                            <span class="fullpage-step-index w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center">3</span>
                                            <span class="text-xs sm:text-sm font-semibold">Revisão</span>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <input type="hidden" id="fullpage-sub-editing-id">
                                        <input type="hidden" id="fullpage_existing_cover_image_fullpage">
                                        <input type="hidden" id="fullpage_existing_corel_file_fullpage">
                                        
                                        <!-- Step 1 -->
                                        <div id="fullpage-step-1" class="fullpage-sub-step space-y-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tipo de Produto *</label>
                                                    <select id="fullpage_sub_type" onchange="loadFullpageSubAddons()" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione o tipo</option>
                                                        @if(isset($sublimationTypes))
                                                        @foreach($sublimationTypes as $type)
                                                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nome da Arte *</label>
                                                    <input type="text" id="fullpage_art_name" placeholder="Ex: Logo Empresa ABC" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tecido *</label>
                                                    <select id="fullpage_sub_fabric_type" onchange="handleFullpageFabricTypeChange()" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Modelo *</label>
                                                    <select id="fullpage_sub_model" onchange="calculateFullpageSubTotal()" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="">Selecione</option>
                                                        <option value="BASICA">BASICA</option>
                                                        <option value="BABYLOOK">BABYLOOK</option>
                                                        <option value="INFANTIL">INFANTIL</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="fullpage-special-fabric-fields" class="hidden p-3 rounded-lg border space-y-3">
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1">Nome do Tecido</label>
                                                    <input type="text" id="fullpage_sub_fabric_custom" oninput="syncFullpageSpecialFabricPricing(); calculateFullpageSubTotal();" placeholder="Ex: Dryfit" class="w-full px-3 py-2 rounded-lg border bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold mb-1">Acréscimo por tecido especial (R$)</label>
                                                    <input type="number" id="fullpage_sub_fabric_surcharge" value="0.00" step="0.01" min="0" readonly class="w-full px-3 py-2 rounded-lg border bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                                    <p id="fullpage-special-fabric-hint" class="mt-1 text-[11px]"></p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Cor do Tecido</label>
                                                    <input type="text" id="fullpage_sub_fabric_color" value="BRANCO" readonly class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-slate-300 mb-1">Gola Padrão</label>
                                                    <select id="fullpage_sub_base_collar" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-400">
                                                        <option value="REDONDA">REDONDA</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 2 -->
                                        <div id="fullpage-step-2" class="fullpage-sub-step hidden space-y-4">
                                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                                                <div class="xl:col-span-2 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Tamanhos e Quantidades</label>
                                                        <div class="grid grid-cols-5 gap-2 mb-2">
                                                            @foreach(['PP', 'P', 'M', 'G', 'GG'] as $size)
                                                            <div class="text-center">
                                                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $size }}</label>
                                                                <input type="number" data-size="{{ $size }}" min="0" value="0" onchange="calculateFullpageSubTotal()" class="fullpage-sub-size w-full px-1 py-2 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400">
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="grid grid-cols-5 gap-2">
                                                            @foreach(['EXG', 'G1', 'G2', 'G3', 'Esp.'] as $size)
                                                            <div class="text-center">
                                                                <label class="block text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $size }}</label>
                                                                <input type="number" data-size="{{ $size == 'Esp.' ? 'Especial' : $size }}" min="0" value="0" onchange="calculateFullpageSubTotal()" class="fullpage-sub-size w-full px-1 py-2 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400">
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Adicionais</label>
                                                        <div id="fullpage-sub-addons" class="fullpage-sub-card grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 rounded-lg border border-gray-200 dark:border-slate-700">
                                                            <p class="text-sm text-gray-400 col-span-full text-center py-2">Selecione um tipo primeiro</p>
                                                        </div>
                                                    </div>

                                                    <div class="fullpage-sub-card p-3 rounded-lg border border-gray-200 dark:border-slate-700">
                                                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                                            <input type="checkbox" id="fullpage_has_addon_colors" onchange="renderFullpageAddonColorFields()" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                                            Terá cor nos adicionais?
                                                        </label>
                                                        <div id="fullpage-addon-color-fields" class="hidden space-y-2"></div>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <div class="fullpage-sub-metric p-3 rounded-lg border border-gray-200 dark:border-slate-700 text-center">
                                                        <span class="block text-xs text-gray-500 dark:text-slate-400 mb-0.5">Total de Peças</span>
                                                        <span class="text-2xl font-bold text-gray-900 dark:text-white" id="fullpage-total-qty">0</span>
                                                    </div>
                                                    <div class="fullpage-sub-metric p-3 rounded-lg border border-gray-200 dark:border-slate-700 text-center">
                                                        <span class="block text-xs text-gray-500 dark:text-slate-400 mb-0.5">Preço Unitário</span>
                                                        <span class="text-xl font-bold text-gray-900 dark:text-white" id="fullpage-unit-price">R$ 0,00</span>
                                                    </div>
                                                    <div class="fullpage-sub-metric is-highlight p-4 rounded-lg text-center border border-gray-200 dark:border-slate-700">
                                                        <span class="block text-sm text-gray-600 dark:text-slate-400 mb-1">Total do Item</span>
                                                        <span class="text-3xl font-bold text-gray-900 dark:text-white" id="fullpage-total-price">R$ 0,00</span>
                                                        <p id="fullpage-price-breakdown" class="text-xs text-gray-500 dark:text-slate-400 mt-1">Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 3 -->
                                        <div id="fullpage-step-3" class="fullpage-sub-step hidden space-y-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Arquivo Corel</label>
                                                        <label class="fullpage-sub-upload flex flex-col items-center justify-center w-full min-h-20 px-3 py-3 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg cursor-pointer transition-colors">
                                                            <i class="fa-solid fa-upload text-gray-400 text-sm mb-0.5"></i>
                                                            <span id="fullpage-corel-placeholder" class="text-xs text-gray-500">.CDR, .AI, .PDF</span>
                                                            <span id="fullpage-corel-file-name" class="hidden mt-2 max-w-full px-3 py-1 rounded-full bg-[#7c3aed]/10 text-[#7c3aed] text-xs font-semibold truncate"></span>
                                                            <input type="file" id="fullpage_corel_file" class="hidden" accept=".cdr,.ai,.pdf,.eps" onchange="previewFullpageCorelFile(this)">
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Imagem Capa</label>
                                                        <label class="fullpage-sub-upload relative flex items-center justify-center w-full h-24 border border-dashed border-gray-300 dark:border-slate-600 rounded-lg cursor-pointer transition-colors overflow-hidden">
                                                            <div id="fullpage-cover-placeholder" class="flex flex-col items-center justify-center">
                                                                <i class="fa-solid fa-image text-gray-400 text-sm mb-0.5"></i>
                                                                <span class="text-xs text-gray-500">PNG, JPG</span>
                                                            </div>
                                                            <div id="fullpage-cover-preview-container" class="hidden absolute inset-0">
                                                                <img id="fullpage-cover-preview" src="" alt="Preview da capa" class="w-full h-full object-cover">
                                                                <div id="fullpage-cover-file-name" class="absolute inset-x-0 bottom-0 px-3 py-1.5 bg-slate-950/80 text-white text-[11px] font-medium truncate"></div>
                                                            </div>
                                                            <input type="file" id="fullpage_cover_image" class="hidden" accept="image/*" onchange="previewFullpageCoverImage(this)">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Observações</label>
                                                        <textarea id="fullpage_notes" rows="6" placeholder="Observações para produção..." class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm focus:ring-1 focus:ring-gray-400"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="fullpage-sub-summary p-4 rounded-lg border border-gray-200 dark:border-slate-700">
                                                <p class="text-sm text-gray-600 dark:text-slate-300 mb-2">Resumo final</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                                    <div class="text-gray-700 dark:text-slate-300">Peças: <span id="fullpage-total-qty-review" class="font-semibold text-gray-900 dark:text-white">0</span></div>
                                                    <div class="text-gray-700 dark:text-slate-300">Unitário: <span id="fullpage-unit-price-review" class="font-semibold text-gray-900 dark:text-white">R$ 0,00</span></div>
                                                    <div class="text-gray-700 dark:text-slate-300">Total: <span id="fullpage-total-price-review" class="font-semibold text-gray-900 dark:text-white">R$ 0,00</span></div>
                                                </div>
                                                <p id="fullpage-price-breakdown-review" class="text-xs text-gray-500 dark:text-slate-400 mt-2">Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Nav -->
                                    <div class="fullpage-sub-footer mt-6 pt-4 border-t border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <button id="fullpage-step-prev" type="button" onclick="goToPrevFullpageSubStep()" class="hidden w-full sm:w-auto px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                            <i class="fa-solid fa-arrow-left mr-1"></i>
                                            Voltar
                                        </button>

                                        <div class="w-full sm:w-auto sm:ml-auto flex gap-2">
                                            <button id="fullpage-step-next" type="button" onclick="goToNextFullpageSubStep()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#7c3aed] text-white font-semibold hover:bg-[#6d28d9] transition-colors">
                                                Próxima
                                                <i class="fa-solid fa-arrow-right ml-1"></i>
                                            </button>

                                            <button id="fullpage-step-submit" type="button" onclick="submitFullpageSubItem()" class="hidden w-full sm:w-auto px-5 py-2.5 rounded-lg bg-white hover:bg-gray-50 border border-gray-300 dark:border-slate-600 text-gray-800 dark:text-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 font-semibold transition-colors flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-plus"></i>
                                                Adicionar Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="sewing-wizard-modal" class="fixed inset-0 z-[10000] hidden" role="dialog" aria-modal="true">

                                <!-- Backdrop -->
                                <div class="wizard-overlay bg-black/60 backdrop-blur-sm transition-opacity" 
                                     onclick="closeSewingWizard()"></div>

                                <!-- Modal Panel -->
                                <div class="wizard-frame">
                                    <div class="sewing-wizard-panel bg-white dark:bg-slate-900 rounded-none shadow-xl w-full h-full max-w-none max-h-none overflow-hidden transition-all animate-fade-in-up border border-gray-200 dark:border-slate-700">
                                        
                                        <!-- Header -->
                                        <div class="wizard-head px-5 sm:px-6 lg:px-8 py-3 sm:py-4 flex-none border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
                                            <div>
                                                <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">Configurar Modelo</h3>
                                                <p class="text-[10px] text-gray-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5" id="wizard-step-title">Etapa 1 de 5</p>
                                            </div>
                                            <button type="button" onclick="closeSewingWizard()" class="wizard-close-btn transition-colors">
                                                <i class="fa-solid fa-xmark text-xl"></i>
                                            </button>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="wizard-bar-track w-full bg-gray-100 dark:bg-slate-800 h-1 flex-none">
                                            <div id="wizard-progress" class="bg-[#7c3aed] h-full transition-all duration-300" style="width: 20%"></div>
                                        </div>

                                        <!-- Steps Content -->
                                        <div class="flex-1 overflow-y-auto min-h-0 p-4 sm:p-6 lg:p-7 custom-scrollbar" id="wizard-content">
                                            
                                            <!-- Step 1: Personalizacao -->
                                            <div id="step-1" class="wizard-step">
                                                <div class="wizard-step-copy">
                                                    <span class="wizard-step-kicker">Escolha as t&eacute;cnicas</span>
                                                    <h4 class="wizard-step-heading">Selecione a Personalização</h4>
                                                    <p class="wizard-step-text">Voc&ecirc; pode selecionar m&uacute;ltiplas op&ccedil;&otilde;es.</p>
                                                </div>
                                                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4" id="wizard-options-personalizacao">
                                                    <!-- Filled by JS -->
                                                </div>
                                            </div>

                                            <!-- Step SUB: Sublimacao Total (shown when SUB.TOTAL is selected) -->
                                            <div id="step-sub" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Configurar Sublima&ccedil;&atilde;o Total</h4>
                                                
                                                <div class="space-y-5">
                                                    <!-- Tipo de Produto SUB.TOTAL -->
                                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tipo de Produto</label>
                                                        <select id="sub_wizard_type" onchange="loadSubWizardAddons()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                                                            <option value="">Selecione o tipo</option>
                                                            @if(isset($sublimationTypes))
                                                            @foreach($sublimationTypes as $type)
                                                            <option value="{{ $type->slug }}">{{ $type->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <!-- Adicionais -->
                                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Adicionais</label>
                                                        <div id="sub-wizard-addons" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                            <p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>
                                                        </div>
                                                    </div>

                                                    <!-- Nome da Arte -->
                                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Nome da Arte *</label>
                                                        <input type="text" id="sub_wizard_art_name" placeholder="Ex: Logo Empresa ABC" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                                                    </div>

                                                    <!-- Tamanhos e Quantidades -->
                                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Tamanhos e Quantidades</label>
                                                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3 mb-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">PP</label>
                                                                <input type="number" data-size="PP" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">P</label>
                                                                <input type="number" data-size="P" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">M</label>
                                                                <input type="number" data-size="M" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G</label>
                                                                <input type="number" data-size="G" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">GG</label>
                                                                <input type="number" data-size="GG" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3">
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">EXG</label>
                                                                <input type="number" data-size="EXG" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G1</label>
                                                                <input type="number" data-size="G1" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G2</label>
                                                                <input type="number" data-size="G2" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">G3</label>
                                                                <input type="number" data-size="G3" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1 font-medium text-center">Esp.</label>
                                                                <input type="number" data-size="Especial" min="0" value="0" onchange="calculateSubWizardTotal()" class="sub-wizard-size w-full px-2 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-center text-sm bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Total de Pecas e Preco -->
                                                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-center">
                                                                <span class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Total de Pe&ccedil;as</span>
                                                                <span class="text-2xl font-black text-purple-600 dark:text-purple-400" id="sub-wizard-total-qty">0</span>
                                                            </div>
                                                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg text-center">
                                                                <span class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Pre&ccedil;o Unit&aacute;rio</span>
                                                                <span class="text-2xl font-black text-green-600 dark:text-green-400" id="sub-wizard-unit-price">R$ 0,00</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Arquivos -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Arquivo Corel</label>
                                                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <i class="fa-solid fa-file-import text-gray-400 text-xl mb-1"></i>
                                                                <span class="text-xs text-gray-500">.CDR, .AI, .PDF</span>
                                                                <input type="file" id="sub_wizard_corel" class="hidden" accept=".cdr,.ai,.pdf,.eps">
                                                            </label>
                                                        </div>
                                                        <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Imagem de Capa</label>
                                                            <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <i class="fa-solid fa-image text-gray-400 text-xl mb-1"></i>
                                                                <span class="text-xs text-gray-500">PNG, JPG, WEBP</span>
                                                                <input type="file" id="sub_wizard_cover" class="hidden" accept="image/*">
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- Observacoes -->
                                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-900/40 rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Observa&ccedil;&otilde;es</label>
                                                        <textarea id="sub_wizard_notes" rows="2" placeholder="Observa&ccedil;&otilde;es importantes para a produ&ccedil;&atilde;o..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm"></textarea>
                                                    </div>

                                                    <!-- Total Final -->
                                                    <div class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl text-white text-center">
                                                        <span class="block text-sm opacity-80 mb-1">Total do Item</span>
                                                        <span class="text-3xl font-black" id="sub-wizard-total-price">R$ 0,00</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 2: Tecido -->
                                            <div id="step-2" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione o Tecido</h4>
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-gray-600 dark:text-slate-400 mb-2">Tecido</label>
                                                        <select id="wizard_tecido" onchange="loadWizardTiposTecido()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all">
                                                            <option value="">Selecione o tecido</option>
                                                            <!-- Options populated by JS -->
                                                        </select>
                                                    </div>
                                                    <div id="wizard-tipo-tecido-container" class="hidden">
                                                        <label class="block text-xs font-semibold text-gray-600 dark:text-slate-400 mb-2">Tipo de Tecido</label>
                                                        <select id="wizard_tipo_tecido" onchange="onWizardTipoTecidoChange()" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all">
                                                            <option value="">Selecione o tipo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 3: Cor do Tecido -->
                                            <div id="step-3" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor do Tecido</h4>
                                                <!-- Search/Filter could go here -->
                                                <select id="wizard_cor" onchange="//Handled by next button filtered check or immediate JS" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] outline-none transition-all mb-4">
                                                    <option value="">Selecione uma cor</option>
                                                </select>
                                                <div id="wizard-colors-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-h-60 overflow-y-auto">
                                                    <!-- Visually rich color picker populated by JS -->
                                                </div>
                                            </div>

                                            <!-- Step 4: Tipo de Corte -->
                                            <div id="step-4" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione o Tipo de Corte</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="wizard-options-corte">
                                                    <div class="p-8 text-center text-gray-500">Carregando op&ccedil;&otilde;es...</div>
                                                </div>
                                            </div>

                                            <!-- Step 5: Detalhe -->
                                            <div id="step-5" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Selecione o(s) Detalhe(s)</h4>
                                                <p class="text-[10px] text-gray-500 mb-3">Voc&ecirc; pode selecionar m&uacute;ltiplos detalhes.</p>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 mb-4" id="wizard-options-detalhe">
                                                    <!-- Filled by JS -->
                                                </div>
                                                
                                                <div class="space-y-3">
                                                    <div class="p-3 sewing-ui-muted rounded-xl border border-gray-200 dark:border-slate-700">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="checkbox" id="different_detail_color_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]" onchange="toggleDetailColorUI()">
                                                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Cor do detalhe diferente do tecido?</span>
                                                        </label>
                                                    </div>
                                                    
                                                    <div id="individual-colors-toggle-container" class="hidden p-3 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-200 dark:border-purple-800">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="checkbox" id="individual_detail_colors_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]" onchange="wizardData.individual_detail_colors = this.checked; renderWizardDetailColorOptions();">
                                                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Definir cores individuais por detalhe?</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 6: Cor do Detalhe -->
                                            <div id="step-6" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor do Detalhe</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="wizard-options-cor-detalhe">
                                                     <!-- Filled by JS (using existing loop logic or JS render) -->
                                                </div>
                                            </div>

                                            <!-- Step 7: Gola -->
                                            <div id="step-7" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Gola</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4" id="wizard-options-gola">
                                                    <!-- Filled by JS -->
                                                </div>
                                                <div class="p-3 sewing-ui-muted rounded-xl border border-gray-200 dark:border-slate-700">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="different_collar_color_cb" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Cor da gola diferente do tecido?</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Step 8: Cor da Gola -->
                                            <div id="step-8" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Selecione a Cor da Gola</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="wizard-options-cor-gola">
                                                    <!-- Filled by JS -->
                                                </div>
                                            </div>

                                            <!-- Step 9: Tamanhos -->
                                            <div id="step-9" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Defina os Tamanhos e Quantidades</h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-4" id="wizard-sizes-grid">
                                                    <!-- Standard Sizes -->
                                                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'] as $size)
                                                    <div>
                                                        <label class="block text-[10px] text-gray-500 dark:text-slate-400 mb-1 font-bold text-center uppercase">{{ $size }}</label>
                                                        <input type="number" data-size="{{ $size }}" min="0" value="0" class="wizard-size-input w-full px-1 py-1.5 border border-gray-200 dark:border-slate-700 rounded-lg text-center font-bold bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed] transition-all text-sm">
                                                    </div>
                                                    @endforeach
                                                </div>
                                                
                                                 <!-- Checkbox para acréscimo independente (apenas para Infantil/Baby look) -->
                                                <div id="wizard-surcharge-container" class="hidden mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_apply_surcharge" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Aplicar acréscimo de tamanho especial</span>
                                                    </label>
                                                </div>

                                                <!-- Checkbox para Modelagem do Cliente (Aparece se Especial > 0) -->
                                                <div id="wizard-modeling-container" class="hidden mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox" id="wizard_is_client_modeling" class="w-4 h-4 text-[#7c3aed] rounded focus:ring-[#7c3aed]">
                                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Tamanho especial Ã© pela modelagem do cliente?</span>
                                                    </label>
                                                </div>
                                                
                                                <div class="flex justify-between items-center bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl border border-purple-100 dark:border-purple-800/50">
                                                    <span class="text-sm font-bold text-gray-700 dark:text-slate-300">Total de Peças:</span>
                                                    <span class="text-2xl font-black text-[#7c3aed]" id="wizard-total-pieces">0</span>
                                                </div>
                                            </div>

                                            <!-- Step 10: Imagem e Obs -->
                                            <div id="step-10" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Personalização e Detalhes Finais</h4>
                                                
                                                <div class="space-y-5">
                                                    <!-- Image Upload -->
                                                    <div class="wizard-soft-surface p-4 border border-dashed border-gray-300 dark:border-slate-600 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors text-center cursor-pointer relative" onclick="document.getElementById('wizard_file_input').click()">
                                                        <input type="file" id="wizard_file_input" class="hidden" accept="image/*" onchange="previewWizardImage(this)">
                                                        
                                                        <div id="wizard-image-placeholder" class="py-4">
                                                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                                                            <p class="text-sm font-semibold text-gray-600 dark:text-slate-300">Clique para enviar a imagem de capa</p>
                                                            <p class="text-xs text-gray-400">PNG, JPG ou WEBP (Max. 10MB)</p>
                                                        </div>
                                                        <div id="wizard-image-preview-container" class="hidden relative inline-block group">
                                                             <img id="wizard-image-preview" class="h-32 object-contain rounded-lg shadow-sm border border-gray-200">
                                                             <button onclick="event.stopPropagation(); clearWizardImage()" class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs shadow-md hover:bg-red-600 transition-transform hover:scale-110 z-10"><i class="fa-solid fa-times"></i></button>
                                                        </div>
                                                    </div>

                                                    <!-- Notes -->
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Observações</label>
                                                        <textarea id="wizard_notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#7c3aed]" placeholder="Alguma observação importante para a produção?"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 11: Resumo Final -->
                                            <div id="step-11" class="wizard-step hidden">
                                                <h4 class="text-sm font-bold text-center text-gray-900 dark:text-white mb-6">Conferência Final</h4>
                                                
                                                <div class="wizard-soft-surface rounded-2xl p-6 border border-gray-200 dark:border-slate-700 space-y-4">
                                                    <!-- Dynamic Summary List -->
                                                    <div class="space-y-3 text-sm">
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Tecido:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-tecido-val">-</span>
                                                        </div>
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Cor:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-cor-val">-</span>
                                                        </div>
                                                        <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Modelo:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-modelo-val">-</span>
                                                        </div>
                                                         <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                                                            <span class="text-gray-500 dark:text-slate-400">Peças:</span>
                                                            <span class="font-bold text-gray-900 dark:text-white text-right" id="summary-pecas-val">0</span>
                                                        </div>
                                                    </div>

                                                    <!-- Prices -->
                                                    <div class="mt-6 pt-4 border-t border-gray-300 dark:border-slate-600">
                                                        <h5 class="font-bold text-gray-900 dark:text-white mb-3">Custos e Valores</h5>
                                                        
                                                        <!-- Admin Only Unit Cost -->
                                                        <div class="flex justify-between items-center p-3 bg-red-50/10 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30 rounded-lg mb-3" 
                                                             style="display: {{ auth()->user()->isAdmin() ? 'flex' : 'none' }}">
                                                            <span class="text-red-600 dark:text-red-400 font-bold text-sm">Custo Unitário:</span>
                                                            <div class="flex items-center">
                                                                <span class="text-red-600 dark:text-red-400 font-bold mr-1">R$</span>
                                                                <input type="number" id="wizard_unit_cost" class="w-20 bg-transparent text-right font-bold text-red-600 dark:text-red-400 border-none p-0 focus:ring-0" value="0.00" step="0.01">
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-between items-center p-4 bg-purple-50/20 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-900/30 rounded-xl shadow-sm">
                                                            <span class="text-[#7c3aed] dark:text-purple-400 font-bold">Valor Unitário:</span>
                                                            <span class="text-2xl font-black text-[#7c3aed] dark:text-purple-400" id="wizard-final-price">R$ 0,00</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <!-- Footer -->
                                        <div class="wizard-foot px-5 sm:px-6 lg:px-8 py-3 sm:py-4 flex-none border-t border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-none">
                                            <button type="button" id="wizard-prev-btn" onclick="wizardPrevStep()" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200 text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                                &larr; Voltar
                                            </button>
                                            <div class="flex gap-2">
                                                <button type="button" id="wizard-next-btn" onclick="wizardNextStep()" class="px-6 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white stay-white text-sm font-bold rounded-lg transition-all shadow-md shadow-purple-500/20">
                                                    Pr&oacute;ximo
                                                </button>
                                                <button type="button" id="wizard-submit-btn" onclick="submitSewingWizard()" class="hidden px-6 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white stay-white text-sm font-bold rounded-lg transition-all shadow-md shadow-purple-500/20">
                                                    Salvar Alterações
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tamanhos (Moved above) -->

                            <!-- <!-- Botões (Removido - controllado pelo Wizard) -->
                            <!-- <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-slate-700"> ... </div> -->
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Resumo dos Itens -->
            <div class="lg:col-span-1" id="items-sidebar-container">
                @include('orders.wizard.partials.items_sidebar')
            </div>
        </div>
    </div>
    <!-- Modal de Confirmação de Exclusão -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all scale-100 opacity-100">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover Item?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Esta ação não pode ser desfeita. O item será removido permanentemente do pedido.</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white stay-white rounded-lg font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-105">
                        Sim, Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>

@if(isset($sublimationEnabled) && $sublimationEnabled)
@include('orders.wizard.partials.sublimation_modal')
@endif

@push('page-scripts')
<script>
(function() {
    const storageUrl = "{{ asset('storage') }}/";
    window.storageUrl = storageUrl;
    
    // Options for Wizard
    let options = {
        tecido: @json($fabrics ?? []),
        cor: @json($colors ?? []),
        personalizacao: @json($personalizationOptions ?? [])
    };
    window.options = options;
    @php
        $fabricPiecesJson = collect($fabricPieces ?? [])->map(function($piece) {
            return [
                'id' => $piece->id,
                'label' => $piece->display_name,
                'unit' => $piece->control_unit,
                'unit_label' => $piece->control_unit === 'metros' ? 'Metros' : 'Kg',
                'unit_suffix' => $piece->control_unit === 'metros' ? 'm' : 'kg',
                'available_quantity' => (float) $piece->available_quantity,
                'store_name' => $piece->store?->name,
            ];
        })->values();
    @endphp
    const fabricPiecesData = @json($fabricPiecesJson);
    window.fabricPiecesData = fabricPiecesData;

    function resetFabricPieceSelection() {
        const select = document.getElementById('fabric_piece_id');
        const qtyInput = document.getElementById('fabric_piece_quantity');
        const unitInput = document.getElementById('fabric_piece_unit');
        const info = document.getElementById('fabric-piece-selection-info');
        const badge = document.getElementById('fabric-piece-current-badge');

        if (select) select.value = '';
        if (qtyInput) {
            qtyInput.value = '';
            qtyInput.removeAttribute('max');
            qtyInput.step = '0.001';
        }
        if (unitInput) unitInput.value = '';
        if (info) info.classList.add('hidden');
        if (badge) badge.classList.add('hidden');
    }
    window.resetFabricPieceSelection = resetFabricPieceSelection;

    function updateFabricPieceSelection() {
        const select = document.getElementById('fabric_piece_id');
        const qtyInput = document.getElementById('fabric_piece_quantity');
        const unitInput = document.getElementById('fabric_piece_unit');
        const info = document.getElementById('fabric-piece-selection-info');
        const badge = document.getElementById('fabric-piece-current-badge');
        const unitLabel = document.getElementById('fabric-piece-unit-label');
        const availableLabel = document.getElementById('fabric-piece-available-label');
        const storeLabel = document.getElementById('fabric-piece-store-label');

        if (!select || !qtyInput || !unitInput) return;

        const piece = fabricPiecesData.find(item => String(item.id) === String(select.value));
        if (!piece) {
            resetFabricPieceSelection();
            return;
        }

        unitInput.value = piece.unit;
        qtyInput.step = piece.unit === 'metros' ? '0.01' : '0.001';
        qtyInput.min = piece.unit === 'metros' ? '0.01' : '0.001';
        qtyInput.max = piece.available_quantity;

        if (!qtyInput.value) {
            qtyInput.value = piece.available_quantity < 1
                ? piece.available_quantity
                : (piece.unit === 'metros' ? '1.00' : '1.000');
        }

        if (unitLabel) unitLabel.textContent = piece.unit_label;
        if (availableLabel) {
            availableLabel.textContent = `${Number(piece.available_quantity).toLocaleString('pt-BR', {
                minimumFractionDigits: piece.unit === 'metros' ? 2 : 3,
                maximumFractionDigits: piece.unit === 'metros' ? 2 : 3
            })} ${piece.unit_suffix}`;
        }
        if (storeLabel) storeLabel.textContent = piece.store_name || 'N/A';
        if (info) info.classList.remove('hidden');
        if (badge) badge.classList.remove('hidden');
    }
    window.updateFabricPieceSelection = updateFabricPieceSelection;

    // Ãcones e cores especÃ­ficos por tipo de personalizaÃ§Ã£o
    const personalizationIconMap = {
        dtf:            { icon: 'fa-print',        bubble: 'bg-orange-100 dark:bg-orange-900/30',  color: 'text-orange-600 dark:text-orange-400' },
        serigrafia:     { icon: 'fa-fill-drip',    bubble: 'bg-indigo-100 dark:bg-indigo-900/30',  color: 'text-indigo-600 dark:text-indigo-400' },
        bordado:        { icon: 'fa-pen-nib',      bubble: 'bg-pink-100 dark:bg-pink-900/30',      color: 'text-pink-600 dark:text-pink-400' },
        emborrachado:   { icon: 'fa-cubes',        bubble: 'bg-emerald-100 dark:bg-emerald-900/30',color: 'text-emerald-600 dark:text-emerald-400' },
        sub_local:      { icon: 'fa-layer-group',  bubble: 'bg-purple-100 dark:bg-purple-900/30',  color: 'text-purple-600 dark:text-purple-400' },
        sub_total:      { icon: 'fa-image',        bubble: 'bg-cyan-100 dark:bg-cyan-900/30',      color: 'text-cyan-700 dark:text-cyan-300' },
        lisas:          { icon: 'fa-shirt',        bubble: 'bg-gray-100 dark:bg-slate-700/50',     color: 'text-gray-600 dark:text-gray-300' },
        default:        { icon: 'fa-shirt',        bubble: 'bg-gray-100 dark:bg-slate-700/50',     color: 'text-[#7c3aed] dark:text-[#7c3aed]' }
    };
    window.personalizationIconMap = personalizationIconMap;

    function normalizePersonalizationKey(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^\w]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }
    window.normalizePersonalizationKey = normalizePersonalizationKey;

    function getPersonalizationSortWeight(item) {
        const key = normalizePersonalizationKey(item?.slug || item?.name || '');
        if (key.includes('dtf')) return 10;
        if (key.includes('serigraf')) return 20;
        if (key.includes('bordad')) return 30;
        if (key.includes('emborrach')) return 40;
        if (key.includes('sub_local') || (key.includes('sub') && key.includes('local'))) return 50;
        if (key.includes('sub_total') || (key.includes('sub') && key.includes('total'))) return 60;
        if (key.includes('lisa')) return 70;
        return 900;
    }
    window.getPersonalizationSortWeight = getPersonalizationSortWeight;

    // Mapa de cores conhecidas por nome
    const colorNameToHex = {
        'preto': '#000000', 'black': '#000000',
        'branco': '#FFFFFF', 'white': '#FFFFFF',
        'azul': '#2563EB', 'blue': '#2563EB', 'azul marinho': '#1E3A5F', 'azul royal': '#4169E1', 'azul celeste': '#87CEEB', 'azul turquesa': '#40E0D0',
        'vermelho': '#DC2626', 'red': '#DC2626', 'vermelho escuro': '#8B0000', 'bordÃ´': '#800020', 'vinho': '#722F37',
        'verde': '#16A34A', 'green': '#16A34A', 'verde limÃ£o': '#32CD32', 'verde escuro': '#006400', 'verde musgo': '#8A9A5B', 'verde militar': '#4B5320', 'verde Ã¡gua': '#66CDAA',
        'amarelo': '#F59E0B', 'yellow': '#F59E0B', 'amarelo ouro': '#FFD700', 'mostarda': '#FFDB58',
        'laranja': '#EA580C', 'orange': '#EA580C',
        'rosa': '#EC4899', 'pink': '#EC4899', 'rosa claro': '#FFB6C1', 'rosa pink': '#FF69B4', 'rosa bebÃª': '#F4C2C2',
        'roxo': '#7C3AED', 'purple': '#7C3AED', 'violeta': '#8B5CF6', 'lilÃ¡s': '#C8A2C8',
        'cinza': '#6B7280', 'gray': '#6B7280', 'grey': '#6B7280', 'cinza claro': '#D1D5DB', 'cinza escuro': '#374151', 'cinza mescla': '#9CA3AF', 'mescla': '#9CA3AF', 'chumbo': '#36454F',
        'marrom': '#92400E', 'brown': '#92400E', 'cafÃ©': '#6F4E37', 'chocolate': '#7B3F00', 'caramelo': '#FFD59A', 'bege': '#F5F5DC',
        'nude': '#E3BC9A', 'salmÃ£o': '#FA8072', 'coral': '#FF7F50', 'creme': '#FFFDD0', 'off-white': '#FAF9F6',
        'dourado': '#FFD700', 'gold': '#FFD700', 'prata': '#C0C0C0', 'silver': '#C0C0C0',
        'cyan': '#06B6D4', 'ciano': '#06B6D4', 'magenta': '#D946EF', 'fucsia': '#FF00FF'
    };
    window.colorNameToHex = colorNameToHex;

    function getColorHex(colorName) {
        if (!colorName) return '#ccc';
        const normalized = colorName.toLowerCase().trim();
        return colorNameToHex[normalized] || '#ccc';
    }
    window.getColorHex = getColorHex;

    @php
        $safeSublimationTypes = isset($sublimationTypes) 
            ? $sublimationTypes->map(fn($t) => ['slug' => $t->slug, 'name' => $t->name])->values()
            : [];
        $safePreselectedTypes = $preselectedTypes ?? [];
    @endphp

    // SUB. TOTAL - Dados e Configurações
    const sublimationEnabled = {{ isset($sublimationEnabled) && $sublimationEnabled ? 'true' : 'false' }};
    window.sublimationEnabled = sublimationEnabled;
    const sublimationTypes = @json($safeSublimationTypes);
    window.sublimationTypes = sublimationTypes;
    let sublimationAddonsCache = {};
    window.sublimationAddonsCache = sublimationAddonsCache;

    @php
        // $sizeSurcharges agora é um array PHP puro (getDefaultSurcharges())
        $safeSizeSurcharges = isset($sizeSurcharges) ? array_map(fn($s) => [
            'size' => $s['size'],
            'price_from' => (float) $s['price_from'],
            'price_to' => $s['price_to'] !== null ? (float) $s['price_to'] : null,
            'surcharge' => (float) $s['surcharge'],
        ], $sizeSurcharges) : [];
    @endphp
    const sizeSurchargesData = @json($safeSizeSurcharges);
    window.sizeSurchargesData = sizeSurchargesData;

    function getFullpageSizeSurcharge(size, baseUnitPrice) {
        const normalized = (size === 'Especial' || size === 'ESPECIAL') ? 'ESPECIAL' : size;
        const matches = (window.sizeSurchargesData || []).filter(s =>
            s.size === normalized &&
            s.price_from <= baseUnitPrice &&
            (s.price_to === null || s.price_to >= baseUnitPrice)
        );
        if (!matches.length) return 0;
        matches.sort((a, b) => b.price_from - a.price_from);
        return matches[0].surcharge;
    }
    window.getFullpageSizeSurcharge = getFullpageSizeSurcharge;
    
    // Tipos de personalização pré-selecionados na etapa anterior
    const preselectedPersonalizationTypes = @json($safePreselectedTypes);
    window.preselectedPersonalizationTypes = preselectedPersonalizationTypes;

    let isInSublimationMode = false;
    window.isInSublimationMode = isInSublimationMode;

    let itemToDeleteId = null;

    function openDeleteModal(itemId) {
        itemToDeleteId = itemId;
        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }
    window.openDeleteModal = openDeleteModal;

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        itemToDeleteId = null;
    }
    window.closeDeleteModal = closeDeleteModal;

    // VariÃ¡vel global para dados dos itens
    let itemsData = {!! json_encode($order->items->toArray()) !!};
    window.itemsData = itemsData;

    async function confirmDelete() {
        if (!itemToDeleteId) return;

        const btn = document.querySelector('#delete-modal button.bg-red-600');
        const originalText = btn ? btn.innerText : 'Sim, Remover';
        if (btn) {
            btn.innerHTML = 'Removendo...';
            btn.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('action', 'delete_item');
            formData.append('item_id', itemToDeleteId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch("{{ route('orders.wizard.sewing') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Optimistic: The item is already being updated by the sidebar HTML below
                // handle instant visual feedback
                const itemEl = document.getElementById(`sidebar-item-${itemToDeleteId}`);
                if (itemEl) itemEl.style.opacity = '0';

                // Atualizar HTML da sidebar
                const sidebar = document.getElementById('items-sidebar-container');
                if (sidebar) {
                    sidebar.innerHTML = data.html;
                }
                
                // Atualizar dados dos itens
                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }
                
                if (window.showToast) window.showToast('Item removido com sucesso!', 'success');
                closeDeleteModal();
            } else {
                alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro na exclusão:', error);
            alert('Erro ao processar a exclusão.');
        } finally {
            if (btn) {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
    }
    window.confirmDelete = confirmDelete;

    async function handleSewingFormSubmit(e) {
        if (e) e.preventDefault();
        
        const form = document.getElementById('sewing-form');
        const submitBtn = document.getElementById('wizard-submit-btn'); // Changed to wizard-submit-btn
        if (!form || form.dataset.submitting === 'true') return;

        // Validação atualizada para o Wizard
        const personalizacaoInputs = document.querySelectorAll('input[name="personalizacao[]"]');
        
        if (personalizacaoInputs.length === 0) {
             const preselected = document.querySelectorAll('.preselected-personalization');
             if (preselected.length === 0) {
                 alert('Por favor, selecione pelo menos uma personalização.');
                 return;
             }
        }

        const quantity = parseInt(document.getElementById('quantity').value || 0);
        
        if (quantity === 0) {
             let hasSize = false;
             document.querySelectorAll('input[name^="tamanhos"]').forEach(i => {
                 if(parseInt(i.value) > 0) hasSize = true;
             });
             if (!hasSize) {
                 alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
                 return;
             }
        }

        // UI de processamento
        // const submitBtn = document.getElementById('submit-button'); // Original line, now using wizard-submit-btn
        let originalText = '';
        if (submitBtn) {
            originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processando...
            `;
        }
        form.dataset.submitting = 'true';

        try {
            const formData = new FormData(form);
            
            // Fix: Manually append the cover image if it exists in the detached modal
            const wizardFileInput = document.getElementById('wizard_file_input');
            if (wizardFileInput && wizardFileInput.files && wizardFileInput.files[0]) {
                formData.append('item_cover_image', wizardFileInput.files[0]);
            }

            const actionUrl = form.dataset.actionUrl || form.getAttribute('action');
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Erro HTTP na submissão:', response.status, text);
                alert('Erro ao salvar item: ' + (response.statusText || 'erro HTTP'));
                return;
            }

            const data = await response.json();

            if (data.success) {
                if (data.html) {
                    const sidebar = document.getElementById('items-sidebar-container');
                    if (sidebar) sidebar.innerHTML = data.html;
                }

                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }

                const action = document.getElementById('form-action').value;
                if (action === 'add_item') {
                    // Reset form instead of reload
                    if (typeof window.resetForm === 'function') window.resetForm();
                    else form.reset();
                    
                    if (window.showToast) window.showToast('Item adicionado com sucesso!', 'success');
                } else {
                    if (window.showToast) window.showToast('Item atualizado com sucesso!', 'success');
                }
                
                cancelEdit(); 
                
            } else {
                 if (data.errors) {
                     let msg = 'Erros de validação:\n';
                     for (let field in data.errors) {
                         msg += `- ${data.errors[field].join(', ')}\n`;
                     }
                     alert(msg);
                 } else {
                     alert(data.message || 'Erro ao salvar item.');
                 }
            }

        } catch (error) {
            console.error('Erro no envio:', error);
            alert('Ocorreu um erro ao processar sua solicitação.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = document.getElementById('form-action').value === 'update_item' ? 'Salvar Alterações' : 'Adicionar Item';
            }
            if (form) form.dataset.submitting = 'false';
        }
    }
    function resetForm() {
        if (typeof window.resetSewingWizard === 'function') {
            window.resetSewingWizard();
        }
        
        const form = document.getElementById('sewing-form');
        if (form) {
            form.reset();
            const editingId = document.getElementById('editing-item-id');
            if (editingId) editingId.value = '';
            
            const formAction = document.getElementById('form-action');
            if (formAction) formAction.value = 'add_item';
            
            const formTitle = document.getElementById('form-title');
            if (formTitle) formTitle.innerText = 'Adicionar Novo Item';

            // Reset selected types in Step 1
            const types = document.querySelectorAll('.personalization-type-checkbox');
            types.forEach(cb => cb.checked = false);
            
            // Clear current customization tags
            const tags = document.getElementById('main-summary-tags');
            if (tags) {
                tags.innerHTML = '';
                tags.classList.add('hidden');
            }
            
            // Back to step 1
            if (typeof window.goToWizardStep === 'function') {
                window.goToWizardStep(1);
            }

            resetFabricPieceSelection();
        }
    }
    window.resetForm = resetForm;

    window.handleSewingFormSubmit = handleSewingFormSubmit;

    let optionsWithParents = {};
    window.optionsWithParents = optionsWithParents;

    function isGroupedOptionsPayload(payload) {
        if (!payload || typeof payload !== 'object') return false;
        const keys = ['personalizacao', 'tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola'];
        return keys.some(k => Array.isArray(payload[k]));
    }
    window.isGroupedOptionsPayload = isGroupedOptionsPayload;

    function loadOptions() {
        const apiBase = "{{ url('/api') }}";

        fetch(`${apiBase}/product-options`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status} loading product-options: ${text.slice(0, 200)}`);
                }
                return response.json();
            })
            .then(data => {
                if (isGroupedOptionsPayload(data)) {
                    options = data;
                    window.options = options;
                } else {
                    console.warn('VESTALIZE: /api/product-options returned unexpected payload; keeping existing window.options', data);
                }
                return fetch(`${apiBase}/product-options-with-parents`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status} loading product-options-with-parents: ${text.slice(0, 200)}`);
                }
                return response.json();
            })
            .then(data => {
                if (isGroupedOptionsPayload(data)) {
                    optionsWithParents = data;
                    window.optionsWithParents = optionsWithParents;
                } else {
                    console.warn('VESTALIZE: /api/product-options-with-parents returned unexpected payload; keeping existing window.optionsWithParents', data);
                }

                if (typeof window.loadWizardOptionsForStep === 'function' && typeof window.wizardCurrentStep !== 'undefined') {
                    loadWizardOptionsForStep(window.wizardCurrentStep);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar opções:', error);
                // Don't fail the page: keep server-provided window.options as fallback.
                if (typeof window.loadWizardOptionsForStep === 'function' && typeof window.wizardCurrentStep !== 'undefined') {
                    loadWizardOptionsForStep(window.wizardCurrentStep);
                }
            });
    }
    window.loadOptions = loadOptions;

    async function loadStockByCutType() {
        const cutTypeId = document.getElementById('tipo_corte')?.value;
        
        if (!cutTypeId) {
            const stockSection = document.getElementById('stock-info-section');
            if (stockSection) stockSection.classList.add('hidden');
            return;
        }
        
        try {
            const params = new URLSearchParams({
                cut_type_id: cutTypeId
            });
            
            const response = await fetch(`/api/stocks/by-cut-type?${params}`);
            const data = await response.json();
            
            const stockSection = document.getElementById('stock-info-section');
            const stockBySize = document.getElementById('stock-by-size');
            
            if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
                let html = '';
                data.stock_by_size.forEach(item => {
                    const hasStock = item.available > 0;
                    const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600';
                    
                    let storeDetails = '';
                    if (item.stores && item.stores.length > 0) {
                        storeDetails = item.stores.map(store => {
                            const storeHasStock = store.available > 0;
                            const storeColor = storeHasStock ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500';
                            const storeBg = storeHasStock ? 'bg-green-100 dark:bg-green-900/40' : 'bg-gray-100 dark:bg-gray-600';
                            return `<span class="ml-2 px-2 py-0.5 text-xs rounded ${storeBg} ${storeColor}" title="${store.store_name}">
                                ${store.store_name.replace('Loja ', '')}: ${store.available}${store.reserved > 0 ? ' (R:' + store.reserved + ')' : ''}
                            </span>`;
                        }).join('');
                    }
                    
                    html += `
                        <div class="p-2 ${bgColor} rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${item.size}:</span>
                                ${hasStock ? `
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                         ${item.available} total
                                    </span>
                                ` : `
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                         Sem estoque
                                    </span>
                                `}
                            </div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                ${storeDetails}
                            </div>
                        </div>
                    `;
                });
                
                if (stockSection && stockBySize) {
                    stockSection.classList.remove('hidden');
                    stockBySize.innerHTML = html;
                }
            } else {
                if (stockSection) {
                    stockSection.classList.remove('hidden');
                    if (stockBySize) {
                        stockBySize.innerHTML = '<p class="text-sm text-yellow-600 dark:text-yellow-400 text-center py-2"> Nenhum estoque cadastrado para este produto</p>';
                    }
                }
            }
        } catch (error) {
            console.error('Erro ao buscar estoque:', error);
            const stockSection = document.getElementById('stock-info-section');
            if (stockSection) stockSection.classList.add('hidden');
        }
    }
    window.loadStockByCutType = loadStockByCutType;

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        const qtyInput = document.getElementById('quantity');
        if (qtyInput) qtyInput.value = total;
        
        // Update sidebar if needed...
    }
    window.calculateTotal = calculateTotal;



    // --- WIZARD LOGIC ---
    let wizardCurrentStep = 1;
    window.wizardCurrentStep = wizardCurrentStep;
    const wizardTotalSteps = 11;
    window.wizardTotalSteps = wizardTotalSteps;
    const isAdmin = @json(auth()->user()->isAdmin());
    window.isAdmin = isAdmin;

    let wizardData = {
        tecido: null,
        tipo_tecido: null,
        cor: null,
        tipo_corte: null,
        detalhe: [], // Alterado para array
        detail_color: null,
        detail_colors: {}, // Novo: para cores individuais por detalhe
        individual_detail_colors: false, // Novo toggle
        gola: null,
        collar_color: null,
        personalizacao: [],
        image: null,
        imageUrl: null,
        notes: '',
        sizes: {},
        unit_cost: 0,
        unit_price: 0
    };
    window.wizardData = wizardData;

    let selectedPersonalizacoes = [];
    window.selectedPersonalizacoes = selectedPersonalizacoes;

    function ensureSewingWizardPortal() {
        const sewingForm = document.getElementById('sewing-form');
        const modalInsideForm = sewingForm ? sewingForm.querySelector('#sewing-wizard-modal') : null;
        const modal = modalInsideForm || document.getElementById('sewing-wizard-modal');
        if (!modal) return null;

        document.querySelectorAll('#sewing-wizard-modal').forEach(existingModal => {
            if (existingModal !== modal) existingModal.remove();
        });

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        return modal;
    }
    window.ensureSewingWizardPortal = ensureSewingWizardPortal;

    function openSewingWizard() {
        const modal = ensureSewingWizardPortal();
        if (modal) {
            const submitButton = document.getElementById('wizard-submit-btn');
            const formAction = document.getElementById('form-action')?.value;
            if (submitButton) {
                submitButton.innerHTML = formAction === 'update_item'
                    ? 'Salvar Alterações'
                    : 'Confirmar e Adicionar Item';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            updateWizardUI();
        }
    }
    window.openSewingWizard = openSewingWizard;

    function closeSewingWizard() {
        const modal = ensureSewingWizardPortal();
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    window.closeSewingWizard = closeSewingWizard;

    function wizardNextStep() {
        if (!validateStep(wizardCurrentStep)) return;

        // Check if SUB.TOTAL is selected at step 1 - redirect to step-sub
        if (wizardCurrentStep === 1 && isSublimationTotalSelected()) {
            // Close modal and show fullpage sublimation form
            closeSewingWizard();
            showSubFullpageForm();
            return;
        }


        if (wizardCurrentStep < wizardTotalSteps) {
            // Skip logic for Detail Color
            if (wizardCurrentStep === 5) {
                const isDifferentDetail = document.getElementById('different_detail_color_cb')?.checked;
                if (!hasWizardRealDetail() || !isDifferentDetail) {
                    wizardData.detail_color = wizardData.cor;
                    wizardCurrentStep += 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            // Skip logic for Collar Color
            if (wizardCurrentStep === 7) {
                const isDifferentCollar = document.getElementById('different_collar_color_cb')?.checked;
                const gola = wizardData.gola;
                if (!gola || !gola.name || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                    wizardData.collar_color = wizardData.cor;
                    wizardCurrentStep += 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            wizardCurrentStep++;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
        }
    }
    window.wizardNextStep = wizardNextStep;

    function wizardPrevStep() {
        // Handle going back from step-sub
        if (isInSublimationMode) {
            isInSublimationMode = false;
            window.isInSublimationMode = false;
            
            // Hide step-sub and sublimation submit button
            const subStep = document.getElementById('step-sub');
            if (subStep) subStep.classList.add('hidden');
            
            const subSubmitBtn = document.getElementById('wizard-sub-submit-btn');
            if (subSubmitBtn) subSubmitBtn.classList.add('hidden');
            
            // Show next button
            const nextBtn = document.getElementById('wizard-next-btn');
            if (nextBtn) nextBtn.classList.remove('hidden');
            
            // Reset sublimation step
            resetSubWizardStep();
            
            // Go back to step 1
            wizardCurrentStep = 1;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
            return;
        }
        
        if (wizardCurrentStep > 1) {
            // Skip logic backward for Detail Color
            if (wizardCurrentStep === 7) {
                const isDifferentDetail = document.getElementById('different_detail_color_cb')?.checked;
                if (!hasWizardRealDetail() || !isDifferentDetail) {
                    wizardCurrentStep -= 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            // Skip logic backward for Sizes
            if (wizardCurrentStep === 9) {
                const isDifferentCollar = document.getElementById('different_collar_color_cb')?.checked;
                const gola = wizardData.gola;
                if (!gola || !gola.name || gola.name.toLowerCase().includes('sem') || !isDifferentCollar) {
                    wizardCurrentStep -= 2;
                    window.wizardCurrentStep = wizardCurrentStep;
                    updateWizardUI();
                    return;
                }
            }

            wizardCurrentStep--;
            window.wizardCurrentStep = wizardCurrentStep;
            updateWizardUI();
        }
    }
    window.wizardPrevStep = wizardPrevStep;


    function validateStep(step) {
        if (step === 1) {
             if (!wizardData.personalizacao || wizardData.personalizacao.length === 0) {
                 alert('Selecione pelo menos uma personalização.');
                 return false;
             }
        }
        if (step === 2) {
            if (!wizardData.tecido) {
                alert('Selecione um tecido para continuar.');
                return false;
            }
        }
        if (step === 3) {
            if (!wizardData.cor) {
                alert('Selecione uma cor para continuar.');
                return false;
            }
        }
        if (step === 4) {
             if (!wizardData.tipo_corte) {
                alert('Selecione um tipo de corte.');
                return false;
            }
        }
        if (step === 9) {
            const total = calculateWizardTotal();
            if (total <= 0) {
                alert('Informe a quantidade de peças (pelo menos 1).');
                return false;
            }
            wizardData.sizes = {};
            document.querySelectorAll('.wizard-size-input').forEach(input => {
                const val = parseInt(input.value) || 0;
                if(val > 0) wizardData.sizes[input.dataset.size] = val;
            });
        }
        return true;
    }
    window.validateStep = validateStep;

    function updateWizardUI() {
        const titles = [
            "Personalização", "Tecido", "Cor do Tecido", "Modelo", "Detalhe", "Cor do Detalhe", 
            "Gola", "Cor da Gola", "Tamanhos", "Imagem / Obs", "Resumo"
        ];
        const titleEl = document.getElementById('wizard-step-title');
        if (titleEl) titleEl.textContent = `${titles[wizardCurrentStep-1]} (Etapa ${wizardCurrentStep} de ${wizardTotalSteps})`;
        
        const progressEl = document.getElementById('wizard-progress');
        if (progressEl) progressEl.style.width = `${(wizardCurrentStep / wizardTotalSteps) * 100}%`;

        for (let i = 1; i <= wizardTotalSteps; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            if (stepEl) {
                if (i === wizardCurrentStep) {
                    stepEl.classList.remove('hidden');
                    loadWizardOptionsForStep(wizardCurrentStep);
                } else {
                    stepEl.classList.add('hidden');
                }
            }
        }
        
        const prevBtn = document.getElementById('wizard-prev-btn');
        if (prevBtn) {
            prevBtn.disabled = wizardCurrentStep === 1;
            prevBtn.classList.toggle('opacity-50', wizardCurrentStep === 1);
            prevBtn.classList.toggle('cursor-not-allowed', wizardCurrentStep === 1);
        }
        
        const nextBtn = document.getElementById('wizard-next-btn');
        if (nextBtn) {
            if (wizardCurrentStep === wizardTotalSteps) {
                nextBtn.setAttribute('style', 'display: none !important');
            } else {
                nextBtn.style.display = 'flex';
            }
        }

        const submitBtn = document.getElementById('wizard-submit-btn');
        if (submitBtn) {
            if (wizardCurrentStep === wizardTotalSteps) {
                submitBtn.setAttribute('style', 'display: flex !important');
            } else {
                submitBtn.style.display = 'none';
            }
        }

        if (wizardCurrentStep === 11) {
            updateFinalSummary();
        }
    }
    window.updateWizardUI = updateWizardUI;

    function getOptionList(possibleKeys) {
        for (const key of possibleKeys) {
            if (optionsWithParents && optionsWithParents[key] && Array.isArray(optionsWithParents[key]) && optionsWithParents[key].length) {
                return optionsWithParents[key];
            }
            if (options && options[key] && Array.isArray(options[key]) && options[key].length) {
                return options[key];
            }
        }
        return [];
    }
    window.getOptionList = getOptionList;

    function getWizardDetalhes() {
        if (Array.isArray(wizardData.detalhe)) return wizardData.detalhe.filter(Boolean);
        if (wizardData.detalhe) return [wizardData.detalhe];
        return [];
    }
    window.getWizardDetalhes = getWizardDetalhes;

    function getWizardPrimaryDetalhe() {
        return getWizardDetalhes()[0] || null;
    }
    window.getWizardPrimaryDetalhe = getWizardPrimaryDetalhe;

    function hasWizardRealDetail() {
        return getWizardDetalhes().some(detail => {
            const name = (detail?.name || '').toLowerCase();
            return name && !name.includes('sem');
        });
    }
    window.hasWizardRealDetail = hasWizardRealDetail;

    function filterByParent(items, parentId) {
        if (!parentId) return items;

        const parentIds = (Array.isArray(parentId) ? parentId : [parentId])
            .map(id => id?.toString())
            .filter(id => id !== undefined && id !== null && id !== '');

        if (parentIds.length === 0) return items;

        return items.filter(item => {
            if (Array.isArray(item.parent_ids)) {
                return item.parent_ids.some(pid => parentIds.includes(pid?.toString()));
            }
            if (item.parent_id !== undefined && item.parent_id !== null) {
                return parentIds.includes(item.parent_id?.toString());
            }
            return true;
        });
    }
    window.filterByParent = filterByParent;

    function renderWizardPersonalizacao() {
        const container = document.getElementById('wizard-options-personalizacao');
        if (!container) return;

        const personalizacaoList = getOptionList(['personalizacao']);
        if (personalizacaoList.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma opção disponível.</p>';
            return;
        }

        container.innerHTML = personalizacaoList.map(item => {
            const isSelected = wizardData.personalizacao.includes(item.id.toString()) || wizardData.personalizacao.includes(item.id);
            const activeClass = isSelected ? 'ring-2 ring-[#7c3aed] is-selected shadow-sm' : '';
            const key = normalizePersonalizationKey(item.slug || item.name || '');
            const style = personalizationIconMap[key] || personalizationIconMap.default;

            return `
            <label class="wizard-option-card wizard-personalization-card group cursor-pointer p-3 sm:p-3.5 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all duration-200 ease-in-out flex flex-col items-center justify-center gap-2 ${activeClass}">
                <input type="checkbox" class="personalizacao-checkbox hidden" value="${item.id ?? ''}" ${isSelected ? 'checked' : ''} onchange="syncWizardPersonalizacaoUI()">
                <div class="wizard-personalization-icon w-11 h-11 sm:w-12 sm:h-12 rounded-full ${style.bubble} flex items-center justify-center ${style.color}">
                     <i class="fa-solid ${style.icon} text-base"></i>
                </div>
                <span class="text-[11px] sm:text-xs font-bold text-center leading-tight text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${item.name}</span>
            </label>
            `;
        }).join('');

        syncWizardPersonalizacaoUI();
    }
    window.renderWizardPersonalizacao = renderWizardPersonalizacao;

    function syncWizardPersonalizacaoUI() {
        const container = document.getElementById('wizard-options-personalizacao');
        if (!container) return;

        const cards = container.querySelectorAll('.wizard-option-card');
        const selectedIds = [];

        cards.forEach(card => {
            const checkbox = card.querySelector('.personalizacao-checkbox');
            if (!checkbox) return;

            const isChecked = checkbox.checked;
            card.classList.toggle('ring-2', isChecked);
            card.classList.toggle('ring-[#7c3aed]', isChecked);
            card.classList.toggle('is-selected', isChecked);
            card.classList.toggle('shadow-sm', isChecked);
            card.classList.toggle('scale-105', isChecked);

            if (isChecked && checkbox.value !== '') {
                selectedIds.push(checkbox.value.toString());
            }
        });

        wizardData.personalizacao = selectedIds;
        selectedPersonalizacoes = [...wizardData.personalizacao];
        window.selectedPersonalizacoes = selectedPersonalizacoes;

        const hiddenContainer = document.getElementById('hidden-personalizacao-container');
        if (hiddenContainer) {
            hiddenContainer.innerHTML = selectedIds
                .map(pid => `<input type="hidden" name="personalizacao[]" value="${pid}">`)
                .join('');
        }
    }
    window.syncWizardPersonalizacaoUI = syncWizardPersonalizacaoUI;

    function toggleWizardPersonalizacao(element) {
        if (!element) return;
        const checkbox = element.querySelector('.personalizacao-checkbox');
        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;
        syncWizardPersonalizacaoUI();
    }
    window.toggleWizardPersonalizacao = toggleWizardPersonalizacao;

    // --- Step 2: Tecidos ---
    function loadWizardTecidos() {
        const select = document.getElementById('wizard_tecido');
        if(!select) return;
        
        if (select.options.length <= 1) {
            let items = getOptionList(['tecido']);
            const tipoTecidoItems = getOptionList(['tipo_tecido']);
            const selectedIds = (selectedPersonalizacoes || []).map(id => id.toString());
            
            if (selectedIds.length > 0) {
                items = items.filter(tecido => {
                    const parentIds = Array.isArray(tecido.parent_ids) ? tecido.parent_ids.map(id => id.toString()) : [];
                    if (parentIds.length === 0) return true;
                    if (parentIds.some(pid => selectedIds.includes(pid))) return true;
                    // Se não houver no tecido, checar nos tipos de tecido vinculados
                    const hasTypeMatch = tipoTecidoItems.some(tipo => {
                        const tipoParentId = (tipo.parent_id || '').toString();
                        if (tipoParentId !== tecido.id.toString()) return false;
                        const tipoParentIds = Array.isArray(tipo.parent_ids) ? tipo.parent_ids.map(id => id.toString()) : [];
                        return tipoParentIds.some(pid => selectedIds.includes(pid));
                    });
                    return hasTypeMatch;
                });
            }

            select.innerHTML = '<option value="">Selecione o tecido</option>' + 
                items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
        }

        if (wizardData.tecido) {
            select.value = wizardData.tecido.id;
            loadWizardTiposTecido();
        }
    }
    window.loadWizardTecidos = loadWizardTecidos;
    
    function loadWizardTiposTecido() {
         const select = document.getElementById('wizard_tecido');
         const typeContainer = document.getElementById('wizard-tipo-tecido-container');
         const typeSelect = document.getElementById('wizard_tipo_tecido');
         
         if (!select || !typeContainer || !typeSelect) return;

         const fabricId = select.value;
         if(!fabricId) {
             wizardData.tecido = null;
             return;
         }
         
          const fabricName = select.options[select.selectedIndex].text;
          
          if (!wizardData.tecido || wizardData.tecido.id != fabricId) {
              wizardData.tecido = { id: fabricId, name: fabricName, price: 0 };
          }
          
          const subItems = filterByParent(getOptionList(['tipo_tecido']), fabricId);
          if(subItems.length > 0) {
              typeContainer.classList.remove('hidden');
              typeSelect.innerHTML = '<option value="">Selecione o tipo</option>' + 
                 subItems.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
              
              if (wizardData.tipo_tecido) {
                  const stillValid = subItems.find(s => s.id == wizardData.tipo_tecido.id);
                  if (stillValid) {
                      typeSelect.value = wizardData.tipo_tecido.id;
                  } else {
                      wizardData.tipo_tecido = null;
                  }
              }
          } else {
              typeContainer.classList.add('hidden');
              wizardData.tipo_tecido = null;
          }
         
         loadWizardCores(); 
    }
    window.loadWizardTiposTecido = loadWizardTiposTecido;
    
    function onWizardTipoTecidoChange() {
         const select = document.getElementById('wizard_tipo_tecido');
         if(select && select.value) {
             wizardData.tipo_tecido = { id: select.value, name: select.options[select.selectedIndex].text };
         } else {
             wizardData.tipo_tecido = null;
         }
    }
    window.onWizardTipoTecidoChange = onWizardTipoTecidoChange;

    function renderSelectableOptionCards(containerId, items, selectedId, onClickName) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (!items.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma opção disponível.</p>';
            return;
        }

        container.innerHTML = items.map(item => {
            const isActive = selectedId && selectedId.toString() === item.id.toString();
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            const price = parseFloat(item.price || 0);
            return `
                <button type="button" class="wizard-option-card text-left p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="${onClickName}('${item.id}')">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">${item.name}</div>
                    <div class="text-xs text-gray-500 mt-1">${price > 0 ? `+ R$ ${price.toFixed(2).replace('.', ',')}` : 'Sem acréscimo'}</div>
                </button>
            `;
        }).join('');
    }

    function getEligibleCutParentIds() {
        const parentIds = [];

        if (wizardData.tipo_tecido?.id) {
            parentIds.push(wizardData.tipo_tecido.id.toString());
        }

        if (wizardData.tecido?.id) {
            parentIds.push(wizardData.tecido.id.toString());

            const fabricTypes = filterByParent(getOptionList(['tipo_tecido']), wizardData.tecido.id);
            fabricTypes.forEach(item => {
                if (item?.id) {
                    parentIds.push(item.id.toString());
                }
            });
        }

        return [...new Set(parentIds.filter(Boolean))];
    }
    window.getEligibleCutParentIds = getEligibleCutParentIds;

    function renderWizardCortes() {
        const cutOptions = getOptionList(['tipo_corte', 'corte']);
        const eligibleParentIds = getEligibleCutParentIds();
        const items = eligibleParentIds.length
            ? filterByParent(cutOptions, eligibleParentIds)
            : cutOptions;

        renderSelectableOptionCards('wizard-options-corte', items, wizardData.tipo_corte?.id, 'selectWizardCorte');
    }
    window.renderWizardCortes = renderWizardCortes;

    function selectWizardCorte(id) {
        const cut = getOptionList(['tipo_corte', 'corte']).find(item => item.id == id);
        if (!cut) return;
        wizardData.tipo_corte = { id: cut.id, name: cut.name, price: parseFloat(cut.price || 0) };
        wizardData.detalhe = [];
        wizardData.detail_color = null;
        wizardData.detail_colors = {};
        updateWizardUI();
        wizardNextStep();
    }
    window.selectWizardCorte = selectWizardCorte;

    function renderWizardDetalhes() {
        const container = document.getElementById('wizard-options-detalhe');
        if (!container) return;

        const items = filterByParent(getOptionList(['detalhe']), wizardData.tipo_corte?.id || null);
        if (!items.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhum detalhe disponível.</p>';
            return;
        }

        const selectedIds = getWizardDetalhes().map(detail => detail.id.toString());
        container.innerHTML = items.map(item => {
            const isActive = selectedIds.includes(item.id.toString());
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            const price = parseFloat(item.price || 0);
            return `
                <button type="button" class="wizard-option-card text-left p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="toggleWizardDetalhe('${item.id}')">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">${item.name}</div>
                    <div class="text-xs text-gray-500 mt-1">${price > 0 ? `+ R$ ${price.toFixed(2).replace('.', ',')}` : 'Sem acréscimo'}</div>
                </button>
            `;
        }).join('');

        toggleDetailColorUI();
    }
    window.renderWizardDetalhes = renderWizardDetalhes;

    function toggleWizardDetalhe(id) {
        const detail = getOptionList(['detalhe']).find(item => item.id == id);
        if (!detail) return;

        const details = getWizardDetalhes();
        const detailId = detail.id.toString();
        const index = details.findIndex(item => item.id.toString() === detailId);

        if (index >= 0) {
            details.splice(index, 1);
            delete wizardData.detail_colors[detailId];
        } else {
            details.push({ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) });
        }

        wizardData.detalhe = details;
        if (!hasWizardRealDetail()) {
            wizardData.detail_color = wizardData.cor;
            wizardData.detail_colors = {};
            wizardData.individual_detail_colors = false;
        }

        renderWizardDetalhes();
        renderWizardDetailColorOptions();
    }
    window.toggleWizardDetalhe = toggleWizardDetalhe;

    function toggleDetailColorUI() {
        const hasDifferentColor = !!document.getElementById('different_detail_color_cb')?.checked && hasWizardRealDetail();
        const individualContainer = document.getElementById('individual-colors-toggle-container');
        const individualCheckbox = document.getElementById('individual_detail_colors_cb');

        if (individualContainer) individualContainer.classList.toggle('hidden', !hasDifferentColor);

        if (!hasDifferentColor) {
            wizardData.individual_detail_colors = false;
            wizardData.detail_colors = {};
            wizardData.detail_color = wizardData.cor;
            if (individualCheckbox) individualCheckbox.checked = false;
        } else if (individualCheckbox) {
            wizardData.individual_detail_colors = !!individualCheckbox.checked;
        }

        renderWizardDetailColorOptions();
    }
    window.toggleDetailColorUI = toggleDetailColorUI;

    function renderWizardDetailColorOptions() {
        const container = document.getElementById('wizard-options-cor-detalhe');
        if (!container) return;

        const colors = getOptionList(['cor']);
        if (!document.getElementById('different_detail_color_cb')?.checked || !hasWizardRealDetail()) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Os detalhes usarão a mesma cor do tecido.</p>';
            return;
        }

        if (!colors.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma cor disponível.</p>';
            return;
        }

        const details = getWizardDetalhes();
        if (wizardData.individual_detail_colors && details.length > 1) {
            container.innerHTML = details.map(detail => {
                const detailId = detail.id.toString();
                const selectedColorId = (wizardData.detail_colors[detailId] || '').toString();
                return `
                    <div class="col-span-full border border-gray-200 dark:border-slate-700 rounded-xl p-3">
                        <div class="text-sm font-bold text-gray-900 dark:text-white mb-3">${detail.name}</div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            ${colors.map(color => {
                                const activeClass = selectedColorId === color.id.toString() ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
                                return `
                                    <button type="button" class="wizard-option-card p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                                        onclick="selectWizardDetailColor('${detailId}', '${color.id}')">
                                        <div class="w-8 h-8 mx-auto rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                                        <div class="mt-2 text-xs font-bold text-center text-gray-700 dark:text-slate-300">${color.name}</div>
                                    </button>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }).join('');
            return;
        }

        const selectedColorId = (wizardData.detail_color?.id || '').toString();
        container.innerHTML = colors.map(color => {
            const activeClass = selectedColorId === color.id.toString() ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            return `
                <button type="button" class="wizard-option-card p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="selectWizardDetailColor('', '${color.id}')">
                    <div class="w-8 h-8 mx-auto rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                    <div class="mt-2 text-xs font-bold text-center text-gray-700 dark:text-slate-300">${color.name}</div>
                </button>
            `;
        }).join('');
    }
    window.renderWizardDetailColorOptions = renderWizardDetailColorOptions;

    function selectWizardDetailColor(detailId, colorId) {
        const color = getOptionList(['cor']).find(item => item.id == colorId);
        if (!color) return;

        if (detailId) {
            wizardData.detail_colors[detailId.toString()] = color.id.toString();
        } else {
            wizardData.detail_color = { id: color.id, name: color.name, price: 0 };
        }

        renderWizardDetailColorOptions();
    }
    window.selectWizardDetailColor = selectWizardDetailColor;

    function renderWizardGolas() {
        if (wizardData.is_sublimation_total && wizardData.tipo_sublimacao_total) {
            const typeSlug = wizardData.tipo_sublimacao_total.id;
            const cache = sublimationAddonsCache[typeSlug];
            if (cache && Array.isArray(cache.collars) && cache.collars.length > 0) {
                const items = cache.collars.map(c => ({
                    id: c,
                    name: c,
                    price: 0
                }));
                renderSelectableOptionCards('wizard-options-gola', items, wizardData.gola?.id, 'selectWizardGola');
                return;
            }
        }
        console.log('VESTALIZE: Rendering Golas. Current Cut ID:', wizardData.tipo_corte?.id);
        const allGolas = getOptionList(['gola']);
        console.log('VESTALIZE: Total Gola options available:', allGolas.length);
        
        let items = filterByParent(allGolas, wizardData.tipo_corte?.id || null);
        
        // If no golas found for specific cut, maybe show all golas that have no specific parent designated?
        // Or if still empty, show all active golas as fallback if cut is "Básica" (often a generic case)
        if (items.length === 0 && allGolas.length > 0) {
            console.warn('VESTALIZE: No Golas found for cut ID', wizardData.tipo_corte?.id, '. Showing all golas with no parent.');
            items = allGolas.filter(item => !item.parent_ids || item.parent_ids.length === 0);
            
            // If still empty and it's a known common case like "Básica", maybe show all?
            if (items.length === 0) {
                console.warn('VESTALIZE: Still no Golas. Fallback to all Golas.');
                items = allGolas;
            }
        }
        
        console.log('VESTALIZE: Golas to render:', items.length);
        renderSelectableOptionCards('wizard-options-gola', items, wizardData.gola?.id, 'selectWizardGola');
    }
    window.renderWizardGolas = renderWizardGolas;

    function selectWizardGola(id) {
        let collar = getOptionList(['gola']).find(item => item.id == id);
        
        if (!collar && wizardData.is_sublimation_total && wizardData.tipo_sublimacao_total) {
            const typeSlug = wizardData.tipo_sublimacao_total.id;
            const cache = sublimationAddonsCache[typeSlug];
            if (cache && Array.isArray(cache.collars) && cache.collars.includes(id)) {
                collar = { id: id, name: id, price: 0 };
            }
        }
        if (!collar) return;
        wizardData.gola = { id: collar.id, name: collar.name, price: parseFloat(collar.price || 0) };
        updateWizardUI();
        wizardNextStep();
    }
    window.selectWizardGola = selectWizardGola;

    function renderWizardCollarColorOptions() {
        const container = document.getElementById('wizard-options-cor-gola');
        if (!container) return;

        const colors = getOptionList(['cor']);
        if (!document.getElementById('different_collar_color_cb')?.checked) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">A gola usará a mesma cor do tecido.</p>';
            return;
        }

        if (!colors.length) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhuma cor disponível.</p>';
            return;
        }

        const selectedColorId = (wizardData.collar_color?.id || '').toString();
        container.innerHTML = colors.map(color => {
            const activeClass = selectedColorId === color.id.toString() ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            return `
                <button type="button" class="wizard-option-card p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] transition-all ${activeClass}"
                    onclick="selectWizardCollarColor('${color.id}')">
                    <div class="w-8 h-8 mx-auto rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                    <div class="mt-2 text-xs font-bold text-center text-gray-700 dark:text-slate-300">${color.name}</div>
                </button>
            `;
        }).join('');
    }
    window.renderWizardCollarColorOptions = renderWizardCollarColorOptions;

    function selectWizardCollarColor(colorId) {
        const color = getOptionList(['cor']).find(item => item.id == colorId);
        if (!color) return;
        wizardData.collar_color = { id: color.id, name: color.name };
        renderWizardCollarColorOptions();
    }
    window.selectWizardCollarColor = selectWizardCollarColor;

    function loadWizardOptionsForStep(step) {
        switch (step) {
            case 1:
                renderWizardPersonalizacao();
                break;
            case 8:
                renderWizardCollarColorOptions();
                break;
            case 2:
                loadWizardTecidos();
                break;
            case 3:
                loadWizardCores();
                break;
            case 4:
                renderWizardCortes();
                break;
            case 5:
                renderWizardDetalhes();
                break;
            case 6:
                renderWizardDetailColorOptions();
                break;
            case 7:
                renderWizardGolas();
                break;
            default:
                break;
        }
    }
    window.loadWizardOptionsForStep = loadWizardOptionsForStep;

    function goToWizardStep(step) {
        wizardCurrentStep = Math.min(Math.max(parseInt(step, 10) || 1, 1), wizardTotalSteps);
        window.wizardCurrentStep = wizardCurrentStep;
        updateWizardUI();
    }
    window.goToWizardStep = goToWizardStep;

    // --- Step 3: Cores ---
    function loadWizardCores() {
         const container = document.getElementById('wizard-colors-grid');
         const select = document.getElementById('wizard_cor'); 
         if(!container) return;
         
         let items = getOptionList(['cor']);
         const tecidoId = wizardData.tecido ? wizardData.tecido.id : null;
         const tipoTecidoId = wizardData.tipo_tecido ? wizardData.tipo_tecido.id : null;
         const allowedParentIds = [
             ...(selectedPersonalizacoes || []),
             ...(tecidoId ? [tecidoId] : []),
             ...(tipoTecidoId ? [tipoTecidoId] : [])
         ].map(id => id.toString());
         
         if (allowedParentIds.length > 0) {
            items = items.filter(cor => {
                if (!cor.parent_ids || cor.parent_ids.length === 0) return true;
                return cor.parent_ids.some(pid => allowedParentIds.includes(pid.toString()));
            });
         }
         
         // Filter to only Branco (white) if sublimation personalization is selected
         const personalizacaoOptions = getOptionList(['personalizacao']);
         const selectedPersonalizacoesNames = (selectedPersonalizacoes || []).map(id => {
             const p = personalizacaoOptions.find(opt => opt.id.toString() === id.toString());
             return p ? p.name.toUpperCase() : '';
         });
         
         const isSublimacaoSelected = selectedPersonalizacoesNames.some(name => 
             name.includes('SUB') || name.includes('SUBLIMACAO') || name.includes('SUBLIMAÇÃO')
         );
         
         if (isSublimacaoSelected) {
             items = items.filter(cor => 
                 cor.name.toUpperCase() === 'BRANCO' || cor.name.toUpperCase() === 'WHITE'
             );
         }
         
         container.innerHTML = items.map(color => {
            const isActive = wizardData.cor && wizardData.cor.id == color.id;
            const activeClass = isActive ? 'ring-2 ring-[#7c3aed] bg-purple-50 dark:bg-purple-900/20 shadow-sm' : '';
            return `
            <div class="wizard-option-card group cursor-pointer p-3 rounded-xl border border-gray-200 dark:border-slate-700 hover:border-[#7c3aed] dark:hover:border-[#7c3aed] hover:shadow-md transition-all flex flex-col items-center gap-2 ${activeClass}"
                 data-id="${color.id}"
                 onclick="selectWizardColor(this)">
                <div class="w-8 h-8 rounded-full shadow-sm ring-2 ring-gray-100 dark:ring-slate-800" style="background-color: ${color.color_hex || color.hex_code || getColorHex(color.name)}"></div>
                <span class="text-xs font-bold text-center text-gray-700 dark:text-slate-300 group-hover:text-[#7c3aed]">${color.name}</span>
            </div>
            `;
         }).join('');
        
         if(select) {
             select.innerHTML = '<option value="">Selecione uma cor</option>' + 
                items.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
             
             if (wizardData.cor) {
                 select.value = wizardData.cor.id;
             }

             select.onchange = function() {
                 if(this.value) {
                     const mockEl = { dataset: { id: this.value } };
                     selectWizardColor(mockEl);
                 }
             };
         }
    }
    window.loadWizardCores = loadWizardCores;

    function selectWizardColor(element) {
        const id = element.dataset ? element.dataset.id : element; 
        const color = (options.cor || []).find(c => c.id == id);
        if(color) {
            wizardData.cor = { id: color.id, name: color.name };
            const select = document.getElementById('wizard_cor');
            if (select) select.value = color.id;
            wizardNextStep();
        }
    }
    window.selectWizardColor = selectWizardColor;
        
    // --- Step 8: Calculate Total ---
    function calculateWizardTotal() {
        const inputs = document.querySelectorAll('.wizard-size-input');
        let total = 0;
        let especialQty = 0;
        
        inputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            total += qty;
            if (input.dataset.size === 'Especial') especialQty = qty;
        });
        
        const totalPiecesEl = document.getElementById('wizard-total-pieces');
        if (totalPiecesEl) totalPiecesEl.textContent = total;
        
        const summaryPecasVal = document.getElementById('summary-pecas-val');
        if (summaryPecasVal) summaryPecasVal.textContent = total;

        const modelingContainer = document.getElementById('wizard-modeling-container');
        if (modelingContainer) {
            if (especialQty > 0) {
                modelingContainer.classList.remove('hidden');
            } else {
                modelingContainer.classList.add('hidden');
                const modelingCheckbox = document.getElementById('wizard_is_client_modeling');
                if (modelingCheckbox) modelingCheckbox.checked = false;
            }
        }

        return total;
    }
    window.calculateWizardTotal = calculateWizardTotal;

    function previewWizardImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('wizard-image-preview');
                const previewContainer = document.getElementById('wizard-image-preview-container');
                const placeholder = document.getElementById('wizard-image-placeholder');
                
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                
                wizardData.image = input.files[0];
                window.wizardData = wizardData;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    window.previewWizardImage = previewWizardImage;

    function clearWizardImage() {
        const fileInput = document.getElementById('wizard_file_input');
        if (fileInput) fileInput.value = '';
        
        const previewContainer = document.getElementById('wizard-image-preview-container');
        const placeholder = document.getElementById('wizard-image-placeholder');
        
        if (previewContainer) previewContainer.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
        
        wizardData.image = null;
        window.wizardData = wizardData;
        
        const existingImg = document.getElementById('existing_cover_image_hidden');
        if(existingImg) existingImg.value = '';
    }
    window.clearWizardImage = clearWizardImage;

    function previewSublimationImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('sub-image-preview');
                const previewContainer = document.getElementById('sub-image-preview-container');
                const placeholder = document.getElementById('sub-image-placeholder');
                
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    window.previewSublimationImage = previewSublimationImage;

    function clearSublimationImage() {
        const fileInput = document.getElementById('sub_wizard_file_input');
        if (fileInput) fileInput.value = '';
        
        const previewContainer = document.getElementById('sub-image-preview-container');
        const placeholder = document.getElementById('sub-image-placeholder');
        
        if (previewContainer) previewContainer.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
    }
    window.clearSublimationImage = clearSublimationImage;

    function updateFinalSummary() {
        const summaryTecido = document.getElementById('summary-tecido-val');
        if (summaryTecido) summaryTecido.textContent = wizardData.tecido ? wizardData.tecido.name : '-';
        
        const summaryCor = document.getElementById('summary-cor-val');
        if (summaryCor) summaryCor.textContent = wizardData.cor ? wizardData.cor.name : '-';
        
        const summaryModelo = document.getElementById('summary-modelo-val');
        if (summaryModelo) summaryModelo.textContent = wizardData.tipo_corte ? wizardData.tipo_corte.name : '-';
        
        let unitPrice = 0;
        if(wizardData.tipo_corte) unitPrice += parseFloat(wizardData.tipo_corte.price || 0);
        unitPrice += getWizardDetalhes().reduce((sum, detail) => sum + parseFloat(detail.price || 0), 0);
        if(wizardData.gola) unitPrice += parseFloat(wizardData.gola.price || 0);
        
        const finalPriceEl = document.getElementById('wizard-final-price');
        if (finalPriceEl) finalPriceEl.textContent = 'R$ ' + unitPrice.toFixed(2).replace('.', ',');
        
        wizardData.unit_price = unitPrice;
        window.wizardData = wizardData;
    }
    window.updateFinalSummary = updateFinalSummary;
        
    function submitSewingWizard() {
        const tecidoHidden = document.getElementById('tecido_hidden');
        if (tecidoHidden) tecidoHidden.value = wizardData.tecido ? wizardData.tecido.id : '';
        
        const tipoTecidoHidden = document.getElementById('tipo_tecido_hidden');
        if (tipoTecidoHidden) tipoTecidoHidden.value = wizardData.tipo_tecido ? wizardData.tipo_tecido.id : '';
        
        const corHidden = document.getElementById('cor_hidden');
        if (corHidden) corHidden.value = wizardData.cor ? wizardData.cor.id : '';
        
        const tipoCorteHidden = document.getElementById('tipo_corte_hidden');
        if (tipoCorteHidden) tipoCorteHidden.value = wizardData.tipo_corte ? wizardData.tipo_corte.id : '';
        
        const detalheHidden = document.getElementById('detalhe_hidden');
        if (detalheHidden) {
            detalheHidden.value = '';
            detalheHidden.disabled = true;
        }
        
        const detailColorHidden = document.getElementById('detail_color_hidden');
        if (detailColorHidden) detailColorHidden.value = wizardData.detail_color ? wizardData.detail_color.id : '';
        
        const golaHidden = document.getElementById('gola_hidden');
        if (golaHidden) golaHidden.value = wizardData.gola ? wizardData.gola.id : '';
        
        const collarColorHidden = document.getElementById('collar_color_hidden');
        if (collarColorHidden) collarColorHidden.value = wizardData.collar_color ? wizardData.collar_color.id : '';
        
        const hiddenInputs = [
            { id: 'apply_surcharge_hidden', name: 'apply_surcharge', value: document.getElementById('wizard_apply_surcharge')?.checked ? '1' : '0' },
            { id: 'is_client_modeling_hidden', name: 'is_client_modeling', value: document.getElementById('wizard_is_client_modeling')?.checked ? '1' : '0' },
            { id: 'existing_cover_image_hidden', name: 'existing_cover_image', value: (typeof wizardData.image === 'string') ? wizardData.image : '' }
        ];

        hiddenInputs.forEach(meta => {
            let input = document.getElementById(meta.id);
            if(!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.id = meta.id;
                input.name = meta.name;
                const form = document.getElementById('sewing-form');
                if (form) form.appendChild(input);
            }
            input.value = meta.value;
        });

        const sizeContainer = document.getElementById('hidden-sizes-container');
        if (sizeContainer) {
            sizeContainer.innerHTML = '';
            const detailIds = getWizardDetalhes().map(detail => detail.id);
            detailIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'detalhe[]';
                input.value = id;
                sizeContainer.appendChild(input);
            });

            if (wizardData.individual_detail_colors) {
                Object.entries(wizardData.detail_colors || {}).forEach(([detailId, colorId]) => {
                    if (!colorId) return;
                    const colorInput = document.createElement('input');
                    colorInput.type = 'hidden';
                    colorInput.name = `detail_color_map[${detailId}]`;
                    colorInput.value = colorId;
                    sizeContainer.appendChild(colorInput);
                });
            }

            let totalQty = 0;
            for (const [size, qty] of Object.entries(wizardData.sizes)) {
                if(qty > 0) {
                   const input = document.createElement('input');
                   input.type = 'hidden';
                   input.name = `tamanhos[${size}]`;
                   input.value = qty;
                   sizeContainer.appendChild(input);
                   totalQty += parseInt(qty) || 0;
                }
            }
            const qtyInput = document.getElementById('quantity');
            if (qtyInput) qtyInput.value = totalQty;
        }
        
        const notes = document.getElementById('wizard_notes')?.value || '';
        const artNotesInput = document.querySelector('input[name="art_notes"]');
        if(!artNotesInput) {
             const nInput = document.createElement('input');
             nInput.type = 'hidden';
             nInput.name = 'art_notes';
             nInput.value = notes;
             if (sizeContainer) sizeContainer.appendChild(nInput);
        } else {
             artNotesInput.value = notes;
        }
        
        if (isAdmin) {
            const cost = document.getElementById('wizard_unit_cost')?.value || 0;
             const cInput = document.createElement('input');
             cInput.type = 'hidden';
             cInput.name = 'unit_cost';
             cInput.value = cost;
             if (sizeContainer) sizeContainer.appendChild(cInput);
        }

        const unitPriceInput = document.getElementById('unit_price');
        if (unitPriceInput) {
            const finalPrice = wizardData.unit_price || parseFloat((document.getElementById('wizard-final-price')?.textContent || '0').replace(/[R$\s\.]/g,'').replace(',','.')) || 0;
            unitPriceInput.value = finalPrice;
        }

        const formActionInput = document.getElementById('form-action');
        const editingItemIdInput = document.getElementById('editing-item-id');
        if (formActionInput?.value === 'update_item') {
            const currentEditingId = editingItemIdInput?.value?.toString();
            const validEditingItem = itemsData.some(item => item.id?.toString() === currentEditingId);
            
            console.log('VESTALIZE: Validating Edit ID:', currentEditingId, 'against itemsData:', itemsData);
            
            if (!validEditingItem) {
                console.warn('VESTALIZE: Editing ID not found in itemsData. Resetting to add_item.');
                formActionInput.value = 'add_item';
                if (editingItemIdInput) editingItemIdInput.value = '';
            }
        }
        
        const personalizacaoContainer = document.getElementById('hidden-personalizacao-container');
        if (personalizacaoContainer) {
            personalizacaoContainer.innerHTML = '';
            wizardData.personalizacao.forEach(pId => {
                const pInput = document.createElement('input');
                pInput.type = 'hidden';
                pInput.name = 'personalizacao[]';
                pInput.value = pId;
                personalizacaoContainer.appendChild(pInput);
            });
        }
        
        const wizardFile = document.getElementById('wizard_file_input');
        if (wizardFile) wizardFile.name = 'item_cover_image';
        
        const form = document.getElementById('sewing-form');
        if (form) {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    }
    window.submitSewingWizard = submitSewingWizard;

    async function editItem(itemId) {
        const item = itemsData.find(i => i.id == itemId);
        if (!item) {
            alert('Item nÃ£o encontrado.');
            return;
        }

        if (isGroupedFullpageSublimationItem(item)) {
            await populateFullpageSubFormFromItem(item, false);
            return;
        }

        await populateWizardFromItem(itemId, false);
    }
    window.editItem = editItem;

    async function duplicateItem(itemId) {
        const item = itemsData.find(i => i.id == itemId);
        if (!item) {
            alert('Item não encontrado.');
            return;
        }

        if (isGroupedFullpageSublimationItem(item)) {
            await populateFullpageSubFormFromItem(item, true);
            return;
        }

        await populateWizardFromItem(itemId, true);
    }
    window.duplicateItem = duplicateItem;

    async function populateWizardFromItem(itemId, isDuplicate) {
        const item = itemsData.find(i => i.id == itemId);
        if (!item) {
            alert('Item não encontrado.');
            return;
        }

        if (Object.keys(optionsWithParents).length === 0) {
             console.log('Waiting for options to load...');
             await new Promise(resolve => setTimeout(resolve, 800));
             if (Object.keys(optionsWithParents).length === 0) {
                 alert('As opções de produtos ainda estão carregando. Por favor, aguarde um segundo e tente novamente.');
                 return;
             }
        }

        wizardData = {
            tecido: null,
            tipo_tecido: null,
            cor: null,
            tipo_corte: null,
            detalhe: [],
            detail_color: null,
            detail_colors: {},
            individual_detail_colors: false,
            gola: null,
            collar_color: null,
            personalizacao: [],
            image: item.cover_image || null,
            imageUrl: item.cover_image_url || null,
            notes: item.art_notes || '',
            sizes: {},
            unit_cost: item.unit_cost || 0
        };
        window.wizardData = wizardData;

        const editingItemId = document.getElementById('editing-item-id');
        if (editingItemId) editingItemId.value = isDuplicate ? '' : itemId;
        
        const formAction = document.getElementById('form-action');
        if (formAction) formAction.value = isDuplicate ? 'add_item' : 'update_item';
        
        const formTitle = document.getElementById('form-title');
        if (formTitle) formTitle.textContent = isDuplicate ? 'Duplicar Item' : 'Editar Item';

        const submitButton = document.getElementById('wizard-submit-btn');
        if (submitButton) {
            submitButton.innerHTML = isDuplicate ? 'Confirmar e Adicionar Item' : 'Salvar Alterações';
        }
        
        let printDesc = {};
        try {
            printDesc = typeof item.print_desc === 'string' ? JSON.parse(item.print_desc) : item.print_desc;
        } catch(e) { console.error('Erro ao parsear print_desc', e); }

        const wIds = printDesc.wizard_ids || {};
        const fabricPiece = printDesc.fabric_piece || null;
        
        const findOptionByName = (listKey, name) => {
            const list = getOptionList([listKey]);
            if (!name) return null;
            const cleanName = name.split(' - ')[0].trim().toLowerCase();
            return list.find(o => o.name && o.name.toLowerCase().includes(cleanName)) || null;
        };

        if (wIds.tecido) {
            const tissue = getOptionList(['tecido']).find(o => o.id == wIds.tecido);
            if (tissue) wizardData.tecido = { id: tissue.id, name: tissue.name, price: parseFloat(tissue.price || 0) };
        } else {
             const opt = findOptionByName('tecido', item.fabric);
             if(opt) wizardData.tecido = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (wIds.tipo_tecido) {
            const subTissue = getOptionList(['tipo_tecido']).find(o => o.id == wIds.tipo_tecido);
             if (subTissue) wizardData.tipo_tecido = { id: subTissue.id, name: subTissue.name, price: parseFloat(subTissue.price || 0) };
        }

        if (wIds.cor) {
            const color = getOptionList(['cor']).find(o => o.id == wIds.cor);
            if (color) wizardData.cor = { id: color.id, name: color.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.color);
             if(opt) wizardData.cor = { id: opt.id, name: opt.name, price: 0 };
        }

        if (wIds.tipo_corte) {
            const cut = getOptionList(['tipo_corte', 'corte']).find(o => o.id == wIds.tipo_corte);
            if (cut) wizardData.tipo_corte = { id: cut.id, name: cut.name, price: parseFloat(cut.price || 0) };
        } else {
             const opt = findOptionByName('tipo_corte', item.model);
             if(opt) wizardData.tipo_corte = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (Array.isArray(wIds.detalhe)) {
            wizardData.detalhe = wIds.detalhe
                .map(detailId => getOptionList(['detalhe']).find(o => o.id == detailId))
                .filter(Boolean)
                .map(detail => ({ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) }));
        } else if (wIds.detalhe) {
            const detail = getOptionList(['detalhe']).find(o => o.id == wIds.detalhe);
            if (detail) wizardData.detalhe = [{ id: detail.id, name: detail.name, price: parseFloat(detail.price || 0) }];
        } else if (item.detail) {
            wizardData.detalhe = item.detail
                .split(',')
                .map(name => findOptionByName('detalhe', name.trim()))
                .filter(Boolean)
                .map(opt => ({ id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) }));
        }

        if (wIds.detail_color) {
            const dc = getOptionList(['cor']).find(o => o.id == wIds.detail_color);
            if (dc) wizardData.detail_color = { id: dc.id, name: dc.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.detail_color);
             if(opt) wizardData.detail_color = { id: opt.id, name: opt.name, price: 0 };
        }

        if (wIds.detail_color_map && typeof wIds.detail_color_map === 'object') {
            wizardData.detail_colors = Object.fromEntries(
                Object.entries(wIds.detail_color_map).map(([detailId, colorId]) => [detailId.toString(), colorId?.toString()])
            );
        }
        wizardData.individual_detail_colors = !!wIds.individual_detail_colors;

        if (wIds.gola) {
            const collar = getOptionList(['gola']).find(o => o.id == wIds.gola);
            if (collar) wizardData.gola = { id: collar.id, name: collar.name, price: parseFloat(collar.price || 0) };
        } else {
             const opt = findOptionByName('gola', item.collar);
             if(opt) wizardData.gola = { id: opt.id, name: opt.name, price: parseFloat(opt.price || 0) };
        }

        if (wIds.collar_color) {
            const cc = getOptionList(['cor']).find(o => o.id == wIds.collar_color);
            if (cc) wizardData.collar_color = { id: cc.id, name: cc.name, price: 0 };
        } else {
             const opt = findOptionByName('cor', item.collar_color);
             if(opt) wizardData.collar_color = { id: opt.id, name: opt.name, price: 0 };
        }

        if (Array.isArray(wIds.personalizacao)) {
            wizardData.personalizacao = wIds.personalizacao.map(id => id.toString());
        } else {
            if (item.print_type) {
                const names = item.print_type.split(',').map(n => n.trim().toLowerCase());
                const allP = getOptionList(['personalizacao']);
                wizardData.personalizacao = allP
                    .filter(p => p.name && names.includes(p.name.toLowerCase()))
                    .map(p => p.id.toString());
            }
        }

        let itemSizes = {};
        try {
            itemSizes = typeof item.sizes === 'string' ? JSON.parse(item.sizes) : item.sizes;
        } catch(e) {}
        wizardData.sizes = itemSizes || {};

        const diffDetailCb = document.getElementById('different_detail_color_cb');
        if (diffDetailCb) {
            diffDetailCb.checked = !!wizardData.individual_detail_colors || (wizardData.detail_color && wizardData.cor && wizardData.detail_color.id != wizardData.cor.id);
        }
        const individualDetailColorsCb = document.getElementById('individual_detail_colors_cb');
        if (individualDetailColorsCb) {
            individualDetailColorsCb.checked = !!wizardData.individual_detail_colors;
        }
        
        const diffCollarCb = document.getElementById('different_collar_color_cb');
        if (diffCollarCb) {
            diffCollarCb.checked = (wizardData.collar_color && wizardData.cor && wizardData.collar_color.id != wizardData.cor.id);
        }

        const wizardNotes = document.getElementById('wizard_notes');
        if (wizardNotes) wizardNotes.value = wizardData.notes;
        
        const wizardUnitCost = document.getElementById('wizard_unit_cost');
        if (wizardUnitCost) wizardUnitCost.value = wizardData.unit_cost;
        
        const applySurcharge = document.getElementById('wizard_apply_surcharge');
        if (applySurcharge) applySurcharge.checked = !!printDesc.apply_surcharge;
        
        const isClientModeling = document.getElementById('wizard_is_client_modeling');
        if (isClientModeling) isClientModeling.checked = !!printDesc.is_client_modeling;

        const fabricPieceSelect = document.getElementById('fabric_piece_id');
        const fabricPieceQty = document.getElementById('fabric_piece_quantity');
        const fabricPieceUnit = document.getElementById('fabric_piece_unit');
        if (fabricPiece && fabricPieceSelect && fabricPieceQty) {
            fabricPieceSelect.value = fabricPiece.id || '';
            fabricPieceQty.value = fabricPiece.quantity || '';
            if (fabricPieceUnit) fabricPieceUnit.value = fabricPiece.unit || '';
            updateFabricPieceSelection();
        } else {
            resetFabricPieceSelection();
        }

        document.querySelectorAll('.wizard-size-input').forEach(input => {
            const s = input.dataset.size;
            input.value = wizardData.sizes[s] || 0;
        });
        
        calculateWizardTotal();
        
        const previewImg = document.getElementById('wizard-image-preview');
        const previewContainer = document.getElementById('wizard-image-preview-container');
        const placeholder = document.getElementById('wizard-image-placeholder');
        
        if (wizardData.imageUrl) {
            if (previewImg) previewImg.src = wizardData.imageUrl;
            if (previewContainer) previewContainer.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        } else if (wizardData.image && typeof wizardData.image === 'string') {
            if (previewImg) previewImg.src = storageUrl + wizardData.image;
            if (previewContainer) previewContainer.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            clearWizardImage();
        }

        selectedPersonalizacoes = [...wizardData.personalizacao];
        window.selectedPersonalizacoes = selectedPersonalizacoes;

        wizardCurrentStep = 4;
        window.wizardCurrentStep = wizardCurrentStep;
        openSewingWizard();
    }
    window.populateWizardFromItem = populateWizardFromItem;
        
    function previewCoverImage(input) {
        const previewContainer = document.getElementById('cover-image-preview-container');
        const previewImage = document.getElementById('cover-image-preview');
        const fileNameDisplay = document.getElementById('file-name-display');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImage) previewImage.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
            if (fileNameDisplay) {
                fileNameDisplay.textContent = 'Arquivo selecionado: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            }
        }
    }
    window.previewCoverImage = previewCoverImage;

    function cancelEdit() {
        const editingItemId = document.getElementById('editing-item-id');
        const formAction = document.getElementById('form-action');
        const formTitle = document.getElementById('form-title');
        const submitButton = document.getElementById('wizard-submit-btn');
        const sewingForm = document.getElementById('sewing-form');
        const coverPreviewContainer = document.getElementById('cover-image-preview-container');
        const coverPreview = document.getElementById('cover-image-preview');
        const fileNameDisplay = document.getElementById('file-name-display');
        
        if (editingItemId) editingItemId.value = '';
        if (formAction) formAction.value = 'add_item';
        if (formTitle) formTitle.textContent = 'Adicionar Novo Item';
        if (submitButton) submitButton.innerHTML = 'Confirmar e Adicionar Item';
        if (sewingForm) sewingForm.reset();
        
        if (coverPreviewContainer) coverPreviewContainer.classList.add('hidden');
        if (coverPreview) coverPreview.src = '';
        if (fileNameDisplay) {
            fileNameDisplay.classList.add('hidden');
            fileNameDisplay.textContent = '';
        }
        
        document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        wizardData = {
            tecido: null, tipo_tecido: null, cor: null, tipo_corte: null,
            detalhe: [], detail_color: null, detail_colors: {}, individual_detail_colors: false,
            gola: null, collar_color: null, personalizacao: [], image: null, imageUrl: null, notes: '', sizes: {}, unit_cost: 0
        };
        window.wizardData = wizardData;
        selectedPersonalizacoes = [];
        window.selectedPersonalizacoes = selectedPersonalizacoes;
        wizardCurrentStep = 1;
        window.wizardCurrentStep = wizardCurrentStep;
        resetFabricPieceSelection();
        closeSewingWizard();
    }
    window.cancelEdit = cancelEdit;

    async function togglePin(itemId) {
        try {
            const response = await fetch(`/order-items/${itemId}/toggle-pin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();
            if (data.success) {
                // Update sidebar HTML
                const sidebar = document.getElementById('items-sidebar-container');
                if (sidebar && data.html) {
                    sidebar.innerHTML = data.html;
                }
                
                // Update itemsData global
                if (data.items_data) {
                    itemsData = data.items_data;
                    window.itemsData = itemsData;
                }
                
                if (window.showToast) window.showToast(data.message || 'Status alterado!', 'success');
            } else {
                alert('Erro ao alterar status do item: ' + (data.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao processar solicitação');
        }
    }
    window.togglePin = togglePin;

    function openSublimationModal() {
        const modal = document.getElementById('sublimation-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            const form = document.getElementById('sublimation-form');
            if (form) form.reset();
            const totalPecas = document.getElementById('sub-total-pecas');
            if (totalPecas) totalPecas.textContent = '0';
            const totalPrice = document.getElementById('sub-total-price');
            if (totalPrice) totalPrice.textContent = 'R$ 0,00';
            const qtyInput = document.getElementById('sub_quantity');
            if (qtyInput) qtyInput.value = 0;
        }
    }
    window.openSublimationModal = openSublimationModal;
    
    function closeSublimationModal() {
        const modal = document.getElementById('sublimation-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    window.closeSublimationModal = closeSublimationModal;
    
    async function loadSublimationAddons() {
        const typeSlug = document.getElementById('sublimation_type')?.value;
        const container = document.getElementById('sublimation-addons-container');
        if (!typeSlug || !container) {
            if (container) container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
            return;
        }
        
        if (sublimationAddonsCache[typeSlug]) {
            renderSublimationAddons(sublimationAddonsCache[typeSlug].addons);
            return;
        }
        
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Carregando...</p>';
        
        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            const data = await response.json();
            if (data.success) {
                sublimationAddonsCache[typeSlug] = { addons: data.data || [], models: data.models || [], collars: data.collars || [] };
                renderSublimationAddons(sublimationAddonsCache[typeSlug].addons);
                calculateSublimationPrice();
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Nenhum adicional</p>';
            }
        } catch (error) {
            console.error('Erro ao carregar adicionais:', error);
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full">Erro ao carregar</p>';
        }
    }
    window.loadSublimationAddons = loadSublimationAddons;
    
    function renderSublimationAddons(addons) {
        const container = document.getElementById('sublimation-addons-container');
        if (!container) return;
        
        if (!addons || addons.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Nenhum adicional disponível</p>';
            return;
        }
        
        container.innerHTML = addons.map(addon => `
            <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer transition-all border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-purple-300 dark:hover:border-purple-600">
                <input type="checkbox" name="sublimation_addons[]" value="${addon.id}" data-price="${addon.price}" onchange="calculateSublimationPrice()" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">${addon.name}</span>
                ${addon.price != 0 ? `<span class="ml-auto text-xs ${addon.price >= 0 ? 'text-green-600' : 'text-red-600'}">${addon.price >= 0 ? '+' : ''}R$ ${parseFloat(addon.price).toFixed(2).replace('.', ',')}</span>` : ''}
            </label>
        `).join('');
    }
    window.renderSublimationAddons = renderSublimationAddons;
    
    function calculateSublimationTotal() {
        const inputs = document.querySelectorAll('.sub-size-input');
        let total = 0;
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        const totalPecas = document.getElementById('sub-total-pecas');
        if (totalPecas) totalPecas.textContent = total;
        const qtyInput = document.getElementById('sub_quantity');
        if (qtyInput) qtyInput.value = total;
        calculateSublimationPrice();
    }
    window.calculateSublimationTotal = calculateSublimationTotal;
    
    async function calculateSublimationPrice() {
        const typeSlug = document.getElementById('sublimation_type')?.value;
        const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
        
        if (!typeSlug || quantity === 0) {
            updateSublimationPreview();
            return;
        }
        
        try {
            const response = await fetch(`/api/sublimation-total/price/${typeSlug}/${quantity}`);
            const data = await response.json();
            if (data.success) {
                let basePrice = parseFloat(data.price);
                const selectedAddons = document.querySelectorAll('input[name="sublimation_addons[]"]:checked');
                selectedAddons.forEach(addon => {
                    basePrice += parseFloat(addon.dataset.price);
                });
                const unitPriceInput = document.getElementById('sub_unit_price');
                if (unitPriceInput) unitPriceInput.value = basePrice.toFixed(2);
                updateSublimationPreview();
            }
        } catch (error) {
            console.error('Erro ao buscar preço:', error);
        }
    }
    window.calculateSublimationPrice = calculateSublimationPrice;
    
    function updateSublimationPreview() {
        const unitPrice = parseFloat(document.getElementById('sub_unit_price')?.value) || 0;
        const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
        const total = unitPrice * quantity;
        const totalPriceEl = document.getElementById('sub-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    }
    window.updateSublimationPreview = updateSublimationPreview;

    function initSublimationForm() {
        const sublimationForm = document.getElementById('sublimation-form');
        if (sublimationForm) {
            sublimationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const quantity = parseInt(document.getElementById('sub_quantity')?.value) || 0;
                if (quantity === 0) {
                    alert('Adicione pelo menos uma peça nos tamanhos.');
                    return;
                }
                const artName = document.getElementById('sub_art_name')?.value.trim();
                if (!artName) {
                    alert('Informe o nome da arte.');
                    return;
                }
                const btn = document.getElementById('submit-sublimation-btn');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adicionando...';
                try {
                    const formData = new FormData(sublimationForm);
                    const actionUrl = sublimationForm.getAttribute('action');
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        closeSublimationModal();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erro ao adicionar item.');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao processar solicitação.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
    }
    window.initSublimationForm = initSublimationForm;

    // ========================
    // SUBLIMATION WIZARD STEP (Integrated into main wizard)
    // ========================
    
    let subWizardAddons = [];
    let subWizardTecidoId = null;
    let subWizardUnitPrice = 0;
    window.subWizardAddons = subWizardAddons;
    window.subWizardUnitPrice = subWizardUnitPrice;
    
    // Check if any selected personalization is SUB.TOTAL
    function isSublimationTotalSelected() {
        const personalizacaoOptions = getOptionList(['personalizacao']);
        const selectedIds = selectedPersonalizacoes || [];
        
        for (const id of selectedIds) {
            const p = personalizacaoOptions.find(opt => opt.id.toString() === id.toString());
            if (p) {
                if (!p.name) continue;
                const name = p.name.toUpperCase();
                if (name.includes('SUB') && (name.includes('TOTAL') || name.includes('SUBLIM'))) {
                    return true;
                }
            }
        }
        return false;
    }
    window.isSublimationTotalSelected = isSublimationTotalSelected;
    
    // Load addons for selected sublimation type
    async function loadSubWizardAddons() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const container = document.getElementById('sub-wizard-addons');
        if (!container) return;
        
        if (!typeSlug) {
            container.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
            subWizardAddons = [];
            return;
        }
        
        container.innerHTML = '<p class="text-sm text-gray-500 col-span-full"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Carregando...</p>';
        
        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            const data = await response.json();
            
            subWizardTecidoId = data.tecido_id || null;
            
            if (data.success && data.addons && data.addons.length > 0) {
                subWizardAddons = data.addons;
                container.innerHTML = data.addons.map(addon => {
                    const priceText = addon.price > 0 ? `+R$${parseFloat(addon.price).toFixed(2).replace('.', ',')}` : 
                                      addon.price < 0 ? `-R$${Math.abs(parseFloat(addon.price)).toFixed(2).replace('.', ',')}` : '';
                    return `
                    <label class="flex items-center gap-2 p-2 bg-white dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer hover:border-purple-400 transition-colors">
                        <input type="checkbox" name="sub_addons[]" value="${addon.id}" onchange="calculateSubWizardTotal()" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300 flex-1">${addon.name}</span>
                        ${priceText ? `<span class="text-xs font-bold ${addon.price > 0 ? 'text-green-600' : 'text-red-500'}">${priceText}</span>` : ''}
                    </label>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 col-span-full">Nenhum adicional disponível</p>';
                subWizardAddons = [];
            }
        } catch (error) {
            console.error('Error loading addons:', error);
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full">Erro ao carregar adicionais</p>';
        }
        
        calculateSubWizardTotal();
    }
    window.loadSubWizardAddons = loadSubWizardAddons;
    
    // Calculate total for sublimation wizard
    async function calculateSubWizardTotal() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const sizeInputs = document.querySelectorAll('.sub-wizard-size');
        
        let totalQty = 0;
        sizeInputs.forEach(input => {
            totalQty += parseInt(input.value) || 0;
        });
        
        // Update quantity display
        const qtyEl = document.getElementById('sub-wizard-total-qty');
        if (qtyEl) qtyEl.textContent = totalQty;
        
        // Get selected addons price adjustment
        let addonsAdjustment = 0;
        const selectedAddonCheckboxes = document.querySelectorAll('#sub-wizard-addons input[type="checkbox"]:checked');
        selectedAddonCheckboxes.forEach(cb => {
            const addonId = parseInt(cb.value);
            const addon = subWizardAddons.find(a => a.id === addonId);
            if (addon) addonsAdjustment += parseFloat(addon.price) || 0;
        });
        
        // Fetch base price from API
        let baseUnitPrice = 0;
        if (typeSlug && totalQty > 0) {
            try {
                const url = `/api/sublimation-total/price/${typeSlug}/${totalQty}${subWizardTecidoId ? `?tecido_id=${subWizardTecidoId}` : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                if (data.success && data.price) {
                    baseUnitPrice = parseFloat(data.price);
                }
            } catch (error) {
                console.error('Error fetching price:', error);
            }
        }
        
        // Calculate final unit price with addons
        subWizardUnitPrice = baseUnitPrice + addonsAdjustment;
        const totalPrice = subWizardUnitPrice * totalQty;
        
        // Update displays
        const unitPriceEl = document.getElementById('sub-wizard-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = 'R$ ' + subWizardUnitPrice.toFixed(2).replace('.', ',');
        
        const totalPriceEl = document.getElementById('sub-wizard-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ ' + totalPrice.toFixed(2).replace('.', ',');
        
        window.subWizardUnitPrice = subWizardUnitPrice;
    }
    window.calculateSubWizardTotal = calculateSubWizardTotal;
    
    // Submit sublimation item from wizard step
    async function submitSubWizardItem() {
        const typeSlug = document.getElementById('sub_wizard_type')?.value;
        const artName = document.getElementById('sub_wizard_art_name')?.value?.trim();
        
        if (!typeSlug) {
            alert('Selecione o tipo de produto.');
            return false;
        }
        
        if (!artName) {
            alert('Informe o nome da arte.');
            return false;
        }
        
        // Get sizes
        const sizeInputs = document.querySelectorAll('.sub-wizard-size');
        const tamanhos = {};
        let totalQty = 0;
        sizeInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                tamanhos[input.dataset.size] = qty;
                totalQty += qty;
            }
        });
        
        if (totalQty === 0) {
            alert('Adicione pelo menos uma peça.');
            return false;
        }
        
        // Get selected addons
        const selectedAddons = [];
        document.querySelectorAll('#sub-wizard-addons input[type="checkbox"]:checked').forEach(cb => {
            selectedAddons.push(parseInt(cb.value));
        });
        
        // Build form data
        const formData = new FormData();
        formData.append('action', 'add_sublimation_item');
        formData.append('sublimation_type', typeSlug);
        formData.append('art_name', artName);
        formData.append('tamanhos', JSON.stringify(tamanhos));
        formData.append('quantity', totalQty);
        formData.append('unit_price', subWizardUnitPrice);
        formData.append('art_notes', document.getElementById('sub_wizard_notes')?.value || '');
        formData.append('_token', document.querySelector('input[name="_token"]')?.value);
        
        selectedAddons.forEach(id => formData.append('sublimation_addons[]', id));
        
        const coverFile = document.getElementById('sub_wizard_cover')?.files?.[0];
        if (coverFile) formData.append('item_cover_image', coverFile);
        
        const corelFile = document.getElementById('sub_wizard_corel')?.files?.[0];
        if (corelFile) formData.append('corel_file', corelFile);
        
        try {
            const response = await fetch('{{ isset($editData) ? route("orders.edit.sewing") : route("orders.wizard.sewing") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                closeSewingWizard();
                window.location.reload();
                return true;
            } else {
                alert(data.message || 'Erro ao adicionar item.');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao processar solicitação.');
            return false;
        }
    }
    window.submitSubWizardItem = submitSubWizardItem;
    
    // Reset sublimation wizard step
    function resetSubWizardStep() {
        const typeSelect = document.getElementById('sub_wizard_type');
        if (typeSelect) typeSelect.value = '';
        
        const artInput = document.getElementById('sub_wizard_art_name');
        if (artInput) artInput.value = '';
        
        document.querySelectorAll('.sub-wizard-size').forEach(input => input.value = 0);
        
        const notesInput = document.getElementById('sub_wizard_notes');
        if (notesInput) notesInput.value = '';
        
        const addonsContainer = document.getElementById('sub-wizard-addons');
        if (addonsContainer) addonsContainer.innerHTML = '<p class="text-sm text-gray-500 dark:text-slate-400 col-span-full">Selecione um tipo primeiro</p>';
        
        subWizardAddons = [];
        subWizardUnitPrice = 0;
        
        document.getElementById('sub-wizard-total-qty').textContent = '0';
        document.getElementById('sub-wizard-unit-price').textContent = 'R$ 0,00';
        document.getElementById('sub-wizard-total-price').textContent = 'R$ 0,00';
    }
    window.resetSubWizardStep = resetSubWizardStep;

    // ========================
    // FULLPAGE SUBLIMATION FORM (Outside modal, in main content area)
    // ========================
    
    let fullpageSubAddons = [];
    let fullpageSubUnitPrice = 0;
    let fullpageSubBaseUnitPrice = 0;
    let fullpageSubAddonsAdjustment = 0;
    let fullpageSubFabricSurcharge = 0;
    let fullpageSubMatchedFabricAddonId = null;
    let fullpageSubMode = 'add';
    let fullpageSubEditingItemId = '';
    let fullpageSubExistingCoverImage = '';
    let fullpageSubExistingCorelFile = '';
    let fullpageSubUnitCost = 0;
    let fullpageSubTypeMeta = {
        defaultFabricName: '',
        startingPrice: 0,
        startingQuantityFrom: null,
        typeLabel: '',
        models: [],
    };
    let fullpageSubCurrentStep = 1;

    function getFullpageSubmitIdleHtml() {
        if (fullpageSubMode === 'update') {
            return '<i class="fa-solid fa-floppy-disk"></i> Salvar Alteracoes';
        }

        return fullpageSubMode === 'duplicate'
            ? '<i class="fa-solid fa-copy"></i> Duplicar Item'
            : '<i class="fa-solid fa-plus"></i> Adicionar Item';
    }

    function getFullpageSubmitLoadingHtml() {
        if (fullpageSubMode === 'update') {
            return '<i class="fa-solid fa-spinner animate-spin"></i> Salvando...';
        }

        return fullpageSubMode === 'duplicate'
            ? '<i class="fa-solid fa-spinner animate-spin"></i> Duplicando...'
            : '<i class="fa-solid fa-spinner animate-spin"></i> Adicionando...';
    }

    function setFullpageSubmitButtonMode(mode = 'add', editingItemId = '') {
        fullpageSubMode = mode;
        fullpageSubEditingItemId = mode === 'update' ? String(editingItemId || '') : '';

        const submitBtn = document.getElementById('fullpage-step-submit');
        if (submitBtn) {
            submitBtn.innerHTML = getFullpageSubmitIdleHtml();
        }
    }
    
    // Show fullpage sublimation form
    function showSubFullpageForm() {
        const normalTrigger = document.getElementById('normal-wizard-trigger');
        const fullpageForm = document.getElementById('sublimation-fullpage-form');
        const sewingWizardModal = document.getElementById('sewing-wizard-modal');
        
        if (normalTrigger) normalTrigger.classList.add('hidden');
        if (fullpageForm) fullpageForm.classList.remove('hidden');
        if (sewingWizardModal) sewingWizardModal.classList.add('hidden');
        
        isInSublimationMode = true;
        window.isInSublimationMode = true;
        
        document.body.style.overflow = 'auto';
        setFullpageSubStep(1);
        calculateFullpageSubTotal();
    }
    window.showSubFullpageForm = showSubFullpageForm;
    
    // Hide fullpage sublimation form
    function hideSubFullpageForm() {
        const normalTrigger = document.getElementById('normal-wizard-trigger');
        const fullpageForm = document.getElementById('sublimation-fullpage-form');
        
        if (normalTrigger) normalTrigger.classList.remove('hidden');
        if (fullpageForm) fullpageForm.classList.add('hidden');
        
        isInSublimationMode = false;
        window.isInSublimationMode = false;
        
        // Reset form
        resetFullpageSubForm();
    }
    window.hideSubFullpageForm = hideSubFullpageForm;

    function getFullpageSubTotalQty() {
        let totalQty = 0;
        document.querySelectorAll('.fullpage-sub-size').forEach(input => {
            totalQty += parseInt(input.value, 10) || 0;
        });
        return totalQty;
    }

    function validateFullpageSubStep(step) {
        if (step === 1) {
            const typeSlug = document.getElementById('fullpage_sub_type')?.value || '';
            const artName = document.getElementById('fullpage_art_name')?.value?.trim() || '';
            const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
            const modelType = document.getElementById('fullpage_sub_model')?.value || '';
            const fabricCustom = document.getElementById('fullpage_sub_fabric_custom')?.value?.trim() || '';

            if (!typeSlug) {
                alert('Selecione o tipo de produto.');
                return false;
            }
            if (!artName) {
                alert('Informe o nome da arte.');
                return false;
            }
            if (!fabricType) {
                alert('Selecione o tecido.');
                return false;
            }
            if (fabricType === 'OUTRO' && !fabricCustom) {
                alert('Informe o nome do tecido especial.');
                return false;
            }
            if (!modelType) {
                alert('Selecione o modelo.');
                return false;
            }
        }

        if (step === 2) {
            if (getFullpageSubTotalQty() <= 0) {
                alert('Informe ao menos uma peça nos tamanhos.');
                return false;
            }

            const hasAddonColors = !!document.getElementById('fullpage_has_addon_colors')?.checked;
            if (hasAddonColors) {
                const selectedAddons = getSelectedFullpageAddonIds();
                if (!selectedAddons.length) {
                    alert('Marque ao menos um adicional para informar cor.');
                    return false;
                }

                for (const addonId of selectedAddons) {
                    const input = document.querySelector(`#fullpage-addon-color-fields input[data-addon-color-id="${addonId}"]`);
                    if (!input || !input.value.trim()) {
                        alert('Preencha a cor de todos os adicionais selecionados.');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    function setFullpageSubStep(step) {
        const safeStep = Math.min(3, Math.max(1, parseInt(step, 10) || 1));
        fullpageSubCurrentStep = safeStep;

        const titles = {
            1: 'Etapa 1 de 3 · Configuração',
            2: 'Etapa 2 de 3 · Produção',
            3: 'Etapa 3 de 3 · Revisão'
        };
        const subtitle = document.getElementById('fullpage-step-subtitle');
        if (subtitle) subtitle.textContent = titles[safeStep];

        document.querySelectorAll('#sublimation-fullpage-form .fullpage-sub-step').forEach((section, idx) => {
            section.classList.toggle('hidden', (idx + 1) !== safeStep);
        });

        document.querySelectorAll('#fullpage-step-indicator .fullpage-step-chip').forEach(chip => {
            const chipStep = parseInt(chip.dataset.step, 10);

            chip.classList.remove('is-active', 'is-complete', 'is-pending');

            if (chipStep === safeStep) {
                chip.classList.add('is-active');
            } else if (chipStep < safeStep) {
                chip.classList.add('is-complete');
            } else {
                chip.classList.add('is-pending');
            }
        });

        const prevBtn = document.getElementById('fullpage-step-prev');
        const nextBtn = document.getElementById('fullpage-step-next');
        const submitBtn = document.getElementById('fullpage-step-submit');

        if (prevBtn) prevBtn.classList.toggle('hidden', safeStep === 1);
        if (nextBtn) nextBtn.classList.toggle('hidden', safeStep === 3);
        if (submitBtn) submitBtn.classList.toggle('hidden', safeStep !== 3);
    }
    window.setFullpageSubStep = setFullpageSubStep;

    function goToNextFullpageSubStep() {
        if (!validateFullpageSubStep(fullpageSubCurrentStep)) return;
        setFullpageSubStep(fullpageSubCurrentStep + 1);
    }
    window.goToNextFullpageSubStep = goToNextFullpageSubStep;

    function goToPrevFullpageSubStep() {
        setFullpageSubStep(fullpageSubCurrentStep - 1);
    }
    window.goToPrevFullpageSubStep = goToPrevFullpageSubStep;
    // Legacy SUB.TOTAL block replaced by overrides below.
    let isSubmittingFullpage = false;

    // --- SUB.TOTAL fullpage overrides ---
    function normalizeFullpageLookupValue(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .toUpperCase();
    }

    function getFullpageAvailableModelOptions() {
        const models = Array.isArray(fullpageSubTypeMeta?.models)
            ? fullpageSubTypeMeta.models
            : [];

        const normalizedModels = Array.from(new Set(models
            .map(model => String(model || '').trim().toUpperCase())
            .filter(Boolean)));

        return normalizedModels.length > 0
            ? normalizedModels
            : ['BASICA', 'BABYLOOK', 'INFANTIL'];
    }

    function normalizeFullpageModelValue(value) {
        return normalizeFullpageLookupValue(value).replace(/[^A-Z0-9]/g, '');
    }

    function parseFullpageItemPrintDesc(item) {
        if (!item) return {};
        if (item.print_desc && typeof item.print_desc === 'object') {
            return item.print_desc;
        }
        if (typeof item.print_desc === 'string' && item.print_desc.trim() !== '') {
            try {
                const parsed = JSON.parse(item.print_desc);
                return parsed && typeof parsed === 'object' ? parsed : {};
            } catch (error) {
                console.error('Erro ao parsear print_desc do item SUB. TOTAL', error);
            }
        }
        return {};
    }

    function parseFullpageItemSizes(item) {
        if (!item) return {};
        if (item.sizes && typeof item.sizes === 'object') {
            return item.sizes;
        }
        if (typeof item.sizes === 'string' && item.sizes.trim() !== '') {
            try {
                const parsed = JSON.parse(item.sizes);
                return parsed && typeof parsed === 'object' ? parsed : {};
            } catch (error) {
                console.error('Erro ao parsear tamanhos do item SUB. TOTAL', error);
            }
        }
        return {};
    }

    function parseFullpageItemAddonIds(item, printDesc = parseFullpageItemPrintDesc(item)) {
        let rawIds = [];

        if (Array.isArray(item?.sublimation_addons)) {
            rawIds = item.sublimation_addons;
        } else if (typeof item?.sublimation_addons === 'string' && item.sublimation_addons.trim() !== '') {
            try {
                const parsed = JSON.parse(item.sublimation_addons);
                rawIds = Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                console.error('Erro ao parsear adicionais do item SUB. TOTAL', error);
            }
        } else if (Array.isArray(printDesc.addons)) {
            rawIds = printDesc.addons;
        }

        return rawIds
            .map(id => parseInt(id, 10))
            .filter(id => Number.isFinite(id) && id > 0);
    }

    function resolveFullpageFabricType(item, printDesc = parseFullpageItemPrintDesc(item)) {
        const fabricType = String(printDesc.fabric_type || '').toUpperCase();
        if (['PADRAO', 'PP', 'CACHARREL', 'OUTRO'].includes(fabricType)) {
            return fabricType;
        }

        const fabricName = String(item?.fabric || '').toUpperCase();
        if (fabricName === 'PP') return 'PP';
        if (fabricName === 'CACHARREL') return 'CACHARREL';
        return fabricName ? 'OUTRO' : '';
    }

    function resolveFullpageModelType(item, printDesc = parseFullpageItemPrintDesc(item)) {
        const availableModels = getFullpageAvailableModelOptions();
        const normalizedModels = availableModels.map(model => ({
            value: model,
            key: normalizeFullpageModelValue(model),
        }));

        const modelType = String(printDesc.model_type || '').trim();
        if (modelType) {
            const normalizedType = normalizeFullpageModelValue(modelType);
            const matchedType = normalizedModels.find(model => model.key === normalizedType);
            if (matchedType) return matchedType.value;
        }

        const modelName = String(item?.model || '').trim();
        if (modelName) {
            const modelTail = modelName.includes(' - ') ? modelName.split(' - ').pop() : modelName;
            const normalizedName = normalizeFullpageModelValue(modelTail);
            const matchedName = normalizedModels.find(model => model.key === normalizedName);
            if (matchedName) return matchedName.value;
        }

        return '';
    }

    function getFullpageBaseName(path) {
        return String(path || '').split('/').pop() || '';
    }

    function isGroupedFullpageSublimationItem(item) {
        if (!item) return false;
        if (item.is_sublimation_total === true || item.is_sublimation_total === 1 || item.is_sublimation_total === '1') {
            return true;
        }
        const printDesc = parseFullpageItemPrintDesc(item);
        return !!printDesc.is_sublimation_total;
    }

    function resolveFullpageSublimationType(item) {
        if (!item) return '';
        if (item.sublimation_type) {
            return String(item.sublimation_type);
        }
        const printDesc = parseFullpageItemPrintDesc(item);
        return typeof printDesc.type === 'string' ? printDesc.type : '';
    }

    function getFullpageGroupedExistingQty(typeSlug, excludeItemId = null) {
        if (!typeSlug) return 0;

        const currentItems = Array.isArray(itemsData)
            ? itemsData
            : (Array.isArray(window.itemsData) ? window.itemsData : []);

        return currentItems.reduce((sum, item) => {
            if (!isGroupedFullpageSublimationItem(item)) {
                return sum;
            }

            if (resolveFullpageSublimationType(item) !== typeSlug) {
                return sum;
            }

            if (excludeItemId && String(item.id) === String(excludeItemId)) {
                return sum;
            }

            return sum + (parseInt(item.quantity, 10) || 0);
        }, 0);
    }

    function setFullpageUploadActiveState(input, isActive) {
        const uploadCard = input?.closest('.fullpage-sub-upload');
        if (!uploadCard) return;

        uploadCard.classList.toggle('border-[#7c3aed]', isActive);
        uploadCard.classList.toggle('bg-[#7c3aed]/5', isActive);
    }

    function resetFullpageCorelPreview() {
        fullpageSubExistingCorelFile = '';

        const fileName = document.getElementById('fullpage-corel-file-name');
        const placeholder = document.getElementById('fullpage-corel-placeholder');
        const input = document.getElementById('fullpage_corel_file');

        if (fileName) {
            fileName.textContent = '';
            fileName.classList.add('hidden');
        }
        if (placeholder) {
            placeholder.classList.remove('hidden');
        }

        setFullpageUploadActiveState(input, false);
    }

    function previewFullpageCorelFile(input) {
        const file = input?.files?.[0];
        const fileName = document.getElementById('fullpage-corel-file-name');
        const placeholder = document.getElementById('fullpage-corel-placeholder');

        if (!file) {
            resetFullpageCorelPreview();
            return;
        }

        fullpageSubExistingCorelFile = '';

        if (fileName) {
            fileName.textContent = file.name;
            fileName.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }

        setFullpageUploadActiveState(input, true);
    }
    window.previewFullpageCorelFile = previewFullpageCorelFile;

    function resetFullpageCoverPreview() {
        fullpageSubExistingCoverImage = '';

        const placeholder = document.getElementById('fullpage-cover-placeholder');
        const previewContainer = document.getElementById('fullpage-cover-preview-container');
        const previewImage = document.getElementById('fullpage-cover-preview');
        const fileName = document.getElementById('fullpage-cover-file-name');
        const input = document.getElementById('fullpage_cover_image');

        if (placeholder) {
            placeholder.classList.remove('hidden');
        }
        if (previewContainer) {
            previewContainer.classList.add('hidden');
        }
        if (previewImage) {
            previewImage.src = '';
        }
        if (fileName) {
            fileName.textContent = '';
        }

        setFullpageUploadActiveState(input, false);
    }

    function previewFullpageCoverImage(input) {
        const file = input?.files?.[0];
        const placeholder = document.getElementById('fullpage-cover-placeholder');
        const previewContainer = document.getElementById('fullpage-cover-preview-container');
        const previewImage = document.getElementById('fullpage-cover-preview');
        const fileName = document.getElementById('fullpage-cover-file-name');

        if (!file) {
            resetFullpageCoverPreview();
            return;
        }

        fullpageSubExistingCoverImage = '';

        const reader = new FileReader();
        reader.onload = function(event) {
            if (previewImage) {
                previewImage.src = event.target?.result || '';
            }
            if (previewContainer) {
                previewContainer.classList.remove('hidden');
            }
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
            if (fileName) {
                fileName.textContent = file.name;
            }

            setFullpageUploadActiveState(input, true);
        };
        reader.readAsDataURL(file);
    }
    window.previewFullpageCoverImage = previewFullpageCoverImage;

    function setExistingFullpageCorelFile(filePath) {
        fullpageSubExistingCorelFile = String(filePath || '').trim();

        const fileName = document.getElementById('fullpage-corel-file-name');
        const placeholder = document.getElementById('fullpage-corel-placeholder');
        const input = document.getElementById('fullpage_corel_file');
        if (input) input.value = '';

        const hiddenInput = document.getElementById('fullpage_existing_corel_file');
        if (hiddenInput) hiddenInput.value = fullpageSubExistingCorelFile;

        if (!fullpageSubExistingCorelFile) {
            resetFullpageCorelPreview();
            return;
        }

        if (fileName) {
            fileName.textContent = getFullpageBaseName(fullpageSubExistingCorelFile);
            fileName.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }

        setFullpageUploadActiveState(input, true);
    }

    function setExistingFullpageCoverImage(filePath, imageUrl = '') {
        fullpageSubExistingCoverImage = String(filePath || '').trim();

        const hiddenInput = document.getElementById('fullpage_existing_cover_image');
        if (hiddenInput) hiddenInput.value = fullpageSubExistingCoverImage;

        const placeholder = document.getElementById('fullpage-cover-placeholder');
        const previewContainer = document.getElementById('fullpage-cover-preview-container');
        const previewImage = document.getElementById('fullpage-cover-preview');
        const fileName = document.getElementById('fullpage-cover-file-name');
        const input = document.getElementById('fullpage_cover_image');
        if (input) input.value = '';

        if (!fullpageSubExistingCoverImage) {
            resetFullpageCoverPreview();
            return;
        }

        const normalizedPath = fullpageSubExistingCoverImage.replace(/^\/+/, '');
        const resolvedUrl = imageUrl || (normalizedPath ? `${storageUrl}${normalizedPath}` : '');

        if (previewImage) {
            previewImage.src = resolvedUrl;
        }
        if (previewContainer) {
            previewContainer.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
        if (fileName) {
            fileName.textContent = getFullpageBaseName(fullpageSubExistingCoverImage);
        }

        setFullpageUploadActiveState(input, true);
    }

    // List of fabrics available from backend
    const fullpageSubFabrics = @json($tecidos ?? []);

    function populateFullpageFabricOptions() {
        const fabricSelect = document.getElementById('fullpage_sub_fabric_type');
        if (!fabricSelect) return;

        const currentValue = fabricSelect.value || '';
        const defaultFabricName = fullpageSubTypeMeta.defaultFabricName || 'Tecido padrão';
        const defaultFabricId = fullpageSubTypeMeta.tecido_id || '';

        let html = `<option value="">Selecione</option>`;
        
        // Option for default fabric only if a valid defaultFabricId exists
        if (defaultFabricId) {
            html += `<option value="PADRAO" data-tecido-id="${defaultFabricId}">${defaultFabricName} (Correto)</option>`;
        }
        
        // Dynamic options from $tecidos
        fullpageSubFabrics.forEach(f => {
            // Avoid duplicating the default fabric if it's already there
            if (!defaultFabricId || f.id != defaultFabricId) {
                html += `<option value="${f.id}" data-tecido-id="${f.id}">${f.name}</option>`;
            }
        });

        html += `<option value="OUTRO">OUTRO TECIDO (Acréscimo)</option>`;

        fabricSelect.innerHTML = html;

        // Try to restore previous value or default to PADRAO if available
        if (currentValue) {
            fabricSelect.value = currentValue;
        } else if (defaultFabricId) {
            fabricSelect.value = 'PADRAO';
        }
    }

    async function populateFullpageSubFormFromItem(item, isDuplicate = true) {
        if (!item || !isGroupedFullpageSublimationItem(item)) {
            return false;
        }

        const printDesc = parseFullpageItemPrintDesc(item);
        const typeSlug = resolveFullpageSublimationType(item);
        if (!typeSlug) {
            alert('Tipo de SUB. TOTAL não encontrado para este item.');
            return false;
        }

        resetFullpageSubForm();
        showSubFullpageForm();
        setFullpageSubmitButtonMode(isDuplicate ? 'duplicate' : 'update', isDuplicate ? '' : item.id);

        fullpageSubUnitCost = parseFloat(item.unit_cost || 0) || 0;

        const typeSelect = document.getElementById('fullpage_sub_type');
        if (typeSelect) {
            typeSelect.value = typeSlug;
        }
        await loadFullpageSubAddons();

        const artInput = document.getElementById('fullpage_art_name');
        if (artInput) {
            artInput.value = item.art_name || '';
        }

        const fabricType = resolveFullpageFabricType(item, printDesc);
        const fabricTypeSelect = document.getElementById('fullpage_sub_fabric_type');
        if (fabricTypeSelect) {
            fabricTypeSelect.value = fabricType || '';
        }

        const fabricCustomInput = document.getElementById('fullpage_sub_fabric_custom');
        if (fabricCustomInput) {
            fabricCustomInput.value = fabricType === 'OUTRO'
                ? (printDesc.fabric_custom || item.fabric || '')
                : '';
        }

        const fabricColorInput = document.getElementById('fullpage_sub_fabric_color');
        if (fabricColorInput) {
            fabricColorInput.value = printDesc.fabric_color || item.color || 'BRANCO';
        }

        const baseCollarInput = document.getElementById('fullpage_sub_base_collar');
        if (baseCollarInput) {
            baseCollarInput.value = printDesc.base_collar || String(item.collar || 'REDONDA').split('+')[0].trim() || 'REDONDA';
        }

        const modelSelect = document.getElementById('fullpage_sub_model');
        if (modelSelect) {
            modelSelect.value = resolveFullpageModelType(item, printDesc);
        }

        handleFullpageFabricTypeChange();

        const selectedAddonIds = parseFullpageItemAddonIds(item, printDesc);
        document.querySelectorAll('#fullpage-sub-addons input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = selectedAddonIds.includes(parseInt(checkbox.value, 10));
        });

        const addonColorMap = printDesc.addon_color_map && typeof printDesc.addon_color_map === 'object'
            ? printDesc.addon_color_map
            : {};
        const hasAddonColors = !!printDesc.has_addon_colors || Object.keys(addonColorMap).length > 0;
        const addonColorsCheckbox = document.getElementById('fullpage_has_addon_colors');
        if (addonColorsCheckbox) {
            addonColorsCheckbox.checked = hasAddonColors;
        }

        renderFullpageAddonColorFields();
        Object.entries(addonColorMap).forEach(([addonId, color]) => {
            const input = document.querySelector(`#fullpage-addon-color-fields input[data-addon-color-id="${addonId}"]`);
            if (input) {
                input.value = color || '';
            }
        });

        const itemSizes = parseFullpageItemSizes(item);
        document.querySelectorAll('.fullpage-sub-size').forEach(input => {
            const sizeKey = input.dataset.size;
            input.value = parseInt(itemSizes[sizeKey], 10) || 0;
        });

        const notesInput = document.getElementById('fullpage_notes');
        if (notesInput) {
            notesInput.value = item.art_notes || '';
        }

        setExistingFullpageCoverImage(item.cover_image || '', item.cover_image_url || '');
        setExistingFullpageCorelFile(item.corel_file_path || printDesc.corel_file || '');

        await calculateFullpageSubTotal();
        setFullpageSubStep(3);

        return true;
    }
    window.populateFullpageSubFormFromItem = populateFullpageSubFormFromItem;

    function getMatchedFullpageFabricAddon() {
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        const fabricName = document.getElementById('fullpage_sub_fabric_custom')?.value || '';

        if (fabricType !== 'OUTRO' || !fabricName.trim()) {
            return null;
        }

        const normalizedFabricName = normalizeFullpageLookupValue(fabricName);
        return fullpageSubAddons.find(addon => {
            const normalizedAddonName = normalizeFullpageLookupValue(addon.name);
            return normalizedAddonName === normalizedFabricName
                || normalizedAddonName.includes(normalizedFabricName)
                || normalizedFabricName.includes(normalizedAddonName);
        }) || null;
    }

    function syncFullpageSpecialFabricPricing() {
        const surchargeInput = document.getElementById('fullpage_sub_fabric_surcharge');
        const hint = document.getElementById('fullpage-special-fabric-hint');
        if (!surchargeInput) return;

        const matchedAddon = getMatchedFullpageFabricAddon();
        fullpageSubMatchedFabricAddonId = matchedAddon ? parseInt(matchedAddon.id, 10) : null;

        if (matchedAddon) {
            surchargeInput.value = Number(parseFloat(matchedAddon.price) || 0).toFixed(2);
            if (hint) {
                hint.textContent = `Valor configurado em SUB. TOTAL > ${fullpageSubTypeMeta.typeLabel || 'Tipo'} para "${matchedAddon.name}".`;
            }
            return;
        }

        surchargeInput.value = '0.00';
        if (hint) {
            const hasFabricName = !!document.getElementById('fullpage_sub_fabric_custom')?.value?.trim();
            hint.textContent = hasFabricName
                ? 'Nenhum acréscimo configurado para este tecido no painel de preços.'
                : '';
        }
    }
    window.syncFullpageSpecialFabricPricing = syncFullpageSpecialFabricPricing;

    function handleFullpageFabricTypeChange() {
        toggleFullpageSpecialFabric();
        calculateFullpageSubTotal();
    }
    window.handleFullpageFabricTypeChange = handleFullpageFabricTypeChange;

    function toggleFullpageSpecialFabric() {
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        const wrapper = document.getElementById('fullpage-special-fabric-fields');
        const customInput = document.getElementById('fullpage_sub_fabric_custom');
        const surchargeInput = document.getElementById('fullpage_sub_fabric_surcharge');
        if (!wrapper || !customInput || !surchargeInput) return;

        if (fabricType === 'OUTRO') {
            wrapper.classList.remove('hidden');
            customInput.required = true;
            syncFullpageSpecialFabricPricing();
        } else {
            wrapper.classList.add('hidden');
            customInput.required = false;
            customInput.value = '';
            surchargeInput.value = '0.00';
            fullpageSubMatchedFabricAddonId = null;
            const hint = document.getElementById('fullpage-special-fabric-hint');
            if (hint) hint.textContent = '';
        }
    }
    window.toggleFullpageSpecialFabric = toggleFullpageSpecialFabric;

    function getSelectedFullpageAddonIds() {
        const ids = [];
        document.querySelectorAll('#fullpage-sub-addons input[type="checkbox"]:checked').forEach(cb => {
            ids.push(parseInt(cb.value, 10));
        });
        return ids;
    }

    function getFullpageFabricSurcharge() {
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        if (fabricType !== 'OUTRO') return 0;
        syncFullpageSpecialFabricPricing();
        const inputValue = parseFloat(document.getElementById('fullpage_sub_fabric_surcharge')?.value || '0');
        return Number.isFinite(inputValue) ? Math.max(0, inputValue) : 0;
    }

    function renderFullpageAddonColorFields() {
        const wrapper = document.getElementById('fullpage-addon-color-fields');
        const hasColors = document.getElementById('fullpage_has_addon_colors')?.checked;
        if (!wrapper) return;

        if (!hasColors) {
            wrapper.classList.add('hidden');
            wrapper.innerHTML = '';
            return;
        }

        const currentValues = {};
        wrapper.querySelectorAll('input[data-addon-color-id]').forEach(input => {
            currentValues[input.dataset.addonColorId] = input.value;
        });

        const selectedIds = getSelectedFullpageAddonIds();
        if (!selectedIds.length) {
            wrapper.classList.remove('hidden');
            wrapper.innerHTML = '<p class="text-xs text-gray-500 dark:text-slate-400">Selecione pelo menos um adicional para informar a cor.</p>';
            return;
        }

        wrapper.innerHTML = '';
        selectedIds.forEach(addonId => {
            const addon = fullpageSubAddons.find(a => parseInt(a.id, 10) === addonId);
            if (!addon) return;

            const row = document.createElement('div');

            const label = document.createElement('label');
            label.className = 'block text-xs font-medium text-gray-600 dark:text-slate-300 mb-1';
            label.textContent = addon.name;

            const input = document.createElement('input');
            input.type = 'text';
            input.dataset.addonColorId = addon.id;
            input.placeholder = `Cor para ${addon.name}`;
            input.className = 'w-full px-2.5 py-2 rounded-md border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-1 focus:ring-purple-500';
            input.value = currentValues[String(addon.id)] || '';

            row.appendChild(label);
            row.appendChild(input);
            wrapper.appendChild(row);
        });

        wrapper.classList.remove('hidden');
    }
    window.renderFullpageAddonColorFields = renderFullpageAddonColorFields;

    async function loadFullpageSubAddons() {
        const typeSlug = document.getElementById('fullpage_sub_type')?.value;
        const container = document.getElementById('fullpage-sub-addons');
        if (!container) return;

        if (!typeSlug) {
            container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Selecione um tipo primeiro</p>';
            fullpageSubAddons = [];
            fullpageSubTypeMeta = {
                defaultFabricName: '',
                startingPrice: 0,
                startingQuantityFrom: null,
                typeLabel: '',
                models: [],
                collars: [],
            };
            populateFullpageFabricOptions();
            renderFullpageAddonColorFields();
            calculateFullpageSubTotal();
            return;
        }

        container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Carregando...</p>';

        let payload = null;
        try {
            const response = await fetch(`/api/sublimation-total/addons/${typeSlug}`);
            payload = await response.json();
            const addons = Array.isArray(payload?.addons) ? payload.addons : (Array.isArray(payload?.data) ? payload.data : []);
            const models = Array.isArray(payload?.models)
                ? Array.from(new Set(payload.models
                    .map(model => String(model || '').trim().toUpperCase())
                    .filter(Boolean)))
                : [];
            fullpageSubTypeMeta = {
                defaultFabricName: payload?.default_fabric_name || '',
                tecido_id: payload?.tecido_id ?? null,
                startingPrice: parseFloat(payload?.starting_price || 0) || 0,
                startingQuantityFrom: payload?.starting_quantity_from ?? null,
                typeLabel: payload?.type_name || typeSlug,
                models,
                collars: Array.isArray(payload?.collars)
                    ? Array.from(new Set(payload.collars
                        .map(collar => String(collar || '').trim().toUpperCase())
                        .filter(Boolean)))
                    : [],
            };
            populateFullpageFabricOptions();

            if (payload?.success && addons.length > 0) {
                fullpageSubAddons = addons;
                container.innerHTML = addons.map(addon => {
                    const addonPrice = parseFloat(addon.price) || 0;
                    const priceText = addonPrice > 0
                        ? `+R$${addonPrice.toFixed(2).replace('.', ',')}`
                        : addonPrice < 0
                            ? `-R$${Math.abs(addonPrice).toFixed(2).replace('.', ',')}`
                            : '';
                    return `
                    <label class="fullpage-sub-addon-item flex items-center gap-2 p-2.5 cursor-pointer">
                        <input type="checkbox" name="fullpage_addons[]" value="${addon.id}" onchange="calculateFullpageSubTotal(); renderFullpageAddonColorFields();" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300 flex-1">${addon.name}</span>
                        ${priceText ? `<span class="text-xs font-bold ${addonPrice > 0 ? 'text-green-600' : 'text-red-500'}">${priceText}</span>` : ''}
                    </label>
                    `;
                }).join('');
            } else {
                fullpageSubAddons = [];
                container.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Nenhum adicional disponivel</p>';
            }
        } catch (error) {
            console.error('Error loading addons:', error);
            fullpageSubAddons = [];
            fullpageSubTypeMeta = {
                defaultFabricName: '',
                startingPrice: 0,
                startingQuantityFrom: null,
                typeLabel: typeSlug,
                models: [],
                collars: [],
            };
            populateFullpageFabricOptions();
            container.innerHTML = '<p class="text-sm text-red-500 col-span-full text-center py-4">Erro ao carregar adicionais</p>';
        }

        toggleFullpageSpecialFabric();
        syncFullpageSpecialFabricPricing();
        renderFullpageAddonColorFields();
        calculateFullpageSubTotal();

        // ── Populate model and collar dropdowns from API response ──
        const modelSelect = document.getElementById('fullpage_sub_model');
        const collarSelect = document.getElementById('fullpage_sub_base_collar');

        if (modelSelect) {
            const currentVal = modelSelect.value;
            const models = fullpageSubTypeMeta.models || [];

            modelSelect.innerHTML = '<option value="">Selecione</option>';
            models.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = m;
                if (m === currentVal) opt.selected = true;
                modelSelect.appendChild(opt);
            });
        }

        if (collarSelect) {
            const currentVal = collarSelect.value;
            const collars = fullpageSubTypeMeta.collars || [];

            collarSelect.innerHTML = '<option value="">Selecione</option>';
            collars.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (c === currentVal) opt.selected = true;
                collarSelect.appendChild(opt);
            });
            
            // Fallback for default if empty
            if (collarSelect.options.length <= 1) {
                const opt = document.createElement('option');
                opt.value = 'REDONDA';
                opt.textContent = 'REDONDA';
                if (currentVal === 'REDONDA') opt.selected = true;
                collarSelect.appendChild(opt);
            }
        }
    }
    window.loadFullpageSubAddons = loadFullpageSubAddons;

    async function calculateFullpageSubTotal() {
        const typeSlug = document.getElementById('fullpage_sub_type')?.value;
        const sizeInputs = document.querySelectorAll('.fullpage-sub-size');
        let totalQty = 0;

        sizeInputs.forEach(input => {
            totalQty += parseInt(input.value, 10) || 0;
        });

        const qtyEl = document.getElementById('fullpage-total-qty');
        if (qtyEl) qtyEl.textContent = totalQty;
        const qtyReviewEl = document.getElementById('fullpage-total-qty-review');
        if (qtyReviewEl) qtyReviewEl.textContent = totalQty;

        const groupedExistingQty = getFullpageGroupedExistingQty(
            typeSlug,
            fullpageSubMode === 'update' ? fullpageSubEditingItemId : null
        );
        const pricingQty = totalQty + groupedExistingQty;

        fullpageSubFabricSurcharge = getFullpageFabricSurcharge();

        const selectedAddonIds = getSelectedFullpageAddonIds();
        fullpageSubAddonsAdjustment = selectedAddonIds.reduce((sum, addonId) => {
            if (fullpageSubMatchedFabricAddonId && parseInt(addonId, 10) === fullpageSubMatchedFabricAddonId) {
                return sum;
            }
            const addon = fullpageSubAddons.find(a => parseInt(a.id, 10) === addonId);
            return sum + (addon ? (parseFloat(addon.price) || 0) : 0);
        }, 0);
        fullpageSubBaseUnitPrice = fullpageSubTypeMeta.startingPrice || 0;

        if (typeSlug && pricingQty > 0) {
            try {
                const fabricSelect = document.getElementById('fullpage_sub_fabric_type');
                const selectedOption = fabricSelect?.options[fabricSelect?.selectedIndex];
                const tecidoId = selectedOption?.dataset?.tecidoId || fullpageSubTypeMeta.tecido_id || null;
                const url = `/api/sublimation-total/price/${typeSlug}/${pricingQty}${tecidoId ? `?tecido_id=${tecidoId}` : ''}`;
                const response = await fetch(url);
                const payload = await response.json();
                if (payload?.success) {
                    fullpageSubBaseUnitPrice = parseFloat(payload.price) || fullpageSubBaseUnitPrice;
                }
            } catch (error) {
                console.error('Error fetching price:', error);
            }
        }

        fullpageSubUnitPrice = fullpageSubBaseUnitPrice + fullpageSubAddonsAdjustment + fullpageSubFabricSurcharge;
        if (fullpageSubUnitPrice < 0) fullpageSubUnitPrice = 0;

        // Calcular acréscimos por tamanho (igual ao backend calculateItemTotalPrice)
        let totalSizeSurcharge = 0;
        sizeInputs.forEach(input => {
            const qty = parseInt(input.value, 10) || 0;
            const size = input.dataset.size;
            if (qty > 0 && size) {
                totalSizeSurcharge += getFullpageSizeSurcharge(size, fullpageSubUnitPrice) * qty;
            }
        });

        const totalPrice = fullpageSubUnitPrice * totalQty + totalSizeSurcharge;
        const formatMoney = value => `R$ ${Number(value || 0).toFixed(2).replace('.', ',')}`;
        const addonSign = fullpageSubAddonsAdjustment < 0 ? '-' : '+';
        const fabricSign = fullpageSubFabricSurcharge < 0 ? '-' : '+';

        const unitPriceEl = document.getElementById('fullpage-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = formatMoney(fullpageSubUnitPrice);
        const unitPriceReviewEl = document.getElementById('fullpage-unit-price-review');
        if (unitPriceReviewEl) unitPriceReviewEl.textContent = formatMoney(fullpageSubUnitPrice);

        const totalPriceEl = document.getElementById('fullpage-total-price');
        if (totalPriceEl) totalPriceEl.textContent = formatMoney(totalPrice);
        const totalPriceReviewEl = document.getElementById('fullpage-total-price-review');
        if (totalPriceReviewEl) totalPriceReviewEl.textContent = formatMoney(totalPrice);

        const breakdownEl = document.getElementById('fullpage-price-breakdown');
        if (breakdownEl) {
            let breakdownText = `Base ${formatMoney(fullpageSubBaseUnitPrice)} ${addonSign} Adicionais ${formatMoney(Math.abs(fullpageSubAddonsAdjustment))} ${fabricSign} Tecido ${formatMoney(Math.abs(fullpageSubFabricSurcharge))}`;
            if (totalSizeSurcharge > 0) {
                breakdownText += ` + Tamanhos ${formatMoney(totalSizeSurcharge)}`;
            }
            if (totalQty > 0) {
                breakdownText += ` · Unitário calculado para ${totalQty} peça(s)`;
            }
            breakdownEl.textContent = breakdownText;
            const breakdownReviewEl = document.getElementById('fullpage-price-breakdown-review');
            if (breakdownReviewEl) breakdownReviewEl.textContent = breakdownText;
        }
    }
    window.calculateFullpageSubTotal = calculateFullpageSubTotal;

    async function submitFullpageSubItem() {
        if (isSubmittingFullpage) return false;

        const submitBtn = document.querySelector('#sublimation-fullpage-form button[onclick*="submitFullpageSubItem"]');
        const setSubmittingState = (loading) => {
            isSubmittingFullpage = loading;
            if (!submitBtn) return;
            submitBtn.disabled = loading;
            submitBtn.innerHTML = loading ? getFullpageSubmitLoadingHtml() : getFullpageSubmitIdleHtml();
        };

        const isUpdate = fullpageSubMode === 'update';
        const wasDuplicate = fullpageSubMode === 'duplicate';
        const typeSlug = document.getElementById('fullpage_sub_type')?.value || '';
        const artName = document.getElementById('fullpage_art_name')?.value?.trim() || '';
        const fabricType = document.getElementById('fullpage_sub_fabric_type')?.value || '';
        const modelType = document.getElementById('fullpage_sub_model')?.value || '';
        const fabricCustom = document.getElementById('fullpage_sub_fabric_custom')?.value?.trim() || '';
        const fabricColor = document.getElementById('fullpage_sub_fabric_color')?.value || 'BRANCO';
        const baseCollar = document.getElementById('fullpage_sub_base_collar')?.value || 'REDONDA';
        const hasAddonColors = !!document.getElementById('fullpage_has_addon_colors')?.checked;

        if (!typeSlug) {
            alert('Selecione o tipo de produto.');
            return false;
        }
        if (!artName) {
            alert('Informe o nome da arte.');
            return false;
        }
        if (!fabricType) {
            alert('Selecione o tecido.');
            return false;
        }
        if (fabricType === 'OUTRO' && !fabricCustom) {
            alert('Informe o nome do tecido especial.');
            return false;
        }
        if (!modelType) {
            alert('Selecione o modelo.');
            return false;
        }

        setSubmittingState(true);

        const sizeInputs = document.querySelectorAll('.fullpage-sub-size');
        const tamanhos = {};
        let totalQty = 0;
        sizeInputs.forEach(input => {
            const qty = parseInt(input.value, 10) || 0;
            if (qty > 0) {
                tamanhos[input.dataset.size] = qty;
                totalQty += qty;
            }
        });

        if (totalQty === 0) {
            alert('Adicione pelo menos uma peca.');
            setSubmittingState(false);
            return false;
        }

        await calculateFullpageSubTotal();

        const selectedAddons = getSelectedFullpageAddonIds().filter(addonId => {
            return !fullpageSubMatchedFabricAddonId || parseInt(addonId, 10) !== fullpageSubMatchedFabricAddonId;
        });
        const addonColorMap = {};
        if (hasAddonColors) {
            selectedAddons.forEach(addonId => {
                const input = document.querySelector(`#fullpage-addon-color-fields input[data-addon-color-id="${addonId}"]`);
                if (input && input.value.trim()) {
                    addonColorMap[String(addonId)] = input.value.trim();
                }
            });
        }

        const formData = new FormData();
        formData.append('action', isUpdate ? 'update_item' : 'add_sublimation_item');
        if (isUpdate && fullpageSubEditingItemId) {
            formData.append('editing_item_id', fullpageSubEditingItemId);
        }
        formData.append('sublimation_type', typeSlug);
        formData.append('art_name', artName);

        const fabricSelectEl = document.getElementById('fullpage_sub_fabric_type');
        const selectedFabricOption = fabricSelectEl?.options[fabricSelectEl?.selectedIndex];
        const tecidoIdToSend = selectedFabricOption?.dataset?.tecidoId || fullpageSubTypeMeta?.tecido_id || '';
        formData.append('fabric_type', fabricType);
        if (tecidoIdToSend) formData.append('tecido_id', tecidoIdToSend);
        formData.append('fabric_custom', fabricCustom);
        formData.append('fabric_color', fabricColor);
        formData.append('model_type', modelType);
        formData.append('base_collar', baseCollar);
        formData.append('fabric_surcharge', String(getFullpageFabricSurcharge()));
        formData.append('has_addon_colors', hasAddonColors ? '1' : '0');
        formData.append('addon_color_map', JSON.stringify(addonColorMap));

        Object.keys(tamanhos).forEach(size => {
            formData.append(`tamanhos[${size}]`, tamanhos[size]);
        });

        formData.append('quantity', String(totalQty));
        formData.append('unit_price', Number(fullpageSubUnitPrice || 0).toFixed(2));
        formData.append('unit_cost', Number(fullpageSubUnitCost || 0).toFixed(2));
        formData.append('art_notes', document.getElementById('fullpage_notes')?.value || '');
        formData.append('_token', document.querySelector('input[name="_token"]')?.value || '');

        selectedAddons.forEach(id => formData.append('sublimation_addons[]', String(id)));

        const coverFile = document.getElementById('fullpage_cover_image')?.files?.[0];
        if (coverFile) {
            formData.append('item_cover_image', coverFile);
        } else if (fullpageSubExistingCoverImage) {
            formData.append('existing_cover_image', fullpageSubExistingCoverImage);
        }

        const corelFile = document.getElementById('fullpage_corel_file')?.files?.[0];
        if (corelFile) {
            formData.append('corel_file', corelFile);
        } else if (fullpageSubExistingCorelFile) {
            formData.append('existing_corel_file', fullpageSubExistingCorelFile);
        }

        try {
            const response = await fetch('{{ isset($editData) ? route("orders.edit.sewing") : route("orders.wizard.sewing") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();
            if (!data.success) {
                alert(data.message || (isUpdate ? 'Erro ao atualizar item.' : 'Erro ao adicionar item.'));
                return false;
            }

            const sidebar = document.getElementById('items-sidebar-container');
            if (sidebar && data.html) sidebar.innerHTML = data.html;

            if (data.items_data) {
                itemsData = data.items_data;
                window.itemsData = itemsData;
            }

            hideSubFullpageForm();

            if (window.showToast) {
                const successMessage = isUpdate
                    ? 'Item SUB. TOTAL atualizado!'
                    : (wasDuplicate ? 'Item SUB. TOTAL duplicado!' : 'Item SUB. TOTAL adicionado!');
                window.showToast(successMessage, 'success');
            }
            return true;
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao processar solicitacao.');
            return false;
        } finally {
            setSubmittingState(false);
        }
    }
    window.submitFullpageSubItem = submitFullpageSubItem;

    function resetFullpageSubForm() {
        const typeSelect = document.getElementById('fullpage_sub_type');
        if (typeSelect) typeSelect.value = '';

        const artInput = document.getElementById('fullpage_art_name');
        if (artInput) artInput.value = '';

        const fabricTypeSelect = document.getElementById('fullpage_sub_fabric_type');
        if (fabricTypeSelect) fabricTypeSelect.value = '';

        const modelSelect = document.getElementById('fullpage_sub_model');
        if (modelSelect) modelSelect.value = '';

        const fabricCustomInput = document.getElementById('fullpage_sub_fabric_custom');
        if (fabricCustomInput) fabricCustomInput.value = '';

        const fabricSurchargeInput = document.getElementById('fullpage_sub_fabric_surcharge');
        if (fabricSurchargeInput) fabricSurchargeInput.value = '0.00';

        const fabricColorInput = document.getElementById('fullpage_sub_fabric_color');
        if (fabricColorInput) fabricColorInput.value = 'BRANCO';

        const baseCollarInput = document.getElementById('fullpage_sub_base_collar');
        if (baseCollarInput) baseCollarInput.value = 'REDONDA';

        const hasAddonColors = document.getElementById('fullpage_has_addon_colors');
        if (hasAddonColors) hasAddonColors.checked = false;

        const addonColorFields = document.getElementById('fullpage-addon-color-fields');
        if (addonColorFields) {
            addonColorFields.innerHTML = '';
            addonColorFields.classList.add('hidden');
        }

        document.querySelectorAll('.fullpage-sub-size').forEach(input => {
            input.value = 0;
        });

        const notesInput = document.getElementById('fullpage_notes');
        if (notesInput) notesInput.value = '';

        const coverInput = document.getElementById('fullpage_cover_image');
        if (coverInput) coverInput.value = '';
        resetFullpageCoverPreview();

        const corelInput = document.getElementById('fullpage_corel_file');
        if (corelInput) corelInput.value = '';
        resetFullpageCorelPreview();

        const addonsContainer = document.getElementById('fullpage-sub-addons');
        if (addonsContainer) addonsContainer.innerHTML = '<p class="text-sm text-gray-500 col-span-full text-center py-4">Selecione um tipo primeiro</p>';

        fullpageSubAddons = [];
        fullpageSubUnitPrice = 0;
        fullpageSubBaseUnitPrice = 0;
        fullpageSubAddonsAdjustment = 0;
        fullpageSubFabricSurcharge = 0;
        fullpageSubMatchedFabricAddonId = null;
        fullpageSubUnitCost = 0;
        fullpageSubTypeMeta = {
            defaultFabricName: '',
            startingPrice: 0,
            startingQuantityFrom: null,
            typeLabel: '',
            models: [],
        };

        if (fabricTypeSelect) {
            fabricTypeSelect.innerHTML = '<option value="">Selecione</option>';
            fabricTypeSelect.value = '';
        }

        const specialFabricHint = document.getElementById('fullpage-special-fabric-hint');
        if (specialFabricHint) specialFabricHint.textContent = '';

        const qtyEl = document.getElementById('fullpage-total-qty');
        if (qtyEl) qtyEl.textContent = '0';
        const qtyReviewEl = document.getElementById('fullpage-total-qty-review');
        if (qtyReviewEl) qtyReviewEl.textContent = '0';

        const unitPriceEl = document.getElementById('fullpage-unit-price');
        if (unitPriceEl) unitPriceEl.textContent = 'R$ 0,00';
        const unitPriceReviewEl = document.getElementById('fullpage-unit-price-review');
        if (unitPriceReviewEl) unitPriceReviewEl.textContent = 'R$ 0,00';

        const totalPriceEl = document.getElementById('fullpage-total-price');
        if (totalPriceEl) totalPriceEl.textContent = 'R$ 0,00';
        const totalPriceReviewEl = document.getElementById('fullpage-total-price-review');
        if (totalPriceReviewEl) totalPriceReviewEl.textContent = 'R$ 0,00';

        const breakdownEl = document.getElementById('fullpage-price-breakdown');
        if (breakdownEl) breakdownEl.textContent = 'Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00';
        const breakdownReviewEl = document.getElementById('fullpage-price-breakdown-review');
        if (breakdownReviewEl) breakdownReviewEl.textContent = 'Base R$ 0,00 + Adicionais R$ 0,00 + Tecido R$ 0,00';

        toggleFullpageSpecialFabric();
        fullpageSubEditingItemId = '';
        setFullpageSubmitButtonMode('add');
        setFullpageSubStep(1);
    }
    window.resetFullpageSubForm = resetFullpageSubForm;

    // --- Clipboard Paste Listener ---
    document.addEventListener('paste', function(e) {
        // Encontrar se algum modal relevante estÃ¡ aberto
        const wizardModal = document.getElementById('sewing-wizard-modal');
        const subModal = document.getElementById('sublimation-modal');

        let targetInput = null;
        let previewFn = null;

        if (subModal && !subModal.classList.contains('hidden')) {
            targetInput = document.getElementById('sub_wizard_file_input');
            previewFn = window.previewSublimationImage;
        } else if (wizardModal && !wizardModal.classList.contains('hidden') && window.wizardCurrentStep === 10) {
            targetInput = document.getElementById('wizard_file_input');
            previewFn = window.previewWizardImage;
        }

        if (!targetInput) return;

        const items = (e.clipboardData || window.clipboardData).items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                if (!blob) continue;

                // Criar arquivo fake do blob
                const file = new File([blob], "pasted-image-" + Date.now() + ".png", { type: "image/png" });

                // Usar DataTransfer para simular seleÃ§Ã£o de arquivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                targetInput.files = dataTransfer.files;

                // Disparar preview
                if (previewFn) previewFn(targetInput);
                
                // Mostrar notificaÃ§Ã£o se disponÃ­vel
                if (window.showToast) {
                    window.showToast('Imagem colada com sucesso!', 'success');
                } else if (typeof showNotification === 'function') {
                    showNotification('Imagem colada com sucesso!', 'success');
                }
                
                e.preventDefault();
                break;
            }
        }
    });
})();


</script>
@endpush
@endsection
