<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quote_messages', function (Blueprint $table) {
            $table->timestamp('seen_by_user_at')->nullable()->after('meta');
            $table->timestamp('seen_by_vendor_at')->nullable()->after('seen_by_user_at');

            $table->index(['quote_request_id', 'seen_by_user_at']);
            $table->index(['quote_request_id', 'seen_by_vendor_at']);
        });
    }

    public function down(): void
    {
        Schema::table('quote_messages', function (Blueprint $table) {
            $table->dropIndex(['quote_request_id', 'seen_by_user_at']);
            $table->dropIndex(['quote_request_id', 'seen_by_vendor_at']);
            $table->dropColumn(['seen_by_user_at', 'seen_by_vendor_at']);
        });
    }
};
