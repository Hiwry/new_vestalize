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
        // Encontrar colunas marcadas como sticky
        const stickyHeaders = table.querySelectorAll('th[data-sticky]');
        const stickyColumnsIndexes = [];

        stickyHeaders.forEach((th, index) => {
            // Calcular posição left acumulada
            let leftPosition = 0;
            for (let i = 0; i < index; i++) {
                const prevTh = table.querySelector(`thead th:nth-child(${i + 1})`);
                if (prevTh && prevTh.hasAttribute('data-sticky')) {
                    leftPosition += prevTh.offsetWidth;
                }
            }

            th.style.position = 'sticky';
            th.style.left = leftPosition + 'px';
            th.style.zIndex = '20';
            th.classList.add('sticky-column');

            stickyColumnsIndexes.push({
                index: index + 1,
                left: leftPosition
            });
        });

        // Aplicar às células do body
        const tbody = table.querySelector('tbody');
        if (tbody) {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                stickyColumnsIndexes.forEach(({ index, left }) => {
                    const cell = row.querySelector(`td:nth-child(${index})`);
                    if (cell) {
                        cell.style.position = 'sticky';
                        cell.style.left = left + 'px';
                        cell.style.zIndex = '10';
                        cell.classList.add('sticky-column');
                    }
                });
            });
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

    /* Colunas fixas */
    .sticky-column {
        background-color: inherit;
    }

    /* Garantir que o fundo das células sticky seja opaco */
    .sticky-table thead .sticky-column {
        background-color: rgb(249 250 251); /* gray-50 */
    }

    .dark .sticky-table thead .sticky-column {
        background-color: rgb(55 65 81); /* gray-700 */
    }

    .sticky-table tbody .sticky-column {
        background-color: rgb(255 255 255); /* white */
    }

    .dark .sticky-table tbody .sticky-column {
        background-color: rgb(31 41 55); /* gray-800 */
    }

    /* Hover nas linhas mantém a cor nas colunas sticky */
    .sticky-table tbody tr:hover .sticky-column {
        background-color: rgb(249 250 251); /* gray-50 */
    }

    .dark .sticky-table tbody tr:hover .sticky-column {
        background-color: rgba(55, 65, 81, 0.5); /* gray-700/50 */
    }

    /* Sombra sutil para indicar colunas fixas */
    .sticky-column::after {
        content: '';
        position: absolute;
        top: 0;
        right: -8px;
        bottom: 0;
        width: 8px;
        pointer-events: none;
        background: linear-gradient(to right, rgba(0,0,0,0.05), transparent);
    }

    .sticky-column:last-of-type::after {
        display: block;
    }

    /* Última coluna sticky tem sombra */
    .sticky-table th[data-sticky]:last-of-type,
    .sticky-table tbody td.sticky-column:nth-child(2) {
        box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
    }

    .dark .sticky-table th[data-sticky]:last-of-type,
    .dark .sticky-table tbody td.sticky-column:nth-child(2) {
        box-shadow: 2px 0 5px -2px rgba(0,0,0,0.3);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .sticky-table .sticky-column {
            max-width: 120px;
        }
        
        .sticky-table .sticky-column .truncate {
            max-width: 100px;
        }
    }

    /* Indicador de scroll */
    .table-sticky-wrapper::before,
    .table-sticky-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 24px;
        pointer-events: none;
        z-index: 30;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .table-sticky-wrapper::after {
        right: 0;
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
    }

    .table-sticky-wrapper.has-scroll-right::after {
        opacity: 1;
    }

    .dark .table-sticky-wrapper::after {
        background: linear-gradient(to left, rgba(0,0,0,0.3), transparent);
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
