@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="container">
        <h1 class="my-4">Manage Users</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Create User</button>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <table class="table table-striped">
            <thead>
            <tr>
                    <th>No</th>
                    <th>Foto Profil</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/photo_profile/' . $user->profile_photo) }}" alt="{{ $user->username }}" class="rounded-circle" width="100" height="100">
                            @else
                                <img src="{{ asset('storage/photo_profile/default_photo_profile.jpg') }}" alt="{{ $user->username }}" class="rounded-circle" width="100" height="100">
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">Edit</button>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailUserModal{{ $user->id }}">Detail</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">Delete</button>
                        </td>
                    </tr>

                    <!-- Modal Detail User -->
                    <div class="modal fade" id="detailUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="detailUserModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailUserModalLabel{{ $user->id }}">Detail User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-3">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/photo_profile/' . $user->profile_photo) }}" alt="{{ $user->username }}" class="rounded-circle" width="100" height="100">
                                        @else
                                            <img src="{{ asset('storage/photo_profile/default_photo_profile.jpg') }}" alt="{{ $user->username }}" class="rounded-circle" width="100" height="100">
                                        @endif
                                    </div>
                                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                                    <p><strong>Username:</strong> {{ $user->username }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                                    <p><strong>Subscription Berakhir Pada:</strong> {{ $user->subscription_ends_at ?? 'null' }}</p>
                                    <p><strong>Reset Download Pada:</strong> {{ $user->download_reset_at ?? 'null' }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit User -->
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group position-relative">
                                            <label for="profile_photo">Foto Profil</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                                                @if($user->profile_photo)
                                                    <div class="input-group-append">
                                                        <!-- Tombol untuk membuka modal -->
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePhotoModal{{ $user->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($user->profile_photo)
                                                <small class="form-text text-muted">Foto saat ini: {{ $user->profile_photo }}</small>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" id="username" value="{{ $user->username }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="email" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="password">
                                            <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                                        </div>
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select name="role" class="form-control" id="role" required>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="pro" {{ $user->role == 'pro' ? 'selected' : '' }}>Pro</option>
                                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Delete Photo -->
                    <div class="modal fade" id="deletePhotoModal{{ $user->id }}" tabindex="-1" aria-labelledby="deletePhotoModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePhotoModalLabel{{ $user->id }}">Hapus Foto Profil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah Anda yakin ingin menghapus foto profil?</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.users.deleteProfilePhoto', $user->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Delete User -->
                    <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete the user <strong>{{ $user->username }}</strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Create User -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.users.create') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control" id="profile_photo">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-control" id="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                                <option value="pro">Pro</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection