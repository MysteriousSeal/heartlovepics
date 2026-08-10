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
        Schema::table('images', function (Blueprint $table) {
            $table->string('artist_name')->nullable()->after('is_private');
            $table->string('deviantart_url')->nullable()->after('artist_name');
            $table->string('furaffinity_url')->nullable()->after('deviantart_url');
            $table->string('patreon_url')->nullable()->after('furaffinity_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['artist_name', 'deviantart_url', 'furaffinity_url', 'patreon_url']);
        });
    }
};
