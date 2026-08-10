@extends('layouts.app')

@section('title', 'New Journal Entry — HeartLovePics')

@section('content')
    <div class="page-header">
        <h2>New journal entry</h2>
        <a href="{{ route('users.journal', auth()->user()) }}" class="btn btn-secondary">Back</a>
    </div>

    @include('partials.flash')

    <div class="form-card journal-form-card">
        <form method="POST" action="{{ route('profile.journal.store') }}">
            @csrf
            @include('partials.journal-form')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Post entry</button>
                <a href="{{ route('users.journal', auth()->user()) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('meta')
    <link rel="stylesheet" href="{{ asset('css/vendor/quill.snow.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/vendor/quill.js') }}"></script>
    <script src="{{ asset('js/description-editor.js') }}" defer></script>
    <script src="{{ asset('js/journal-title-counter.js') }}" defer></script>
@endpush
