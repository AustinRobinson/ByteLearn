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
        Schema::create('playlist_video', function (Blueprint $table) {
            $table->foreignUuid('playlist_id')->constrained();
            $table->foreignUuid('video_id')->constrained();
            $table->primary(['playlist_id', 'video_id']);
            $table->timestamp('added_at');
            $table->unsignedInteger('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_video');
    }
};
