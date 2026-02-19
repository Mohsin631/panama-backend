<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            // Auth
            $table->string('email')->unique();
            $table->string('password');

            // Onboarding fields
            $table->string('business_name')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('whatsapp_no')->nullable(); // store with country code
            $table->text('about')->nullable();
            $table->unsignedSmallInteger('years_in_business')->nullable();

            // Lists (JSON arrays)
            $table->json('export_markets')->nullable(); // e.g. ["Pakistan","UAE"]
            $table->json('languages')->nullable();      // e.g. ["English","Urdu"]

            // Image
            $table->string('image_path')->nullable();

            // Flow control
            $table->unsignedTinyInteger('onboarding_step')->default(1); // 1..3
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
