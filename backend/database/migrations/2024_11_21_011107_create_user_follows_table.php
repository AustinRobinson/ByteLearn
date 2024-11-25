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
        Schema::create('user_follows', function (Blueprint $table) {
            $table->foreignUuid('follower_id')->constrained('users', 'id');
            $table->foreignUuid('creator_id')->constrained('users', 'id');
            $table->primary(['follower_id', 'creator_id']);
            $table->timestamp('followed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_follows');
    }
};
