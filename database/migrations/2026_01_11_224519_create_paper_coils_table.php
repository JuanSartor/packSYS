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
        Schema::create('paper_coils', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_papel', 100)->nullable();
            $table->decimal('ancho', 8, 2)->nullable();
            $table->decimal('gramaje', 8, 2)->nullable();
            $table->decimal('peso_inicial', 10, 2);
            $table->decimal('peso_actual', 10, 2);
            $table->decimal('alerta_minima', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_coils');
    }
};
