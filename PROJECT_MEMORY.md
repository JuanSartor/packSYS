# PackSYS - Memoria del Proyecto

## Descripción del Proyecto
Sistema de gestión de producción y ventas de productos de embalaje (bolsas de papel, friselina, cajas e insumos). El sistema maneja la producción, inventario, bobinas de papel, ventas y clientes.

## Stack Tecnológico
- **Framework**: Laravel 10.50.0
- **PHP**: 8.4.16
- **Base de Datos**: MySQL (WAMP64)
- **ORM**: Eloquent

## Configuración Importante

### AppServiceProvider
- Configuración de longitud de string a 191 caracteres para compatibilidad con MySQL
- Ubicación: `app/Providers/AppServiceProvider.php`

```php
Schema::defaultStringLength(191);
```

## Estructura de Base de Datos

### Tablas Principales

#### 1. **users**
Usuarios del sistema con roles diferenciados.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `name`: VARCHAR(100)
- `email`: VARCHAR(150) UNIQUE
- `password`: VARCHAR(255)
- `role`: ENUM('operario', 'vendedor', 'gestor')
- `email_verified_at`: TIMESTAMP NULL
- `remember_token`: VARCHAR(100) NULL
- `created_at`, `updated_at`: TIMESTAMP NULL

**Roles:**
- **operario**: Maneja producción y empaquetado
- **vendedor**: Gestiona ventas y clientes
- **gestor**: Administrador del sistema

#### 2. **clients**
Clientes del negocio.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `nombre`: VARCHAR(150)
- `telefono`: VARCHAR(50) NULL
- `email`: VARCHAR(150) NULL
- `direccion`: VARCHAR(255) NULL
- `created_at`, `updated_at`: TIMESTAMP NULL

#### 3. **products**
Productos que fabrica/vende la empresa.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `name`: VARCHAR(150)
- `type`: ENUM('bolsa_papel', 'friselina', 'caja', 'insumo')
- `unidad`: ENUM('unidad', 'kg', 'metro') DEFAULT 'unidad'
- `stock_actual`: DECIMAL(10,2) DEFAULT 0
- `stock_minimo`: DECIMAL(10,2) DEFAULT 0
- `usa_bobina`: BOOLEAN DEFAULT FALSE
- `created_at`, `updated_at`: TIMESTAMP NULL

**Tipos de Productos:**
- **bolsa_papel**: Bolsas de papel
- **friselina**: Bolsas de friselina
- **caja**: Cajas de cartón
- **insumo**: Insumos varios

#### 4. **paper_coils**
Bobinas de papel para producción.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `tipo_papel`: VARCHAR(100) NULL
- `ancho`: DECIMAL(8,2) NULL
- `gramaje`: DECIMAL(8,2) NULL
- `peso_inicial`: DECIMAL(10,2)
- `peso_actual`: DECIMAL(10,2)
- `alerta_minima`: DECIMAL(10,2) DEFAULT 0
- `created_at`, `updated_at`: TIMESTAMP NULL

#### 5. **product_materials**
Relación entre productos y bobinas (qué bobina usa cada producto).

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `product_id`: BIGINT UNSIGNED (FK → products)
- `paper_coil_id`: BIGINT UNSIGNED (FK → paper_coils)
- `consumo_por_unidad`: DECIMAL(10,4)

**Sin timestamps**

#### 6. **stock_movements**
Movimientos de inventario (entrada/salida/ajuste).

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `product_id`: BIGINT UNSIGNED NULL (FK → products)
- `paper_coil_id`: BIGINT UNSIGNED NULL (FK → paper_coils)
- `tipo`: ENUM('entrada', 'salida', 'ajuste')
- `cantidad`: DECIMAL(10,2)
- `referencia`: VARCHAR(50) NULL
- `referencia_id`: BIGINT UNSIGNED NULL
- `created_at`: TIMESTAMP (auto)

**Solo created_at, sin updated_at**

#### 7. **production_orders**
Órdenes de producción de productos.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `product_id`: BIGINT UNSIGNED (FK → products)
- `cantidad`: INT
- `estado`: ENUM('espera', 'pendiente', 'produccion', 'pausada', 'finalizada') DEFAULT 'espera'
- `created_by`: BIGINT UNSIGNED NULL (FK → users)
- `started_at`: TIMESTAMP NULL
- `finished_at`: TIMESTAMP NULL
- `created_at`, `updated_at`: TIMESTAMP NULL

**Estados de Orden:**
- **espera**: Esperando iniciar
- **pendiente**: Pendiente de comenzar
- **produccion**: En proceso de producción
- **pausada**: Pausada temporalmente
- **finalizada**: Completada

#### 8. **production_times**
Registro de tiempos de producción.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `production_order_id`: BIGINT UNSIGNED (FK → production_orders)
- `inicio`: TIMESTAMP
- `fin`: TIMESTAMP NULL

**Sin timestamps**

#### 9. **sales**
Ventas realizadas.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `client_id`: BIGINT UNSIGNED NULL (FK → clients)
- `total`: DECIMAL(12,2)
- `transporte`: BOOLEAN DEFAULT FALSE
- `created_by`: BIGINT UNSIGNED NULL (FK → users)
- `created_at`, `updated_at`: TIMESTAMP NULL

#### 10. **sale_items**
Items/productos de cada venta.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `sale_id`: BIGINT UNSIGNED (FK → sales)
- `product_id`: BIGINT UNSIGNED (FK → products)
- `cantidad`: INT
- `precio_unitario`: DECIMAL(10,2)

**Sin timestamps**

#### 11. **product_prices**
Historial de precios de productos.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `product_id`: BIGINT UNSIGNED (FK → products)
- `costo`: DECIMAL(10,2) NULL
- `precio_venta`: DECIMAL(10,2) NULL
- `vigente_desde`: TIMESTAMP (auto)

**Sin timestamps**

#### 12. **transports**
Catálogo de transportes disponibles.

**Campos:**
- `id`: BIGINT UNSIGNED (PK)
- `nombre`: VARCHAR(100) NULL
- `costo`: DECIMAL(10,2) NULL

**Sin timestamps**

## Modelos Eloquent y Relaciones

### User
**Ubicación**: `app/Models/User.php`

**Constantes:**
```php
const ROLE_OPERARIO = 'operario';
const ROLE_VENDEDOR = 'vendedor';
const ROLE_GESTOR = 'gestor';
```

**Relaciones:**
- `productionOrders()` - HasMany → ProductionOrder (created_by)
- `sales()` - HasMany → Sale (created_by)

**Métodos Helper:**
- `isOperario()`: bool
- `isVendedor()`: bool
- `isGestor()`: bool

**Scopes:**
- `scopeRole($role)` - Filtrar por rol específico
- `scopeOperarios()` - Solo operarios
- `scopeVendedores()` - Solo vendedores
- `scopeGestores()` - Solo gestores

**Fillable:**
`name`, `email`, `password`, `role`

---

### Client
**Ubicación**: `app/Models/Client.php`

**Relaciones:**
- `sales()` - HasMany → Sale

**Fillable:**
`nombre`, `telefono`, `email`, `direccion`

---

### Product
**Ubicación**: `app/Models/Product.php`

**Relaciones:**
- `productionOrders()` - HasMany → ProductionOrder
- `stockMovements()` - HasMany → StockMovement
- `saleItems()` - HasMany → SaleItem
- `prices()` - HasMany → ProductPrice
- `materials()` - HasMany → ProductMaterial
- `paperCoils()` - BelongsToMany → PaperCoil (through product_materials)

**Métodos Especiales:**
- `currentPrice()` - Obtiene el precio actual vigente

**Fillable:**
`name`, `type`, `unidad`, `stock_actual`, `stock_minimo`, `usa_bobina`

**Casts:**
- `stock_actual`: decimal:2
- `stock_minimo`: decimal:2
- `usa_bobina`: boolean

---

### PaperCoil
**Ubicación**: `app/Models/PaperCoil.php`

**Relaciones:**
- `materials()` - HasMany → ProductMaterial
- `stockMovements()` - HasMany → StockMovement
- `products()` - BelongsToMany → Product (through product_materials)

**Fillable:**
`tipo_papel`, `ancho`, `gramaje`, `peso_inicial`, `peso_actual`, `alerta_minima`

**Casts:**
Todos los campos numéricos: decimal:2

---

### ProductMaterial
**Ubicación**: `app/Models/ProductMaterial.php`

**Relaciones:**
- `product()` - BelongsTo → Product
- `paperCoil()` - BelongsTo → PaperCoil

**Fillable:**
`product_id`, `paper_coil_id`, `consumo_por_unidad`

**Casts:**
- `consumo_por_unidad`: decimal:4

**Timestamps:** No tiene

---

### StockMovement
**Ubicación**: `app/Models/StockMovement.php`

**Relaciones:**
- `product()` - BelongsTo → Product
- `paperCoil()` - BelongsTo → PaperCoil

**Fillable:**
`product_id`, `paper_coil_id`, `tipo`, `cantidad`, `referencia`, `referencia_id`

**Casts:**
- `cantidad`: decimal:2

**Timestamps:** Solo created_at (const UPDATED_AT = null)

---

### ProductionOrder
**Ubicación**: `app/Models/ProductionOrder.php`

**Relaciones:**
- `product()` - BelongsTo → Product
- `creator()` - BelongsTo → User (created_by)
- `productionTimes()` - HasMany → ProductionTime

**Fillable:**
`product_id`, `cantidad`, `estado`, `created_by`, `started_at`, `finished_at`

**Casts:**
- `started_at`: datetime
- `finished_at`: datetime

---

### ProductionTime
**Ubicación**: `app/Models/ProductionTime.php`

**Relaciones:**
- `productionOrder()` - BelongsTo → ProductionOrder

**Fillable:**
`production_order_id`, `inicio`, `fin`

**Casts:**
- `inicio`: datetime
- `fin`: datetime

**Timestamps:** No tiene

---

### Sale
**Ubicación**: `app/Models/Sale.php`

**Relaciones:**
- `client()` - BelongsTo → Client
- `creator()` - BelongsTo → User (created_by)
- `items()` - HasMany → SaleItem

**Fillable:**
`client_id`, `total`, `transporte`, `created_by`

**Casts:**
- `total`: decimal:2
- `transporte`: boolean

---

### SaleItem
**Ubicación**: `app/Models/SaleItem.php`

**Relaciones:**
- `sale()` - BelongsTo → Sale
- `product()` - BelongsTo → Product

**Fillable:**
`sale_id`, `product_id`, `cantidad`, `precio_unitario`

**Casts:**
- `precio_unitario`: decimal:2

**Timestamps:** No tiene

---

### ProductPrice
**Ubicación**: `app/Models/ProductPrice.php`

**Relaciones:**
- `product()` - BelongsTo → Product

**Fillable:**
`product_id`, `costo`, `precio_venta`, `vigente_desde`

**Casts:**
- `costo`: decimal:2
- `precio_venta`: decimal:2
- `vigente_desde`: datetime

**Timestamps:** No tiene

---

### Transport
**Ubicación**: `app/Models/Transport.php`

**Sin relaciones**

**Fillable:**
`nombre`, `costo`

**Casts:**
- `costo`: decimal:2

**Timestamps:** No tiene

---

## Diagrama de Relaciones

```
User (role: operario/vendedor/gestor)
├── productionOrders (created_by)
└── sales (created_by)

Client
└── sales

Product
├── productionOrders
├── stockMovements
├── saleItems
├── prices
├── materials
└── paperCoils (many-to-many via product_materials)

PaperCoil
├── materials
├── stockMovements
└── products (many-to-many via product_materials)

ProductionOrder
├── product
├── creator (User)
└── productionTimes

Sale
├── client
├── creator (User)
└── items

SaleItem
├── sale
└── product

ProductPrice
└── product

StockMovement
├── product (nullable)
└── paperCoil (nullable)

ProductMaterial
├── product
└── paperCoil

ProductionTime
└── productionOrder

Transport
(sin relaciones)
```

## Migraciones Ejecutadas

1. `2014_10_12_000000_create_users_table`
2. `2014_10_12_100000_create_password_reset_tokens_table`
3. `2019_08_19_000000_create_failed_jobs_table`
4. `2019_12_14_000001_create_personal_access_tokens_table`
5. `2026_01_11_223126_add_role_to_users_table`
6. `2026_01_11_224518_create_clients_table`
7. `2026_01_11_224518_create_products_table`
8. `2026_01_11_224519_create_paper_coils_table`
9. `2026_01_11_224520_create_transports_table`
10. `2026_01_11_224527_create_product_materials_table`
11. `2026_01_11_224528_create_production_orders_table`
12. `2026_01_11_224528_create_stock_movements_table`
13. `2026_01_11_224529_create_production_times_table`
14. `2026_01_11_224530_create_sale_items_table`
15. `2026_01_11_224530_create_sales_table`
16. `2026_01_11_224531_create_product_prices_table`

## Archivos Importantes

- **Esquema de BD Original**: `ss (2).txt` - Contiene la definición SQL original de todas las tablas
- **Configuración de Schema**: `app/Providers/AppServiceProvider.php` - Longitud de strings
- **Migración de Roles**: `database/migrations/2026_01_11_223126_add_role_to_users_table.php`

## Convenciones del Proyecto

1. **Nombres de Tablas**: Plural en inglés (excepto casos especiales como clients, sales)
2. **Foreign Keys**: Nombradas como `{tabla_singular}_id`
3. **Timestamps**: Usar `created_at` y `updated_at` excepto en tablas de relación
4. **Decimales**: Usar DECIMAL(10,2) para cantidades y precios
5. **Estados**: Usar ENUM para estados definidos
6. **Soft Deletes**: No implementado actualmente

## Próximos Pasos Sugeridos

1. **Autenticación**: Implementar sistema de login con roles
2. **Seeders**: Crear seeders para datos de prueba
3. **Controllers**: Crear controladores para cada modelo
4. **API**: Definir rutas y endpoints de API
5. **Validaciones**: Implementar Form Requests para validaciones
6. **Middleware**: Crear middleware de roles para protección de rutas
7. **Frontend**: Decidir stack frontend (Vue/React/Blade)
8. **Testing**: Implementar tests unitarios y de integración

## Notas de Desarrollo

- El proyecto usa WAMP64, asegurarse de tener el servidor corriendo
- Base de datos configurada en `.env`
- PHP 8.4 puede mostrar warnings de deprecación en Composer (son normales)
- Usar `php artisan migrate:fresh` con precaución (borra todos los datos)
- Los timestamps en tablas de relación (pivot) están deshabilitados para optimización

## Comandos Útiles

```bash
# Ver estado de migraciones
php artisan migrate:status

# Crear nuevo modelo con migración
php artisan make:model NombreModelo -m

# Revertir última migración
php artisan migrate:rollback

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas
php artisan route:list

# Iniciar servidor de desarrollo
php artisan serve
```

---

**Última actualización**: 2026-01-11
**Versión Laravel**: 10.50.0
**Desarrollador**: Sistema PackSYS
