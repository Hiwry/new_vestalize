// Kanban Drag and Drop with Sortable.js
(function() {
    'use strict';

    const initializedColumns = new WeakSet();
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKanbanDragDrop);
    } else {
        initKanbanDragDrop();
    }

    // Re-init after AJAX navigation content swaps
    document.addEventListener('content-loaded', initKanbanDragDrop);
    document.addEventListener('ajax-content-loaded', initKanbanDragDrop);
    
    function initKanbanDragDrop() {
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js library not loaded!');
            return;
        }
        
        const kanbanColumns = document.querySelectorAll('[data-status-id]');
        
        if (kanbanColumns.length === 0) {
            console.warn('No Kanban columns found');
            return;
        }
        
        console.log(`Initializing drag-drop on ${kanbanColumns.length} columns`);
        
        kanbanColumns.forEach(column => {
            if (initializedColumns.has(column)) {
                return;
            }
            initializedColumns.add(column);
            new Sortable(column, {
                group: 'kanban-board',
                animation: 200,
                ghostClass: 'opacity-30',
                dragClass: 'shadow-2xl',
                handle: '.kanban-drag-handle',
                draggable: '.kanban-card',
                
                onEnd: function(evt) {
                    const orderId = evt.item.getAttribute('data-order-id');
                    const newStatusId = evt.to.getAttribute('data-status-id');
                    const oldStatusId = evt.from.getAttribute('data-status-id');
                    
                    if (newStatusId === oldStatusId) {
                        return;
                    }
                    
                    console.log(`Moving order ${orderId} to status ${newStatusId}`);
                    
                    fetch(`/kanban/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ 
                            order_id: orderId,
                            status_id: newStatusId 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof showNotification === 'function') {
                                showNotification('Status atualizado!', 'success');
                            }
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                        if (typeof showNotification === 'function') {
                            showNotification('Erro ao atualizar status', 'error');
                        }
                    });
                }
            });
        });
        
        console.log('Kanban drag-drop initialized!');
    }
})();
