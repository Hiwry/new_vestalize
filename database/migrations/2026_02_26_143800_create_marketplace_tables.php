<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Designer Profiles ─────────────────────────────────────
        Schema::create('marketplace_designer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->json('specialties')->nullable();
            $table->string('instagram')->nullable();
            $table->string('behance')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('status')->default('pending'); // pending, active, rejected, suspended
            $table->unsignedTinyInteger('commission_rate')->default(80); // % que o designer recebe
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('slug');
        });

        // ─── Services ──────────────────────────────────────────────
        Schema::create('marketplace_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designer_profile_id')->constrained('marketplace_designer_profiles')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->unsignedInteger('price_credits');
            $table->unsignedSmallInteger('delivery_days')->default(3);
            $table->text('requirements')->nullable();
            $table->unsignedTinyInteger('revisions')->default(2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
            $table->index('category');
        });

        // ─── Service Images ────────────────────────────────────────
        Schema::create('marketplace_service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_service_id')->constrained('marketplace_services')->onDelete('cascade');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ─── Tools (Digital Products) ──────────────────────────────
        Schema::create('marketplace_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->unsignedInteger('price_credits');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('total_downloads')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
            $table->index('category');
        });

        // ─── Tool Images ───────────────────────────────────────────
        Schema::create('marketplace_tool_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_tool_id')->constrained('marketplace_tools')->onDelete('cascade');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ─── Orders ────────────────────────────────────────────────
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->string('orderable_type'); // 'service' or 'tool'
            $table->unsignedBigInteger('orderable_id');
            $table->unsignedBigInteger('designer_id')->nullable();
            $table->unsignedInteger('price_credits');
            $table->unsignedInteger('credits_to_designer')->nullable();
            $table->string('status')->default('pending_payment');
            $table->text('buyer_instructions')->nullable();
            $table->text('delivery_message')->nullable();
            $table->string('delivery_file')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('designer_id')->references('id')->on('marketplace_designer_profiles')->onDelete('set null');
            $table->index(['buyer_id', 'status']);
            $table->index(['designer_id', 'status']);
            $table->index(['orderable_type', 'orderable_id']);
        });

        // ─── Reviews ───────────────────────────────────────────────
        Schema::create('marketplace_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->text('comment')->nullable();
            $table->text('designer_reply')->nullable();
            $table->timestamps();

            $table->unique('marketplace_order_id'); // uma avaliação por pedido
        });

        // ─── Credit Wallets ────────────────────────────────────────
        Schema::create('marketplace_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('balance')->default(0);
            $table->integer('total_purchased')->default(0);
            $table->integer('total_spent')->default(0);
            $table->timestamps();
        });

        // ─── Credit Transactions ───────────────────────────────────
        Schema::create('marketplace_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // purchase, spend, refund, earn, bonus
            $table->integer('amount'); // positivo = crédito, negativo = débito
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->string('description');
            $table->string('reference_type')->nullable(); // order, package, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('payment_method')->nullable(); // pix, stripe, etc.
            $table->string('payment_id')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->boolean('subscriber_discount_applied')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });

        // ─── Credit Packages ───────────────────────────────────────
        Schema::create('marketplace_credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('credits');
            $table->decimal('price', 10, 2);
            $table->decimal('subscriber_price', 10, 2);
            $table->string('badge')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_credit_packages');
        Schema::dropIfExists('marketplace_credit_transactions');
        Schema::dropIfExists('marketplace_credit_wallets');
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('marketplace_orders');
        Schema::dropIfExists('marketplace_tool_images');
        Schema::dropIfExists('marketplace_tools');
        Schema::dropIfExists('marketplace_service_images');
        Schema::dropIfExists('marketplace_services');
        Schema::dropIfExists('marketplace_designer_profiles');
    }
};
