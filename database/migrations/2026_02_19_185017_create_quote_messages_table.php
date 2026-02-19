<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_messages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('quote_request_id')->constrained('quote_requests')->cascadeOnDelete();

      // polymorphic sender (user or vendor)
      $table->enum('sender_type', ['user','vendor']);
      $table->unsignedBigInteger('sender_id');

      $table->text('message')->nullable();

      // optional attachment (future)
      $table->string('attachment_path')->nullable();
      $table->string('attachment_type')->nullable(); // image/pdf/etc

      $table->timestamps();

      $table->index(['quote_request_id','created_at']);
      $table->index(['sender_type','sender_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_messages');
  }
};
