<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomear phone para phone_primary se existir
        if (Schema::hasColumn('clients', 'phone') && !Schema::hasColumn('clients', 'phone_primary')) {
            DB::statement('ALTER TABLE `clients` CHANGE `phone` `phone_primary` VARCHAR(255) NULL');
        }
        
        Schema::table('clients', function (Blueprint $table) {
            // Adicionar phone_primary se não existir
            if (!Schema::hasColumn('clients', 'phone_primary')) {
                $table->string('phone_primary')->nullable()->after('name');
            }
            
            // Adicionar phone_secondary se não existir
            if (!Schema::hasColumn('clients', 'phone_secondary')) {
                $table->string('phone_secondary')->nullable()->after('phone_primary');
            }
            
            // Adicionar cpf_cnpj se não existir
            if (!Schema::hasColumn('clients', 'cpf_cnpj')) {
                $table->string('cpf_cnpj', 20)->nullable()->after('email');
            }
            
            // Adicionar city se não existir
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            // Adicionar state se não existir
            if (!Schema::hasColumn('clients', 'state')) {
                $table->string('state', 2)->nullable()->after('city');
            }
            
            // Adicionar zip_code se não existir
            if (!Schema::hasColumn('clients', 'zip_code')) {
                $table->string('zip_code', 12)->nullable()->after('state');
            }
        });
        
        // Adicionar índices se não existirem (usando DB::statement para verificar)
        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('name', 'idx_clients_name');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('phone_primary', 'idx_clients_phone_primary');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('email', 'idx_clients_email');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            Schema::table('clients', function (Blueprint $table) {
                $table->index('cpf_cnpj', 'idx_clients_cpf_cnpj');
            });
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migration não deve ser revertida em produção
        // pois pode causar perda de dados
    }
};
