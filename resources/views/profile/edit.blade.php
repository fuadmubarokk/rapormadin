@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil Saya</h3>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            <div class="alert-body">
                                {{ __('Profil berhasil diperbarui!') }}
                            </div>
                        </div>
                    @endif

                    <div class="nav nav-tabs nav-tabs-bordered" id="profile-tab" role="tablist">
                        <button class="nav-link active" id="profile-information-tab" data-toggle="tab" data-target="#profile-information" type="button" role="tab" aria-controls="profile-information" aria-selected="true">Informasi Profil</button>
                        <button class="nav-link" id="profile-password-tab" data-toggle="tab" data-target="#profile-password" type="button" role="tab" aria-controls="profile-password" aria-selected="false">Ubah Password</button>
                        <button class="nav-link" id="profile-delete-tab" data-toggle="tab" data-target="#profile-delete" type="button" role="tab" aria-controls="profile-delete" aria-selected="false">Hapus Akun</button>
                    </div>

                    <div class="tab-content pt-4" id="profile-tabContent">
                        <div class="tab-pane fade show active" id="profile-information" role="tabpanel" aria-labelledby="profile-information-tab">
                            {{-- Mengganti komponen dengan @include --}}
                            @include('profile.partials.update-profile-information-form', ['user' => $user])
                        </div>

                        <div class="tab-pane fade" id="profile-password" role="tabpanel" aria-labelledby="profile-password-tab">
                            {{-- Mengganti komponen dengan @include --}}
                            @include('profile.partials.update-password-form')
                        </div>

                        <div class="tab-pane fade" id="profile-delete" role="tabpanel" aria-labelledby="profile-delete-tab">
                            {{-- Mengganti komponen dengan @include --}}
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection