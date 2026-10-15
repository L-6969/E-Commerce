<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.products.create', ['categories' => $this->categories()]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product, 'categories' => $this->categories()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->productRules());
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }
        unset($data['photo']);
        $data['is_active'] = true;

        Product::create($data);

        return redirect()->route('admin.products')->with('ok', 'Produk ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->productRules());
        $oldPhoto = $product->photo_path;
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request->file('photo'));
        }
        unset($data['photo']);
        $product->update($data);
        if (isset($data['photo_path']) && $oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()->route('admin.products')->with('ok', 'Produk diperbarui.');
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['stock' => ['required', 'integer', 'min:0', 'max:1000000']]);
        if ((int) $data['stock'] === 0) {
            $data['is_available'] = false;
        }
        $product->update($data);

        return back()->with('ok', 'Stok produk diperbarui.');
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['field' => ['required', Rule::in(['is_available', 'is_active'])]]);
        abort_unless($data['field'] !== 'is_active' || $request->user()->role === 'owner', 403);
        $field = $data['field'];
        $product->update([$field => ! $product->$field]);

        return back()->with('ok', 'Ketersediaan diperbarui.');
    }

    private function categories(): \Illuminate\Support\Collection
    {
        return collect(['Cilok', 'Tambahan', 'Minuman'])
            ->merge(Product::query()->distinct()->orderBy('category')->pluck('category'))
            ->unique()->values();
    }

    private function productRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:1'],
            'emoji' => ['nullable', 'string', 'max:16'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_available' => ['required', 'boolean'],
            'photo' => ['nullable', 'file', 'image', 'mimes:webp', 'extensions:webp', 'max:200'],
        ];
    }

    private function storePhoto(UploadedFile $photo): string
    {
        if (! function_exists('imagecreatefromwebp') || ! function_exists('imagewebp')) {
            return $photo->store('products', 'public');
        }

        $info = @getimagesize($photo->getRealPath());
        if (! $info || $info[0] < 1 || $info[1] < 1 || $info[0] > 6000 || $info[1] > 6000 || $info[0] * $info[1] > 20000000) {
            throw ValidationException::withMessages(['photo' => 'Dimensi foto terlalu besar atau tidak valid.']);
        }

        $source = @imagecreatefromwebp($photo->getRealPath());
        if ($source === false) {
            throw ValidationException::withMessages(['photo' => 'Foto WebP tidak dapat dibaca.']);
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, 1600 / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        $working = imagecreatetruecolor($width, $height);
        imagealphablending($working, false);
        imagesavealpha($working, true);
        imagecopyresampled($working, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $bytes = '';
        for ($attempt = 0; $attempt < 8; $attempt++) {
            ob_start();
            $encoded = imagewebp($working, null, max(30, 82 - $attempt * 8));
            $bytes = ob_get_clean();
            if (! $encoded) {
                imagedestroy($working);
                imagedestroy($source);
                throw ValidationException::withMessages(['photo' => 'Foto WebP gagal diproses.']);
            }
            if (strlen($bytes) <= 204800 || max($width, $height) <= 256) {
                break;
            }
            $nextWidth = max(1, (int) round($width * 0.8));
            $nextHeight = max(1, (int) round($height * 0.8));
            $smaller = imagecreatetruecolor($nextWidth, $nextHeight);
            imagealphablending($smaller, false);
            imagesavealpha($smaller, true);
            imagecopyresampled($smaller, $working, 0, 0, 0, 0, $nextWidth, $nextHeight, $width, $height);
            imagedestroy($working);
            $working = $smaller;
            $width = $nextWidth;
            $height = $nextHeight;
        }
        imagedestroy($working);
        imagedestroy($source);

        if (strlen($bytes) > 204800) {
            throw ValidationException::withMessages(['photo' => 'Foto tidak dapat dikompres hingga maksimal 200 KB.']);
        }

        $path = 'products/'.Str::uuid().'.webp';
        if (! Storage::disk('public')->put($path, $bytes)) {
            throw ValidationException::withMessages(['photo' => 'Foto gagal disimpan.']);
        }

        return $path;
    }
}
