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
        Schema::create('tecnologia_user', function (Blueprint $table) {
            $table->id();
            // constrained es una forma sencilla de referencias, es lo mismo que hacer : references('id')->on('users')
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tecnologia_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnologia_user');
    }
};
