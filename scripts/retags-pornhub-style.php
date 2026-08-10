<?php

/**
 * One-shot retag: replace junk/compound tags with Pornhub-style vocabulary.
 * Run: php scripts/retags-pornhub-style.php
 */

use App\Models\Image;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

/** @var TagService $tagService */
$tagService = $app->make(TagService::class);

/**
 * Curated tags per image id. Empty array clears tags (WIP stubs).
 * Vocabulary: short, searchable, PH-style — hair, body, act, setting, role.
 *
 * @var array<int, list<string>>
 */
$map = [
    // Exhibition / city solo-ish
    342 => ['brunette', 'big tits', 'exhibition', 'solo'],
    344 => ['big tits', 'lingerie', 'solo', 'exhibition'],

    // Explicit scenes
    354 => ['office', 'brunette', 'bodysuit', 'sex'],
    358 => ['beach', 'bikini', 'outdoor', 'sex'],
    360 => ['construction', 'outdoor', 'solo', 'exhibition'],
    361 => ['studio', 'solo', 'masturbation'],
    362 => ['salon', 'solo', 'masturbation'],
    363 => ['diner', 'fingering', 'counter'],
    364 => ['office', 'boss', 'exhibition', 'masturbation'],
    365 => ['redhead', 'sex', 'doggystyle'],
    366 => ['redhead', 'bikini', 'cum on tits'],
    367 => ['brunette', 'studio', 'facial'],
    368 => ['blonde', 'emt', 'facial', 'cum on tits'],
    369 => ['asian', 'facial', 'outdoor', 'rain'],
    371 => ['redhead', 'kitchen', 'big tits', 'facial'],
    372 => ['redhead', 'kitchen', 'big tits', 'facial'],
    373 => ['blonde', 'bondage', 'blowjob', 'facial'],
    374 => ['pink hair', 'schoolgirl', 'facial', 'big tits'],
    375 => ['purple hair', 'big tits', 'facial', 'bed'],

    384 => ['lesbian', 'threesome', 'ffm'],
    385 => ['solo', 'bathroom', 'big tits', 'masturbation'],
    388 => ['solo', 'bedroom', 'big tits', 'fingering'],
    391 => ['milf', 'age gap', 'bedroom'],
    394 => ['redhead', 'threesome', 'big tits'],
    397 => ['locker room', 'big tits', 'school'],
    398 => ['taboo', 'brother', 'big tits'],
    399 => ['taboo', 'daddy', 'big tits'],
    400 => ['boss', 'store', 'age gap'],

    401 => ['boss', 'store'],
    402 => ['boss', 'store', 'rough sex'],
    403 => ['boss', 'store', 'cumshot'],
    404 => ['redhead', 'hotel', 'sex'],
    405 => ['blonde', 'car', 'rough sex'],
    406 => ['lesbian', 'kitchen', 'ebony'],
    407 => ['laundromat', 'sex'],
    408 => ['train', 'stranger', 'sex'],
    409 => ['pool', 'sex'],
    410 => ['outdoor', 'rough sex'],
    411 => ['taboo', 'daddy'],
    412 => ['library', 'sex'],
    413 => ['office', 'rough sex'],
    414 => ['taboo', 'brother', 'rain'],
    415 => ['cabin', 'threesome', 'lesbian'],

    // Untitled WIP stubs — strip tags
    418 => [],
    419 => [],
    420 => [],
    421 => [],
    422 => [],
    423 => [],
    424 => [],
    425 => [],

    // Pose / visual posts
    426 => ['black hair', 'ass', 'doggy', 'bed'],
    427 => ['redhead', 'big tits', 'couch'],
    428 => ['white hair', 'window', 'exhibition'],
    429 => ['big tits', 'spread', 'floor'],
    430 => ['pink hair', 'bathroom', 'doggy'],
    431 => ['redhead', 'big tits', 'facial', 'pov'],
    432 => ['blonde', 'big tits', 'doggy', 'facial'],
    434 => ['redhead', 'big tits', 'blowjob', 'pov'],
    435 => ['white hair', 'big tits', 'window', 'creampie'],
    436 => ['blonde', 'kneeling', 'fire'],
    437 => ['pink hair', 'bathroom', 'rough sex', 'facial'],

    440 => ['solo', 'masturbation', 'big tits', 'stockings'],
    441 => ['pool', 'creampie', 'big tits', 'rough sex'],
    442 => ['kitchen', 'solo', 'masturbation', 'big tits'],
    443 => ['lesbian', 'balcony', 'oral', 'facesitting'],
    444 => ['solo', 'masturbation', 'big tits'],
    445 => ['creampie', 'window', 'stockings', 'rough sex'],
    446 => ['kitchen', 'solo', 'masturbation', 'big tits'],
    447 => ['kitchen', 'lesbian', 'oral', 'big tits'],
    448 => ['solo', 'bedroom', 'masturbation', 'big tits'],
    449 => ['lesbian', 'oral', 'scissoring', 'big tits'],
    450 => ['solo', 'fingering', 'big tits', 'bed'],
    451 => ['blonde', 'stranger', 'facial', 'titfuck'],
    452 => ['shower', 'solo', 'fingering', 'big tits'],
    453 => ['redhead', 'lesbian', 'oral', 'couch'],
    454 => ['blonde', 'solo', 'fingering', 'exhibition'],
    455 => ['black hair', 'kitchen', 'titfuck', 'big tits'],
    456 => ['black hair', 'solo', 'fingering', 'doggy'],
    457 => ['redhead', 'kitchen', 'lesbian', 'oral'],
];

$updated = 0;
$missing = [];
$allIds = Image::query()->pluck('id')->all();

foreach ($allIds as $id) {
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
\Illuminate\Support\Facades\Cache::forget('footer.popular_tags');

echo "\nUpdated images: {$updated}\n";
echo "Orphan tags removed: {$orphans}\n";
echo 'Tags remaining: '.Tag::count()."\n";
echo 'Avg tags/image: '.round(
    \Illuminate\Support\Facades\DB::table('image_tag')->count() / max(Image::count(), 1),
    2
)."\n";

if ($missing !== []) {
    echo 'WARNING missing map for ids: '.implode(', ', $missing)."\n";
    exit(1);
}
