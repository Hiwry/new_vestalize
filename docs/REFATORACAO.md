---
description: Como consolidar migrations de forma segura (schema:dump)
---

# Consolidação de Migrations

## ⚠️ IMPORTANTE: Leia antes de executar

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

## Arquivos Criados Nesta Refatoração

### Services
- `app/Services/OrderWizardService.php` - Lógica extraída do controller

### Form Requests
- `app/Http/Requests/StoreClientRequest.php` - Validação de cliente
- `app/Http/Requests/AddOrderItemRequest.php` - Validação de itens
- `app/Http/Requests/FinalizeOrderRequest.php` - Validação de finalização

### Como Usar os Form Requests

```php
// Antes (no controller):
public function storeClient(Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        // ... 10+ linhas de validação
    ]);
}

// Depois:
use App\Http\Requests\StoreClientRequest;

public function storeClient(StoreClientRequest $request) {
    // Validação já aconteceu automaticamente
    $validated = $request->validated();
}
```

### Como Usar o Service

```php
// Antes (no controller):
public function storeClient(Request $request) {
    // 100+ linhas de lógica
}

// Depois:
use App\Services\OrderWizardService;

public function __construct(private OrderWizardService $service) {}

public function storeClient(StoreClientRequest $request) {
    $order = $this->service->storeClientAndStartOrder($request->validated());
    return redirect()->route('orders.wizard.type');
}
```

## Próximos Passos

1. [ ] Aplicar Form Requests nos controllers existentes gradualmente
2. [ ] Mover mais lógica para Services
3. [ ] Criar testes para os Services
4. [ ] Documentar APIs internas
