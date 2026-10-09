<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dijaga middleware 'auth'
    }

    protected function prepareForValidation(): void
    {
        // Warna boleh dikirim sebagai teks dipisah koma: "Hitam, Putih, Dusty Pink"
        if (is_string($this->colors)) {
            $this->merge([
                'colors' => collect(explode(',', $this->colors))
                    ->map(fn ($c) => trim($c))->filter()->values()->all(),
            ]);
        }

        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:120'],
            'price'          => ['required', 'integer', 'min:0'],
            'category'       => ['required', 'string', 'max:60'],
            'sizes'          => ['required', 'array', 'min:1'],
            'sizes.*'        => ['string', Rule::in(Product::SIZES)],
            'colors'         => ['required', 'array', 'min:1'],
            'colors.*'       => ['string', 'max:30'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'image'          => ['nullable', 'image', 'max:2048'], // foto sampul, maks 2 MB
            'gallery_new'    => ['nullable', 'array', 'max:' . Product::MAX_GALLERY],
            'gallery_new.*'  => ['image', 'max:2048'],
            'keep_gallery'   => ['nullable', 'array'],
            'keep_gallery.*' => ['string'],
            'is_active'      => ['boolean'],
        ];
    }
}
