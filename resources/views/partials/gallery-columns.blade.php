@foreach ($columns as $column)
    <div class="masonry-column">
        @foreach ($column as $image)
            @include('partials.gallery-item', [
                'image' => $image,
                'likedMap' => $likedMap,
                'bookmarkMap' => $bookmarkMap ?? [],
                'lcpImageId' => $lcpImage?->id,
                'isFirstPage' => $isFirstPage ?? false,
                'showNsfw' => $showNsfw ?? false,
                'showGalleryAuthors' => $showGalleryAuthors ?? true,
                'showGalleryArtists' => $showGalleryArtists ?? false,
            ])
        @endforeach
    </div>
@endforeach
