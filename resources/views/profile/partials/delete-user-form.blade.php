{{-- Ganti <x-action-section> dengan div biasa --}}
<div>
    <h5>{{ __('Hapus Akun') }}</h5>

    <p class="text-muted">{{ __('Apakah Anda yakin ingin menghapus akun Anda? Setelah akun dihapus, semua data dan sumber dayanya akan dihapus secara permanen.') }}</p>

    <div>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
            @csrf
            @method('DELETE')

            <div class="form-group">
                {{-- Ganti <x-input-label> dengan <label> --}}
                <label for="password">{{ __('Password') }}</label>
                {{-- Ganti <x-password-input> dengan <input type="password"> --}}
                <input id="password" name="password" class="form-control mt-1" type="password" required>
                {{-- Ganti <x-input-error> dengan @error --}}
                @error('password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{-- Ganti <x-action-link> dengan <a> --}}
                <a class="btn btn-secondary mr-2" href="{{ route('profile.edit') }}">
                    {{ __('Batal') }}
                </a>

                {{-- Ganti <x-danger-button> dengan <button type="submit"> --}}
                <button type="submit" class="btn btn-danger">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </div>
</div>