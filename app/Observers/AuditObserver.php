<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity('created', $model, "Novo registro criado em " . class_basename($model));
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();
        
        if (count($dirty) > 0) {
            $changes = [];
            foreach ($dirty as $key => $value) {
                if ($key === 'updated_at') continue;
                
                $oldValue = $model->getOriginal($key);
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $value
                ];
            }

            if (count($changes) > 0) {
                $description = class_basename($model) . " #{$model->id} atualizado.";
                $this->logActivity('updated', $model, $description, $changes);
            }
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logActivity('deleted', $model, class_basename($model) . " #{$model->id} excluído.");
    }

    /**
     * Helper para registrar atividade no banco e arquivo
     */
    protected function logActivity($event, Model $model, $description, $properties = null)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? (method_exists($model, 'tenant_id') ? $model->tenant_id : null);

        // 1. Salvar no Banco de Dados (Principal)
        ActivityLog::create([
            'log_name' => 'audit',
            'description' => $description,
            'subject_id' => $model->id,
            'subject_type' => get_class($model),
            'causer_id' => $user ? $user->id : null,
            'causer_type' => $user ? get_class($user) : null,
            'properties' => $properties,
            'event' => $event,
            'tenant_id' => $tenantId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // 2. Backup em arquivo (Canal Audit)
        Log::channel('audit')->info("AUDIT: [{$event}] em " . class_basename($model) . " #{$model->id}", [
            'causer' => $user ? $user->name : 'Sistema',
            'description' => $description,
            'properties' => $properties
        ]);
    }
}
