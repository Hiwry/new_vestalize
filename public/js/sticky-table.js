/**
 * Sticky Table Columns - Colunas fixas em tabelas com scroll horizontal
 * Fixa as colunas ID e Cliente enquanto o resto da tabela rola horizontalmente
 */

class StickyTableColumns {
    constructor() {
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        const tables = document.querySelectorAll('.sticky-table');
        tables.forEach(table => this.applyStickyColumns(table));
    }

    applyStickyColumns(table) {
        const stickyHeaders = table.querySelectorAll('th[data-sticky]');
        const stickyColumnsIndexes = [];
        const wrapper = table.closest('.table-sticky-wrapper');

        stickyHeaders.forEach((th, index) => {
            let leftPosition = 0;
            for (let i = 0; i < index; i++) {
                const prevTh = table.querySelector(`thead th:nth-child(${i + 1})`);
                if (prevTh && prevTh.hasAttribute('data-sticky')) {
                    leftPosition += prevTh.offsetWidth;
                }
            }

            th.style.position = 'sticky';
            th.style.left = leftPosition + 'px';
            th.classList.add('sticky-column');

            // Marcar a última coluna fixa para sombra
            if (index === stickyHeaders.length - 1) {
                th.setAttribute('data-sticky-last', 'true');
            }

            stickyColumnsIndexes.push({
                index: index + 1,
                left: leftPosition,
                isLast: index === stickyHeaders.length - 1
            });
        });

        const tbody = table.querySelector('tbody');
        if (tbody) {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                stickyColumnsIndexes.forEach(({ index, left, isLast }) => {
                    const cell = row.querySelector(`td:nth-child(${index})`);
                    if (cell) {
                        cell.style.position = 'sticky';
                        cell.style.left = left + 'px';
                        cell.classList.add('sticky-column');
                        if (isLast) cell.setAttribute('data-sticky-last', 'true');
                    }
                });
            });
        }

        // Detectar scroll inicial e adicionar listener
        if (wrapper) {
            const checkScroll = () => {
                wrapper.classList.toggle('has-scroll', wrapper.scrollLeft > 0);
                const hasScrollRight = wrapper.scrollWidth > wrapper.clientWidth &&
                    wrapper.scrollLeft < (wrapper.scrollWidth - wrapper.clientWidth - 5);
                wrapper.classList.toggle('has-scroll-right', hasScrollRight);
            };
            wrapper.addEventListener('scroll', checkScroll);
            window.addEventListener('resize', checkScroll);
            setTimeout(checkScroll, 100);
        }
    }

    // Recalcular posições após mudanças dinâmicas
    recalculate() {
        this.setup();
    }
}

// CSS para tabelas com colunas sticky
const stickyTableStyles = document.createElement('style');
stickyTableStyles.textContent = `
    /* Container da tabela com scroll horizontal */
    .table-sticky-wrapper {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }

    /* Tabela com colunas sticky */
    .sticky-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Colunas fixas - Herdam o fundo do container ou card */
    .sticky-column {
        background-color: var(--card-bg, #ffffff) !important;
        transition: background-color 0.2s ease;
    }

    /* No modo hover da linha, a coluna sticky deve acompanhar */
    .sticky-table tbody tr:hover .sticky-column {
        background-color: var(--avento-bg-card-hover, #f9fafb) !important;
    }

    /* No Dark Mode, usamos as variáveis específicas se disponíveis */
    .dark .sticky-column {
        background-color: var(--card-bg, #030303) !important;
    }

    .dark .sticky-table tbody tr:hover .sticky-column {
        background-color: var(--avento-bg-card-hover, #080808) !important;
    }

    /* Cabeçalhos sticky */
    .sticky-table thead .sticky-column {
        background-color: var(--avento-bg-secondary, #f8fafc) !important;
        z-index: 25 !important;
    }
    
    .dark .sticky-table thead .sticky-column {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }

    /* Sombra indicativa sutil apenas na última coluna fixa */
    .sticky-column[data-sticky-last]::after {
        content: '';
        position: absolute;
        top: 0;
        right: -15px;
        bottom: 0;
        width: 15px;
        pointer-events: none;
        background: linear-gradient(to right, rgba(0,0,0,0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .dark .sticky-column[data-sticky-last]::after {
        background: linear-gradient(to right, rgba(0,0,0,0.2), transparent);
    }

    .table-sticky-wrapper.has-scroll .sticky-column[data-sticky-last]::after {
        opacity: 1;
    }

    /* Ajuste de z-index para garantir que nada passe por cima das colunas fixas */
    .sticky-table th.sticky-column { z-index: 30 !important; }
    .sticky-table td.sticky-column { z-index: 20 !important; }

    /* Indicador de scroll lateral (sombra na borda direita do wrapper) */
    .table-sticky-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 30px;
        pointer-events: none;
        z-index: 40;
        background: linear-gradient(to left, rgba(0,0,0,0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .dark .table-sticky-wrapper::after {
        background: linear-gradient(to left, rgba(0,0,0,0.2), transparent);
    }

    .table-sticky-wrapper.has-scroll-right::after {
        opacity: 1;
    }
`;
document.head.appendChild(stickyTableStyles);

// Inicializar automaticamente
window.stickyTableColumns = new StickyTableColumns();

// Função utilitária para atualizar scroll indicators
function updateScrollIndicators() {
    const wrappers = document.querySelectorAll('.table-sticky-wrapper');
    wrappers.forEach(wrapper => {
        const hasScrollRight = wrapper.scrollWidth > wrapper.clientWidth &&
            wrapper.scrollLeft < (wrapper.scrollWidth - wrapper.clientWidth - 5);
        wrapper.classList.toggle('has-scroll-right', hasScrollRight);
    });
}

// Atualizar indicadores de scroll
document.addEventListener('DOMContentLoaded', () => {
    updateScrollIndicators();

    document.querySelectorAll('.table-sticky-wrapper').forEach(wrapper => {
        wrapper.addEventListener('scroll', updateScrollIndicators);
    });

    window.addEventListener('resize', updateScrollIndicators);
});
