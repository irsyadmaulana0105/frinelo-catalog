<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', $this->formProps(null));
    }

    public function store(ProductRequest $request)
    {
        Product::create($this->payload($request));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Form', $this->formProps($product));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($this->payload($request, $product));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->deleteStoredImage();
        $product->delete();

        return back()->with('success', 'Produk dihapus.');
    }

    // Switch tampil/sembunyi di dashboard
    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back();
    }

    private function formProps(?Product $product): array
    {
        return [
            'product'     => $product,
            'categories'  => Product::query()->distinct()->orderBy('category')->pluck('category'),
            'sizeOptions' => Product::SIZES,
        ];
    }

    private function payload(ProductRequest $request, ?Product $product = null): array
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $product?->deleteStoredImage();
            $data['image_url'] = $request->file('image')->store('products', 'public');
        }

        return $data;
    }
}
