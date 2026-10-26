@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-600">
        {{-- Keterangan Baris / Total --}}
        <div>
            <p class="text-xs text-gray-500">
                Menampilkan 
                @if ($paginator->firstItem())
                    <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}</span>
                    sampai 
                    <span class="font-semibold text-gray-900">{{ $paginator->lastItem() }}</span>
                @else
                    <span class="font-semibold text-gray-900">{{ $paginator->count() }}</span>
                @endif
                dari total 
                <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span> 
                data
            </p>
        </div>

        {{-- Navigasi Tombol Angka --}}
        <div>
            <ul class="inline-flex items-center -space-x-px rounded border border-gray-300 bg-white shadow-sm overflow-hidden text-xs">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <li>
                        <span class="inline-flex items-center px-2.5 py-1.5 text-gray-300 cursor-not-allowed bg-gray-50" aria-disabled="true">
                            <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Sebelumnya
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                            <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Sebelumnya
                        </a>
                    </li>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- Titik-titik (...) Pemisah --}}
                    @if (is_string($element))
                        <li>
                            <span class="inline-flex items-center px-3 py-1.5 text-gray-400 bg-gray-50 border-x border-gray-200">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    {{-- Daftar Link Angka --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <span aria-current="page" class="inline-flex items-center px-3 py-1.5 font-semibold text-white bg-gray-900">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="inline-flex items-center px-3 py-1.5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Selanjutnya --}}
                @if ($paginator->hasMorePages())
                    <li>
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                            Selanjutnya <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                        </a>
                    </li>
                @else
                    <li>
                        <span class="inline-flex items-center px-2.5 py-1.5 text-gray-300 cursor-not-allowed bg-gray-50" aria-disabled="true">
                            Selanjutnya <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
