<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Image;
use App\Models\Parody;
use App\Models\Tag;
use App\Services\GalleryActivityService;
use App\Services\VisitCounterService;
use App\Services\ImageViewService;
use App\Services\BookmarkService;
use App\Services\CollectionService;
use App\Services\CommentLikeService;
use App\Services\LikeService;
use App\Services\SiteSettingsService;
use App\Support\GalleryColumns;
use App\Support\NsfwPreference;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const SORT_OPTIONS = ['latest', 'views', 'likes', 'random', 'following'];

    private const RANDOM_IDS_SESSION_KEY = 'gallery.random_ids';

    /** Third image in the feed (0-based index 2) — LCP candidate in the 4-column layout. */
    private const LCP_IMAGE_INDEX = 2;

    private const RELATED_IMAGE_LIMIT = 4;

    private const SAME_AUTHOR_IMAGE_LIMIT = 4;

    private const SAME_ARTIST_IMAGE_LIMIT = 4;

    public function __construct(private SiteSettingsService $settings) {}

    public function index(Request $request, LikeService $likes, BookmarkService $bookmarks, GalleryActivityService $activity, VisitCounterService $visitCounter): View|JsonResponse
    {
        $sort = $this->resolveSort($request);

        if ($sort === 'following') {
            abort_unless($request->user(), 401);
        }

        $query = $this->publishedImagesQuery();

        if ($sort === 'following') {
            $this->applyFollowingFilter($query, $request);
        }

        $this->applySort($query, $sort, $request);

        return $this->renderGallery($request, $query, $likes, $bookmarks, $activity, $visitCounter, $sort);
    }

    public function search(Request $request, LikeService $likes, BookmarkService $bookmarks, GalleryActivityService $activity, VisitCounterService $visitCounter): View|JsonResponse
    {
        $searchQuery = $request->string('q')->trim()->toString();

        $query = $this->publishedImagesQuery();
        $this->applySearch($query, $searchQuery);
        $query->latest();

        return $this->renderGallery(
            $request,
            $query,
            $likes,
            $bookmarks,
            $activity,
            $visitCounter,
            'search',
            $searchQuery,
        );
    }

    public function tags(Request $request): View
    {
        $sort = $request->query('sort') === 'count' ? 'count' : 'name';

        $tags = Tag::query()
            ->withCount(['images' => fn (Builder $query) => $query->publiclyVisible()])
            ->whereHas('images', fn (Builder $query) => $query->publiclyVisible())
            ->when(
                $sort === 'count',
                fn (Builder $query) => $query->orderByDesc('images_count')->orderBy('name'),
                fn (Builder $query) => $query->orderBy('name'),
            )
            ->get();

        return view('home.tags', compact('tags', 'sort'));
    }

    public function tag(string $tag, Request $request, LikeService $likes, BookmarkService $bookmarks, GalleryActivityService $activity, VisitCounterService $visitCounter): View|JsonResponse
    {
        $tagModel = Tag::query()
            ->where('name', Tag::normalizeName(urldecode($tag)))
            ->firstOrFail();

        $query = $this->publishedImagesQuery($request->user())
            ->whereHas('tags', fn (Builder $builder) => $builder->where('tags.id', $tagModel->id))
            ->latest();

        return $this->renderGallery(
            $request,
            $query,
            $likes,
            $bookmarks,
            $activity,
            $visitCounter,
            'tag',
            tag: $tagModel,
        );
    }

    public function artist(string $artist, Request $request, LikeService $likes, BookmarkService $bookmarks, GalleryActivityService $activity, VisitCounterService $visitCounter): View|JsonResponse
    {
        $artistName = trim(urldecode($artist));

        $query = $this->publishedImagesQuery()
            ->where('artist_name', $artistName)
            ->latest();

        // There's no separate "artist" entity to look up (artist_name is a free-text
        // field on the image itself) — an artist page only makes sense if at least
        // one visible post currently credits them.
        abort_if($artistName === '' || $query->clone()->doesntExist(), 404);

        $artistModel = Artist::query()->where('name', $artistName)->first();

        return $this->renderGallery(
            $request,
            $query,
            $likes,
            $bookmarks,
            $activity,
            $visitCounter,
            'artist',
            artistName: $artistName,
            artistModel: $artistModel,
        );
    }

    public function parody(string $parody, Request $request, LikeService $likes, BookmarkService $bookmarks, GalleryActivityService $activity, VisitCounterService $visitCounter): View|JsonResponse
    {
        $parodyName = trim(urldecode($parody));

        $query = $this->publishedImagesQuery()
            ->where('parody', $parodyName)
            ->latest();

        abort_if($parodyName === '' || $query->clone()->doesntExist(), 404);

        $parodyModel = Parody::query()->where('name', $parodyName)->first();

        return $this->renderGallery(
            $request,
            $query,
            $likes,
            $bookmarks,
            $activity,
            $visitCounter,
            'parody',
            parodyName: $parodyName,
            parodyModel: $parodyModel,
        );
    }

    public function show(string $slug, Request $request, LikeService $likes, BookmarkService $bookmarks, CommentLikeService $commentLikes, ImageViewService $views, CollectionService $collectionService): View
    {
        $image = Image::query()
            ->where('slug', $slug)
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->with([
                'user',
                'artist',
                'additionalImages',
                'comments' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->latest()
                    ->withCount('likes')
                    ->with(['user', 'replies' => $this->nestedRepliesQuery()]),
                'tags' => fn ($query) => $query->orderBy('name'),
            ])
            ->firstOrFail();

        if (! $image->isVisibleTo($request->user())) {
            abort(404);
        }

        $isDraft = ! $image->is_published;

        $views->recordDetailView($image);

        $isLiked = $likes->isLiked($image, $request);
        $isBookmarked = $bookmarks->isBookmarked($image, $request);
        $allComments = $this->flattenComments($image->comments);
        $commentLikedMap = $commentLikes->likedMapForComments($allComments, $request);
        $commentLikersMap = $commentLikes->recentLikersMapForComments($allComments);

        $relatedImages = $this->relatedImagesFor($image);
        $relatedTitle = $image->tags->isNotEmpty() ? 'Related images' : 'More images';

        $relatedLikedMap = $likes->likedMapForImages($relatedImages, $request);

        $sameAuthorImages = $this->sameAuthorImagesFor($image);
        $sameArtistImages = $this->sameArtistImagesFor($image);

        $showNsfw = NsfwPreference::isEnabled($request);
        $ageConfirmed = NsfwPreference::hasAgeConfirmed($request);

        $canManageImage = $image->user_id !== null
            && $request->user()?->id === $image->user_id;

        $canComment = ! $request->user() || $request->user()->canPost();
        $recentLikers = $likes->recentLikersForImage($image);

        $userCollections = $request->user() ? $collectionService->forUser($request->user()) : collect();
        $collectionMap = $request->user() ? $collectionService->membershipMap($request->user(), $image) : [];

        $imageSchema = $this->imageObjectSchema($image);

        return view('home.show', compact(
            'image',
            'isLiked',
            'isBookmarked',
            'commentLikedMap',
            'commentLikersMap',
            'relatedImages',
            'relatedLikedMap',
            'relatedTitle',
            'sameAuthorImages',
            'sameArtistImages',
            'canManageImage',
            'canComment',
            'isDraft',
            'showNsfw',
            'ageConfirmed',
            'recentLikers',
            'userCollections',
            'collectionMap',
            'imageSchema',
        ));
    }

    private function relatedImagesFor(Image $image)
    {
        $tagIds = $image->tags->pluck('id');

        $query = Image::query()
            ->publiclyVisible()
            ->where('id', '!=', $image->id)
            ->withCount(['likes', 'comments', 'bookmarks']);

        if ($tagIds->isNotEmpty()) {
            return $query
                ->whereHas('tags', fn (Builder $builder) => $builder->whereIn('tags.id', $tagIds))
                ->withCount([
                    'tags as shared_tags_count' => fn (Builder $builder) => $builder->whereIn('tags.id', $tagIds),
                ])
                ->orderByDesc('shared_tags_count')
                ->orderByDesc('likes_count')
                ->orderByDesc('created_at')
                ->limit(self::RELATED_IMAGE_LIMIT)
                ->get();
        }

        return $query
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at')
            ->limit(self::RELATED_IMAGE_LIMIT)
            ->get();
    }

    private function sameAuthorImagesFor(Image $image)
    {
        if (! $image->user_id) {
            return collect();
        }

        return Image::query()
            ->publiclyVisible()
            ->where('user_id', $image->user_id)
            ->where('id', '!=', $image->id)
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->inRandomOrder()
            ->limit(self::SAME_AUTHOR_IMAGE_LIMIT)
            ->get();
    }

    private function sameArtistImagesFor(Image $image)
    {
        if (! filled($image->artist_name)) {
            return collect();
        }

        return Image::query()
            ->publiclyVisible()
            ->where('artist_name', $image->artist_name)
            ->where('id', '!=', $image->id)
            ->withCount(['likes', 'comments', 'bookmarks'])
            ->inRandomOrder()
            ->limit(self::SAME_ARTIST_IMAGE_LIMIT)
            ->get();
    }

    private function renderGallery(
        Request $request,
        Builder $query,
        LikeService $likes,
        BookmarkService $bookmarks,
        GalleryActivityService $activity,
        VisitCounterService $visitCounter,
        string $sort,
        ?string $searchQuery = null,
        ?Tag $tag = null,
        ?string $artistName = null,
        ?Artist $artistModel = null,
        ?string $parodyName = null,
        ?Parody $parodyModel = null,
    ): View|JsonResponse {
        $images = $query->paginate(20)->withQueryString();

        $likedMap = $likes->likedMapForImages($images, $request);
        $bookmarkMap = $bookmarks->bookmarkedMapForImages($images, $request);
        $offset = ($images->currentPage() - 1) * $images->perPage();
        $columns = GalleryColumns::distribute($images, $offset);

        $showNsfw = NsfwPreference::isEnabled($request);
        $ageConfirmed = NsfwPreference::hasAgeConfirmed($request);
        $showGalleryAuthors = $this->settings->showGalleryAuthors();
        $showGalleryArtists = $this->settings->showGalleryArtists();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('partials.gallery-items', compact('images', 'likedMap', 'bookmarkMap', 'showNsfw', 'showGalleryAuthors', 'showGalleryArtists'))->render(),
                'offset' => $offset,
                'next_page_url' => $images->nextPageUrl(),
                'has_more' => $images->hasMorePages(),
            ]);
        }

        $activityStats = $activity->statsForLast24Hours();
        $totalVisits = $visitCounter->total();
        $totalImages = $activity->totalPublishedImages();
        $lcpImage = $images->getCollection()->get(self::LCP_IMAGE_INDEX) ?? $images->first();
        $isFirstPage = $images->currentPage() === 1;
        $pageMeta = $this->pageMeta($sort, $searchQuery, $images, $tag, $artistName, $parodyName);
        $pageMeta['og_image'] = $lcpImage?->direct_url ?? $activity->defaultOgImageUrl();

        // Only computed for the true homepage: real body text + internal links
        // for crawlers, not repeated on every sort/pagination variant.
        $popularTags = $sort === 'latest' && $isFirstPage
            ? $this->popularTags()
            : collect();

        $imageListSchema = $sort === 'latest' && $isFirstPage
            ? $this->sfwImageListSchema($images)
            : null;

        return view('home.index', compact(
            'images',
            'likedMap',
            'bookmarkMap',
            'columns',
            'sort',
            'searchQuery',
            'tag',
            'artistName',
            'artistModel',
            'parodyName',
            'parodyModel',
            'activityStats',
            'totalVisits',
            'totalImages',
            'lcpImage',
            'isFirstPage',
            'pageMeta',
            'showNsfw',
            'showGalleryAuthors',
            'showGalleryArtists',
            'ageConfirmed',
            'popularTags',
            'imageListSchema',
        ));
    }

    /**
     * ItemList/ImageObject structured data for Google Images — deliberately
     * scoped to SFW images only. The site's NSFW blur is a client-side CSS
     * effect on our own page, not something baked into the file itself;
     * structured data is a strong signal telling Google to index and surface
     * the raw image directly in Image Search, which would let people find and
     * view NSFW images without ever passing through our age-confirmation gate.
     */
    private function sfwImageListSchema(LengthAwarePaginator $images): ?array
    {
        $items = $images->getCollection()
            ->reject(fn (Image $image) => $image->is_nsfw)
            ->values();

        if ($items->isEmpty()) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $items->map(fn (Image $image, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => route('images.show', $image->slug),
                'item' => [
                    '@type' => 'ImageObject',
                    'contentUrl' => $image->direct_url,
                    'thumbnailUrl' => $image->thumbnail_direct_url,
                    'name' => $image->title,
                    'description' => $image->hasDescription()
                        ? \Illuminate\Support\Str::limit($image->description_plain, 200)
                        : $image->title,
                    'datePublished' => $image->created_at->toIso8601String(),
                    ...($image->user ? ['creator' => [
                        '@type' => 'Person',
                        'name' => $image->user->username,
                    ]] : []),
                ],
            ])->values()->all(),
        ];
    }

    /**
     * ImageObject structured data for a single post's detail page. Same NSFW
     * safeguard as sfwImageListSchema() above, plus we only ever want this on
     * genuinely public posts (published, non-private) — a draft or private
     * image isn't reachable by a crawler anyway (isVisibleTo() 404s it), but
     * there's no reason to emit "please index this" structured data for it
     * even when the owner is the one viewing the page.
     */
    private function imageObjectSchema(Image $image): ?array
    {
        if ($image->is_nsfw || ! $image->is_published || $image->is_private) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ImageObject',
            'contentUrl' => $image->direct_url,
            'thumbnailUrl' => $image->thumbnail_direct_url,
            'name' => $image->title,
            'description' => $image->hasDescription()
                ? \Illuminate\Support\Str::limit($image->description_plain, 200)
                : $image->title,
            'url' => route('images.show', $image->slug),
            'datePublished' => $image->created_at->toIso8601String(),
            'dateModified' => $image->updated_at->toIso8601String(),
            ...($image->width && $image->height ? [
                'width' => $image->width,
                'height' => $image->height,
            ] : []),
            ...($image->user ? ['creator' => [
                '@type' => 'Person',
                'name' => $image->user->username,
            ]] : []),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Tag> */
    private function popularTags()
    {
        return Tag::query()
            ->withCount(['images' => fn (Builder $query) => $query->publiclyVisible()])
            ->whereHas('images', fn (Builder $query) => $query->publiclyVisible())
            ->orderByDesc('images_count')
            ->limit(12)
            ->get();
    }

    private function publishedImagesQuery(): Builder
    {
        return Image::query()
            ->publiclyVisible()
            ->select('images.*')
            ->with(['user', 'artist'])
            ->selectSub(
                DB::table('images as artist_images')
                    ->selectRaw('count(*)')
                    ->where('artist_images.is_published', true)
                    ->where('artist_images.is_private', false)
                    ->whereNull('artist_images.deleted_at')
                    ->whereColumn('artist_images.artist_name', 'images.artist_name'),
                'artist_posts_count',
            )
            ->withCount(['likes', 'comments', 'bookmarks', 'additionalImages']);
    }

    private function applySort(Builder $query, string $sort, Request $request): void
    {
        match ($sort) {
            'views' => $query->orderByDesc('views')->orderByDesc('created_at'),
            'likes' => $query->orderByDesc('likes_count')->orderByDesc('created_at'),
            'random' => $this->applyRandomOrder($query, $request),
            default => $query->latest(),
        };
    }

    private function applyFollowingFilter(Builder $query, Request $request): void
    {
        $followingIds = Follow::query()
            ->where('follower_id', $request->user()->id)
            ->pluck('following_id');

        if ($followingIds->isEmpty()) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereIn('user_id', $followingIds);
    }

    private function applySearch(Builder $query, string $searchQuery): void
    {
        if ($searchQuery === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($searchQuery) {
            $builder->where('title', 'like', "%{$searchQuery}%")
                ->orWhere('description', 'like', "%{$searchQuery}%")
                ->orWhere('alt_text', 'like', "%{$searchQuery}%");
        });
    }

    private function resolveSort(Request $request): string
    {
        $sort = $request->route('sort', 'latest');

        return in_array($sort, self::SORT_OPTIONS, true) ? $sort : 'latest';
    }

    private function applyRandomOrder(Builder $query, Request $request): void
    {
        if ((int) $request->query('page', 1) === 1 || ! $request->session()->has(self::RANDOM_IDS_SESSION_KEY)) {
            $ids = Image::query()
                ->where('is_published', true)
                ->pluck('id')
                ->shuffle()
                ->values()
                ->all();

            $request->session()->put(self::RANDOM_IDS_SESSION_KEY, $ids);
        }

        $ids = $request->session()->get(self::RANDOM_IDS_SESSION_KEY, []);

        if ($ids === []) {
            $query->inRandomOrder();

            return;
        }

        $cases = collect($ids)
            ->map(fn (int $id, int $index) => "WHEN {$id} THEN {$index}")
            ->implode(' ');

        $query
            ->whereIn('id', $ids)
            ->orderByRaw("CASE id {$cases} END");
    }

    /** @return array{title: string, heading: string, description: string, canonical: string, noindex: bool} */
    private function pageMeta(string $sort, ?string $searchQuery, LengthAwarePaginator $images, ?Tag $tag = null, ?string $artistName = null, ?string $parodyName = null): array
    {
        if ($sort === 'tag' && $tag) {
            $title = "Tag: {$tag->name} — HeartLovePics";
            $description = "Browse images tagged with \"{$tag->name}\" in the HeartLovePics gallery.";

            return [
                'title' => $title,
                'heading' => "Tag: {$tag->name}",
                'description' => $description,
                'canonical' => $this->canonicalUrl($images, route('gallery.tag', $tag->name)),
                // Thin/empty tag pages have nothing for a crawler to index.
                'noindex' => $images->total() === 0,
            ];
        }

        if ($sort === 'artist' && $artistName) {
            $title = "Art by {$artistName} — HeartLovePics";
            $description = "Browse images credited to {$artistName} on HeartLovePics.";

            return [
                'title' => $title,
                'heading' => "Art by {$artistName}",
                'description' => $description,
                'canonical' => $this->canonicalUrl($images, route('gallery.artist', $artistName)),
                // Thin/empty artist pages have nothing for a crawler to index.
                'noindex' => $images->total() === 0,
            ];
        }

        if ($sort === 'parody' && $parodyName) {
            $title = "Parody: {$parodyName} — HeartLovePics";
            $description = "Browse images parodying {$parodyName} on HeartLovePics.";

            return [
                'title' => $title,
                'heading' => "Parody: {$parodyName}",
                'description' => $description,
                'canonical' => $this->canonicalUrl($images, route('gallery.parody', $parodyName)),
                'noindex' => $images->total() === 0,
            ];
        }

        if ($sort === 'search') {
            $title = $searchQuery
                ? "Search: {$searchQuery} — HeartLovePics"
                : 'Search — HeartLovePics';

            $description = $searchQuery
                ? "Search results for \"{$searchQuery}\" in the HeartLovePics gallery."
                : 'Search the HeartLovePics gallery by title or description.';

            return [
                'title' => $title,
                'heading' => $searchQuery ? "Results for \"{$searchQuery}\"" : 'Search the gallery',
                'description' => $description,
                'canonical' => $this->canonicalUrl($images, route('gallery.search', $searchQuery ? ['q' => $searchQuery] : [])),
                // Search result pages are unbounded (any query string) and mostly
                // thin/duplicate content — keep them out of the index entirely.
                'noindex' => true,
            ];
        }

        $sortMeta = [
            'latest' => [
                'title' => 'Latest — HeartLovePics',
                'heading' => 'Latest posts',
                'description' => 'Browse the latest images in HeartLovePics — a quiet collection of photos to explore, view, and like.',
                'route' => 'home',
            ],
            'views' => [
                'title' => 'Most Viewed — HeartLovePics',
                'heading' => 'Most viewed images',
                'description' => 'Discover the most viewed images on HeartLovePics. See what the community has been exploring lately.',
                'route' => 'gallery.views',
            ],
            'likes' => [
                'title' => 'Most Liked — HeartLovePics',
                'heading' => 'Most liked images',
                'description' => 'See the community\'s favorite images on HeartLovePics, sorted by the most likes.',
                'route' => 'gallery.likes',
            ],
            'random' => [
                'title' => 'Random — HeartLovePics',
                'heading' => 'Random images',
                'description' => 'Explore a fresh random shuffle of images from the HeartLovePics gallery.',
                'route' => 'gallery.random',
            ],
            'following' => [
                'title' => 'Following — HeartLovePics',
                'heading' => 'Images from people you follow',
                'description' => 'See the latest images from creators you follow on HeartLovePics.',
                'route' => 'gallery.following',
            ],
        ];

        $meta = $sortMeta[$sort] ?? $sortMeta['latest'];

        return [
            'title' => $meta['title'],
            'heading' => $meta['heading'],
            'description' => $meta['description'],
            'canonical' => $this->canonicalUrl($images, route($meta['route'])),
            // "Random" reshuffles on every request (duplicate/inconsistent content
            // for a crawler) and "following" is a personalized, auth-gated view —
            // neither is meaningful to index. Every other sort is real, stable,
            // crawlable content and stays indexable.
            'noindex' => in_array($sort, ['random', 'following'], true),
        ];
    }

    private function canonicalUrl(LengthAwarePaginator $images, string $baseUrl): string
    {
        if ($images->currentPage() <= 1) {
            return $baseUrl;
        }

        return $images->url($images->currentPage());
    }

    private function nestedRepliesQuery(int $remainingDepth = Comment::MAX_REPLY_DEPTH): Closure
    {
        return function ($query) use ($remainingDepth) {
            $query->withCount('likes')->with('user')->latest();

            if ($remainingDepth > 1) {
                $query->with(['replies' => $this->nestedRepliesQuery($remainingDepth - 1)]);
            }
        };
    }

    /** @param  Collection<int, Comment>  $comments */
    private function flattenComments(Collection $comments): Collection
    {
        return $comments->flatMap(function (Comment $comment) {
            return collect([$comment])->concat(
                $this->flattenComments($comment->replies)
            );
        });
    }
}
