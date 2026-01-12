# PackSYS - Guía de Implementación

## ✅ LO QUE YA ESTÁ IMPLEMENTADO

### 1. Sistema de Autenticación
- ✅ Laravel Breeze instalado con Blade + Tailwind CSS
- ✅ Login funcional
- ✅ Sistema de roles (operario, vendedor, gestor)
- ✅ Middleware de roles configurado

### 2. Base de Datos
- ✅ Todas las tablas creadas y migradas
- ✅ Modelos Eloquent con relaciones
- ✅ Seeders con usuarios de prueba:
  - **admin@packsys.com** / password (Gestor)
  - **vendedor@packsys.com** / password (Vendedor)
  - **operario@packsys.com** / password (Operario)

### 3. Controladores CRUD Completos
- ✅ DashboardController - Estadísticas del sistema
- ✅ UserController - ABM de usuarios
- ✅ ClientController - ABM de clientes
- ✅ ProductController - ABM de productos
- ✅ TransportController - ABM de transportes
- ✅ PaperCoilController (creado, falta implementar)
- ✅ ProductionOrderController (creado, falta implementar)
- ✅ SaleController (creado, falta implementar)

### 4. Rutas Configuradas
- ✅ Rutas protegidas por autenticación
- ✅ Rutas protegidas por roles:
  - **Gestor**: Acceso total (usuarios, todos los ABMs)
  - **Vendedor**: Clientes, ventas, ver productos
  - **Operario**: Bobinas, órdenes de producción, ver productos

### 5. Vistas Creadas
- ✅ Dashboard con estadísticas
- ✅ Layout de autenticación (Breeze)
- ✅ **Navegación completa con links basados en roles** (resources/views/layouts/navigation.blade.php)
- ✅ **Vistas de Usuarios completas** (resources/views/users/):
  - index.blade.php - Lista con paginación y badges de roles
  - create.blade.php - Formulario con validación
  - edit.blade.php - Edición con password opcional
  - show.blade.php - Detalles con historial de ventas/órdenes
- ✅ **Vistas de Clientes completas** (resources/views/clients/):
  - index.blade.php - Lista con paginación
  - create.blade.php - Formulario completo
  - edit.blade.php - Edición de datos
  - show.blade.php - Detalles con historial de ventas
- ✅ **Vistas de Productos completas** (resources/views/products/):
  - index.blade.php - Lista con alertas de stock bajo (fondo rojo)
  - create.blade.php - Formulario con tipos y unidades
  - edit.blade.php - Edición completa
  - show.blade.php - Detalles con historial de precios y movimientos
- ✅ **Vistas de Transportes completas** (resources/views/transports/):
  - index.blade.php - Lista simple
  - create.blade.php - Formulario básico
  - edit.blade.php - Edición

### 6. GitIgnore Actualizado
- ✅ Configurado para excluir node_modules, vendor, .env, archivos IDE
- ✅ Excluye archivos temporales y de sistema operativo
- ✅ Proyecto listo para subir a repositorio

---

## ⏳ LO QUE FALTA POR IMPLEMENTAR

### Vistas Principales Pendientes

#### 1. Vistas de Bobinas de Papel (Paper Coils) - PRIORIDAD MEDIA
**Directorio**: `resources/views/paper-coils/`

**Controlador**: app/Http/Controllers/PaperCoilController.php (CREADO, FALTA IMPLEMENTAR MÉTODOS)

**index.blade.php**
```blade
<!-- Tabla: Tipo, Ancho, Gramaje, Peso Actual, Peso Inicial, Alerta Mínima -->
<!-- Mostrar en amarillo si peso_actual <= alerta_minima -->
```

**create.blade.php**
```blade
<!-- Campos:
  - tipo_papel (text)
  - ancho (number, decimal)
  - gramaje (number, decimal)
  - peso_inicial (number, decimal)
  - peso_actual (number, decimal)
  - alerta_minima (number, decimal)
-->
```

**edit.blade.php** - Similar a create

**show.blade.php**
```blade
<!-- Detalles de la bobina -->
<!-- Productos que usan esta bobina -->
<!-- Consumo histórico -->
```

#### 2. Vistas de Órdenes de Producción - PRIORIDAD MEDIA
**Directorio**: `resources/views/production-orders/`

**Controlador**: app/Http/Controllers/ProductionOrderController.php (CREADO, FALTA IMPLEMENTAR MÉTODOS)

**IMPORTANTE**: Implementar métodos especiales en el controlador:
```php
public function start(ProductionOrder $order) {
    $order->update(['estado' => 'produccion', 'started_at' => now()]);
}

public function pause(ProductionOrder $order) {
    $order->update(['estado' => 'pausada']);
}

public function finish(ProductionOrder $order) {
    $order->update(['estado' => 'finalizada', 'finished_at' => now()]);
}
```

**index.blade.php**
```blade
<!-- Tabla: ID, Producto, Cantidad, Estado, Creado por, Fecha -->
<!-- Filtros por estado -->
<!-- Badge de colores según estado -->
```

**create.blade.php**
```blade
<!-- Campos:
  - product_id (select con productos)
  - cantidad (number)
  - estado (select: espera, pendiente, produccion, pausada, finalizada)
-->
```

**edit.blade.php** - Editar cantidad y estado

**show.blade.php**
```blade
<!-- Detalles de la orden -->
<!-- Tiempos de producción (tabla production_times) -->
<!-- Botones para cambiar estado (Iniciar, Pausar, Finalizar) -->
```

#### 3. Vistas de Ventas - PRIORIDAD ALTA (MÁS COMPLEJA)
**Directorio**: `resources/views/sales/`

**Controlador**: app/Http/Controllers/SaleController.php (CREADO, FALTA IMPLEMENTAR)

**IMPORTANTE**: El método store debe:
1. Crear la venta
2. Crear los sale_items (tabla intermedia)
3. Restar stock de productos
4. Crear movimientos de stock (stock_movements)
5. Validar que haya stock suficiente

**index.blade.php**
```blade
<!-- Tabla: ID, Cliente, Total, Transporte, Vendedor, Fecha -->
<!-- Filtros por fecha y cliente -->
<!-- Paginación -->
```

**create.blade.php** - **REQUIERE JAVASCRIPT**
```blade
<!-- Formulario de venta:
  - client_id (select)
  - Items dinámicos (agregar/quitar filas con JavaScript)
    - product_id (select)
    - cantidad (number)
    - precio_unitario (number, autocompletar del precio actual del producto)
  - transport_id (select opcional)
  - total (calculado automáticamente con JavaScript)
-->
```

**NOTA**: Necesita JavaScript para:
- Agregar/quitar filas de items dinámicamente
- Calcular precio unitario al seleccionar producto
- Calcular total automáticamente (suma items + transporte si aplica)

**show.blade.php**
```blade
<!-- Información de la venta -->
<!-- Datos del cliente -->
<!-- Tabla de items vendidos -->
<!-- Total y transporte -->
<!-- Botón imprimir/exportar PDF (OPCIONAL) -->
```

---

## 🎨 COMPONENTES TAILWIND REUTILIZABLES

### Crear componentes blade para reutilizar:

**resources/views/components/alert.blade.php**
```blade
@props(['type' => 'success'])

@php
    $classes = [
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
        'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
    ][$type];
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-lg border $classes"]) }}>
    {{ $slot }}
</div>
```

**resources/views/components/button-link.blade.php**
```blade
@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
```

---

## 🚀 COMANDOS ÚTILES

### Iniciar servidor
```bash
php artisan serve
```

### Compilar assets (Tailwind)
```bash
npm run dev    # Modo desarrollo
npm run build  # Modo producción
```

### Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📝 PATRÓN DE VISTAS

### Estructura básica de una vista index:

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nombre del Módulo
            </h2>
            @can('create', App\Models\ModelName::class)
                <a href="{{ route('route.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Nuevo
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes flash -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabla -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Thead y tbody -->
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
```

### Estructura básica de formulario (create/edit):

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($item) ? 'Editar' : 'Crear' }} Nombre
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ isset($item) ? route('route.update', $item) : route('route.store') }}">
                        @csrf
                        @if(isset($item))
                            @method('PUT')
                        @endif

                        <!-- Campos del formulario -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Campo
                            </label>
                            <input type="text" name="campo" value="{{ old('campo', $item->campo ?? '') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('campo') border-red-500 @enderror">
                            @error('campo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('route.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 🔧 CONTROLADORES PENDIENTES DE IMPLEMENTAR

### PaperCoilController
Seguir el mismo patrón que ProductController

### ProductionOrderController
```php
// Agregar métodos especiales:
public function start(ProductionOrder $order) {
    $order->update(['estado' => 'produccion', 'started_at' => now()]);
}

public function pause(ProductionOrder $order) {
    $order->update(['estado' => 'pausada']);
}

public function finish(ProductionOrder $order) {
    $order->update(['estado' => 'finalizada', 'finished_at' => now()]);
}
```

### SaleController
```php
// El store debe:
// 1. Crear la venta
// 2. Crear los sale_items
// 3. Restar stock de productos
// 4. Crear movimientos de stock
```

---

## 🎯 PRIORIDADES DE DESARROLLO - ESTADO ACTUAL

### ✅ COMPLETADO (11-Ene-2026)
1. ✅ **COMPLETADO**: Navegación con todos los módulos y roles
2. ✅ **COMPLETADO**: Vistas de Usuarios completas (index, create, edit, show)
3. ✅ **COMPLETADO**: Vistas de Productos completas (index, create, edit, show)
4. ✅ **COMPLETADO**: Vistas de Clientes completas (index, create, edit, show)
5. ✅ **COMPLETADO**: Vistas de Transportes completas (index, create, edit)
6. ✅ **COMPLETADO**: GitIgnore actualizado y proyecto listo para Git

### ⏳ PENDIENTE - SIGUIENTE SESIÓN
1. **ALTA**: Implementar PaperCoilController y crear vistas de Bobinas
2. **ALTA**: Implementar ProductionOrderController con métodos especiales (start, pause, finish)
3. **ALTA**: Crear vistas de Órdenes de Producción
4. **ALTA**: Implementar SaleController completo con lógica de:
   - Validación de stock
   - Creación de sale_items
   - Actualización de stock
   - Creación de movimientos de stock
5. **ALTA**: Crear vistas de Ventas (requiere JavaScript para items dinámicos)
6. **MEDIA**: Crear archivo JavaScript para formulario de ventas dinámico
7. **BAJA**: Reportes y estadísticas adicionales
8. **BAJA**: Exportar ventas a PDF (opcional)

---

## 🔐 CREDENCIALES DE ACCESO

```
Gestor (Administrador):
- Email: admin@packsys.com
- Password: password
- Permisos: Acceso total

Vendedor:
- Email: vendedor@packsys.com
- Password: password
- Permisos: Clientes, Ventas, Ver Productos

Operario:
- Email: operario@packsys.com
- Password: password
- Permisos: Bobinas, Órdenes de Producción, Ver Productos
```

---

## 📦 PARA HOSTINGER

### Preparación para producción:

1. **Archivo .env**:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Base de datos de Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

2. **Compilar assets**:
```bash
npm run build
```

3. **Optimizar**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. **Subir archivos**:
- Subir todo excepto: `/node_modules`, `/vendor`, `.env`
- En el servidor ejecutar: `composer install --no-dev`
- Copiar `.env.example` a `.env` y configurar
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed --class=UserSeeder`

---

## ✅ CHECKLIST FINAL

### FASE 1 - COMPLETADA ✅
- [x] Actualizar navegación con roles
- [x] Crear vistas de usuarios (index, create, edit, show)
- [x] Crear vistas de clientes (index, create, edit, show)
- [x] Crear vistas de productos (index, create, edit, show)
- [x] Crear vistas de transportes (index, create, edit)
- [x] Actualizar GitIgnore para repositorio
- [x] UserController implementado y funcional
- [x] ClientController implementado y funcional
- [x] ProductController implementado y funcional
- [x] TransportController implementado y funcional

### FASE 2 - PENDIENTE
- [ ] Implementar PaperCoilController completo
- [ ] Crear vistas de bobinas (index, create, edit, show)
- [ ] Implementar ProductionOrderController con métodos especiales
- [ ] Crear vistas de órdenes de producción (index, create, edit, show)
- [ ] Implementar SaleController con lógica de stock
- [ ] Crear vistas de ventas (index, create, show)
- [ ] Crear JavaScript para formulario dinámico de ventas
- [ ] Probar todos los CRUDs

### FASE 3 - DEPLOYMENT
- [ ] Compilar assets: `npm run build`
- [ ] Optimizar para producción: `php artisan optimize`
- [ ] Configurar .env para producción
- [ ] Subir a Hostinger
- [ ] Ejecutar migraciones en producción
- [ ] Crear usuarios iniciales en producción

---

**Contacto**: Cualquier duda, consulta este archivo y PROJECT_MEMORY.md
