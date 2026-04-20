<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gigi_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->text('message_sanitized');
            $table->text('reply')->nullable();
            $table->string('reply_level', 24);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gigi_chat_logs');
    }
};
