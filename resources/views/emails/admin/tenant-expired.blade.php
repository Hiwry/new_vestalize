<x-mail::message>
# ⚠️ Atenção, Admin!

O período de **{{ $type }}** da empresa **{{ $tenant->name }}** venceu hoje (ou está vencendo).

**Detalhes da Conta:**
- **Empresa:** {{ $tenant->name }}
- **E-mail de Contato:** {{ $tenant->email }}
- **Plano:** {{ $tenant->plan->name ?? 'N/A' }}
- **Vencimento:** {{ ($type == 'trial' ? $tenant->trial_ends_at : $tenant->subscription_ends_at)->format('d/m/Y') }}

Esta é uma excelente oportunidade para uma **rechamada**! Entre em contato para entender se precisam de ajuda ou para negociar a renovação.

<x-mail::button :url="config('app.url') . '/admin/tenants'">
Gerenciar no Painel
</x-mail::button>

O cliente continuará aparecendo como "Inadimplente" no seu painel até que a situação seja regularizada ou a conta suspensa.

Atenciosamente,<br>
Sistema {{ config('app.name') }}
</x-mail::message>
