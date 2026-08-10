<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('image_id')->constrained()->nullOnDelete();
        });

        DB::statement('
            UPDATE comments
            SET user_id = (
                SELECT id FROM users WHERE users.username = comments.author_name LIMIT 1
            )
            WHERE user_id IS NULL
            AND EXISTS (
                SELECT 1 FROM users WHERE users.username = comments.author_name
            )
        ');
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};