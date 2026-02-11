<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $gestor;
    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->vendedor = User::factory()->create(['role' => 'vendedor']);
    }

    public function test_usuario_no_autenticado_no_puede_ver_productos(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_usuario_autenticado_puede_ver_lista_productos(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->vendedor)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    public function test_lista_no_muestra_productos_eliminados(): void
    {
        $productoActivo = Product::factory()->create(['eliminado' => false]);
        $productoEliminado = Product::factory()->create(['eliminado' => true]);

        $response = $this->actingAs($this->vendedor)->get(route('products.index'));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertTrue($products->contains('id', $productoActivo->id));
        $this->assertFalse($products->contains('id', $productoEliminado->id));
    }

    public function test_gestor_puede_ver_formulario_crear_producto(): void
    {
        ProductType::factory()->create();
        Proveedor::factory()->create();
        Unidad::factory()->create();

        $response = $this->actingAs($this->gestor)->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('products.create');
        $response->assertViewHas(['proveedores', 'productTypes', 'unidades']);
    }

    public function test_vendedor_no_puede_ver_formulario_crear_producto(): void
    {
        $response = $this->actingAs($this->vendedor)->get(route('products.create'));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_crear_producto(): void
    {
        $productType = ProductType::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $unidad = Unidad::factory()->create();

        $data = [
            'name' => 'Producto de Prueba',
            'descripcion' => 'Descripción del producto',
            'product_type_id' => $productType->id,
            'proveedor_id' => $proveedor->id,
            'unidad_id' => $unidad->id,
            'stock_actual' => 100,
            'stock_minimo' => 10,
            'usa_bobina' => false,
            'costo' => 50.00,
            'precio_venta' => 75.00,
        ];

        $response = $this->actingAs($this->gestor)->post(route('products.store'), $data);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Producto de Prueba',
            'created_by' => $this->gestor->id,
        ]);

        $this->assertDatabaseHas('product_prices', [
            'costo' => 50.00,
            'precio_venta' => 75.00,
        ]);
    }

    public function test_crear_producto_con_bobina_requiere_medidas(): void
    {
        $productType = ProductType::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $unidad = Unidad::factory()->create();

        $data = [
            'name' => 'Producto con Bobina',
            'product_type_id' => $productType->id,
            'proveedor_id' => $proveedor->id,
            'unidad_id' => $unidad->id,
            'stock_actual' => 100,
            'stock_minimo' => 10,
            'usa_bobina' => 1,
            'costo' => 50.00,
            'precio_venta' => 75.00,
        ];

        $response = $this->actingAs($this->gestor)->post(route('products.store'), $data);

        $response->assertSessionHasErrors(['ancho', 'largo', 'fuelle']);
    }

    public function test_usuario_puede_ver_detalle_producto(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product');
    }

    public function test_gestor_puede_editar_producto(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->gestor)->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
    }

    public function test_vendedor_no_puede_editar_producto(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->vendedor)->get(route('products.edit', $product));

        $response->assertStatus(403);
    }

    public function test_gestor_puede_actualizar_producto(): void
    {
        $product = Product::factory()->create();
        ProductPrice::factory()->create(['product_id' => $product->id]);

        $data = [
            'name' => 'Producto Actualizado',
            'descripcion' => 'Nueva descripción',
            'product_type_id' => $product->product_type_id,
            'proveedor_id' => $product->proveedor_id,
            'unidad_id' => $product->unidad_id,
            'stock_actual' => 200,
            'stock_minimo' => 20,
            'costo' => 60.00,
            'precio_venta' => 90.00,
        ];

        $response = $this->actingAs($this->gestor)->put(route('products.update', $product), $data);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
        ]);
    }

    public function test_actualizar_precio_crea_nuevo_registro_precio(): void
    {
        $product = Product::factory()->create();
        $originalPrice = ProductPrice::factory()->create([
            'product_id' => $product->id,
            'costo' => 50.00,
            'precio_venta' => 75.00,
        ]);

        $data = [
            'name' => $product->name,
            'product_type_id' => $product->product_type_id,
            'proveedor_id' => $product->proveedor_id,
            'unidad_id' => $product->unidad_id,
            'stock_actual' => $product->stock_actual,
            'stock_minimo' => $product->stock_minimo,
            'costo' => 100.00,
            'precio_venta' => 150.00,
        ];

        $this->actingAs($this->gestor)->put(route('products.update', $product), $data);

        $this->assertEquals(2, $product->fresh()->prices()->count());
    }

    public function test_gestor_puede_eliminar_producto(): void
    {
        $product = Product::factory()->create(['eliminado' => false]);

        $response = $this->actingAs($this->gestor)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertTrue($product->fresh()->eliminado == 1);
    }

    public function test_vendedor_no_puede_eliminar_producto(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->vendedor)->delete(route('products.destroy', $product));

        $response->assertStatus(403);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $productType = ProductType::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $unidad = Unidad::factory()->create();

        $data = [
            'name' => '',
            'product_type_id' => $productType->id,
            'proveedor_id' => $proveedor->id,
            'unidad_id' => $unidad->id,
            'stock_actual' => 100,
            'stock_minimo' => 10,
            'costo' => 50.00,
            'precio_venta' => 75.00,
        ];

        $response = $this->actingAs($this->gestor)->post(route('products.store'), $data);

        $response->assertSessionHasErrors('name');
    }
}
