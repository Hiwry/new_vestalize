<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDesignRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $serviceTitle,
        public string $buyerName,
        public string $orderId,
        public ?string $instructions = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎨 Novo pedido de arte para você!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você recebeu um novo pedido de serviço:')
            ->line('**Serviço:** ' . $this->serviceTitle)
            ->line('**Cliente:** ' . $this->buyerName)
            ->when($this->instructions, fn($mail) => $mail->line('**Instruções:** ' . $this->instructions))
            ->action('Ver pedido', url('/marketplace/orders/' . $this->orderId))
            ->line('Acesse sua conta para confirmar e iniciar o trabalho.')
            ->salutation('Equipe Vestalize Marketplace');
    }
}
