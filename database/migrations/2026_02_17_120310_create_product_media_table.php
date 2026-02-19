<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->enum('type', ['image','video']);
            $table->string('path'); // storage path e.g. products/1/images/xxx.jpg
            $table->string('thumbnail_path')->nullable(); // optional later for video thumbnails
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['product_id','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
