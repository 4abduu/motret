@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Profil Pengguna</h1>
    <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" class="img-thumbnail rounded-circle" width="150">
    <p>Nama: {{ $user->name }}</p>
    <p>Username: {{ $user->username }}</p>
    <p>Followers: <span id="followers-count">{{ $user->followers()->count() }}</span></p>
    <p>Following: <span id="following-count">{{ $user->following()->count() }}</span></p>
    @if(Auth::id() === $user->id)
        <p>Email: {{ $user->email }}</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
    @else
        <div id="follow-section">
            @if(Auth::check())
                <button id="follow-button" class="btn btn-link" data-user-id="{{ $user->id }}" data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}">
                    {{ Auth::user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                </button>
            @else
                <a href="{{ route('login') }}" class="btn btn-link">Follow</a>
            @endif
        </div>
    @endif

    <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#followersModal">Lihat Followers</button>
    <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#followingModal">Lihat Following</button>

    <h2 class="mt-5">Foto yang Diunggah</h2>
    <div class="row">
        @foreach($photos as $photo)
            @if($photo->banned && $photo->user_id !== Auth::id())
                @continue
            @endif
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($photo->banned && $photo->user_id === Auth::id())
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

<!-- Modal Followers -->
<div class="modal fade" id="followersModal" tabindex="-1" role="dialog" aria-labelledby="followersModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="followers-list">
                    @foreach($user->followers as $follower)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.showProfile', $follower->username) }}">{{ $follower->username }}</a>
                            @if(Auth::check())
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($follower) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
                                    data-user-id="{{ $follower->id }}">
                                    {{ Auth::user()->isFollowing($follower) ? 'Unfollow' : 'Follow' }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Following -->
<div class="modal fade" id="followingModal" tabindex="-1" role="dialog" aria-labelledby="followingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followingModalLabel">Following</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="following-list">
                    @foreach($user->following as $following)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.showProfile', $following->username) }}">{{ $following->username }}</a>
                            @if(Auth::check())
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($following) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
                                    data-user-id="{{ $following->id }}">
                                    {{ Auth::user()->isFollowing($following) ? 'Unfollow' : 'Follow' }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateFollowButton(button, following) {
        if (following) {
            button.textContent = 'Unfollow';
            button.classList.remove('btn-primary', 'follow-button');
            button.classList.add('btn-danger', 'unfollow-button');
        } else {
            button.textContent = 'Follow';
            button.classList.remove('btn-danger', 'unfollow-button');
            button.classList.add('btn-primary', 'follow-button');
        }
    }

    function handleFollowUnfollow(button) {
        const userId = button.getAttribute('data-user-id');
        const isUnfollow = button.classList.contains('unfollow-button');
        const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
        const token = '{{ csrf_token() }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            updateFollowButton(button, !isUnfollow);
            const followersCount = document.getElementById('followers-count');
            followersCount.textContent = data.followers_count;
            const followingCount = document.getElementById('following-count');
            followingCount.textContent = data.following_count;

            // Update following list in the modal
            const followingList = document.getElementById('following-list');
            if (!isUnfollow) {
                const newFollowingItem = document.createElement('li');
                newFollowingItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                newFollowingItem.innerHTML = `
                    <a href="/users/${userId}">${data.username}</a>
                    <button class="btn btn-sm btn-danger unfollow-button" data-user-id="${userId}">Unfollow</button>
                `;
                followingList.appendChild(newFollowingItem);

                // Add event listener to the new unfollow button
                newFollowingItem.querySelector('.unfollow-button').addEventListener('click', function () {
                    handleFollowUnfollow(this);
                });
            } else {
                const followingItems = followingList.querySelectorAll('li');
                followingItems.forEach(item => {
                    if (item.querySelector('button').getAttribute('data-user-id') === userId) {
                        item.remove();
                    }
                });
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Tombol Follow/Unfollow di profil pengguna
    const followButton = document.getElementById('follow-button');
    if (followButton) {
        followButton.addEventListener('click', function () {
            handleFollowUnfollow(followButton);
        });
    }

    // Tombol Follow/Unfollow di modal followers/following
    document.querySelectorAll('.follow-button, .unfollow-button').forEach(button => {
        button.addEventListener('click', function () {
            handleFollowUnfollow(button);
        });
    });
});
</script>
@endpush