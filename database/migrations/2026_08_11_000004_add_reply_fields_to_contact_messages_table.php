<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->timestamp('replied_at')->nullable()->after('archived_at');
            $table->text('reply_body')->nullable()->after('replied_at');
            $table->string('reply_subject', 120)->nullable()->after('reply_body');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['replied_at', 'reply_body', 'reply_subject']);
        });
    }
};
