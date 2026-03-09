<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabric_pieces', function (Blueprint $table) {
            if (!Schema::hasColumn('fabric_pieces', 'control_unit')) {
                $table->enum('control_unit', ['kg', 'metros'])->default('kg')->after('meters');
            }

            if (!Schema::hasColumn('fabric_pieces', 'meters_current')) {
                $table->decimal('meters_current', 10, 2)->nullable()->after('meters');
            }

            if (!Schema::hasColumn('fabric_pieces', 'min_quantity_alert')) {
                $table->decimal('min_quantity_alert', 10, 3)->default(0)->after('sale_price');
            }

            if (!Schema::hasColumn('fabric_pieces', 'available_in_pdv')) {
                $table->boolean('available_in_pdv')->default(true)->after('min_quantity_alert');
            }

            if (!Schema::hasColumn('fabric_pieces', 'available_in_catalog')) {
                $table->boolean('available_in_catalog')->default(true)->after('available_in_pdv');
            }

            if (!Schema::hasColumn('fabric_pieces', 'available_in_orders')) {
                $table->boolean('available_in_orders')->default(true)->after('available_in_catalog');
            }
        });

        DB::statement("
            UPDATE fabric_pieces
            SET control_unit = CASE
                WHEN (weight IS NULL OR weight <= 0) AND COALESCE(meters, 0) > 0 THEN 'metros'
                ELSE 'kg'
            END
        ");

        DB::statement("
            UPDATE fabric_pieces
            SET weight_current = COALESCE(weight_current, weight, 0)
            WHERE control_unit = 'kg'
        ");

        DB::statement("
            UPDATE fabric_pieces
            SET meters_current = COALESCE(meters_current, meters, 0)
            WHERE control_unit = 'metros'
        ");

        Schema::table('fabric_piece_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('fabric_piece_sales', 'catalog_order_id')) {
                $table->foreignId('catalog_order_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('fabric_piece_sales', 'order_item_id')) {
                $table->foreignId('order_item_id')->nullable()->after('catalog_order_id')->constrained('order_items')->nullOnDelete();
            }

            if (!Schema::hasColumn('fabric_piece_sales', 'channel')) {
                $table->string('channel', 30)->default('manual')->after('sold_by');
            }

            if (!Schema::hasColumn('fabric_piece_sales', 'reverted_at')) {
                $table->timestamp('reverted_at')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('fabric_piece_sales', 'reverted_by')) {
                $table->foreignId('reverted_by')->nullable()->after('reverted_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('fabric_piece_sales', 'revert_reason')) {
                $table->text('revert_reason')->nullable()->after('reverted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fabric_piece_sales', function (Blueprint $table) {
            if (Schema::hasColumn('fabric_piece_sales', 'revert_reason')) {
                $table->dropColumn('revert_reason');
            }

            if (Schema::hasColumn('fabric_piece_sales', 'reverted_by')) {
                $table->dropConstrainedForeignId('reverted_by');
            }

            if (Schema::hasColumn('fabric_piece_sales', 'reverted_at')) {
                $table->dropColumn('reverted_at');
            }

            if (Schema::hasColumn('fabric_piece_sales', 'channel')) {
                $table->dropColumn('channel');
            }

            if (Schema::hasColumn('fabric_piece_sales', 'order_item_id')) {
                $table->dropConstrainedForeignId('order_item_id');
            }

            if (Schema::hasColumn('fabric_piece_sales', 'catalog_order_id')) {
                $table->dropConstrainedForeignId('catalog_order_id');
            }
        });

        Schema::table('fabric_pieces', function (Blueprint $table) {
            $columns = [
                'available_in_orders',
                'available_in_catalog',
                'available_in_pdv',
                'min_quantity_alert',
                'meters_current',
                'control_unit',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('fabric_pieces', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
