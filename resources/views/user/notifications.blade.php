@extends('layouts.app')

@push('style')
<style>
    .notification-icon {
        font-size: 24px; /* Ukuran ikon lebih besar */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px; /* Lebar ikon */
        height: 50px; /* Tinggi ikon */
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Notifikasi</h2>
    <ul class="list-group">
        @foreach($notifications as $notification)
            <li class="list-group-item d-flex align-items-start">
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
                
                <div>
                    @if($notification->type !== 'system')
                        @if($notification->sender)
                            @if($notification->sender->profile_photo)
                                <img src="{{ asset('storage/photo_profile/' . $notification->sender->profile_photo) }}" class="rounded-circle mb-1" alt="{{ $notification->sender->username }}" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/foto profil.jpg') }}" class="rounded-circle mb-1" alt="{{ $notification->sender->username }}" style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                        @else
                            <img src="{{ asset('images/foto profil.jpg') }}" class="rounded-circle mb-1" alt="Unknown User" style="width: 40px; height: 40px; object-fit: cover;">
                        @endif
                    @endif
                    <div class="text-dark fw-bold">
                        @if($notification->sender)
                            <a href="{{ route('user.showProfile', $notification->sender->username) }}" style="color: black;">
                                {{ $notification->sender->username }}
                            </a>
                        @endif
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
                    </div>
                    <span class="text-muted" style="font-size: 12px;">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection