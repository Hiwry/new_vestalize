<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar coluna tenant_id à tabela stores
        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
        });

        // Migrar dados existentes para um tenant padrão
        $storesCount = DB::table('stores')->count();
        
        if ($storesCount > 0) {
            // Criar tenant padrão
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'Tenant Padrão',
                'store_code' => strtoupper(Str::random(6)),
                'plan' => 'premium', // Dar acesso total aos dados existentes
                'status' => 'active',
                'subscription_ends_at' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Associar todas as lojas ao tenant padrão
            DB::table('stores')->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
