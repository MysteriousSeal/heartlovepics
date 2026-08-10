<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Collection;
use App\Models\Image;
use App\Models\Journal;
use App\Models\Shout;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\BannerService;
use App\Services\BookmarkService;
use App\Services\CollectionService;
use App\Services\FollowService;
use App\Services\GalleryActivityService;
use App\Services\LikeService;
use App\Support\GalleryColumns;
use App\Support\HtmlSanitizer;
use App\Support\NsfwPreference;
use App\Support\Sparkline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user, Request $request, LikeService $likes, BookmarkService $bookmarks, FollowService $follows, GalleryActivityService $activity): View
    {
        $isOwner = $request->user()?->is($user) ?? false;

        $images = Image::query()
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->when(! $isOwner, fn ($query) => $query->where('is_private', false))
            ->with('user')
            ->withCount(['likes', 'comments', 'bookmarks', 'additionalImages'])
            ->latest()
            ->get();

        $likedMap = $likes->likedMapForImages($images, $request);
        $bookmarkMap = $bookmarks->bookmarkedMapForImages($images, $request);
        $columns = GalleryColumns::distribute($images);
        $showNsfw = NsfwPreference::isEnabled($request);
        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $images->count();
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $isOwner ? $this->bookmarkedImagesCount($user) : 0;
        $draftCount = $isOwner ? $this->draftImagesCount($user) : 0;
        $journalCount = $this->journalCount($user);
        $activeTab = 'uploads';
        $followData = $this->profileFollowData($user, $request, $follows);
        $shouts = Shout::where('user_id', $user->id)->with('author')->latest()->take(20)->get();
        $canShout = ($request->user() && ! $request->user()->is($user)) ? $request->user()->canPost() : false;

        return view('users.show', compact(
            'user',
            'images',
            'likedMap',
            'bookmarkMap',
            'columns',
            'showNsfw',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
            'shouts',
            'canShout',
        ) + $followData);
    }

    public function likes(User $user, Request $request, LikeService $likes, BookmarkService $bookmarks, FollowService $follows, GalleryActivityService $activity): View
    {
        $isOwner = $request->user()?->is($user) ?? false;

        $images = Image::query()
            ->publiclyVisible()
            ->whereHas('likes', fn ($query) => $query->where('user_id', $user->id))
            ->with('user')
            ->withCount(['likes', 'comments', 'bookmarks', 'additionalImages'])
            ->latest()
            ->get();

        $likedMap = $likes->likedMapForImages($images, $request);
        $bookmarkMap = $bookmarks->bookmarkedMapForImages($images, $request);
        $columns = GalleryColumns::distribute($images);
        $showNsfw = NsfwPreference::isEnabled($request);
        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $this->uploadedImagesCount($user, $request->user());
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $isOwner ? $this->bookmarkedImagesCount($user) : 0;
        $draftCount = $isOwner ? $this->draftImagesCount($user) : 0;
        $journalCount = $this->journalCount($user);
        $activeTab = 'likes';
        $followData = $this->profileFollowData($user, $request, $follows);

        return view('users.likes', compact(
            'user',
            'images',
            'likedMap',
            'bookmarkMap',
            'columns',
            'showNsfw',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
        ) + $followData);
    }

    public function bookmarks(User $user, Request $request, LikeService $likes, BookmarkService $bookmarks, FollowService $follows, GalleryActivityService $activity, CollectionService $collectionService): View
    {
        abort_unless($request->user()?->is($user), 403);

        $activeCollection = $request->filled('collection')
            ? Collection::where('user_id', $user->id)->find($request->integer('collection'))
            : null;

        $bookmarkedQuery = Image::query()
            ->publiclyVisible()
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id));

        $images = (clone $bookmarkedQuery)
            ->when($activeCollection, fn ($query) => $query->whereHas(
                'collections',
                fn ($query) => $query->where('collections.id', $activeCollection->id)
            ))
            ->with('user')
            ->withCount(['likes', 'comments', 'bookmarks', 'additionalImages'])
            ->latest()
            ->get();

        $likedMap = $likes->likedMapForImages($images, $request);
        $bookmarkMap = $bookmarks->bookmarkedMapForImages($images, $request);
        $columns = GalleryColumns::distribute($images);
        $showNsfw = NsfwPreference::isEnabled($request);
        $isOwner = true;
        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $this->uploadedImagesCount($user, $request->user());
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $bookmarkedQuery->count();
        $draftCount = $this->draftImagesCount($user);
        $journalCount = $this->journalCount($user);
        $activeTab = 'bookmarks';
        $followData = $this->profileFollowData($user, $request, $follows);
        $collections = $collectionService->forUser($user);

        return view('users.bookmarks', compact(
            'user',
            'images',
            'likedMap',
            'bookmarkMap',
            'columns',
            'showNsfw',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
            'collections',
            'activeCollection',
        ) + $followData);
    }

    public function drafts(User $user, Request $request, LikeService $likes, BookmarkService $bookmarks, FollowService $follows, GalleryActivityService $activity): View
    {
        abort_unless($request->user()?->is($user), 403);

        $images = Image::query()
            ->where('user_id', $user->id)
            ->where('is_published', false)
            ->with('user')
            ->withCount(['likes', 'comments', 'bookmarks', 'additionalImages'])
            ->latest()
            ->get();

        $likedMap = $likes->likedMapForImages($images, $request);
        $bookmarkMap = $bookmarks->bookmarkedMapForImages($images, $request);
        $columns = GalleryColumns::distribute($images);
        $showNsfw = NsfwPreference::isEnabled($request);
        $isOwner = true;
        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $this->uploadedImagesCount($user, $request->user());
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $this->bookmarkedImagesCount($user);
        $draftCount = $images->count();
        $journalCount = $this->journalCount($user);
        $activeTab = 'drafts';
        $followData = $this->profileFollowData($user, $request, $follows);

        return view('users.drafts', compact(
            'user',
            'images',
            'likedMap',
            'bookmarkMap',
            'columns',
            'showNsfw',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
        ) + $followData);
    }

    public function stats(User $user, Request $request, FollowService $follows, GalleryActivityService $activity): View
    {
        abort_unless($request->user()?->is($user), 403);

        $isOwner = true;
        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $this->uploadedImagesCount($user, $request->user());
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $this->bookmarkedImagesCount($user);
        $draftCount = $this->draftImagesCount($user);
        $journalCount = $this->journalCount($user);
        $activeTab = 'stats';
        $followData = $this->profileFollowData($user, $request, $follows);

        $allTime = $activity->allTimeStatsForUser($user);
        $trend = $activity->dailyTrendForUser($user, 30);
        $trendCharts = [
            'posts' => $this->sparklineFor($trend['posts']),
            'likes' => $this->sparklineFor($trend['likes']),
            'comments' => $this->sparklineFor($trend['comments']),
            'views' => $this->sparklineFor($trend['views']),
        ];

        return view('users.stats', compact(
            'user',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
            'allTime',
            'trendCharts',
        ) + $followData);
    }

    public function journal(User $user, Request $request, FollowService $follows, GalleryActivityService $activity): View
    {
        $isOwner = $request->user()?->is($user) ?? false;

        $journals = Journal::where('user_id', $user->id)->latest()->get();

        $ogImage = $user->og_image_url ?? $activity->defaultOgImageUrl();
        $uploadCount = $this->uploadedImagesCount($user, $request->user());
        $likedCount = $this->likedImagesCount($user);
        $bookmarkCount = $isOwner ? $this->bookmarkedImagesCount($user) : 0;
        $draftCount = $isOwner ? $this->draftImagesCount($user) : 0;
        $journalCount = $journals->count();
        $activeTab = 'journal';
        $followData = $this->profileFollowData($user, $request, $follows);

        return view('users.journal', compact(
            'user',
            'journals',
            'isOwner',
            'ogImage',
            'uploadCount',
            'likedCount',
            'bookmarkCount',
            'draftCount',
            'journalCount',
            'activeTab',
        ) + $followData);
    }

    /** @param  list<int>  $series
     *  @return array{points: string, area: string, total: int, max: int} */
    private function sparklineFor(array $series): array
    {
        return [
            'points' => Sparkline::points($series),
            'area' => Sparkline::areaPath($series),
            'total' => array_sum($series),
            'max' => $series === [] ? 0 : max($series),
        ];
    }

    /** @return array{isFollowing: bool, followersCount: int, followingCount: int} */
    private function profileFollowData(User $user, Request $request, FollowService $follows): array
    {
        $user->loadCount(['followers', 'following']);

        return [
            'isFollowing' => $follows->isFollowing($user, $request),
            'followersCount' => $user->followers_count,
            'followingCount' => $user->following_count,
        ];
    }

    private function uploadedImagesCount(User $user, ?User $viewer = null): int
    {
        $isOwner = $viewer?->is($user) ?? false;

        return Image::query()
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->when(! $isOwner, fn ($query) => $query->where('is_private', false))
            ->count();
    }

    private function likedImagesCount(User $user): int
    {
        return Image::query()
            ->publiclyVisible()
            ->whereHas('likes', fn ($query) => $query->where('user_id', $user->id))
            ->count();
    }

    private function draftImagesCount(User $user): int
    {
        return Image::query()
            ->where('user_id', $user->id)
            ->where('is_published', false)
            ->count();
    }

    private function bookmarkedImagesCount(User $user): int
    {
        return Image::query()
            ->where('is_published', true)
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->count();
    }

    private function journalCount(User $user): int
    {
        return Journal::where('user_id', $user->id)->count();
    }

    public function edit(Request $request): View
    {
        return view('users.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request, AvatarService $avatars, BannerService $banners): RedirectResponse
    {
        $user = $request->user();
        $data = [];

        if ($request->hasFile('avatar')) {
            $avatars->delete($user->avatar_path);
            $data['avatar_path'] = $avatars->store($request->file('avatar'));
        }

        if ($request->hasFile('banner')) {
            $banners->delete($user->banner_path);
            $data['banner_path'] = $banners->store($request->file('banner'));
        }

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        if ($request->filled('username')) {
            $data['username'] = $request->input('username');
        }

        if ($request->has('bio')) {
            // clean() returns null for markup that carries no visible text.
            $data['bio'] = HtmlSanitizer::clean($request->input('bio'));
        }

        $user->update($data);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Profile updated.');
    }

    /**
     * Live "is this username available" check used while editing the profile
     * form — same format rules as registration, minus the current user's own
     * row from the uniqueness check.
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $validator = Validator::make(
            ['username' => trim((string) $request->query('username'))],
            [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:30',
                    'alpha_dash',
                    Rule::unique('users', 'username')->ignore($request->user()->id),
                    Rule::notIn(['admin']),
                ],
            ],
        );

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => $validator->errors()->first('username'),
            ]);
        }

        return response()->json(['available' => true]);
    }
}