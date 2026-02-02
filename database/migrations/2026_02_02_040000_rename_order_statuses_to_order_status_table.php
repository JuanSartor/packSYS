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
        // Primero eliminar la foreign key en production_orders
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeign(['order_status_id']);
        });

        // Renombrar la tabla
        Schema::rename('order_statuses', 'order_status');

        // Volver a crear la foreign key apuntando a la nueva tabla
        Schema::table('production_orders', function (Blueprint $table) {
            $table->foreign('order_status_id')->references('id')->on('order_status')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la foreign key
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeign(['order_status_id']);
        });

        // Renombrar la tabla de vuelta
        Schema::rename('order_status', 'order_statuses');

        // Volver a crear la foreign key apuntando a la tabla original
        Schema::table('production_orders', function (Blueprint $table) {
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('set null');
        });
    }
};
