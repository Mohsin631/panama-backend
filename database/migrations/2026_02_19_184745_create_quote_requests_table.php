<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_requests', function (Blueprint $table) {
      $table->id();

      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();

      $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

      // snapshot fields (keep product info even if product changes)
      $table->string('product_title')->nullable();

      // RFQ fields
      $table->unsignedInteger('quantity')->nullable();
      $table->string('unit')->nullable(); // pcs, kg, cartons, etc
      $table->string('shipping_country')->nullable();
      $table->string('shipping_city')->nullable();
      $table->text('note')->nullable();

      // vendor may quote
      $table->decimal('quoted_price', 12, 2)->nullable();
      $table->string('currency', 5)->default('USD');
      $table->unsignedInteger('quoted_moq')->nullable();

      // lifecycle
      $table->enum('status', [
        'open','negotiating','quoted','accepted','paid','shipped','closed','cancelled'
      ])->default('open');

      // useful flags
      $table->timestamp('last_message_at')->nullable();

      $table->timestamps();

      $table->index(['vendor_id','status']);
      $table->index(['user_id','status']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_requests');
  }
};
