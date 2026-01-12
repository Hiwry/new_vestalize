/**
 * Skeleton Loaders - Substitui spinners por placeholders animados
 * Melhora percepção de performance enquanto conteúdo carrega
 */

// CSS para Skeleton Loaders
const skeletonStyles = document.createElement('style');
skeletonStyles.textContent = `
    /* Animação de shimmer */
    @keyframes skeleton-shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    .skeleton {
        background: linear-gradient(
            90deg,
            rgb(229 231 235) 0%,
            rgb(243 244 246) 50%,
            rgb(229 231 235) 100%
        );
        background-size: 200% 100%;
        animation: skeleton-shimmer 1.5s ease-in-out infinite;
        border-radius: 0.375rem;
    }

    .dark .skeleton {
        background: linear-gradient(
            90deg,
            rgb(55 65 81) 0%,
            rgb(75 85 99) 50%,
            rgb(55 65 81) 100%
        );
        background-size: 200% 100%;
    }

    /* Variações de skeleton */
    .skeleton-text {
        height: 1rem;
        width: 100%;
    }

    .skeleton-text-sm {
        height: 0.75rem;
        width: 80%;
    }

    .skeleton-text-lg {
        height: 1.5rem;
        width: 60%;
    }

    .skeleton-title {
        height: 1.75rem;
        width: 40%;
    }

    .skeleton-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
    }

    .skeleton-avatar-lg {
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
    }

    .skeleton-button {
        height: 2.5rem;
        width: 6rem;
        border-radius: 0.5rem;
    }

    .skeleton-card {
        height: 200px;
        border-radius: 0.75rem;
    }

    .skeleton-image {
        aspect-ratio: 16/9;
        border-radius: 0.5rem;
    }

    .skeleton-badge {
        height: 1.5rem;
        width: 5rem;
        border-radius: 9999px;
    }

    /* Skeleton para tabela */
    .skeleton-table-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid rgb(229 231 235);
    }

    .dark .skeleton-table-row {
        border-color: rgb(55 65 81);
    }

    /* Skeleton para KPI card */
    .skeleton-kpi {
        padding: 1.5rem;
        background: white;
        border-radius: 0.75rem;
        border: 1px solid rgb(229 231 235);
    }

    .dark .skeleton-kpi {
        background: rgb(31 41 55);
        border-color: rgb(55 65 81);
    }
`;

// Adicionar CSS ao documento
if (document.head) {
    document.head.appendChild(skeletonStyles);
} else {
    document.addEventListener('DOMContentLoaded', () => {
        document.head.appendChild(skeletonStyles);
    });
}

// Classe utilitária para criar skeletons
class SkeletonLoader {
    // Skeleton de texto simples
    static text(lines = 3, widths = []) {
        const defaultWidths = ['100%', '90%', '80%', '70%', '95%'];
        let html = '';
        for (let i = 0; i < lines; i++) {
            const width = widths[i] || defaultWidths[i % defaultWidths.length];
            html += `<div class="skeleton skeleton-text mb-2" style="width: ${width}"></div>`;
        }
        return html;
    }

    // Skeleton de card de KPI
    static kpiCard() {
        return `
            <div class="skeleton-kpi">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="skeleton skeleton-text-sm mb-3" style="width: 60%"></div>
                        <div class="skeleton skeleton-title mb-2"></div>
                        <div class="skeleton skeleton-text-sm" style="width: 40%"></div>
                    </div>
                    <div class="skeleton skeleton-avatar"></div>
                </div>
            </div>
        `;
    }

    // Skeleton de múltiplos KPIs
    static kpiGrid(count = 4) {
        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">';
        for (let i = 0; i < count; i++) {
            html += this.kpiCard();
        }
        html += '</div>';
        return html;
    }

    // Skeleton de tabela
    static tableRow(cols = 5) {
        let html = '<div class="skeleton-table-row">';
        for (let i = 0; i < cols; i++) {
            const width = i === 0 ? '15%' : i === cols - 1 ? '10%' : `${20 + Math.random() * 20}%`;
            html += `<div class="skeleton skeleton-text" style="width: ${width}"></div>`;
        }
        html += '</div>';
        return html;
    }

    // Skeleton de tabela completa
    static table(rows = 5, cols = 5) {
        let html = '<div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">';
        // Header
        html += '<div class="skeleton-table-row bg-gray-50 dark:bg-gray-700">';
        for (let i = 0; i < cols; i++) {
            html += `<div class="skeleton skeleton-text-sm" style="width: ${15 + Math.random() * 10}%"></div>`;
        }
        html += '</div>';
        // Rows
        for (let i = 0; i < rows; i++) {
            html += this.tableRow(cols);
        }
        html += '</div>';
        return html;
    }

    // Skeleton de card de pedido
    static orderCard() {
        return `
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="skeleton skeleton-text-sm mb-2" style="width: 30%"></div>
                        <div class="skeleton skeleton-text" style="width: 70%"></div>
                    </div>
                    <div class="skeleton skeleton-badge"></div>
                </div>
                <div class="space-y-2 mb-3">
                    <div class="skeleton skeleton-text-sm" style="width: 60%"></div>
                    <div class="skeleton skeleton-text-sm" style="width: 40%"></div>
                </div>
                <div class="skeleton skeleton-button mt-3"></div>
            </div>
        `;
    }

    // Skeleton de lista de cards
    static cardList(count = 4) {
        let html = '<div class="space-y-4">';
        for (let i = 0; i < count; i++) {
            html += this.orderCard();
        }
        html += '</div>';
        return html;
    }

    // Skeleton de gráfico
    static chart() {
        return `
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="skeleton skeleton-title" style="width: 30%"></div>
                    <div class="skeleton skeleton-badge"></div>
                </div>
                <div class="skeleton skeleton-card" style="height: 300px"></div>
            </div>
        `;
    }

    // Skeleton de cliente
    static clientCard() {
        return `
            <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="skeleton skeleton-avatar-lg"></div>
                <div class="flex-1">
                    <div class="skeleton skeleton-text mb-2" style="width: 50%"></div>
                    <div class="skeleton skeleton-text-sm" style="width: 80%"></div>
                    <div class="skeleton skeleton-text-sm mt-2" style="width: 30%"></div>
                </div>
            </div>
        `;
    }

    // Método para substituir elemento por skeleton
    static replace(element, skeletonHtml) {
        const originalContent = element.innerHTML;
        const originalDisplay = element.style.display;

        element.innerHTML = skeletonHtml;
        element.dataset.originalContent = originalContent;
        element.dataset.originalDisplay = originalDisplay;

        return {
            restore: () => {
                element.innerHTML = element.dataset.originalContent || '';
                delete element.dataset.originalContent;
                delete element.dataset.originalDisplay;
            }
        };
    }

    // Método para mostrar skeleton com promessa
    static async withLoading(element, loadingType, asyncFn) {
        let skeleton;

        switch (loadingType) {
            case 'kpi':
                skeleton = this.kpiGrid();
                break;
            case 'table':
                skeleton = this.table();
                break;
            case 'cards':
                skeleton = this.cardList();
                break;
            case 'chart':
                skeleton = this.chart();
                break;
            default:
                skeleton = this.text();
        }

        const loader = this.replace(element, skeleton);

        try {
            const result = await asyncFn();
            return result;
        } finally {
            loader.restore();
        }
    }
}

// Expor globalmente
window.SkeletonLoader = SkeletonLoader;

// Função helper
window.showSkeleton = (selector, type = 'text') => {
    const element = document.querySelector(selector);
    if (!element) return null;

    let skeleton;
    switch (type) {
        case 'kpi':
        case 'kpis':
            skeleton = SkeletonLoader.kpiGrid();
            break;
        case 'table':
            skeleton = SkeletonLoader.table();
            break;
        case 'cards':
            skeleton = SkeletonLoader.cardList();
            break;
        case 'chart':
            skeleton = SkeletonLoader.chart();
            break;
        case 'client':
            skeleton = SkeletonLoader.clientCard();
            break;
        default:
            skeleton = SkeletonLoader.text();
    }

    return SkeletonLoader.replace(element, skeleton);
};
