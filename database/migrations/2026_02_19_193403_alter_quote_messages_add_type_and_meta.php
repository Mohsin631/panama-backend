<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quote_messages', function (Blueprint $table) {
            // message types: text (normal), system (helper), quote (price update), status (status update)
            $table->enum('message_type', ['text','system','status','quote'])
                  ->default('text')
                  ->after('quote_request_id');

            // store extra info like from_status/to_status, tracking no, quoted_price, etc.
            $table->json('meta')->nullable()->after('message');

            // allow null sender_id for system messages
            $table->unsignedBigInteger('sender_id')->nullable()->change();
        });

        // IMPORTANT: update sender_type enum to include "system"
        // MySQL enum change needs raw SQL
        DB::statement("ALTER TABLE quote_messages MODIFY sender_type ENUM('user','vendor','system') NOT NULL");
    }

    public function down(): void
    {
        // revert sender_type enum
        DB::statement("ALTER TABLE quote_messages MODIFY sender_type ENUM('user','vendor') NOT NULL");

        Schema::table('quote_messages', function (Blueprint $table) {
            $table->dropColumn(['message_type','meta']);
            $table->unsignedBigInteger('sender_id')->nullable(false)->change();
        });
    }
};
