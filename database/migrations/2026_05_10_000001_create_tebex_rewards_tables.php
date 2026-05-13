<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tebex_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('player_uuid')->nullable()->index();
            $table->string('player_name', 32)->index();

            $table->unsignedBigInteger('package_id')->nullable()->index();
            $table->string('package_name')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 8)->nullable();

            $table->timestamp('purchase_date')->index();
            $table->string('status', 32)->default('complete')->index();

            $table->string('tebex_transaction_id', 64)->nullable()->unique();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->index(['player_name', 'purchase_date']);
        });

        Schema::create('tebex_rank_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('rank_name', 64);
            $table->decimal('min_amount', 12, 2)->default(0)->index();
            $table->string('icon')->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->boolean('enabled')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tebex_rank_tiers');
        Schema::dropIfExists('tebex_transactions');
    }
};

