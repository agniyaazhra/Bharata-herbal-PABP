@props(['crumbs' => []])

@if (count($crumbs) > 0)
<nav aria-label="breadcrumb" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
    <ol class="flex items-center flex-wrap gap-1 text-sm text-gray-500">
        @foreach ($crumbs as $index => $crumb)
            @if ($index > 0)
                <li class="flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
            @endif
            <li>
                @if (isset($crumb['url']) && !$loop->last)
                    <a href="{{ $crumb['url'] }}"
                       class="hover:text-herbal-700 transition-colors font-medium">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span class="{{ $loop->last ? 'text-herbal-700 font-semibold' : '' }}">
                        {{ $crumb['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
