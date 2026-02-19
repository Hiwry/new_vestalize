<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');

            // Customer info (no login required)
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->string('customer_cpf')->nullable();

            // Order details
            $table->json('items'); // [{product_id, title, qty, size, color, unit_price, total}]
            $table->integer('total_items')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Pricing mode
            $table->enum('pricing_mode', ['varejo', 'atacado'])->default('varejo');

            // Payment
            $table->string('payment_method')->nullable(); // stripe, mercadopago_pix, etc.
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_gateway_id')->nullable(); // stripe session id, mp payment id
            $table->json('payment_data')->nullable(); // raw gateway response

            // Order status
            $table->enum('status', ['pending', 'approved', 'rejected', 'converted', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('order_code')->unique(); // public reference code

            // Delivery
            $table->text('delivery_address')->nullable();

            $table->timestamps();

            $table->index(['store_id', 'status']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_orders');
    }
};
