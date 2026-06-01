@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 px-2">
        <div class="text-muted small">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries
        </div>

        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                {{-- Tombol Previous --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link shadow-sm text-muted px-3" style="border-radius: 8px;">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link shadow-sm text-secondary px-3" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border-radius: 8px;">Previous</a>
                    </li>
                @endif

                {{-- Nomor Halaman Berjalan --}}
                @foreach ($elements as $element)
                    {{-- Pembatas Tiga Titik "..." --}}
                    @if (is_string($element))
                        <li class="page-item disabled"><span class="page-link border-0 bg-transparent text-muted">{{ $element }}</span></li>
                    @endif

                    {{-- Link Angka Elemen --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link shadow-sm border-0 px-3 text-white fw-bold" style="background-color: #6c757d; border-radius: 8px;">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link shadow-sm text-secondary px-3" href="{{ $url }}" style="border-radius: 8px;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link shadow-sm text-secondary px-3" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border-radius: 8px;">Next</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link shadow-sm text-muted px-3" style="border-radius: 8px;">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif