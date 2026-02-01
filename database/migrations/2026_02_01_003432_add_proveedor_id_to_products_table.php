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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'proveedor_id')) {
                $table->foreignId('proveedor_id')->nullable()->after('type')->constrained('proveedores')->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'proveedor_id')) {
                $table->dropForeign(['proveedor_id']);
                $table->dropColumn('proveedor_id');
            }
        });
    }
};
