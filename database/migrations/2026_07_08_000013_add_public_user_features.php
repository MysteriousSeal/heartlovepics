<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('username');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('image_likes', function (Blueprint $table) {
            $table->dropUnique(['image_id', 'fingerprint']);
        });

        Schema::table('image_likes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('image_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint', 64)->nullable()->change();
            $table->unique(['image_id', 'user_id']);
            $table->unique(['image_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::table('image_likes', function (Blueprint $table) {
            $table->dropUnique(['image_id', 'fingerprint']);
            $table->dropUnique(['image_id', 'user_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->string('fingerprint', 64)->nullable(false)->change();
            $table->unique(['image_id', 'fingerprint']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};