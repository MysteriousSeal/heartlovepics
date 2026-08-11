<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->updateOrInsert(
            ['key' => SiteSetting::SHOW_GALLERY_ARTISTS],
            [
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->where('key', SiteSetting::SHOW_GALLERY_ARTISTS)
            ->delete();
    }
};
