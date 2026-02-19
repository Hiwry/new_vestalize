{{-- 
    Side Panel Component - Painel lateral para edições rápidas
    
    O script side-panel.js já é carregado globalmente no layout admin.
    Este componente existe apenas para documentação e caso precise de customizações.
    
    Uso básico:
    1. window.sidePanel.open({ title: 'Editar', content: '...' })
    2. openSidePanelFromUrl('/rota/editar', 'Editar Item')
    3. quickEditClient(clientId) - Edição rápida de cliente
    4. quickEditOrder(orderId) - Edição rápida de pedido
    
    Usando data-attributes em links:
    <a href="/clientes/1/quick-edit" data-side-panel data-title="Editar Cliente">Editar</a>
--}}

{{-- O script side-panel.js já está incluído no layout admin.blade.php --}}
