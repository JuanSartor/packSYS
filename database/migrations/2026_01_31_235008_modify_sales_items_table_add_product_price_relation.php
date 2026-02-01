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
        Schema::table('sale_items', function (Blueprint $table) {
            // Renombrar precio_unitario a precio_unitario_venta
            if (Schema::hasColumn('sale_items', 'precio_unitario')) {
                $table->renameColumn('precio_unitario', 'precio_unitario_venta');
            }

            // Agregar product_price_id como foreign key
            if (!Schema::hasColumn('sale_items', 'product_price_id')) {
                $table->unsignedBigInteger('product_price_id')->after('product_id')->nullable();
                $table->foreign('product_price_id')->references('id')->on('product_prices')->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Revertir cambios
            if (Schema::hasColumn('sale_items', 'product_price_id')) {
                $table->dropForeign(['product_price_id']);
                $table->dropColumn('product_price_id');
            }

            if (Schema::hasColumn('sale_items', 'precio_unitario_venta')) {
                $table->renameColumn('precio_unitario_venta', 'precio_unitario');
            }
        });
    }
};
