<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_status_histories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('quote_request_id')->constrained('quote_requests')->cascadeOnDelete();

      $table->string('from_status')->nullable();
      $table->string('to_status');
      $table->enum('changed_by_type', ['user','vendor','system']);
      $table->unsignedBigInteger('changed_by_id')->nullable();

      $table->text('note')->nullable();
      $table->timestamps();

      $table->index(['quote_request_id','created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_status_histories');
  }
};
