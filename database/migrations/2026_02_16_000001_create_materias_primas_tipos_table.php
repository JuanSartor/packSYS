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
        Schema::create('materias_primas_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->json('campos_inventario');
            $table->json('campos_producto')->nullable();
            $table->string('unidad_consumo', 50)->default('unidad');
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
        Schema::dropIfExists('materias_primas_tipos');
    }
};
