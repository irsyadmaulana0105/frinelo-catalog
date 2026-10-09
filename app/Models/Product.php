<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    public const SIZES = ['All Size', 'S', 'M', 'L', 'XL'];

    /** Jumlah maksimal foto tambahan (di luar foto sampul). */
    public const MAX_GALLERY = 5;

    protected $fillable = [
        'name', 'price', 'category', 'sizes', 'colors',
        'image_url', 'gallery', 'description', 'is_active',
    ];

    protected $casts = [
        'sizes'     => 'array',
        'colors'    => 'array',
        'gallery'   => 'array',
        'price'     => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_src', 'gallery_src'];

    /** URL penuh dipakai apa adanya; path relatif diarahkan ke storage publik. */
    public static function urlFor(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Str::startsWith($value, ['http://', 'https://'])
            ? $value
            : asset('storage/' . $value);
    }

    /** Hapus file hanya jika berupa file upload lokal (bukan URL luar). */
    public static function deleteFile(?string $value): void
    {
        if ($value && ! Str::startsWith($value, ['http://', 'https://'])) {
            Storage::disk('public')->delete($value);
        }
    }

    protected function imageSrc(): Attribute
    {
        return Attribute::get(fn () => static::urlFor($this->image_url));
    }

    protected function gallerySrc(): Attribute
    {
        return Attribute::get(
            fn () => array_map(fn ($path) => static::urlFor($path), $this->gallery ?? [])
        );
    }

    public function deleteStoredImage(): void
    {
        static::deleteFile($this->image_url);
    }

    public function deleteStoredGallery(): void
    {
        foreach ($this->gallery ?? [] as $path) {
            static::deleteFile($path);
        }
    }
}
