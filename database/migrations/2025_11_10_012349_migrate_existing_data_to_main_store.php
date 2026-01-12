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
        // Criar loja principal
        $mainStoreId = DB::table('stores')->insertGetId([
            'name' => 'Loja Principal',
            'parent_id' => null,
            'is_main' => true,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Migrar dados existentes para a loja principal
        // Orders
        DB::table('orders')->whereNull('store_id')->update(['store_id' => $mainStoreId]);
        
        // Budgets
        DB::table('budgets')->whereNull('store_id')->update(['store_id' => $mainStoreId]);
        
        // Clients
        DB::table('clients')->whereNull('store_id')->update(['store_id' => $mainStoreId]);
        
        // Cash Transactions
        DB::table('cash_transactions')->whereNull('store_id')->update(['store_id' => $mainStoreId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover loja principal e limpar store_id dos dados
        $mainStore = DB::table('stores')->where('is_main', true)->first();
        
        if ($mainStore) {
            // Limpar store_id dos dados
            DB::table('orders')->where('store_id', $mainStore->id)->update(['store_id' => null]);
            DB::table('budgets')->where('store_id', $mainStore->id)->update(['store_id' => null]);
            DB::table('clients')->where('store_id', $mainStore->id)->update(['store_id' => null]);
            DB::table('cash_transactions')->where('store_id', $mainStore->id)->update(['store_id' => null]);
            
            // Deletar loja principal
            DB::table('stores')->where('id', $mainStore->id)->delete();
        }
    }
};
