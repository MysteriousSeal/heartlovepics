<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('image_id')
                ->constrained('comments')
                ->cascadeOnDelete();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->string('content_warning', 100)->nullable()->after('is_nsfw');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('content_warning');
        });
    }
};