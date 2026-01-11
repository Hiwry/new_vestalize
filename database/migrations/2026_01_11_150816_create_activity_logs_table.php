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
        Schema::create('activity_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('log_name')->nullable();
            $blueprint->text('description');
            $blueprint->nullableMorphs('subject');
            $blueprint->nullableMorphs('causer');
            $blueprint->json('properties')->nullable();
            $blueprint->string('event')->nullable();
            $blueprint->unsignedBigInteger('tenant_id')->nullable()->index();
            $blueprint->string('ip_address', 45)->nullable();
            $blueprint->text('user_agent')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
