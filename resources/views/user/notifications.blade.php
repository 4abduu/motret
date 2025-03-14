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
    }
    .notification-time {
        font-size: 12px;
        color: #888;
        margin-left: auto;
    }
    .notification-message {
        margin-left: 10px;
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Notifikasi</h2>
    <ul class="list-group">
        @foreach($notifications as $index => $notification)
            <li class="list-group-item">
                <div class="notification-icon">
                    @switch($notification->type)
                        @case('follow')
                            <i class="fa fa-user-plus" style="color: black;"></i>
                            @break
                        @case('like')
                            <i class="fa fa-heart" style="color: red;"></i>
                            @break
                        @case('comment')
                            <i class="fa fa-comment" style="color: blue;"></i>
                            @break
                        @case('reply')
                            <i class="fa fa-reply" style="color: green;"></i>
                            @break
                        @case('system')
                            <i class="fa fa-info-circle" style="color: gray;"></i>
                            @break
                        @default
                            <i class="fa fa-info-circle" style="color: gray;"></i>
                    @endswitch
                </div>
                
                <div class="notification-content">
                    <div class="notification-header">
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
                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection