<nav class="campus-shortcuts mb-4" aria-label="{{ $label ?? 'Quick access' }}">
    @foreach($links as $link)
        <a class="campus-shortcut" href="{{ route($link['route']) }}">
            <span class="campus-shortcut-icon"><i class="fa-solid {{ $link['icon'] }}" aria-hidden="true"></i></span>
            <span><strong>{{ $link['title'] }}</strong><small>{{ $link['description'] }}</small></span>
            <i class="fa-solid fa-arrow-up-right-from-square campus-shortcut-arrow" aria-hidden="true"></i>
        </a>
    @endforeach
</nav>
