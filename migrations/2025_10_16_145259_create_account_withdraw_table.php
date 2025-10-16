<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_withdraw', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->string('method');
            $table->decimal('amount', 10, 2);
            $table->boolean('scheduled')->default(false);
            $table->dateTime('scheduled_for')->nullable();
            $table->boolean('done')->default(false);
            $table->boolean('error')->default(false);
            $table->string('error_reason')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('account')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_withdraw');
    }
};
