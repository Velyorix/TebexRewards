<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tebex_event_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('channel', 32)->index();
            $table->string('level', 16)->default('info')->index();
            $table->string('event', 64)->index();
            $table->string('message', 500);
            $table->json('context')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tebex_event_logs');
    }
};
