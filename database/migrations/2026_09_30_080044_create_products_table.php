<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('price');        // Rupiah, tanpa desimal
            $table->string('category');              // Tanktop, Rajut, Cardigan, dst.
            $table->json('sizes');                   // ["All Size"] atau ["S","M","L"]
            $table->json('colors');                  // ["Pink","Cream"]
            $table->string('image_url')->nullable(); // URL penuh ATAU path file upload
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};