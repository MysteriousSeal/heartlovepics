@extends('layouts.app')

@section('title', 'Upload complete — HeartLovePics')

@section('content')
    <section class="upload-success">
        <div class="upload-success-card">
            <p class="upload-success-eyebrow">Upload complete</p>
            <h2 class="upload-success-title">{{ $image->title }}</h2>

            @if ($image->additionalImages->isNotEmpty())
                <div class="upload-success-gallery">
                    <div class="upload-success-gallery-item">
                        <img src="{{ $image->thumbnail_url }}" alt="{{ $image->alt_text ?? $image->title }}">
                    </div>
                    @foreach ($image->additionalImages as $extra)
                        <div class="upload-success-gallery-item">
                            <img src="{{ $extra->thumbnail_url }}" alt="">
                        </div>
                    @endforeach
                </div>

                <p class="upload-success-message">
                    Your post with {{ $image->additionalImages->count() + 1 }} images is live on HeartLovePics.
                </p>
            @else
                <img
                    class="upload-success-thumb"
                    src="{{ $image->thumbnail_url }}"
                    alt="{{ $image->alt_text ?? $image->title }}"
                    width="160"
                    height="160"
                >

                <p class="upload-success-message">Your image is live on HeartLovePics.</p>
            @endif

            <div class="upload-success-actions">
                <a href="{{ route('images.show', $image->slug) }}" class="btn btn-primary">View post</a>
                <a href="{{ route('profile.images.create') }}" class="btn btn-secondary">Upload another</a>
            </div>
        </div>
    </section>
@endsection