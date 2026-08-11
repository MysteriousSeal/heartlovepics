@foreach ($images as $image)
    @include('partials.gallery-item', [
        'image' => $image,
        'likedMap' => $likedMap,
        'bookmarkMap' => $bookmarkMap ?? [],
        'showNsfw' => $showNsfw ?? false,
        'showGalleryAuthors' => $showGalleryAuthors ?? true,
        'showGalleryArtists' => $showGalleryArtists ?? false,
    ])
@endforeach
