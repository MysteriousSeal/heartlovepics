<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Tag;

class TagService
{
    private const STOPWORDS = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'in', 'is', 'it',
        'of', 'on', 'or', 'the', 'this', 'to', 'was', 'with', 'you', 'your',
    ];

    /** @return list<string> */
    public function suggest(string $title, ?string $description = null, int $limit = 8): array
    {
        $tokens = $this->tokenize(trim($title.' '.($description ?? '')));

        if ($tokens === []) {
            return [];
        }

        // Broad net-cast at the DB level (still substring — just to keep the
        // candidate set small); the real scoring happens below in PHP.
        $candidates = Tag::query()
            ->where(function ($query) use ($tokens) {
                foreach ($tokens as $token) {
                    $query->orWhere('name', 'like', '%'.$token.'%');
                }
            })
            ->withCount('images')
            ->get(['id', 'name']);

        $scored = [];

        foreach ($candidates as $tag) {
            $tagWords = $this->tokenize($tag->name);
            $score = 0;

            foreach ($tokens as $token) {
                if (in_array($token, $tagWords, true)) {
                    // Word-boundary match: the token is a whole word of the tag.
                    // A single-word tag that exactly equals the token is the
                    // strongest signal; matching just one word of a multi-word
                    // tag (e.g. "kiss" in "kiss cam") is still real, but weaker.
                    $score += count($tagWords) === 1 ? 4 : 3;
                } elseif (str_contains($tag->name, $token) || str_contains($token, $tag->name)) {
                    // Substring fallback, scored low on purpose: this is what
                    // catches this site's many concatenated tags (e.g. "kitchen"
                    // matching "kitchensex") without letting loose substrings
                    // like "art" outscore a real word match against "party".
                    $score += 1;
                }
            }

            if ($score > 0) {
                $scored[] = ['name' => $tag->name, 'score' => $score, 'images_count' => $tag->images_count];
            }
        }

        usort($scored, fn (array $a, array $b) => $b['score'] <=> $a['score']
            ?: $b['images_count'] <=> $a['images_count']
            ?: $a['name'] <=> $b['name']);

        return array_slice(array_column($scored, 'name'), 0, $limit);
    }

    /** @return list<string> */
    private function tokenize(string $text): array
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($words)
            ->filter(fn (string $word) => mb_strlen($word) >= 3 && ! in_array($word, self::STOPWORDS, true))
            ->unique()
            ->values()
            ->all();
    }

    /** @param  array<int, string>  $tagNames */
    public function syncForImage(Image $image, array $tagNames): void
    {
        $tagIds = collect($tagNames)
            ->map(fn (string $name) => Tag::normalizeName($name))
            ->filter(fn (string $name) => $name !== '')
            ->unique()
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name])->id)
            ->values()
            ->all();

        $image->tags()->sync($tagIds);
    }
}