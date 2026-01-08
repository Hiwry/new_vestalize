<x-mail::message>
# 🎉 Bem-vindo ao Vestalize!

Olá **{{ $tenant->name }}**,

É um prazer ter você conosco! Sua conta foi criada com sucesso e você já pode começar a usar nosso sistema.

---

## 🔐 Suas Credenciais de Acesso

<x-mail::table>
| Campo | Valor |
|:------|:------|
| **Email** | {{ $user->email }} |
| **Senha** | `{{ $password }}` |
| **Código da Loja** | `{{ $tenant->store_code }}` |
</x-mail::table>

---

<x-mail::button :url="config('app.url') . '/login'" color="primary">
🚀 Acessar o Sistema
</x-mail::button>

---

## ⚠️ Importante

- **Altere sua senha** no primeiro acesso para garantir a segurança da sua conta.
- Guarde o **Código da Loja** — ele identifica sua empresa no sistema.

---

Ficou com alguma dúvida? Estamos aqui para ajudar! Basta responder este email.

Com carinho,<br>
**Equipe {{ config('app.name') }}**

<x-mail::subcopy>
Este é um email automático. Por favor, não responda diretamente a este endereço.
Acesse: [{{ config('app.url') }}]({{ config('app.url') }})
</x-mail::subcopy>
</x-mail::message>
