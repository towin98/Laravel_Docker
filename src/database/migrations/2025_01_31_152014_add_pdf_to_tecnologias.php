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
        Schema::table('tecnologias', function (Blueprint $table) {
            $table->string('pdf')->nullable()->comment('Nombre del pdf asociado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tecnologias', function (Blueprint $table) {
            $table->dropColumn('pdf');
        });
    }
};
