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
        // Modify column to include 'edicao'
        // Note: Using raw SQL because Doctrine DBAL interaction with ENUMs can be tricky
        // This syntax is for MySQL/MariaDB which is the stack here
        DB::statement("ALTER TABLE stock_history MODIFY COLUMN action_type ENUM('entrada', 'saida', 'reserva', 'liberacao', 'transferencia', 'ajuste', 'devolucao', 'perda', 'edicao') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list
        // Note: This might fail if there are records with 'edicao'
        DB::statement("ALTER TABLE stock_history MODIFY COLUMN action_type ENUM('entrada', 'saida', 'reserva', 'liberacao', 'transferencia', 'ajuste', 'devolucao', 'perda') NOT NULL");
    }
};
