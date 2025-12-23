<form method="post" action="{{ route('profile.update') }}" class="mt-6">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="name">{{ __('Nama') }}</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                @error('name')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>


    <div class="row mt-4">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="no_hp">{{ __('No. HP') }}</label>
                <input id="no_hp" name="no_hp" type="text" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                @error('no_hp')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="foto">{{ __('Foto') }}</label>
                <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror">
                <small class="form-text text-muted">Format foto harus JPG, PNG, JPEG. Maksimal 2MB.</small>
                @error('foto')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                @if($user->foto)
                <small class="form-text text-muted">Foto saat ini:</small>
                <small><img src="{{ auth()->user()->foto ? asset('img/foto_guru/' . auth()->user()->foto) : asset('img/profil.png') }}" width="50" class="img-thumbnail"></small>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-secondary mr-2" href="{{ route('profile.edit') }}">
            {{ __('Batal') }}
        </a>

        <button type="submit" class="btn btn-primary">
            {{ __('Simpan') }}
        </button>
    </div>
</form>