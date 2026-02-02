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
        Schema::table('transports', function (Blueprint $table) {
            if (!Schema::hasColumn('transports', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('costo')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('transports', 'eliminado')) {
                $table->tinyInteger('eliminado')->default(0)->after('created_by');
            }
            if (!Schema::hasColumn('transports', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transports', function (Blueprint $table) {
            if (Schema::hasColumn('transports', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('transports', 'eliminado')) {
                $table->dropColumn('eliminado');
            }
            if (Schema::hasColumn('transports', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
