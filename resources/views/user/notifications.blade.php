@extends('layouts.app')

@section('content')
<div style="background-color: #f8f9fa; min-height: 100vh; padding: 20px;">
    <div style="width: 100%; max-width: 1600px; margin: 0 auto;">
        <!-- Header Notifikasi -->
        <div style="background: linear-gradient(90deg, #32bd40, #2a9d36); color: #fff; padding: 20px; border-radius: 10px 10px 0 0;">
            <span style="font-size: 24px; font-weight: bold;">
                <i class="fas fa-bell" style="margin-right: 10px;"></i> Notifikasi
            </span>
        </div>

        <!-- Container Notifikasi -->
        <div style="background-color: #fff; border-radius: 0 0 10px 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 20px;">
            @foreach($notifications as $notification)
            <div style="display: block; width: 100%; padding: 20px 0; border-bottom: 1px solid #e0e0e0; transition: background-color 0.3s; cursor: pointer;" onmouseover="this.style.backgroundColor='#f0f0f0'" onmouseout="this.style.backgroundColor='#fff'">
                <div style="display: flex; align-items: center;">
                    <!-- Icon -->
                    <div style="margin-right: 20px;">
                        @switch($notification->type)
                            @case('follow')
                                <i class="fas fa-user-plus" style="font-size: 40px; color: black;"></i>
                                @break
                            @case('like')
                                <i class="fas fa-heart" style="font-size: 40px; color: red;"></i>
                                @break
                            @case('comment')
                                <i class="fas fa-comment" style="font-size: 40px; color: blue;"></i>
                                @break
                            @case('reply')
                                <i class="fas fa-reply" style="font-size: 40px; color: green;"></i>
                                @break
                            @case('system')
                                <i class="fas fa-info-circle" style="font-size: 40px; color: gray;"></i>
                                @break
                            @default
                                <i class="fas fa-info-circle" style="font-size: 40px; color: gray;"></i>
                        @endswitch
                    </div>

                    <!-- Konten Notifikasi -->
                    <div style="flex: 1;">
                        <!-- Pesan dan Pengirim -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center;">
                                @if($notification->type !== 'system' && $notification->sender)
                                    <a href="{{ route('user.showProfile', $notification->sender->username) }}" style="margin-right: 15px;">
                                        <img src="{{ asset($notification->sender->profile_photo ? 'storage/photo_profile/' . $notification->sender->profile_photo : 'images/foto profil.jpg') }}" alt="{{ $notification->sender->username }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                    </a>
                                @endif
                                <div>
                                    @if($notification->sender)
                                        <a href="{{ route('user.showProfile', $notification->sender->username) }}" style="font-weight: 700; font-size: 20px; color: #000; text-decoration: none;">
                                            {{ $notification->sender->username }}
                                        </a>
                                    @endif
                                    <span style="font-size: 18px; color: #000;">
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

                            <!-- Waktu -->
                            <div style="font-size: 14px; color: #888; margin-left: 20px;">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection