<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint', 64);
            $table->timestamps();

            $table->unique(['image_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_likes');
    }
};