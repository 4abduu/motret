@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Notifikasi</h1>
    <ul class="list-group">
        @foreach($notifications as $notification)
            <li class="list-group-item {{ $notification->status ? '' : 'list-group-item-info' }}">
                @switch($notification->type)
                    @case('follow')
                        <a href="{{ route('user.showProfile', $notification->sender->username) }}">
                            {{ $notification->sender->username }} mulai mengikuti Anda.
                        </a>
                        @break
                    @case('like')
                        <a href="{{ route('photos.show', $notification->target_id) }}">
                            {{ $notification->sender->username }} menyukai foto Anda.
                        </a>
                        @break
                    @case('comment')
                        <a href="{{ route('photos.show', $notification->target_id) }}">
                            {{ $notification->sender->username }} mengomentari foto Anda.
                        </a>
                        @break
                    @default
                        {{ $notification->message }}
                @endswitch
                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">Mark as read</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection