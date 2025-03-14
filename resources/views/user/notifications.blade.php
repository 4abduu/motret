@extends('layouts.app')

@push('style')
<style>
    .notification-icon {
        font-size: 36px; /* Ukuran ikon lebih besar */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px; /* Lebar ikon */
        height: 70px; /* Tinggi ikon */
        margin-right: 15px;
        position: relative;
        top: 50%; /* Posisikan ikon di tengah vertikal */
        transform: translateY(-50%);
    }
    .list-group-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: none;
        position: relative;
    }
    .list-group-item:not(:last-child)::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 15px;
        right: 15px;
        height: 1px;
        background-color: #ddd;
    }
    .notification-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
    }
    .notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between; /* Waktu di sebelah kanan */
        width: 100%;
    }
    .notification-time {
        font-size: 12px;
        color: #888;
        margin-left: 10px; /* Jarak antara pesan dan waktu */
    }
    .notification-message {
        margin-left: 10px;
    }
    .back-button {
        font-size: 24px;
        margin-right: 10px;
        cursor: pointer;
    }
    .notification-title {
        display: flex;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="notification-title">
        <!-- Tombol Back -->
        <button onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> 
        </button>
        
        <h2 class="mb-4">Notifikasi</h2>
    </div>
    <ul class="list-group">
        @foreach($notifications as $index => $notification)
            <li class="list-group-item">
                <!-- Icon Notifikasi -->
                <div class="notification-icon">
                    @switch($notification->type)
                        @case('follow')
                            <i class="fas fa-user-plus" style="color: black;"></i> <!-- FontAwesome Icons -->
                            @break
                        @case('like')
                            <i class="fas fa-heart" style="color: red;"></i> <!-- FontAwesome Icons -->
                            @break
                        @case('comment')
                            <i class="fas fa-comment" style="color: blue;"></i> <!-- FontAwesome Icons -->
                            @break
                        @case('reply')
                            <i class="fas fa-reply" style="color: green;"></i> <!-- FontAwesome Icons -->
                            @break
                        @case('system')
                            <i class="fas fa-info-circle" style="color: gray;"></i> <!-- FontAwesome Icons -->
                            @break
                        @default
                            <i class="fas fa-info-circle" style="color: gray;"></i> <!-- FontAwesome Icons -->
                    @endswitch
                </div>
                
                <!-- Konten Notifikasi -->
                <div class="notification-content">
                    <div class="notification-header">
                        <div class="d-flex align-items-center">
                            @if($notification->type !== 'system' && $notification->sender)
                                <img src="{{ asset($notification->sender->profile_photo ? 'storage/photo_profile/' . $notification->sender->profile_photo : 'images/foto profil.jpg') }}" 
                                     class="rounded-circle" alt="{{ $notification->sender->username }}" 
                                     style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                            @endif
                            <div class="text-dark fw-bold">
                                @if($notification->sender)
                                    <a href="{{ route('user.showProfile', $notification->sender->username) }}" style="color: black;">
                                        {{ $notification->sender->username }}
                                    </a>
                                @endif
                                <span class="notification-message">
                                    @switch($notification->type)
                                        @case('follow')
                                            mengikuti anda!
                                            @break
                                        @case('like')
                                            menyukai foto anda!
                                            @break
                                        @case('comment')
                                            mengomentari foto anda!
                                            @break
                                        @case('reply')
                                            membalas komentar Anda.
                                            @break
                                        @case('system')
                                            {{ $notification->message }}
                                            @break
                                        @default
                                            {{ $notification->message }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        <!-- Waktu Notifikasi -->
                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection