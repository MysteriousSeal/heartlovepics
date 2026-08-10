<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_likes')->default(true)->after('is_banned');
            $table->boolean('notify_comments')->default(true)->after('notify_likes');
            $table->boolean('notify_bookmarks')->default(true)->after('notify_comments');
            $table->boolean('notify_replies')->default(true)->after('notify_bookmarks');
            $table->boolean('notify_mentions')->default(true)->after('notify_replies');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_likes',
                'notify_comments',
                'notify_bookmarks',
                'notify_replies',
                'notify_mentions',
            ]);
        });
    }
};