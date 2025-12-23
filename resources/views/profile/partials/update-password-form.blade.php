<form method="post" action="{{ route('profile.update') }}" class="mt-6">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="current_password">{{ __('Pasword Lama') }}</label>
                <input id="current_password" name="current_password" class="form-control mt-1" type="password" required>
                @error('current_password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="password">{{ __('Password Baru') }}</label>
                <input id="password" name="password" class="form-control mt-1" type="password" required autocomplete="new-password">
                @error('password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="password_confirmation">{{ __('Konfirmasi Password') }}</label>
                <input id="password_confirmation" name="password_confirmation" class="form-control mt-1" type="password" required autocomplete="new-password">
                @error('password_confirmation')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
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