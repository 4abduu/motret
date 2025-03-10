@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Notifikasi</h2>
    <ul class="list-group">
        @foreach($notifications as $notification)
            <li class="list-group-item d-flex align-items-start">
                @switch($notification->type)
                    @case('follow')
                        <i class="bi bi-person-plus-fill me-2" style="color: black; font-size: 20px;"></i>
                        @break
                    @case('like')
                        <i class="bi bi-heart-fill me-2" style="color: red; font-size: 20px;"></i>
                        @break
                    @case('comment')
                        <i class="bi bi-chat-left-text-fill me-2" style="color: blue; font-size: 20px;"></i>
                        @break
                    @case('system')
                        <i class="bi bi-info-circle me-2" style="color: gray; font-size: 20px;"></i>
                        @break
                    @default
                        <i class="bi bi-info-circle me-2" style="color: gray; font-size: 20px;"></i>
                @endswitch
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
                            <a href="{{ route('user.showProfile', $notification->sender->username) }}">
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