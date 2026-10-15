<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'@example.test',
            'password' => 'test-password',
            'role' => $role,
        ]);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Cilok Original', 'description' => 'Cilok sapi', 'category' => 'Cilok',
            'price' => 10000, 'emoji' => '🍢', 'stock' => 10, 'is_available' => '1',
        ], $overrides);
    }

    private function webpUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'product-webp-');
        $image = imagecreatetruecolor(8, 8);
        imagefilledrectangle($image, 0, 0, 7, 7, imagecolorallocate($image, 20, 80, 40));
        imagewebp($image, $path);
        imagedestroy($image);
        $contents = file_get_contents($path);
        unlink($path);

        return UploadedFile::fake()->createWithContent('product.webp', $contents);
    }

    public function test_owner_can_create_product_and_webp_photo_is_stored(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user('owner'))->post(route('admin.products.store'), array_merge(
            $this->productData(), ['photo' => $this->webpUpload()],
        ))->assertRedirect(route('admin.products'));

        $product = Product::firstOrFail();
        $this->assertSame(10000, $product->price);
        $this->assertStringEndsWith('.webp', $product->photo_path);
        Storage::disk('public')->assertExists($product->photo_path);
        $this->assertLessThanOrEqual(204800, Storage::disk('public')->size($product->photo_path));
    }

    public function test_owner_can_edit_product(): void
    {
        $owner = $this->user('owner');
        $product = Product::create(['name' => 'Nama Lama', 'category' => 'Cilok', 'price' => 10000, 'stock' => 5]);

        $this->actingAs($owner)->put(route('admin.products.update', $product), $this->productData([
            'name' => 'Nama Baru', 'price' => 15000,
        ]))->assertRedirect(route('admin.products'));

        $this->assertSame('Nama Baru', $product->fresh()->name);
        $this->assertSame(15000, $product->fresh()->price);
    }

    public function test_staff_can_update_stock_but_cannot_edit_product(): void
    {
        $staff = $this->user('staff');
        $product = Product::create(['name' => 'Cilok', 'category' => 'Cilok', 'price' => 10000, 'stock' => 5]);

        $this->actingAs($staff)->patch(route('admin.products.stock', $product), ['stock' => 0])->assertRedirect();
        $this->assertSame(0, $product->fresh()->stock);
        $this->assertFalse($product->fresh()->is_available);
        $this->actingAs($staff)->put(route('admin.products.update', $product), $this->productData(['price' => 1]))
            ->assertForbidden();
        $this->assertSame(10000, $product->fresh()->price);
    }

    public function test_staff_can_change_availability_but_cannot_activate_product(): void
    {
        $staff = $this->user('staff');
        $product = Product::create(['name' => 'Cilok', 'category' => 'Cilok', 'price' => 10000, 'stock' => 5]);

        $this->actingAs($staff)->patch(route('admin.products.toggle', $product), ['field' => 'is_available'])->assertRedirect();
        $this->assertFalse($product->fresh()->is_available);
        $this->actingAs($staff)->patch(route('admin.products.toggle', $product), ['field' => 'is_active'])->assertForbidden();
        $this->assertTrue($product->fresh()->is_active);
    }

    public function test_owner_can_deactivate_product_without_deleting_it(): void
    {
        $owner = $this->user('owner');
        $product = Product::create(['name' => 'Cilok', 'category' => 'Cilok', 'price' => 10000, 'stock' => 5]);

        $this->actingAs($owner)->patch(route('admin.products.toggle', $product), ['field' => 'is_active'])->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => false]);
    }

    public function test_staff_cannot_create_product_and_invalid_image_is_rejected(): void
    {
        $staff = $this->user('staff');
        $this->actingAs($staff)->post(route('admin.products.store'), $this->productData())->assertForbidden();
        $this->assertDatabaseCount('products', 0);

        Storage::fake('public');
        $this->actingAs($this->user('owner'))->from(route('admin.products.create'))
            ->post(route('admin.products.store'), array_merge($this->productData(), [
                'photo' => UploadedFile::fake()->image('not-webp.jpg'),
            ]))->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('products', 0);
    }
}
