<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria tabela para tipos de produtos dinâmicos
     */
    public function up(): void
    {
        Schema::create('sublimation_product_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->boolean('active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Inserir tipos padrão
        $defaultTypes = [
            ['slug' => 'camisa', 'name' => 'Camisa', 'order' => 1],
            ['slug' => 'conjunto', 'name' => 'Conjunto Esportivo', 'order' => 2],
            ['slug' => 'bandeira', 'name' => 'Bandeira', 'order' => 3],
            ['slug' => 'winderbanner', 'name' => 'Winderbanner', 'order' => 4],
            ['slug' => 'bone', 'name' => 'Boné', 'order' => 5],
        ];

        foreach ($defaultTypes as $type) {
            DB::table('sublimation_product_types')->insert([
                'tenant_id' => null, // Global (template)
                'slug' => $type['slug'],
                'name' => $type['name'],
                'order' => $type['order'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sublimation_product_types');
    }
};
