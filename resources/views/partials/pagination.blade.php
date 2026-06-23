@if ($paginator->hasPages())
    <div style="display:inline-flex; gap:0.35rem; align-items:center;" class="custom-pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn disabled">
                <i class="fa-solid fa-chevron-left" style="font-size:0.75rem;"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn">
                <i class="fa-solid fa-chevron-left" style="font-size:0.75rem;"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="pagination-ellipsis">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn">
                <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i>
            </a>
        @else
            <span class="pagination-btn disabled">
                <i class="fa-solid fa-chevron-right" style="font-size:0.75rem;"></i>
            </span>
        @endif
    </div>

    <style>
        .custom-pagination {
            user-select: none;
        }
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--r-md);
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
            background: var(--bg-surface);
        }
        .pagination-btn:hover:not(.disabled):not(.active) {
            background: var(--bg-hover);
            border-color: var(--border-strong) !important;
            color: var(--accent) !important;
            transform: translateY(-1px);
        }
        .pagination-btn.active {
            background: var(--accent) !important;
            color: var(--accent-fg) !important;
            border-color: var(--accent) !important;
            font-weight: 700;
            box-shadow: 0 4px 10px var(--accent-alpha);
        }
        .pagination-btn.disabled {
            color: var(--text-muted);
            cursor: not-allowed;
            opacity: 0.5;
            background: var(--bg-hover);
        }
        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
@endif
