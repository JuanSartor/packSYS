<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MateriaPrimaController extends Controller
{
    public function index()
    {
        $search = request('search');

        $materiasPrimas = MateriaPrima::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('materias-primas.index', compact('materiasPrimas'));
    }

    public function create()
    {
        return view('materias-primas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'unidad_consumo' => ['required', 'string', 'max:50'],
            'formula_consumo' => ['nullable', 'string'],
            'campos_inventario' => ['nullable', 'array'],
            'campos_inventario.*.etiqueta' => ['required', 'string', 'max:100'],
            'campos_inventario.*.descripcion' => ['nullable', 'string', 'max:255'],
            'campos_inventario.*.tipo' => ['required', 'in:text,number,textarea'],
            'campos_inventario.*.obligatorio' => ['required'],
            'campos_inventario.*.step' => ['nullable', 'string'],
            'campos_producto' => ['nullable', 'array'],
            'campos_producto.*.etiqueta' => ['required', 'string', 'max:100'],
            'campos_producto.*.descripcion' => ['nullable', 'string', 'max:255'],
            'campos_producto.*.tipo' => ['required', 'in:text,number,textarea'],
            'campos_producto.*.obligatorio' => ['required'],
            'campos_producto.*.step' => ['nullable', 'string'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_inicial' => ['required', 'numeric', 'min:0'],
            'alerta_minima' => ['required', 'numeric', 'min:0'],
        ]);

        // Normalizar campos: auto-generar nombre interno
        $camposInventario = $this->normalizarCampos($validated['campos_inventario'] ?? []);
        $camposProducto = $this->normalizarCampos($validated['campos_producto'] ?? []);

        // Validar y recopilar valores de campos de inventario
        $camposValores = [];
        foreach ($camposInventario as $campo) {
            $key = 'campo_' . $campo['nombre'];
            $rules = [];

            if ($campo['obligatorio']) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            if ($campo['tipo'] === 'number') {
                $rules[] = 'numeric';
            } elseif (in_array($campo['tipo'], ['text', 'textarea'])) {
                $rules[] = 'string';
                $rules[] = 'max:255';
            }

            $request->validate([$key => $rules]);
            $camposValores[$campo['nombre']] = $request->input($key);
        }

        $materiaPrima = MateriaPrima::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'unidad_consumo' => $validated['unidad_consumo'],
            'campos_inventario' => !empty($camposInventario) ? $camposInventario : null,
            'campos_producto' => !empty($camposProducto) ? $camposProducto : null,
            'campos_valores' => !empty($camposValores) ? $camposValores : null,
            'formula_consumo' => $validated['formula_consumo'] ?? null,
            'stock_actual' => $validated['stock_actual'],
            'stock_inicial' => $validated['stock_inicial'],
            'alerta_minima' => $validated['alerta_minima'],
            'created_by' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $materiaPrima->id,
                'nombre' => $materiaPrima->nombre,
                'stock_actual' => $materiaPrima->stock_actual,
                'campos_producto' => $materiaPrima->campos_producto,
            ]);
        }

        return redirect()->route('materias-primas.index')
            ->with('success', 'Materia prima creada exitosamente.');
    }

    public function show(MateriaPrima $materiaPrima)
    {
        $materiaPrima->load(['productos', 'stockMovements' => function ($q) {
            $q->latest('created_at')->limit(20);
        }]);

        return view('materias-primas.show', compact('materiaPrima'));
    }

    public function edit(MateriaPrima $materiaPrima)
    {
        return view('materias-primas.edit', compact('materiaPrima'));
    }

    public function update(Request $request, MateriaPrima $materiaPrima)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'unidad_consumo' => ['required', 'string', 'max:50'],
            'formula_consumo' => ['nullable', 'string'],
            'campos_inventario' => ['nullable', 'array'],
            'campos_inventario.*.etiqueta' => ['required', 'string', 'max:100'],
            'campos_inventario.*.descripcion' => ['nullable', 'string', 'max:255'],
            'campos_inventario.*.tipo' => ['required', 'in:text,number,textarea'],
            'campos_inventario.*.obligatorio' => ['required'],
            'campos_inventario.*.step' => ['nullable', 'string'],
            'campos_producto' => ['nullable', 'array'],
            'campos_producto.*.etiqueta' => ['required', 'string', 'max:100'],
            'campos_producto.*.descripcion' => ['nullable', 'string', 'max:255'],
            'campos_producto.*.tipo' => ['required', 'in:text,number,textarea'],
            'campos_producto.*.obligatorio' => ['required'],
            'campos_producto.*.step' => ['nullable', 'string'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_inicial' => ['required', 'numeric', 'min:0'],
            'alerta_minima' => ['required', 'numeric', 'min:0'],
        ]);

        // Normalizar campos
        $camposInventario = $this->normalizarCampos($validated['campos_inventario'] ?? []);
        $camposProducto = $this->normalizarCampos($validated['campos_producto'] ?? []);

        // Validar y recopilar valores de campos de inventario
        $camposValores = [];
        foreach ($camposInventario as $campo) {
            $key = 'campo_' . $campo['nombre'];
            $rules = [];

            if ($campo['obligatorio']) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            if ($campo['tipo'] === 'number') {
                $rules[] = 'numeric';
            } elseif (in_array($campo['tipo'], ['text', 'textarea'])) {
                $rules[] = 'string';
                $rules[] = 'max:255';
            }

            $request->validate([$key => $rules]);
            $camposValores[$campo['nombre']] = $request->input($key);
        }

        $materiaPrima->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'unidad_consumo' => $validated['unidad_consumo'],
            'campos_inventario' => !empty($camposInventario) ? $camposInventario : null,
            'campos_producto' => !empty($camposProducto) ? $camposProducto : null,
            'campos_valores' => !empty($camposValores) ? $camposValores : null,
            'formula_consumo' => $validated['formula_consumo'] ?? null,
            'stock_actual' => $validated['stock_actual'],
            'stock_inicial' => $validated['stock_inicial'],
            'alerta_minima' => $validated['alerta_minima'],
        ]);

        return redirect()->route('materias-primas.index')
            ->with('success', 'Materia prima actualizada exitosamente.');
    }

    public function destroy(MateriaPrima $materiaPrima)
    {
        $materiaPrima->update(['eliminado' => 1]);

        return redirect()->route('materias-primas.index')
            ->with('success', 'Materia prima eliminada exitosamente.');
    }

    /**
     * Obtener todas las materias primas (JSON para formulario de producto)
     */
    public function listar()
    {
        $materias = MateriaPrima::where('eliminado', 0)
            ->select('id', 'nombre', 'stock_actual', 'unidad_consumo', 'campos_producto')
            ->orderBy('nombre')
            ->get();

        return response()->json($materias);
    }

    /**
     * Normalizar campos: auto-generar nombre interno desde etiqueta
     */
    private function normalizarCampos(array $campos): array
    {
        $result = [];
        foreach ($campos as $campo) {
            if (empty($campo['etiqueta'])) continue;

            $nombre = Str::slug($campo['etiqueta'], '_');

            // Evitar duplicados
            $base = $nombre;
            $i = 2;
            while (collect($result)->pluck('nombre')->contains($nombre)) {
                $nombre = $base . '_' . $i++;
            }

            $result[] = [
                'nombre' => $nombre,
                'etiqueta' => $campo['etiqueta'],
                'descripcion' => $campo['descripcion'] ?? null,
                'tipo' => $campo['tipo'] ?? 'text',
                'obligatorio' => filter_var($campo['obligatorio'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'step' => $campo['step'] ?? null,
            ];
        }
        return $result;
    }
}
