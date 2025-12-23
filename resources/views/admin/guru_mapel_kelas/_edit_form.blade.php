<div class="form-group">
    <label for="guru_id_edit"><i class="fas fa-user-tie mr-1"></i> Guru</label>
    <select name="guru_id" id="guru_id_edit" class="form-control" required>
        <option value="">-- Pilih Guru --</option>
        @foreach($guru as $g)
        <option value="{{ $g->id }}" {{ $gmk->guru_id == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="mapel_id_edit"><i class="fas fa-book mr-1"></i> Mapel</label>
    <select name="mapel_id" id="mapel_id_edit" class="form-control" required>
        <option value="">-- Pilih Mapel --</option>
        @foreach($mapel as $m)
        <option value="{{ $m->id }}" {{ $gmk->mapel_id == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="kelas_id_edit"><i class="fas fa-school mr-1"></i> Kelas</label>
    <select name="kelas_id" id="kelas_id_edit" class="form-control" required>
        <option value="">-- Pilih Kelas --</option>
        @foreach($kelas as $k)
        <option value="{{ $k->id }}" {{ $gmk->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
        @endforeach
    </select>
</div>