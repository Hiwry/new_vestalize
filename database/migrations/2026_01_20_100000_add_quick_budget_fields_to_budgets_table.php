<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Quick budget flag
            $table->boolean('is_quick')->default(false)->after('status');
            
            // Contact info for quick budgets (without registered client)
            $table->string('contact_name')->nullable()->after('is_quick');
            $table->string('contact_phone')->nullable()->after('contact_name');
            
            // Deadline in days (default 15)
            $table->integer('deadline_days')->default(15)->after('contact_phone');
            
            // Internal product (not shown in PDF)
            $table->string('product_internal')->nullable()->after('deadline_days');
            
            // Technique (Silk 1 cor, Silk 2 cores, Bordado, etc)
            $table->string('technique')->nullable()->after('product_internal');
            
            // Quantity and unit price for quick budgets
            $table->integer('quantity')->nullable()->after('technique');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
        });
        
        // Make client_id nullable for quick budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn([
                'is_quick',
                'contact_name',
                'contact_phone',
                'deadline_days',
                'product_internal',
                'technique',
                'quantity',
                'unit_price',
            ]);
        });
    }
};
