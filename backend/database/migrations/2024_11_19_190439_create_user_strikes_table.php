<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_strikes', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('user_id')->constrained();
            $table->text('reason');
            $table->boolean('has_caused_suspension');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_strikes');
    }
};
