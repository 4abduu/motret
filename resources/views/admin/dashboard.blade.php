@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
    <div class="container">
        <h1 class="my-4">Halo, Admin!</h1>
        <p>Ini adalah halaman dashboard untuk Admin.</p>
        <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>  {{-- Tombol logout --}}
    </div>
@endsection
