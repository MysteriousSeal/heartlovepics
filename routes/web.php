<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShoutController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-tags.xml', [SitemapController::class, 'tags'])->name('sitemap.tags');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/sitemap-users.xml', [SitemapController::class, 'users'])->name('sitemap.users');
Route::get('/sitemap-artists.xml', [SitemapController::class, 'artists'])->name('sitemap.artists');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/changelog', [PageController::class, 'changelog'])->name('pages.changelog');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('pages.contact.submit');
Route::post('/preferences/nsfw', [PreferenceController::class, 'updateNsfw'])->name('preferences.nsfw');
Route::post('/preferences/theme', [PreferenceController::class, 'updateTheme'])->name('preferences.theme');
Route::get('/tags/suggest', [TagController::class, 'suggest'])->name('tags.suggest');

// Dev-only route to preview the styled 500 error page. Never exposed in production.
if (! app()->environment('production')) {
    Route::get('/test-500', function () {
        abort(500, 'This is a test error page.');
    })->name('test.500');
}

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');
    Route::get('/register/suggest-username', [RegisterController::class, 'suggestUsername'])->name('register.suggest-username');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.submit');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/likes', [ProfileController::class, 'likes'])->name('users.likes');
Route::get('/users/{user}/journal', [ProfileController::class, 'journal'])->name('users.journal');
Route::get('/users/{user}/bookmarks', [ProfileController::class, 'bookmarks'])->middleware('auth')->name('users.bookmarks');
Route::get('/users/{user}/drafts', [ProfileController::class, 'drafts'])->middleware('auth')->name('users.drafts');
Route::get('/journals/{journal}', [JournalController::class, 'show'])->name('journals.show');
Route::get('/profile/upload', [UserImageController::class, 'create'])->name('profile.images.create');
Route::post('/profile/upload', [UserImageController::class, 'store'])->name('profile.images.store');

Route::middleware('auth')->group(function () {
    Route::get('/users/{user}/stats', [ProfileController::class, 'stats'])->name('users.stats');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/check-username', [ProfileController::class, 'checkUsername'])->name('profile.check-username');
    Route::get('/profile/images/{image}/edit', [UserImageController::class, 'edit'])->name('profile.images.edit');
    Route::put('/profile/images/{image}', [UserImageController::class, 'update'])->name('profile.images.update');
    Route::patch('/profile/images/{image}/publish', [UserImageController::class, 'publish'])->name('profile.images.publish');
    Route::delete('/profile/images/{image}', [UserImageController::class, 'destroy'])->name('profile.images.destroy');
    Route::post('/profile/images/{image}/restore', [UserImageController::class, 'restore'])->name('profile.images.restore');
    Route::post('/images/{slug}/bookmark', [BookmarkController::class, 'toggle'])->name('images.bookmark');
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    Route::post('/users/{user}/shouts', [ShoutController::class, 'store'])->name('shouts.store');
    Route::delete('/shouts/{shout}', [ShoutController::class, 'destroy'])->name('shouts.destroy');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::put('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::post('/collections/{collection}/images/{image}', [CollectionController::class, 'toggleImage'])->name('collections.images.toggle');
    Route::get('/profile/journal/create', [JournalController::class, 'create'])->name('profile.journal.create');
    Route::post('/profile/journal', [JournalController::class, 'store'])->name('profile.journal.store');
    Route::get('/profile/journal/{journal}/edit', [JournalController::class, 'edit'])->name('profile.journal.edit');
    Route::put('/profile/journal/{journal}', [JournalController::class, 'update'])->name('profile.journal.update');
    Route::delete('/profile/journal/{journal}', [JournalController::class, 'destroy'])->name('profile.journal.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/visit', [NotificationController::class, 'visitGroup'])->name('notifications.visit');
    Route::put('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/profile/upload/success/{image}', [UserImageController::class, 'success'])->name('profile.images.success');
});

Route::get('/', [HomeController::class, 'index'])->defaults('sort', 'latest')->name('home');
Route::get('/views', [HomeController::class, 'index'])->defaults('sort', 'views')->name('gallery.views');
Route::get('/likes', [HomeController::class, 'index'])->defaults('sort', 'likes')->name('gallery.likes');
Route::get('/random', [HomeController::class, 'index'])->defaults('sort', 'random')->name('gallery.random');
Route::get('/following', [HomeController::class, 'index'])->defaults('sort', 'following')->middleware('auth')->name('gallery.following');
Route::get('/search', [HomeController::class, 'search'])->name('gallery.search');
Route::get('/tags', [HomeController::class, 'tags'])->name('gallery.tags');
Route::get('/tags/{tag}', [HomeController::class, 'tag'])->name('gallery.tag');
Route::get('/artists/{artist}', [HomeController::class, 'artist'])->name('gallery.artist');
Route::get('/images/{slug}', [HomeController::class, 'show'])->name('images.show');
Route::post('/images/{slug}/like', [LikeController::class, 'toggle'])->name('images.like');
Route::post('/images/{slug}/comments', [CommentController::class, 'store'])->name('images.comments.store');
Route::post('/comments/{comment}/like', [CommentLikeController::class, 'toggle'])->name('comments.like');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware('admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('images', ImageController::class)->except(['show', 'create', 'store']);
        Route::resource('artists', ArtistController::class)->except(['show', 'create']);
        Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::post('messages/{message}/reply', [ContactMessageController::class, 'reply'])->name('messages.reply');
        Route::post('messages/{message}/archive', [ContactMessageController::class, 'archive'])->name('messages.archive');
        Route::post('messages/{message}/unarchive', [ContactMessageController::class, 'unarchive'])->name('messages.unarchive');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('configuration', [ConfigurationController::class, 'edit'])->name('configuration.edit');
        Route::put('configuration', [ConfigurationController::class, 'update'])->name('configuration.update');
    });
});
