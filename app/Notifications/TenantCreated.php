<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Tenant $tenant,
        public string $password = ''
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $planNames = [
            'basic' => 'Básico (R$ 200/mês)',
            'pro' => 'Pro (R$ 300/mês)',
            'premium' => 'Premium (R$ 500/mês)',
        ];

        $planName = $planNames[$this->tenant->plan] ?? 'Básico';

        $message = (new MailMessage)
            ->subject('Bem-vindo ao Vestalize! 🎉')
            ->greeting('Olá, ' . $this->tenant->name . '!')
            ->line('Sua conta foi criada com sucesso.')
            ->line('')
            ->line('**Seu código de acesso:**')
            ->line('# ' . $this->tenant->store_code)
            ->line('')
            ->line('**Seu plano:** ' . $planName)
            ->line('')
            ->line('Use este código junto com seu email e senha para fazer login no sistema.')
            ->action('Acessar o Sistema', url('/login'))
            ->line('')
            ->line('**Próximos passos:**')
            ->line('1. Acesse a página de login')
            ->line('2. Digite o código acima')
            ->line('3. Informe seu email e senha')
            ->line('4. Comece a usar o sistema!');

        if ($this->password) {
            $message->line('')
                    ->line('**Sua senha temporária:** ' . $this->password)
                    ->line('Recomendamos alterar sua senha no primeiro acesso.');
        }

        return $message
            ->salutation('Equipe Vestalize');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'store_code' => $this->tenant->store_code,
        ];
    }
}
