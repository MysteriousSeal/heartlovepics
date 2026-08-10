<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Artist links used to live on every image row. Moving them onto a shared
     * Artist record (matched by name) means one edit updates every post that
     * credits that artist instead of having to fix the same links N times.
     */
    public function up(): void
    {
        $rows = DB::table('images')
            ->select('artist_name', 'deviantart_url', 'furaffinity_url', 'patreon_url')
            ->whereNotNull('artist_name')
            ->where('artist_name', '!=', '')
            ->get();

        $byName = [];

        foreach ($rows as $row) {
            $name = trim($row->artist_name);

            if ($name === '') {
                continue;
            }

            $byName[$name] ??= ['deviantart_url' => null, 'furaffinity_url' => null, 'patreon_url' => null];
            $byName[$name]['deviantart_url'] ??= $row->deviantart_url;
            $byName[$name]['furaffinity_url'] ??= $row->furaffinity_url;
            $byName[$name]['patreon_url'] ??= $row->patreon_url;
        }

        $now = now();

        foreach ($byName as $name => $links) {
            DB::table('artists')->updateOrInsert(
                ['name' => $name],
                [...$links, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['deviantart_url', 'furaffinity_url', 'patreon_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('deviantart_url')->nullable();
            $table->string('furaffinity_url')->nullable();
            $table->string('patreon_url')->nullable();
        });

        $images = DB::table('images')->select('id', 'artist_name')->whereNotNull('artist_name')->get();
        $artists = DB::table('artists')->get()->keyBy('name');

        foreach ($images as $image) {
            $artist = $artists->get(trim((string) $image->artist_name));

            if (! $artist) {
                continue;
            }

            DB::table('images')->where('id', $image->id)->update([
                'deviantart_url' => $artist->deviantart_url,
                'furaffinity_url' => $artist->furaffinity_url,
                'patreon_url' => $artist->patreon_url,
            ]);
        }
    }
};
