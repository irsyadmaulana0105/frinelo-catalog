<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->latest()->get();

        return Inertia::render('Catalog/Index', [
            'products'       => $products,
            'categories'     => $products->pluck('category')->unique()->sort()->values(),
            'whatsappNumber' => config('frinelo.whatsapp_number'),
            'shop'           => Arr::except(config('frinelo'), ['whatsapp_number']),
        ]);
    }
}
