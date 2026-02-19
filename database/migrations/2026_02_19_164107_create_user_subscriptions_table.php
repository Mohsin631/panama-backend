<?php

// database/migrations/xxxx_create_user_subscriptions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('user_subscriptions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();

      $table->string('stripe_customer_id')->nullable()->index();
      $table->string('stripe_subscription_id')->nullable()->index(); // only for subscription plans
      $table->string('stripe_checkout_session_id')->nullable()->index();

      $table->enum('status', [
        'pending','active','canceled','past_due','incomplete','expired'
      ])->default('pending');

      $table->timestamp('starts_at')->nullable();
      $table->timestamp('expires_at')->nullable();          // for day-pass or if you want local access control
      $table->timestamp('current_period_end')->nullable();  // subscription renew end
      $table->timestamp('canceled_at')->nullable();

      $table->timestamps();
      $table->index(['user_id','status']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('user_subscriptions');
  }
};
