<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Backfill: apply the new <slug>-softdelete-<datetime> renaming to posts that
 * were already soft-deleted before that behavior existed (see Image::booted()),
 * so their original slugs are freed up for reuse right away instead of only on
 * the next delete.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('images')) {
            return;
        }

        $trashed = DB::table('images')
            ->whereNotNull('deleted_at')
            ->get(['id', 'slug', 'deleted_at']);

        foreach ($trashed as $image) {
            if (preg_match('/-softdelete-\d{14}$/', $image->slug)) {
                continue;
            }

            $suffix = Carbon::parse($image->deleted_at)->format('YmdHis');

            DB::table('images')
                ->where('id', $image->id)
                ->update(['slug' => $image->slug.'-softdelete-'.$suffix]);
        }
    }

    public function down(): void
    {
        // Irreversible data cleanup — original slugs aren't recorded anywhere.
    }
};
