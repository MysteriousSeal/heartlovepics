<?php

/**
 * Visual retag from main image analysis (not story text alone).
 * Run: php scripts/retags-visual.php
 */

use App\Models\Image;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

/** @var TagService $tagService */
$tagService = $app->make(TagService::class);

/**
 * Tags from what is visible in the main image.
 * Placeholders / empty graphics get [].
 *
 * @var array<int, list<string>>
 */
$map = [
    342 => ['brunette', 'big tits', 'big ass', 'balcony'],
    344 => ['brunette', 'big tits', 'lingerie'],
    354 => ['brunette', 'big tits', 'bodysuit', 'office'],
    358 => ['brunette', 'big tits', 'bikini', 'beach'],
    360 => ['redhead', 'big tits', 'construction'],
    361 => ['brunette', 'big tits', 'studio'],
    362 => ['brunette', 'big tits', 'salon'],
    363 => ['black hair', 'big tits', 'diner'],
    364 => ['black hair', 'big tits', 'office'],
    365 => ['redhead', 'big tits', 'red dress'],
    366 => ['redhead', 'big tits', 'bikini', 'beach'],
    367 => ['brunette', 'big tits', 'studio'],
    368 => ['blonde', 'big tits', 'emt'],
    369 => ['asian', 'black hair', 'big tits', 'glasses'],
    371 => ['redhead', 'big tits', 'kitchen', 'topless'],
    372 => ['redhead', 'big tits', 'kitchen', 'nude'],
    373 => ['blonde', 'big tits', 'bondage'],
    374 => ['pink hair', 'schoolgirl', 'big ass'],
    375 => ['purple hair', 'big tits', 'nude', 'solo'],
    384 => ['black hair', 'big tits', 'nude', 'solo'],
    385 => ['redhead', 'big tits', 'shower'],
    388 => ['black hair', 'big tits', 'bed', 'nude'],
    391 => ['blonde', 'big tits', 'bedroom', 'nude'],
    394 => ['redhead', 'big tits', 'bed', 'topless'],
    397 => ['brunette', 'big tits', 'locker room', 'nude'],
    398 => ['blonde', 'big tits', 'nude'],
    399 => ['redhead', 'big tits', 'nude'],
    400 => ['black hair', 'big tits', 'store', 'nude'],
    401 => [], // placeholder graphic
    402 => ['black hair', 'big tits', 'store'],
    403 => ['black hair', 'big tits', 'store'],
    404 => ['redhead', 'big tits', 'hotel'],
    405 => ['blonde', 'big tits', 'car'],
    406 => ['ebony', 'big tits', 'kitchen', 'topless'],
    407 => [], // placeholder
    408 => [], // placeholder
    409 => [], // placeholder
    410 => ['brunette', 'big tits'],
    411 => ['brunette', 'big tits', 'garage'],
    412 => [], // placeholder
    413 => ['brunette', 'big tits', 'office'],
    414 => [], // placeholder
    415 => ['redhead', 'big tits', 'cabin'],
    418 => ['blonde', 'big tits', 'bathroom', 'nude'],
    419 => ['blonde', 'big tits', 'pool'],
    420 => ['blonde', 'big tits', 'lingerie'],
    421 => ['blonde', 'big tits', 'lingerie'],
    422 => ['brunette', 'big tits', 'kitchen'],
    423 => ['black hair', 'big tits', 'window', 'nude'],
    424 => ['redhead', 'big tits'],
    425 => ['black hair', 'big tits'],
    426 => ['black hair', 'big tits', 'ass', 'doggy'],
    427 => ['redhead', 'big tits', 'couch', 'nude'],
    428 => ['white hair', 'big tits', 'window', 'nude'],
    429 => ['brunette', 'big tits', 'floor', 'spread'],
    430 => ['pink hair', 'big tits', 'bathroom', 'doggy'],
    431 => ['redhead', 'big tits', 'nude'],
    432 => ['blonde', 'big tits', 'doggy', 'ass'],
    434 => ['redhead', 'big tits', 'spread', 'nude'],
    435 => ['white hair', 'big tits', 'ass', 'window'],
    436 => ['blonde', 'big tits', 'fire', 'kneeling'],
    437 => ['pink hair', 'big tits', 'bathroom', 'nude'],
    440 => ['black hair', 'big tits', 'stockings'],
    441 => ['black hair', 'big tits', 'pool'],
    442 => ['black hair', 'big tits', 'kitchen', 'bodysuit'],
    443 => ['black hair', 'big tits', 'balcony'],
    444 => ['black hair', 'big tits', 'nude'],
    445 => ['black hair', 'big tits', 'stockings'],
    446 => ['black hair', 'big tits', 'kitchen'],
    447 => ['black hair', 'big tits', 'kitchen', 'topless'],
    448 => ['black hair', 'big tits', 'bedroom', 'nude'],
    449 => ['redhead', 'big tits', 'nude'],
    450 => ['black hair', 'big tits', 'bed', 'nude'],
    451 => ['blonde', 'big tits', 'bed', 'nude'],
    452 => ['black hair', 'big tits', 'shower', 'nude'],
    453 => ['redhead', 'big tits', 'couch', 'nude'],
    454 => ['redhead', 'big tits', 'hallway', 'nude'],
    455 => ['black hair', 'big tits', 'kitchen', 'nude'],
    456 => ['black hair', 'big tits', 'doggy', 'nude'],
    457 => ['redhead', 'big tits', 'kitchen', 'masturbation'],
];

$updated = 0;
$missing = [];

foreach (Image::query()->pluck('id') as $id) {
    if (! array_key_exists($id, $map)) {
        $missing[] = $id;
        continue;
    }

    $image = Image::query()->find($id);
    if (! $image) {
        continue;
    }

    $tagService->syncForImage($image, $map[$id]);
    $updated++;
    $label = $map[$id] === [] ? '(none)' : implode(', ', $map[$id]);
    echo "#{$id}: {$label}\n";
}

$orphans = Tag::query()->whereDoesntHave('images')->delete();
Cache::forget('footer.popular_tags');

echo "\nUpdated: {$updated}\n";
echo "Orphans removed: {$orphans}\n";
echo 'Tags left: '.Tag::count()."\n";
echo 'Avg tags/image: '.round(DB::table('image_tag')->count() / max(Image::count(), 1), 2)."\n";

if ($missing !== []) {
    echo 'Missing map: '.implode(', ', $missing)."\n";
    exit(1);
}
