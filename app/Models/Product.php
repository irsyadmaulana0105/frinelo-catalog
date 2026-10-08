<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    public const SIZES = ['All Size', 'S', 'M', 'L', 'XL'];

    protected $fillable = [
        'name', 'price', 'category', 'sizes', 'colors',
        'image_url', 'description', 'is_active',
    ];

    protected $casts = [
        'sizes'     => 'array',
        'colors'    => 'array',
        'price'     => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_src'];

    // Mendukung link gambar eksternal maupun file yang diupload admin
    protected function imageSrc(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->image_url) {
                return null;
            }

            return Str::startsWith($this->image_url, ['http://', 'https://'])
                ? $this->image_url
                : asset('storage/' . $this->image_url);
        });
    }

    public function deleteStoredImage(): void
    {
        if ($this->image_url && ! Str::startsWith($this->image_url, ['http://', 'https://'])) {
            Storage::disk('public')->delete($this->image_url);
        }
    }
}
