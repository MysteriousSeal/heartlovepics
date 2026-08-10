<div class="image-stats">
    @include('partials.view-count', ['image' => $image])
    @include('partials.comment-count', ['image' => $image])
    @include('partials.like-button', [
        'image' => $image,
        'liked' => $liked ?? false,
    ])
    @include('partials.bookmark-button', [
        'image' => $image,
        'bookmarked' => $bookmarked ?? false,
    ])
</div>