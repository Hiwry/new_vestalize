<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_sublimation_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_sublimation_id');
            $table->string('file_name'); // Nome original do arquivo
            $table->string('file_path'); // Caminho do arquivo no storage
            $table->string('file_type')->nullable(); // Tipo MIME do arquivo
            $table->integer('file_size')->nullable(); // Tamanho em bytes
            $table->timestamps();

            $table->foreign('order_sublimation_id')->references('id')->on('order_sublimations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_sublimation_files');
    }
};
