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
        // Agregar created_by a product_prices
        Schema::table('product_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('product_prices', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('vigente_desde')->constrained('users')->onDelete('set null');
            }
        });

        // Agregar created_by a clients
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('eliminado')->constrained('users')->onDelete('set null');
            }
        });

        // Agregar created_by a products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('eliminado')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            if (Schema::hasColumn('product_prices', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
