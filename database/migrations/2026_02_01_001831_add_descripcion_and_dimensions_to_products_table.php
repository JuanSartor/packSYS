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
            if (!Schema::hasColumn('products', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'ancho')) {
                $table->decimal('ancho', 8, 2)->nullable()->after('usa_bobina');
            }

            if (!Schema::hasColumn('products', 'largo')) {
                $table->decimal('largo', 8, 2)->nullable()->after('ancho');
            }

            if (!Schema::hasColumn('products', 'fuelle')) {
                $table->decimal('fuelle', 8, 2)->nullable()->after('largo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'descripcion')) {
                $table->dropColumn('descripcion');
            }

            if (Schema::hasColumn('products', 'ancho')) {
                $table->dropColumn('ancho');
            }

            if (Schema::hasColumn('products', 'largo')) {
                $table->dropColumn('largo');
            }

            if (Schema::hasColumn('products', 'fuelle')) {
                $table->dropColumn('fuelle');
            }
        });
    }
};
