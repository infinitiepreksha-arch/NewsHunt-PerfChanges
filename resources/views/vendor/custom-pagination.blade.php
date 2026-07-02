@if ($paginator->hasPages())
    <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary text-black" data-uc-margin="" >
        <li{{ ($paginator->currentPage() == 1) ? ' class="uc-disabled"' : '' }}>
            @if ($paginator->currentPage() == 1)
                <span class="icon icon-1 unicon-chevron-left"></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"><span class="icon icon-1 unicon-chevron-left"></span></a>
            @endif
        </li>
        @if ($paginator->currentPage() > 3)
            <li><a href="{{ $paginator->url(1) }}">1</a></li>
            <li class="uc-disabled"><span>…</span></li>
        @endif
        @for ($i = $paginator->currentPage() - 1; $i <= $paginator->currentPage() + 1; $i++)
            @if ($i > 0 && $i <= $paginator->lastPage())
                <li{{ ($i == $paginator->currentPage()) ? ' class="uc-active"' : '' }}>
                    @if ($i == $paginator->currentPage())
                        <a class="uc-active" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    @else
                        <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    @endif
                </li>
            @endif
        @endfor
        @if ($paginator->currentPage() < $paginator->lastPage() - 2)
            <li class="uc-disabled"><span>…</span></li>
            <li><a href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a></li>
        @endif
        <li{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' class="uc-disabled"' : '' }}>
            @if ($paginator->currentPage() == $paginator->lastPage())
                <span class="icon icon-1 unicon-chevron-right"></span>
            @else
                <a href="{{ $paginator->nextPageUrl() }}"><span class="icon icon-1 unicon-chevron-right"></span></a>
            @endif
        </li>
    </ul>
@endif