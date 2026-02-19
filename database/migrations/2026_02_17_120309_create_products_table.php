<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('title');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->string('location')->nullable(); // shown on product page
            $table->string('currency', 5)->default('USD');

            $table->decimal('price', 12, 2)->nullable();        // e.g. 6.20
            $table->decimal('old_price', 12, 2)->nullable();    // e.g. 8.50 (optional)
            $table->unsignedInteger('moq')->nullable();         // Minimum order quantity

            $table->boolean('is_deal')->default(false);
            $table->boolean('is_active')->default(true);

            // publishing
            $table->enum('status', ['draft','published','archived'])->default('draft');

            // optional metadata
            $table->json('ideal_for')->nullable(); // ["Retailers","Importers",...]
            $table->json('tags')->nullable();      // optional

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
