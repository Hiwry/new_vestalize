<x-mail::message>
#  Novo Cliente no Vestalize!

Olá, Admin. Uma nova empresa acaba de se cadastrar na plataforma através do onboarding automático.

**Detalhes da Empresa:**
- **Nome:** {{ $tenant->name }}
- **E-mail:** {{ $tenant->email }}
- **Plano Selecionado:** {{ $tenant->plan->name ?? 'N/A' }}
- **Data de Cadastro:** {{ $tenant->created_at->format('d/m/Y H:i') }}

**Dados do Administrador:**
- **Nome:** {{ $user->name }}
- **E-mail:** {{ $user->email }}

O período de **teste de 7 dias** foi ativado automaticamente para este cliente. 

<x-mail::button :url="config('app.url') . '/admin/tenants'">
Ver no Painel Admin
</x-mail::button>

Use estas informações para entrar em contato (rechamada) e oferecer suporte inicial ou fechar a venda definitiva!

Atenciosamente,<br>
Equipe {{ config('app.name') }}
</x-mail::message>
