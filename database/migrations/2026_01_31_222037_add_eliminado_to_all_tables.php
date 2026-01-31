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
        // Tablas con timestamps
        $tablesWithTimestamps = [
            'users',
            'clients',
            'canales',
            'products',
            'paper_coils',
            'production_orders',
            'sales',
        ];

        foreach ($tablesWithTimestamps as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'eliminado')) {
                    $table->tinyInteger('eliminado')->default(0)->after('updated_at');
                }
            });
        }

        // Tablas sin timestamps - agregar al final
        $tablesWithoutTimestamps = [
            'transports',
            'sale_items',
            'product_materials',
            'stock_movements',
            'production_times',
            'product_prices',
        ];

        foreach ($tablesWithoutTimestamps as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'eliminado')) {
                    $table->tinyInteger('eliminado')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'clients',
            'canales',
            'products',
            'transports',
            'paper_coils',
            'production_orders',
            'sales',
            'sale_items',
            'product_materials',
            'stock_movements',
            'production_times',
            'product_prices',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('eliminado');
            });
        }
    }
};
