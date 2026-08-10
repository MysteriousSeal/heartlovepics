<div class="empty-state">
    <p class="empty-state-message">{{ $message }}</p>

    @if (! empty($suggestions))
        <ul class="empty-state-suggestions">
            @foreach ($suggestions as $suggestion)
                <li>
                    <a href="{{ $suggestion['url'] }}">{{ $suggestion['label'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>