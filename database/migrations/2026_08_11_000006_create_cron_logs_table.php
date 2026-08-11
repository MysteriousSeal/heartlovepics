<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command', 255);
            $table->string('description', 255)->nullable();
            $table->string('expression', 64)->nullable();
            $table->string('status', 20)->default('running'); // running, success, failed
            $table->text('output')->nullable();
            $table->text('error')->nullable();
            $table->unsignedInteger('exit_code')->nullable();
            $table->decimal('runtime_ms', 12, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'started_at']);
            $table->index('command');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
    }
};
