<span class="public-header__inquiry-label">
    {{ __('Anfrageliste') }}

    @if ($count > 0)
        <span
            data-inquiry-count-badge
            data-inquiry-count="{{ $count }}"
            class="public-header__inquiry-badge"
        >
            {{ $count }}
        </span>
    @endif
</span>
