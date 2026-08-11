<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained('contact_messages')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 120);
            $table->text('body');
            $table->string('from_address', 255)->nullable();
            $table->timestamps();

            $table->index(['contact_message_id', 'created_at']);
        });

        // Move any single legacy reply into the history table.
        $legacy = DB::table('contact_messages')
            ->whereNotNull('reply_body')
            ->where('reply_body', '!=', '')
            ->get(['id', 'reply_subject', 'reply_body', 'replied_at']);

        foreach ($legacy as $row) {
            DB::table('contact_message_replies')->insert([
                'contact_message_id' => $row->id,
                'admin_id' => null,
                'subject' => $row->reply_subject ?: 'Re: message',
                'body' => $row->reply_body,
                'from_address' => null,
                'created_at' => $row->replied_at ?? now(),
                'updated_at' => $row->replied_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');
    }
};
