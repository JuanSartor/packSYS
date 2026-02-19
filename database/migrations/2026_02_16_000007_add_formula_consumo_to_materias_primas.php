<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columnas del tipo directamente a materias_primas
        Schema::table('materias_primas', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            $table->string('unidad_consumo', 50)->default('unidad')->after('descripcion');
            $table->json('campos_inventario')->nullable()->after('unidad_consumo');
            $table->json('campos_producto')->nullable()->after('campos_inventario');
            $table->text('formula_consumo')->nullable()->after('campos_valores');
        });

        // Migrar datos: copiar campos del tipo a cada materia prima
        $materias = DB::table('materias_primas')->whereNotNull('materia_prima_tipo_id')->get();
        foreach ($materias as $mp) {
            $tipo = DB::table('materias_primas_tipos')->find($mp->materia_prima_tipo_id);
            if ($tipo) {
                DB::table('materias_primas')->where('id', $mp->id)->update([
                    'descripcion' => $tipo->descripcion,
                    'unidad_consumo' => $tipo->unidad_consumo ?? 'unidad',
                    'campos_inventario' => $tipo->campos_inventario,
                    'campos_producto' => $tipo->campos_producto,
                ]);
            }
        }

        // Eliminar FK y columna materia_prima_tipo_id
        Schema::table('materias_primas', function (Blueprint $table) {
            $table->dropForeign(['materia_prima_tipo_id']);
            $table->dropColumn('materia_prima_tipo_id');
        });

        // Actualizar materias_config en products: cambiar keys de tipo_id a materia_prima_id
        $products = DB::table('products')->whereNotNull('materias_config')->get();
        foreach ($products as $product) {
            $config = json_decode($product->materias_config, true);
            if (!$config) continue;

            $newConfig = [];
            // Para cada tipo_id en la config, buscar las materias primas vinculadas a este producto de ese tipo
            foreach ($config as $tipoId => $campos) {
                // Buscar las materias primas de este producto que eran de este tipo
                $items = DB::table('producto_materia_prima')
                    ->join('materias_primas', 'producto_materia_prima.materia_prima_id', '=', 'materias_primas.id')
                    ->where('producto_materia_prima.product_id', $product->id)
                    ->select('producto_materia_prima.materia_prima_id')
                    ->get();

                // Asignar la config a cada materia prima del producto
                foreach ($items as $item) {
                    $newConfig[$item->materia_prima_id] = $campos;
                }
            }

            if (!empty($newConfig)) {
                DB::table('products')->where('id', $product->id)->update([
                    'materias_config' => json_encode($newConfig),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('materias_primas', function (Blueprint $table) {
            $table->foreignId('materia_prima_tipo_id')->nullable()->constrained('materias_primas_tipos')->onDelete('cascade');
        });

        Schema::table('materias_primas', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'unidad_consumo', 'campos_inventario', 'campos_producto', 'formula_consumo']);
        });
    }
};
