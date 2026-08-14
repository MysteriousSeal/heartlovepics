<style>{!! file_get_contents(public_path('css/critical.css')) !!}</style>
@include('partials.font-preload')
<link rel="preload" href="{{ asset('css/app.css') }}?v={{ config('app.version') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ config('app.version') }}"></noscript>