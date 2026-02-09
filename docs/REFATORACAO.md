---
description: Como consolidar migrations de forma segura (schema:dump)
---

# Consolidação de Migrations

##  IMPORTANTE: Leia antes de executar

Este processo **NÃO DEVE** ser executado diretamente em produção. 
Ele requer um backup completo do banco de dados antes de qualquer ação.

## Pré-requisitos

1. Backup completo do banco de dados de produção
2. Ambiente de desenvolvimento limpo
3. Todas as migrations já aplicadas no banco atual

## Passos para Consolidação

### 1. Fazer Backup (OBRIGATÓRIO)

```bash
# No servidor de produção
mysqldump -u usuario -p nome_do_banco > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Gerar Schema Dump (Apenas em Desenvolvimento)

```bash
# Este comando gera um arquivo SQL com toda a estrutura atual do banco
php artisan schema:dump

# Isso cria: database/schema/mysql-schema.sql
```

### 3. Limpar Migrations Antigas (Opcional - CUIDADO)

```bash
# Remove todas as migrations antigas após o dump
php artisan schema:dump --prune
```

### 4. Testar em Ambiente Limpo

```bash
# Criar banco novo e testar
php artisan migrate:fresh --seed
```

## Alternativa Mais Segura: Agrupamento Manual

Em vez de usar `schema:dump`, você pode:

1. **Manter as migrations existentes** - Elas documentam a evolução do sistema
2. **Criar uma migration de consolidação** - Para novos deploys apenas
3. **Usar seeder para dados padrão** - Separar estrutura de dados

## Quando NÃO Consolidar

- Se o sistema já está em produção com dados reais
- Se você não tem backup testado e restaurável
- Se outras equipes dependem do histórico de migrations

---

# Arquivos Criados na Refatoração

## Services

| Arquivo | Descrição | Tamanho Original |
|---------|-----------|------------------|
| `app/Services/OrderWizardService.php` | Lógica do wizard de pedidos | 76KB → Service |
| `app/Services/PDVService.php` | Lógica do Ponto de Venda | 79KB → Service |
| `app/Services/EditOrderService.php` | Lógica de edição de pedidos | 83KB → Service |

## Form Requests

| Arquivo | Uso |
|---------|-----|
| `app/Http/Requests/StoreClientRequest.php` | Validação de cliente no wizard |
| `app/Http/Requests/AddOrderItemRequest.php` | Validação ao adicionar item |
| `app/Http/Requests/FinalizeOrderRequest.php` | Validação na finalização do pedido |
| `app/Http/Requests/AddToCartRequest.php` | Validação ao adicionar item ao carrinho PDV |
| `app/Http/Requests/PDVCheckoutRequest.php` | Validação do checkout PDV |
| `app/Http/Requests/EditOrderClientRequest.php` | Validação de cliente na edição |
| `app/Http/Requests/SavePersonalizationRequest.php` | Validação de personalização |
| `app/Http/Requests/EditOrderPaymentRequest.php` | Validação de pagamento na edição |

---

## Como Usar os Form Requests

```php
// ANTES (no controller):
public function addToCart(Request $request) {
    $validated = $request->validate([
        'product_id' => 'nullable|exists:products,id',
        'quantity' => 'required|numeric|min:0.01',
        // ... 15+ linhas de validação
    ]);
}

// DEPOIS:
use App\Http\Requests\AddToCartRequest;

public function addToCart(AddToCartRequest $request) {
    // Validação já aconteceu automaticamente
    // Preços com vírgula já foram convertidos para ponto
    $validated = $request->validated();
}
```

## Como Usar o PDVService

```php
// ANTES (no controller):
public function checkout(Request $request) {
    // 400+ linhas de lógica
}

// DEPOIS:
use App\Services\PDVService;
use App\Http\Requests\PDVCheckoutRequest;

public function __construct(private PDVService $service) {}

public function checkout(PDVCheckoutRequest $request) {
    try {
        $order = $this->service->processCheckout(
            $request->getCartData(),
            $request->getPaymentData()
        );
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'message' => 'Venda finalizada com sucesso!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
}
```

## Métodos Disponíveis no PDVService

| Método | Descrição |
|--------|-----------|
| `getCurrentStoreId()` | Retorna ID da loja atual do usuário |
| `getCart()` | Obtém carrinho da sessão |
| `saveCart(array $cart)` | Salva carrinho na sessão |
| `clearCart()` | Limpa carrinho |
| `calculateCartTotal(array $cart)` | Calcula total do carrinho |
| `createProductCartItem(Product, data)` | Cria item de produto |
| `createProductOptionCartItem(Option, data)` | Cria item de tipo de corte |
| `createFabricPieceCartItem(piece, data)` | Cria item de peça de tecido |
| `createMachineCartItem(machine, data)` | Cria item de máquina |
| `createSupplyCartItem(supply, data)` | Cria item de suprimento |
| `createUniformCartItem(uniform, data)` | Cria item de uniforme |
| `checkStockAndCreateRequest(...)` | Verifica estoque e cria solicitação |
| `processCheckout(cartData, paymentData)` | Processa checkout completo |
| `removeFromCart(itemId)` | Remove item do carrinho |
| `updateCartItem(itemId, updates)` | Atualiza item do carrinho |

## Métodos Disponíveis no OrderWizardService

| Método | Descrição |
|--------|-----------|
| `resolveStoreId()` | Resolve loja do usuário |
| `storeClientAndStartOrder(data)` | Cria cliente e inicia pedido |
| `addItemToOrder(data, imagePath)` | Adiciona item ao pedido |
| `recalculateOrderTotals(order)` | Recalcula totais do pedido |
| `processPayment(paymentData, order)` | Processa pagamento |
| `finalizeOrder(order, finalData)` | Finaliza o pedido |
| `deleteItem(itemId)` | Remove item do pedido |

## Métodos Disponíveis no EditOrderService

| Método | Descrição |
|--------|-----------|
| `startEdit(order)` | Inicia sessão de edição |
| `getEditData()` | Obtém dados da edição da sessão |
| `getEditOrderId()` | Obtém ID do pedido em edição |
| `hasActiveSession()` | Verifica se há sessão ativa |
| `updateClientData(data)` | Atualiza dados do cliente na sessão |
| `getProductOptionName(id, type)` | Converte ID para nome de opção |
| `getPersonalizationName(id)` | Converte ID para nome de personalização |
| `convertToName(value, type)` | Converte ID para nome |
| `createSnapshotBefore(order)` | Cria snapshot antes da edição |
| `createSnapshotAfter(order)` | Cria snapshot após a edição |
| `calculateEditDifferences(before, after)` | Calcula diferenças entre snapshots |
| `resolveStoreIdForEdit(order)` | Resolve loja para edição |
| `finalizeEdit(order, editData)` | Finaliza a edição do pedido |
| `savePersonalization(item, data)` | Salva personalização |
| `processPersonalizationFiles(pers, files)` | Processa arquivos de personalização |
| `updateItemTotalPrice(item)` | Atualiza preço total do item |
| `clearSession()` | Limpa sessão de edição |
| `canUserEdit(order)` | Verifica se usuário pode editar |
| `recalculateSubtotal(order)` | Recalcula subtotal do pedido |

---

## Resumo da Refatoração

### Controllers Refatorados

| Controller | Linhas | Service Criado |
|------------|--------|----------------|
| `OrderWizardController.php` | 1739 | `OrderWizardService.php` |
| `PDVController.php` | 1773 | `PDVService.php` |
| `EditOrderController.php` | 1749 | `EditOrderService.php` |

**Total: ~5.200 linhas de código movidas para Services**

### Form Requests Criados

- **8 Form Requests** com validação automática
- **Conversão automática** de vírgula para ponto em preços
- **Mensagens de erro** em português

---

## Próximos Passos

1. [x] ~~Refatorar OrderWizardController~~
2. [x] ~~Refatorar PDVController~~
3. [x] ~~Refatorar EditOrderController~~
4. [ ] Aplicar Form Requests nos controllers existentes gradualmente
5. [ ] Criar testes automatizados para os Services
6. [ ] Documentar APIs internas

