@extends('layouts.app')

@section('title', 'Daftar Rapor Santri')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            
            {{-- TAMPILKAN DROPDOWN HANYA UNTUK ADMIN --}}
            @if(auth()->user()->isAdmin())
                <div class="card card-primary card-outline mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-2"></i>
                            Pilih Kelas untuk Dikelola
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Silakan pilih kelas untuk melihat daftar rapor santri.</p>
                        
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <div class="form-group">
                                    <label for="kelas-select" class="form-label">Nama Kelas</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-school"></i>
                                            </span>
                                        </div>
                                        <select class="form-control" id="kelas-select">
                                            <option value="">-- Pilih Kelas --</option>
                                            @if(isset($kelasList))
                                                @foreach($kelasList as $kelas)
                                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end justify-content-end">
                                {{-- Anda bisa menambahkan tombol aksi di sini jika perlu --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- KONTAINER UNTUK LOADING INDICATOR --}}
            <div id="loading" style="display: none; text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p class="mt-2">Memuat data...</p>
            </div>

            {{-- KONTAINER UNTUK ISI KELAS (AKAN DIISI OLEH AJAX ATAU LANGSUNG) --}}
            <div id="kelas-content-container">
                {{-- Jika Admin, area ini awalnya kosong. Jika Wali, langsung diisi. --}}
                @if(auth()->user()->isWaliKelas() && isset($kelas))
                    @include('rapor._kelas_content')
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
 $(document).ready(function() {
    $('#kelas-select').on('change', function() {
        var kelasId = $(this).val();

        if (kelasId) {
            // Tampilkan loading, sembunyikan konten lama
            $('#loading').show();
            $('#kelas-content-container').hide();

            // Buat AJAX request
            $.ajax({
                url: `{{ route('rapor.data', ':kelasId') }}`.replace(':kelasId', kelasId),
                type: 'GET',
                success: function(response) {
                    // Sembunyikan loading
                    $('#loading').hide();
                    
                    // Isi kontainer dengan HTML yang dikembalikan dari server
                    $('#kelas-content-container').html(response.html).fadeIn();
                },
                error: function(xhr) {
                    // Sembunyikan loading
                    $('#loading').hide();
                    $('#kelas-content-container').show();

                    // Tampilkan pesan error
                    var errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan saat memuat data.';
                    $('#kelas-content-container').html(`<div class="alert alert-danger">${errorMessage}</div>`);
                }
            });
        } else {
            // Jika dropdown dikosongkan, kosongkan juga kontainer
            $('#kelas-content-container').html('');
        }
    });
});
</script>
@endpush