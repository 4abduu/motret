@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Profil Pengguna</h1>
        <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" class="img-thumbnail rounded-circle" width="150">
        <p>Nama: {{ $user->name }}</p>
        <p>Username: {{ $user->username }}</p>
        @if(Auth::id() === $user->id)
        <p>Email: {{ $user->email }}</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
        @endif

        <h2 class="mt-5">Foto yang Diunggah</h2>
        <div class="row">
            @foreach($photos as $photo)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        @if($photo->banned)
                            <div class="card-body">
                                <h5 class="card-title">Postingan ini telah dibanned.</h5>
                                @foreach($photo->reports as $report)
                                    <p class="card-text"><strong>Alasan:</strong> {{ $report->reason }}</p>
                                @endforeach
                            </div>
                        @else
                            <a href="{{ route('photos.show', $photo->id) }}">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $photo->title }}</h5>
                                <p class="card-text">{{ $photo->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if(Auth::id() === $user->id)
        <!-- Modal Edit Profil -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editProfileForm" method="POST" action="{{ route('user.updateProfile') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group position-relative">
                                <label for="profile_photo">Foto Profil</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                                    @if($user->profile_photo)
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePhotoModal">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @if($user->profile_photo)
                                    <small class="form-text text-muted">Foto saat ini: {{ $user->profile_photo }}</small>
                                @endif
                            </div>
                            <div class="form-group position-relative">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group position-relative">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                                <small id="usernameHelp" class="form-text text-muted"></small>
                                <span id="usernameIcon" class="position-absolute" style="right: 10px; top: 35px;"></span>
                            </div>
                            <div class="form-group position-relative">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                <small id="emailHelp" class="form-text text-muted"></small>
                                <span id="emailIcon" class="position-absolute" style="right: 10px; top: 35px;"></span>
                            </div>
                            <div class="form-group">
                                <label for="current_password">Password Lama</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="form-group">
                                <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus Foto -->
        <div class="modal fade" id="deletePhotoModal" tabindex="-1" role="dialog" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePhotoModalLabel">Hapus Foto Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus foto profil?</p>
                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="{{ route('user.deleteProfilePhoto') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#username').on('blur', function() {
                var username = $(this).val();
                $.ajax({
                    url: '{{ route("user.checkUsername") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        username: username
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#username').css('border-color', 'red');
                            $('#usernameIcon').html('<i class="bi bi-x-circle-fill text-danger"></i>');
                            $('#usernameHelp').text('Username sudah digunakan.').css('color', 'red');
                        } else {
                            $('#username').css('border-color', 'green');
                            $('#usernameIcon').html('<i class="bi bi-check-circle-fill text-success"></i>');
                            $('#usernameHelp').text('Username tersedia.').css('color', 'green');
                        }
                    }
                });
            });

            $('#email').on('blur', function() {
                var email = $(this).val();
                $.ajax({
                    url: '{{ route("user.checkEmail") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: email
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('#email').css('border-color', 'red');
                            $('#emailIcon').html('<i class="bi bi-x-circle-fill text-danger"></i>');
                            $('#emailHelp').text('Email sudah digunakan.').css('color', 'red');
                        } else {
                            $('#email').css('border-color', 'green');
                            $('#emailIcon').html('<i class="bi bi-check-circle-fill text-success"></i>');
                            $('#emailHelp').text('Email tersedia.').css('color', 'green');
                        }
                    }
                });
            });

            $('#editProfileModal').on('hidden.bs.modal', function () {
                $('#username').css('border-color', '');
                $('#usernameIcon').html('');
                $('#usernameHelp').text('').css('color', '');
                $('#email').css('border-color', '');
                $('#emailIcon').html('');
                $('#emailHelp').text('').css('color', '');
            });
        });
    </script>
@endpush