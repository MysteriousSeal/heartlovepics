<?php

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = User::query()->where('username', 'admin')->value('id');

        if ($adminId === null) {
            return;
        }

        Image::query()
            ->whereNull('user_id')
            ->update(['user_id' => $adminId]);
    }

    public function down(): void
    {
        $adminId = User::query()->where('username', 'admin')->value('id');

        if ($adminId === null) {
            return;
        }

        Image::query()
            ->where('user_id', $adminId)
            ->update(['user_id' => null]);
    }
};