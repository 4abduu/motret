@extends('layouts.app')

@section('title', 'Manage Users')

@push('link')
<style>
    .custom-preview-btn {
        transition: all 0.3s ease;
        color: #32bd40;
        border-color: #32bd40;
    }

    .custom-preview-btn:hover {
        background-color: #32bd40;
        color: white !important;
        border-color: #32bd40;
    }

    .custom-preview-btn:hover i {
        color: white !important;
    }

    .modal-body img.rounded-circle {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
    }

    .dt-length {
        margin-left: 20px;
        padding-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <h3>Manage User</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage User</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Data User</h4>
                    <button type="button" class="btn btn-success btn-icon-text" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah User
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
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
                                            <img src="{{ asset('storage/photo_profile/' . $user->profile_photo) }}" alt="{{ $user->username }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;">
                                        @else
                                            <img src="{{ asset('storage/photo_profile/default_photo_profile.jpg') }}" alt="{{ $user->username }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;">
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="ti-pencil-alt" style="color: white;"></i></button>
                                        <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailUserModal{{ $user->id }}"><i class="ti-info" style="color: white;"></i></button>
                                        <button class="btn btn-danger btn-icon delete-user-btn" data-id="{{ $user->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail User -->
@foreach($users as $user)
<div class="modal fade" id="detailUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="detailUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #32bd40;">
                <h5 class="modal-title" id="detailUserModalLabel{{ $user->id }}">Detail User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $user->profile_photo) }}" alt="{{ $user->username }}" class="rounded-circle img-thumbnail" width="120" height="120">
                    @else
                        <img src="{{ asset('storage/photo_profile/default_photo_profile.jpg') }}" alt="{{ $user->username }}" class="rounded-circle img-thumbnail" width="120" height="120">
                    @endif
                    <h4 class="mt-3">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->username }}</p>
                    <a href="{{ route('admin.users.previewProfile', $user->id) }}" class="btn btn-outline-success btn-sm mt-2 custom-preview-btn">
                        <i class="ti-eye me-2"></i> Preview Akun
                    </a>
                </div>
                <div class="mt-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title text-center" style="color: #32bd40;">Informasi User</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                                    <p><strong>Subscription Berakhir Pada:</strong> {{ $user->subscription_ends_at ?? 'Tidak Ada' }}</p>
                                    <p><strong>Reset Download Pada:</strong> {{ $user->download_reset_at ?? 'Tidak Ada' }}</p>
                                    <p><strong>Bio:</strong> {{ $user->bio ?? 'Tidak Ada' }}</p>
                                    <p><strong>Website:</strong> {{ $user->website ?? 'Tidak Ada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit User -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="edit-user-form" data-id="{{ $user->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <input type="text" name="bio" class="form-control" value="{{ $user->bio }}">
                    </div>
                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="text" name="website" class="form-control" value="{{ $user->website }}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pro" {{ $user->role == 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Foto Profil</label>
                        <input type="file" name="profile_photo" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success text-white">Update</button>
                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Create User -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.users.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control" id="role" required>
                            <option value="admin">Admin</option>
                            <option value="pro">Pro</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success text-white">Tambah User</button>
                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTables
    const table = $('#example').DataTable();

    // Fungsi untuk menampilkan SweetAlert2
    function showAlert(icon, title, text, callback = null) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonText: 'OK'
        }).then(() => {
            if (callback) callback();
        });
    }

    // Handle form submission untuk tambah user
    const addUserForm = document.querySelector('#createUserModal form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('admin.users.create') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'User berhasil ditambahkan.', () => {
                        window.location.reload(); // Reload halaman setelah berhasil
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menambahkan user.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        });
    }

    // Handle form submission untuk edit user
    document.addEventListener('submit', async function (e) {
        if (e.target && e.target.matches('.edit-user-form')) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const userId = e.target.getAttribute('data-id');
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'User berhasil diupdate.', () => {
                        window.location.reload(); // Reload halaman setelah berhasil
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat mengupdate user.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        }
    });

    // Handle delete user dengan event delegation
    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.delete-user-btn')) {
            const button = e.target.closest('.delete-user-btn');
            const userId = button.getAttribute('data-id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan bisa mengembalikan user ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/users/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showAlert('success', 'Berhasil!', data.message || 'User berhasil dihapus.', () => {
                                window.location.reload(); // Reload halaman setelah berhasil
                            });
                        } else {
                            showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menghapus user.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
                    }
                }
            });
        }
    });
});
</script>
@endpush