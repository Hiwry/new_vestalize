<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $correctOrder = [
            ['name' => 'Quando não assina', 'color' => '#EF4444'],
            ['name' => 'Assinado', 'color' => '#22C55E'],
            ['name' => 'Fila de Impressão', 'color' => '#6366F1'],
            ['name' => 'Pendente', 'color' => '#F59E0B'],
            ['name' => 'Inicio', 'color' => '#F59E0B'],
            ['name' => 'Fila Corte', 'color' => '#6366F1'],
            ['name' => 'Cortado', 'color' => '#3B82F6'],
            ['name' => 'Costura', 'color' => '#8B5CF6'],
            ['name' => 'Costurar Novamente', 'color' => '#EC4899'],
            ['name' => 'Personalização', 'color' => '#10B981'],
            ['name' => 'Limpeza', 'color' => '#14B8A6'],
            ['name' => 'Concluído', 'color' => '#059669'],
            ['name' => 'Pronto', 'color' => '#10B981'],
            ['name' => 'Entregue', 'color' => '#059669'],
            ['name' => 'Cancelado', 'color' => '#EF4444'],
            ['name' => 'Aguardando Aprovação', 'color' => '#8B5CF6'],
        ];

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // 1. Limpeza: Mesclar status duplicados ou malformados
            $allTenantStatuses = Status::where('tenant_id', $tenant->id)->get();
            
            foreach ($correctOrder as $index => $item) {
                $targetName = $item['name'];
                $targetSlug = Str::slug($targetName);
                
                // Procurar por status que se assemelham ao alvo (pelo slug)
                $similarStatuses = $allTenantStatuses->filter(function($s) use ($targetSlug) {
                    return Str::slug($s->name) === $targetSlug || 
                           str_contains(Str::slug($s->name), $targetSlug) ||
                           str_contains($targetSlug, Str::slug($s->name));
                });

                if ($similarStatuses->isNotEmpty()) {
                    $primary = $similarStatuses->first();
                    
                    // Renomear o primeiro para o nome correto e atualizar cor/posição
                    $primary->update([
                        'name' => $targetName,
                        'color' => $item['color'],
                        'position' => $index + 1
                    ]);

                    // Se houver duplicatas "mutiladas", mover os pedidos e deletar
                    if ($similarStatuses->count() > 1) {
                        foreach ($similarStatuses->slice(1) as $duplicate) {
                            \App\Models\Order::where('status_id', $duplicate->id)->update(['status_id' => $primary->id]);
                            $duplicate->delete();
                        }
                    }
                } else {
                    // Se não existir nada parecido, criar do zero
                    Status::create([
                        'name' => $targetName,
                        'color' => $item['color'],
                        'tenant_id' => $tenant->id,
                        'position' => $index + 1
                    ]);
                }
            }
        }
        
        // Limpeza final: deletar qualquer status que sobrou e não está na lista correta
        $correctNames = array_column($correctOrder, 'name');
        Status::whereNotIn('name', $correctNames)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
