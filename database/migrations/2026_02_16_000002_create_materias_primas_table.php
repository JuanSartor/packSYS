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
        Schema::create('materias_primas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_prima_tipo_id')->constrained('materias_primas_tipos')->onDelete('cascade');
            $table->string('nombre', 200);
            $table->json('campos_valores')->nullable();
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->decimal('stock_inicial', 10, 2)->default(0);
            $table->decimal('alerta_minima', 10, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('eliminado')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias_primas');
    }
};
