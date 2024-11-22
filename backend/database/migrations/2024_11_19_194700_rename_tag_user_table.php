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
        Schema::rename('tag_user', 'user_interest');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('user_interest', 'tag_user');
    }
};
