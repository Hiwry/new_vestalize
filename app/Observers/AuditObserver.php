<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->logActivity('pedido_criado', $order, "Novo pedido/orçamento criado: #{$order->id}");
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $dirty = $order->getDirty();
        
        if (count($dirty) > 0) {
            $changes = [];
            foreach ($dirty as $key => $value) {
                // Ignorar timestamp updated_at
                if ($key === 'updated_at') continue;
                
                $oldValue = $order->getOriginal($key);
                $changes[] = "{$key}: {$oldValue} -> {$value}";
            }

            if (count($changes) > 0) {
                $description = "Pedido #{$order->id} atualizado: " . implode(', ', $changes);
                $this->logActivity('pedido_atualizado', $order, $description);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $this->logActivity('pedido_excluido', $order, "Pedido #{$order->id} excluído permanentemente.");
    }

    /**
     * Helper para registrar atividade
     */
    protected function logActivity($type, $model, $description)
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Sistema';
        $userId = $user ? $user->id : 0;

        // Log estruturado em arquivo
        Log::channel('audit')->info("AUDIT: [{$type}] por {$userName} ({$userId})", [
            'model' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Aqui poderíamos também salvar em uma tabela `activity_logs` no banco
    }
}
