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

---

## ⏳ LO QUE FALTA POR IMPLEMENTAR

### Vistas Principales a Crear

#### 1. Navegación Mejorada (PRIORIDAD ALTA)
**Archivo**: `resources/views/layouts/navigation.blade.php`

Actualizar la navegación con los siguientes items (según rol):

```blade
<!-- Todos los roles -->
- Dashboard
- Productos (Ver)

<!-- Solo Gestor -->
- Usuarios
- Productos (CRUD completo)
- Transportes
- Clientes

<!-- Gestor y Vendedor -->
- Clientes
- Ventas

<!-- Gestor y Operario -->
- Bobinas de Papel
- Órdenes de Producción
```

#### 2. Vistas de Usuarios
**Directorio**: `resources/views/users/`

**index.blade.php** - Lista de usuarios
```blade
<x-app-layout>
    <!-- Tabla con: ID, Nombre, Email, Rol, Acciones -->
    <!-- Botón "Nuevo Usuario" -->
    <!-- Paginación -->
</x-app-layout>
```

**create.blade.php** - Formulario crear usuario
```blade
<x-app-layout>
    <!-- Formulario con: nombre, email, password, role (select) -->
</x-app-layout>
```

**edit.blade.php** - Formulario editar usuario
```blade
<x-app-layout>
    <!-- Formulario similar a create, password opcional -->
</x-app-layout>
```

**show.blade.php** - Ver detalles usuario
```blade
<x-app-layout>
    <!-- Información del usuario -->
    <!-- Estadísticas (ventas si es vendedor, órdenes si es operario) -->
</x-app-layout>
```

#### 3. Vistas de Clientes
**Directorio**: `resources/views/clients/`

Misma estructura que usuarios:
- index.blade.php (lista con paginación)
- create.blade.php (formulario: nombre, teléfono, email, dirección)
- edit.blade.php
- show.blade.php (incluir ventas del cliente)

#### 4. Vistas de Productos
**Directorio**: `resources/views/products/`

**index.blade.php**
```blade
<!-- Tabla: Nombre, Tipo, Unidad, Stock Actual, Stock Mínimo, Usa Bobina -->
<!-- Mostrar en rojo si stock_actual <= stock_minimo -->
<!-- Botón "Nuevo Producto" solo para gestor -->
```

**create.blade.php**
```blade
<!-- Campos:
  - name (text)
  - type (select: bolsa_papel, friselina, caja, insumo)
  - unidad (select: unidad, kg, metro)
  - stock_actual (number)
  - stock_minimo (number)
  - usa_bobina (checkbox)
-->
```

**edit.blade.php** - Similar a create

**show.blade.php**
```blade
<!-- Info del producto -->
<!-- Historial de precios (tabla product_prices) -->
<!-- Movimientos de stock recientes -->
```

#### 5. Vistas de Transportes
**Directorio**: `resources/views/transports/`

Simple, solo index, create, edit:
- index: Tabla con nombre, costo
- create: Formulario nombre, costo
- edit: Igual que create

#### 6. Vistas de Bobinas de Papel (Paper Coils)
**Directorio**: `resources/views/paper-coils/`

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

#### 7. Vistas de Órdenes de Producción
**Directorio**: `resources/views/production-orders/`

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

**show.blade.php**
```blade
<!-- Detalles de la orden -->
<!-- Tiempos de producción (tabla production_times) -->
<!-- Botones para cambiar estado -->
```

#### 8. Vistas de Ventas
**Directorio**: `resources/views/sales/`

**index.blade.php**
```blade
<!-- Tabla: ID, Cliente, Total, Transporte, Vendedor, Fecha -->
<!-- Filtros por fecha y cliente -->
```

**create.blade.php**
```blade
<!-- Formulario de venta:
  - client_id (select o autocompletar)
  - Items dinámicos (agregar/quitar)
    - product_id (select)
    - cantidad (number)
    - precio_unitario (number, autocompletar del precio actual)
  - transporte (checkbox)
  - total (calculado automáticamente con JS)
-->
```

**show.blade.php**
```blade
<!-- Información de la venta -->
<!-- Tabla de items vendidos -->
<!-- Botón imprimir/exportar PDF -->
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

## 🎯 PRIORIDADES DE DESARROLLO

1. **ALTA**: Actualizar navegación con todos los módulos
2. **ALTA**: Vistas de Usuarios completas (ya tienes el controlador)
3. **ALTA**: Vistas de Productos
4. **ALTA**: Vistas de Clientes
5. **MEDIA**: Vistas de Ventas (más complejo por items dinámicos)
6. **MEDIA**: Vistas de Órdenes de Producción
7. **MEDIA**: Vistas de Bobinas de Papel
8. **BAJA**: Vistas de Transportes
9. **BAJA**: Reportes y estadísticas adicionales

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

- [ ] Actualizar navegación
- [ ] Crear vistas de usuarios (index, create, edit, show)
- [ ] Crear vistas de clientes
- [ ] Crear vistas de productos
- [ ] Crear vistas de transportes
- [ ] Crear vistas de bobinas
- [ ] Crear vistas de órdenes de producción
- [ ] Crear vistas de ventas
- [ ] Implementar controladores pendientes
- [ ] Probar todos los CRUDs
- [ ] Optimizar para producción
- [ ] Subir a Hostinger

---

**Contacto**: Cualquier duda, consulta este archivo y PROJECT_MEMORY.md
