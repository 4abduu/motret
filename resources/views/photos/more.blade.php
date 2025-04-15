{{-- filepath: c:\xampp new\htdocs\motret\resources\views\photos\more.blade.php --}}
@extends('layouts.app')

@section('title', 'Lihat Lebih Banyak Foto')


@section('content')

<style>  
    /* ============================== */
    /* Most Searched Section Styling */
    /* ============================== */
    .most-searched-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 2rem;
    }
    
    .most-searched-title {
        font-size: 1.25rem;
        font-weight: bold;
        text-align: center;
        margin: 0;
    }
    
    .most-searched-keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }
    
    .keyword-item {
        background-color: #f0f0f0;
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-block;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .keyword-item:hover {
        background-color: #32bd40;
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    /* ====================== */
    /* Responsive Adjustments */
    /* ====================== */
    @media (max-width: 768px) {
        .most-searched-title {
            font-size: 1rem;
        }
    
        .keyword-item {
            font-size: 12px;
            padding: 4px 8px;
        }
    }
    
    @media (max-width: 480px) {
        .most-searched-title {
            font-size: 0.9rem;
        }
        .keyword-item {
            font-size: 11px;
            padding: 3px 6px;
        }
    }
    
    /* ==================== */
    /* Masonry Card Layout */
    /* ==================== */
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
    
    /* Overlay Effect */
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
    
    /* =========================== */
    /* Horizontal Scroll Section */
    /* =========================== */
    .horizontal-scroll-container {
        width: 100%;
        overflow-x: auto; /* Changed from scroll to hide scrollbar */
        padding-bottom: 10px;
        position: relative; /* Added for positioning the "Lihat Lebih Banyak" */
    }
    
    /* Hide scrollbar completely */
    .horizontal-scroll-container::-webkit-scrollbar {
        height: 8px;
        background: transparent;
    }
    
    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }
    
    .horizontal-scroll-container:hover::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.6);
    }
    
    .horizontal-scroll-wrapper {
        display: flex;
        gap: 20px;
        padding: 5px 0;
        scroll-snap-type: x mandatory;
        scroll-padding: 10px;
    }
    
    /* Scroll Card Styling */
    .scroll-card {
            flex: 0 0 auto;
            width: 300px;
            height: 180px; /* Tinggi dipendekkan dari 230px menjadi 180px */
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    
     /* Mobile Adjustments */
     @media (max-width: 576px) {
            .scroll-card {
                width: 150px; /* Lebar foto lebih kecil */
                height: 100px; /* Tinggi foto lebih kecil */
            }
            .horizontal-scroll-wrapper {
                gap: 10px; /* Kurangi jarak antar foto */
            }
        }
    
    .scroll-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .scroll-card:hover .overlay {
        opacity: 1;
    }
     /*lihat lebih banyak*/
    
        .section-link {
            font-size: 1rem;
            color: #1b1c1beb;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
            line-height: 1.2;
        }
    
        .section-link:hover {
            color: #1b1c1bac;
            text-decoration: underline;
        }
</style>

<div class="container-fluid">
    <div class="row">
        <h3 class="mb-5 text-center">Foto yang paling banyak dilihat</h3>
        <div class="card-columns">
            @foreach($mostViewedPhotos as $photo)
            <div class="card card-pin">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay">
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>


@endsection