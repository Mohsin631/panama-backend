<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained('vendors')->cascadeOnDelete();

            // Order notifications
            $table->boolean('new_order_received')->default(true);
            $table->boolean('order_status_updates')->default(true);
            $table->boolean('order_cancelled')->default(true);

            // Message notifications
            $table->boolean('new_customer_message')->default(true);
            $table->boolean('admin_messages')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_settings');
    }
};
