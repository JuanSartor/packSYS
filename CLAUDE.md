# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Descripcion del Proyecto

PackSYS es un sistema de gestion de manufactura y ventas desarrollado en Laravel 10, orientado a productos de papel (bolsas, friselina, cajas, insumos). Incluye seguimiento de ordenes de produccion, gestion de materias primas genericas con consumo por formula, ventas con control de stock y gestion de usuarios con multiples roles. La aplicacion esta completamente en espanol.

## Entorno de Desarrollo

- **Runtime**: PHP 8.1+, Node.js con npm
- **Servidor**: WAMP (Windows, Apache, MySQL, PHP) en `http://localhost/packSYS/public`
- **Base de datos**: MySQL 8.0+ en localhost:3306, base `packsys`, usuario `root`, sin contrasena

## Comandos Frecuentes

```bash
# Assets del frontend
npm run dev          # Servidor Vite con HMR
npm run build        # Build de produccion

# Laravel
php artisan serve    # Servidor de desarrollo (alternativa a WAMP)
php artisan migrate  # Ejecutar migraciones
php artisan db:seed  # Poblar base de datos

# Tests
./vendor/bin/phpunit                              # Todos los tests
./vendor/bin/phpunit tests/Feature                # Solo tests de Feature
./vendor/bin/phpunit tests/Unit                   # Solo tests unitarios
./vendor/bin/phpunit --filter=SaleControllerTest  # Un test especifico

# Estilo de codigo
./vendor/bin/pint    # Corregir formato PHP (Laravel Pint)
```

## Arquitectura

### Roles y Autorizacion

Tres roles de usuario con acceso jerarquico, controlados por `RoleMiddleware`:
- **gestor** (administrador): Acceso total a todos los modulos, gestion de usuarios y tablas de configuracion
- **vendedor**: Clientes, ventas, visualizacion de productos
- **operario**: Materias primas, ordenes de produccion

Las constantes de rol estan definidas en el modelo `User`. Las rutas se protegen con `middleware('role:gestor,vendedor')` en [web.php](routes/web.php).

### Eliminacion Logica (Soft Deletes)

Se usa una columna booleana `eliminado` personalizada (no el SoftDeletes nativo de Laravel). Todas las tablas principales tienen esta columna. Los controladores filtran con `where('eliminado', false)`. El middleware `CheckUserNotDeleted` cierra la sesion de usuarios eliminados.

### Logica de Negocio Principal

- **Ventas** ([SaleController](app/Http/Controllers/SaleController.php)): Usa transacciones de BD. Valida disponibilidad de stock antes de crear ventas, decrementa automaticamente `stock_actual` y crea registros de auditoria en `StockMovement`.
- **Ordenes de Produccion** ([ProductionOrderController](app/Http/Controllers/ProductionOrderController.php)): Maquina de estados con acciones start/pause/finish. Al crear (`store`), verifica stock de materias primas usando formulas; si no alcanza, muestra la capacidad maxima producible. `finish()` re-evalua formulas, descuenta materias primas e incrementa `stock_actual` del producto. Registra tiempos de produccion via modelo `ProductionTime`.
- **Precios de Producto**: Historial de precios en tabla `product_prices` con campos `costo` y `precio_venta`. `Product::currentPrice()` devuelve el precio vigente mas reciente.

### Sistema de Materias Primas

Reemplazo generico del viejo sistema de bobinas de papel. Cada materia prima es autocontenida (sin tipos separados).

**Modelo `MateriaPrima`** ([app/Models/MateriaPrima.php](app/Models/MateriaPrima.php)):
- `nombre`, `descripcion`, `unidad_consumo` (ej: "kg", "metros")
- `campos_inventario` (JSON): Define campos dinamicos propios de la MP. Ej: `[{"nombre": "gramaje", "tipo": "number"}]`
- `campos_producto` (JSON): Define campos que cada producto debe completar al vincularse. Ej: `[{"nombre": "ancho", "tipo": "number"}, {"nombre": "largo", "tipo": "number"}]`
- `campos_valores` (JSON): Valores actuales de los campos_inventario. Ej: `{"gramaje": 80}`
- `formula_consumo` (string): Formula matematica que usa variables de campos_producto y campos_valores. Ej: `"(ancho * largo * gramaje) / 10000"`
- `stock_actual`, `stock_inicial`, `alerta_minima`

**Consumo por formula** ([FormulaEvaluator](app/Services/FormulaEvaluator.php)):
- Al vincular una MP a un producto, el usuario completa los `campos_producto` (ej: ancho=30, largo=40)
- Los valores se guardan en `Product.materias_config` (JSON) indexados por `materia_prima_id`
- El consumo se calcula automaticamente: `FormulaEvaluator::calcularConsumo(formula, materias_config[mp_id], mp.campos_valores)`
- El resultado se almacena en `ProductoMateriaPrima.consumo_por_unidad`
- Las formulas se re-evaluan al crear y finalizar ordenes de produccion
- El frontend (Alpine.js) muestra un preview del consumo calculado en tiempo real usando `new Function()`

**Tabla pivot `producto_materia_prima`**:
- `product_id`, `materia_prima_id`, `consumo_por_unidad` (decimal:4, calculado por formula)

**Flujo de consumo en produccion**:
1. Al crear orden: se evalua formula por cada MP vinculada, se verifica que `stock_actual >= consumo * cantidad`
2. Si stock insuficiente: se muestra error con capacidad maxima producible
3. Al finalizar orden: se re-evaluan formulas, se descuenta stock de MPs, se incrementa stock del producto, se crean StockMovements

### Stack del Frontend

- Templates Blade con Tailwind CSS 3 + plugin Forms
- Alpine.js para componentes interactivos (formularios dinamicos de items de venta, materias primas con preview de formula, etc.)
- SweetAlert2 via CDN para notificaciones (`showSuccess`, `showError`)
- Layouts en [resources/views/layouts/](resources/views/layouts/)

**Patron importante en Blade**: Usar `{!! json_encode($var) !!}` en lugar de `@json($var)` para arrays/objetos anidados dentro de atributos Alpine `x-data`, ya que `@json` puede causar problemas con comillas en ese contexto.

### Helpers

[app/Helpers/helpers.php](app/Helpers/helpers.php) provee `formatNumber()` y `formatCurrency()` para formato numerico en locale espanol (ej: `$1.500.000,00`).

### Tests

Existen tests de Feature para todos los controladores principales (14 archivos en `tests/Feature/`). El entorno de testing usa `.env.testing` con drivers array para cache/sesion y rounds reducidos de bcrypt.

### Base de Datos

45 archivos de migracion en `database/migrations/`. Relaciones clave:
- Product -> ProductType, Proveedor, Unidad, ProductPrice[], ProductoMateriaPrima[] -> MateriaPrima
- Product.materias_config (JSON): configuracion de campos_producto por MP vinculada
- MateriaPrima: campos_inventario, campos_producto, campos_valores (JSON), formula_consumo
- Sale -> Client, Transport, SaleItem[] -> Product, ProductPrice
- ProductionOrder -> Product, OrderStatus, ProductionTime[]
- StockMovement: puede referenciar product_id, paper_coil_id (legacy), o materia_prima_id
- Client -> Canal

Todas las vistas index paginan a 15 elementos y soportan busqueda por filtro.

### Codigo Legacy (pendiente de limpieza)

Los siguientes elementos son restos del viejo sistema de bobinas. No se usan activamente pero siguen en el codigo:
- `Product.usa_bobina`, `Product.ancho`, `Product.largo`, `Product.fuelle` en fillable/casts (reemplazados por `materias_config`)
- `Product.paperCoils()`, `Product.materials()` relaciones (reemplazadas por `materiasPrimas()`, `productoMateriasPrimas()`)
- `PaperCoilController` y rutas `paper-coils` en web.php
- `StockMovement.paper_coil_id` (ahora se usa `materia_prima_id`)
- Tabla `materias_primas_tipos` (migracion existe pero modelo y controller eliminados)
