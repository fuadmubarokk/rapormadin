@if(count($guruMapelKelas) > 0)
    @foreach($guruMapelKelas as $gmk)
    <tr class="data-row" data-kelas-id="{{ $gmk->kelas_id }}">
        <td>{{ $guruMapelKelas->firstItem() + $loop->index }}</td>
        <td>{{ $gmk->guru->name }}</td>
        <td>{{ $gmk->kelas->nama_kelas }}</td>
        <td>{{ $gmk->mapel->nama_mapel }}</td>
        <td class="action-buttons">
            <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="{{ $gmk->id }}" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $gmk->id }}" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Belum ada data penugasan guru</h5>
                <p>Klik tombol "Tambah Penugasan" untuk membuat penugasan baru</p>
            </div>
        </td>
    </tr>
@endif