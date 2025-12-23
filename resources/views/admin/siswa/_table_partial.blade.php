{{-- resources/views/admin/siswa/_table.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="bg-light">
            <tr>
                <th style="width:50px;">No</th>
                <th @click="$parent.sortBy(1)" style="cursor: pointer;">
                    NIS
                    <i class="fas fa-sort ml-1 sort-icon"></i>
                </th>
                <th @click="$parent.sortBy(2)" style="cursor: pointer;">
                    Nama
                    <i class="fas fa-sort ml-1 sort-icon"></i>
                </th>
                <th @click="$parent.sortBy(3)" style="cursor: pointer;">
                    Kelas
                    <i class="fas fa-sort ml-1 sort-icon"></i>
                </th>
                <th style="width:130px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $s)
            <tr>
                <td>{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}</td>
                <td>{{ $s->nisn }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->kelas->nama_kelas }}</td>
                <td>
                    <a href="{{ route('admin.siswa.edit', $s->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm btnHapusSatuan"
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
                    <td colspan="5" class="text-center">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Tidak ada data santri yang ditemukan.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex justify-content-end">
    {!! $siswa->links('pagination::bootstrap-4') !!}
</div>