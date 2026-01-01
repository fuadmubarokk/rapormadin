<div class="table-responsive">
    <table class="table table-bordered table-striped" id="siswa-table">
        <thead class="bg-light">
            <tr>
                <th style="width:50px;" class="d-none d-md-table-cell">No</th>
                <th style="width:50px;">Foto</th>
                <th class="sortable" data-column="nisn" data-sort="nisn">
                    NISN
                    <i class="fas fa-sort ms-1 sort-icon"></i>
                </th>
                <th class="sortable" data-column="nama" data-sort="nama">
                    Nama
                    <i class="fas fa-sort ms-1 sort-icon"></i>
                </th>
                <th class="sortable d-none d-sm-table-cell" data-column="kelas" data-sort="kelas_id">
                    Kelas
                    <i class="fas fa-sort ms-1 sort-icon"></i>
                </th>
                <th style="width:130px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $s)
            <tr> <!-- Hapus data attributes -->
                <td class="d-none d-md-table-cell">{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}</td>
                <td style="text-align: center; vertical-align: middle;">
                    <img src="{{ !empty($s->foto) ? asset('img/foto_siswa/' . $s->foto) : asset('img/logo.png') }}" 
                         style="max-height: 40px; width: auto; border-radius:5px; border: 1px solid #ddd;" 
                         alt="Foto Siswa"
                         class="img-fluid">
                </td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nama }}</td>
                <td class="d-none d-sm-table-cell">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    <button type="button" 
                            class="btn btn-warning btn-sm"
                            onclick="openEditModal({{ $s->id }})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-danger btn-sm btnHapusSatuan ms-1"
                            data-id="{{ $s->id }}">
                        <i class="fas fa-trash"></i>
                    </button>

                    <form id="formHapusSatuan-{{ $s->id }}" 
                        action="{{ route('admin.siswa.destroy', $s->id) }}" 
                        method="POST" 
                        style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<!-- Pagination -->
@if($siswa->hasPages())
<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
    <div class="text-muted mb-2 mb-md-0">
        Menampilkan {{ $siswa->firstItem() ?? 0 }} - {{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} data
    </div>
    <div>
        <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if($siswa->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">«</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" 
                           href="{{ $siswa->previousPageUrl() }}" 
                           data-page="{{ $siswa->currentPage() - 1 }}">
                            «
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $current = $siswa->currentPage();
                    $last = $siswa->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                    
                    // Adjust for small number of pages
                    if ($last <= 5) {
                        $start = 1;
                        $end = $last;
                    } elseif ($current <= 3) {
                        $start = 1;
                        $end = 5;
                    } elseif ($current >= $last - 2) {
                        $start = $last - 4;
                        $end = $last;
                    }
                @endphp

                {{-- First Page --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $siswa->url(1) }}" data-page="1">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                {{-- Page Numbers --}}
                @for($i = $start; $i <= $end; $i++)
                    @if($i == $current)
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $siswa->url($i) }}" data-page="{{ $i }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if($end < $last)
                    @if($end < $last - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $siswa->url($last) }}" data-page="{{ $last }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($siswa->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" 
                           href="{{ $siswa->nextPageUrl() }}" 
                           data-page="{{ $siswa->currentPage() + 1 }}">
                            »
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endif