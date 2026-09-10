@php
    $locales = [
        'en' => 'English',
        'gu' => 'ગુજરાતી',
        'hi' => 'हिन्दी',
    ];
    $current = app()->getLocale();
@endphp
<div class="dropdown" role="navigation" aria-label="{{ __('public.language') }}">
    <button type="button"
            class="btn btn-sm btn-outline-light dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-haspopup="true">
        <i class="fa-solid fa-globe me-1" aria-hidden="true"></i>
        <span>{{ $locales[$current] ?? 'English' }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
        @foreach($locales as $code => $label)
            <li>
                <a class="dropdown-item {{ $current === $code ? 'active fw-bold' : '' }}"
                   href="{{ route('locale.switch', $code) }}"
                   hreflang="{{ $code }}"
                   lang="{{ $code }}">
                    {{ $label }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
