@extends('layouts.app')

@section('title', 'Edit Journal Entry — HeartLovePics')

@section('content')
    <div class="page-header">
        <h2>Edit journal entry</h2>
        <a href="{{ route('journals.show', $journal) }}" class="btn btn-secondary">Back to entry</a>
    </div>

    @include('partials.flash')

    <div class="form-card journal-form-card">
        <form method="POST" action="{{ route('profile.journal.update', $journal) }}">
            @csrf
            @method('PUT')
            @include('partials.journal-form', ['journal' => $journal])

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('journals.show', $journal) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <form
            method="POST"
            action="{{ route('profile.journal.destroy', $journal) }}"
            class="image-delete-form"
            data-confirm="Delete this journal entry permanently? This cannot be undone."
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete entry</button>
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
