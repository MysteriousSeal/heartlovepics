<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Default for SQLite when adding a non-null column to an existing table.
            $table->string('email', 255)->default('')->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
