<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Urutan Mapel: {{ $angkatan->nama_angkatan }}</title>
    <!-- Gunakan stylesheet utama aplikasi Anda jika ada -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style untuk drag-and-drop */
        .sortable-item { 
            cursor: move; 
            transition: background-color 0.2s;
        }
        .sortable-item:hover { 
            background-color: #f0f0f0; 
        }
        .sortable-ghost { 
            opacity: 0.4; 
            background: #c8ebfb; 
        }
        .add-mapel-item { 
            cursor: pointer; 
            transition: background-color 0.2s;
        }
        .add-mapel-item:hover { 
            background-color: #e9ecef; 
        }
    </style>
</head>
<body>
<!-- Anda bisa bungkus ini dengan layout utama aplikasi Anda -->
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Atur Urutan Mapel: {{ $angkatan->nama_angkatan }}</h2>
        <div>
            <label for="angkatan-selector" class="form-label">Pilih Angkatan:</label>
            <select id="angkatan-selector" class="form-select" onchange="changeAngkatan()">
                @foreach (\App\Models\Angkatan::all() as $a)
                    <option value="{{ route('admin.mapel.urutan', $a->id) }}" {{ $a->id == $angkatan->id ? 'selected' : '' }}>
                        {{ $a->nama_angkatan }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h5>Mapel Aktif (Seret untuk mengatur urutan)</h5>
            <form id="formUrutan" action="{{ route('admin.mapel.urutan.update') }}" method="POST">
                @csrf
                <input type="hidden" name="angkatan_id" value="{{ $angkatan->id }}">
                <ul id="sortable-mapel" class="list-group">
                    @forelse ($mapelDiAngkatan as $mapel)
                        <li class="list-group-item sortable-item" data-id="{{ $mapel->id }}">
                            {{ $mapel->nama_mapel }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada mapel untuk angkatan ini. Tambahkan dari sebelah kanan.</li>
                    @endforelse
                </ul>
            </form>
        </div>
        <div class="col-md-4">
            <h5>Mapel Tersedia (Klik untuk menambahkan)</h5>
            <ul id="available-mapel" class="list-group">
                @forelse ($mapelTersedia as $mapel)
                    <li class="list-group-item add-mapel-item" data-id="{{ $mapel->id }}">
                        <i class="bi bi-plus-circle"></i> {{ $mapel->nama_mapel }}
                    </li>
                @empty
                    <li class="list-group-item text-muted">Semua mapel sudah ditambahkan.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Script untuk drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sortableEl = document.getElementById('sortable-mapel');
    const availableEl = document.getElementById('available-mapel');
    const form = document.getElementById('formUrutan');

    // Inisialisasi SortableJS pada daftar mapel aktif
    Sortable.create(sortableEl, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: saveOrder // Panggil fungsi saveOrder setelah selesai menyeret
    });

    // Event listener untuk menambahkan mapel dari daftar tersedia
    availableEl.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-mapel-item')) {
            const mapelId = e.target.dataset.id;
            const mapelName = e.target.innerText.trim().substring(2); // Hilangkan ikon
            
            // Buat elemen baru untuk ditambahkan ke daftar aktif
            const newItem = document.createElement('li');
            newItem.className = 'list-group-item sortable-item';
            newItem.dataset.id = mapelId;
            newItem.textContent = mapelName;

            sortableEl.appendChild(newItem);
            e.target.remove(); // Hapus dari daftar tersedia
            
            saveOrder(); // Simpan perubahan
        }
    });

    function saveOrder() {
        const mapelIds = Array.from(sortableEl.querySelectorAll('.sortable-item')).map(item => item.dataset.id);
        
        fetch(form.action, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value 
            },
            body: JSON.stringify({ 
                urutan: mapelIds, 
                angkatan_id: form.querySelector('input[name="angkatan_id"]').value 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                alert('Gagal memperbarui: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

function changeAngkatan() {
    window.location.href = document.getElementById('angkatan-selector').value;
}
</script>
</body>
</html>