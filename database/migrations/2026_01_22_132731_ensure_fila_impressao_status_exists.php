<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Status;
use App\Models\Tenant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            Status::firstOrCreate(
                [
                    'name' => 'Fila de Impressão',
                    'tenant_id' => $tenant->id
                ],
                [
                    'color' => '#6366F1',
                    'position' => 2,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to delete statuses on rollback as they might be in use
    }
};
