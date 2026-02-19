<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Budgets
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
                $table->string('budget_number')->unique();
                $table->string('order_number')->nullable();
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->date('valid_until');
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->string('discount_type')->nullable();
                $table->decimal('total', 10, 2)->default(0);
                $table->text('observations')->nullable();
                $table->text('admin_notes')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
                
                $table->index('store_id');
            });
        }

        // Budget Items
        if (!Schema::hasTable('budget_items')) {
            Schema::create('budget_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
                $table->integer('item_number');
                $table->string('fabric')->nullable();
                $table->string('fabric_type')->nullable();
                $table->string('color')->nullable();
                $table->integer('quantity');
                $table->string('personalization_types')->nullable();
                $table->decimal('item_total', 10, 2)->default(0);
                $table->string('cover_image')->nullable();
                $table->timestamps();
            });
        }

        // Budget Customizations
        if (!Schema::hasTable('budget_customizations')) {
            Schema::create('budget_customizations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_id')->nullable()->constrained('budgets')->onDelete('cascade');
                $table->foreignId('budget_item_id')->constrained('budget_items')->onDelete('cascade');
                $table->string('art_name')->nullable();
                $table->string('location')->nullable();
                $table->string('personalization_type');
                $table->string('size')->nullable();
                $table->integer('quantity');
                $table->integer('color_count')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->string('image')->nullable();
                $table->text('art_files')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Cash Transactions
        if (!Schema::hasTable('cash_transactions')) {
            Schema::create('cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
                $table->enum('type', ['entrada', 'saida']);
                $table->string('category');
                $table->text('description');
                $table->decimal('amount', 12, 2);
                $table->enum('payment_method', ['dinheiro','pix','cartao','transferencia','boleto','entrada_dinheiro','debito_conta','credito_conta','multiplo'])->default('dinheiro');
                $table->longText('payment_methods')->nullable();
                $table->enum('status', ['pendente', 'confirmado'])->default('pendente');
                $table->dateTime('transaction_date')->nullable();
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('user_name');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('user_id');
                $table->index('type');
                $table->index('status');
                $table->index('transaction_date');
                $table->index(['type', 'status']);
                $table->index('store_id');
            });
        }

        // Catalog Categories
        if (!Schema::hasTable('catalog_categories')) {
            Schema::create('catalog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // Catalog Items
        if (!Schema::hasTable('catalog_items')) {
            Schema::create('catalog_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('catalog_category_id')->constrained('catalog_categories')->onDelete('cascade');
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        // Categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Subcategories
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['category_id', 'active', 'order']);
            });
        }

        // Company Settings
        if (!Schema::hasTable('company_settings')) {
            Schema::create('company_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
                $table->string('company_name')->nullable();
                $table->text('company_address')->nullable();
                $table->string('company_city')->nullable();
                $table->string('company_state')->nullable();
                $table->string('company_zip')->nullable();
                $table->string('company_phone')->nullable();
                $table->string('company_email')->nullable();
                $table->string('company_website')->nullable();
                $table->string('company_cnpj')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_agency')->nullable();
                $table->string('bank_account')->nullable();
                $table->string('pix_key')->nullable();
                $table->text('footer_text')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->timestamps();
                
                $table->index('store_id');
            });
        }

        // Delivery Requests
        if (!Schema::hasTable('delivery_requests')) {
            Schema::create('delivery_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->date('current_delivery_date');
                $table->date('requested_delivery_date');
                $table->text('reason');
                $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
                $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('requested_by_name');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('reviewed_by_name')->nullable();
                $table->text('review_notes')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('status');
            });
        }

        // Notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('type');
                $table->string('title');
                $table->text('message');
                $table->string('link')->nullable();
                $table->longText('data')->nullable();
                $table->boolean('read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Order Cancellations
        if (!Schema::hasTable('order_cancellations')) {
            Schema::create('order_cancellations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('reason')->nullable();
                $table->text('admin_notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('status');
            });
        }

        // Order Comments
        if (!Schema::hasTable('order_comments')) {
            Schema::create('order_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('user_name');
                $table->text('comment');
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('user_id');
            });
        }

        // Order Edit History
        if (!Schema::hasTable('order_edit_history')) {
            Schema::create('order_edit_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('user_name');
                $table->string('action');
                $table->text('description');
                $table->longText('changes')->nullable();
                $table->timestamps();
            });
        }

        // Order Edit Requests
        if (!Schema::hasTable('order_edit_requests')) {
            Schema::create('order_edit_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->text('reason')->nullable();
                $table->longText('changes')->nullable();
                $table->longText('order_snapshot_before')->nullable();
                $table->longText('order_snapshot_after')->nullable();
                $table->text('admin_notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('user_id');
                $table->index('status');
            });
        }

        // Order Files
        if (!Schema::hasTable('order_files')) {
            Schema::create('order_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
                $table->string('file_name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();
                $table->timestamps();
            });
        }

        // Order Logs
        if (!Schema::hasTable('order_logs')) {
            Schema::create('order_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('user_name');
                $table->string('action');
                $table->text('description');
                $table->longText('old_value')->nullable();
                $table->longText('new_value')->nullable();
                $table->timestamps();
                
                $table->index('order_id');
                $table->index('user_id');
                $table->index('action');
            });
        }

        // Order Sublimations
        if (!Schema::hasTable('order_sublimations')) {
            Schema::create('order_sublimations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
                $table->string('application_type')->nullable();
                $table->string('art_name')->nullable();
                $table->foreignId('size_id')->nullable()->constrained('sublimation_sizes')->onDelete('set null');
                $table->string('size_name')->nullable();
                $table->foreignId('location_id')->nullable()->constrained('sublimation_locations')->onDelete('set null');
                $table->string('location_name')->nullable();
                $table->integer('quantity');
                $table->integer('color_count')->default(0);
                $table->boolean('has_neon')->default(false);
                $table->decimal('neon_surcharge', 10, 2)->default(0);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('final_price', 10, 2);
                $table->string('application_image')->nullable();
                $table->text('seller_notes')->nullable();
                $table->text('color_details')->nullable();
                $table->longText('addons')->nullable();
                $table->boolean('regata_discount')->default(false);
                $table->timestamps();
                
                $table->index('order_item_id');
                $table->index('size_id');
                $table->index('location_id');
            });
        }

        // Order Sublimation Files
        if (!Schema::hasTable('order_sublimation_files')) {
            Schema::create('order_sublimation_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_sublimation_id')->constrained('order_sublimations')->onDelete('cascade');
                $table->string('file_name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();
                $table->timestamps();
            });
        }

        // Tecidos (deve ser criado antes de products)
        if (!Schema::hasTable('tecidos')) {
            Schema::create('tecidos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Personalizacoes (deve ser criado antes de products)
        if (!Schema::hasTable('personalizacoes')) {
            Schema::create('personalizacoes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Modelos (deve ser criado antes de products)
        if (!Schema::hasTable('modelos')) {
            Schema::create('modelos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Products (depois de tecidos, personalizacoes e modelos)
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->foreignId('tecido_id')->nullable()->constrained('tecidos')->onDelete('set null');
                $table->foreignId('personalizacao_id')->nullable()->constrained('personalizacoes')->onDelete('set null');
                $table->foreignId('modelo_id')->nullable()->constrained('modelos')->onDelete('set null');
                $table->decimal('price', 10, 2)->default(0);
                $table->enum('sale_type', ['unidade', 'kg', 'metro'])->default('unidade');
                $table->boolean('allow_application')->default(false);
                $table->longText('application_types')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
                $table->index(['tecido_id', 'active']);
                $table->index(['personalizacao_id', 'active']);
                $table->index(['modelo_id', 'active']);
            });
        }

        // Product Images
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('image_path');
                $table->boolean('is_primary')->default(false);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['product_id', 'is_primary', 'order']);
            });
        }

        // Serigraphy Colors
        if (!Schema::hasTable('serigraphy_colors')) {
            Schema::create('serigraphy_colors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_neon')->default(false);
                $table->integer('order')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // Size Surcharges
        if (!Schema::hasTable('size_surcharges')) {
            Schema::create('size_surcharges', function (Blueprint $table) {
                $table->id();
                $table->string('size');
                $table->decimal('price_from', 10, 2);
                $table->decimal('price_to', 10, 2)->nullable();
                $table->decimal('surcharge', 10, 2);
                $table->timestamps();
            });
        }

        // Sublimation Addons
        if (!Schema::hasTable('sublimation_addons')) {
            Schema::create('sublimation_addons', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->decimal('price_adjustment', 10, 2);
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Tecidos
        if (!Schema::hasTable('tecidos')) {
            Schema::create('tecidos', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
                
                $table->index(['active', 'order']);
            });
        }

        // Terms Conditions
        if (!Schema::hasTable('terms_conditions')) {
            Schema::create('terms_conditions', function (Blueprint $table) {
                $table->id();
                $table->text('content');
                $table->string('version')->default('1.0');
                $table->string('personalization_type')->nullable();
                $table->foreignId('fabric_type_id')->nullable()->constrained('product_options')->onDelete('set null');
                $table->string('title')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                
                $table->index(['personalization_type', 'fabric_type_id', 'active'], 'terms_cond_type_fabric_active_idx');
            });
        }
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('terms_conditions');
        Schema::dropIfExists('tecidos');
        Schema::dropIfExists('sublimation_addons');
        Schema::dropIfExists('size_surcharges');
        Schema::dropIfExists('serigraphy_colors');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('personalizacoes');
        Schema::dropIfExists('order_sublimation_files');
        Schema::dropIfExists('order_sublimations');
        Schema::dropIfExists('order_logs');
        Schema::dropIfExists('order_files');
        Schema::dropIfExists('order_edit_requests');
        Schema::dropIfExists('order_edit_history');
        Schema::dropIfExists('order_comments');
        Schema::dropIfExists('order_cancellations');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('modelos');
        Schema::dropIfExists('delivery_requests');
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('catalog_items');
        Schema::dropIfExists('catalog_categories');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('budget_customizations');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }
};

