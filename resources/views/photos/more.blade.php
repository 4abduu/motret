@extends('layouts.app')

@section('title', 'Lihat Lebih Banyak Foto')

@section('content')

<style>
    .card-columns {
        column-count: 2;
        column-gap: 1rem;
    }

    @media (min-width: 768px) {
        .card-columns {
            column-count: 3;
        }
    }

    @media (min-width: 1024px) {
        .card-columns {
            column-count: 5;
        }
    }

    .card {
        display: inline-block;
        width: 100%;
        margin-bottom: 1rem;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .card-pin:hover .overlay {
        opacity: 1;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <h3 class="mb-5 text-center">
            @if($type === 'most_liked')
                Foto yang Paling Banyak Disukai
            @elseif($type === 'most_downloaded')
                Foto yang Paling Banyak Diunduh
            @else
                Foto yang Paling Banyak Dilihat
            @endif
        </h3>
        <div class="card-columns">
            @foreach($photos as $photo)
            <div class="card card-pin">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay"></div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
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