{{-- 
    Side Panel Component - Painel lateral para edições rápidas
    
    Uso:
    1. Inclua este componente no layout: @include('components.side-panel')
    2. Use JavaScript para abrir: window.sidePanel.open({ title: 'Editar', content: '...' })
    3. Ou carregue de URL: openSidePanelFromUrl('/rota/editar', 'Editar Item')
--}}

@push('scripts')
<script src="{{ asset('js/side-panel.js') }}"></script>
<script>
    // Configurar Side Panel padrão após carregamento
    document.addEventListener('DOMContentLoaded', function() {
        // Interceptar links com data-side-panel para abrir no side panel
        document.querySelectorAll('[data-side-panel]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href') || this.dataset.url;
                const title = this.dataset.title || 'Editar';
                openSidePanelFromUrl(url, title);
            });
        });

        // Interceptar formulários dentro do side panel para envio via AJAX
        document.addEventListener('submit', async function(e) {
            const form = e.target;
            if (!form.closest('.side-panel-content')) return;

            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent;
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Salvando...
                `;
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success !== false) {
                    showSidePanelToast(data.message || 'Salvo com sucesso!', 'success');
                    
                    // Fechar após 1.5s e recarregar se necessário
                    setTimeout(() => {
                        window.sidePanel.close();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.reload !== false) {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Erro ao salvar');
                }
            } catch (error) {
                console.error('Erro:', error);
                showSidePanelToast(error.message || 'Erro ao salvar', 'error');
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    });

    // Função helper para abrir edição rápida de cliente
    function quickEditClient(clientId) {
        openSidePanelFromUrl(`/clientes/${clientId}/quick-edit`, 'Editar Cliente');
    }

    // Função helper para abrir edição rápida de pedido
    function quickEditOrder(orderId) {
        openSidePanelFromUrl(`/pedidos/${orderId}/quick-edit`, 'Editar Pedido');
    }

    // Função helper para abrir detalhes de pedido
    function quickViewOrder(orderId) {
        openSidePanelFromUrl(`/pedidos/${orderId}/quick-view`, 'Detalhes do Pedido');
    }
</script>
@endpush
