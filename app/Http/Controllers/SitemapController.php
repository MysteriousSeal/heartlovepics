<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemaps = [
            [
                'loc' => url(route('sitemap.pages', absolute: false)),
                'lastmod' => null,
            ],
            [
                'loc' => url(route('sitemap.tags', absolute: false)),
                'lastmod' => null,
            ],
            [
                'loc' => url(route('sitemap.posts', absolute: false)),
                'lastmod' => null,
            ],
            [
                'loc' => url(route('sitemap.users', absolute: false)),
                'lastmod' => null,
            ],
            [
                'loc' => url(route('sitemap.artists', absolute: false)),
                'lastmod' => null,
            ],
            [
                'loc' => url(route('sitemap.parodies', absolute: false)),
                'lastmod' => null,
            ],
        ];

        return response()
            ->view('sitemap-index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    public function pages(): Response
    {
        $urls = [
            [
                'loc' => url(route('home', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => url(route('gallery.views', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url(route('gallery.likes', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url(route('gallery.random', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url(route('gallery.search', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ],
            [
                'loc' => url(route('gallery.tags', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
            [
                'loc' => url(route('gallery.artists', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
            [
                'loc' => url(route('gallery.parodies', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
            [
                'loc' => url(route('pages.privacy', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => url(route('pages.terms', absolute: false)),
                'lastmod' => null,
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        return $this->urlsetResponse($urls);
    }

    public function tags(): Response
    {
        $urls = Tag::query()
            ->whereHas('images', fn ($query) => $query->where('is_published', true)->where('is_private', false))
            ->withMax(['images as last_image_updated_at' => fn ($query) => $query->where('is_published', true)->where('is_private', false)], 'updated_at')
            ->orderBy('name')
            ->get(['id', 'name', 'updated_at'])
            ->map(fn (Tag $tag) => [
                'loc' => url(route('gallery.tag', $tag->name, absolute: false)),
                'lastmod' => $tag->last_image_updated_at
                    ? Carbon::parse($tag->last_image_updated_at)->toAtomString()
                    : $tag->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ])
            ->all();

        return $this->urlsetResponse($urls);
    }

    public function artists(): Response
    {
        $urls = Image::query()
            ->where('is_published', true)
            ->where('is_private', false)
            ->whereNotNull('artist_name')
            ->where('artist_name', '!=', '')
            ->selectRaw('artist_name, MAX(updated_at) as last_updated_at')
            ->groupBy('artist_name')
            ->orderBy('artist_name')
            ->get()
            ->map(fn ($row) => [
                'loc' => url(route('gallery.artist', $row->artist_name, absolute: false)),
                'lastmod' => $row->last_updated_at ? Carbon::parse($row->last_updated_at)->toAtomString() : null,
                'changefreq' => 'weekly',
                'priority' => '0.4',
            ])
            ->all();

        return $this->urlsetResponse($urls);
    }

    public function parodies(): Response
    {
        $urls = Image::query()
            ->where('is_published', true)
            ->where('is_private', false)
            ->whereNotNull('parody')
            ->where('parody', '!=', '')
            ->selectRaw('parody, MAX(updated_at) as last_updated_at')
            ->groupBy('parody')
            ->orderBy('parody')
            ->get()
            ->map(fn ($row) => [
                'loc' => url(route('gallery.parody', $row->parody, absolute: false)),
                'lastmod' => $row->last_updated_at ? Carbon::parse($row->last_updated_at)->toAtomString() : null,
                'changefreq' => 'weekly',
                'priority' => '0.4',
            ])
            ->all();

        return $this->urlsetResponse($urls);
    }

    public function posts(): Response
    {
        $urls = Image::query()
            ->where('is_published', true)
            ->where('is_private', false)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Image $image) => [
                'loc' => url(route('images.show', $image->slug, absolute: false)),
                'lastmod' => $image->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ])
            ->all();

        return $this->urlsetResponse($urls);
    }

    public function users(): Response
    {
        $urls = User::query()
            ->where('is_banned', false)
            ->orderBy('username')
            ->get(['username', 'updated_at'])
            ->map(fn (User $user) => [
                'loc' => url(route('users.show', $user->username, absolute: false)),
                'lastmod' => $user->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.4',
            ])
            ->all();

        return $this->urlsetResponse($urls);
    }

    /** @param  array<int, array{loc: string, lastmod: ?string, changefreq: string, priority: string}>  $urls */
    private function urlsetResponse(array $urls): Response
    {
        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
