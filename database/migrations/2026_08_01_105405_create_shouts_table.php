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
        Schema::create('shouts', function (Blueprint $table) {
            $table->id();
            // Profile this shout was left on.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Who posted it. Shouts require login (no anonymous guestbook spam).
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('body', 500);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shouts');
    }
};
