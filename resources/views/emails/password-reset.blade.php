@component('mail::message')
# Recuperação de Senha

Olá {{ $user->name }},

Recebemos uma solicitação de recuperação de senha para sua conta.

## Suas Novas Credenciais

**Email:** {{ $user->email }}

@if($storeCode)
**Código da Loja:** {{ $storeCode }}
@endif

**Nova Senha:** {{ $newPassword }}

@component('mail::button', ['url' => route('login')])
Acessar Sistema
@endcomponent

**Importante:** Por questões de segurança, recomendamos que você altere esta senha assim que fizer login.

Se você não solicitou esta recuperação de senha, entre em contato conosco imediatamente.

Atenciosamente,<br>
{{ config('app.name') }}
@endcomponent
