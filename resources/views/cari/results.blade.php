@extends('layouts.app')

@section('title', 'Search Results')

@push('link')
    <style>
        html, body {
            margin: 0;
        }
        
        .search-results-container {
            padding: 12px;
            max-width: 1200px;
            margin: 0 auto;
        }
    
        .search-results-container h3 {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 12px;
        }
    
        .search-results-container h4 {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
    
        .list-group-item {
            border: none;
            border-radius: 6px;
            margin-bottom: 6px;
            padding: 8px 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }
    
        .list-group-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }
    
        .list-group-item h5 {
            color: #32bd40;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
    
        .list-group-item p {
            color: #666;
            font-size: 0.8rem;
            margin-bottom: 0;
        }
    
        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            height: 100%;
            margin-bottom: 12px;
        }

        .card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        }

        .image-container {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 aspect ratio (more rectangular) */
            overflow: hidden;
        }

        .card-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 8px;
        }

        .card-title {
            color: #333;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .card-text {
            color: #666;
            font-size: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .hashtags {
            font-size: 0.7rem;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 0;
            color: #999;
        }

        /* Desktop view - 4 columns */
        @media (min-width: 992px) {
            .photo-column {
                flex: 0 0 25%;
                max-width: 25%;
                padding: 0 6px;
            }
        }

        /* Tablet view - 3 columns */
        @media (min-width: 768px) and (max-width: 991px) {
            .photo-column {
                flex: 0 0 33.333%;
                max-width: 33.333%;
                padding: 0 6px;
            }
        }

        /* Mobile view - 2 columns */
        @media (max-width: 767px) {
            .search-results-container {
                margin-top: 40px;
                padding: 8px;
            }
            
            .photo-column {
                flex: 0 0 50%;
                max-width: 50%;
                padding: 0 4px;
                margin-bottom: 10px;
            }
            
            .image-container {
                padding-top: 75%; /* Maintain 4:3 on mobile */
            }
            
            .card {
                margin-bottom: 8px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="search-results-container">
        <h4 class="my-2">Search Results for "{{ $keyword }}"</h4>
        @if($users->isEmpty() && $photos->isEmpty())
            <p class="text-muted" style="font-size: 0.9rem;">No results found.</p>
        @else
            @if(!$users->isEmpty())
                <h3 class="mb-2">Users</h3>
                <div class="list-group mb-3">
                    @foreach($users as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->username }}" 
                                     class="rounded-circle me-3" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                <a href="{{ route('user.showProfile', $user->username) }}" class="text-decoration-none">
                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                    <p class="mb-1">{{ '@' . $user->username }}</p>
                                </a>
                            </div>
                            
                            <div>
                                @if(Auth::check() && Auth::id() !== $user->id)
                                    <button 
                                        class="btn btn-sm {{ Auth::user()->isFollowing($user) ? 'btn-danger unfollow-button' : 'btn-success follow-button' }}" 
                                        data-user-id="{{ $user->id }}">
                                        {{ Auth::user()->isFollowing($user) ? 'Batal Ikuti' : 'Ikuti' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!$users->isEmpty() && !$photos->isEmpty())
                <hr class="my-2" style="border-top: 1px solid #eee;">
            @endif

            @if(!$photos->isEmpty())
                <h3 class="mb-2">Photos</h3>
                <div class="row mx-0">
                    @foreach($photos as $photo)
                        <div class="photo-column">
                            <div class="card shadow-sm">
                                <a href="{{ route('photos.show', $photo->id) }}">
                                    <div class="image-container">
                                        @if(Auth::check())
                                            <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
                                        @else
                                            <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                                        @endif 
                                    </div>
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $photo->title }}</h5>
                                    <p class="card-text">{{ $photo->description }}</p>
                                    <p class="hashtags">
                                        Hashtags: {{ implode(', ', json_decode($photo->hashtags)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = '{{ csrf_token() }}';

    // Update button appearance
    function updateButtonAppearance(button, isFollowing) {
        if (!button) return;
        button.textContent = isFollowing ? 'Batal Ikuti' : 'Ikuti';
        button.className = isFollowing 
            ? 'btn btn-danger btn-sm unfollow-button' 
            : 'btn btn-success btn-sm follow-button';
    }

    // Handle follow/unfollow action
    async function handleFollowAction(button, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const userId = button.getAttribute('data-user-id');
        const isUnfollow = button.classList.contains('unfollow-button');
        const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;

        try {
            button.disabled = true;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal memproses permintaan.');

            // Update all buttons with the same user ID
            document.querySelectorAll(`button[data-user-id="${userId}"]`).forEach(btn => {
                updateButtonAppearance(btn, data.action === 'follow');
                btn.disabled = false;
            });
        } catch (error) {
            console.error('Error:', error);
            button.disabled = false;
        }
    }

    // Attach event listeners to follow/unfollow buttons
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.follow-button, .unfollow-button');
        if (button) {
            handleFollowAction(button, e);
        }
    });

    //canvas untuk guest
    const lazyCanvases = document.querySelectorAll("canvas.card-img");

    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const canvas = entry.target;
                    const imgSrc = canvas.getAttribute("data-src");
                    if (imgSrc) {
                        const img = new Image();
                        img.src = imgSrc;
                        img.onload = function () {
                            const ctx = canvas.getContext("2d");
                            let width = canvas.clientWidth;
                            let height = canvas.clientHeight;
                            const aspectRatio = img.width / img.height;

                            if (width / height > aspectRatio) {
                                width = height * aspectRatio;
                            } else {
                                height = width / aspectRatio;
                            }

                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);
                        };
                    }
                    observer.unobserve(canvas);
                }
            });
        },
        { rootMargin: "100px" }
    );

    lazyCanvases.forEach((canvas) => {
        observer.observe(canvas);
    });

    // Fallback untuk browser yang tidak support IntersectionObserver
    if (!("IntersectionObserver" in window)) {
        lazyCanvases.forEach((canvas) => {
            const imgSrc = canvas.getAttribute("data-src");
            if (imgSrc) {
                const img = new Image();
                img.src = imgSrc;
                img.onload = function () {
                    const ctx = canvas.getContext("2d");
                    let width = canvas.clientWidth;
                    let height = canvas.clientHeight;
                    const aspectRatio = img.width / img.height;

                    if (width / height > aspectRatio) {
                        width = height * aspectRatio;
                    } else {
                        height = width / aspectRatio;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                };
            }
        });
    }
});
</script>
@endpush