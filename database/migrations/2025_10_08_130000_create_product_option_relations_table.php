<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('parent_id');
            $table->timestamps();

            $table->foreign('option_id')->references('id')->on('product_options')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('product_options')->onDelete('cascade');
            
            $table->unique(['option_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_relations');
    }
};
