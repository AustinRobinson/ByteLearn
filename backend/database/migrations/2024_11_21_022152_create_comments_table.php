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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('video_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('comment_id')->nullable();
            $table->text('comment');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
