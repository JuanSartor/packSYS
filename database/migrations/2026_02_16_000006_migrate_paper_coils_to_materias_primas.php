<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear el tipo "Bobina de papel" con los campos correspondientes
        $tipoId = DB::table('materias_primas_tipos')->insertGetId([
            'nombre' => 'Bobina de papel',
            'descripcion' => 'Bobinas de papel para produccion',
            'campos_inventario' => json_encode([
                ['nombre' => 'tipo_papel', 'etiqueta' => 'Tipo de Papel', 'tipo' => 'text', 'obligatorio' => true, 'step' => null],
                ['nombre' => 'ancho', 'etiqueta' => 'Ancho (cm)', 'tipo' => 'number', 'obligatorio' => true, 'step' => '0.01'],
                ['nombre' => 'gramaje', 'etiqueta' => 'Gramaje (g/m2)', 'tipo' => 'number', 'obligatorio' => true, 'step' => '0.01'],
            ]),
            'campos_producto' => json_encode([
                ['nombre' => 'ancho', 'etiqueta' => 'Ancho (cm)', 'tipo' => 'number', 'obligatorio' => true, 'step' => '0.01'],
                ['nombre' => 'largo', 'etiqueta' => 'Largo (cm)', 'tipo' => 'number', 'obligatorio' => true, 'step' => '0.01'],
                ['nombre' => 'fuelle', 'etiqueta' => 'Fuelle (cm)', 'tipo' => 'number', 'obligatorio' => false, 'step' => '0.01'],
            ]),
            'unidad_consumo' => 'kg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Migrar cada paper_coil a materias_primas
        $paperCoils = DB::table('paper_coils')->get();
        $coilIdMap = [];

        foreach ($paperCoils as $coil) {
            $nombre = trim(($coil->tipo_papel ?? 'Bobina') . ' - ' . ($coil->ancho ?? '0') . 'cm');

            $newId = DB::table('materias_primas')->insertGetId([
                'materia_prima_tipo_id' => $tipoId,
                'nombre' => $nombre,
                'campos_valores' => json_encode([
                    'tipo_papel' => $coil->tipo_papel,
                    'ancho' => $coil->ancho,
                    'gramaje' => $coil->gramaje,
                ]),
                'stock_actual' => $coil->peso_actual ?? 0,
                'stock_inicial' => $coil->peso_inicial ?? 0,
                'alerta_minima' => $coil->alerta_minima ?? 0,
                'eliminado' => $coil->eliminado ?? 0,
                'created_at' => $coil->created_at,
                'updated_at' => $coil->updated_at,
            ]);

            $coilIdMap[$coil->id] = $newId;
        }

        // 3. Migrar product_materials a producto_materia_prima
        $productMaterials = DB::table('product_materials')->get();
        foreach ($productMaterials as $pm) {
            if (isset($coilIdMap[$pm->paper_coil_id])) {
                DB::table('producto_materia_prima')->insert([
                    'product_id' => $pm->product_id,
                    'materia_prima_id' => $coilIdMap[$pm->paper_coil_id],
                    'consumo_por_unidad' => $pm->consumo_por_unidad,
                ]);
            }
        }

        // 4. Migrar materias_config en productos que usan bobina
        $products = DB::table('products')->where('usa_bobina', true)->get();
        foreach ($products as $product) {
            $config = [
                (string) $tipoId => [
                    'ancho' => $product->ancho,
                    'largo' => $product->largo,
                    'fuelle' => $product->fuelle,
                ]
            ];
            DB::table('products')->where('id', $product->id)->update([
                'materias_config' => json_encode($config),
            ]);
        }

        // 5. Migrar stock_movements que referencian paper_coil_id
        $movements = DB::table('stock_movements')->whereNotNull('paper_coil_id')->get();
        foreach ($movements as $movement) {
            if (isset($coilIdMap[$movement->paper_coil_id])) {
                DB::table('stock_movements')
                    ->where('id', $movement->id)
                    ->update(['materia_prima_id' => $coilIdMap[$movement->paper_coil_id]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpiar datos migrados
        DB::table('producto_materia_prima')->truncate();
        DB::table('materias_primas')->truncate();

        // Limpiar materias_config de productos
        DB::table('products')->whereNotNull('materias_config')->update(['materias_config' => null]);

        // Limpiar materia_prima_id de stock_movements
        DB::table('stock_movements')->whereNotNull('materia_prima_id')->update(['materia_prima_id' => null]);

        // Eliminar el tipo auto-creado
        DB::table('materias_primas_tipos')->where('nombre', 'Bobina de papel')->delete();
    }
};
