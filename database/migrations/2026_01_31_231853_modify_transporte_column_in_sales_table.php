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
        Schema::table('sales', function (Blueprint $table) {
            // Eliminar columna transporte (boolean) si existe
            if (Schema::hasColumn('sales', 'transporte')) {
                $table->dropColumn('transporte');
            }

            // Agregar transport_id como foreign key obligatoria solo si no existe
            if (!Schema::hasColumn('sales', 'transport_id')) {
                $table->unsignedBigInteger('transport_id')->after('client_id');
                $table->foreign('transport_id')->references('id')->on('transports')->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['transport_id']);
            $table->dropColumn('transport_id');

            // Restaurar columna transporte boolean
            $table->boolean('transporte')->default(false);
        });
    }
};
