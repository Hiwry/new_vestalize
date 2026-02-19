<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('code', 8)->unique();
            $table->decimal('commission_rate', 5, 2)->default(10.00); // % de comissão padrão
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('bank_info')->nullable(); // Dados bancários para saque
            $table->decimal('total_earnings', 12, 2)->default(0); // Total ganho
            $table->decimal('pending_balance', 12, 2)->default(0); // Saldo pendente
            $table->decimal('withdrawn_balance', 12, 2)->default(0); // Total sacado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
