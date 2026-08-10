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
        // The bio is now rich text, so 500 plain characters can carry several
        // times that in markup. VARCHAR(200) no longer fits.
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio', 200)->nullable()->change();
        });
    }
};
