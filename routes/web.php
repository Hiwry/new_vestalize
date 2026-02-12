<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Rota específica para imagens de aplicação (alternativa mais confiável que /storage/)
// Esta rota NÃO depende de .htaccess ou symlink
Route::get('/imagens-aplicacao/{filename}', [\App\Http\Controllers\StorageController::class, 'serveApplicationImage'])
    ->where('filename', '[^/]+')
    ->name('application.image')
    ->middleware([]);

// Landing Page para Personalizados (subdomain em produção - DEVE VIR ANTES da rota principal)
Route::domain('personalizados.vestalize.com')->group(function () {
    Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'personalizados'])->name('welcome.personalizados');
    
    // Forçar Login e Logout para o domínio principal para manter consistência e design
    Route::get('/login', function() {
        return redirect()->to(config('app.url') . '/login');
    });
    Route::get('/logout', function() {
        return redirect()->to(config('app.url') . '/logout');
    });
});

// Rota Pública da Landing Page (principal)
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Fallback route for local development
Route::get('/lp-personalizados', [\App\Http\Controllers\WelcomeController::class, 'personalizados'])->name('welcome.personalizados.dev');
Route::post('/save-lead', [\App\Http\Controllers\LeadController::class, 'store'])->name('leads.store');
// Link público de indicação
Route::get('/r/{code}', [\App\Http\Controllers\AffiliatePortalController::class, 'redirect'])->name('affiliate.ref');

// Rota para servir arquivos do storage quando symlink não existe (deve vir antes das outras rotas)
// IMPORTANTE: Esta rota deve ser pública e não requerer autenticação
Route::get('/storage/{path}', [\App\Http\Controllers\StorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve')
    ->middleware([]); // Garantir que não há middleware bloqueando

// Rotas Públicas para Clientes
Route::prefix('pedido')->name('client.order.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\ClientOrderController::class, 'show'])->name('show');
    Route::post('/{token}/confirmar', [\App\Http\Controllers\ClientOrderController::class, 'confirm'])->name('confirm');
});

// Catálogo Público (acesso sem login)
Route::prefix('catalogo')->name('catalog.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CatalogController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\CatalogController::class, 'show'])->name('show');
});

// Orçamento Online Público
Route::prefix('solicitar-orcamento')->name('quote.')->group(function () {
    Route::get('/', function() {
        return "Link inválido. Por favor, utilize o link completo fornecido pela empresa.";
    })->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\PublicQuoteController::class, 'show'])->name('public');
    Route::post('/{slug}', [\App\Http\Controllers\PublicQuoteController::class, 'submit'])->name('submit');
});

// Termos e Condições (Rotas Públicas)
Route::view('/termos', 'terms')->name('terms.show');
Route::view('/privacidade', 'privacy')->name('privacy.show');

// Todas as rotas autenticadas
Route::middleware('auth')->group(function () {
    // Home/Dashboard
    // Nomear como "dashboard" para alinhar com o redirecionamento do login
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::view('/vendas', 'sales.index')->name('sales.index');
    Route::get('/financeiro', [\App\Http\Controllers\FinancialController::class, 'index'])->name('financial.dashboard')->middleware('plan:financial');
    Route::get('/financeiro/nfe', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.invoices.index')->middleware('plan:financial');
    
    // Links Rápidos
    Route::get('/links', [\App\Http\Controllers\DashboardController::class, 'links'])->name('links.index');
    
    // Configurações
    Route::get('/settings/personalizations', [\App\Http\Controllers\SettingsController::class, 'personalizations'])->name('settings.personalizations');
    Route::view('/settings/customized-products', 'settings.customized-products')->name('settings.customized-products');
    Route::get('/settings/company', [\App\Http\Controllers\SettingsController::class, 'company'])->name('settings.company');
    Route::put('/settings/company', [\App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('settings.company.update');
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    
    // Auditoria e Logs de Atividade
    Route::prefix('admin/audit')->name('admin.audit.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
        Route::get('/{id}/details', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
    });
    
    // Personalização de Marca (White-labeling)
    Route::get('/settings/branding', [\App\Http\Controllers\TenantBrandingController::class, 'edit'])->name('settings.branding.edit')->middleware('plan:branding');
    Route::post('/settings/branding', [\App\Http\Controllers\TenantBrandingController::class, 'update'])->name('settings.branding.update')->middleware('plan:branding');
    
    // Configuração de Nota Fiscal
    Route::get('/settings/nfe', [\App\Http\Controllers\Admin\TenantInvoiceConfigController::class, 'edit'])->name('admin.invoice-config.edit');
    Route::get('/settings/nfe/tenant/{tenant}', [\App\Http\Controllers\Admin\TenantInvoiceConfigController::class, 'editTenant'])->name('admin.invoice-config.editTenant');
    Route::put('/settings/nfe', [\App\Http\Controllers\Admin\TenantInvoiceConfigController::class, 'update'])->name('admin.invoice-config.update');
    Route::post('/settings/nfe/test', [\App\Http\Controllers\Admin\TenantInvoiceConfigController::class, 'testConnection'])->name('admin.invoice-config.test');
    
    // Emissão de NF-e
    Route::post('/pedidos/{id}/nfe/emitir', [\App\Http\Controllers\Admin\InvoiceController::class, 'emit'])->name('admin.invoice.emit');
    Route::get('/pedidos/{id}/nfe', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.invoice.show');
    Route::get('/nfe/{id}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'checkStatus'])->name('admin.invoice.status');
    Route::post('/nfe/{id}/cancelar', [\App\Http\Controllers\Admin\InvoiceController::class, 'cancel'])->name('admin.invoice.cancel');
    
    // Lista de Pedidos
    Route::get('/pedidos', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{id}/detalhes', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('/pedidos/arquivo/remover', [\App\Http\Controllers\OrderController::class, 'deleteFile'])->name('orders.file.delete');
    
    // Gerenciamento de Pagamentos
    Route::post('/pedidos/{id}/pagamento/adicionar', [\App\Http\Controllers\OrderController::class, 'addPayment'])->name('orders.payment.add');
    Route::put('/pedidos/{id}/pagamento/editar', [\App\Http\Controllers\OrderController::class, 'updatePayment'])->name('orders.payment.update');
    Route::delete('/pedidos/{id}/pagamento/remover', [\App\Http\Controllers\OrderController::class, 'deletePayment'])->name('orders.payment.delete');
    
    // PIX QR Code - Gerar dinamicamente
    Route::get('/pedidos/{id}/pix', function ($id) {
        $order = \App\Models\Order::findOrFail($id);
        $pixService = app(\App\Services\PixService::class);
        $remaining = $order->payments->first()?->remaining_amount ?? $order->total;
        return response()->json($pixService->generate($remaining));
    })->name('orders.pix.generate');
    
    // Atualizar data de entrega
    Route::put('/pedidos/{id}/delivery-date', [\App\Http\Controllers\OrderController::class, 'updateDeliveryDate'])->name('orders.delivery-date.update');
    
    // Download de Nota do Cliente
    Route::get('/pedidos/{id}/nota-cliente', [\App\Http\Controllers\OrderController::class, 'downloadClientReceipt'])->name('orders.client-receipt');
    
    // Gerar Link de Compartilhamento
    Route::post('/pedidos/{id}/gerar-link', [\App\Http\Controllers\OrderController::class, 'generateShareLink'])->name('orders.generate-share-link');
    
    // Edição de Pedidos
    Route::get('/pedidos/{id}/pagamento/{paymentId}', [\App\Http\Controllers\OrderController::class, 'getPayment'])->name('orders.payment.get');
    Route::post('/pedidos/{id}/solicitar-edicao', [\App\Http\Controllers\OrderController::class, 'requestEdit'])->name('orders.request-edit');
    Route::post('/pedidos/{id}/aprovar-edicao', [\App\Http\Controllers\OrderController::class, 'approveEdit'])->name('orders.approve-edit');
    Route::post('/pedidos/{id}/rejeitar-edicao', [\App\Http\Controllers\OrderController::class, 'rejectEdit'])->name('orders.reject-edit');
    Route::get('/pedidos/{id}/editar', [\App\Http\Controllers\OrderController::class, 'editOrder'])->name('orders.edit');
    Route::put('/pedidos/{id}/atualizar', [\App\Http\Controllers\OrderController::class, 'updateOrder'])->name('orders.update');
    
    // Cancelamento de Pedidos
    Route::post('/pedidos/{order}/cancelar', [\App\Http\Controllers\OrderCancellationController::class, 'request'])->name('orders.cancellation.request');
    
    // Solicitação de Edição de Pedidos
    Route::post('/pedidos/{order}/edit-request', [\App\Http\Controllers\OrderEditRequestController::class, 'request'])->name('orders.edit-request.request');
    
    // Duplicar Pedido
    Route::post('/pedidos/{id}/duplicar', [\App\Http\Controllers\OrderController::class, 'duplicate'])->name('orders.duplicate');

    // Ação de Pin de Item (AJAX)
    Route::post('/order-items/{id}/toggle-pin', [\App\Http\Controllers\OrderWizardController::class, 'togglePin'])->name('order-items.toggle-pin');

    // Wizard de Pedido (6 etapas)
    Route::prefix('pedidos')->group(function () {
        // Etapa 1: Cliente
        Route::get('novo', [\App\Http\Controllers\OrderWizardController::class, 'start'])->name('orders.wizard.start');
        Route::get('cliente', [\App\Http\Controllers\OrderWizardController::class, 'start'])->name('orders.wizard.client.show'); // Redirect GET to start
        Route::post('cliente', [\App\Http\Controllers\OrderWizardController::class, 'storeClient'])->name('orders.wizard.client');
        
        // Etapa 2: Tipo de Personalização
        Route::get('tipo-personalizacao', [\App\Http\Controllers\OrderWizardController::class, 'personalizationType'])->name('orders.wizard.personalization-type');
        
        // Etapa 3: Itens/Costura (baseado no tipo escolhido)
        Route::match(['get','post'],'itens', [\App\Http\Controllers\OrderWizardController::class, 'items'])->name('orders.wizard.items');
        Route::match(['get','post'],'costura', [\App\Http\Controllers\OrderWizardController::class, 'sewing'])->name('orders.wizard.sewing');
        
        // Etapa 4: Detalhes/Personalização
        Route::match(['get','post'],'personalizacao', [\App\Http\Controllers\OrderWizardController::class, 'customization'])->name('orders.wizard.customization');
        Route::get('personalizacao/refresh', [\App\Http\Controllers\OrderWizardController::class, 'refreshCustomizations'])->name('orders.wizard.customization.refresh');
        
        // Etapa 5: Pagamento
        Route::match(['get','post'],'pagamento', [\App\Http\Controllers\OrderWizardController::class, 'payment'])->name('orders.wizard.payment');
        
        // Etapa 6: Confirmação
        Route::get('confirmacao', [\App\Http\Controllers\OrderWizardController::class, 'confirm'])->name('orders.wizard.confirm');
        Route::post('finalizar', [\App\Http\Controllers\OrderWizardController::class, 'finalize'])->name('orders.wizard.finalize');
        Route::get('finalizar', function () {
            return redirect()->route('kanban.index')->with('info', 'Pedido já foi finalizado ou sessão expirou.');
        });
    });

    // Sistema de Edição de Pedidos
    Route::prefix('pedidos')->group(function () {
        Route::get('{id}/editar', [\App\Http\Controllers\EditOrderController::class, 'start'])->name('orders.edit.start');
        Route::match(['get','post'],'editar/cliente', [\App\Http\Controllers\EditOrderController::class, 'client'])->name('orders.edit.client');
        Route::match(['get','post'],'editar/costura', [\App\Http\Controllers\EditOrderController::class, 'sewing'])->name('orders.edit.sewing');
        Route::match(['get','post'],'editar/personalizacao', [\App\Http\Controllers\EditOrderController::class, 'customization'])->name('orders.edit.customization');
        Route::match(['get','post'],'editar/pagamento', [\App\Http\Controllers\EditOrderController::class, 'payment'])->name('orders.edit.payment');
        Route::get('editar/confirmacao', [\App\Http\Controllers\EditOrderController::class, 'confirm'])->name('orders.edit.confirm');
        Route::post('editar/finalizar', [\App\Http\Controllers\EditOrderController::class, 'finalizeEdit'])->name('orders.edit.finalize');
        Route::get('editar/clear-session', [\App\Http\Controllers\EditOrderController::class, 'clearSession'])->name('orders.edit.clear-session');
    });

    // Termos e Condições movidas para fora do grupo auth (públicas)

    // Sistema de Orçamento
    Route::prefix('orcamento')->name('budget.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BudgetController::class, 'index'])->name('index');
        Route::get('novo', [\App\Http\Controllers\BudgetController::class, 'start'])->name('start');
        Route::get('cliente', [\App\Http\Controllers\BudgetController::class, 'start'])->name('client.show'); // Redirect GET to start
        Route::post('cliente', [\App\Http\Controllers\BudgetController::class, 'storeClient'])->name('client');
        Route::match(['get','post'],'personalizacao-tipo', [\App\Http\Controllers\BudgetController::class, 'personalizationType'])->name('personalization-type');
        Route::match(['get','post'],'itens', [\App\Http\Controllers\BudgetController::class, 'items'])->name('items');
        Route::match(['get','post'],'personalizacao', [\App\Http\Controllers\BudgetController::class, 'customization'])->name('customization');
        Route::delete('personalizacao/{index}', [\App\Http\Controllers\BudgetController::class, 'deleteCustomization'])->name('customization.delete');
        Route::get('personalizacao/refresh', [\App\Http\Controllers\BudgetController::class, 'refreshCustomizations'])->name('customization.refresh');
        Route::get('confirmacao', [\App\Http\Controllers\BudgetController::class, 'confirm'])->name('confirm');
        Route::post('finalizar', [\App\Http\Controllers\BudgetController::class, 'finalize'])->name('finalize');
        Route::get('{id}/pdf', [\App\Http\Controllers\BudgetController::class, 'downloadPdf'])->name('pdf')->middleware('plan:pdf_quotes');
        Route::get('{id}/detalhes', [\App\Http\Controllers\BudgetController::class, 'show'])->name('show');
        Route::post('{id}/solicitar-edicao', [\App\Http\Controllers\BudgetController::class, 'requestEdit'])->name('request-edit');
        Route::post('{id}/aprovar', [\App\Http\Controllers\BudgetController::class, 'approve'])->name('approve');
        Route::post('{id}/rejeitar', [\App\Http\Controllers\BudgetController::class, 'reject'])->name('reject');
        Route::get('{id}/converter-pedido', [\App\Http\Controllers\BudgetController::class, 'showConvertForm'])->name('convert-form');
        Route::post('{id}/converter-pedido', [\App\Http\Controllers\BudgetController::class, 'convertToOrder'])->name('convert-to-order');
        
        // Quick Budget (Orçamento Rápido)
        Route::get('rapido', [\App\Http\Controllers\BudgetController::class, 'quickCreate'])->name('quick-create');
        Route::post('rapido', [\App\Http\Controllers\BudgetController::class, 'storeQuick'])->name('quick-store');
        Route::get('{id}/whatsapp', [\App\Http\Controllers\BudgetController::class, 'shareWhatsApp'])->name('whatsapp');
    });



    // Notificações
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('clear-all', [\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('clear-all');
        Route::delete('{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Leads (Lista VIP)
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('admin.leads.index');
    Route::post('/leads/bulk-delete', [\App\Http\Controllers\Admin\LeadController::class, 'bulkDelete'])->name('admin.leads.bulk-delete');
    Route::delete('/leads/{id}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('admin.leads.destroy');

    // Kanban
    Route::middleware('plan:kanban')->group(function () {
        Route::get('/kanban', [\App\Http\Controllers\KanbanController::class, 'index'])->name('kanban.index');
        Route::post('/kanban/update-status', [\App\Http\Controllers\KanbanController::class, 'updateStatus'])->name('kanban.update-status');
        Route::get('/kanban/order/{id}', [\App\Http\Controllers\KanbanController::class, 'getOrderDetails']);
        Route::post('/kanban/order/{id}/comment', [\App\Http\Controllers\KanbanController::class, 'addComment']);
        Route::post('/kanban/order/{id}/add-payment', [\App\Http\Controllers\KanbanController::class, 'addPayment']);
        Route::get('/kanban/download-costura/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadCostura']);
        Route::get('/kanban/download-personalizacao/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadPersonalizacao']);
        Route::get('/kanban/download-files/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadFiles']);
        Route::post('/kanban/upload-item-file', [\App\Http\Controllers\KanbanController::class, 'uploadItemFile'])->name('kanban.upload-item-file');
        Route::post('/kanban/delete-file', [\App\Http\Controllers\KanbanController::class, 'deleteFile'])->name('kanban.delete-file');
    });

    // Catálogo

    // Produção (para administradores e produção)
    Route::prefix('producao')->name('production.')->group(function () {
        Route::get('/', function() {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado. Apenas administradores e usuários de produção podem acessar.');
            }
            return app(\App\Http\Controllers\ProductionController::class)->index(request());
        })->name('index');
        Route::get('/kanban', function() {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado. Apenas administradores e usuários de produção podem acessar.');
            }
            return app(\App\Http\Controllers\ProductionController::class)->kanban(request());
        })->name('kanban');
        Route::get('/pdf', function() {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado. Apenas administradores e usuários de produção podem acessar.');
            }
            return app(\App\Http\Controllers\ProductionController::class)->downloadPdf(request());
        })->name('pdf');
        
        // Solicitações de Edição para Produção
        Route::get('/edit-requests', function() {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado. Apenas administradores e usuários de produção podem acessar.');
            }
            return app(\App\Http\Controllers\OrderEditRequestController::class)->index();
        })->name('edit-requests.index');
        
        Route::get('/edit-requests/{editRequest}/changes', function($editRequest) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado.');
            }
            return app(\App\Http\Controllers\OrderEditRequestController::class)
                ->showChanges(\App\Models\OrderEditRequest::findOrFail($editRequest));
        })->name('edit-requests.changes');
        
        Route::post('/edit-requests/{editRequest}/approve', function($editRequest) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado.');
            }
            return app(\App\Http\Controllers\OrderEditRequestController::class)
                ->approve(request(), \App\Models\OrderEditRequest::findOrFail($editRequest));
        })->name('edit-requests.approve');
        
        Route::post('/edit-requests/{editRequest}/reject', function($editRequest) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado.');
            }
            return app(\App\Http\Controllers\OrderEditRequestController::class)
                ->reject(request(), \App\Models\OrderEditRequest::findOrFail($editRequest));
        })->name('edit-requests.reject');
        
        // Solicitações de Antecipação de Entrega para Produção
        Route::get('/delivery-requests', function() {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado. Apenas administradores e usuários de produção podem acessar.');
            }
            return app(\App\Http\Controllers\DeliveryRequestController::class)->index();
        })->name('delivery-requests.index');
        
        Route::post('/delivery-requests/{deliveryRequest}/approve', function($deliveryRequest) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado.');
            }
            return app(\App\Http\Controllers\DeliveryRequestController::class)
                ->approve(request(), \App\Models\DeliveryRequest::findOrFail($deliveryRequest));
        })->name('delivery-requests.approve');
        
        Route::post('/delivery-requests/{deliveryRequest}/reject', function($deliveryRequest) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isProducao()) {
                abort(403, 'Acesso negado.');
            }
            return app(\App\Http\Controllers\DeliveryRequestController::class)
                ->reject(request(), \App\Models\DeliveryRequest::findOrFail($deliveryRequest));
        })->name('delivery-requests.reject');
    });

    // Gerenciamento de Clientes
    Route::prefix('clientes')->name('clients.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClientController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ClientController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\ClientController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('destroy');
        
        // Edição rápida via Side Panel (AJAX)
        Route::get('/{id}/quick-edit', [\App\Http\Controllers\ClientController::class, 'quickEdit'])->name('quick-edit');
        Route::put('/{id}/quick-update', [\App\Http\Controllers\ClientController::class, 'quickUpdate'])->name('quick-update');
    });

    // Gerenciamento de Colunas do Kanban
    Route::prefix('kanban/columns')->name('kanban.columns.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StatusController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\StatusController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\StatusController::class, 'store'])->name('store');
        Route::get('/{status}/edit', [\App\Http\Controllers\StatusController::class, 'edit'])->name('edit');
        Route::put('/{status}', [\App\Http\Controllers\StatusController::class, 'update'])->name('update');
        Route::delete('/{status}', [\App\Http\Controllers\StatusController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [\App\Http\Controllers\StatusController::class, 'reorder'])->name('reorder');
        Route::post('/{status}/move-orders', [\App\Http\Controllers\StatusController::class, 'moveOrders'])->name('move-orders');
    });

    // API Routes
    Route::prefix('api')->middleware('throttle:60,1')->group(function () {
        Route::get('/clients/search', [\App\Http\Controllers\Api\ClientController::class, 'search']);
        Route::get('/product-options', [\App\Http\Controllers\Api\ClientController::class, 'getProductOptions']);
        Route::get('/product-options-with-parents', [\App\Http\Controllers\Api\ClientController::class, 'getProductOptionsWithParents']);
        Route::get('/sublimation-sizes', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationSizes']);
        Route::get('/sublimation-locations', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationLocations']);
        Route::get('/sublimation-price/{sizeId}/{quantity}', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationPrice']);
        Route::get('/serigraphy-colors', [\App\Http\Controllers\Api\ClientController::class, 'getSerigraphyColors']);
        Route::get('/size-surcharge/{size}', [\App\Http\Controllers\Api\ClientController::class, 'getSizeSurcharge']);
        
        Route::get('/personalization-prices/price', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPrice']);
        Route::get('/personalization-prices/sizes', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getSizes']);
        Route::get('/personalization-prices/ranges', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPriceRanges']);
        Route::post('/personalization-prices/multiple', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getMultiplePrices'])->middleware('throttle:30,1');
        
        Route::get('/sublimation-addons', [\App\Http\Controllers\Api\SublimationAddonController::class, 'getAddons']);
        Route::put('/addons/update', [\App\Http\Controllers\Admin\SublimationAddonController::class, 'updateAddons'])->name('admin.addons.update')->middleware('throttle:10,1');
        
        // Stock APIs (moved here to have session auth for tenant_id)
        Route::get('/stocks/by-cut-type', [\App\Http\Controllers\StockController::class, 'getByCutType'])->name('api.stocks.by-cut-type');
        Route::get('/stocks/fabric-by-cut-type', [\App\Http\Controllers\StockController::class, 'getFabricByCutType']);
        Route::get('/stocks/fabric-types', [\App\Http\Controllers\StockController::class, 'getFabricTypes']);
        Route::get('/stocks/check', [\App\Http\Controllers\StockController::class, 'check']);
    });
    
    // Aprovações de Caixa (deve vir ANTES do resource para não ser capturado)
    Route::prefix('cash/approvals')->name('cash.approvals.')->middleware(['cash', 'plan:financial'])->group(function () {
        Route::get('/', [\App\Http\Controllers\CashApprovalController::class, 'index'])->name('index');
        Route::get('/{orderId}/receipt', [\App\Http\Controllers\CashApprovalController::class, 'viewReceipt'])->name('view-receipt');
        Route::post('/{orderId}/approve', [\App\Http\Controllers\CashApprovalController::class, 'approve'])->name('approve');
        Route::post('/{orderId}/attach-receipt', [\App\Http\Controllers\CashApprovalController::class, 'attachReceipt'])->name('attach-receipt');
        Route::post('/{orderId}/remove-receipt', [\App\Http\Controllers\CashApprovalController::class, 'removeReceipt'])->name('remove-receipt');
        Route::post('/approve-multiple', [\App\Http\Controllers\CashApprovalController::class, 'approveMultiple'])->name('approve-multiple');
    });
    
    Route::resource('cash', \App\Http\Controllers\CashController::class)->middleware(['cash', 'plan:financial']);
    Route::get('/cash/report/simplified', [\App\Http\Controllers\CashController::class, 'reportSimplified'])->name('cash.report.simplified')->middleware('cash');
    Route::get('/cash/report/detailed', [\App\Http\Controllers\CashController::class, 'reportDetailed'])->name('cash.report.detailed')->middleware(['cash', 'plan:reports_complete']);

    // PDV - Ponto de Venda
    Route::prefix('pdv')->name('pdv.')->middleware('plan:pdv')->group(function () {
        Route::get('/', [\App\Http\Controllers\PDVController::class, 'index'])->name('index');
        Route::get('/vendas', [\App\Http\Controllers\PDVController::class, 'sales'])->name('sales');
        Route::get('/vendas/{id}/editar', [\App\Http\Controllers\PDVController::class, 'editSale'])->name('sales.edit');
        Route::put('/vendas/{id}', [\App\Http\Controllers\PDVController::class, 'updateSale'])->name('sales.update');
        Route::post('/vendas/{id}/cancelar', [\App\Http\Controllers\PDVController::class, 'cancelSale'])->name('sales.cancel');
        Route::post('/cart/add', [\App\Http\Controllers\PDVController::class, 'addToCart'])->name('cart.add');
        Route::put('/cart/update', [\App\Http\Controllers\PDVController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove', [\App\Http\Controllers\PDVController::class, 'removeFromCart'])->name('cart.remove');
        Route::delete('/cart/clear', [\App\Http\Controllers\PDVController::class, 'clearCart'])->name('cart.clear');
        Route::get('/cart', [\App\Http\Controllers\PDVController::class, 'getCart'])->name('cart.get');
        Route::post('/checkout', [\App\Http\Controllers\PDVController::class, 'checkout'])->name('checkout');
        Route::get('/sale-receipt/{id}', [\App\Http\Controllers\PDVController::class, 'downloadSaleReceipt'])->name('sale-receipt');
    });

    // Módulo Personalizados
    Route::prefix('personalizados')->name('personalized.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\PersonalizedController::class, 'index'])->name('index');
        Route::get('/pedidos', [\App\Http\Controllers\OrderController::class, 'indexPersonalized'])->name('orders.index');
        Route::post('/cart/add', [\App\Http\Controllers\PersonalizedController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/remove', [\App\Http\Controllers\PersonalizedController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [\App\Http\Controllers\PersonalizedController::class, 'clearCart'])->name('cart.clear');
        Route::post('/checkout', [\App\Http\Controllers\PersonalizedController::class, 'checkout'])->name('checkout');
    });

    // Visualização de Estoque para Vendedores (somente leitura)
    // Esta rota permite que vendedores vejam o estoque sem precisar do plano de estoque completo
    Route::get('/stocks-view', [\App\Http\Controllers\StockController::class, 'indexReadOnly'])->name('stocks.view');

    // Estoque (apenas planos Pro e Premium)
    Route::prefix('stocks')->name('stocks.')->middleware('plan:stock')->group(function () {
        // Dashboard & History (Must be before {id} routes)
        Route::get('/dashboard', [\App\Http\Controllers\StockDashboardController::class, 'index'])->name('dashboard');
        Route::get('/history', [\App\Http\Controllers\StockHistoryController::class, 'index'])->name('history');
        Route::get('/details', [\App\Http\Controllers\StockController::class, 'getStockDetails'])->name('details');
        Route::get('/check-availability', [\App\Http\Controllers\StockController::class, 'checkAvailability'])->name('check-availability');

        Route::get('/', [\App\Http\Controllers\StockController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\StockController::class, 'create'])->name('create');
        Route::get('/check', [\App\Http\Controllers\StockController::class, 'check'])->name('check');
        Route::get('/edit', [\App\Http\Controllers\StockController::class, 'edit'])->name('edit');
        Route::put('/update-group', [\App\Http\Controllers\StockController::class, 'updateGroup'])->name('update-group');
        Route::post('/reserve', [\App\Http\Controllers\StockController::class, 'reserve'])->name('reserve');
        Route::post('/release', [\App\Http\Controllers\StockController::class, 'release'])->name('release');
        Route::post('/', [\App\Http\Controllers\StockController::class, 'store'])->name('store');
        Route::get('/movements/{id}/print', [\App\Http\Controllers\StockController::class, 'printMovement'])->name('movements.print');
        Route::get('/{id}', [\App\Http\Controllers\StockController::class, 'show'])->name('show');
        Route::post('/{id}/transfer', [\App\Http\Controllers\StockController::class, 'transfer'])->name('transfer');
        Route::post('/{id}/write-off', [\App\Http\Controllers\StockController::class, 'writeOff'])->name('write-off');
        Route::put('/{id}', [\App\Http\Controllers\StockController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\StockController::class, 'destroy'])->name('destroy');
    });

    // Solicitações de Estoque (apenas planos Pro e Premium)
    Route::prefix('stock-requests')->name('stock-requests.')->middleware('plan:stock')->group(function () {
        Route::get('/', [\App\Http\Controllers\StockRequestController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\StockRequestController::class, 'store'])->name('store');
        Route::get('/{id}/receipt', [\App\Http\Controllers\StockRequestController::class, 'generateReceipt'])->name('receipt');
        Route::post('/{id}/approve', [\App\Http\Controllers\StockRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\StockRequestController::class, 'reject'])->name('reject');
        Route::post('/{id}/complete', [\App\Http\Controllers\StockRequestController::class, 'complete'])->name('complete');
    });

    // Histórico de Vendas
    Route::prefix('sales-history')->name('sales-history.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SalesHistoryController::class, 'index'])->name('index');
    });

    // Histórico de Estoque (apenas planos Pro e Premium)
    Route::prefix('stock-history')->name('stock-history.')->middleware('plan:stock')->group(function () {
        Route::get('/', [\App\Http\Controllers\StockHistoryController::class, 'index'])->name('index');
    });

    // Dashboard de Produção
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ProductionDashboardController::class, 'index'])->name('dashboard');
    });

    // Solicitações de Antecipação de Entrega
    Route::post('/delivery-requests', [\App\Http\Controllers\DeliveryRequestController::class, 'store']);
    Route::get('/delivery-requests', [\App\Http\Controllers\DeliveryRequestController::class, 'index'])->name('delivery-requests.index');
    Route::post('/delivery-requests/{deliveryRequest}/approve', [\App\Http\Controllers\DeliveryRequestController::class, 'approve'])->name('delivery-requests.approve');
    Route::post('/delivery-requests/{deliveryRequest}/reject', [\App\Http\Controllers\DeliveryRequestController::class, 'reject'])->name('delivery-requests.reject');

    // Termos e Condições (apenas para administradores)
    Route::resource('admin/terms-conditions', \App\Http\Controllers\Admin\TermsConditionController::class)
        ->names('admin.terms-conditions');
    Route::post('/admin/terms-conditions/{termsCondition}/activate', [\App\Http\Controllers\Admin\TermsConditionController::class, 'activate'])
        ->name('admin.terms-conditions.activate');

    // Lojas (apenas para administradores)
    Route::resource('admin/stores', \App\Http\Controllers\Admin\StoreController::class)
        ->names('admin.stores');
    Route::post('/admin/stores/{store}/assign-admin', [\App\Http\Controllers\Admin\StoreController::class, 'assignAdmin'])
        ->name('admin.stores.assign-admin');
    Route::delete('/admin/stores/{store}/remove-admin/{user}', [\App\Http\Controllers\Admin\StoreController::class, 'removeAdmin'])
        ->name('admin.stores.remove-admin');

    // Solicitações de Edição (apenas para administradores)
    Route::get('/admin/edit-requests', [\App\Http\Controllers\OrderEditRequestController::class, 'index'])
        ->name('admin.edit-requests.index');
    Route::get('/admin/edit-requests/{editRequest}/changes', [\App\Http\Controllers\OrderEditRequestController::class, 'showChanges'])
        ->name('admin.edit-requests.changes');
    Route::post('/admin/edit-requests/{editRequest}/approve', [\App\Http\Controllers\OrderEditRequestController::class, 'approve'])
        ->name('admin.edit-requests.approve');
    Route::post('/admin/edit-requests/{editRequest}/reject', [\App\Http\Controllers\OrderEditRequestController::class, 'reject'])
        ->name('admin.edit-requests.reject');
    
    // Solicitações de Cancelamento (apenas para administradores)
    Route::get('/admin/cancellations', [\App\Http\Controllers\OrderCancellationController::class, 'index'])
        ->name('admin.cancellations.index');
    Route::post('/admin/cancellations/{cancellation}/approve', [\App\Http\Controllers\OrderCancellationController::class, 'approve'])
        ->name('admin.cancellations.approve');
    Route::post('/admin/cancellations/{cancellation}/reject', [\App\Http\Controllers\OrderCancellationController::class, 'reject'])
        ->name('admin.cancellations.reject');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Subscription Management (for tenant users)
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
        Route::post('/upgrade/{plan}', [\App\Http\Controllers\SubscriptionController::class, 'requestUpgrade'])->name('upgrade');
        Route::post('/trial/{plan}', [\App\Http\Controllers\SubscriptionController::class, 'requestTrial'])->name('trial');
        Route::post('/renew', [\App\Http\Controllers\SubscriptionController::class, 'renewRequest'])->name('renew');
        Route::post('/validate-coupon', [\App\Http\Controllers\SubscriptionController::class, 'validateCoupon'])->name('validate-coupon');

        // Stripe Checkout
        Route::get('/checkout/{plan}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/create-intent/{plan}', [\App\Http\Controllers\PaymentController::class, 'createIntent'])->name('create-intent');
        Route::get('/return', [\App\Http\Controllers\PaymentController::class, 'handleReturn'])->name('return');
    });

    // Portal do Afiliado
    Route::prefix('afiliado')->name('affiliate.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AffiliatePortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/indicados', [\App\Http\Controllers\AffiliatePortalController::class, 'referrals'])->name('referrals');
    });
});

// Stripe Webhook (must be outside auth and CSRF protected group) - handling CSRF exclusion in bootstrap/app.php
Route::post('/stripe/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('stripe.webhook');

// Admin (apenas para administradores)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard principal
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // AJAX Routes for Product Options - MUST be before resource
    Route::post('product-options/reorder', [\App\Http\Controllers\Admin\ProductOptionController::class, 'reorder'])->name('product-options.reorder');
    Route::post('product-options/{option}/toggle-status', [\App\Http\Controllers\Admin\ProductOptionController::class, 'toggleStatus'])->name('product-options.toggle-status');
    
    Route::resource('product-options', \App\Http\Controllers\Admin\ProductOptionController::class);
    Route::resource('catalog-items', \App\Http\Controllers\Admin\CatalogItemController::class)->names('catalog-items');
    Route::resource('catalog-categories', \App\Http\Controllers\Admin\CatalogCategoryController::class)->names('catalog-categories');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Preços de Sublimação (legado)
    Route::get('sublimation-prices', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'index'])->name('sublimation-prices.index');
    Route::get('sublimation-prices/{size}/edit', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'edit'])->name('sublimation-prices.edit');
    Route::put('sublimation-prices/{size}', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'update'])->name('sublimation-prices.update');
    Route::get('sublimation-prices/add-row', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'addPriceRow'])->name('sublimation-prices.add-row');
    
    // Preços de Personalização (novo sistema unificado)
    Route::get('personalization-prices', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'index'])->name('personalization-prices.index');
    Route::get('personalization-prices/{type}/edit', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'edit'])->name('personalization-prices.edit');
    Route::put('personalization-prices/{type}', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'update'])->name('personalization-prices.update');
    Route::get('personalization-prices/add-row', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'addPriceRow'])->name('personalization-prices.add-row');
    Route::get('personalization-prices/sizes', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'getSizesForType'])->name('personalization-prices.sizes');
    Route::post('personalization-prices/locations', [\App\Http\Controllers\Admin\SublimationLocationController::class, 'store'])->name('personalization-prices.locations.store');
    Route::delete('personalization-prices/locations/{location}', [\App\Http\Controllers\Admin\SublimationLocationController::class, 'destroy'])->name('personalization-prices.locations.destroy');
    Route::patch('personalization-prices/locations/{location}/toggle', [\App\Http\Controllers\Admin\SublimationLocationController::class, 'toggle'])->name('personalization-prices.locations.toggle');
    Route::patch('personalization-prices/locations/{location}/toggle-pdf', [\App\Http\Controllers\Admin\SublimationLocationController::class, 'togglePdf'])->name('personalization-prices.locations.toggle-pdf');
    
    // Configurações de Personalização (novo sistema)
    Route::get('personalization-settings/{type}', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'edit'])->name('personalization-settings.edit');
    Route::put('personalization-settings/{type}', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'update'])->name('personalization-settings.update');
    
    // Opções Especiais de Personalização
    Route::post('personalization-settings/{type}/special-options', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'storeSpecialOption'])->name('personalization-settings.special-options.store');
    Route::put('personalization-settings/{type}/special-options/{option}', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'updateSpecialOption'])->name('personalization-settings.special-options.update');
    Route::delete('personalization-settings/{type}/special-options/{option}', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'destroySpecialOption'])->name('personalization-settings.special-options.destroy');
    Route::patch('personalization-settings/{type}/special-options/{option}/toggle', [\App\Http\Controllers\Admin\PersonalizationSettingController::class, 'toggleSpecialOption'])->name('personalization-settings.special-options.toggle');
    
    // Configurações da Empresa
    Route::get('company-settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('company.settings');
    Route::put('company-settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('company.settings.update');
    Route::delete('company-settings/logo', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteLogo'])->name('company.settings.deleteLogo');

    // Configurações de Observações do Orçamento
    Route::get('budget-settings', [\App\Http\Controllers\Admin\BudgetSettingsController::class, 'edit'])->name('budget-settings.edit');
    Route::put('budget-settings', [\App\Http\Controllers\Admin\BudgetSettingsController::class, 'update'])->name('budget-settings.update');
    
    // Deletar logo de uma loja específica
    Route::delete('/stores/{store}/logo', [\App\Http\Controllers\Admin\StoreController::class, 'deleteLogo'])->name('stores.deleteLogo');
    
    // Gerenciamento do Catálogo
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('categories');
    Route::resource('subcategories', \App\Http\Controllers\Admin\SubcategoryController::class)->names('subcategories');
    Route::resource('tecidos', \App\Http\Controllers\Admin\TecidoController::class)->names('tecidos');
    Route::resource('personalizacoes', \App\Http\Controllers\Admin\PersonalizacaoController::class)->names('personalizacoes');
    Route::resource('modelos', \App\Http\Controllers\Admin\ModeloController::class)->names('modelos');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('products');
    Route::delete('products/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('products/images/{image}/set-primary', [\App\Http\Controllers\Admin\ProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');
    
    // Cadastro Rápido de Produtos
    Route::get('quick-products', [\App\Http\Controllers\Admin\QuickProductController::class, 'index'])->name('quick-products.index');
    Route::post('quick-products/fabric', [\App\Http\Controllers\Admin\QuickProductController::class, 'storeFabric'])->name('quick-products.fabric.store');
    Route::post('quick-products/product', [\App\Http\Controllers\Admin\QuickProductController::class, 'storeProduct'])->name('quick-products.product.store');

    // Gestão de Planos (Super Admin apenas)
    Route::middleware('superadmin')->group(function () {
        Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class);
        
        // Gestão de Assinaturas (Tenants)
        Route::resource('tenants', \App\Http\Controllers\Admin\TenantController::class);
        Route::post('tenants/{tenant}/resend-access', [\App\Http\Controllers\Admin\TenantController::class, 'resendAccess'])->name('tenants.resend-access');
        Route::get('subscription-payments', [\App\Http\Controllers\Admin\SubscriptionPaymentController::class, 'index'])->name('subscription-payments.index');
        
        // Contexto de Tenant para Super Admin
        Route::post('set-tenant-context', [\App\Http\Controllers\Admin\TenantContextController::class, 'setContext'])->name('tenants.set-context');
        
        // Afiliados
        Route::prefix('affiliates')->name('affiliates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AffiliateController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\AffiliateController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\AffiliateController::class, 'store'])->name('store');
            Route::get('/{affiliate}', [\App\Http\Controllers\AffiliateController::class, 'show'])->name('show');
            Route::get('/{affiliate}/edit', [\App\Http\Controllers\AffiliateController::class, 'edit'])->name('edit');
            Route::put('/{affiliate}', [\App\Http\Controllers\AffiliateController::class, 'update'])->name('update');
            Route::get('/{affiliate}/commissions', [\App\Http\Controllers\AffiliateController::class, 'commissions'])->name('commissions');
            Route::delete('/{affiliate}', [\App\Http\Controllers\AffiliateController::class, 'destroy'])->name('destroy');
        });
        
        // Ações de Comissões de Afiliados
        Route::post('/commissions/{commission}/approve', [\App\Http\Controllers\AffiliateController::class, 'approveCommission'])->name('affiliates.commissions.approve');
        Route::post('/commissions/{commission}/pay', [\App\Http\Controllers\AffiliateController::class, 'payCommission'])->name('affiliates.commissions.pay');
        Route::post('/commissions/{commission}/cancel', [\App\Http\Controllers\AffiliateController::class, 'cancelCommission'])->name('affiliates.commissions.cancel');
    });

    // Configuração de Orçamento Online (Planos Pro/Premium)
    Route::get('quote-settings', [\App\Http\Controllers\Admin\QuoteSettingsController::class, 'index'])->name('quote-settings.index')->middleware('plan:external_quote');
    Route::post('quote-settings', [\App\Http\Controllers\Admin\QuoteSettingsController::class, 'update'])->name('quote-settings.update')->middleware('plan:external_quote');
    
    // SUB. TOTAL - Sistema de Preços por Tipo
    Route::prefix('sublimation-products')->name('sublimation-products.')->middleware('plan:sublimation_total')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SublimationProductController::class, 'index'])->name('index');
        
        // Gerenciamento de tipos
        Route::post('/types', [\App\Http\Controllers\Admin\SublimationProductController::class, 'storeType'])->name('types.store');
        Route::delete('/types/{type}', [\App\Http\Controllers\Admin\SublimationProductController::class, 'destroyType'])->name('types.destroy');
        
        // Editar preços por tipo (camisa, bandeira, conjunto, etc.)
        Route::get('/type/{type}', [\App\Http\Controllers\Admin\SublimationProductController::class, 'editType'])->name('edit-type');
        Route::put('/type/{type}', [\App\Http\Controllers\Admin\SublimationProductController::class, 'updateType'])->name('update-type');
        
        // Adicionais por tipo
        Route::post('/type/{type}/addons', [\App\Http\Controllers\Admin\SublimationProductController::class, 'storeAddon'])->name('addons.store');
        Route::delete('/addons/{addon}', [\App\Http\Controllers\Admin\SublimationProductController::class, 'destroyAddon'])->name('addons.destroy');
        
        // Toggle habilitar/desabilitar SUB. TOTAL
        Route::post('/toggle-enabled', [\App\Http\Controllers\Admin\SublimationProductController::class, 'toggleEnabled'])->name('toggle-enabled');
    });

    // Produtos Sublimação Local
    Route::resource('sub-local-products', \App\Http\Controllers\Admin\SubLocalProductController::class);
    Route::post('sub-local-products/{subLocalProduct}/addons', [\App\Http\Controllers\Admin\SubLocalProductController::class, 'storeAddon'])->name('sub-local-products.addons.store');
    Route::delete('sub-local-products/{subLocalProduct}/addons/{addon}', [\App\Http\Controllers\Admin\SubLocalProductController::class, 'destroyAddon'])->name('sub-local-products.addons.destroy');
});

// Peças de Tecido (dentro do middleware auth)
Route::middleware('auth')->prefix('fabric-pieces')->name('fabric-pieces.')->group(function () {
    Route::get('/', [\App\Http\Controllers\FabricPieceController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\FabricPieceController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\FabricPieceController::class, 'store'])->name('store');
    Route::post('/import', [\App\Http\Controllers\FabricPieceController::class, 'import'])->name('import');
    Route::get('/{id}/edit', [\App\Http\Controllers\FabricPieceController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\FabricPieceController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\FabricPieceController::class, 'destroy'])->name('destroy');
    
    // Ações especiais
    Route::post('/{id}/open', [\App\Http\Controllers\FabricPieceController::class, 'open'])->name('open');
    Route::post('/{id}/sell', [\App\Http\Controllers\FabricPieceController::class, 'sell'])->name('sell');
    Route::post('/{id}/sell-partial', [\App\Http\Controllers\FabricPieceController::class, 'sellPartial'])->name('sell-partial');
    Route::post('/{id}/transfer', [\App\Http\Controllers\FabricPieceController::class, 'transfer'])->name('transfer');
    Route::post('/transfers/{transferId}/receive', [\App\Http\Controllers\FabricPieceController::class, 'receiveTransfer'])->name('transfers.receive');
    Route::post('/transfers/{transferId}/cancel', [\App\Http\Controllers\FabricPieceController::class, 'cancelTransfer'])->name('transfers.cancel');
    Route::get('/transfers/{transferId}/print', [\App\Http\Controllers\FabricPieceController::class, 'printTransfer'])->name('transfers.print');
    
    // Relatório
    Route::get('/report', [\App\Http\Controllers\FabricPieceController::class, 'report'])->name('report');
});

// Máquinas de Costura
Route::middleware('auth')->prefix('sewing-machines')->name('sewing-machines.')->group(function () {
    Route::resource('/', \App\Http\Controllers\SewingMachineController::class)->parameters(['' => 'sewingMachine']);
});

// Estoque de Produção (Aviamentos/Tintas)
Route::middleware('auth')->prefix('production-supplies')->name('production-supplies.')->group(function () {
    Route::resource('/', \App\Http\Controllers\ProductionSupplyController::class)->parameters(['' => 'productionSupply']);
});

// Uniformes e EPIs
Route::middleware('auth')->prefix('uniforms')->name('uniforms.')->group(function () {
    Route::resource('/', \App\Http\Controllers\UniformController::class)->parameters(['' => 'uniform']);
});




// ========================================
// ASSINATURAS - Seleção de Método e Checkout
// ========================================
Route::middleware('auth')->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/select-payment/{plan}', function(\App\Models\Plan $plan) {
        return view('subscription.payment-method-selection', compact('plan'));
    })->name('select-payment');
    
    // Stripe Checkout
    Route::get('/checkout/{plan}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/create-intent/{plan}', [\App\Http\Controllers\PaymentController::class, 'createIntent'])->name('create-intent');
    Route::get('/return', [\App\Http\Controllers\PaymentController::class, 'return'])->name('return');
});

// Webhook do Stripe (fora do CSRF)
Route::post('/stripe/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('stripe.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ========================================
// MERCADO PAGO - Pagamentos com PIX
// ========================================
Route::middleware('auth')->prefix('mercadopago')->name('mercadopago.')->group(function () {
    Route::get('/checkout/{plan}', [\App\Http\Controllers\MercadoPagoController::class, 'checkout'])->name('checkout');
    Route::post('/create-preference/{plan}', [\App\Http\Controllers\MercadoPagoController::class, 'createPreference'])->name('create-preference');
    Route::post('/pix/{plan}', [\App\Http\Controllers\MercadoPagoController::class, 'generatePixPayment'])->name('pix');
    Route::get('/success', [\App\Http\Controllers\MercadoPagoController::class, 'success'])->name('success');
    Route::get('/pending', [\App\Http\Controllers\MercadoPagoController::class, 'pending'])->name('pending');
    Route::get('/failure', [\App\Http\Controllers\MercadoPagoController::class, 'failure'])->name('failure');
});

// Webhook do Mercado Pago (fora do CSRF)
Route::post('/mercadopago/webhook', [\App\Http\Controllers\MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';

Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Teste de envio de email Titan Mail para Hotmail - Vestalize', function ($message) {
            $message->to('hiwry-keveny2013@hotmail.com')
                   ->subject('Teste de Conexão SMTP Externo');
        });
        return 'Email enviado com sucesso para hiwry-keveny2013@hotmail.com! Verifique sua caixa de entrada e spam.';
    } catch (\Exception $e) {
        return 'Erro ao enviar email: ' . $e->getMessage();
    }
});

