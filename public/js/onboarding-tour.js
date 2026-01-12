/**
 * Onboarding Tour - Sistema de tour guiado para novos usuários
 * Exibe dicas e explicações sobre elementos da interface
 */

class OnboardingTour {
    constructor() {
        this.steps = [];
        this.currentStep = -1;
        this.overlay = null;
        this.popover = null;
        this.isDark = document.documentElement.classList.contains('dark');

        this.init();
    }

    init() {
        this.createElements();
        window.addEventListener('resize', () => {
            if (this.currentStep >= 0) this.updatePosition();
        });
    }

    createElements() {
        // Overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'fixed inset-0 z-[9998] hidden transition-all duration-300';
        this.overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        this.overlay.style.backdropFilter = 'blur(2px)';

        // Popover
        this.popover = document.createElement('div');
        this.popover.className = 'fixed z-[9999] hidden bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 max-w-sm w-full transition-all duration-300 pointer-events-auto';
        this.popover.style.boxShadow = '0 10px 40px -10px rgba(0,0,0,0.3)';

        document.body.appendChild(this.overlay);
        document.body.appendChild(this.popover);
    }

    setSteps(steps) {
        this.steps = steps;
    }

    start() {
        if (this.steps.length === 0) return;
        this.currentStep = 0;
        this.overlay.classList.remove('hidden');
        this.popover.classList.remove('hidden');
        this.showStep();
        document.body.style.overflow = 'hidden';
    }

    showStep() {
        const step = this.steps[this.currentStep];
        const target = document.querySelector(step.target);

        if (!target) {
            console.warn(`Target ${step.target} not found for onboarding step ${this.currentStep}`);
            this.next();
            return;
        }

        // Renderizar conteúdo
        const progress = `${this.currentStep + 1} / ${this.steps.length}`;
        const isLast = this.currentStep === this.steps.length - 1;

        this.popover.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">${step.title || 'Dica'}</span>
                    <span class="text-xs text-gray-400 font-mono">${progress}</span>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">${target.dataset.tourTitle || step.title}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">${step.content}</p>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <button onclick="onboardingTour.skip()" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 font-medium transition">Pular</button>
                    <div class="flex gap-2">
                        ${this.currentStep > 0 ? `
                            <button onclick="onboardingTour.prev()" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-bold transition">Voltar</button>
                        ` : ''}
                        <button onclick="onboardingTour.next()" class="px-6 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-bold shadow-lg shadow-indigo-200 dark:shadow-none transition">
                            ${isLast ? 'Entendido!' : 'Próximo'}
                        </button>
                    </div>
                </div>
            </div>
        `;

        this.updatePosition();
        this.highlightTarget(target);
    }

    updatePosition() {
        const step = this.steps[this.currentStep];
        const target = document.querySelector(step.target);
        if (!target) return;

        const rect = target.getBoundingClientRect();
        const scrollY = window.scrollY;

        // Posição base (padrão bottom)
        let top = rect.bottom + 20;
        let left = rect.left + (rect.width / 2) - (this.popover.offsetWidth / 2);

        // Ajustes para as bordas da tela
        if (left < 20) left = 20;
        if (left + this.popover.offsetWidth > window.innerWidth - 20) {
            left = window.innerWidth - this.popover.offsetWidth - 20;
        }

        // Se bater no fundo, mostrar em cima
        if (top + this.popover.offsetHeight > window.innerHeight - 20) {
            top = rect.top - this.popover.offsetHeight - 20;
        }

        this.popover.style.top = `${top}px`;
        this.popover.style.left = `${left}px`;

        // Scroll suave até o elemento se necessário
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    highlightTarget(target) {
        // Remover highlight anterior
        const prev = document.querySelector('.tour-highlight');
        if (prev) {
            prev.classList.remove('tour-highlight');
            prev.style.position = prev.dataset.origPos;
            prev.style.zIndex = prev.dataset.origZ;
        }

        // Aplicar novo highlight
        target.dataset.origPos = window.getComputedStyle(target).position;
        target.dataset.origZ = window.getComputedStyle(target).zIndex;

        target.classList.add('tour-highlight');
        target.style.position = 'relative';
        target.style.zIndex = '9999';

        // Adicionar efeito de foco visual (opcional)
        target.style.boxShadow = '0 0 0 10px rgba(79, 70, 229, 0.4)';
        target.style.borderRadius = '8px';
        target.style.transition = 'all 0.3s ease';
    }

    next() {
        if (this.currentStep < this.steps.length - 1) {
            this.currentStep++;
            this.showStep();
        } else {
            this.finish();
        }
    }

    prev() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.showStep();
        }
    }

    skip() {
        this.finish();
    }

    finish() {
        this.currentStep = -1;
        this.overlay.classList.add('hidden');
        this.popover.classList.add('hidden');
        document.body.style.overflow = '';

        // Limpar highlights
        const highlighted = document.querySelector('.tour-highlight');
        if (highlighted) {
            highlighted.classList.remove('tour-highlight');
            highlighted.style.boxShadow = '';
            highlighted.style.position = highlighted.dataset.origPos;
            highlighted.style.zIndex = highlighted.dataset.origZ;
        }

        // Salvar que o tour foi visto
        localStorage.setItem('onboarding_seen', 'true');
    }

    static runIfNew(steps) {
        if (!localStorage.getItem('onboarding_seen')) {
            const tour = new OnboardingTour();
            tour.setSteps(steps);
            window.onboardingTour = tour;
            setTimeout(() => tour.start(), 1000);
        }
    }
}

// Estilos globais para o tour
const tourStyles = document.createElement('style');
tourStyles.textContent = `
    .tour-highlight {
        background-color: white !important;
    }
    .dark .tour-highlight {
        background-color: rgb(31 41 55) !important;
    }
`;
document.head.appendChild(tourStyles);

window.OnboardingTour = OnboardingTour;
