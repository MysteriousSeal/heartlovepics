@extends('layouts.app')

@section('title', 'Something went wrong — HeartLovePics')
@section('meta_description', 'Something went wrong on our end.')

@section('content')
    <section class="error-page">
        <p class="error-page-code" aria-hidden="true">500</p>
        <h2 class="error-page-title">Something broke on our end</h2>
        <p class="error-page-text">
            That's on us, not you. Try again in a moment — if it keeps happening, come back later.
        </p>

        <ul class="error-page-links">
            <li><a href="{{ route('home') }}">Back to the gallery</a></li>
        </ul>
    </section>
@endsection
